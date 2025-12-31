<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(fn () => \App\Models\Leave::count()),
            'pending' => Tab::make('Menunggu')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => \App\Models\Leave::where('status', 'pending')->count())
                ->badgeColor('warning'),
            'approved' => Tab::make('Disetujui')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved'))
                ->badge(fn () => \App\Models\Leave::where('status', 'approved')->count())
                ->badgeColor('success'),
            'rejected' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->badge(fn () => \App\Models\Leave::where('status', 'rejected')->count())
                ->badgeColor('danger'),
            'sakit' => Tab::make('Sakit')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'sakit'))
                ->badge(fn () => \App\Models\Leave::where('type', 'sakit')->count())
                ->badgeColor('danger'),
            'izin' => Tab::make('Izin')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'izin'))
                ->badge(fn () => \App\Models\Leave::where('type', 'izin')->count())
                ->badgeColor('warning'),
            'cuti' => Tab::make('Cuti')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'cuti'))
                ->badge(fn () => \App\Models\Leave::where('type', 'cuti')->count())
                ->badgeColor('info'),
        ];
    }
}

