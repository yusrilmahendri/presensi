<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;

class PendingLeaves extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Pengajuan Izin yang Perlu Direview')
            ->description('Klik "Lihat Semua" untuk melihat semua pengajuan izin')
            ->query(
                Leave::query()
                    ->where('status', 'pending')
                    ->with(['user', 'approvedBy'])
                    ->latest()
                    ->limit(10)
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
                TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('attachment')
                    ->label('Lampiran')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state): string => $state ? 'Ada' : '-')
                    ->icon(fn ($state): ?string => $state ? 'heroicon-o-paper-clip' : null),
                TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Catatan (Opsional)')
                            ->maxLength(65535),
                    ])
                    ->action(function (Leave $record, array $data) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'admin_notes' => $data['admin_notes'] ?? null,
                        ]);
                    })
                    ->successNotificationTitle('Pengajuan izin berhasil disetujui'),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(65535),
                    ])
                    ->action(function (Leave $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'admin_notes' => $data['admin_notes'],
                        ]);
                    })
                    ->successNotificationTitle('Pengajuan izin berhasil ditolak'),
                Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Leave $record): string => route('filament.admin.resources.leaves.view', $record))
                    ->openUrlInNewTab(false),
            ])
            ->emptyStateHeading('Tidak ada pengajuan izin yang perlu direview')
            ->emptyStateDescription('Semua pengajuan izin sudah diproses atau belum ada pengajuan baru.')
            ->emptyStateIcon('heroicon-o-check-badge');
    }
}
