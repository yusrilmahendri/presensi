<?php

namespace App\Filament\Resources\OvertimeResource\Pages;

use App\Filament\Resources\OvertimeResource;
use App\Models\Overtime;
use App\Notifications\OvertimeApprovalNotification;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Overtime $record) => auth()->user()->role === 'admin' && $record->status === 'pending')
                ->action(function (Overtime $record) {
                    $record->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);
                    
                    // Notify employee
                    $record->user->notify(new OvertimeApprovalNotification($record, 'approved'));
                    
                    Notification::make()
                        ->title('Lembur Disetujui')
                        ->success()
                        ->send();
                }),
            
            Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Alasan Penolakan')
                        ->required(),
                ])
                ->visible(fn (Overtime $record) => auth()->user()->role === 'admin' && $record->status === 'pending')
                ->action(function (Overtime $record, array $data) {
                    $record->update([
                        'status' => 'rejected',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'notes' => $data['notes'],
                    ]);
                    
                    // Notify employee
                    $record->user->notify(new OvertimeApprovalNotification($record, 'rejected'));
                    
                    Notification::make()
                        ->title('Lembur Ditolak')
                        ->success()
                        ->send();
                }),
        ];
    }
}
