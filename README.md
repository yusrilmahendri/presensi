# Sistem Presensi

Aplikasi presensi berbasis web menggunakan Laravel, Filament untuk admin panel, Bootstrap untuk UI, dan SweetAlert untuk notifikasi.

## Fitur

- ✅ **Kamera Depan Otomatis** - Menggunakan front camera untuk foto selfie saat absen
- ✅ **Lokasi & Timestamp** - Menyimpan lokasi GPS dan waktu absen
- ✅ **Validasi Radius 5 Meter** - Hanya bisa absen jika berada dalam radius 5 meter dari titik absen
- ✅ **Multi User** - Setiap akun memiliki login terpisah
- ✅ **Admin Panel** - Admin dapat memantau semua data absensi melalui Filament
- ✅ **Sistem Shift** - Terdapat 3 shift:
  - **Pagi**: 07:00 - 15:00
  - **Sore**: 15:00 - 23:00
  - **Malam**: 23:00 - 07:00
- ✅ **Validasi Shift** - Absen hanya dapat dilakukan pada jam shift yang sesuai

## Teknologi

- **Laravel 12** - Framework PHP
- **Filament 3** - Admin panel builder
- **Bootstrap 5** - CSS Framework
- **SweetAlert2** - Library untuk notifikasi
- **MySQL/PostgreSQL** - Database

## Instalasi

### 1. Install Dependencies

```bash
composer install
```

### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presensi
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Jalankan Migrasi dan Seeder

```bash
php artisan migrate
php artisan db:seed
```

Seeder akan membuat:
- 3 shift (Pagi, Sore, Malam)
- 1 lokasi absen default (Kantor Pusat - Jakarta)
- 1 admin user
- 10 sample users

### 5. Buat Symbolic Link untuk Storage

```bash
php artisan storage:link
```

Ini diperlukan untuk mengakses foto-foto absensi yang disimpan.

### 6. Jalankan Development Server

```bash
php artisan serve
```

### 7. Akses Aplikasi

- **Halaman Presensi**: http://localhost:8000/presensi
- **Admin Panel**: http://localhost:8000/admin

#### Kredensial Login

**Admin:**
- Email: `admin@presensi.com`
- Password: `password`

**Sample Users:**
- Email: `karyawan1@presensi.com` sampai `karyawan10@presensi.com`
- Password: `password`

## Konfigurasi Lokasi Absen

Setelah login ke admin panel, Anda perlu mengatur lokasi absen sesuai dengan koordinat tempat kerja Anda:

1. Login ke admin panel
2. Buka menu "Lokasi Absen"
3. Edit lokasi default atau buat lokasi baru
4. Masukkan koordinat latitude dan longitude lokasi Anda
5. Radius default adalah 5 meter

**Cara mendapatkan koordinat:**
- Buka Google Maps
- Klik kanan pada lokasi yang diinginkan
- Pilih koordinat (akan ter-copy)
- Paste ke form lokasi absen

## Cara Menggunakan

### Untuk Karyawan

1. Buka halaman presensi: http://localhost:8000/presensi
2. Masukkan email dan password
3. Berikan izin akses kamera dan lokasi saat diminta browser
4. Ambil foto selfie dengan menekan tombol capture
5. Pilih tipe absen (Check In atau Check Out)
6. Pastikan lokasi sudah terdeteksi (status menunjukkan "Lokasi berhasil didapatkan")
7. Pastikan Anda berada dalam radius 5 meter dari titik absen
8. Klik "Submit Presensi"

### Untuk Admin

1. Login ke admin panel: http://localhost:8000/admin
2. Anda dapat melihat dan mengelola:
   - **Karyawan**: Tambah, edit, hapus karyawan
   - **Shift**: Kelola shift kerja
   - **Lokasi Absen**: Kelola titik-titik lokasi absen
   - **Data Absensi**: Lihat semua data absensi dengan filter

## Struktur Database

### Tabel `users`
- id
- name
- email
- password
- shift_id (foreign key)
- timestamps

### Tabel `shifts`
- id
- name (Pagi, Sore, Malam)
- start_time
- end_time
- description
- timestamps

### Tabel `attendance_locations`
- id
- name
- latitude
- longitude
- radius (dalam meter, default 5)
- description
- timestamps

### Tabel `attendances`
- id
- user_id (foreign key)
- shift_id (foreign key)
- attendance_location_id (foreign key)
- type (check_in, check_out)
- attendance_time
- latitude
- longitude
- photo (path ke foto)
- notes
- timestamps

## Catatan Penting

1. **Kamera**: Pastikan browser memiliki akses ke kamera dan menggunakan HTTPS (atau localhost) untuk mengakses kamera
2. **Lokasi**: Pastikan browser memiliki izin akses lokasi
3. **Radius**: Absen hanya dapat dilakukan jika berada dalam radius yang ditentukan (default 5 meter)
4. **Shift**: Absen hanya dapat dilakukan pada jam shift yang sesuai dengan shift karyawan
5. **Check Out**: Tidak dapat melakukan check out jika belum melakukan check in pada hari yang sama

## Troubleshooting

### Kamera tidak muncul
- Pastikan browser memberikan izin akses kamera
- Pastikan menggunakan HTTPS atau localhost
- Refresh halaman dan coba lagi

### Lokasi tidak terdeteksi
- Pastikan browser memberikan izin akses lokasi
- Pastikan GPS aktif (untuk mobile device)
- Cek koneksi internet

### Error "Anda berada di luar radius"
- Pastikan Anda berada dalam radius 5 meter dari titik absen yang sudah ditentukan
- Admin perlu memastikan koordinat lokasi absen sudah benar
- Gunakan aplikasi GPS untuk memastikan posisi Anda

## License

MIT License

# presensi
