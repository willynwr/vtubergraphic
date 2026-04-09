<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffDayOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'override_date',
        'is_off',
        'reason',
    ];

    protected $casts = [
        'override_date' => 'date',
        'is_off' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
