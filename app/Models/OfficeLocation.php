<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate distance between two GPS coordinates using Haversine formula
     * Returns distance in meters
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if given coordinates are within this office location's radius
     */
    public function isWithinRadius($latitude, $longitude)
    {
        $distance = self::calculateDistance(
            $this->latitude,
            $this->longitude,
            $latitude,
            $longitude
        );

        return [
            'within' => $distance <= $this->radius_meters,
            'distance' => round($distance, 2),
        ];
    }

    /**
     * Get the nearest office location for given coordinates
     */
    public static function getNearestOffice($latitude, $longitude)
    {
        $offices = self::where('is_active', true)->get();
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($offices as $office) {
            $distance = self::calculateDistance(
                $office->latitude,
                $office->longitude,
                $latitude,
                $longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $office;
            }
        }

        return [
            'office' => $nearest,
            'distance' => round($minDistance, 2),
            'within' => $nearest ? $minDistance <= $nearest->radius_meters : false,
        ];
    }
}
