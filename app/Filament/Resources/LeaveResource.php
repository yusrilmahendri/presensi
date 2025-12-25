<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationLabel = 'Pengajuan Izin';
    
    protected static ?string $navigationGroup = 'Absensi';
    
    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && $user->isAdmin() && !$user->isSuperAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn ($record) => $record !== null),
                Forms\Components\Select::make('type')
                    ->label('Jenis Izin')
                    ->options([
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'cuti' => 'Cuti',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $endDate = $get('end_date');
                        if ($state && $endDate) {
                            $days = \Carbon\Carbon::parse($state)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
                            $set('total_days', $days);
                        }
                    }),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $startDate = $get('start_date');
                        if ($state && $startDate) {
                            $days = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($state)) + 1;
                            $set('total_days', $days);
                        }
                    }),
                Forms\Components\TextInput::make('total_days')
                    ->label('Total Hari')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Textarea::make('reason')
                    ->label('Alasan')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('attachment')
                    ->label('Lampiran (Opsional)')
                    ->image()
                    ->directory('leaves')
                    ->visibility('public')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->required()
                    ->default('pending')
                    ->disabled(fn ($record) => $record === null),
                Forms\Components\Textarea::make('admin_notes')
                    ->label('Catatan Admin')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
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
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Dari')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Sampai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_days')
                    ->label('Jumlah Hari')
                    ->numeric()
                    ->suffix(' hari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Disetujui Oleh')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Persetujuan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->multiple(),
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'cuti' => 'Cuti',
                    ])
                    ->multiple(),
                SelectFilter::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Diajukan Dari')
                            ->placeholder('Pilih tanggal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai')
                            ->placeholder('Pilih tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Diajukan dari ' . \Carbon\Carbon::parse($data['created_from'])->format('d M Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Sampai ' . \Carbon\Carbon::parse($data['created_until'])->format('d M Y');
                        }
                        return $indicators;
                    }),
                Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Periode Dari')
                            ->placeholder('Pilih tanggal'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Sampai')
                            ->placeholder('Pilih tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'Periode dari ' . \Carbon\Carbon::parse($data['date_from'])->format('d M Y');
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators[] = 'Sampai ' . \Carbon\Carbon::parse($data['date_until'])->format('d M Y');
                        }
                        return $indicators;
                    }),
            ])
            ->filtersFormColumns(2)
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
                    ->successNotificationTitle('Pengajuan izin berhasil disetujui')
                    ->visible(fn (Leave $record): bool => $record->isPending()),
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
                    ->successNotificationTitle('Pengajuan izin berhasil ditolak')
                    ->visible(fn (Leave $record): bool => $record->isPending()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Leave $record): bool => $record->isPending()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Setujui Pengajuan Izin Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menyetujui semua pengajuan izin yang dipilih?')
                        ->form([
                            Forms\Components\Textarea::make('admin_notes')
                                ->label('Catatan (Opsional)')
                                ->helperText('Catatan ini akan diterapkan ke semua pengajuan yang dipilih')
                                ->maxLength(65535),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $records->each(function (Leave $record) use ($data) {
                                if ($record->isPending()) {
                                    $record->update([
                                        'status' => 'approved',
                                        'approved_by' => Auth::id(),
                                        'approved_at' => now(),
                                        'admin_notes' => $data['admin_notes'] ?? null,
                                    ]);
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Pengajuan berhasil disetujui'),
                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Tolak Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Tolak Pengajuan Izin Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menolak semua pengajuan izin yang dipilih?')
                        ->form([
                            Forms\Components\Textarea::make('admin_notes')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->helperText('Catatan ini akan diterapkan ke semua pengajuan yang dipilih')
                                ->maxLength(65535),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $records->each(function (Leave $record) use ($data) {
                                if ($record->isPending()) {
                                    $record->update([
                                        'status' => 'rejected',
                                        'approved_by' => Auth::id(),
                                        'approved_at' => now(),
                                        'admin_notes' => $data['admin_notes'],
                                    ]);
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Pengajuan berhasil ditolak'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'view' => Pages\ViewLeave::route('/{record}'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
