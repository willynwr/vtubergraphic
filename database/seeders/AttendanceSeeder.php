<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OffDay;
use App\Models\OfficeLocation;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $office = OfficeLocation::first();
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        foreach ($employees as $employee) {
            // Get this employee's off day numbers
            $offDayNumbers = OffDay::where('employee_id', $employee->employee_id)
                ->pluck('day_of_week')
                ->toArray();

            $current = $startOfMonth->copy();

            while ($current->lt($today)) {
                $isOffDay = in_array($current->dayOfWeek, $offDayNumbers);

                if ($isOffDay) {
                    $current->addDay();
                    continue;
                }

                // Random attendance (90% present, 5% izin, 5% sakit)
                $rand = rand(1, 100);

                if ($rand <= 90) {
                    $inHour = rand(7, 9);
                    $inMinute = rand(0, 59);
                    Attendance::create([
                        'employee_id' => $employee->employee_id,
                        'type' => 'IN',
                        'date' => $current->format('Y-m-d'),
                        'time' => sprintf('%02d:%02d:00', $inHour, $inMinute),
                        'latitude' => $office->latitude + (rand(-50, 50) / 100000),
                        'longitude' => $office->longitude + (rand(-50, 50) / 100000),
                        'distance_meters' => rand(10, 800),
                    ]);

                    $workHours = rand(8, 10);
                    $outHour = min($inHour + $workHours, 23);
                    $outMinute = rand(0, 59);

                    Attendance::create([
                        'employee_id' => $employee->employee_id,
                        'type' => 'OUT',
                        'date' => $current->format('Y-m-d'),
                        'time' => sprintf('%02d:%02d:00', $outHour, $outMinute),
                        'latitude' => $office->latitude + (rand(-50, 50) / 100000),
                        'longitude' => $office->longitude + (rand(-50, 50) / 100000),
                        'distance_meters' => rand(10, 800),
                    ]);
                } elseif ($rand <= 95) {
                    Attendance::create([
                        'employee_id' => $employee->employee_id,
                        'type' => 'IZIN',
                        'date' => $current->format('Y-m-d'),
                        'time' => '08:00:00',
                        'note' => 'Keperluan keluarga',
                    ]);
                } else {
                    Attendance::create([
                        'employee_id' => $employee->employee_id,
                        'type' => 'SAKIT',
                        'date' => $current->format('Y-m-d'),
                        'time' => '08:00:00',
                        'note' => 'Tidak enak badan',
                    ]);
                }

                $current->addDay();
            }
        }
    }
}
