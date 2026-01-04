# 🚀 Konfigurasi Server Production - Notifikasi Email

## ✅ Status Konfigurasi Email

Berdasarkan test email yang berhasil:
- ✅ **MAIL_MAILER**: smtp (Hostinger)
- ✅ **MAIL_HOST**: smtp.hostinger.com
- ✅ **MAIL_PORT**: 587
- ✅ **MAIL_USERNAME**: noreply@hadir.pioneersolve.id
- ✅ **MAIL_ENCRYPTION**: tls
- ✅ **Test email**: BERHASIL masuk ke Gmail ✓

---

## ⚠️ 2 Komponen Wajib untuk Notifikasi Otomatis

### 1️⃣ **QUEUE WORKER** (Wajib Berjalan)

**Fungsi**: Memproses dan mengirim email dari database queue

**Status Saat Ini**: ❌ **BELUM BERJALAN**

**Cara Cek di Server:**
```bash
# SSH ke server
ssh user@hadir.pioneersolve.id

# Cek apakah ada proses queue worker
ps aux | grep "queue:work"

# Jika tidak ada output = queue worker TIDAK berjalan
```

**Cara Menjalankan:**

#### Opsi A: Manual (untuk testing sementara)
```bash
cd /path/to/your/project
php artisan queue:work --tries=3 --timeout=90
```
⚠️ **Kelemahan**: Akan berhenti jika terminal ditutup atau SSH disconnect

#### Opsi B: Supervisor (RECOMMENDED untuk production)

**Step 1: Install Supervisor**
```bash
sudo apt-get update
sudo apt-get install supervisor -y
```

**Step 2: Buat Config File**
```bash
sudo nano /etc/supervisor/conf.d/hadir-worker.conf
```

**Isi file:**
```ini
[program:hadir-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/u462762732/domains/hadir.pioneersolve.id/public_html/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=u462762732
numprocs=2
redirect_stderr=true
stdout_logfile=/home/u462762732/domains/hadir.pioneersolve.id/storage/logs/worker.log
stopwaitsecs=3600
```

⚠️ **Sesuaikan path**:
- `/home/u462762732/domains/hadir.pioneersolve.id/public_html` → path ke project Anda
- `u462762732` → username hosting Anda

**Step 3: Reload Supervisor**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start hadir-worker:*
```

**Step 4: Cek Status**
```bash
sudo supervisorctl status hadir-worker:*
```

Output yang benar:
```
hadir-worker:hadir-worker_00   RUNNING   pid 12345, uptime 0:01:23
hadir-worker:hadir-worker_01   RUNNING   pid 12346, uptime 0:01:23
```

---

### 2️⃣ **SCHEDULER (Cron Job)** (Wajib untuk Reminder Otomatis)

**Fungsi**: Menjalankan task terjadwal (reminder checkout, weekly summary, dll)

**Status Saat Ini**: ❌ **BELUM DIKONFIGURASI**

**Cara Setup:**

**Step 1: Buka crontab**
```bash
crontab -e
```

**Step 2: Tambahkan baris ini:**
```bash
* * * * * cd /home/u462762732/domains/hadir.pioneersolve.id/public_html && php artisan schedule:run >> /dev/null 2>&1
```

⚠️ **Sesuaikan path** ke project Anda

**Step 3: Simpan dan keluar**
- Jika menggunakan nano: `Ctrl+X`, lalu `Y`, lalu `Enter`
- Jika menggunakan vi: tekan `ESC`, ketik `:wq`, tekan `Enter`

**Step 4: Verifikasi cron sudah terdaftar**
```bash
crontab -l
```

---

## 🧪 Cara Testing Notifikasi

### Test 1: Queue Worker Berfungsi

**Step 1: Cek jobs di database**
```bash
php artisan tinker
```

Lalu ketik:
```php
\DB::table('jobs')->count();
// Output: 0 = queue kosong
// Output: > 0 = ada jobs pending
```

**Step 2: Buat pengajuan izin lewat aplikasi**
1. Login sebagai karyawan
2. Buat pengajuan izin baru
3. Submit

**Step 3: Cek apakah email masuk**
- Jika **queue worker berjalan** → email masuk dalam 1-5 detik ✅
- Jika **queue worker TIDAK berjalan** → email tidak masuk ❌

**Step 4: Cek log jika error**
```bash
tail -f /path/to/storage/logs/laravel.log
tail -f /path/to/storage/logs/worker.log  # jika pakai supervisor
```

---

### Test 2: Scheduler Berfungsi

**Manual trigger scheduler:**
```bash
php artisan schedule:run
```

Output yang benar:
```
Running scheduled command: 'artisan' attendance:send-upcoming-checkin-reminder
Running scheduled command: 'artisan' attendance:send-checkout-reminder
...
```

**Auto test (tunggu di waktu yang tepat):**
- 15:00 - 20:00 → Late checkout reminder (setiap 15 menit)
- 08:30 → Check-in reminder
- Senin 08:00 → Weekly summary

---

## 📊 Monitoring & Troubleshooting

### Cek Status Queue

```bash
# Cek jumlah jobs pending
php artisan queue:monitor

# Cek failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Cek Log

```bash
# Log Laravel
tail -f storage/logs/laravel.log

# Log Worker (jika pakai supervisor)
tail -f storage/logs/worker.log

# Log Cron
grep CRON /var/log/syslog | tail -20
```

### Common Issues

#### ❌ Email tidak terkirim setelah pengajuan izin

**Penyebab**: Queue worker tidak berjalan

**Solusi**:
```bash
# Cek apakah worker running
ps aux | grep queue:work

# Jika tidak ada, start supervisor
sudo supervisorctl start hadir-worker:*

# Atau jalankan manual
php artisan queue:work
```

#### ❌ Reminder tidak terkirim otomatis

**Penyebab**: Cron job tidak terkonfigurasi

**Solusi**:
```bash
# Cek crontab
crontab -l

# Jika kosong, tambahkan cron job (lihat section Scheduler)
crontab -e
```

#### ❌ Jobs masuk ke failed_jobs table

**Penyebab**: Error saat proses email

**Solusi**:
```bash
# Cek error di failed jobs
php artisan queue:failed

# Lihat detail error
SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 1;

# Retry
php artisan queue:retry all
```

---

## ✅ Checklist Deployment Production

- [x] File .env sudah benar (MAIL_MAILER=smtp, dll)
- [ ] **Queue worker berjalan** (Supervisor atau manual)
- [ ] **Cron job terkonfigurasi** untuk scheduler
- [ ] Test pengajuan izin → email masuk ✅
- [ ] Test reminder checkout → email masuk ✅
- [ ] Monitor log error
- [ ] Setup monitoring uptime supervisor

---

## 🔐 Security Notes

1. **File Permission**
```bash
# Pastikan Laravel bisa write ke storage
chmod -R 775 storage bootstrap/cache
chown -R u462762732:u462762732 storage bootstrap/cache
```

2. **Log Rotation**
```bash
# Setup logrotate untuk mencegah log file terlalu besar
sudo nano /etc/logrotate.d/laravel
```

Isi:
```
/path/to/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    missingok
    create 0644 u462762732 u462762732
}
```

---

## 📞 Quick Commands Cheat Sheet

```bash
# Start queue worker (manual)
php artisan queue:work --tries=3

# Cek supervisor status
sudo supervisorctl status hadir-worker:*

# Restart supervisor workers
sudo supervisorctl restart hadir-worker:*

# Test scheduler manual
php artisan schedule:run

# Cek jobs pending
php artisan queue:monitor

# Cek failed jobs
php artisan queue:failed

# Clear cache
php artisan optimize:clear

# View logs
tail -f storage/logs/laravel.log
```

---

## 🎯 Kesimpulan

### Yang Sudah Berfungsi:
✅ Konfigurasi email SMTP Hostinger  
✅ Test email manual berhasil terkirim  
✅ Aplikasi Laravel berjalan  

### Yang Perlu Dilakukan di Server:
1. ⚠️ **Setup Queue Worker** (Supervisor - wajib!)
2. ⚠️ **Setup Cron Job** (Scheduler - wajib!)
3. ✅ **Test notifikasi** setelah kedua komponen berjalan

### Estimasi Waktu Setup:
- Queue Worker (Supervisor): **10-15 menit**
- Cron Job: **2-5 menit**
- Testing: **5-10 menit**

**Total**: ~30 menit untuk setup lengkap

---

**Prioritas**: TINGGI  
**Status**: Menunggu konfigurasi di server production  
**Next Step**: Setup Supervisor untuk queue worker

---

**Dibuat**: 4 Januari 2026  
**Update**: -  
**Versi**: 1.0
