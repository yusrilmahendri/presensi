<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeaveStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalLeaves = Leave::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();
        $rejectedLeaves = Leave::where('status', 'rejected')->count();

        $thisMonthLeaves = Leave::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Total Pengajuan Izin', $totalLeaves)
                ->description('Total semua pengajuan izin')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            
            Stat::make('Menunggu Persetujuan', $pendingLeaves)
                ->description('Pengajuan yang perlu direview')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            Stat::make('Disetujui', $approvedLeaves)
                ->description('Pengajuan yang disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Ditolak', $rejectedLeaves)
                ->description('Pengajuan yang ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
            
            Stat::make('Bulan Ini', $thisMonthLeaves)
                ->description('Pengajuan bulan ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
