<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\ScheduleSwapRequest;

class SwapRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Sample swap between RIGGER employees (same department)
        $riggerEmployees = Employee::where('department', 'RIGGER')->get();

        if ($riggerEmployees->count() >= 2) {
            ScheduleSwapRequest::create([
                'employee_id' => $riggerEmployees[0]->employee_id, // Yohan
                'requested_date' => now()->addDays(3)->format('Y-m-d'),
                'swap_with_employee_id' => $riggerEmployees[1]->employee_id, // Lina
                'reason' => 'Perlu menukar jadwal libur karena ada acara keluarga',
                'status' => ScheduleSwapRequest::STATUS_PENDING,
            ]);
        }

        // Sample approved swap between CC employees
        $ccEmployees = Employee::where('department', 'CC')->get();

        if ($ccEmployees->count() >= 2) {
            ScheduleSwapRequest::create([
                'employee_id' => $ccEmployees[0]->employee_id, // Yenni
                'requested_date' => now()->addDays(5)->format('Y-m-d'),
                'swap_with_employee_id' => $ccEmployees[1]->employee_id, // Rahmat
                'reason' => 'Ingin tukar libur untuk keperluan pribadi',
                'status' => ScheduleSwapRequest::STATUS_APPROVED,
                'reviewed_by' => 'Admin',
                'reviewed_at' => now()->subDay(),
            ]);
        }

        // Sample rejected swap between AR employees
        $arEmployees = Employee::where('department', 'AR')->get();

        if ($arEmployees->count() >= 2) {
            ScheduleSwapRequest::create([
                'employee_id' => $arEmployees[0]->employee_id, // April
                'requested_date' => now()->addDays(7)->format('Y-m-d'),
                'swap_with_employee_id' => $arEmployees[1]->employee_id, // Ridho
                'reason' => 'Ada keperluan mendesak, mohon tukar jadwal libur',
                'status' => ScheduleSwapRequest::STATUS_REJECTED,
                'reviewed_by' => 'Admin',
                'reviewed_at' => now()->subDays(2),
                'admin_note' => 'Tidak bisa ditukar, jadwal sudah padat',
            ]);
        }
    }
}
