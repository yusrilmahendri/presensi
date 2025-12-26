<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ $user->organization->name ?? 'Sistem Presensi' }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: white !important;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card h5 {
            color: rgba(255,255,255,0.9);
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .stat-card h2 {
            font-size: 2.5em;
            font-weight: bold;
        }
        .today-status {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .status-checkin {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }
        .status-checkout {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .status-pending {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .table {
            background: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">{{ $user->organization->name ?? 'Sistem Presensi' }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('attendance.index') }}">Absen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('karyawan.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.leaves.index') }}">Pengajuan Izin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.overtime.index') }}">Lembur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shift-change.index') }}">Pergantian Shift</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.profile') }}">Profil</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('karyawan.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link" style="border: none; background: none; color: white !important;">
                                Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Welcome Section -->
        <div class="card">
            <div class="card-body">
                <h4>{{ $greeting }}, {{ $user->name }}!</h4>
                <p class="text-muted mb-0">
                    Surel: {{ $user->email }} | Shift: {{ $user->shift->name ?? 'Tidak ada' }}
                </p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar-check"></i> Absen Bulan Ini</h5>
                        <h2>{{ $thisMonthAttendance }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar-week"></i> Absen Minggu Ini</h5>
                        <h2>{{ $thisWeekAttendance }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="card-body">
                        <h5><i class="fas fa-sign-in-alt"></i> Total Check In</h5>
                        <h2>{{ $totalCheckIn }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);">
                    <div class="card-body">
                        <h5><i class="fas fa-sign-out-alt"></i> Total Check Out</h5>
                        <h2>{{ $totalCheckOut }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Statistics -->
        <div class="row">
            <div class="col-md-4">
                <div class="card stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body">
                        <h5><i class="fas fa-clock"></i> Terlambat</h5>
                        <h2>{{ $totalLate }}</h2>
                        <small style="opacity: 0.9;">Check in telat dari jadwal</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body">
                        <h5><i class="fas fa-check-circle"></i> Tepat Waktu</h5>
                        <h2>{{ $totalOnTime }}</h2>
                        <small style="opacity: 0.9;">Check in sesuai jadwal</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="card-body">
                        <h5><i class="fas fa-star"></i> Lebih Awal</h5>
                        <h2>{{ $totalEarly }}</h2>
                        <small style="opacity: 0.9;">Check in lebih awal</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status Hari Ini</h5>
            </div>
            <div class="card-body">
                @if($checkInToday)
                    <div class="today-status status-checkin">
                        <strong>✓ Check In:</strong> {{ $checkInToday->attendance_time->format('d M Y H:i:s') }}
                        
                        @if($todayStatus === 'late')
                            <span class="badge bg-danger ms-2">🕐 Terlambat</span>
                        @elseif($todayStatus === 'on_time')
                            <span class="badge bg-success ms-2">✓ Tepat Waktu</span>
                        @elseif($todayStatus === 'early')
                            <span class="badge bg-info ms-2">⭐ Lebih Awal</span>
                        @endif
                        
                        <br>
                        <small>Lokasi: {{ $checkInToday->attendanceLocation->name ?? 'N/A' }}</small>
                        @if($checkInToday->shift)
                            <br>
                            <small>Shift: {{ $checkInToday->shift->name }} ({{ $checkInToday->shift->start_time }} - {{ $checkInToday->shift->end_time }})</small>
                        @endif
                    </div>
                @else
                    <div class="today-status status-pending">
                        <strong>Belum Check In</strong>
                    </div>
                @endif

                @if($checkOutToday)
                    <div class="today-status status-checkout">
                        <strong>✓ Check Out:</strong> {{ $checkOutToday->attendance_time->format('d M Y H:i:s') }}
                        
                        @if($checkOutStatus === 'early')
                            <span class="badge bg-warning ms-2">🏃 Pulang Lebih Awal</span>
                        @elseif($checkOutStatus === 'on_time')
                            <span class="badge bg-success ms-2">✓ Pulang Tepat Waktu</span>
                        @elseif($checkOutStatus === 'overtime')
                            <span class="badge bg-info ms-2">💼 Lembur</span>
                        @endif
                        
                        <br>
                        <small>Lokasi: {{ $checkOutToday->attendanceLocation->name ?? 'N/A' }}</small>
                        @if($checkOutToday->shift)
                            <br>
                            <small>Shift: {{ $checkOutToday->shift->name }} ({{ $checkOutToday->shift->start_time }} - {{ $checkOutToday->shift->end_time }})</small>
                        @endif
                    </div>
                @else
                    @if($checkInToday)
                        <div class="today-status status-pending">
                            <strong>Belum Check Out</strong>
                        </div>
                    @endif
                @endif

                <a href="{{ route('attendance.index') }}" class="btn btn-primary mt-3">
                    Lakukan Absen
                </a>
            </div>
        </div>

        <!-- Recent Attendances -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Riwayat Absen Terkini</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal" data-type="excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal" data-type="pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($recentAttendances->count() > 0)
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-filter"></i> Filter Tipe:</label>
                            <select id="filterType" class="form-select form-select-sm">
                                <option value="all">Semua Tipe</option>
                                <option value="check_in">Check In Saja</option>
                                <option value="check_out">Check Out Saja</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-filter"></i> Filter Status:</label>
                            <select id="filterStatus" class="form-select form-select-sm">
                                <option value="all">Semua Status</option>
                                <optgroup label="Status Check In">
                                    <option value="late">🕐 Terlambat</option>
                                    <option value="on_time">✓ Tepat Waktu</option>
                                    <option value="early">⭐ Lebih Awal</option>
                                </optgroup>
                                <optgroup label="Status Check Out">
                                    <option value="checkout_early">🏃 Pulang Lebih Awal</option>
                                    <option value="checkout_on_time">✓ Pulang Tepat Waktu</option>
                                    <option value="overtime">💼 Lembur</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button id="resetFilter" class="btn btn-secondary btn-sm d-block w-100">
                                <i class="fas fa-redo"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal & Waktu</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Lokasi</th>
                                    <th>Foto</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                @foreach($recentAttendances as $attendance)
                                    @php
                                        $statusValue = 'none';
                                        if ($attendance->type === 'check_in' && $attendance->shift) {
                                            $checkInTime = $attendance->attendance_time;
                                            $shiftStart = \Carbon\Carbon::parse($attendance->shift->start_time);
                                            $shiftStart->setDate($checkInTime->year, $checkInTime->month, $checkInTime->day);
                                            $diffMinutes = $checkInTime->diffInMinutes($shiftStart, false);
                                            
                                            if ($diffMinutes > 0) {
                                                $statusValue = 'late';
                                            } elseif ($diffMinutes >= -15) {
                                                $statusValue = 'on_time';
                                            } else {
                                                $statusValue = 'early';
                                            }
                                        } elseif ($attendance->type === 'check_out' && $attendance->shift) {
                                            $checkOutTime = $attendance->attendance_time;
                                            $shiftEnd = \Carbon\Carbon::parse($attendance->shift->end_time);
                                            $shiftEnd->setDate($checkOutTime->year, $checkOutTime->month, $checkOutTime->day);
                                            
                                            if ($attendance->shift->end_time < $attendance->shift->start_time) {
                                                $shiftEnd->addDay();
                                            }
                                            
                                            $diffMinutes = $checkOutTime->diffInMinutes($shiftEnd, false);
                                            
                                            if ($diffMinutes > 15) {
                                                $statusValue = 'checkout_early';
                                            } elseif ($diffMinutes >= -15) {
                                                $statusValue = 'checkout_on_time';
                                            } else {
                                                $statusValue = 'overtime';
                                            }
                                        }
                                    @endphp
                                    <tr data-type="{{ $attendance->type }}" data-status="{{ $statusValue }}">
                                        <td>{{ $attendance->attendance_time->format('d M Y H:i:s') }}</td>
                                        <td>
                                            @if($attendance->type === 'check_in')
                                                <span class="badge bg-success">Check In</span>
                                            @else
                                                <span class="badge bg-warning">Check Out</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->type === 'check_in' && $attendance->shift)
                                                @php
                                                    $checkInTime = $attendance->attendance_time;
                                                    $shiftStart = \Carbon\Carbon::parse($attendance->shift->start_time);
                                                    $shiftStart->setDate($checkInTime->year, $checkInTime->month, $checkInTime->day);
                                                    $diffMinutes = $checkInTime->diffInMinutes($shiftStart, false);
                                                    
                                                    if ($diffMinutes > 0) {
                                                        $label = 'Terlambat';
                                                        $badgeClass = 'bg-danger';
                                                        $icon = '🕐';
                                                    } elseif ($diffMinutes >= -15) {
                                                        $label = 'Tepat Waktu';
                                                        $badgeClass = 'bg-success';
                                                        $icon = '✓';
                                                    } else {
                                                        $label = 'Lebih Awal';
                                                        $badgeClass = 'bg-info';
                                                        $icon = '⭐';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $icon }} {{ $label }}</span>
                                            @elseif($attendance->type === 'check_out' && $attendance->shift)
                                                @php
                                                    $checkOutTime = $attendance->attendance_time;
                                                    $shiftEnd = \Carbon\Carbon::parse($attendance->shift->end_time);
                                                    $shiftEnd->setDate($checkOutTime->year, $checkOutTime->month, $checkOutTime->day);
                                                    
                                                    // Handle night shifts
                                                    if ($attendance->shift->end_time < $attendance->shift->start_time) {
                                                        $shiftEnd->addDay();
                                                    }
                                                    
                                                    $diffMinutes = $checkOutTime->diffInMinutes($shiftEnd, false);
                                                    
                                                    if ($diffMinutes > 15) {
                                                        $label = 'Pulang Lebih Awal';
                                                        $badgeClass = 'bg-warning';
                                                        $icon = '🏃';
                                                    } elseif ($diffMinutes >= -15) {
                                                        $label = 'Pulang Tepat Waktu';
                                                        $badgeClass = 'bg-success';
                                                        $icon = '✓';
                                                    } else {
                                                        $label = 'Lembur';
                                                        $badgeClass = 'bg-info';
                                                        $icon = '💼';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $icon }} {{ $label }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $attendance->attendanceLocation->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($attendance->photo)
                                                <a href="{{ asset('storage/' . $attendance->photo) }}" target="_blank" class="btn btn-sm btn-info">
                                                    Lihat Foto
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Belum ada riwayat absen.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Riwayat Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="exportForm" method="GET">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <small class="text-muted">Kosongkan untuk export semua data</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: white; padding: 20px 0; margin-top: 40px; text-align: center; box-shadow: 0 -2px 10px rgba(0,0,0,0.1);">
        <div class="container">
            <div style="color: #666; font-size: 0.85em;">
                &copy; 2025 Created by <strong style="color: #667eea;">Yusril Mahendri</strong> 
                <a href="https://yusrilmahendri.site" target="_blank" style="color: #764ba2; text-decoration: none;">yusrilmahendri.site</a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Attendance Filter
        document.addEventListener('DOMContentLoaded', function() {
            const filterType = document.getElementById('filterType');
            const filterStatus = document.getElementById('filterStatus');
            const resetFilter = document.getElementById('resetFilter');
            const tableRows = document.querySelectorAll('#attendanceTableBody tr');
            
            function applyFilters() {
                const selectedType = filterType.value;
                const selectedStatus = filterStatus.value;
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    if (row.classList.contains('no-data-row')) return;
                    
                    const rowType = row.getAttribute('data-type');
                    const rowStatus = row.getAttribute('data-status');
                    
                    let showRow = true;
                    
                    // Filter by type
                    if (selectedType !== 'all' && rowType !== selectedType) {
                        showRow = false;
                    }
                    
                    // Filter by status
                    if (selectedStatus !== 'all' && rowStatus !== selectedStatus) {
                        showRow = false;
                    }
                    
                    if (showRow) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Show message if no results
                const tbody = document.getElementById('attendanceTableBody');
                let noDataRow = tbody.querySelector('.no-data-row');
                
                if (visibleCount === 0) {
                    if (!noDataRow) {
                        noDataRow = document.createElement('tr');
                        noDataRow.className = 'no-data-row';
                        noDataRow.innerHTML = '<td colspan="5" class="text-center text-muted py-3"><i class="fas fa-search"></i> Tidak ada data yang sesuai dengan filter.</td>';
                        tbody.appendChild(noDataRow);
                    }
                } else {
                    if (noDataRow) {
                        noDataRow.remove();
                    }
                }
            }
            
            if (filterType && filterStatus && resetFilter) {
                filterType.addEventListener('change', applyFilters);
                filterStatus.addEventListener('change', applyFilters);
                
                resetFilter.addEventListener('click', function() {
                    filterType.value = 'all';
                    filterStatus.value = 'all';
                    applyFilters();
                });
            }
        });
        
        // Handle export modal
        const exportModal = document.getElementById('exportModal');
        const exportForm = document.getElementById('exportForm');
        
        exportModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const type = button.getAttribute('data-type');
            
            if (type === 'excel') {
                exportForm.action = '{{ route("karyawan.export.excel") }}';
                exportModal.querySelector('.modal-title').textContent = 'Export Excel - Riwayat Absensi';
            } else {
                exportForm.action = '{{ route("karyawan.export.pdf") }}';
                exportModal.querySelector('.modal-title').textContent = 'Export PDF - Riwayat Absensi';
            }
        });
    </script>
</body>
</html>

