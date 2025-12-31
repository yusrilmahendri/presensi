<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect('/admin');
        }
        return redirect()->route('karyawan.dashboard');
    }
    return redirect()->route('karyawan.login');
});

// Auth routes for karyawan
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('karyawan.login');
    Route::post('/login', [AuthController::class, 'login'])->name('karyawan.login.post');
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
    
    // Attendance routes - require login
    Route::get('/presensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/presensi', [AttendanceController::class, 'store'])->name('attendance.store');
});

