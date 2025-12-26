<?php

namespace App\Filament\Resources\OvertimeResource\Pages;

use App\Filament\Resources\OvertimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;
    
    protected ?string $heading = 'Pengajuan Lembur';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Pengajuan Lembur'),
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(fn () => \App\Models\Overtime::count()),
                
            'pending' => Tab::make('Menunggu')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => \App\Models\Overtime::where('status', 'pending')->count())
                ->badgeColor('warning'),
                
            'approved' => Tab::make('Disetujui')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved'))
                ->badge(fn () => \App\Models\Overtime::where('status', 'approved')->count())
                ->badgeColor('success'),
                
            'rejected' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->badge(fn () => \App\Models\Overtime::where('status', 'rejected')->count())
                ->badgeColor('danger'),
        ];
    }
}
