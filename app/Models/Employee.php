<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'department',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id');
    }

    public function todayAttendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id')->whereDate('date', today());
    }

    public function offDays()
    {
        return $this->hasMany(OffDay::class, 'employee_id', 'employee_id');
    }

    public function offDayOverrides()
    {
        return $this->hasMany(OffDayOverride::class, 'employee_id', 'employee_id');
    }

    public function scheduleSwapRequests()
    {
        return $this->hasMany(ScheduleSwapRequest::class, 'employee_id', 'employee_id');
    }

    public function getQrDataAttribute()
    {
        return $this->employee_id;
    }

    /**
     * Get colleagues in the same department.
     */
    public function departmentColleagues()
    {
        return self::where('department', $this->department)
            ->where('employee_id', '!=', $this->employee_id)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get off day names (e.g., "Jumat, Sabtu").
     */
    public function getOffDayNamesAttribute()
    {
        return $this->offDays->map(fn($od) => $od->day_name)->implode(', ');
    }
}
