<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengajuan Izin - Sistem Presensi</title>
    
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
        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }
        .badge-approved {
            background-color: #28a745;
            color: #fff;
        }
        .badge-rejected {
            background-color: #dc3545;
            color: #fff;
        }
        /* Modal styles */
        .modal-body {
            padding: 1.5rem !important;
            min-height: 200px;
        }
        .modal-dialog {
            max-width: 600px;
        }
        .modal-content {
            border-radius: 10px;
        }
        .table-borderless td {
            padding: 0.5rem 0;
            vertical-align: top;
        }
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistem Presensi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('attendance.index') }}">Absen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('karyawan.leaves.index') }}">Pengajuan Izin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.profile') }}">Profile</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('karyawan.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link" style="border: none; background: none; color: white !important;">
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Form Pengajuan -->
        <div class="card">
            <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Ajukan Izin Baru</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('karyawan.leaves.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Jenis Izin <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Pilih Jenis Izin</option>
                                <option value="sakit" {{ old('type') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="izin" {{ old('type') == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="cuti" {{ old('type') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="total_days" class="form-label">Total Hari</label>
                            <input type="number" class="form-control" id="total_days" name="total_days" value="{{ old('total_days', 1) }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="attachment" class="form-label">Lampiran (Opsional)</label>
                        <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*">
                        <small class="text-muted">Upload surat dokter atau dokumen pendukung (gambar)</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Ajukan Izin
                    </button>
                </form>
            </div>
        </div>

        <!-- Riwayat Pengajuan -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Pengajuan Izin</h5>
            </div>
            <div class="card-body">
                @if($leaves->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis</th>
                                    <th>Periode</th>
                                    <th>Jumlah Hari</th>
                                    <th>Status</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaves as $index => $leave)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($leave->type === 'sakit')
                                                <span class="badge bg-danger">Sakit</span>
                                            @elseif($leave->type === 'izin')
                                                <span class="badge bg-warning text-dark">Izin</span>
                                            @else
                                                <span class="badge bg-info">Cuti</span>
                                            @endif
                                        </td>
                                        <td>{{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}</td>
                                        <td>{{ $leave->total_days }} hari</td>
                                        <td>
                                            @if($leave->status === 'pending')
                                                <span class="badge badge-pending">Menunggu</span>
                                            @elseif($leave->status === 'approved')
                                                <span class="badge badge-approved">Disetujui</span>
                                            @else
                                                <span class="badge badge-rejected">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>{{ $leave->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $leave->id }}">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modals - Outside of table -->
                    @foreach($leaves as $leave)
                        <div class="modal fade" id="detailModal{{ $leave->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $leave->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <h5 class="modal-title" id="detailModalLabel{{ $leave->id }}">
                                            <i class="fas fa-info-circle"></i> Detail Pengajuan Izin
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td width="150"><strong>Jenis Izin</strong></td>
                                                    <td>: {{ $leave->type_label }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Periode</strong></td>
                                                    <td>: {{ $leave->start_date->format('d M Y') }} s/d {{ $leave->end_date->format('d M Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total Hari</strong></td>
                                                    <td>: {{ $leave->total_days }} hari</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Alasan</strong></td>
                                                    <td>: {{ $leave->reason }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Status</strong></td>
                                                    <td>: 
                                                        @if($leave->status === 'pending')
                                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                                        @elseif($leave->status === 'approved')
                                                            <span class="badge bg-success">Disetujui</span>
                                                        @else
                                                            <span class="badge bg-danger">Ditolak</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if($leave->attachment)
                                                    <tr>
                                                        <td><strong>Lampiran</strong></td>
                                                        <td>: <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-paperclip"></i> Lihat File</a></td>
                                                    </tr>
                                                @endif
                                                @if($leave->admin_notes)
                                                    <tr>
                                                        <td><strong>Catatan Admin</strong></td>
                                                        <td>: {{ $leave->admin_notes }}</td>
                                                    </tr>
                                                @endif
                                                @if($leave->approved_by)
                                                    <tr>
                                                        <td><strong>Disetujui Oleh</strong></td>
                                                        <td>: {{ $leave->approvedBy->name ?? '-' }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times"></i> Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">Belum ada riwayat pengajuan izin.</p>
                @endif
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
        // Calculate total days
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const totalDays = document.getElementById('total_days');

        function calculateDays() {
            if (startDate.value && endDate.value) {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                totalDays.value = diff > 0 ? diff : 0;
            }
        }

        startDate.addEventListener('change', calculateDays);
        endDate.addEventListener('change', calculateDays);

        // Ensure modals work properly
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all modals
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                new bootstrap.Modal(modal, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
            });

            // Debug: Log when modal is shown
            modals.forEach(function(modal) {
                modal.addEventListener('show.bs.modal', function (event) {
                    console.log('Modal opened:', this.id);
                });
            });
        });
    </script>
</body>
</html>
