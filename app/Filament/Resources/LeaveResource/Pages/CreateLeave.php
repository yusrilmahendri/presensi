<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;
    
    protected ?string $heading = 'Buat Pengajuan Izin';
    
    protected ?string $subheading = 'Buat pengajuan izin, sakit, atau cuti baru';
}
