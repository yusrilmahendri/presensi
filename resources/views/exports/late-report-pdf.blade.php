<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keterlambatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-item .value {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        td {
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-danger {
            background-color: #fee;
            color: #c00;
        }
        .badge-warning {
            background-color: #ffc;
            color: #f90;
        }
        .badge-info {
            background-color: #e7f3ff;
            color: #0066cc;
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
        <h2>LAPORAN KETERLAMBATAN</h2>
        <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
        <p>Dicetak: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary" style="display: table; width: 100%; margin: 20px 0;">
        <div style="display: table-cell; text-align: center; padding: 10px;">
            <div class="label">Total Keterlambatan</div>
            <div class="value">{{ count($reportData) }}</div>
        </div>
        <div style="display: table-cell; text-align: center; padding: 10px;">
            <div class="label">Rata-rata Terlambat</div>
            <div class="value">{{ count($reportData) > 0 ? round(array_sum(array_column($reportData, 'late_minutes')) / count($reportData)) : 0 }} menit</div>
        </div>
        <div style="display: table-cell; text-align: center; padding: 10px;">
            <div class="label">Terlambat Terlama</div>
            <div class="value">{{ count($reportData) > 0 ? max(array_column($reportData, 'late_minutes')) : 0 }} menit</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 20%;">Nama</th>
                <th style="width: 10%;">NIK</th>
                <th style="width: 12%;">Shift</th>
                <th style="width: 10%;">Jam Shift</th>
                <th style="width: 10%;">Check-in</th>
                <th style="width: 11%;">Terlambat</th>
                <th style="width: 10%;">Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $index => $record)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($record['date'])->format('d M Y') }}</td>
                    <td>{{ $record['name'] }}</td>
                    <td class="text-center">{{ $record['nik'] }}</td>
                    <td>{{ $record['shift'] }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($record['shift_start'])->format('H:i') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($record['check_in_time'])->format('H:i') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $record['late_minutes'] > 60 ? 'badge-danger' : ($record['late_minutes'] > 30 ? 'badge-warning' : 'badge-info') }}">
                            {{ $record['late_minutes'] }} menit
                        </span>
                    </td>
                    <td>{{ $record['location'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data keterlambatan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} Sistem Presensi</p>
    </div>
</body>
</html>
