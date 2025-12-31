<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAdmins extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Admin Terbaru';
    
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('role', 'admin')
                    ->with('organization')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Admin')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Bisnis')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->paginated([5, 10]);
    }
}
