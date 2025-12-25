<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        
        // For regular admin, automatically set organization_id
        if ($user->isAdmin() && !$user->isSuperAdmin()) {
            $data['organization_id'] = $user->organization_id;
        }
        
        return $data;
    }
}

