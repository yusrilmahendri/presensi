<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Filament\Resources\AuditLogResource\RelationManagers;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Log Aktivitas';
    
    protected static ?string $modelLabel = 'Log Aktivitas';
    
    protected static ?string $pluralModelLabel = 'Log Aktivitas';
    
    protected static ?string $navigationGroup = 'Laporan';
    
    protected static ?int $navigationSort = 40;
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }
    
    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'admin';
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
    
    public static function canEdit($record): bool
    {
        return false;
    }
    
    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('organization_id')
                    ->relationship('organization', 'name'),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name'),
                Forms\Components\TextInput::make('event')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('auditable_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('auditable_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('old_values'),
                Forms\Components\TextInput::make('new_values'),
                Forms\Components\TextInput::make('ip_address')
                    ->maxLength(45),
                Forms\Components\TextInput::make('user_agent')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->default('Sistem'),
                    
                Tables\Columns\TextColumn::make('event')
                    ->label('Aktivitas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'login' => 'success',
                        'logout' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Dibuat',
                        'updated' => 'Diubah',
                        'deleted' => 'Dihapus',
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'fake_gps_detected' => 'GPS Palsu Terdeteksi',
                        'no_face_detected' => 'Wajah Tidak Terdeteksi',
                        'low_face_confidence' => 'Kualitas Wajah Rendah',
                        'device_change_detected' => 'Pergantian Device',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAuditLogs::route('/'),
            'create' => Pages\CreateAuditLog::route('/create'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
            'edit' => Pages\EditAuditLog::route('/{record}/edit'),
        ];
    }
}
