<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Show the QR scanner page
     */
    public function scanner()
    {
        return view('attendance.scanner');
    }

    /**
     * Validate employee from QR code
     */
    public function validateEmployee(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan atau tidak aktif.',
            ], 404);
        }

        $request->session()->regenerate();
        $request->session()->put('employee_portal_id', $employee->employee_id);

        // Get today's attendance records
        $todayRecords = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', today())
            ->orderBy('time', 'asc')
            ->get();

        $hasCheckedIn = $todayRecords->where('type', 'IN')->isNotEmpty();
        $hasCheckedOut = $todayRecords->where('type', 'OUT')->isNotEmpty();

        // Calculate work duration if both IN and OUT exist
        $workDuration = null;
        if ($hasCheckedIn) {
            $workDuration = Attendance::getWorkDuration($employee->employee_id, today());
        }

        return response()->json([
            'success' => true,
            'portal_url' => route('portal.index'),
            'employee' => [
                'id' => $employee->id,
                'employee_id' => $employee->employee_id,
                'name' => $employee->name,
                'department' => $employee->department,
                'position' => $employee->position,
            ],
            'today' => [
                'records' => $todayRecords->map(function ($r) {
                    return [
                        'type' => $r->type,
                        'type_label' => $r->getTypeLabel(),
                        'time' => $r->time,
                    ];
                }),
                'has_checked_in' => $hasCheckedIn,
                'has_checked_out' => $hasCheckedOut,
                'work_duration_minutes' => $workDuration,
            ],
        ]);
    }

    /**
     * Store attendance record
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
            'type' => 'required|in:IN,OUT,IZIN,SAKIT,TUKAR_LIBUR',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'note' => 'nullable|string|max:500',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan.',
            ], 404);
        }

        // Only validate GPS for IN and OUT types
        $distance = null;
        if (in_array($request->type, ['IN', 'OUT'])) {
            $result = OfficeLocation::getNearestOffice($request->latitude, $request->longitude);

            if (!$result['within']) {
                $distanceKm = round($result['distance'] / 1000, 2);
                return response()->json([
                    'success' => false,
                    'message' => "Lokasi Anda terlalu jauh dari kantor. Jarak: {$distanceKm} KM. Maksimal 1 KM.",
                    'distance' => $result['distance'],
                ], 422);
            }
            $distance = $result['distance'];
        }

        // Check duplicate for IN - only one IN per day
        if ($request->type === 'IN') {
            $existing = Attendance::where('employee_id', $employee->employee_id)
                ->whereDate('date', today())
                ->where('type', 'IN')
                ->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen masuk hari ini pada ' . $existing->time,
                ], 422);
            }
        }

        // Check duplicate for OUT - only one OUT per day
        if ($request->type === 'OUT') {
            $existing = Attendance::where('employee_id', $employee->employee_id)
                ->whereDate('date', today())
                ->where('type', 'OUT')
                ->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen pulang hari ini pada ' . $existing->time,
                ], 422);
            }

            // Must have checked IN first
            $hasIn = Attendance::where('employee_id', $employee->employee_id)
                ->whereDate('date', today())
                ->where('type', 'IN')
                ->exists();
            if (!$hasIn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan absen masuk hari ini.',
                ], 422);
            }
        }

        $now = Carbon::now();
        $attendance = Attendance::create([
            'employee_id' => $employee->employee_id,
            'type' => $request->type,
            'date' => $now->toDateString(),
            'time' => $now->toTimeString(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance_meters' => $distance,
            'note' => $request->note,
        ]);

        // Calculate work duration if OUT
        $workDuration = null;
        $workDurationFormatted = null;
        if ($request->type === 'OUT') {
            $workDuration = Attendance::getWorkDuration($employee->employee_id, today());
            if ($workDuration) {
                $hours = floor($workDuration / 60);
                $minutes = $workDuration % 60;
                $workDurationFormatted = "{$hours} jam {$minutes} menit";
            }
        }

        $typeLabels = Attendance::getTypes();

        return response()->json([
            'success' => true,
            'message' => "Absen {$typeLabels[$request->type]} berhasil dicatat!",
            'attendance' => [
                'type' => $attendance->type,
                'type_label' => $attendance->getTypeLabel(),
                'date' => $attendance->date->format('d M Y'),
                'time' => $attendance->time,
                'distance_meters' => $distance,
            ],
            'work_duration' => $workDurationFormatted,
        ]);
    }
}
