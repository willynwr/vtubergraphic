<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            // RIGGER
            ['employee_id' => 'VTG-001', 'name' => 'Yohan', 'department' => 'RIGGER', 'position' => 'Staff'],
            ['employee_id' => 'VTG-002', 'name' => 'Lina', 'department' => 'RIGGER', 'position' => 'Staff'],
            ['employee_id' => 'VTG-003', 'name' => 'Reza', 'department' => 'RIGGER', 'position' => 'Staff'],
            ['employee_id' => 'VTG-004', 'name' => 'Ragil', 'department' => 'RIGGER', 'position' => 'Staff'],

            // PNG TUBER
            ['employee_id' => 'VTG-005', 'name' => 'Rofi', 'department' => 'PNG TUBER', 'position' => 'Staff'],

            // SOCMED
            ['employee_id' => 'VTG-006', 'name' => 'Indy', 'department' => 'SOCMED', 'position' => 'Staff'],
            ['employee_id' => 'VTG-007', 'name' => 'Bunga', 'department' => 'SOCMED', 'position' => 'Staff'],

            // PNGTUBER / RIGGER
            ['employee_id' => 'VTG-008', 'name' => 'Septian', 'department' => 'RIGGER', 'position' => 'Staff'],

            // PNG TUBER / CC
            ['employee_id' => 'VTG-009', 'name' => 'Trian', 'department' => 'PNG TUBER', 'position' => 'Staff'],

            // CC
            ['employee_id' => 'VTG-010', 'name' => 'Yenni', 'department' => 'CC', 'position' => 'Staff'],
            ['employee_id' => 'VTG-011', 'name' => 'Rahmat', 'department' => 'CC', 'position' => 'Staff'],

            // AR / PNG TUBER
            ['employee_id' => 'VTG-012', 'name' => 'Yayuk', 'department' => 'AR / PNG TUBER', 'position' => 'Staff'],

            // AR
            ['employee_id' => 'VTG-013', 'name' => 'April', 'department' => 'AR', 'position' => 'Staff'],
            ['employee_id' => 'VTG-014', 'name' => 'Ridho', 'department' => 'AR', 'position' => 'Staff'],

            // ILLUSTRATOR
            ['employee_id' => 'VTG-015', 'name' => 'Tetto', 'department' => 'ILLUSTRATOR', 'position' => 'Staff'],
        ];

        foreach ($employees as $emp) {
            Employee::create($emp);
        }
    }
}
