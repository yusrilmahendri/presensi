<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static ?string $navigationLabel = 'Bisnis';
    
    protected static ?string $navigationGroup = 'Manajemen Super Admin';
    
    protected static ?int $navigationSort = 100;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Bisnis')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('PT ABC, Toko XYZ, Instansi ABC'),
                Forms\Components\Select::make('type')
                    ->label('Jenis Bisnis')
                    ->options([
                        'umkm' => 'UMKM',
                        'instansi' => 'Instansi Pemerintah',
                        'perusahaan' => 'Perusahaan',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('email')
                    ->label('Email Bisnis')
                    ->email()
                    ->maxLength(255)
                    ->placeholder('kontak@bisnis.com'),
                Forms\Components\TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->maxLength(255)
                    ->placeholder('08123456789'),
                Forms\Components\Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('logo')
                    ->label('Logo Bisnis')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->directory('logos')
                    ->visibility('public'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
                Forms\Components\TextInput::make('max_users')
                    ->label('Maksimal Pengguna')
                    ->helperText('Jumlah maksimal karyawan yang dapat ditambahkan')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-logo.png'))
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Bisnis')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Jenis')
                    ->colors([
                        'primary' => 'umkm',
                        'success' => 'instansi',
                        'warning' => 'perusahaan',
                        'secondary' => 'lainnya',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'umkm' => 'UMKM',
                        'instansi' => 'Instansi',
                        'perusahaan' => 'Perusahaan',
                        'lainnya' => 'Lainnya',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Jumlah User')
                    ->counts('users')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('max_users')
                    ->label('Max User')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Bisnis')
                    ->options([
                        'umkm' => 'UMKM',
                        'instansi' => 'Instansi Pemerintah',
                        'perusahaan' => 'Perusahaan',
                        'lainnya' => 'Lainnya',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
