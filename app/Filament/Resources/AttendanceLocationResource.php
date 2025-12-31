<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceLocationResource\Pages;
use App\Models\AttendanceLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceLocationResource extends Resource
{
    protected static ?string $model = AttendanceLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    
    protected static ?string $navigationLabel = 'Lokasi Absen';
    
    protected static ?string $navigationGroup = 'Pengaturan';
    
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && $user->isAdmin() && !$user->isSuperAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lokasi')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                
                Forms\Components\Section::make('Koordinat Lokasi')
                    ->description('Pilih lokasi di peta atau masukkan koordinat manual')
                    ->schema([
                        Forms\Components\ViewField::make('map_picker')
                            ->label('Pilih Lokasi di Peta')
                            ->view('filament.forms.components.map-picker')
                            ->afterStateHydrated(function ($component, $state, $get) {
                                $component->state([
                                    'latitude' => $get('latitude') ?? -6.2088,
                                    'longitude' => $get('longitude') ?? 106.8456,
                                ]);
                            })
                            ->columnSpanFull(),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->required()
                                    ->step(0.00000001)
                                    ->reactive()
                                    ->helperText('Klik peta atau input manual'),
                                
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->required()
                                    ->step(0.00000001)
                                    ->reactive()
                                    ->helperText('Klik peta atau input manual'),
                            ]),
                        
                        Forms\Components\TextInput::make('radius')
                            ->label('Radius Geofencing (meter)')
                            ->numeric()
                            ->required()
                            ->default(100)
                            ->suffix('meter')
                            ->helperText('Karyawan hanya bisa check-in dalam radius ini')
                            ->minValue(5)
                            ->maxValue(1000),
                    ])
                    ->columnSpan(2),
                
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude')
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude')
                    ->sortable(),
                Tables\Columns\TextColumn::make('radius')
                    ->label('Radius')
                    ->suffix(' meter')
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendances_count')
                    ->label('Jumlah Absen')
                    ->counts('attendances')
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
            'index' => Pages\ListAttendanceLocations::route('/'),
            'create' => Pages\CreateAttendanceLocation::route('/create'),
            'edit' => Pages\EditAttendanceLocation::route('/{record}/edit'),
        ];
    }
}

