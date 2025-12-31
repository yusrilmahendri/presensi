<?php

namespace App\Filament\Widgets;

use App\Models\Organization;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OrganizationsTable extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Daftar Bisnis';
    
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Organization::query()->latest())
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->size(35),
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
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Jumlah User')
                    ->counts('users')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Organization $record): string => route('filament.admin.resources.organizations.view', ['record' => $record])),
            ])
            ->paginated([5, 10, 25]);
    }
}
