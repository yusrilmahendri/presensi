# 📧 PANDUAN SETUP EMAIL NOTIFIKASI DI PRODUCTION

## ❌ Kenapa Email Tidak Terkirim di Production?

Ada **3 hal utama** yang harus dikonfigurasi agar email notifikasi berfungsi di production:

---

## 🔧 1. KONFIGURASI MAIL DI .ENV

Email tidak akan terkirim jika masih menggunakan `MAIL_MAILER=log` (default untuk development).

### ✅ Setup untuk Production

Edit file `.env` di server production:

```env
# ❌ JANGAN gunakan ini di production
# MAIL_MAILER=log

# ✅ Gunakan SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="PioneerHadir"
```

### 📝 Pilihan SMTP Provider:

#### **Option 1: Gmail (Gratis)**
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx  # App Password (bukan password Gmail)
MAIL_ENCRYPTION=tls
```

**Cara dapat App Password Gmail:**
1. Login ke Google Account → Security
2. Enable "2-Step Verification"
3. Buka "App Passwords" → Generate password untuk "Mail"
4. Copy password 16 digit → paste ke MAIL_PASSWORD

#### **Option 2: Mailtrap (Development/Testing)**
```env
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
```

#### **Option 3: SendGrid (Production Recommended)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

#### **Option 4: Mailgun**
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=key-your-mailgun-api-key
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

---

## ⚙️ 2. JALANKAN QUEUE WORKER

Notifikasi email menggunakan **queue system** (background job) agar tidak memblokir response ke user.

### ❌ Masalah:
Jika queue worker **tidak berjalan**, email akan masuk ke database tabel `jobs` tapi **tidak pernah dikirim**.

### ✅ Solusi:

#### **A. Manual (Testing)**
```bash
php artisan queue:work
```

#### **B. Production dengan Supervisor (Recommended)**

**1. Install Supervisor:**
```bash
# Ubuntu/Debian
sudo apt-get install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

**2. Buat file konfigurasi:**
```bash
sudo nano /etc/supervisor/conf.d/presensi-worker.conf
```

**3. Isi konfigurasi:**
```ini
[program:presensi-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/presensi/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/presensi/storage/logs/worker.log
stopwaitsecs=3600
```

**4. Jalankan supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start presensi-worker:*
```

**5. Cek status:**
```bash
sudo supervisorctl status presensi-worker:*
```

#### **C. Production dengan Systemd**

**1. Buat service file:**
```bash
sudo nano /etc/systemd/system/presensi-queue.service
```

**2. Isi:**
```ini
[Unit]
Description=Presensi Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/presensi/artisan queue:work database --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

**3. Enable & start:**
```bash
sudo systemctl enable presensi-queue
sudo systemctl start presensi-queue
sudo systemctl status presensi-queue
```

---

## 🗄️ 3. PASTIKAN DATABASE QUEUE TABLE ADA

### Cek apakah tabel `jobs` sudah ada:
```bash
php artisan queue:table
php artisan migrate
```

### Cek isi queue:
```bash
php artisan queue:monitor
```

### Clear failed jobs (jika ada):
```bash
php artisan queue:flush
php artisan queue:restart
```

---

## 🧪 TESTING EMAIL

### 1. Test Koneksi SMTP
```bash
php artisan tinker
```

Jalankan:
```php
Mail::raw('Test email dari PioneerHadir', function($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

### 2. Test Notifikasi Lengkap
```bash
php artisan tinker
```

Jalankan:
```php
$user = \App\Models\User::first();
$user->notify(new \App\Notifications\LateCheckInNotification(
    \App\Models\Attendance::first(),
    30
));
```

### 3. Cek Log
```bash
tail -f storage/logs/laravel.log
```

---

## 🔍 TROUBLESHOOTING

### ❌ Email tidak terkirim, tidak ada error

**Kemungkinan:**
- Queue worker tidak berjalan
- MAIL_MAILER masih = `log`

**Solusi:**
```bash
# 1. Cek queue worker
ps aux | grep queue:work

# 2. Cek .env
grep MAIL_ .env

# 3. Clear config cache
php artisan config:clear
php artisan config:cache

# 4. Restart queue worker
php artisan queue:restart
php artisan queue:work
```

### ❌ Error: Authentication failed

**Kemungkinan:**
- Username/password salah
- App Password belum dibuat (Gmail)
- 2FA belum aktif (Gmail)

**Solusi:**
1. Gmail: Gunakan **App Password**, bukan password biasa
2. Pastikan 2FA aktif di Google Account
3. Generate App Password baru

### ❌ Error: Connection timeout

**Kemungkinan:**
- Port 587 diblokir firewall
- MAIL_HOST salah

**Solusi:**
```bash
# Test koneksi
telnet smtp.gmail.com 587

# Jika timeout, coba port 465 (SSL)
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

### ❌ Email masuk spam

**Solusi:**
1. Setup SPF record di DNS domain
2. Setup DKIM di email provider
3. Gunakan email domain sendiri (bukan Gmail)
4. Gunakan service seperti SendGrid/Mailgun

---

## 📋 CHECKLIST PRODUCTION

- [ ] **1. .ENV Setup**
  - [ ] MAIL_MAILER=smtp (bukan log)
  - [ ] MAIL_HOST terisi
  - [ ] MAIL_USERNAME terisi
  - [ ] MAIL_PASSWORD terisi (App Password untuk Gmail)
  - [ ] MAIL_FROM_ADDRESS terisi
  - [ ] MAIL_FROM_NAME terisi

- [ ] **2. Queue Setup**
  - [ ] QUEUE_CONNECTION=database
  - [ ] php artisan queue:table (sudah migrate)
  - [ ] Queue worker berjalan (supervisor/systemd)

- [ ] **3. Testing**
  - [ ] Test kirim email manual via tinker
  - [ ] Test notifikasi keterlambatan
  - [ ] Cek email masuk (inbox/spam)
  - [ ] Cek log di storage/logs/laravel.log

- [ ] **4. Monitoring**
  - [ ] Setup supervisor/systemd untuk auto-restart worker
  - [ ] Monitor queue dengan `php artisan queue:monitor`
  - [ ] Log worker output ke file terpisah

---

## 🚀 QUICK FIX - Email Tidak Terkirim

```bash
# 1. Edit .env
nano .env

# Ubah:
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="PioneerHadir"

# 2. Clear cache
php artisan config:clear
php artisan config:cache

# 3. Jalankan queue worker
php artisan queue:work

# 4. Test email
php artisan tinker
# Lalu jalankan:
Mail::raw('Test', function($m) { $m->to('your-email@gmail.com')->subject('Test'); });
```

---

## 📞 SUPPORT

Jika masih bermasalah, cek:
1. **Log Laravel:** `storage/logs/laravel.log`
2. **Queue jobs:** `SELECT * FROM jobs;` di database
3. **Failed jobs:** `SELECT * FROM failed_jobs;` di database
4. **Worker status:** `sudo supervisorctl status` atau `systemctl status presensi-queue`

---

**Update:** 26 Desember 2025  
**Status:** Production Ready ✅
