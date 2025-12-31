<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
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

class LateReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    
    protected static string $view = 'filament.pages.late-report';
    
    protected static ?string $navigationLabel = 'Laporan Keterlambatan';
    
    protected static ?string $navigationGroup = 'Laporan';
    
    protected static ?int $navigationSort = 2;
    
    public ?array $data = [];
    public $reportData = null;
    public $startDate;
    public $endDate;
    public $userId;
    
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
            ])
            ->statePath('data')
            ->columns(3);
    }
    
    public function generateReport()
    {
        $data = $this->form->getState();
        $this->startDate = $data['start_date'];
        $this->endDate = $data['end_date'];
        $this->userId = $data['user_id'] ?? null;
        
        $query = Attendance::query()
            ->where('type', 'check_in')
            ->whereBetween('attendance_time', [$this->startDate, $this->endDate])
            ->with(['user.shift', 'attendanceLocation']);
        
        if ($this->userId && $this->userId !== 'all') {
            $query->where('user_id', $this->userId);
        } else {
            $query->whereHas('user', function ($q) {
                $q->where('organization_id', auth()->user()->organization_id)
                  ->where('role', 'karyawan');
            });
        }
        
        $attendances = $query->get();
        
        $lateRecords = [];
        
        foreach ($attendances as $attendance) {
            if (!$attendance->user->shift) {
                continue;
            }
            
            $shiftStart = Carbon::parse($attendance->attendance_time->format('Y-m-d') . ' ' . $attendance->user->shift->start_time);
            $checkInTime = Carbon::parse($attendance->attendance_time);
            $lateThreshold = $shiftStart->copy()->addMinutes(15);
            
            if ($checkInTime->gt($lateThreshold)) {
                $lateMinutes = $checkInTime->diffInMinutes($shiftStart);
                
                $lateRecords[] = [
                    'date' => $attendance->attendance_time->format('Y-m-d'),
                    'name' => $attendance->user->name,
                    'nik' => $attendance->user->nik ?? '-',
                    'shift' => $attendance->user->shift->name,
                    'shift_start' => $attendance->user->shift->start_time,
                    'check_in_time' => $attendance->attendance_time->format('H:i:s'),
                    'late_minutes' => $lateMinutes,
                    'location' => $attendance->attendanceLocation->name ?? '-',
                ];
            }
        }
        
        // Sort by date descending
        usort($lateRecords, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
        
        $this->reportData = $lateRecords;
        
        if (empty($lateRecords)) {
            Notification::make()
                ->title('Tidak Ada Data')
                ->body('Tidak ada keterlambatan pada periode yang dipilih.')
                ->info()
                ->send();
        } else {
            Notification::make()
                ->title('Laporan Berhasil Dibuat')
                ->body(count($lateRecords) . ' data keterlambatan ditemukan.')
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
        
        $pdf = Pdf::loadView('exports.late-report-pdf', [
            'reportData' => $this->reportData,
            'startDate' => Carbon::parse($this->startDate)->format('d M Y'),
            'endDate' => Carbon::parse($this->endDate)->format('d M Y'),
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-keterlambatan-' . now()->format('Y-m-d') . '.pdf');
    }
}
