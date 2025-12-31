<?php

namespace App\Filament\Widgets;

use App\Models\Organization;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuperAdminStats extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }
    
    protected function getStats(): array
    {
        $totalOrganizations = Organization::count();
        $activeOrganizations = Organization::where('is_active', true)->count();
        $totalAdmins = User::where('role', 'admin')->count();
        
        // Organization stats by type
        $umkmCount = Organization::where('type', 'umkm')->count();
        $instansiCount = Organization::where('type', 'instansi')->count();
        $perusahaanCount = Organization::where('type', 'perusahaan')->count();

        return [
            Stat::make('Total Bisnis', $totalOrganizations)
                ->description("{$activeOrganizations} bisnis aktif")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([$umkmCount, $instansiCount, $perusahaanCount, $totalOrganizations]),
            
            Stat::make('UMKM', $umkmCount)
                ->description('Bisnis UMKM terdaftar')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success'),
            
            Stat::make('Instansi Pemerintah', $instansiCount)
                ->description('Instansi pemerintah terdaftar')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('info'),
            
            Stat::make('Perusahaan', $perusahaanCount)
                ->description('Perusahaan terdaftar')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('warning'),
            
            Stat::make('Total Admin', $totalAdmins)
                ->description('Admin dari semua bisnis')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('danger'),
        ];
    }
}
