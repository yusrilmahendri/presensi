<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\ShiftChangeRequestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect('/admin');
        }
        return redirect()->route('karyawan.dashboard');
    }
    return redirect()->route('login');
});

// Auth routes for karyawan
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); // Gunakan 'login' sebagai primary name
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('karyawan.logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('karyawan.dashboard');
    
    // Profile routes for karyawan
    Route::get('/profile', [DashboardController::class, 'profile'])->name('karyawan.profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('karyawan.profile.update');
    
    // Export routes for karyawan
    Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])->name('karyawan.export.excel');
    Route::get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf'])->name('karyawan.export.pdf');
    
    // Leave routes for karyawan
    Route::get('/leaves', [LeaveController::class, 'index'])->name('karyawan.leaves.index');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('karyawan.leaves.store');
    
    // Overtime routes for karyawan
    Route::get('/overtime', [OvertimeController::class, 'index'])->name('karyawan.overtime.index');
    Route::post('/overtime', [OvertimeController::class, 'store'])->name('karyawan.overtime.store');
    Route::delete('/overtime/{id}', [OvertimeController::class, 'cancel'])->name('karyawan.overtime.cancel');
    
    // Shift Change Request routes for karyawan
    Route::get('/shift-change', [ShiftChangeRequestController::class, 'index'])->name('shift-change.index');
    Route::post('/shift-change', [ShiftChangeRequestController::class, 'store'])->name('shift-change.store');
    Route::delete('/shift-change/{id}', [ShiftChangeRequestController::class, 'cancel'])->name('shift-change.cancel');
    
    // Attendance routes - require login
    Route::get('/presensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/presensi', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/log-fake-gps', [AttendanceController::class, 'logFakeGps'])->name('attendance.log-fake-gps');
    Route::post('/attendance/submit-overtime', [AttendanceController::class, 'submitOvertime'])->name('attendance.submit-overtime');
});

