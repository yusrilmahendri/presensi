<?php

namespace App\Filament\Pages;

use App\Models\Overtime;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class OvertimeReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static string $view = 'filament.pages.overtime-report';
    
    protected static ?string $navigationLabel = 'Laporan Overtime';
    
    protected static ?string $navigationGroup = 'Laporan';
    
    protected static ?int $navigationSort = 3;
    
    public ?array $data = [];
    public $reportData = null;
    public $startDate;
    public $endDate;
    public $userId;
    public $status;
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }
    
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }
    
    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->status = 'all';
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->default(now()->startOfMonth())
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Tanggal Akhir')
                    ->default(now()->endOfMonth())
                    ->required(),
                Select::make('user_id')
                    ->label('Karyawan (Opsional)')
                    ->options(function () {
                        $options = ['all' => 'Semua Karyawan'];
                        $employees = \App\Models\User::where('organization_id', auth()->user()->organization_id)
                            ->where('role', 'karyawan')
                            ->pluck('name', 'id')
                            ->toArray();
                        return $options + $employees;
                    })
                    ->searchable()
                    ->default('all')
                    ->preload(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'all' => 'Semua',
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('all')
                    ->required(),
            ])
            ->statePath('data')
            ->columns(4);
    }
    
    public function generateReport()
    {
        $data = $this->form->getState();
        $this->startDate = $data['start_date'];
        $this->endDate = $data['end_date'];
        $this->userId = $data['user_id'] ?? null;
        $this->status = $data['status'];
        
        $query = Overtime::query()
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->with(['user', 'approvedBy']);
        
        if ($this->userId && $this->userId !== 'all') {
            $query->where('user_id', $this->userId);
        } else {
            $query->whereHas('user', function ($q) {
                $q->where('organization_id', auth()->user()->organization_id);
            });
        }
        
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }
        
        $overtimes = $query->orderBy('date', 'desc')->get();
        
        $overtimeRecords = [];
        
        foreach ($overtimes as $overtime) {
            $overtimeRecords[] = [
                'date' => $overtime->date,
                'name' => $overtime->user->name,
                'nik' => $overtime->user->nik ?? '-',
                'start_time' => $overtime->start_time,
                'end_time' => $overtime->end_time,
                'duration_minutes' => $overtime->duration_minutes,
                'duration_hours' => round($overtime->duration_minutes / 60, 2),
                'multiplier' => $overtime->multiplier,
                'status' => $overtime->status,
                'status_label' => match($overtime->status) {
                    'pending' => 'Menunggu',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    default => $overtime->status
                },
                'approved_by' => $overtime->approvedBy->name ?? '-',
                'notes' => $overtime->notes ?? '-',
            ];
        }
        
        $this->reportData = $overtimeRecords;
        
        if (empty($overtimeRecords)) {
            Notification::make()
                ->title('Tidak Ada Data')
                ->body('Tidak ada overtime pada periode yang dipilih.')
                ->info()
                ->send();
        } else {
            Notification::make()
                ->title('Laporan Berhasil Dibuat')
                ->body(count($overtimeRecords) . ' data overtime ditemukan.')
                ->success()
                ->send();
        }
    }
    
    public function printReport()
    {
        if (!$this->reportData) {
            Notification::make()
                ->title('Error')
                ->body('Silakan generate laporan terlebih dahulu.')
                ->danger()
                ->send();
            return;
        }
        
        $pdf = Pdf::loadView('exports.overtime-report-pdf', [
            'reportData' => $this->reportData,
            'startDate' => Carbon::parse($this->startDate)->format('d M Y'),
            'endDate' => Carbon::parse($this->endDate)->format('d M Y'),
            'status' => $this->status,
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-overtime-' . now()->format('Y-m-d') . '.pdf');
    }
}
