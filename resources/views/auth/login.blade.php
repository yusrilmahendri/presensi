<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Presensi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -250px;
            right: -250px;
            z-index: 0;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }

        .login-wrapper {
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 50px 30px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .login-header .icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .login-header .icon-wrapper i {
            font-size: 40px;
            color: white;
        }

        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            color: white;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 3;
        }

        .input-group-icon .form-control {
            padding-left: 50px;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 12px 16px;
        }

        .form-check-label {
            font-size: 14px;
            color: #666;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e0e0e0;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .invalid-feedback {
            display: block;
            font-size: 13px;
            margin-top: 6px;
            color: #dc3545;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-header {
                padding: 40px 25px;
            }

            .login-header h2 {
                font-size: 24px;
            }

            .login-header .icon-wrapper {
                width: 70px;
                height: 70px;
            }

            .login-header .icon-wrapper i {
                font-size: 35px;
            }

            .login-body {
                padding: 30px 25px;
            }

            .form-control {
                padding: 12px 14px;
                font-size: 14px;
            }

            .btn-login {
                padding: 12px;
                font-size: 15px;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 15px;
            }

            .login-wrapper {
                max-width: 100%;
            }

            .login-card {
                border-radius: 15px;
            }

            .login-header {
                padding: 30px 20px;
            }

            .login-header h2 {
                font-size: 22px;
            }

            .login-header .icon-wrapper {
                width: 60px;
                height: 60px;
                margin-bottom: 15px;
            }

            .login-header .icon-wrapper i {
                font-size: 30px;
            }

            .login-body {
                padding: 25px 20px;
            }

            .form-control {
                padding: 11px 12px;
                font-size: 14px;
            }

            .btn-login {
                padding: 11px;
            }

            .login-footer {
                margin-top: 20px;
                padding-top: 20px;
            }
            
            .copyright-footer {
                position: static !important;
                margin-top: 20px;
                padding: 12px 15px !important;
                font-size: 0.75em !important;
            }
        }
        
        /* Footer Styles */
        .copyright-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 20px;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .copyright-footer .copyright-text {
            color: #666;
            font-size: 0.85em;
            margin: 0;
        }
        
        .copyright-footer a {
            color: #764ba2;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .copyright-footer a:hover {
            color: #667eea;
            text-decoration: underline;
        }
        
        @media (max-width: 576px) {
            .copyright-footer {
                position: static;
                margin-top: 20px;
                padding: 12px 15px;
            }
            
            .copyright-footer .copyright-text {
                font-size: 0.75em;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="icon-wrapper">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h2>Login Sistem Presensi</h2>
                <p>Admin & Karyawan - Silakan masuk dengan akun Anda.</p>
            </div>

            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0" style="padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('karyawan.login.post') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="login" class="form-label">
                            <i class="fas fa-user me-2"></i>Username / NIK / NIP / Email
                        </label>
                        <div class="input-group-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" 
                                   class="form-control @error('login') is-invalid @enderror" 
                                   id="login" 
                                   name="login" 
                                   value="{{ old('login') }}" 
                                   required 
                                   autofocus
                                   placeholder="Username (admin) / NIK / NIP / Email">
                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <div class="input-group-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required
                                   placeholder="Masukkan password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                    </button>
                </form>

                <div class="login-footer">
                    <small class="text-muted">
                        <strong>Karyawan:</strong> Gunakan NIK/NIP | <strong>Admin:</strong> Gunakan Username
                    </small>
                </div>
                 <!-- <p class="copyright-text"> -->
            <!-- &copy; 2025 Created by <strong style="color: #667eea;">Yusril Mahendri</strong>  -->
            <!-- <a href="https://yusrilmahendri.site" target="_blank">yusrilmahendri.site</a> -->
        <!-- </p> -->
            </div>

        </div>
            
    </div>

    <!-- Footer -->
    <!-- <footer class="copyright-footer">
        <p class="copyright-text">
            &copy; 2025 Created by <strong style="color: #667eea;">Yusril Mahendri</strong> 
            <a href="https://yusrilmahendri.site" target="_blank">yusrilmahendri.site</a>
        </p>
    </footer> -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
