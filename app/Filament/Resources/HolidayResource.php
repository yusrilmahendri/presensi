<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HolidayResource\Pages;
use App\Filament\Resources\HolidayResource\RelationManagers;
use App\Models\Holiday;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    
    protected static ?string $navigationGroup = 'Pengaturan';
    
    protected static ?string $navigationLabel = 'Hari Libur';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Hari Libur')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->native(false),
                Forms\Components\Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'national' => 'Nasional',
                        'organization' => 'Perusahaan',
                        'religious' => 'Keagamaan',
                    ])
                    ->required()
                    ->default('organization'),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_recurring')
                    ->label('Berulang Setiap Tahun')
                    ->helperText('Jika diaktifkan, libur ini akan otomatis berlaku setiap tahun')
                    ->default(false),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'danger' => 'national',
                        'success' => 'organization',
                        'warning' => 'religious',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'national' => 'Nasional',
                        'organization' => 'Perusahaan',
                        'religious' => 'Keagamaan',
                        default => $state
                    }),
                Tables\Columns\IconColumn::make('is_recurring')
                    ->label('Berulang')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'national' => 'Nasional',
                        'organization' => 'Perusahaan',
                        'religious' => 'Keagamaan',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Hanya Aktif')
                    ->falseLabel('Hanya Nonaktif')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListHolidays::route('/'),
            'create' => Pages\CreateHoliday::route('/create'),
            'edit' => Pages\EditHoliday::route('/{record}/edit'),
        ];
    }
}
