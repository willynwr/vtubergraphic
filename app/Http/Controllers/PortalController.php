<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OffDay;
use App\Models\OffDayOverride;
use App\Models\ScheduleSwapRequest;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    private function getEmployee(Request $request)
    {
        $employeeId = $request->session()->get('employee_portal_id');
        if (!$employeeId) return null;
        return Employee::where('employee_id', $employeeId)->first();
    }

    /**
     * Dashboard - mobile-first, icon menu.
     */
    public function index(Request $request)
    {
        $employee = $this->getEmployee($request);
        if (!$employee) return redirect()->route('scanner');

        $todayAttendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', today())
            ->orderBy('time')
            ->get();

        // Off day stats
        $remainingOffDays = OffDay::remainingThisMonth($employee->employee_id);
        $totalOffDays = OffDay::totalThisMonth($employee->employee_id);

        // Swap request stats
        $allSwaps = ScheduleSwapRequest::where('employee_id', $employee->employee_id)->get();
        $pendingSwaps = $allSwaps->where('status', 'PENDING')->count();
        $approvedSwaps = $allSwaps->where('status', 'APPROVED')->count();

        $pendingRequests = ScheduleSwapRequest::with('swapWithEmployee')
            ->where('employee_id', $employee->employee_id)
            ->where('status', 'PENDING')
            ->latest()->get();

        $approvedRequests = ScheduleSwapRequest::with('swapWithEmployee')
            ->where('employee_id', $employee->employee_id)
            ->where('status', 'APPROVED')
            ->latest()->limit(5)->get();

        // Get off day names (e.g., "Jumat, Sabtu")
        $employee->load('offDays');

        return view('portal.dashboard', compact(
            'employee', 'todayAttendances', 'remainingOffDays', 'totalOffDays',
            'pendingSwaps', 'approvedSwaps', 'pendingRequests', 'approvedRequests'
        ));
    }

    /**
     * Attendance page.
     */
    public function attendance(Request $request)
    {
        $employee = $this->getEmployee($request);
        if (!$employee) return redirect()->route('scanner');

        $todayAttendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', today())
            ->orderBy('time')
            ->get();

        // Today status
        $inRecord = $todayAttendances->where('type', 'IN')->first();
        $outRecord = $todayAttendances->where('type', 'OUT')->first();
        $todayStatus = ['text' => 'Belum Absen', 'time' => '-'];
        if ($outRecord) {
            $todayStatus = ['text' => 'Sudah Pulang', 'time' => 'IN: ' . ($inRecord->time ?? '-') . ' · OUT: ' . $outRecord->time];
        } elseif ($inRecord) {
            $todayStatus = ['text' => 'Sedang Bekerja', 'time' => 'Masuk: ' . $inRecord->time];
        }

        // History (last 20 records)
        $history = Attendance::where('employee_id', $employee->employee_id)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(20)
            ->get();

        return view('portal.attendance', compact('employee', 'todayAttendances', 'todayStatus', 'history'));
    }

    /**
     * Schedule page - calendar view showing department off days.
     */
    public function schedule(Request $request)
    {
        $employee = $this->getEmployee($request);
        if (!$employee) return redirect()->route('scanner');

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        // Get all employees in the same department
        $departmentEmployees = Employee::with('offDays')
            ->where('department', $employee->department)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Build data structure for JS: each employee's off dates for this month
        $offDaysByEmployee = [];
        $employeeColors = [];
        $colors = ['#e87bb0', '#b388d9', '#7eb8e0', '#8dd4b0', '#f0b86e', '#e87070', '#6dcfcf', '#a78bfa', '#fb923c', '#38bdf8'];

        foreach ($departmentEmployees as $idx => $emp) {
            $dates = OffDay::getMonthlyOffDates($emp->employee_id, $month, $year);
            $offDaysByEmployee[$emp->employee_id] = [
                'name' => $emp->name,
                'position' => $emp->position,
                'off_day_names' => $emp->off_day_names,
                'dates' => $dates,
            ];
            $employeeColors[$emp->employee_id] = $colors[$idx % count($colors)];
        }

        return view('portal.schedule', compact(
            'employee', 'offDaysByEmployee', 'employeeColors', 'month', 'year'
        ));
    }

    /**
     * Swap requests page - same department only.
     */
    public function swap(Request $request)
    {
        $employee = $this->getEmployee($request);
        if (!$employee) return redirect()->route('scanner');

        $swapRequests = ScheduleSwapRequest::with('swapWithEmployee')
            ->where('employee_id', $employee->employee_id)
            ->latest()->limit(20)->get();

        $colleagues = Employee::where('department', $employee->department)
            ->where('employee_id', '!=', $employee->employee_id)
            ->where('is_active', true)
            ->orderBy('name')->get();

        return view('portal.swap', compact('employee', 'swapRequests', 'colleagues'));
    }

    /**
     * Store a swap request.
     */
    public function storeSwapRequest(Request $request)
    {
        $employeeId = $request->session()->get('employee_portal_id');
        if (!$employeeId) {
            return response()->json(['success' => false, 'message' => 'Sesi login tidak ditemukan.'], 401);
        }

        $request->validate([
            'requested_date' => 'required|date',
            'target_date' => 'required|date',
            'swap_with_employee_id' => 'required|exists:employees,employee_id',
            'reason' => 'required|string|max:1000',
        ]);

        // Validate same department
        $currentEmployee = Employee::where('employee_id', $employeeId)->first();
        $targetEmployee = Employee::where('employee_id', $request->swap_with_employee_id)->first();

        if ($currentEmployee && $targetEmployee && $currentEmployee->department !== $targetEmployee->department) {
            return response()->json([
                'success' => false,
                'message' => 'Tukar libur hanya bisa dengan karyawan satu departemen.',
            ], 422);
        }

        $swapRequest = ScheduleSwapRequest::create([
            'employee_id' => $employeeId,
            'requested_date' => $request->requested_date,
            'target_date' => $request->target_date,
            'swap_with_employee_id' => $request->swap_with_employee_id,
            'reason' => $request->reason,
            'status' => ScheduleSwapRequest::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan tukar libur berhasil dikirim.',
            'request' => $swapRequest,
        ]);
    }

    /**
     * Logout from portal.
     */
    public function logout(Request $request)
    {
        $request->session()->forget('employee_portal_id');
        return redirect()->route('scanner');
    }
}