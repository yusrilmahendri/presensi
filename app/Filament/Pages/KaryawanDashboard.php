<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class KaryawanDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.karyawan-dashboard';
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role !== 'super_admin';
    }
    
    public static function canAccess(): bool
    {
        return auth()->user()->role !== 'super_admin';
    }
}
