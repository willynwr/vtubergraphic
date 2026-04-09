<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'date',
        'time',
        'latitude',
        'longitude',
        'distance_meters',
        'note',
        'photo',
    ];

    protected $casts = [
        'date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'distance_meters' => 'decimal:2',
    ];

    const TYPE_IN = 'IN';
    const TYPE_OUT = 'OUT';
    const TYPE_IZIN = 'IZIN';
    const TYPE_SAKIT = 'SAKIT';
    const TYPE_TUKAR_LIBUR = 'TUKAR_LIBUR';

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public static function getTypes()
    {
        return [
            self::TYPE_IN => 'Masuk',
            self::TYPE_OUT => 'Pulang',
            self::TYPE_IZIN => 'Izin',
            self::TYPE_SAKIT => 'Sakit',
            self::TYPE_TUKAR_LIBUR => 'Tukar Libur',
        ];
    }

    public function getTypeLabel()
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Calculate work duration for a given employee on a given date
     */
    public static function getWorkDuration($employeeId, $date)
    {
        $checkIn = self::where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->where('type', self::TYPE_IN)
            ->orderBy('time', 'asc')
            ->first();

        $checkOut = self::where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->where('type', self::TYPE_OUT)
            ->orderBy('time', 'desc')
            ->first();

        if ($checkIn && $checkOut) {
            $start = \Carbon\Carbon::parse($checkIn->date->format('Y-m-d') . ' ' . $checkIn->time);
            $end = \Carbon\Carbon::parse($checkOut->date->format('Y-m-d') . ' ' . $checkOut->time);
            return $end->diffInMinutes($start);
        }

        return null;
    }
}
