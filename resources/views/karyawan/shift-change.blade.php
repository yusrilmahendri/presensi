<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengajuan Pergantian Shift - {{ $user->organization->name ?? 'Sistem Presensi' }}</title>
    
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
        .shift-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85em;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('karyawan.dashboard') }}">
                <i class="fas fa-building"></i> {{ $user->organization->name ?? 'Sistem Presensi' }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('attendance.index') }}">
                            <i class="fas fa-calendar-check"></i> Absen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.leaves.index') }}">
                            <i class="fas fa-file-alt"></i> Pengajuan Izin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.overtime.index') }}">
                            <i class="fas fa-clock"></i> Lembur
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('shift-change.index') }}">
                            <i class="fas fa-arrow-right-arrow-left"></i> Pergantian Shift
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.profile') }}">
                            <i class="fas fa-user"></i> Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('karyawan.logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link" style="color: white;">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Current Shift Info -->
        <div class="card">
            <div class="card-body">
                <h5><i class="fas fa-clock"></i> Shift Saat Ini</h5>
                <div class="d-flex align-items-center mt-3">
                    @if($user->shift)
                        <div class="shift-badge bg-primary text-white me-3">
                            <i class="fas fa-briefcase"></i> {{ $user->shift->name }}
                        </div>
                        <div>
                            <strong>Jam Kerja:</strong> {{ $user->shift->start_time }} - {{ $user->shift->end_time }}
                        </div>
                    @else
                        <span class="text-muted">Belum memiliki shift</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Request Form -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Ajukan Pergantian Shift</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('shift-change.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-calendar-alt"></i> Pilih Shift Baru</label>
                        <select name="requested_shift_id" class="form-select @error('requested_shift_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach($availableShifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                </option>
                            @endforeach
                        </select>
                        @error('requested_shift_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-calendar-day"></i> Tanggal Efektif Pergantian</label>
                        <input type="date" 
                               name="effective_date" 
                               class="form-control @error('effective_date') is-invalid @enderror" 
                               value="{{ old('effective_date', date('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}"
                               required>
                        @error('effective_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pilih tanggal kapan shift baru akan mulai berlaku</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-comment"></i> Alasan Pergantian</label>
                        <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" 
                                  rows="4" required 
                                  placeholder="Jelaskan alasan Anda mengajukan pergantian shift (minimal 10 karakter)">{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimal 10 karakter</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Ajukan Pergantian
                    </button>
                </form>
            </div>
        </div>

        <!-- Request History -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Pengajuan</h5>
            </div>
            <div class="card-body">
                @if($requests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Tanggal Efektif</th>
                                    <th>Dari Shift</th>
                                    <th>Ke Shift</th>
                                    <th>Alasan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <i class="fas fa-calendar-day"></i> 
                                                {{ $request->effective_date ? \Carbon\Carbon::parse($request->effective_date)->format('d M Y') : '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $request->currentShift->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $request->requestedShift->name }}</span>
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($request->reason, 50) }}</small>
                                        </td>
                                        <td>
                                            @if($request->status === 'pending')
                                                <span class="status-badge status-pending">
                                                    <i class="fas fa-clock"></i> Menunggu
                                                </span>
                                            @elseif($request->status === 'approved')
                                                <span class="status-badge status-approved">
                                                    <i class="fas fa-check-circle"></i> Disetujui
                                                </span>
                                            @else
                                                <span class="status-badge status-rejected">
                                                    <i class="fas fa-times-circle"></i> Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal{{ $request->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            @if($request->status === 'pending')
                                                <form action="{{ route('shift-change.cancel', $request->id) }}" 
                                                      method="POST" 
                                                      class="cancel-form"
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger cancel-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    
                                    <!-- Detail Modal -->
                                    <div class="modal fade" id="detailModal{{ $request->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $request->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title" id="detailModalLabel{{ $request->id }}">
                                                        <i class="fas fa-info-circle"></i> Detail Pengajuan Pergantian Shift
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="text-muted small">Tanggal Pengajuan:</label>
                                                        <p class="fw-bold mb-0">{{ $request->created_at->format('d M Y H:i') }}</p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="text-muted small">Tanggal Efektif Pergantian:</label>
                                                        <p class="fw-bold mb-0">
                                                            <span class="badge bg-primary">
                                                                <i class="fas fa-calendar-day"></i> 
                                                                {{ $request->effective_date ? \Carbon\Carbon::parse($request->effective_date)->format('d M Y') : '-' }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="text-muted small">Dari Shift:</label>
                                                        <p class="fw-bold mb-0">
                                                            <span class="badge bg-secondary">{{ $request->currentShift->name }}</span>
                                                            <br>
                                                            <small class="text-muted">{{ $request->currentShift->start_time }} - {{ $request->currentShift->end_time }}</small>
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="text-muted small">Ke Shift:</label>
                                                        <p class="fw-bold mb-0">
                                                            <span class="badge bg-info">{{ $request->requestedShift->name }}</span>
                                                            <br>
                                                            <small class="text-muted">{{ $request->requestedShift->start_time }} - {{ $request->requestedShift->end_time }}</small>
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="text-muted small">Alasan:</label>
                                                        <p class="mb-0">{{ $request->reason }}</p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="text-muted small">Status:</label>
                                                        <p class="mb-0">
                                                            @if($request->status === 'pending')
                                                                <span class="badge bg-warning text-dark">
                                                                    <i class="fas fa-clock"></i> Menunggu Persetujuan
                                                                </span>
                                                            @elseif($request->status === 'approved')
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check-circle"></i> Disetujui
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-times-circle"></i> Ditolak
                                                                </span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    
                                                    @if($request->approved_at)
                                                        <div class="mb-3">
                                                            <label class="text-muted small">Tanggal Diproses:</label>
                                                            <p class="mb-0">{{ $request->approved_at->format('d M Y H:i') }}</p>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($request->approver)
                                                        <div class="mb-3">
                                                            <label class="text-muted small">Diproses Oleh:</label>
                                                            <p class="mb-0">{{ $request->approver->name }}</p>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($request->notes)
                                                        <div class="alert alert-info mb-0">
                                                            <strong><i class="fas fa-sticky-note"></i> Catatan Admin:</strong>
                                                            <p class="mb-0 mt-2">{{ $request->notes }}</p>
                                                        </div>
                                                    @endif
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
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-inbox"></i> Belum ada riwayat pengajuan pergantian shift.
                    </p>
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
    
    <!-- SweetAlert2 for Cancel Confirmation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle cancel button click
            const cancelButtons = document.querySelectorAll('.cancel-btn');
            
            cancelButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('.cancel-form');
                    
                    Swal.fire({
                        title: 'Batalkan Pengajuan?',
                        text: 'Yakin ingin membatalkan pengajuan pergantian shift ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash"></i> Ya, Batalkan',
                        cancelButtonText: '<i class="fas fa-times"></i> Tidak',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
