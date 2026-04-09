<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSwapRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';

    protected $fillable = [
        'employee_id',
        'off_day_id',
        'requested_date',
        'target_date',
        'swap_with_employee_id',
        'reason',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'target_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function swapWithEmployee()
    {
        return $this->belongsTo(Employee::class, 'swap_with_employee_id', 'employee_id');
    }

    public function offDay()
    {
        return $this->belongsTo(OffDay::class, 'off_day_id');
    }

    /**
     * @deprecated Use offDay() instead
     */
    public function workSchedule()
    {
        return $this->offDay();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu',
        };
    }
}