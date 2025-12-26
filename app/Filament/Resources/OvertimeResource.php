<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeResource\Pages;
use App\Filament\Resources\OvertimeResource\RelationManagers;
use App\Models\Overtime;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OvertimeResource extends Resource
{
    protected static ?string $model = Overtime::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = 'Lembur';
    
    protected static ?string $navigationGroup = 'Absensi';
    
    protected static ?int $navigationSort = 20;
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role !== 'super_admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->default(Auth::id())
                    ->disabled(fn ($record) => $record !== null || Auth::user()->role === 'karyawan')
                    ->dehydrated()
                    ->required(),
                    
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal Lembur')
                    ->required()
                    ->default(now())
                    ->maxDate(now()->addDays(7)),
                    
                Forms\Components\TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required()
                    ->seconds(false),
                    
                Forms\Components\TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required()
                    ->seconds(false)
                    ->after('start_time')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $start = $get('start_time');
                        $end = $state;
                        if ($start && $end) {
                            $startTime = \Carbon\Carbon::parse($start);
                            $endTime = \Carbon\Carbon::parse($end);
                            $minutes = $startTime->diffInMinutes($endTime);
                            $set('duration_minutes', $minutes);
                        }
                    }),
                    
                Forms\Components\TextInput::make('duration_minutes')
                    ->label('Durasi (Menit)')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->suffix('menit'),
                    
                Forms\Components\TextInput::make('multiplier')
                    ->label('Multiplier Upah')
                    ->required()
                    ->numeric()
                    ->default(1.5)
                    ->step(0.1)
                    ->helperText('1.5 = 150% dari upah normal')
                    ->disabled(fn () => Auth::user()->role === 'karyawan'),
                    
                Forms\Components\Textarea::make('reason')
                    ->label('Alasan Lembur')
                    ->required()
                    ->maxLength(500)
                    ->rows(3)
                    ->columnSpanFull(),
                    
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->required()
                    ->disabled(fn ($record) => $record === null)
                    ->visible(fn () => Auth::user()->role === 'admin'),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Admin')
                    ->maxLength(500)
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record !== null && Auth::user()->role === 'admin'),
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
                    
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->time('H:i'),
                    
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->time('H:i'),
                    
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->formatStateUsing(fn ($state) => floor($state / 60) . ' jam ' . ($state % 60) . ' menit')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('multiplier')
                    ->label('Multiplier')
                    ->formatStateUsing(fn ($state) => $state . 'x')
                    ->description(fn (Overtime $record): string => 
                        $record->multiplier == 1.5 ? 'Hari Kerja (150%)' : 'Weekend/Libur (200%)'
                    )
                    ->tooltip('Pengali upah lembur sesuai aturan ketenagakerjaan')
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
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                // Prominent actions - show directly as buttons (Admin only)
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size('sm')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pengajuan Lembur')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pengajuan lembur ini?')
                    ->modalSubmitActionLabel('Ya, Setujui')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan (Opsional)')
                            ->placeholder('Tambahkan catatan jika diperlukan...')
                            ->maxLength(500),
                    ])
                    ->action(function (Overtime $record, array $data) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'notes' => $data['notes'] ?? null,
                        ]);
                        
                        // Send notification to employee
                        $record->user->notify(new \App\Notifications\OvertimeApprovalNotification(
                            $record,
                            'approved'
                        ));
                    })
                    ->successNotificationTitle('Lembur berhasil disetujui')
                    ->authorize('approve')
                    ->hidden(fn (Overtime $record): bool => $record->status !== 'pending'),
                    
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->size('sm')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pengajuan Lembur')
                    ->modalDescription('Apakah Anda yakin ingin menolak pengajuan lembur ini?')
                    ->modalSubmitActionLabel('Ya, Tolak')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Jelaskan alasan penolakan...')
                            ->maxLength(500),
                    ])
                    ->action(function (Overtime $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'notes' => $data['notes'],
                        ]);
                        
                        // Send notification to employee
                        $record->user->notify(new \App\Notifications\OvertimeApprovalNotification(
                            $record,
                            'rejected'
                        ));
                    })
                    ->successNotificationTitle('Lembur berhasil ditolak')
                    ->authorize('reject')
                    ->hidden(fn (Overtime $record): bool => $record->status !== 'pending'),
                
                // View, edit, delete actions
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                Tables\Actions\EditAction::make()
                    ->label('Ubah'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus Terpilih'),
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
            'index' => Pages\ListOvertimes::route('/'),
            'create' => Pages\CreateOvertime::route('/create'),
            'edit' => Pages\EditOvertime::route('/{record}/edit'),
        ];
    }
}
