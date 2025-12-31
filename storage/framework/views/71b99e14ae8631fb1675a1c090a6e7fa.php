<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekap Bulanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        .info {
            margin-bottom: 15px;
        }
        .info table {
            width: 100%;
            border: none;
        }
        .info td {
            padding: 3px 0;
            border: none;
        }
        .info .label {
            width: 150px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th {
            background-color: #4a5568;
            color: white;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #2d3748;
        }
        table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
        }
        table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }
        table tbody tr:hover {
            background-color: #e9ecef;
        }
        .text-left {
            text-align: left !important;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        .summary-box {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .summary-item {
            text-align: center;
            padding: 8px;
            background: white;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .summary-item .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN REKAP KEHADIRAN BULANAN</h2>
        <p>Periode: <?php echo e($month); ?> <?php echo e($year); ?></p>
        <p style="font-size: 10px;">Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
    </div>

    <div class="summary-box">
        <div style="text-align: center; margin-bottom: 10px; font-weight: bold;">Ringkasan</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Karyawan</div>
                <div class="value"><?php echo e(count($reportData)); ?></div>
            </div>
            <div class="summary-item">
                <div class="label">Rata-rata Kehadiran</div>
                <div class="value"><?php echo e(count($reportData) > 0 ? round(collect($reportData)->avg('persentase_hadir'), 1) : 0); ?>%</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Keterlambatan</div>
                <div class="value" style="color: #dc3545;"><?php echo e(collect($reportData)->sum('total_terlambat')); ?></div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;" class="text-left">Nama Karyawan</th>
                <th style="width: 10%;">Hari Kerja</th>
                <th style="width: 10%;">Hadir</th>
                <th style="width: 10%;">Tepat Waktu</th>
                <th style="width: 10%;">Terlambat</th>
                <th style="width: 10%;">Alpha</th>
                <th style="width: 12%;">% Kehadiran</th>
                <th style="width: 13%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td class="text-left">
                        <strong><?php echo e($data['user']->name); ?></strong><br>
                        <span style="color: #666; font-size: 9px;"><?php echo e($data['user']->email); ?></span>
                    </td>
                    <td><?php echo e($data['total_hari_kerja']); ?></td>
                    <td><strong><?php echo e($data['total_hadir']); ?></strong></td>
                    <td style="color: #28a745;"><?php echo e($data['total_tepat_waktu']); ?></td>
                    <td style="color: #dc3545;"><?php echo e($data['total_terlambat']); ?></td>
                    <td style="color: #6c757d;"><?php echo e($data['total_alpha']); ?></td>
                    <td>
                        <strong 
                            <?php if($data['persentase_hadir'] >= 95): ?>
                                style="color: #28a745;"
                            <?php elseif($data['persentase_hadir'] >= 80): ?>
                                style="color: #ffc107;"
                            <?php else: ?>
                                style="color: #dc3545;"
                            <?php endif; ?>
                        >
                            <?php echo e($data['persentase_hadir']); ?>%
                        </strong>
                    </td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['persentase_hadir'] >= 95): ?>
                            <span class="badge badge-success">Sangat Baik</span>
                        <?php elseif($data['persentase_hadir'] >= 80): ?>
                            <span class="badge badge-warning">Baik</span>
                        <?php elseif($data['persentase_hadir'] >= 60): ?>
                            <span class="badge badge-danger">Perlu Perbaikan</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Buruk</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #999;">
                        Tidak ada data untuk ditampilkan
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="2" class="text-left">TOTAL</td>
                <td><?php echo e(collect($reportData)->sum('total_hari_kerja')); ?></td>
                <td><?php echo e(collect($reportData)->sum('total_hadir')); ?></td>
                <td style="color: #28a745;"><?php echo e(collect($reportData)->sum('total_tepat_waktu')); ?></td>
                <td style="color: #dc3545;"><?php echo e(collect($reportData)->sum('total_terlambat')); ?></td>
                <td style="color: #6c757d;"><?php echo e(collect($reportData)->sum('total_alpha')); ?></td>
                <td colspan="2">-</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Presensi</p>
        <p>© <?php echo e(now()->year); ?> - Semua hak cipta dilindungi</p>
    </div>
</body>
</html>
<?php /**PATH /Users/mac/Documents/code/web/presensi/resources/views/exports/monthly-recap-pdf.blade.php ENDPATH**/ ?>