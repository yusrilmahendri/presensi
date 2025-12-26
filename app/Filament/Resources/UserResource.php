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
    
    protected static ?string $navigationGroup = 'Manajemen Karyawan';
    
    protected static ?int $navigationSort = 20;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // Super admin sees this as "Admin Accounts"
        if ($user->isSuperAdmin()) {
            static::$navigationLabel = 'Admin Bisnis';
            static::$navigationGroup = 'Manajemen Super Admin';
            static::$navigationSort = 100;
            return true;
        }
        
        // Regular admin sees employees
        return $user->isAdmin();
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        
        // Super admin only sees admin users
        if ($user->isSuperAdmin()) {
            return $query->where('role', 'admin');
        }
        
        // Regular admin sees only their organization's employees
        if ($user->isAdmin()) {
            return $query->where('role', 'karyawan')
                ->where('organization_id', $user->organization_id);
        }
        
        // Fallback: return empty query for other roles
        return $query->whereRaw('1 = 0');
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
                            $set('work_type', null);
                        }
                    }),
                
                Forms\Components\Select::make('work_type')
                    ->label('Jenis Kerja')
                    ->options([
                        'shift' => '🕒 Shift - Absen berdasarkan jadwal shift',
                        'working_hours' => '⏰ Working Hours - Absen fleksibel dengan jam kerja minimum',
                    ])
                    ->default('shift')
                    ->required()
                    ->native(false)
                    ->live()
                    ->visible(fn ($get) => !$isSuperAdmin && $get('role') === 'karyawan')
                    ->helperText(fn ($get) => $get('work_type') === 'working_hours' 
                        ? '💡 Karyawan bisa check-in kapan saja, checkout setelah jam minimum'
                        : '💡 Karyawan harus absen sesuai jadwal shift'),
                
                Forms\Components\Select::make('shift_id')
                    ->label('Shift')
                    ->relationship(
                        'shift', 
                        'name',
                        fn ($query) => $query->where('organization_id', auth()->user()->organization_id)
                    )
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => !$isSuperAdmin && $get('role') === 'karyawan' && $get('work_type') === 'shift')
                    ->required(fn ($get) => !$isSuperAdmin && $get('role') === 'karyawan' && $get('work_type') === 'shift')
                    ->helperText('Pilih shift untuk karyawan ini'),
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
                
                Tables\Columns\BadgeColumn::make('work_type')
                    ->label('Jenis Kerja')
                    ->colors([
                        'primary' => 'shift',
                        'success' => 'working_hours',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'shift',
                        'heroicon-o-calendar-days' => 'working_hours',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'shift' => 'Shift',
                        'working_hours' => 'Working Hours',
                        default => '-',
                    })
                    ->placeholder('-')
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
                    ->relationship(
                        'shift', 
                        'name',
                        fn ($query) => $query->where('organization_id', auth()->user()->organization_id)
                    )
                    ->visible(!$isSuperAdmin),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Detail User'),
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus User')
                        ->modalDescription('Apakah Anda yakin ingin menghapus user ini? Data yang terhapus tidak dapat dikembalikan.')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
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
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

