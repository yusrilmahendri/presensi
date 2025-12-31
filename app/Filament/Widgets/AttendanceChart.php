<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Organization;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        if (auth()->user()->role === 'super_admin') {
            return 'Pertumbuhan Jumlah Bisnis (6 Bulan Terakhir)';
        }
        
        return 'Perbandingan Kehadiran Bulan Ini';
    }

    protected function getData(): array
    {
        if (auth()->user()->role === 'super_admin') {
            return $this->getSuperAdminData();
        }
        
        return $this->getAdminData();
    }

    protected function getType(): string
    {
        if (auth()->user()->role === 'super_admin') {
            return 'line';
        }
        
        return 'pie';
    }
    
    /**
     * Data untuk Super Admin: Pertumbuhan bisnis 6 bulan terakhir
     */
    private function getSuperAdminData(): array
    {
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            // Hitung total bisnis sampai bulan tersebut
            $count = Organization::whereDate('created_at', '<=', $date->endOfMonth())
                ->count();
            $data[] = $count;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Bisnis',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }
    
    /**
     * Data untuk Admin: Perbandingan tepat waktu vs terlambat bulan ini
     */
    private function getAdminData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $organizationId = auth()->user()->organization_id;
        
        // Ambil semua check-in bulan ini
        $attendances = Attendance::where('type', 'check_in')
            ->whereBetween('attendance_time', [$startOfMonth, $endOfMonth])
            ->whereHas('user', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId)
                    ->where('role', 'karyawan');
            })
            ->with('user.shift')
            ->get();
        
        $tepatWaktu = 0;
        $terlambat = 0;
        $tanpaShift = 0;
        
        foreach ($attendances as $attendance) {
            if (!$attendance->user->shift) {
                $tanpaShift++;
                continue;
            }
            
            $shiftStart = Carbon::parse($attendance->user->shift->start_time);
            $checkInTime = Carbon::parse($attendance->attendance_time);
            
            // Grace period 15 menit
            $lateThreshold = Carbon::parse($attendance->attendance_time->format('Y-m-d') . ' ' . $attendance->user->shift->start_time)
                ->addMinutes(15);
            
            if ($checkInTime->lte($lateThreshold)) {
                $tepatWaktu++;
            } else {
                $terlambat++;
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Kehadiran',
                    'data' => [$tepatWaktu, $terlambat, $tanpaShift],
                    'backgroundColor' => [
                        'rgb(34, 197, 94)',   // Green - Tepat waktu
                        'rgb(239, 68, 68)',    // Red - Terlambat
                        'rgb(156, 163, 175)',  // Gray - Tanpa shift
                    ],
                ],
            ],
            'labels' => ['Tepat Waktu', 'Terlambat', 'Tanpa Shift'],
        ];
    }
}
