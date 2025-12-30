<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAttendances extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Absensi Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->with(['user', 'attendanceLocation'])
                    ->orderBy('attendance_time', 'desc')
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->iconColor('primary')
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe Absensi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'check_in' => 'success',
                        'check_out' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'check_in' => 'heroicon-m-arrow-right-on-rectangle',
                        'check_out' => 'heroicon-m-arrow-left-on-rectangle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'check_in' => 'Check In',
                        'check_out' => 'Check Out',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('attendance_time')
                    ->label('Waktu Absensi')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-m-clock')
                    ->iconColor('warning'),
                
                Tables\Columns\TextColumn::make('attendanceLocation.name')
                    ->label('Lokasi')
                    ->sortable()
                    ->icon('heroicon-m-map-pin')
                    ->iconColor('info')
                    ->default('-'),
                
                Tables\Columns\TextColumn::make('coordinates')
                    ->label('Koordinat')
                    ->formatStateUsing(fn ($record) => 
                        sprintf('%.6f, %.6f', $record->latitude, $record->longitude)
                    )
                    ->copyable()
                    ->copyMessage('Koordinat disalin!')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('attendance_time', 'desc')
            ->striped()
            ->description('15 data absensi terakhir dari semua karyawan');
    }
}
