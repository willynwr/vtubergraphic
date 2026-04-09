<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            OfficeLocationSeeder::class,
            EmployeeSeeder::class,
            OffDaySeeder::class,
            AttendanceSeeder::class,
            SwapRequestSeeder::class,
        ]);
    }
}
