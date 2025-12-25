<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Barryvdh\DomPDF\Facade\Pdf;

class ManualBook extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static string $view = 'filament.pages.manual-book';
    
    protected static ?string $navigationLabel = 'Manual Book';
    
    protected static ?string $navigationGroup = 'Bantuan';
    
    protected static ?int $navigationSort = 50;
    
    protected static ?string $title = 'Manual Book & Dokumentasi';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function downloadPdf()
    {
        $pdf = Pdf::loadView('pdf.manual-book')
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'Manual_Book_Sistem_Presensi_' . now()->format('Y-m-d') . '.pdf'
        );
    }
}
