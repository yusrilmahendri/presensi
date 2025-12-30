<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisWeek = Carbon::now()->startOfWeek();

        $totalKaryawan = User::where('role', 'karyawan')->count();
        $absensiHariIni = Attendance::whereDate('attendance_time', $today)->count();
        $absensiBulanIni = Attendance::where('attendance_time', '>=', $thisMonth)->count();
        $checkInHariIni = Attendance::whereDate('attendance_time', $today)
            ->where('type', 'check_in')
            ->count();
        $checkOutHariIni = Attendance::whereDate('attendance_time', $today)
            ->where('type', 'check_out')
            ->count();
        $absensiMingguIni = Attendance::where('attendance_time', '>=', $thisWeek)->count();

        return [
            Stat::make('Total Karyawan', $totalKaryawan)
                ->description('Karyawan terdaftar dalam sistem')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([5, 8, 12, 15, 18, 20, $totalKaryawan]),
            
            Stat::make('Check In Hari Ini', $checkInHariIni)
                ->description('Karyawan yang sudah check in')
                ->descriptionIcon('heroicon-m-arrow-right-on-rectangle')
                ->color('success')
                ->chart([2, 4, 6, 8, 10, $checkInHariIni]),
            
            Stat::make('Check Out Hari Ini', $checkOutHariIni)
                ->description('Karyawan yang sudah check out')
                ->descriptionIcon('heroicon-m-arrow-left-on-rectangle')
                ->color('danger')
                ->chart([1, 3, 5, 7, 9, $checkOutHariIni]),
            
            Stat::make('Absensi Hari Ini', $absensiHariIni)
                ->description('Total check in/out hari ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart([3, 6, 9, 12, 15, $absensiHariIni]),
            
            Stat::make('Absensi Minggu Ini', $absensiMingguIni)
                ->description('Total minggu ini')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning')
                ->chart([10, 20, 30, 40, 50, $absensiMingguIni]),
            
            Stat::make('Absensi Bulan Ini', $absensiBulanIni)
                ->description('Total bulan ' . Carbon::now()->format('F'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('purple')
                ->chart([20, 40, 60, 80, 100, $absensiBulanIni]),
        ];
    }
}
