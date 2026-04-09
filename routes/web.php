<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PortalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===== ATTENDANCE (User-facing, no login needed) =====
Route::get('/', [AttendanceController::class, 'scanner'])->name('scanner');
Route::post('/attendance/validate', [AttendanceController::class, 'validateEmployee'])->name('attendance.validate');
Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');

Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PortalController::class, 'index'])->name('index');
    Route::get('/attendance', [PortalController::class, 'attendance'])->name('attendance');
    Route::get('/schedule', [PortalController::class, 'schedule'])->name('schedule');
    Route::get('/swap', [PortalController::class, 'swap'])->name('swap');
    Route::post('/swap-requests', [PortalController::class, 'storeSwapRequest'])->name('swap-requests.store');
    Route::post('/logout', [PortalController::class, 'logout'])->name('logout');
});

// ===== ADMIN DASHBOARD =====
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/password', [AdminController::class, 'showPasswordForm'])->name('password.form');
    Route::post('/password', [AdminController::class, 'verifyPassword'])->name('password.check');
    Route::post('/logout', [AdminController::class, 'logoutPassword'])->name('logout');

    Route::middleware('admin.password')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/api/summary', [AdminController::class, 'apiSummary'])->name('api.summary');

        // Employee Management
        Route::get('/employees', [AdminController::class, 'employees'])->name('employees');
        Route::post('/employees', [AdminController::class, 'storeEmployee'])->name('employees.store');
        Route::delete('/employees/{employee}', [AdminController::class, 'deleteEmployee'])->name('employees.delete');
        Route::get('/employees/{employee}/detail', [AdminController::class, 'employeeDetail'])->name('employees.detail');

        // Location Management
        Route::get('/locations', [AdminController::class, 'locations'])->name('locations');
        Route::post('/locations', [AdminController::class, 'storeLocation'])->name('locations.store');
        Route::delete('/locations/{location}', [AdminController::class, 'deleteLocation'])->name('locations.delete');

        // Off Day (Jadwal Libur) Management
        Route::get('/schedules', [AdminController::class, 'workSchedules'])->name('schedules');
        Route::post('/schedules', [AdminController::class, 'storeWorkSchedule'])->name('schedules.store');
        Route::delete('/schedules/{schedule:id}', [AdminController::class, 'deleteWorkSchedule'])->name('schedules.delete');
        Route::post('/swap-requests/{swapRequest}/approve', [AdminController::class, 'approveSwapRequest'])->name('swap-requests.approve');
        Route::post('/swap-requests/{swapRequest}/reject', [AdminController::class, 'rejectSwapRequest'])->name('swap-requests.reject');
    });
});
