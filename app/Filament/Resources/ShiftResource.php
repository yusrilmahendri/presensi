<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftResource\Pages;
use App\Models\Shift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = 'Shift';
    
    protected static ?string $navigationGroup = 'Pengaturan';
    
    protected static ?int $navigationSort = 40;

    public static function shouldRegisterNavigation(): bool
    {
        // Super Admin selalu bisa lihat
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return true;
        }
        
        // Admin hanya bisa lihat jika shift mode aktif
        if (auth()->check() && auth()->user()->organization) {
            return auth()->user()->organization->isShiftBased();
        }
        
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Shift')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required()
                    ->seconds(false),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required()
                    ->seconds(false),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Shift')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Jumlah Karyawan')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'edit' => Pages\EditShift::route('/{record}/edit'),
        ];
    }
}

