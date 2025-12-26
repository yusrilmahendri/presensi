<?php

namespace App\Filament\Resources\OvertimeResource\Pages;

use App\Filament\Resources\OvertimeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOvertime extends CreateRecord
{
    protected static string $resource = OvertimeResource::class;
    
    protected ?string $heading = 'Buat Pengajuan Lembur';
    
    protected ?string $subheading = 'Ajukan lembur Anda untuk mendapatkan persetujuan';
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-fill organization_id dan user_id
        $data['organization_id'] = Auth::user()->organization_id;
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        $data['status'] = 'pending';
        
        return $data;
    }
}
