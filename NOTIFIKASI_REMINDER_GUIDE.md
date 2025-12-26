# 📧 PANDUAN NOTIFIKASI REMINDER CHECK-IN & CHECK-OUT

## 🔔 Sistem Notifikasi Otomatis untuk Absensi

Sistem ini mengirim **email reminder otomatis** kepada karyawan untuk membantu mereka ingat melakukan check-in dan check-out tepat waktu.

---

## 📋 Jenis Notifikasi Reminder

| No | Notifikasi | Waktu Kirim | Penerima | Email? | Database? |
|----|-----------|-------------|----------|--------|-----------|
| 1️⃣ | **Check-in Reminder (Upcoming)** | 5-10 menit sebelum shift | Karyawan yang belum check-in | ✅ | ✅ |
| 2️⃣ | **Check-in Reminder (Daily)** | 15 menit setelah shift dimulai | Karyawan yang belum check-in | ✅ | ✅ |
| 3️⃣ | **Check-out Reminder** | 30 menit sebelum shift berakhir | Karyawan yang sudah check-in tapi belum check-out | ✅ | ✅ |

---

## 1️⃣ REMINDER CHECK-IN (UPCOMING)

### 📨 Kapan Terkirim?
**5-10 menit sebelum shift dimulai**

Contoh: Shift mulai jam 08:00 → Email dikirim jam 07:50 - 07:55

### 📄 Template Email

```
From: PioneerHadir System <system@company.com>
To: karyawan@gmail.com
Subject: Reminder: Waktu Absen Masuk Segera Tiba

Halo Budi Santoso,

⏰ Waktu absen masuk Anda akan dimulai dalam 8 menit!

Shift Anda dimulai pukul 08:00
Pastikan Anda siap untuk melakukan check-in tepat waktu.

Terima kasih atas kedisiplinan Anda!

[Absen Sekarang]
```

### 🔧 Cara Kerja

**File Command:** `app/Console/Commands/SendUpcomingCheckInReminder.php`

**File Notifikasi:** `app/Notifications/CheckInReminderNotification.php`

**Scheduler:** Berjalan setiap 5 menit (di dalam window 5-10 menit sebelum shift)

```php
// Di app/Console/Kernel.php
$schedule->command('attendance:send-upcoming-checkin-reminder')
    ->everyFiveMinutes();
```

**Logika:**
1. Cek semua karyawan dengan shift aktif
2. Hitung selisih waktu sekarang dengan waktu mulai shift
3. Jika 5-10 menit sebelum shift dimulai
4. Dan belum check-in hari ini
5. Kirim email reminder

---

## 2️⃣ REMINDER CHECK-IN (DAILY)

### 📨 Kapan Terkirim?
**15 menit setelah shift dimulai** (untuk yang terlambat)

Contoh: Shift mulai jam 08:00 → Email dikirim jam 08:15

### 📄 Template Email

```
From: PioneerHadir System <system@company.com>
To: karyawan@gmail.com
Subject: Reminder: Waktu Absen Masuk Segera Tiba

Halo Budi Santoso,

⏰ Anda belum melakukan check-in hari ini.

Shift Anda dimulai pukul 08:00
Segera lakukan check-in untuk menghindari keterlambatan.

Terima kasih atas kedisiplinan Anda!

[Absen Sekarang]
```

### 🔧 Cara Kerja

**File Command:** `app/Console/Commands/SendDailyCheckInReminder.php`

**File Notifikasi:** `app/Notifications/CheckInReminderNotification.php`

**Scheduler:** Berjalan setiap jam (cek apakah sudah 15 menit setelah shift)

```php
// Di app/Console/Kernel.php
$schedule->command('attendance:send-checkin-reminder')
    ->hourly();
```

**Logika:**
1. Cek semua karyawan dengan shift aktif
2. Hitung apakah sudah 15 menit setelah shift dimulai
3. Jika sudah dan belum check-in
4. Kirim email reminder

---

## 3️⃣ REMINDER CHECK-OUT

### 📨 Kapan Terkirim?
**30 menit sebelum shift berakhir**

Contoh: Shift berakhir jam 17:00 → Email dikirim jam 16:30

### 📄 Template Email

```
From: PioneerHadir System <system@company.com>
To: karyawan@gmail.com
Subject: ⏰ Reminder: Waktu Check-out Segera Tiba

Halo Budi Santoso,

🕐 Shift Anda akan berakhir dalam 30 menit!

Waktu berakhir shift: 17:00

📝 Jangan lupa untuk:
• Menyelesaikan pekerjaan yang sedang berjalan
• Melakukan check-out sebelum meninggalkan area kerja
• Pastikan semua tugas sudah terdokumentasi

Terima kasih atas kerja keras Anda hari ini! 💪

[Check-out Sekarang]
```

### 🔧 Cara Kerja

**File Command:** `app/Console/Commands/SendCheckOutReminder.php`

**File Notifikasi:** `app/Notifications/CheckOutReminderNotification.php`

**Scheduler:** Berjalan setiap 5 menit (cek apakah tepat 30 menit sebelum shift berakhir)

```php
// Di app/Console/Kernel.php
$schedule->command('attendance:send-checkout-reminder')
    ->everyFiveMinutes();
```

**Logika:**
1. Cek semua karyawan dengan shift aktif
2. Hitung apakah sekarang tepat 30 menit sebelum shift berakhir (±2 menit toleransi)
3. Jika sudah check-in tapi belum check-out
4. Kirim email reminder

---

## ⏰ Jadwal Scheduler

Di file `app/Console/Kernel.php`, tambahkan:

```php
protected function schedule(Schedule $schedule): void
{
    // Reminder check-in 5-10 menit sebelum shift
    $schedule->command('attendance:send-upcoming-checkin-reminder')
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->runInBackground();

    // Reminder check-in untuk yang terlambat (15 menit setelah shift)
    $schedule->command('attendance:send-checkin-reminder')
        ->hourly()
        ->withoutOverlapping()
        ->runInBackground();

    // Reminder check-out 30 menit sebelum shift berakhir
    $schedule->command('attendance:send-checkout-reminder')
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->runInBackground();
}
```

### 🚀 Aktivasi Scheduler di Server

#### Production dengan Cron

Tambahkan ke crontab server:

```bash
crontab -e
```

Tambahkan baris ini:

```bash
* * * * * cd /var/www/presensi && php artisan schedule:run >> /dev/null 2>&1
```

#### Development (Local)

Jalankan manual:

```bash
php artisan schedule:work
```

---

## 🎯 Timeline Notifikasi (Contoh)

**Shift: 08:00 - 17:00**

```
07:50  →  📧 Email 1: "Check-in dalam 10 menit" (upcoming)
          Karyawan: Belum check-in

08:00  →  Shift dimulai

08:15  →  📧 Email 2: "Anda belum check-in" (daily reminder)
          Karyawan: Masih belum check-in

08:30  →  ✅ Karyawan check-in

16:30  →  📧 Email 3: "Check-out dalam 30 menit"
          Karyawan: Sudah check-in, belum check-out

17:00  →  Shift berakhir

17:10  →  ✅ Karyawan check-out
```

---

## 🧪 Testing Notifikasi Reminder

### Test 1: Check-in Reminder (Upcoming)

```bash
php artisan tinker
```

```php
// Simulasi karyawan dengan shift akan dimulai 8 menit lagi
$user = \App\Models\User::where('role', 'employee')->first();

// Kirim notifikasi upcoming
$user->notify(new \App\Notifications\CheckInReminderNotification(
    '08:00',
    8  // 8 menit lagi
));

// Cek apakah masuk queue
\DB::table('jobs')->latest()->first();
```

### Test 2: Check-in Reminder (Daily)

```php
$user = \App\Models\User::where('role', 'employee')->first();

// Kirim notifikasi daily (belum check-in)
$user->notify(new \App\Notifications\CheckInReminderNotification(
    '08:00',
    0  // Sudah lewat waktu
));
```

### Test 3: Check-out Reminder

```php
$user = \App\Models\User::where('role', 'employee')->first();

// Kirim notifikasi check-out
$user->notify(new \App\Notifications\CheckOutReminderNotification(
    '17:00',
    30  // 30 menit lagi
));
```

### Test Command Manual

```bash
# Test upcoming check-in reminder
php artisan attendance:send-upcoming-checkin-reminder

# Test daily check-in reminder
php artisan attendance:send-checkin-reminder

# Test check-out reminder
php artisan attendance:send-checkout-reminder
```

---

## 📊 Monitoring Reminder

### Cek Berapa Reminder Terkirim Hari Ini

```sql
-- Cek notifikasi check-in reminder
SELECT COUNT(*) as total, 
       JSON_EXTRACT(data, '$.type') as type
FROM notifications
WHERE DATE(created_at) = CURDATE()
  AND JSON_EXTRACT(data, '$.type') LIKE '%reminder%'
GROUP BY type;
```

### Cek Karyawan yang Menerima Reminder

```sql
SELECT 
    u.name,
    u.email,
    JSON_EXTRACT(n.data, '$.title') as notification_title,
    JSON_EXTRACT(n.data, '$.body') as message,
    n.created_at
FROM notifications n
JOIN users u ON JSON_EXTRACT(n.notifiable_id, '$') = u.id
WHERE DATE(n.created_at) = CURDATE()
  AND JSON_EXTRACT(n.data, '$.type') LIKE '%reminder%'
ORDER BY n.created_at DESC;
```

### Cek Queue Jobs

```sql
-- Email reminder yang sedang antri
SELECT * FROM jobs 
WHERE payload LIKE '%CheckInReminderNotification%'
   OR payload LIKE '%CheckOutReminderNotification%'
ORDER BY created_at DESC;
```

---

## 🔍 Troubleshooting

### ❌ Reminder Tidak Terkirim

**Cek 1: Scheduler Running?**
```bash
# Cek crontab
crontab -l

# Test scheduler
php artisan schedule:run

# Atau jalankan manual
php artisan schedule:work
```

**Cek 2: Karyawan Punya Shift?**
```sql
SELECT id, name, email, shift_id 
FROM users 
WHERE role = 'employee' AND shift_id IS NULL;
```
✅ Pastikan semua karyawan punya shift

**Cek 3: Queue Worker Running?**
```bash
ps aux | grep "queue:work"

# Jika tidak ada, jalankan:
php artisan queue:work
```

**Cek 4: Command Berjalan dengan Benar?**
```bash
# Test manual
php artisan attendance:send-upcoming-checkin-reminder

# Lihat output, harusnya ada:
# ✓ Reminder sent to Budi Santoso (8 minutes before shift)
```

### ❌ Email Terkirim Terlalu Banyak

**Masalah:** Command berjalan berkali-kali karena overlapping

**Solusi:** Tambahkan `withoutOverlapping()` di scheduler

```php
$schedule->command('attendance:send-upcoming-checkin-reminder')
    ->everyFiveMinutes()
    ->withoutOverlapping()  // ← Penting!
    ->runInBackground();
```

### ❌ Waktu Reminder Tidak Tepat

**Masalah:** Timezone server berbeda dengan timezone lokal

**Solusi:** Set timezone di `.env`

```env
APP_TIMEZONE=Asia/Jakarta
```

Lalu clear cache:
```bash
php artisan config:cache
```

---

## 📋 Checklist Production

- [ ] **Scheduler Setup**
  - [ ] Crontab configured: `* * * * * cd /path && php artisan schedule:run`
  - [ ] Scheduler berjalan setiap menit
  - [ ] Commands registered di `Kernel.php`

- [ ] **Queue Worker**
  - [ ] Queue worker running (supervisor/systemd)
  - [ ] Auto-restart enabled
  - [ ] Email queue processed

- [ ] **Email Configuration**
  - [ ] SMTP configured correctly
  - [ ] Test email sent successfully
  - [ ] Email tidak masuk spam

- [ ] **Data Karyawan**
  - [ ] Semua karyawan punya shift_id
  - [ ] Semua shift punya start_time & end_time
  - [ ] Email karyawan valid

- [ ] **Testing**
  - [ ] Test upcoming check-in reminder ✅
  - [ ] Test daily check-in reminder ✅
  - [ ] Test check-out reminder ✅
  - [ ] Email masuk ke inbox karyawan ✅

---

## 💡 Tips & Best Practices

### 1. Jangan Kirim Reminder Terlalu Sering

**❌ Buruk:** Kirim setiap menit
```php
->everyMinute() // Terlalu sering!
```

**✅ Baik:** Kirim sesuai kebutuhan
```php
->everyFiveMinutes() // Cukup untuk reminder
```

### 2. Gunakan withoutOverlapping()

Mencegah command berjalan berkali-kali:
```php
$schedule->command('attendance:send-checkout-reminder')
    ->everyFiveMinutes()
    ->withoutOverlapping(10); // Timeout 10 menit
```

### 3. Monitor Failed Jobs

```bash
# Setup alert jika ada failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### 4. Log Reminder Activities

Tambahkan logging di command:
```php
\Log::info('Check-in reminder sent', [
    'employee' => $employee->name,
    'shift_time' => $employee->shift->start_time,
    'sent_at' => now()
]);
```

### 5. Batasi Reminder untuk Hari Kerja Saja

```php
// Di command handle()
$today = Carbon::now();

// Skip weekend
if ($today->isWeekend()) {
    $this->info('Skipped: Weekend');
    return Command::SUCCESS;
}

// Skip holidays
if (Holiday::whereDate('date', $today)->exists()) {
    $this->info('Skipped: Holiday');
    return Command::SUCCESS;
}
```

---

## 📈 Statistik Reminder

### Total Reminder Terkirim Bulan Ini

```sql
SELECT 
    DATE(created_at) as tanggal,
    JSON_EXTRACT(data, '$.type') as jenis_reminder,
    COUNT(*) as total_reminder
FROM notifications
WHERE MONTH(created_at) = MONTH(NOW())
  AND JSON_EXTRACT(data, '$.type') LIKE '%reminder%'
GROUP BY DATE(created_at), jenis_reminder
ORDER BY tanggal DESC;
```

### Karyawan Paling Sering Lupa Check-in

```sql
SELECT 
    u.name,
    COUNT(*) as total_reminder_diterima
FROM notifications n
JOIN users u ON JSON_EXTRACT(n.notifiable_id, '$') = u.id
WHERE JSON_EXTRACT(n.data, '$.type') = 'check_in_reminder'
  AND MONTH(n.created_at) = MONTH(NOW())
GROUP BY u.id, u.name
ORDER BY total_reminder_diterima DESC
LIMIT 10;
```

---

## 🎯 Ringkasan

| Reminder | Waktu | Email Template | Command |
|----------|-------|----------------|---------|
| **Upcoming Check-in** | 5-10 min sebelum shift | "Check-in dalam X menit" | `attendance:send-upcoming-checkin-reminder` |
| **Daily Check-in** | 15 min setelah shift mulai | "Anda belum check-in" | `attendance:send-checkin-reminder` |
| **Check-out** | 30 min sebelum shift berakhir | "Check-out dalam 30 menit" | `attendance:send-checkout-reminder` |

**Semua reminder:**
- ✅ Kirim ke **email** (SMTP)
- ✅ Simpan ke **database** (notifikasi in-app)
- ✅ Berjalan **otomatis** via scheduler
- ✅ Menggunakan **queue** untuk performa optimal

---

**Update:** 27 Desember 2025  
**Status:** Production Ready ✅  
**Fitur Baru:** Check-out reminder sekarang kirim email! 📧
