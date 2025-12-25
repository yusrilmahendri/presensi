<?php

namespace App\Filament\Resources\ShiftChangeRequestResource\Widgets;

use App\Models\ShiftChangeRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShiftChangeStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $organizationId = auth()->user()->organization_id;
        
        $pending = ShiftChangeRequest::where('organization_id', $organizationId)
            ->where('status', 'pending')
            ->count();
            
        $approved = ShiftChangeRequest::where('organization_id', $organizationId)
            ->where('status', 'approved')
            ->count();
            
        $rejected = ShiftChangeRequest::where('organization_id', $organizationId)
            ->where('status', 'rejected')
            ->count();
            
        $total = ShiftChangeRequest::where('organization_id', $organizationId)
            ->count();
        
        return [
            Stat::make('Total Pengajuan', $total)
                ->description('Total semua pengajuan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),
                
            Stat::make('Menunggu Persetujuan', $pending)
                ->description('Perlu ditindaklanjuti')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Disetujui', $approved)
                ->description('Pengajuan yang disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Ditolak', $rejected)
                ->description('Pengajuan yang ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
