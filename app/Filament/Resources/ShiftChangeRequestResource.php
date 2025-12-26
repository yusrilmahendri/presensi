<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftChangeRequestResource\Pages;
use App\Filament\Resources\ShiftChangeRequestResource\RelationManagers;
use App\Models\ShiftChangeRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShiftChangeRequestResource extends Resource
{
    protected static ?string $model = ShiftChangeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    
    protected static ?string $navigationLabel = 'Pergantian Shift';
    
    protected static ?string $modelLabel = 'Pergantian Shift';
    
    protected static ?string $pluralModelLabel = 'Pergantian Shift';
    
    protected static ?string $navigationGroup = 'Manajemen Karyawan';
    
    protected static ?int $navigationSort = 20;
    
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && $user->isAdmin() && !$user->isSuperAdmin();
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('organization_id', auth()->user()->organization_id)
            ->where('status', 'pending')
            ->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        $isAdmin = auth()->user()->role === 'admin';
        
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Karyawan')
                    ->relationship(
                        'user', 
                        'name',
                        fn ($query) => $query->where('organization_id', auth()->user()->organization_id)
                            ->where('role', 'karyawan')
                    )
                    ->searchable()
                    ->required()
                    ->disabled(fn ($record) => $record !== null)
                    ->dehydrated(),
                    
                Forms\Components\Select::make('current_shift_id')
                    ->label('Shift Saat Ini')
                    ->relationship(
                        'currentShift', 
                        'name',
                        fn ($query) => $query->where('organization_id', auth()->user()->organization_id)
                    )
                    ->required()
                    ->disabled(fn ($record) => $record !== null)
                    ->dehydrated(),
                    
                Forms\Components\Select::make('requested_shift_id')
                    ->label('Shift yang Diminta')
                    ->relationship(
                        'requestedShift', 
                        'name',
                        fn ($query) => $query->where('organization_id', auth()->user()->organization_id)
                    )
                    ->searchable()
                    ->required()
                    ->disabled(fn ($record) => $record !== null && $record->status !== 'pending')
                    ->dehydrated(),
                    
                Forms\Components\DatePicker::make('effective_date')
                    ->label('Tanggal Efektif Pergantian')
                    ->required()
                    ->minDate(now()->toDateString())
                    ->disabled(fn ($record) => $record !== null && $record->status !== 'pending')
                    ->dehydrated(),
                    
                Forms\Components\Textarea::make('reason')
                    ->label('Alasan Pergantian')
                    ->required()
                    ->rows(3)
                    ->disabled(fn ($record) => $record !== null && $record->status !== 'pending')
                    ->dehydrated()
                    ->columnSpanFull(),
                    
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->required()
                    ->default('pending')
                    ->disabled(!$isAdmin)
                    ->visible($isAdmin)
                    ->live(),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Admin')
                    ->rows(2)
                    ->visible($isAdmin)
                    ->columnSpanFull(),
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
                    
                Tables\Columns\TextColumn::make('effective_date')
                    ->label('Tanggal Efektif')
                    ->date('d M Y')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('currentShift.name')
                    ->label('Shift Saat Ini')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('requestedShift.name')
                    ->label('Shift Diminta')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->reason)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('-')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Disetujui')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending'),
                    
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
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
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status === 'pending' && auth()->user()->role === 'admin')
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'approved',
                                'approved_by' => auth()->id(),
                                'approved_at' => now('Asia/Jakarta'),
                            ]);
                            
                            // Update user's shift
                            $record->user->update([
                                'shift_id' => $record->requested_shift_id,
                            ]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Pergantian shift disetujui')
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status === 'pending' && auth()->user()->role === 'admin')
                        ->form([
                            Forms\Components\Textarea::make('notes')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'status' => 'rejected',
                                'approved_by' => auth()->id(),
                                'approved_at' => now('Asia/Jakarta'),
                                'notes' => $data['notes'],
                            ]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Pergantian shift ditolak')
                                ->warning()
                                ->send();
                        }),
                        
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn ($record) => $record->status === 'pending'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'approved',
                                        'approved_by' => auth()->id(),
                                        'approved_at' => now('Asia/Jakarta'),
                                    ]);
                                    
                                    // Update user's shift
                                    $record->user->update([
                                        'shift_id' => $record->requested_shift_id,
                                    ]);
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Pergantian shift berhasil disetujui')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Tolak Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('notes')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'rejected',
                                        'approved_by' => auth()->id(),
                                        'approved_at' => now('Asia/Jakarta'),
                                        'notes' => $data['notes'],
                                    ]);
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Pergantian shift berhasil ditolak')
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()->organization_id);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShiftChangeRequests::route('/'),
            'edit' => Pages\EditShiftChangeRequest::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
