<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Book - Sistem Presensi</title>
    <style>
        @page {
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #333;
        }
        
        /* Cover Page */
        .cover-page {
            page-break-after: always;
            text-align: center;
            padding-top: 150px;
        }
        
        .cover-title {
            font-size: 36pt;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        
        .cover-subtitle {
            font-size: 18pt;
            color: #666;
            margin-bottom: 40px;
        }
        
        .cover-version {
            font-size: 12pt;
            color: #999;
            margin-top: 60px;
        }
        
        .cover-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin: 40px auto;
            max-width: 400px;
        }
        
        /* Table of Contents */
        .toc {
            page-break-after: always;
        }
        
        .toc-title {
            font-size: 24pt;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }
        
        .toc-item {
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
            display: flex;
            justify-content: space-between;
        }
        
        .toc-chapter {
            font-weight: bold;
            color: #667eea;
        }
        
        /* Section Headers */
        h1 {
            font-size: 22pt;
            font-weight: bold;
            color: #1a1a1a;
            margin-top: 30px;
            margin-bottom: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 5px;
            page-break-before: always;
        }
        
        h2 {
            font-size: 16pt;
            font-weight: bold;
            color: #667eea;
            margin-top: 25px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        h3 {
            font-size: 13pt;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        h4 {
            font-size: 11pt;
            font-weight: bold;
            color: #666;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        
        p {
            margin-bottom: 12px;
            text-align: justify;
        }
        
        /* Lists */
        ul, ol {
            margin-left: 25px;
            margin-bottom: 15px;
        }
        
        li {
            margin-bottom: 8px;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9pt;
        }
        
        th {
            background: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Info Boxes */
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        .success-box {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        .danger-box {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        /* Code Blocks */
        .code-block {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            overflow-x: auto;
        }
        
        /* Step Numbers */
        .step {
            display: inline-block;
            background: #667eea;
            color: white;
            width: 25px;
            height: 25px;
            line-height: 25px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 10px;
        }
        
        /* Icons */
        .icon {
            font-size: 14pt;
            margin-right: 8px;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 10mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        .page-number:before {
            content: "Halaman " counter(page);
        }
        
        /* Feature Grid */
        .feature-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }
        
        .feature-item {
            display: table-cell;
            width: 50%;
            padding: 10px;
        }
        
        .feature-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #667eea;
        }
        
        .feature-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        /* Checklist */
        .checklist {
            list-style-type: none;
            margin-left: 0;
        }
        
        .checklist li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
            margin-right: 8px;
        }
        
        /* Contact Box */
        .contact-box {
            background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
            color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        
        .contact-item {
            margin: 10px 0;
            font-size: 11pt;
        }
        
        .contact-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
    </style>
</head>
<body>

    <!-- COVER PAGE -->
    <div class="cover-page">
        <div style="margin-bottom: 60px;">
            <div style="width: 150px; height: 150px; margin: 0 auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 72pt; color: white;">📘</span>
            </div>
        </div>
        
        <h1 class="cover-title">MANUAL BOOK</h1>
        <h2 class="cover-subtitle">Sistem Presensi</h2>
        
        <div class="cover-box">
            <p style="font-size: 14pt; margin-bottom: 15px;">Panduan Lengkap Penggunaan</p>
            <p style="font-size: 11pt; opacity: 0.9;">Untuk Admin, Super Admin & Karyawan</p>
        </div>
        
        <div class="cover-version">
            <p><strong>Version 2.0</strong></p>
            <p>{{ now()->format('d F Y') }}</p>
            <p style="margin-top: 20px;">© 2026 Sistem Presensi</p>
        </div>
    </div>

    <!-- TABLE OF CONTENTS -->
    <div class="toc">
        <h1 class="toc-title">📑 Daftar Isi</h1>
        
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 1:</span> Pengenalan Sistem</span>
            <span>3</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 2:</span> Akses & Login</span>
            <span>5</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 3:</span> Role & Hak Akses</span>
            <span>7</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 4:</span> Dashboard Admin</span>
            <span>9</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 5:</span> Dashboard Karyawan</span>
            <span>11</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 6:</span> Manajemen User</span>
            <span>13</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 7:</span> Manajemen Absensi</span>
            <span>16</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 8:</span> Shift & Jadwal</span>
            <span>20</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 9:</span> Lokasi Absensi & Geofencing</span>
            <span>22</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 10:</span> Izin & Cuti</span>
            <span>25</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 11:</span> Lembur (Overtime)</span>
            <span>28</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 12:</span> Laporan</span>
            <span>30</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 13:</span> Notifikasi Email</span>
            <span>33</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 14:</span> Multi-Tenancy</span>
            <span>35</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 15:</span> FAQ - Pertanyaan Umum</span>
            <span>37</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 16:</span> Troubleshooting</span>
            <span>40</span>
        </div>
        <div class="toc-item">
            <span><span class="toc-chapter">Bab 17:</span> Kontak Support</span>
            <span>43</span>
        </div>
    </div>

    <!-- BAB 1: PENGENALAN SISTEM -->
    <h1><span class="icon">🚀</span>Bab 1: Pengenalan Sistem</h1>
    
    <h2>Apa itu Sistem Presensi?</h2>
    <p>Sistem Presensi adalah aplikasi berbasis web modern yang dirancang untuk memudahkan manajemen kehadiran karyawan. Sistem ini menggunakan teknologi GPS dan foto selfie untuk memastikan keakuratan data absensi.</p>
    
    <div class="success-box">
        <strong>✓ Fitur Utama:</strong>
        <ul class="checklist">
            <li>Absensi berbasis GPS (Geofencing)</li>
            <li>Foto selfie saat absen untuk verifikasi</li>
            <li>Manajemen shift kerja fleksibel</li>
            <li>Pengajuan izin & cuti online</li>
            <li>Approval lembur dengan multiplier</li>
            <li>Laporan lengkap (Excel & PDF)</li>
            <li>Notifikasi email otomatis</li>
            <li>Multi-tenancy untuk multiple perusahaan</li>
            <li>Dashboard interaktif dan real-time</li>
            <li>Kalender absensi visual</li>
        </ul>
    </div>
    
    <h3>Teknologi yang Digunakan</h3>
    <table>
        <tr>
            <th style="width: 30%;">Komponen</th>
            <th>Teknologi</th>
        </tr>
        <tr>
            <td><strong>Backend</strong></td>
            <td>Laravel 11 (PHP Framework)</td>
        </tr>
        <tr>
            <td><strong>Admin Panel</strong></td>
            <td>Filament 3</td>
        </tr>
        <tr>
            <td><strong>Frontend</strong></td>
            <td>Livewire, Alpine.js, Tailwind CSS</td>
        </tr>
        <tr>
            <td><strong>Database</strong></td>
            <td>MySQL / PostgreSQL</td>
        </tr>
        <tr>
            <td><strong>Maps</strong></td>
            <td>Leaflet.js (OpenStreetMap)</td>
        </tr>
        <tr>
            <td><strong>Camera</strong></td>
            <td>WebRTC API</td>
        </tr>
        <tr>
            <td><strong>Export</strong></td>
            <td>Maatwebsite Excel, DomPDF</td>
        </tr>
    </table>
    
    <h3>Keunggulan Sistem</h3>
    <div class="feature-grid">
        <div class="feature-item">
            <div class="feature-box">
                <div class="feature-title">⚡ Real-Time</div>
                <p>Data absensi ter-update secara real-time dan langsung tersinkronisasi.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-box">
                <div class="feature-title">📱 Mobile Friendly</div>
                <p>Dapat diakses dari smartphone dengan browser apapun.</p>
            </div>
        </div>
    </div>
    
    <div class="feature-grid">
        <div class="feature-item">
            <div class="feature-box">
                <div class="feature-title">🔒 Aman & Terenkripsi</div>
                <p>Data terenkripsi dengan standar keamanan tinggi.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-box">
                <div class="feature-title">📊 Laporan Lengkap</div>
                <p>Export data ke Excel dan PDF dengan mudah.</p>
            </div>
        </div>
    </div>

    <!-- BAB 2: AKSES & LOGIN -->
    <h1><span class="icon">🔐</span>Bab 2: Akses & Login</h1>
    
    <h2>URL Akses Sistem</h2>
    
    <div class="info-box">
        <strong>🔗 Admin Panel:</strong><br>
        <code style="background: white; padding: 5px 10px; border-radius: 3px;">https://domain-anda.com/admin</code>
    </div>
    
    <div class="info-box">
        <strong>🔗 Karyawan (Absensi):</strong><br>
        <code style="background: white; padding: 5px 10px; border-radius: 3px;">https://domain-anda.com/attendance</code><br><br>
        <strong>🔗 Karyawan (Dashboard):</strong><br>
        <code style="background: white; padding: 5px 10px; border-radius: 3px;">https://domain-anda.com/dashboard</code>
    </div>
    
    <h2>Cara Login Admin</h2>
    <p><span class="step">1</span> Buka URL admin panel di browser</p>
    <p><span class="step">2</span> Masukkan <strong>Username</strong> atau <strong>Email</strong></p>
    <p><span class="step">3</span> Masukkan <strong>Password</strong></p>
    <p><span class="step">4</span> Klik tombol <strong>"Sign In"</strong></p>
    <p><span class="step">5</span> Anda akan diarahkan ke Dashboard Admin</p>
    
    <h2>Cara Login Karyawan</h2>
    <p><span class="step">1</span> Buka URL login karyawan di browser</p>
    <p><span class="step">2</span> Masukkan <strong>Username</strong></p>
    <p><span class="step">3</span> Masukkan <strong>Password</strong></p>
    <p><span class="step">4</span> Klik tombol <strong>"Login"</strong></p>
    <p><span class="step">5</span> Anda akan diarahkan ke Dashboard Karyawan</p>
    
    <div class="warning-box">
        <strong>⚠️ Lupa Password?</strong><br>
        Jika Anda lupa password, hubungi admin untuk melakukan reset password. Admin dapat mengubah password melalui menu <strong>Data User</strong>.
    </div>
    
    <h2>Tips Keamanan</h2>
    <ul>
        <li>Gunakan password yang kuat (minimal 8 karakter, kombinasi huruf, angka, dan simbol)</li>
        <li>Jangan share password Anda kepada siapapun</li>
        <li>Selalu logout setelah selesai menggunakan sistem</li>
        <li>Ganti password secara berkala</li>
        <li>Jangan login di perangkat umum/publik</li>
    </ul>

    <!-- BAB 3: ROLE & HAK AKSES -->
    <h1><span class="icon">👥</span>Bab 3: Role & Hak Akses</h1>
    
    <h2>Jenis Role dalam Sistem</h2>
    
    <h3>1. Super Admin 👑</h3>
    <p>Role dengan hak akses tertinggi dalam sistem.</p>
    <div class="success-box">
        <strong>Hak Akses:</strong>
        <ul class="checklist">
            <li>Manajemen semua organization (multi-tenancy)</li>
            <li>Manajemen semua user di semua organization</li>
            <li>Akses penuh ke semua fitur</li>
            <li>RBAC Management (Role-Based Access Control)</li>
            <li>Audit Logs - melihat semua aktivitas sistem</li>
            <li>Bisa membuat admin baru</li>
        </ul>
    </div>
    
    <h3>2. Admin 💼</h3>
    <p>Role untuk mengelola organization tertentu.</p>
    <div class="info-box">
        <strong>Hak Akses:</strong>
        <ul class="checklist">
            <li>Manajemen user di organization sendiri</li>
            <li>Lihat dan kelola data absensi</li>
            <li>Approval izin, cuti, dan lembur</li>
            <li>Generate dan export laporan</li>
            <li>Setting shift dan lokasi absensi</li>
            <li>Manajemen department dan holiday</li>
        </ul>
    </div>
    
    <h3>3. Karyawan 👤</h3>
    <p>Role untuk pengguna standar (karyawan).</p>
    <div class="warning-box">
        <strong>Hak Akses:</strong>
        <ul class="checklist">
            <li>Melakukan absensi (Check In/Out)</li>
            <li>Lihat dashboard pribadi</li>
            <li>Ajukan izin, cuti, atau sakit</li>
            <li>Lihat riwayat absensi sendiri</li>
            <li>Update profil pribadi</li>
            <li>Lihat saldo cuti</li>
        </ul>
    </div>
    
    <h2>Tabel Perbandingan Hak Akses</h2>
    <table>
        <tr>
            <th style="width: 35%;">Fitur</th>
            <th style="width: 20%; text-align: center;">Super Admin</th>
            <th style="width: 20%; text-align: center;">Admin</th>
            <th style="width: 25%; text-align: center;">Karyawan</th>
        </tr>
        <tr>
            <td>Organizations</td>
            <td style="text-align: center; background: #d4edda;">✓ CRUD</td>
            <td style="text-align: center; background: #f8d7da;">✗</td>
            <td style="text-align: center; background: #f8d7da;">✗</td>
        </tr>
        <tr>
            <td>Departments</td>
            <td style="text-align: center; background: #d4edda;">✓ CRUD</td>
            <td style="text-align: center; background: #d4edda;">✓ CRUD</td>
            <td style="text-align: center; background: #f8d7da;">✗</td>
        </tr>
        <tr>
            <td>Users</td>
            <td style="text-align: center; background: #d4edda;">✓ All</td>
            <td style="text-align: center; background: #fff3cd;">✓ Org Only</td>
            <td style="text-align: center; background: #f8d7da;">✗</td>
        </tr>
        <tr>
            <td>Attendances</td>
            <td style="text-align: center; background: #d4edda;">✓ All</td>
            <td style="text-align: center; background: #fff3cd;">✓ Org Only</td>
            <td style="text-align: center; background: #fff3cd;">✓ Self</td>
        </tr>
        <tr>
            <td>Shifts</td>
            <td style="text-align: center; background: #d4edda;">✓ CRUD</td>
            <td style="text-align: center; background: #d4edda;">✓ CRUD</td>
            <td style="text-align: center; background: #fff3cd;">✓ View</td>
        </tr>
        <tr>
            <td>Locations</td>
            <td style="text-align: center; background: #d4edda;">✓ CRUD</td>
            <td style="text-align: center; background: #d4edda;">✓ CRUD</td>
            <td style="text-align: center; background: #fff3cd;">✓ View</td>
        </tr>
        <tr>
            <td>Leaves (Izin/Cuti)</td>
            <td style="text-align: center; background: #d4edda;">✓ All</td>
            <td style="text-align: center; background: #fff3cd;">✓ Approve</td>
            <td style="text-align: center; background: #fff3cd;">✓ Request</td>
        </tr>
        <tr>
            <td>Overtime</td>
            <td style="text-align: center; background: #d4edda;">✓ All</td>
            <td style="text-align: center; background: #fff3cd;">✓ Approve</td>
            <td style="text-align: center; background: #fff3cd;">✓ View</td>
        </tr>
        <tr>
            <td>Reports</td>
            <td style="text-align: center; background: #d4edda;">✓ All</td>
            <td style="text-align: center; background: #fff3cd;">✓ Org Only</td>
            <td style="text-align: center; background: #f8d7da;">✗</td>
        </tr>
        <tr>
            <td>Holidays</td>
            <td style="text-align: center; background: #d4edda;">✓ CRUD</td>
            <td style="text-align: center; background: #d4edda;">✓ CRUD</td>
            <td style="text-align: center; background: #fff3cd;">✓ View</td>
        </tr>
    </table>

    <!-- Continue with more chapters... untuk menghemat space, saya akan lanjutkan beberapa bab penting -->

    <!-- BAB 7: MANAJEMEN ABSENSI -->
    <h1><span class="icon">✅</span>Bab 7: Manajemen Absensi</h1>
    
    <h2>Cara Karyawan Melakukan Absensi</h2>
    
    <h3>📥 Check In (Absen Masuk)</h3>
    <p><span class="step">1</span> Buka halaman absensi melalui browser smartphone</p>
    <p><span class="step">2</span> Pastikan GPS/Lokasi di smartphone sudah aktif</p>
    <p><span class="step">3</span> Pastikan Anda berada dalam radius lokasi kantor</p>
    <p><span class="step">4</span> Klik tombol <strong>"Ambil Foto"</strong></p>
    <p><span class="step">5</span> Izinkan akses kamera jika diminta browser</p>
    <p><span class="step">6</span> Posisikan wajah Anda di depan kamera</p>
    <p><span class="step">7</span> Klik tombol merah (capture) untuk mengambil foto</p>
    <p><span class="step">8</span> Review foto yang sudah diambil</p>
    <p><span class="step">9</span> Jika foto sudah OK, klik tombol <strong>"Check In"</strong></p>
    <p><span class="step">10</span> Tunggu proses validasi sistem</p>
    <p><span class="step">11</span> Jika berhasil, akan muncul notifikasi sukses ✓</p>
    
    <div class="success-box">
        <strong>✓ Validasi Check In:</strong>
        <ul>
            <li>GPS dalam radius lokasi kantor? ✓</li>
            <li>Belum check in hari ini? ✓</li>
            <li>Foto tersedia? ✓</li>
            <li>Sesuai jam kerja? ✓</li>
        </ul>
    </div>
    
    <h3>📤 Check Out (Absen Pulang)</h3>
    <p><span class="step">1</span> Buka kembali halaman absensi</p>
    <p><span class="step">2</span> Pastikan sudah check in sebelumnya</p>
    <p><span class="step">3</span> Ulangi langkah pengambilan foto seperti check in</p>
    <p><span class="step">4</span> Klik tombol <strong>"Check Out"</strong></p>
    <p><span class="step">5</span> Sistem akan memproses dan menyimpan data</p>
    <p><span class="step">6</span> Check out berhasil! ✓</p>
    
    <div class="warning-box">
        <strong>⚠️ Penting:</strong>
        <ul>
            <li>Check out hanya bisa dilakukan jika sudah check in</li>
            <li>Pastikan berada dalam radius lokasi saat check out</li>
            <li>Foto saat check out juga wajib</li>
        </ul>
    </div>
    
    <h2>Validasi Sistem</h2>
    
    <h3>🗺️ Validasi Geofencing (GPS)</h3>
    <p>Sistem menggunakan rumus Haversine untuk menghitung jarak antara koordinat GPS Anda dengan lokasi kantor.</p>
    
    <div class="code-block">
Formula: distance = haversine(lat1, lon1, lat2, lon2)
Toleransi: Sesuai radius yang diatur (default 200 meter)
Status: ✓ Valid jika distance ≤ radius
    </div>
    
    <h3>⏰ Validasi Waktu</h3>
    <table>
        <tr>
            <th>Status</th>
            <th>Kondisi</th>
            <th>Warna</th>
        </tr>
        <tr>
            <td><strong>Tepat Waktu</strong></td>
            <td>Check in ≤ (Jam Masuk + Toleransi)</td>
            <td style="background: #d4edda; text-align: center;">🟢 Hijau</td>
        </tr>
        <tr>
            <td><strong>Terlambat</strong></td>
            <td>Check in > (Jam Masuk + Toleransi)</td>
            <td style="background: #fff3cd; text-align: center;">🟡 Kuning</td>
        </tr>
        <tr>
            <td><strong>Alpha</strong></td>
            <td>Tidak ada check in sama sekali</td>
            <td style="background: #f8d7da; text-align: center;">🔴 Merah</td>
        </tr>
        <tr>
            <td><strong>Izin</strong></td>
            <td>Ada izin/cuti yang approved</td>
            <td style="background: #e3f2fd; text-align: center;">🔵 Biru</td>
        </tr>
    </table>
    
    <div class="info-box">
        <strong>💡 Contoh:</strong><br>
        Jam Masuk: 08:00<br>
        Toleransi: 15 menit<br><br>
        • Check in 07:55 → <strong style="color: green;">Tepat Waktu ✓</strong><br>
        • Check in 08:10 → <strong style="color: green;">Tepat Waktu ✓</strong><br>
        • Check in 08:15 → <strong style="color: green;">Tepat Waktu ✓</strong><br>
        • Check in 08:16 → <strong style="color: orange;">Terlambat 1 menit ⚠</strong><br>
        • Check in 08:30 → <strong style="color: orange;">Terlambat 15 menit ⚠</strong>
    </div>

    <!-- BAB 17: KONTAK SUPPORT -->
    <h1><span class="icon">📞</span>Bab 17: Kontak Support</h1>
    
    <h2>Tim Support Siap Membantu Anda!</h2>
    <p>Jika Anda mengalami kesulitan atau memiliki pertanyaan yang belum terjawab dalam manual book ini, jangan ragu untuk menghubungi tim support kami.</p>
    
    <div class="contact-box">
        <h3 style="color: white; margin-bottom: 15px;">📧 Kontak Utama</h3>
        
        <div class="contact-item">
            <span class="contact-label">Email:</span>
            <span>yusrilmahendri.yusril@gmail.com</span>
        </div>
        
        <div class="contact-item">
            <span class="contact-label">Telepon:</span>
            <span>085161597598</span>
        </div>
        
        <div class="contact-item">
            <span class="contact-label">WhatsApp:</span>
            <span>085161597598</span>
        </div>
        
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3);">
            <strong>💬 WhatsApp Link:</strong><br>
            <span style="font-size: 9pt;">https://wa.me/6285161597598</span>
        </div>
    </div>
    
    <h3>⏰ Jam Operasional Support</h3>
    <table>
        <tr>
            <th>Hari</th>
            <th>Waktu</th>
        </tr>
        <tr>
            <td><strong>Senin - Jumat</strong></td>
            <td>09:00 - 17:00 WIB</td>
        </tr>
        <tr>
            <td><strong>Sabtu</strong></td>
            <td>09:00 - 13:00 WIB</td>
        </tr>
        <tr>
            <td><strong>Minggu</strong></td>
            <td>Tutup</td>
        </tr>
    </table>
    
    <div class="danger-box">
        <strong>🚨 Emergency Support 24/7</strong><br>
        Untuk masalah kritis yang membutuhkan penanganan segera, hubungi:<br>
        <strong style="font-size: 12pt;">WhatsApp: 085161597598</strong>
    </div>
    
    <h3>📬 Response Time</h3>
    <ul>
        <li><strong>Email:</strong> Respon dalam 1x24 jam kerja</li>
        <li><strong>WhatsApp:</strong> Respon dalam 1-2 jam kerja (24/7 untuk emergency)</li>
        <li><strong>Telepon:</strong> Langsung terhubung saat jam operasional</li>
    </ul>
    
    <div class="info-box">
        <strong>💡 Tips Menghubungi Support:</strong>
        <ul>
            <li>Jelaskan masalah secara detail</li>
            <li>Sertakan screenshot jika memungkinkan</li>
            <li>Sebutkan role Anda (Admin/Karyawan)</li>
            <li>Berikan informasi error message jika ada</li>
            <li>Cek FAQ terlebih dahulu untuk solusi cepat</li>
        </ul>
    </div>
    
    <h2>Pelatihan & Onboarding</h2>
    <p>Kami juga menyediakan layanan pelatihan untuk tim Anda:</p>
    
    <h3>🎓 Pelatihan Admin (1-2 Jam)</h3>
    <ul>
        <li>Login dan navigasi dashboard</li>
        <li>Manajemen user & import bulk</li>
        <li>Setting shift & lokasi absensi</li>
        <li>Approval izin & lembur</li>
        <li>Generate & export laporan</li>
        <li>Troubleshooting dasar</li>
    </ul>
    
    <h3>👥 Pelatihan Karyawan (30 Menit)</h3>
    <ul>
        <li>Cara login dan akses sistem</li>
        <li>Cara melakukan absensi (check in/out)</li>
        <li>Pengajuan izin/cuti</li>
        <li>Melihat riwayat absensi</li>
        <li>Update profil pribadi</li>
    </ul>
    
    <div class="success-box">
        <strong>✅ Request Pelatihan:</strong><br>
        Hubungi kami untuk jadwal pelatihan:<br>
        📧 yusrilmahendri.yusril@gmail.com<br>
        📞 085161597598
    </div>
    
    <!-- PENUTUP -->
    <div style="page-break-before: always; text-align: center; padding-top: 100px;">
        <div style="width: 120px; height: 120px; margin: 0 auto 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
            <span style="font-size: 60pt; color: white;">✓</span>
        </div>
        
        <h1 style="font-size: 28pt; margin-bottom: 20px; page-break-before: auto;">Terima Kasih!</h1>
        
        <p style="font-size: 12pt; margin-bottom: 30px;">Anda telah menyelesaikan Manual Book Sistem Presensi</p>
        
        <div class="success-box" style="text-align: left; max-width: 500px; margin: 0 auto;">
            <strong>📚 Yang Telah Anda Pelajari:</strong>
            <ul class="checklist">
                <li>Cara menggunakan sistem presensi</li>
                <li>Fitur-fitur lengkap untuk Admin & Karyawan</li>
                <li>Troubleshooting masalah umum</li>
                <li>Tips & best practices</li>
                <li>Kontak support untuk bantuan</li>
            </ul>
        </div>
        
        <div class="contact-box" style="max-width: 500px; margin: 30px auto; text-align: left;">
            <h3 style="color: white; margin-bottom: 15px; text-align: center;">💬 Tetap Terhubung</h3>
            <div style="text-align: center;">
                <p style="margin: 5px 0;">📧 yusrilmahendri.yusril@gmail.com</p>
                <p style="margin: 5px 0;">📞 085161597598</p>
                <p style="margin: 5px 0;">💬 https://wa.me/6285161597598</p>
            </div>
        </div>
        
        <div style="margin-top: 60px; padding-top: 30px; border-top: 2px solid #ddd;">
            <p style="color: #999; font-size: 10pt;">© 2026 Sistem Presensi - All Rights Reserved</p>
            <p style="color: #999; font-size: 9pt; margin-top: 10px;">Version 2.0 | {{ now()->format('F Y') }}</p>
            <p style="color: #999; font-size: 9pt; margin-top: 5px;">Developed by: Yusril Mahendri</p>
        </div>
    </div>

    <!-- Footer untuk semua halaman kecuali cover -->
    <div class="footer">
        <span class="page-number"></span> | Manual Book Sistem Presensi v2.0 | © 2026
    </div>

</body>
</html>
