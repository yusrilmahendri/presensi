<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class RecentLeaveApprovals extends BaseWidget
{
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Riwayat Persetujuan Izin Terbaru')
            ->description('5 persetujuan terakhir')
            ->query(
                Leave::query()
                    ->whereIn('status', ['approved', 'rejected'])
                    ->with(['user', 'approvedBy'])
                    ->latest('approved_at')
                    ->limit(5)
            )
            ->headerActions([
                Tables\Actions\Action::make('view_all')
                    ->label('Lihat Semua')
                    ->icon('heroicon-o-arrow-right')
                    ->url(route('filament.admin.resources.leaves.index'))
                    ->color('primary'),
            ])
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sakit' => 'danger',
                        'izin' => 'warning',
                        'cuti' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'cuti' => 'Cuti',
                        default => $state,
                    }),
                TextColumn::make('start_date')
                    ->label('Dari')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Sampai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('total_days')
                    ->label('Hari')
                    ->numeric()
                    ->suffix(' hari'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
                TextColumn::make('approvedBy.name')
                    ->label('Oleh')
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Leave $record): string => route('filament.admin.resources.leaves.view', $record))
                    ->openUrlInNewTab(false),
            ])
            ->emptyStateHeading('Belum ada riwayat persetujuan')
            ->emptyStateDescription('Riwayat persetujuan akan muncul di sini setelah Anda menyetujui atau menolak pengajuan.')
            ->emptyStateIcon('heroicon-o-clock');
    }
}
