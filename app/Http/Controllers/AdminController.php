<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\ScheduleSwapRequest;
use App\Models\OffDay;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    private const ADMIN_PASSWORD = 'pshtjaya';

    /**
     * Admin dashboard
     */
    public function dashboard(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Monthly summary
        $summary = $this->getMonthlySummary($month, $year);

        // Employee list with monthly stats
        $employees = Employee::withCount('attendances')->where('is_active', true)->get();
        $employeeStats = $this->getEmployeeStats($employees, $month, $year);

        // Recent attendance records
        $recentAttendances = Attendance::with('employee')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Today's status
        $todaySummary = $this->getTodaySummary();

        // Locations for the locations page
        $locations = OfficeLocation::all();

        // Pending swap requests for quick approval
        $pendingSwapRequests = ScheduleSwapRequest::with(['employee', 'swapWithEmployee'])
            ->where('status', ScheduleSwapRequest::STATUS_PENDING)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.dashboard', compact(
            'summary',
            'employeeStats',
            'recentAttendances',
            'todaySummary',
            'month',
            'year',
            'employees',
            'locations',
            'pendingSwapRequests'
        ));
    }

    /**
     * Show admin password gate
     */
    public function showPasswordForm()
    {
        if (session('admin_password_passed') === true) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.password');
    }

    /**
     * Verify admin password
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if ($request->input('password') !== self::ADMIN_PASSWORD) {
            return back()
                ->withErrors(['password' => 'Password admin salah.'])
                ->withInput();
        }

        $request->session()->put('admin_password_passed', true);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Logout admin password session
     */
    public function logoutPassword(Request $request)
    {
        $request->session()->forget('admin_password_passed');

        return redirect()->route('admin.password.form');
    }

    /**
     * API: Get monthly summary data
     */
    public function apiSummary(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $summary = $this->getMonthlySummary($month, $year);
        $employees = Employee::where('is_active', true)->get();
        $employeeStats = $this->getEmployeeStats($employees, $month, $year);
        $todaySummary = $this->getTodaySummary();

        $recentAttendances = Attendance::with('employee')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'employee_name' => $a->employee->name,
                    'employee_id' => $a->employee->employee_id,
                    'department' => $a->employee->department,
                    'type' => $a->type,
                    'type_label' => $a->getTypeLabel(),
                    'date' => $a->date->format('d M Y'),
                    'time' => $a->time,
                    'distance' => $a->distance_meters,
                    'note' => $a->note,
                ];
            });

        return response()->json([
            'summary' => $summary,
            'employeeStats' => $employeeStats,
            'todaySummary' => $todaySummary,
            'recentAttendances' => $recentAttendances,
        ]);
    }

    /**
     * Employee management page
     */
    public function employees(Request $request)
    {
        $employees = Employee::withCount('attendances')
            ->orderBy('name')
            ->get();

        return view('admin.employees', compact('employees'));
    }

    /**
     * Store new employee
     */
    public function storeEmployee(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|unique:employees,employee_id',
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        $employee = Employee::create($request->only(['employee_id', 'name', 'department', 'position']));

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil ditambahkan.',
            'employee' => $employee,
        ]);
    }

    /**
     * Delete employee
     */
    public function deleteEmployee(Employee $employee)
    {
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil dihapus.',
        ]);
    }

    /**
     * Office location management
     */
    public function locations()
    {
        $locations = OfficeLocation::all();
        return view('admin.locations', compact('locations'));
    }

    /**
     * Off day schedule management page
     */
    public function workSchedules()
    {
        $employees = Employee::with('offDays')->where('is_active', true)->orderBy('name')->get();
        $schedules = OffDay::with('employee')
            ->orderBy('employee_id')
            ->orderBy('day_of_week')
            ->get();
        $swapRequests = ScheduleSwapRequest::with(['employee', 'swapWithEmployee'])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_schedules' => OffDay::count(),
            'pending_requests' => ScheduleSwapRequest::where('status', ScheduleSwapRequest::STATUS_PENDING)->count(),
            'approved_requests' => ScheduleSwapRequest::where('status', ScheduleSwapRequest::STATUS_APPROVED)->count(),
            'rejected_requests' => ScheduleSwapRequest::where('status', ScheduleSwapRequest::STATUS_REJECTED)->count(),
        ];

        $dayNames = OffDay::DAY_NAMES;

        return view('admin.schedules', compact('employees', 'schedules', 'swapRequests', 'stats', 'dayNames'));
    }

    /**
     * Store off day schedule (day_of_week pattern)
     */
    public function storeWorkSchedule(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'day_of_week' => 'required|integer|min:0|max:6',
        ]);

        $schedule = OffDay::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'day_of_week' => $request->day_of_week,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Jadwal libur berhasil disimpan.',
            'schedule' => $schedule->load('employee'),
        ]);
    }

    /**
     * Delete an off day schedule
     */
    public function deleteWorkSchedule(OffDay $schedule)
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal libur berhasil dihapus.',
        ]);
    }

    /**
     * Approve swap request
     */
    public function approveSwapRequest(ScheduleSwapRequest $swapRequest)
    {
        $swapRequest->update([
            'status' => ScheduleSwapRequest::STATUS_APPROVED,
            'reviewed_by' => 'Admin',
            'reviewed_at' => now(),
            'admin_note' => request('admin_note', $swapRequest->admin_note),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan tukar jadwal berhasil disetujui.',
        ]);
    }

    /**
     * Reject swap request
     */
    public function rejectSwapRequest(ScheduleSwapRequest $swapRequest)
    {
        $swapRequest->update([
            'status' => ScheduleSwapRequest::STATUS_REJECTED,
            'reviewed_by' => 'Admin',
            'reviewed_at' => now(),
            'admin_note' => request('admin_note', $swapRequest->admin_note),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan tukar jadwal ditolak.',
        ]);
    }

    /**
     * Store office location
     */
    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:100|max:5000',
        ]);

        $location = OfficeLocation::create($request->only(['name', 'latitude', 'longitude', 'radius_meters']));

        return response()->json([
            'success' => true,
            'message' => 'Lokasi kantor berhasil ditambahkan.',
            'location' => $location,
        ]);
    }

    /**
     * Delete office location
     */
    public function deleteLocation(OfficeLocation $location)
    {
        $location->delete();
        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil dihapus.',
        ]);
    }

    /**
     * Employee detail with attendance history
     */
    public function employeeDetail(Employee $employee, Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $attendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        // Group by date
        $grouped = $attendances->groupBy(function ($item) {
            return $item->date->format('Y-m-d');
        });

        $dailyRecords = [];
        foreach ($grouped as $date => $records) {
            $inRecord = $records->where('type', 'IN')->first();
            $outRecord = $records->where('type', 'OUT')->first();
            $workDuration = Attendance::getWorkDuration($employee->employee_id, $date);

            $hours = $workDuration ? floor($workDuration / 60) : 0;
            $minutes = $workDuration ? $workDuration % 60 : 0;

            $dailyRecords[] = [
                'date' => Carbon::parse($date)->format('d M Y'),
                'day' => Carbon::parse($date)->translatedFormat('l'),
                'in_time' => $inRecord ? $inRecord->time : '-',
                'out_time' => $outRecord ? $outRecord->time : '-',
                'work_duration' => $workDuration ? "{$hours}j {$minutes}m" : '-',
                'work_minutes' => $workDuration ?? 0,
                'is_sufficient' => $workDuration >= 480, // 8 hours = 480 minutes
                'records' => $records->map(function ($r) {
                    return [
                        'type' => $r->type,
                        'type_label' => $r->getTypeLabel(),
                        'time' => $r->time,
                        'note' => $r->note,
                    ];
                }),
            ];
        }

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'employee_id' => $employee->employee_id,
                'name' => $employee->name,
                'department' => $employee->department,
                'position' => $employee->position,
            ],
            'daily_records' => $dailyRecords,
            'month' => $month,
            'year' => $year,
        ]);
    }

    // ===== Private Helper Methods =====

    private function getMonthlySummary($month, $year)
    {
        $totalIn = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'IN')
            ->count();

        $totalOut = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'OUT')
            ->count();

        $totalIzin = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'IZIN')
            ->count();

        $totalSakit = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'SAKIT')
            ->count();

        $totalTukarLibur = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'TUKAR_LIBUR')
            ->count();

        // Calculate absent (working days without any attendance)
        $totalEmployees = Employee::where('is_active', true)->count();
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $today = Carbon::today();
        if ($endDate->gt($today)) {
            $endDate = $today;
        }

        $workingDays = 0;
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        // Count unique employee-dates with attendance
        $attendedDays = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereIn('type', ['IN', 'IZIN', 'SAKIT', 'TUKAR_LIBUR'])
            ->select('employee_id', 'date')
            ->distinct()
            ->count();

        $expectedDays = $workingDays * $totalEmployees;
        $totalAbsen = max(0, $expectedDays - $attendedDays);

        return [
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_tukar_libur' => $totalTukarLibur,
            'total_absen' => $totalAbsen,
            'working_days' => $workingDays,
            'total_employees' => $totalEmployees,
        ];
    }

    private function getEmployeeStats($employees, $month, $year)
    {
        return $employees->map(function ($employee) use ($month, $year) {
            $attendances = Attendance::where('employee_id', $employee->employee_id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $totalIn = $attendances->where('type', 'IN')->count();
            $totalOut = $attendances->where('type', 'OUT')->count();
            $totalIzin = $attendances->where('type', 'IZIN')->count();
            $totalSakit = $attendances->where('type', 'SAKIT')->count();

            // Average work duration
            $dates = $attendances->where('type', 'IN')->pluck('date')->unique();
            $totalDuration = 0;
            $daysWithDuration = 0;
            foreach ($dates as $date) {
                $duration = Attendance::getWorkDuration($employee->employee_id, $date);
                if ($duration) {
                    $totalDuration += $duration;
                    $daysWithDuration++;
                }
            }
            $avgDuration = $daysWithDuration > 0 ? round($totalDuration / $daysWithDuration) : 0;
            $avgHours = floor($avgDuration / 60);
            $avgMinutes = $avgDuration % 60;

            return [
                'id' => $employee->id,
                'employee_id' => $employee->employee_id,
                'name' => $employee->name,
                'department' => $employee->department,
                'position' => $employee->position,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'total_izin' => $totalIzin,
                'total_sakit' => $totalSakit,
                'avg_work_duration' => $avgDuration > 0 ? "{$avgHours}j {$avgMinutes}m" : '-',
            ];
        });
    }

    private function getTodaySummary()
    {
        $totalEmployees = Employee::where('is_active', true)->count();

        $todayIn = Attendance::whereDate('date', today())
            ->where('type', 'IN')
            ->distinct('employee_id')
            ->count('employee_id');

        $todayOut = Attendance::whereDate('date', today())
            ->where('type', 'OUT')
            ->distinct('employee_id')
            ->count('employee_id');

        $todayIzin = Attendance::whereDate('date', today())
            ->where('type', 'IZIN')
            ->distinct('employee_id')
            ->count('employee_id');

        $todaySakit = Attendance::whereDate('date', today())
            ->where('type', 'SAKIT')
            ->distinct('employee_id')
            ->count('employee_id');

        $notPresent = $totalEmployees - $todayIn - $todayIzin - $todaySakit;

        return [
            'total_employees' => $totalEmployees,
            'present' => $todayIn,
            'out' => $todayOut,
            'izin' => $todayIzin,
            'sakit' => $todaySakit,
            'not_present' => max(0, $notPresent),
        ];
    }
}
