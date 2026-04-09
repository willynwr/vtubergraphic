<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'work_date',
        'shift_name',
        'start_time',
        'end_time',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function getShiftRangeAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
        }

        return $this->shift_name ?: '-';
    }
}