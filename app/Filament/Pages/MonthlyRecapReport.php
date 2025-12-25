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
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MonthlyRecapReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    
    protected static string $view = 'filament.pages.monthly-recap-report';
    
    protected static ?string $navigationLabel = 'Laporan Rekap Bulanan';
    
    protected static ?string $navigationGroup = 'Laporan';
    
    protected static ?int $navigationSort = 30;
    
    public ?array $data = [];
    public $reportData = null;
    public $month;
    public $year;
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
        $this->month = now()->month;
        $this->year = now()->year;
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('month')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ])
                    ->default(now()->month)
                    ->required(),
                Select::make('year')
                    ->label('Tahun')
                    ->options(function () {
                        $years = [];
                        for ($i = now()->year - 2; $i <= now()->year + 1; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->default(now()->year)
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
            ->statePath('data');
    }
    
    public function generateReport()
    {
        $data = $this->form->getState();
        $this->month = $data['month'];
        $this->year = $data['year'];
        $this->userId = $data['user_id'] ?? null;
        
        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::create($this->year, $this->month, 1)->endOfMonth();
        
        $query = User::query()
            ->where('role', 'karyawan')
            ->with(['shift']);
        
        if ($this->userId && $this->userId !== 'all') {
            $query->where('id', $this->userId);
        }
        
        $users = $query->get();
        
        $reportData = [];
        
        foreach ($users as $user) {
            $attendances = Attendance::where('user_id', $user->id)
                ->whereBetween('attendance_time', [$startDate, $endDate])
                ->orderBy('attendance_time')
                ->get();
            
            $checkIns = $attendances->where('type', 'check_in');
            $totalHadir = $checkIns->count();
            $totalTerlambat = 0;
            $totalTepatWaktu = 0;
            
            foreach ($checkIns as $attendance) {
                if ($user->shift) {
                    $shiftStart = Carbon::parse($attendance->attendance_time->format('Y-m-d') . ' ' . $user->shift->start_time);
                    $checkInTime = Carbon::parse($attendance->attendance_time);
                    $lateThreshold = $shiftStart->copy()->addMinutes(15);
                    
                    if ($checkInTime->gt($lateThreshold)) {
                        $totalTerlambat++;
                    } else {
                        $totalTepatWaktu++;
                    }
                }
            }
            
            $workDays = $this->getWorkDays($startDate, $endDate);
            $totalAlpha = $workDays - $totalHadir;
            
            $reportData[] = [
                'user' => $user,
                'total_hari_kerja' => $workDays,
                'total_hadir' => $totalHadir,
                'total_tepat_waktu' => $totalTepatWaktu,
                'total_terlambat' => $totalTerlambat,
                'total_alpha' => $totalAlpha,
                'persentase_hadir' => $workDays > 0 ? round(($totalHadir / $workDays) * 100, 1) : 0,
            ];
        }
        
        $this->reportData = $reportData;
        
        Notification::make()
            ->title('Laporan berhasil dibuat!')
            ->success()
            ->send();
    }
    
    private function getWorkDays($startDate, $endDate)
    {
        $workDays = 0;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            // Exclude weekends (Saturday = 6, Sunday = 0)
            if (!in_array($current->dayOfWeek, [0, 6])) {
                $workDays++;
            }
            $current->addDay();
        }
        
        return $workDays;
    }
    
    public function printReport()
    {
        if (!$this->reportData) {
            Notification::make()
                ->title('Harap generate laporan terlebih dahulu!')
                ->warning()
                ->send();
            return;
        }
        
        $monthName = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ][$this->month];
        
        $pdf = Pdf::loadView('exports.monthly-recap-pdf', [
            'reportData' => $this->reportData,
            'month' => $monthName,
            'year' => $this->year,
        ])->setPaper('a4', 'landscape');
        
        $fileName = 'laporan-rekap-bulanan-' . $this->month . '-' . $this->year . '.pdf';
        return response()->streamDownload(fn () => print($pdf->output()), $fileName);
    }
}
