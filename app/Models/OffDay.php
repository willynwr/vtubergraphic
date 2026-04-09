<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OffDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'day_of_week', // 0=Sunday, 1=Monday, ..., 6=Saturday
    ];

    // Day constants
    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    const DAY_NAMES = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function getDayNameAttribute()
    {
        return self::DAY_NAMES[$this->day_of_week] ?? '-';
    }

    /**
     * Generate all off dates for a given month based on weekly pattern + overrides.
     */
    public static function getMonthlyOffDates($employeeId, $month, $year)
    {
        // Get the fixed weekly off days
        $offDayNumbers = self::where('employee_id', $employeeId)->pluck('day_of_week')->toArray();

        // Generate all dates in the month that fall on those days
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $offDates = [];

        $current = $start->copy();
        while ($current->lte($end)) {
            if (in_array($current->dayOfWeek, $offDayNumbers)) {
                $offDates[$current->format('Y-m-d')] = true;
            }
            $current->addDay();
        }

        // Apply overrides
        $overrides = OffDayOverride::where('employee_id', $employeeId)
            ->whereMonth('override_date', $month)
            ->whereYear('override_date', $year)
            ->get();

        foreach ($overrides as $override) {
            $dateStr = $override->override_date->format('Y-m-d');
            if ($override->is_off) {
                $offDates[$dateStr] = true; // Force off
            } else {
                unset($offDates[$dateStr]); // Force work
            }
        }

        return array_keys($offDates);
    }

    /**
     * Check if a specific date is an off day for an employee.
     */
    public static function isOffDay($employeeId, $date)
    {
        $date = Carbon::parse($date);

        // Check override first
        $override = OffDayOverride::where('employee_id', $employeeId)
            ->whereDate('override_date', $date)
            ->first();

        if ($override) {
            return $override->is_off;
        }

        // Check weekly pattern
        $offDayNumbers = self::where('employee_id', $employeeId)->pluck('day_of_week')->toArray();
        return in_array($date->dayOfWeek, $offDayNumbers);
    }

    /**
     * Count remaining off days in the current month.
     */
    public static function remainingThisMonth($employeeId)
    {
        $offDates = self::getMonthlyOffDates($employeeId, now()->month, now()->year);

        // Count only future off days (including today)
        $remaining = 0;
        foreach ($offDates as $dateStr) {
            if (Carbon::parse($dateStr)->gte(now()->startOfDay())) {
                $remaining++;
            }
        }

        return $remaining;
    }

    /**
     * Count total off days in the current month.
     */
    public static function totalThisMonth($employeeId)
    {
        return count(self::getMonthlyOffDates($employeeId, now()->month, now()->year));
    }
}
