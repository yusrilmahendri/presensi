<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Karyawan';
    
    protected static ?string $navigationGroup = 'Manajemen Karyawan';
    
    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        // Disabled for super admin - no employee management menu
        return false;
    }
    
    public static function canAccess(): bool
    {
        // Disabled for super admin - no employee management access
        return false;
    }
    
    public static function getEloquentQuery(): Builder
    {
        // Super admin sees all employees from all organizations
        return parent::getEloquentQuery()->where('role', 'karyawan');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('organization_id')
                    ->label('Bisnis')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                    
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->maxLength(255),
                    
                Forms\Components\Select::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('shift_id')
                    ->label('Shift')
                    ->relationship('shift', 'name')
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\TextInput::make('position')
                    ->label('Posisi')
                    ->maxLength(255),
                    
                Forms\Components\DatePicker::make('join_date')
                    ->label('Tanggal Bergabung')
                    ->native(false),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Bisnis')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departemen')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('shift.name')
                    ->label('Shift')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('position')
                    ->label('Posisi')
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('join_date')
                    ->label('Tanggal Bergabung')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('Bisnis')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('shift_id')
                    ->label('Shift')
                    ->relationship('shift', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
