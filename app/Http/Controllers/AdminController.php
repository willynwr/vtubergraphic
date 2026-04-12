<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\ScheduleSwapRequest;
use App\Models\OffDay;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AdminController extends Controller
{
    private const ADMIN_GENERAL_PASSWORD = 'office';
    private const ADMIN_CC_PASSWORD = 'capcutcapcut';
    private const ADMIN_SCOPE_GENERAL = 'general';
    private const ADMIN_SCOPE_CC = 'cc';
    private const SESSION_ADMIN_PASSED = 'admin_password_passed';
    private const SESSION_ADMIN_SCOPE = 'admin_scope';

    /**
     * Admin dashboard
     */
    public function dashboard(Request $request)
    {
        return $this->renderAdminPanel($request, 'dashboard');
    }

    /**
     * Show admin password gate
     */
    public function showPasswordForm()
    {
        if (session(self::SESSION_ADMIN_PASSED) === true) {
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

        $scope = null;
        if ($request->input('password') === self::ADMIN_CC_PASSWORD) {
            $scope = self::ADMIN_SCOPE_CC;
        } elseif ($request->input('password') === self::ADMIN_GENERAL_PASSWORD) {
            $scope = self::ADMIN_SCOPE_GENERAL;
        }

        if ($scope === null) {
            return back()
                ->withErrors(['password' => 'Password admin salah.'])
                ->withInput();
        }

        $request->session()->put(self::SESSION_ADMIN_PASSED, true);
        $request->session()->put(self::SESSION_ADMIN_SCOPE, $scope);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Logout admin password session
     */
    public function logoutPassword(Request $request)
    {
        $request->session()->forget([self::SESSION_ADMIN_PASSED, self::SESSION_ADMIN_SCOPE]);

        return redirect()->route('admin.password.form');
    }

    /**
     * API: Get monthly summary data
     */
    public function apiSummary(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $summary = $this->getMonthlySummary($month, $year, $request);
        $employees = Employee::where('is_active', true);
        $employees = $this->applyEmployeeScope($employees, $request)->get();
        $employeeStats = $this->getEmployeeStats($employees, $month, $year);
        $todaySummary = $this->getTodaySummary($request);

        $recentAttendances = Attendance::with('employee')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
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
        return $this->renderAdminPanel($request, 'employees');
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

        $department = $request->input('department');
        $isCcDepartment = $this->isCcDepartment($department);

        if ($this->isCcAdmin($request) && !$isCcDepartment) {
            return response()->json([
                'success' => false,
                'message' => 'Admin CC hanya dapat menambahkan karyawan departemen CC.',
            ], 422);
        }

        if (!$this->isCcAdmin($request) && $isCcDepartment) {
            return response()->json([
                'success' => false,
                'message' => 'Admin general tidak memiliki akses untuk data departemen CC.',
            ], 403);
        }

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
    public function deleteEmployee(Employee $employee, Request $request)
    {
        if (!$this->canAccessEmployee($employee, $request)) {
            abort(403, 'Akses ditolak untuk data karyawan ini.');
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil dihapus.',
        ]);
    }

    /**
     * Office location management
     */
    public function locations(Request $request)
    {
        return $this->renderAdminPanel($request, 'locations');
    }

    /**
     * Off day schedule management page
     */
    public function workSchedules(Request $request)
    {
        return $this->renderAdminPanel($request, 'schedules');
    }

    /**
     * Calendar page
     */
    public function calendar(Request $request)
    {
        return $this->renderAdminPanel($request, 'calendar');
    }

    /**
     * Attendance history page
     */
    public function history(Request $request)
    {
        return $this->renderAdminPanel($request, 'history');
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

        $employee = Employee::where('employee_id', $request->employee_id)->first();
        if (!$employee || !$this->canAccessEmployee($employee, $request)) {
            abort(403, 'Akses ditolak untuk jadwal karyawan ini.');
        }

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
    public function deleteWorkSchedule(OffDay $schedule, Request $request)
    {
        if (!$schedule->employee || !$this->canAccessEmployee($schedule->employee, $request)) {
            abort(403, 'Akses ditolak untuk jadwal karyawan ini.');
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal libur berhasil dihapus.',
        ]);
    }

    /**
     * Approve swap request
     */
    public function approveSwapRequest(ScheduleSwapRequest $swapRequest, Request $request)
    {
        if (!$swapRequest->employee || !$this->canAccessEmployee($swapRequest->employee, $request)) {
            abort(403, 'Akses ditolak untuk permintaan tukar jadwal ini.');
        }

        $request->validate([
            'swap_with_employee_id' => 'nullable|exists:employees,employee_id',
        ]);

        if ($request->filled('swap_with_employee_id')) {
            $targetEmployee = \App\Models\Employee::where('employee_id', $request->swap_with_employee_id)->first();
            if (!$targetEmployee || $targetEmployee->department !== $swapRequest->employee->department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan target harus dari divisi yang sama.'
                ], 422);
            }

            $targetDateCarbon = \Carbon\Carbon::parse($swapRequest->target_date);
            $month = $targetDateCarbon->month;
            $year = $targetDateCarbon->year;
            
            $targetOffDates = \App\Models\OffDay::getMonthlyOffDates($targetEmployee->employee_id, $month, $year);
            $dateString = $targetDateCarbon->format('Y-m-d');
            if (!in_array($dateString, $targetOffDates)) {
                return response()->json([
                    'success' => false,
                    'message' => "Karyawan {$targetEmployee->name} tidak sedang libur pada tanggal target ({$dateString})."
                ], 422);
            }
        }

        $swapRequest->update([
            'status' => ScheduleSwapRequest::STATUS_APPROVED,
            'swap_with_employee_id' => $request->swap_with_employee_id,
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
     * Get eligible employees for a swap request.
     */
    public function eligibleSwapEmployees(ScheduleSwapRequest $swapRequest, Request $request)
    {
        if (!$swapRequest->employee || !$this->canAccessEmployee($swapRequest->employee, $request)) {
            abort(403, 'Akses ditolak.');
        }

        $department = $swapRequest->employee->department;
        $targetDateCarbon = \Carbon\Carbon::parse($swapRequest->target_date);
        $month = $targetDateCarbon->month;
        $year = $targetDateCarbon->year;
        $dateString = $targetDateCarbon->format('Y-m-d');

        $employees = \App\Models\Employee::where('department', $department)
            ->where('employee_id', '!=', $swapRequest->employee_id)
            ->get();

        $eligible = [];
        foreach ($employees as $emp) {
            $offDates = \App\Models\OffDay::getMonthlyOffDates($emp->employee_id, $month, $year);
            if (in_array($dateString, $offDates)) {
                $eligible[] = [
                    'employee_id' => $emp->employee_id,
                    'name' => $emp->name,
                    'position' => $emp->position,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $eligible,
        ]);
    }

    /**
     * Reject swap request
     */
    public function rejectSwapRequest(ScheduleSwapRequest $swapRequest, Request $request)
    {
        if (!$swapRequest->employee || !$this->canAccessEmployee($swapRequest->employee, $request)) {
            abort(403, 'Akses ditolak untuk permintaan tukar jadwal ini.');
        }

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
        if (!$this->canAccessEmployee($employee, $request)) {
            abort(403, 'Akses ditolak untuk data karyawan ini.');
        }

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

    private function renderAdminPanel(Request $request, string $activePage = 'dashboard')
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $summary = $this->getMonthlySummary($month, $year, $request);

        $employees = Employee::withCount('attendances')
            ->where('is_active', true);
        $employees = $this->applyEmployeeScope($employees, $request)->get();
        $employeeStats = $this->getEmployeeStats($employees, $month, $year);

        $recentAttendances = Attendance::with('employee')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $todaySummary = $this->getTodaySummary($request);
        $locations = OfficeLocation::all();

        $pendingSwapRequests = ScheduleSwapRequest::with(['employee', 'swapWithEmployee'])
            ->where('status', ScheduleSwapRequest::STATUS_PENDING)
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->orderByDesc('created_at')
            ->get();

        $schedules = OffDay::with('employee')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->orderBy('employee_id')
            ->orderBy('day_of_week')
            ->get();

        $swapRequests = ScheduleSwapRequest::with(['employee', 'swapWithEmployee'])
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_schedules' => OffDay::whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })->count(),
            'pending_requests' => ScheduleSwapRequest::where('status', ScheduleSwapRequest::STATUS_PENDING)
                ->whereHas('employee', function ($query) use ($request) {
                    $this->applyEmployeeScope($query, $request);
                })->count(),
            'approved_requests' => ScheduleSwapRequest::where('status', ScheduleSwapRequest::STATUS_APPROVED)
                ->whereHas('employee', function ($query) use ($request) {
                    $this->applyEmployeeScope($query, $request);
                })->count(),
            'rejected_requests' => ScheduleSwapRequest::where('status', ScheduleSwapRequest::STATUS_REJECTED)
                ->whereHas('employee', function ($query) use ($request) {
                    $this->applyEmployeeScope($query, $request);
                })->count(),
        ];

        $dayNames = OffDay::DAY_NAMES;

        $offDaysByEmployee = [];
        foreach ($employees as $idx => $emp) {
            $dates = OffDay::getMonthlyOffDates($emp->employee_id, $month, $year);
            
            // Dapatkan absensi IN dan OUT bulan ini untuk employee
            $monthlyAttendances = Attendance::where('employee_id', $emp->employee_id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->whereIn('type', ['IN', 'OUT'])
                ->get();

            // Kumpulkan tanggal yang memiliki record IN
            $inDates = $monthlyAttendances->where('type', 'IN')->map(function($att) {
                return \Carbon\Carbon::parse($att->date)->format('Y-m-d');
            })->unique()->toArray();

            // Kumpulkan tanggal yang memiliki record OUT
            $outDates = $monthlyAttendances->where('type', 'OUT')->map(function($att) {
                return \Carbon\Carbon::parse($att->date)->format('Y-m-d');
            })->unique()->toArray();

            // Hadir/ON berarti dia memiliki IN dan OUT pada hari tersebut
            $attendanceDates = array_values(array_intersect($inDates, $outDates));

            $offDaysByEmployee[$emp->employee_id] = [
                'name' => $emp->name,
                'department' => $emp->department,
                'position' => $emp->position,
                'off_day_names' => $emp->off_day_names,
                'dates' => $dates,
                'attendance_dates' => $attendanceDates,
            ];
        }

        return view('admin.dashboard', compact(
            'summary',
            'employeeStats',
            'recentAttendances',
            'todaySummary',
            'month',
            'year',
            'employees',
            'locations',
            'pendingSwapRequests',
            'schedules',
            'swapRequests',
            'stats',
            'dayNames',
            'activePage',
            'offDaysByEmployee'
        ));
    }

    private function getMonthlySummary($month, $year, Request $request)
    {
        $totalIn = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'IN')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->count();

        $totalOut = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'OUT')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->count();

        $totalIzin = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'IZIN')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->count();

        $totalSakit = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'SAKIT')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->count();

        $totalTukarLibur = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'TUKAR_LIBUR')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->count();

        // Calculate absent (working days without any attendance)
        $totalEmployees = Employee::where('is_active', true);
        $totalEmployees = $this->applyEmployeeScope($totalEmployees, $request)->count();
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
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
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

    private function getTodaySummary(Request $request)
    {
        $totalEmployees = Employee::where('is_active', true);
        $totalEmployees = $this->applyEmployeeScope($totalEmployees, $request)->count();

        $todayIn = Attendance::whereDate('date', today())
            ->where('type', 'IN')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->distinct('employee_id')
            ->count('employee_id');

        $todayOut = Attendance::whereDate('date', today())
            ->where('type', 'OUT')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->distinct('employee_id')
            ->count('employee_id');

        $todayIzin = Attendance::whereDate('date', today())
            ->where('type', 'IZIN')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
            ->distinct('employee_id')
            ->count('employee_id');

        $todaySakit = Attendance::whereDate('date', today())
            ->where('type', 'SAKIT')
            ->whereHas('employee', function ($query) use ($request) {
                $this->applyEmployeeScope($query, $request);
            })
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

    private function isCcAdmin(Request $request): bool
    {
        return $request->session()->get(self::SESSION_ADMIN_SCOPE, self::ADMIN_SCOPE_GENERAL) === self::ADMIN_SCOPE_CC;
    }

    private function canAccessEmployee(Employee $employee, Request $request): bool
    {
        $isCcDepartment = $this->isCcDepartment($employee->department);

        if ($this->isCcAdmin($request)) {
            return $isCcDepartment;
        }

        return !$isCcDepartment;
    }

    private function isCcDepartment(?string $department): bool
    {
        return strtoupper(trim((string) $department)) === 'CC';
    }

    private function applyEmployeeScope(Builder $query, Request $request): Builder
    {
        if ($this->isCcAdmin($request)) {
            return $query->whereRaw('UPPER(TRIM(department)) = ?', ['CC']);
        }

        return $query->where(function ($q) {
            $q->whereNull('department')
                ->orWhereRaw('UPPER(TRIM(department)) <> ?', ['CC']);
        });
    }
}
