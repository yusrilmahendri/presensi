# Panduan Instalasi Sistem Presensi

## Prasyarat

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (opsional, jika diperlukan)

## Langkah Instalasi

### 1. Install Dependencies

```bash
cd presensi
composer install
```

### 2. Setup Environment

Copy file `.env.example` ke `.env` (jika belum ada, buat manual):

```bash
cp .env.example .env
```

Atau buat file `.env` baru dengan konten berikut:

```env
APP_NAME="Sistem Presensi"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presensi
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presensi
DB_USERNAME=root
DB_PASSWORD=your_password
```

Buat database baru:

```sql
CREATE DATABASE presensi;
```

### 5. Jalankan Migrasi

```bash
php artisan migrate
```

### 6. Jalankan Seeder (Data Sample)

```bash
php artisan db:seed
```

Seeder akan membuat:
- 3 shift (Pagi: 07:00-15:00, Sore: 15:00-23:00, Malam: 23:00-07:00)
- 1 lokasi absen default (Kantor Pusat - Jakarta)
- 1 admin user
- 10 sample users

### 7. Buat Symbolic Link untuk Storage

```bash
php artisan storage:link
```

Ini diperlukan untuk mengakses foto-foto absensi.

### 8. Set Permissions (jika di Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 9. Jalankan Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: http://localhost:8000

## Akses Aplikasi

### Halaman Presensi
- URL: http://localhost:8000/presensi
- Login dengan email dan password karyawan

### Admin Panel
- URL: http://localhost:8000/admin
- Email: `admin@presensi.com`
- Password: `password`

### Kredensial Sample Users
- Email: `karyawan1@presensi.com` sampai `karyawan10@presensi.com`
- Password: `password`

## Konfigurasi Lokasi Absen

Setelah login ke admin panel:

1. Buka menu **"Lokasi Absen"**
2. Edit lokasi default atau buat lokasi baru
3. Masukkan koordinat latitude dan longitude
4. Atur radius (default: 5 meter)

**Cara mendapatkan koordinat:**
- Buka Google Maps
- Klik kanan pada lokasi yang diinginkan
- Klik pada koordinat untuk copy
- Paste ke form lokasi absen

## Catatan Penting

1. **Kamera**: Pastikan browser memiliki izin akses kamera (HTTPS atau localhost)
2. **Lokasi**: Pastikan browser memiliki izin akses lokasi GPS
3. **Radius**: Absen hanya bisa dilakukan dalam radius yang ditentukan (default 5 meter)
4. **Shift**: Absen hanya bisa dilakukan pada jam shift yang sesuai

## Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
```

### Error: Storage link tidak bekerja
```bash
php artisan storage:link --force
```

### Error: Permission denied
```bash
chmod -R 775 storage bootstrap/cache
```

### Error: Migration failed
Pastikan database sudah dibuat dan konfigurasi di `.env` benar.

