<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OffDay;

class OffDaySeeder extends Seeder
{
    public function run(): void
    {
        // day_of_week: 0=Minggu, 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu

        $schedules = [
            // RIGGER
            'VTG-001' => [5, 6],    // Yohan → Jumat, Sabtu
            'VTG-002' => [2, 3],    // Lina → Selasa, Rabu
            'VTG-003' => [1, 2],    // Reza → Selasa, Rabu
            'VTG-004' => [3, 4],    // Ragil → Rabu, Kamis

            // PNG TUBER
            'VTG-005' => [5, 6],    // Rofi → Jumat, Sabtu

            // SOCMED
            'VTG-006' => [6, 0],    // Indy → Sabtu, Minggu
            'VTG-007' => [6, 0],    // Bunga → Sabtu, Minggu

            // PNGTUBER / RIGGER
            'VTG-008' => [0, 1],    // Septian → Minggu, Senin

            // PNG TUBER / CC
            'VTG-009' => [6, 0],    // Trian → Sabtu, Minggu

            // CC
            'VTG-010' => [6, 0],    // Yenni → Sabtu, Minggu
            'VTG-011' => [2, 3],    // Rahmat → Selasa, Rabu

            // AR / PNG TUBER
            'VTG-012' => [0, 1],    // Yayuk → Minggu, Senin

            // AR
            'VTG-013' => [1, 2],    // April → Senin, Selasa
            'VTG-014' => [1, 2],    // Ridho → Senin, Selasa

            // ILLUSTRATOR
            'VTG-015' => [4, 5],    // Tetto → Kamis, Jumat
        ];

        foreach ($schedules as $employeeId => $days) {
            foreach ($days as $dayOfWeek) {
                OffDay::create([
                    'employee_id' => $employeeId,
                    'day_of_week' => $dayOfWeek,
                ]);
            }
        }
    }
}
