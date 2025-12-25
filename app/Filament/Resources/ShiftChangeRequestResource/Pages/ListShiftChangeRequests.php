<?php

namespace App\Filament\Resources\ShiftChangeRequestResource\Pages;

use App\Filament\Resources\ShiftChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShiftChangeRequests extends ListRecords
{
    protected static string $resource = ShiftChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            ShiftChangeRequestResource\Widgets\ShiftChangeStatsWidget::class,
        ];
    }
}
