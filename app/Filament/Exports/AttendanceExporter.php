<?php

namespace App\Filament\Exports;

use App\Models\Attendance;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AttendanceExporter extends Exporter
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name')
                ->label('Nama Karyawan'),
            ExportColumn::make('user.nik')
                ->label('NIK'),
            ExportColumn::make('user.department.name')
                ->label('Departemen'),
            ExportColumn::make('type')
                ->label('Tipe')
                ->formatStateUsing(fn (string $state): string => $state === 'check_in' ? 'Check In' : 'Check Out'),
            ExportColumn::make('attendance_time')
                ->label('Waktu'),
            ExportColumn::make('latitude')
                ->label('Latitude'),
            ExportColumn::make('longitude')
                ->label('Longitude'),
            ExportColumn::make('photo')
                ->label('Foto')
                ->formatStateUsing(fn (?string $state): string => $state ? 'Ada' : 'Tidak ada'),
            ExportColumn::make('notes')
                ->label('Catatan'),
            ExportColumn::make('created_at')
                ->label('Dibuat Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export kehadiran selesai! ' . number_format($export->successful_rows) . ' data berhasil di-export.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' data gagal di-export.';
        }

        return $body;
    }
}
