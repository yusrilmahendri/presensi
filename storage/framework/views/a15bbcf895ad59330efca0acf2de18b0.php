<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            color: #667eea;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 3px 0;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table.data th {
            background-color: #667eea;
            color: white;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #667eea;
            font-size: 11px;
        }
        table.data td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table.data tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h4 {
            margin: 0 0 10px 0;
            color: #667eea;
        }
        .summary table {
            width: 100%;
        }
        .summary td {
            padding: 5px 0;
        }
        .summary td:first-child {
            font-weight: bold;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA ABSENSI</h2>
        <p>Sistem Presensi Karyawan</p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startDate || $endDate): ?>
            <p>
                Periode: 
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startDate): ?> <?php echo e(\Carbon\Carbon::parse($startDate)->format('d M Y')); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startDate && $endDate): ?> - <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($endDate): ?> <?php echo e(\Carbon\Carbon::parse($endDate)->format('d M Y')); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="info">
        <table>
            <tr>
                <td style="width: 150px;"><strong>Tanggal Cetak</strong></td>
                <td>: <?php echo e(date('d F Y H:i:s')); ?></td>
            </tr>
            <tr>
                <td><strong>Total Data</strong></td>
                <td>: <?php echo e($attendances->count()); ?> record</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Karyawan</th>
                <th style="width: 12%;">NIK</th>
                <th style="width: 12%;">Shift</th>
                <th style="width: 10%;">Tipe</th>
                <th style="width: 18%;">Waktu Absen</th>
                <th style="width: 23%;">Lokasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="text-align: center;"><?php echo e($index + 1); ?></td>
                <td><?php echo e($attendance->user->name ?? '-'); ?></td>
                <td><?php echo e($attendance->user->nik ?? '-'); ?></td>
                <td><?php echo e($attendance->shift->name ?? '-'); ?></td>
                <td>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->type === 'check_in'): ?>
                        <span class="badge badge-success">Check In</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Check Out</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
                <td><?php echo e($attendance->attendance_time->format('d M Y H:i:s')); ?></td>
                <td><?php echo e($attendance->attendanceLocation->name ?? '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <div class="summary">
        <h4>Ringkasan</h4>
        <table>
            <tr>
                <td>Total Check In</td>
                <td>: <?php echo e($attendances->where('type', 'check_in')->count()); ?></td>
            </tr>
            <tr>
                <td>Total Check Out</td>
                <td>: <?php echo e($attendances->where('type', 'check_out')->count()); ?></td>
            </tr>
            <tr>
                <td>Total Karyawan</td>
                <td>: <?php echo e($attendances->pluck('user_id')->unique()->count()); ?></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>© <?php echo e(date('Y')); ?> Created by Yusril Mahendri - yusrilmahendri.site</p>
    </div>
</body>
</html>
<?php /**PATH /Users/mac/Documents/code/web/presensi/resources/views/exports/attendances-pdf.blade.php ENDPATH**/ ?>