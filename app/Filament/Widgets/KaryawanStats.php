<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Overtime;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KaryawanStats extends BaseWidget
{
    protected static ?int $sort = 1;
    
    public static function canView(): bool
    {
        return auth()->user()->role === 'karyawan';
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        // Hitung kehadiran bulan ini
        $totalHadir = Attendance::where('user_id', $user->id)
            ->where('type', 'check_in')
            ->whereBetween('attendance_time', [$startOfMonth, $endOfMonth])
            ->count();
        
        // Hitung terlambat bulan ini
        $terlambat = 0;
        if ($user->shift) {
            $attendances = Attendance::where('user_id', $user->id)
                ->where('type', 'check_in')
                ->whereBetween('attendance_time', [$startOfMonth, $endOfMonth])
                ->get();
            
            foreach ($attendances as $attendance) {
                $shiftStart = Carbon::parse($attendance->attendance_time->format('Y-m-d') . ' ' . $user->shift->start_time);
                $checkInTime = Carbon::parse($attendance->attendance_time);
                $lateThreshold = $shiftStart->copy()->addMinutes(15);
                
                if ($checkInTime->gt($lateThreshold)) {
                    $terlambat++;
                }
            }
        }
        
        // Saldo cuti (assume 12 hari per tahun)
        $cutiBulanIni = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('start_date', Carbon::now()->year)
            ->sum('days');
        $saldoCuti = 12 - $cutiBulanIni;
        
        // Total overtime disetujui bulan ini
        $overtimeJam = Overtime::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('duration_minutes') / 60;
        
        // Check-in hari ini
        $checkInToday = Attendance::where('user_id', $user->id)
            ->where('type', 'check_in')
            ->whereDate('attendance_time', Carbon::today())
            ->first();
        
        $checkInStatus = $checkInToday 
            ? '✓ ' . $checkInToday->attendance_time->format('H:i')
            : 'Belum Check-in';
        
        return [
            Stat::make('Kehadiran Bulan Ini', $totalHadir . ' hari')
                ->description($terlambat > 0 ? $terlambat . ' kali terlambat' : 'Tidak pernah terlambat')
                ->descriptionIcon($terlambat > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($terlambat > 0 ? 'warning' : 'success')
                ->chart([3, 5, 8, 12, 15, 18, $totalHadir]),
            
            Stat::make('Saldo Cuti', $saldoCuti . ' hari')
                ->description('Dari total 12 hari/tahun')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($saldoCuti > 5 ? 'success' : 'warning'),
            
            Stat::make('Overtime Bulan Ini', round($overtimeJam, 1) . ' jam')
                ->description('Total jam lembur disetujui')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
            
            Stat::make('Check-in Hari Ini', $checkInStatus)
                ->description($user->shift ? 'Shift: ' . $user->shift->start_time . ' - ' . $user->shift->end_time : 'Tidak ada shift')
                ->descriptionIcon('heroicon-m-finger-print')
                ->color($checkInToday ? 'success' : 'gray'),
        ];
    }
}
