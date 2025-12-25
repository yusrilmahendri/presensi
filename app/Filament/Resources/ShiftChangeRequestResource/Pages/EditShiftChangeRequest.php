<?php

namespace App\Filament\Resources\ShiftChangeRequestResource\Pages;

use App\Filament\Resources\ShiftChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShiftChangeRequest extends EditRecord
{
    protected static string $resource = ShiftChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
