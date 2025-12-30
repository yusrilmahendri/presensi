<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendancesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $type;

    public function __construct($startDate = null, $endDate = null, $userId = null, $type = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userId = $userId;
        $this->type = $type;
    }

    public function collection()
    {
        $query = Attendance::with(['user', 'shift', 'attendanceLocation'])
            ->orderBy('attendance_time', 'desc');

        if ($this->startDate) {
            $query->whereDate('attendance_time', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('attendance_time', '<=', $this->endDate);
        }

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'NIK',
            'NIP',
            'Shift',
            'Tipe',
            'Waktu Absen',
            'Lokasi',
            'Latitude',
            'Longitude',
        ];
    }

    public function map($attendance): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $attendance->user->name ?? '-',
            $attendance->user->nik ?? '-',
            $attendance->user->nip ?? '-',
            $attendance->shift->name ?? '-',
            $attendance->type === 'check_in' ? 'Check In' : 'Check Out',
            $attendance->attendance_time->format('d M Y H:i:s'),
            $attendance->attendanceLocation->name ?? '-',
            $attendance->latitude,
            $attendance->longitude,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '667eea']
                ],
            ],
        ];
    }
}
