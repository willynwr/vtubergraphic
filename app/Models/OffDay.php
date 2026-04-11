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

        // Apply approved swaps for this employee
        // As the requester: gave up requested_date (work), took target_date (off)
        $swapsAsRequester = ScheduleSwapRequest::where('employee_id', $employeeId)
            ->where('status', 'APPROVED')
            ->get();

        foreach ($swapsAsRequester as $swap) {
            $reqDate = Carbon::parse($swap->requested_date)->format('Y-m-d');
            $tgtDate = Carbon::parse($swap->target_date)->format('Y-m-d');
            
            // gave up requested_date -> work
            unset($offDates[$reqDate]);
            
            // took target_date -> off
            $offDates[$tgtDate] = true;
        }

        // As the target: gave up target_date (work), took requested_date (off)
        $swapsAsTarget = ScheduleSwapRequest::where('swap_with_employee_id', $employeeId)
            ->where('status', 'APPROVED')
            ->get();

        foreach ($swapsAsTarget as $swap) {
            $reqDate = Carbon::parse($swap->requested_date)->format('Y-m-d');
            $tgtDate = Carbon::parse($swap->target_date)->format('Y-m-d');
            
            // gave up target_date -> work
            unset($offDates[$tgtDate]);
            
            // took requested_date -> off
            $offDates[$reqDate] = true;
        }

        return array_keys($offDates);
    }

    /**
     * Check if a specific date is an off day for an employee.
     */
    public static function isOffDay($employeeId, $date)
    {
        $dateObj = Carbon::parse($date);
        $dateStr = $dateObj->format('Y-m-d');

        // Check if there's any approved swap that forces this day to ON or OFF
        $swapsAsRequester = ScheduleSwapRequest::where('employee_id', $employeeId)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($dateStr) {
                $q->whereDate('requested_date', $dateStr)
                  ->orWhereDate('target_date', $dateStr);
            })->get();

        // If requester is taking target_date, it's an OFF day.
        // If requester gave up requested_date, it's a WORK day.
        foreach ($swapsAsRequester as $swap) {
            $reqDateStr = Carbon::parse($swap->requested_date)->format('Y-m-d');
            $tgtDateStr = Carbon::parse($swap->target_date)->format('Y-m-d');
            if ($dateStr === $tgtDateStr) return true; // Off
            if ($dateStr === $reqDateStr) return false; // Work
        }

        $swapsAsTarget = ScheduleSwapRequest::where('swap_with_employee_id', $employeeId)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($dateStr) {
                $q->whereDate('requested_date', $dateStr)
                  ->orWhereDate('target_date', $dateStr);
            })->get();

        // If target is taking requested_date, it's an OFF day.
        // If target gave up target_date, it's a WORK day.
        foreach ($swapsAsTarget as $swap) {
            $reqDateStr = Carbon::parse($swap->requested_date)->format('Y-m-d');
            $tgtDateStr = Carbon::parse($swap->target_date)->format('Y-m-d');
            if ($dateStr === $reqDateStr) return true; // Off
            if ($dateStr === $tgtDateStr) return false; // Work
        }

        // Check override first
        $override = OffDayOverride::where('employee_id', $employeeId)
            ->whereDate('override_date', $dateObj)
            ->first();

        if ($override) {
            return $override->is_off;
        }

        // Check weekly pattern
        $offDayNumbers = self::where('employee_id', $employeeId)->pluck('day_of_week')->toArray();
        return in_array($dateObj->dayOfWeek, $offDayNumbers);
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
