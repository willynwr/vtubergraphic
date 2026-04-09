<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OfficeLocation;

class OfficeLocationSeeder extends Seeder
{
    public function run(): void
    {
        OfficeLocation::create([
            'name' => 'Kantor VtuberGraphic',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meters' => 1000,
            'is_active' => true,
        ]);
    }
}
