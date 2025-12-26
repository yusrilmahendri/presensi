<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil - {{ $user->organization->name ?? 'Sistem Presensi' }}</title>
    
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
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: bold;
        }
        .editable-field {
            background-color: #fff;
        }
        .disabled-field {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .info-badge {
            background: #f1f3f5;
            padding: 0.25rem 0.75rem;
            border-radius: 5px;
            font-size: 0.875rem;
            color: #6c757d;
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
                        <a class="nav-link" href="{{ route('karyawan.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.leaves.index') }}">Pengajuan Izin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karyawan.overtime.index') }}">Lembur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shift-change.index') }}">Ganti Shift</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('karyawan.profile') }}">Profil</a>
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

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Profile Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-circle me-2"></i> Informasi Profil
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="info-label">Nama Lengkap</label>
                                <input type="text" class="form-control disabled-field" value="{{ $user->name }}" disabled readonly>
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="info-label">Surel</label>
                                <input type="email" class="form-control disabled-field" value="{{ $user->email }}" disabled readonly>
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                        </div>

                        <div class="row">
                            @if($user->nik)
                            <div class="col-md-6 mb-3">
                                <label class="info-label">NIK</label>
                                <input type="text" class="form-control disabled-field" value="{{ $user->nik }}" disabled readonly>
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                            @endif
                            
                            @if($user->nip)
                            <div class="col-md-6 mb-3">
                                <label class="info-label">NIP</label>
                                <input type="text" class="form-control disabled-field" value="{{ $user->nip }}" disabled readonly>
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="info-label">Shift</label>
                                <input type="text" class="form-control disabled-field" value="{{ $user->shift ? $user->shift->name : 'Tidak ada shift' }}" disabled readonly>
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="info-label">Role</label>
                                <input type="text" class="form-control disabled-field" value="{{ ucfirst($user->role) }}" disabled readonly>
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                        </div>

                        @if($user->shift)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="info-label">Jam Masuk</label>
                                <input type="text" class="form-control disabled-field" value="{{ $user->shift->start_time }}" disabled readonly>
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="info-label">Jam Pulang</label>
                                <input type="text" class="form-control disabled-field" value="{{ $user->shift->end_time }}" disabled readonly>
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Edit Username & Password Card -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-key me-2"></i> Edit Username & Password
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Catatan:</strong> Anda hanya dapat mengubah username dan password. Data lainnya tidak dapat diubah oleh karyawan.
                        </div>

                        <form method="POST" action="{{ route('karyawan.profile.update') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="username" class="form-label info-label">
                                    <i class="fas fa-user me-1"></i> Username
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control editable-field @error('username') is-invalid @enderror" 
                                    id="username" 
                                    name="username" 
                                    value="{{ old('username', $user->username) }}"
                                    placeholder="Masukkan username baru (opsional)">
                                <small class="text-muted">Username saat ini: <strong>{{ $user->username ?? 'Belum diset' }}</strong></small>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <div class="mb-3">
                                <label for="password" class="form-label info-label">
                                    <i class="fas fa-lock me-1"></i> Password Baru
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control editable-field @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                                <small class="text-muted">Minimal 8 karakter</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label info-label">
                                    <i class="fas fa-lock me-1"></i> Konfirmasi Password
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control editable-field" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    placeholder="Ulangi password baru">
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('karyawan.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
