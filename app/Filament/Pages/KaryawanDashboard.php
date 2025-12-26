<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class KaryawanDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.karyawan-dashboard';
    
    public static function shouldRegisterNavigation(): bool
    {
        // Fitur dinonaktifkan
        return false;
    }
    
    public static function canAccess(): bool
    {
        // Fitur dinonaktifkan
        return false;
    }
}
