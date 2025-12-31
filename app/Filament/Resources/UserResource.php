<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Karyawan';
    
    protected static ?string $navigationGroup = 'Manajemen User';
    
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // Super admin sees this as "Admin Accounts"
        if ($user->isSuperAdmin()) {
            static::$navigationLabel = 'Admin Bisnis';
            static::$navigationGroup = 'Manajemen Super Admin';
            static::$navigationSort = 2;
            return true;
        }
        
        // Regular admin sees employees
        return $user->isAdmin();
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Super admin only sees admin users
        if (auth()->user()->isSuperAdmin()) {
            return $query->where('role', 'admin');
        }
        
        // Regular admin sees their organization's employees
        return $query->where('role', 'karyawan');
    }

    public static function form(Form $form): Form
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        
        return $form
            ->schema([
                // Organization selection (only for super admin)
                Forms\Components\Select::make('organization_id')
                    ->label('Bisnis')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible($isSuperAdmin)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->visible(fn ($get) => $isSuperAdmin || $get('role') === 'admin')
                    ->required(fn ($get) => $isSuperAdmin || $get('role') === 'admin'),
                
                Forms\Components\TextInput::make('nik')
                    ->label('NIK (untuk Karyawan)')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->visible(fn ($get) => !$isSuperAdmin && $get('role') === 'karyawan'),
                
                Forms\Components\TextInput::make('nip')
                    ->label('NIP (untuk Karyawan)')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->visible(fn ($get) => !$isSuperAdmin && $get('role') === 'karyawan'),
                
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => 
                        !empty($state) ? Hash::make($state) : null
                    )
                    ->dehydrated(fn ($state) => !empty($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->helperText($isSuperAdmin ? 'Password default untuk admin baru' : null),
                
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options($isSuperAdmin ? ['admin' => 'Admin Bisnis'] : [
                        'admin' => 'Admin',
                        'karyawan' => 'Karyawan',
                    ])
                    ->required()
                    ->default($isSuperAdmin ? 'admin' : 'karyawan')
                    ->disabled($isSuperAdmin)
                    ->dehydrated($isSuperAdmin) // Ensure value is submitted even when disabled
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state === 'admin') {
                            $set('shift_id', null);
                        }
                    }),
                
                Forms\Components\Select::make('shift_id')
                    ->label('Shift')
                    ->relationship('shift', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => !$isSuperAdmin && $get('role') === 'karyawan')
                    ->required(fn ($get) => !$isSuperAdmin && $get('role') === 'karyawan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Bisnis')
                    ->searchable()
                    ->sortable()
                    ->visible($isSuperAdmin),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable()
                    ->visible(!$isSuperAdmin),
                
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable()
                    ->visible(!$isSuperAdmin),
                
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'karyawan' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin Bisnis',
                        'karyawan' => 'Karyawan',
                        default => $state,
                    })
                    ->sortable()
                    ->visible(!$isSuperAdmin),
                
                Tables\Columns\TextColumn::make('shift.name')
                    ->label('Shift')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable()
                    ->visible(!$isSuperAdmin),
                
                Tables\Columns\TextColumn::make('attendances_count')
                    ->label('Jumlah Absen')
                    ->counts('attendances')
                    ->sortable()
                    ->toggleable()
                    ->visible(!$isSuperAdmin),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('shift_id')
                    ->label('Shift')
                    ->relationship('shift', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'username', 'nik', 'nip'];
    }
    
    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->name;
    }
    
    public static function getGlobalSearchResultDetails($record): array
    {
        $details = ['Role' => ucfirst($record->role)];
        
        if ($record->username) {
            $details['Username'] = $record->username;
        }
        if ($record->nik) {
            $details['NIK'] = $record->nik;
        }
        if ($record->nip) {
            $details['NIP'] = $record->nip;
        }
        
        return $details;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

