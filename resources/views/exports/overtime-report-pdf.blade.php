<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Overtime</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .summary table {
            width: 100%;
            border: none;
        }
        .summary td {
            border: none;
            text-align: center;
            padding: 10px;
        }
        .summary .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary .value {
            font-size: 18px;
            font-weight: bold;
            color: #0066cc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }
        td {
            vertical-align: middle;
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-danger {
            background-color: #fee;
            color: #c00;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN OVERTIME</h2>
        <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
        @if($status !== 'all')
            <p>Filter Status: {{ match($status) {
                'pending' => 'Menunggu',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                default => $status
            } }}</p>
        @endif
        <p>Dicetak: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="label">Total Overtime</div>
                    <div class="value">{{ count($reportData) }}</div>
                </td>
                <td>
                    <div class="label">Total Jam</div>
                    <div class="value">{{ count($reportData) > 0 ? round(array_sum(array_column($reportData, 'duration_hours')), 1) : 0 }}</div>
                </td>
                <td>
                    <div class="label">Disetujui</div>
                    <div class="value" style="color: #28a745;">{{ count(array_filter($reportData, fn($r) => $r['status'] === 'approved')) }}</div>
                </td>
                <td>
                    <div class="label">Menunggu</div>
                    <div class="value" style="color: #ffc107;">{{ count(array_filter($reportData, fn($r) => $r['status'] === 'pending')) }}</div>
                </td>
                <td>
                    <div class="label">Ditolak</div>
                    <div class="value" style="color: #dc3545;">{{ count(array_filter($reportData, fn($r) => $r['status'] === 'rejected')) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 15%;">Nama</th>
                <th style="width: 8%;">NIK</th>
                <th style="width: 7%;">Mulai</th>
                <th style="width: 7%;">Selesai</th>
                <th style="width: 7%;">Jam</th>
                <th style="width: 5%;">Multi</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 12%;">Disetujui Oleh</th>
                <th style="width: 18%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $index => $record)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($record['date'])->format('d M Y') }}</td>
                    <td>{{ $record['name'] }}</td>
                    <td class="text-center">{{ $record['nik'] }}</td>
                    <td class="text-center">{{ $record['start_time'] }}</td>
                    <td class="text-center">{{ $record['end_time'] }}</td>
                    <td class="text-center"><strong>{{ $record['duration_hours'] }}</strong></td>
                    <td class="text-center">{{ $record['multiplier'] }}x</td>
                    <td class="text-center">
                        <span class="badge {{ $record['status'] === 'approved' ? 'badge-success' : ($record['status'] === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                            {{ $record['status_label'] }}
                        </span>
                    </td>
                    <td>{{ $record['approved_by'] }}</td>
                    <td style="font-size: 9px;">{{ $record['notes'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">Tidak ada data overtime</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} Sistem Presensi</p>
    </div>
</body>
</html>
