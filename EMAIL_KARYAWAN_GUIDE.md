# 📧 CARA KERJA EMAIL NOTIFIKASI KARYAWAN

## ✅ Konsep Penting

### 🔑 1 Email Sistem = Kirim ke SEMUA Karyawan

```
┌─────────────────────────────────────┐
│  MAIL_USERNAME (di .env)            │
│  = system@yourcompany.com           │ ← Setup 1x saja!
│  = Email PENGIRIM untuk semua       │
└─────────────────────────────────────┘
           │
           ├──→ karyawan1@gmail.com (dari database)
           ├──→ karyawan2@yahoo.com (dari database)
           ├──→ karyawan3@outlook.com (dari database)
           └──→ admin@company.com (dari database)
```

---

## 🔧 Setup Email (SEKALI untuk Semua Karyawan)

### 1️⃣ Setup Gmail sebagai Email Pengirim Sistem

#### A. Buat/Gunakan Gmail untuk Sistem
Contoh: `presensi.system@gmail.com`

#### B. Enable 2-Step Verification
1. Buka https://myaccount.google.com/security
2. Klik **2-Step Verification**
3. Ikuti langkah aktivasi

#### C. Generate App Password
1. Kembali ke https://myaccount.google.com/security
2. Scroll ke **App Passwords**
3. Pilih:
   - App: **Mail**
   - Device: **Other** (ketik "PioneerHadir")
4. Klik **Generate**
5. Copy 16 digit password (contoh: `abcd efgh ijkl mnop`)

#### D. Update .env di Server Production
```env
# Email Configuration - PENGIRIM (1x setup untuk semua)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=presensi.system@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop  # ← App Password 16 digit
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=presensi.system@gmail.com
MAIL_FROM_NAME="PioneerHadir System"
```

#### E. Clear Cache & Test
```bash
php artisan config:clear
php artisan config:cache
php artisan queue:restart
```

---

## 👥 Email Karyawan (Penerima)

### ✅ Tidak Perlu Setup Apapun!

Karyawan **HANYA** perlu:
1. ✅ Punya email aktif (Gmail, Yahoo, Outlook, dll)
2. ✅ Email tersimpan di database (tabel `users`)

### 🔍 Cek Email Karyawan di Database

```sql
SELECT id, name, email, role FROM users;
```

Hasil contoh:
```
+----+---------------+-------------------------+----------+
| id | name          | email                   | role     |
+----+---------------+-------------------------+----------+
| 1  | Admin Bisnis  | admin@company.com       | admin    |
| 2  | Budi Santoso  | budi.santoso@gmail.com  | employee |
| 3  | Siti Aminah   | siti.aminah@yahoo.com   | employee |
| 4  | Joko Widodo   | joko.w@outlook.com      | employee |
+----+---------------+-------------------------+----------+
```

---

## 📨 Cara Kerja Notifikasi Email

### Skenario 1: Keterlambatan Check-In

**Trigger:** Karyawan check-in terlambat > 15 menit

**File:** `app/Observers/AttendanceObserver.php`

```php
// Otomatis kirim email ke karyawan yang terlambat
$attendance->user->notify(new LateCheckInNotification($attendance, $lateMinutes));
```

**Email yang dikirim:**
```
From: PioneerHadir System <presensi.system@gmail.com>
To: budi.santoso@gmail.com
Subject: Keterlambatan Absen

Halo Budi Santoso,

Anda terlambat melakukan check-in hari ini.
Waktu check-in: 09:30
Terlambat: 90 menit

Harap lebih tepat waktu di kemudian hari.

[Lihat Presensi]
```

### Skenario 2: Persetujuan Izin/Cuti

**Trigger:** Admin approve/reject leave request

**File:** `app/Filament/Resources/LeaveResource.php`

```php
// Kirim email ke karyawan yang mengajukan izin
$record->user->notify(new LeaveStatusNotification(
    $record,
    'approved',
    'Selamat menikmati cuti Anda!'
));
```

**Email yang dikirim:**
```
From: PioneerHadir System <presensi.system@gmail.com>
To: siti.aminah@yahoo.com
Subject: Pengajuan Izin Disetujui

Halo Siti Aminah,

Pengajuan izin Anda telah DISETUJUI.
Tanggal: 27-31 Desember 2025
Catatan: Selamat menikmati cuti Anda!

[Lihat Detail Izin]
```

### Skenario 3: Pengajuan Lembur

**Trigger:** Admin approve/reject overtime

**File:** `app/Filament/Resources/OvertimeResource.php`

```php
// Kirim email ke karyawan
$record->user->notify(new OvertimeApprovalNotification(
    $record,
    'approved',
    'Terima kasih atas dedikasi Anda'
));
```

**Email yang dikirim:**
```
From: PioneerHadir System <presensi.system@gmail.com>
To: joko.w@outlook.com
Subject: Pengajuan Lembur Disetujui

Halo Joko Widodo,

Pengajuan lembur Anda telah DISETUJUI.
Tanggal: 26 Desember 2025
Durasi: 3 jam
Catatan: Terima kasih atas dedikasi Anda

[Lihat Detail Lembur]
```

---

## 🎯 Daftar Semua Notifikasi Email

| No | Notifikasi | Kapan Terkirim | Penerima |
|----|-----------|----------------|----------|
| 1 | **LateCheckInNotification** | Karyawan terlambat > 15 menit | Karyawan yang terlambat |
| 2 | **LeaveStatusNotification** | Admin approve/reject izin | Karyawan pengaju izin |
| 3 | **OvertimeApprovalNotification** | Admin approve/reject lembur | Karyawan pengaju lembur |
| 4 | **CheckInReminderNotification** | Scheduler harian (pagi) | Semua karyawan aktif |
| 5 | **CheckOutReminderNotification** | Scheduler harian (sore) | Karyawan yang belum checkout |
| 6 | **UpcomingLeaveReminderNotification** | H-1 sebelum cuti | Karyawan yang akan cuti |
| 7 | **WeeklySummaryNotification** | Setiap Senin pagi | Admin bisnis |

---

## 🔍 Cara Cek Email Karyawan Terkirim atau Tidak

### 1. Cek Queue Jobs
```sql
-- Cek antrian email yang akan dikirim
SELECT * FROM jobs ORDER BY created_at DESC LIMIT 10;
```

### 2. Cek Failed Jobs
```sql
-- Cek email yang gagal terkirim
SELECT * FROM failed_jobs ORDER BY failed_at DESC;
```

### 3. Cek Log Laravel
```bash
tail -f storage/logs/laravel.log
```

### 4. Cek Database Notifications
```sql
-- Cek notifikasi yang tersimpan di database
SELECT n.*, u.name, u.email 
FROM notifications n
JOIN users u ON u.id = JSON_UNQUOTE(JSON_EXTRACT(n.notifiable_id, '$'))
WHERE n.type LIKE '%LateCheckIn%'
ORDER BY n.created_at DESC
LIMIT 10;
```

---

## ✅ Testing Kirim Email ke Karyawan Tertentu

### Test 1: Kirim Manual via Tinker

```bash
php artisan tinker
```

```php
// Pilih karyawan
$karyawan = \App\Models\User::where('email', 'budi.santoso@gmail.com')->first();

// Kirim notifikasi keterlambatan (contoh)
$attendance = $karyawan->attendances()->latest()->first();
$karyawan->notify(new \App\Notifications\LateCheckInNotification($attendance, 30));

// Cek apakah masuk queue
\DB::table('jobs')->count();
```

### Test 2: Trigger Real Scenario

```bash
# 1. Jalankan queue worker
php artisan queue:work

# 2. Di tab lain, trigger notifikasi
php artisan tinker
```

```php
// Buat attendance yang terlambat
$user = \App\Models\User::find(2); // Karyawan ID 2

$attendance = \App\Models\Attendance::create([
    'user_id' => $user->id,
    'type' => 'check_in',
    'attendance_time' => now(),
    'latitude' => '-6.200000',
    'longitude' => '106.816666',
    'photo' => 'data:image/png;base64,test'
]);

// Observer akan auto-trigger notifikasi jika terlambat
```

---

## 🚨 Troubleshooting

### ❌ Email Tidak Terkirim ke Karyawan

**Cek 1: Email karyawan valid?**
```sql
SELECT id, name, email FROM users WHERE email IS NULL OR email = '';
```
✅ Pastikan semua karyawan punya email valid

**Cek 2: Queue worker jalan?**
```bash
ps aux | grep "queue:work"
```
✅ Harus ada process yang running

**Cek 3: Email sistem terblokir?**
```bash
php artisan tinker
```
```php
Mail::raw('Test', function($m) {
    $m->to('your-personal-email@gmail.com')->subject('Test');
});
```
✅ Cek apakah email masuk (inbox/spam)

**Cek 4: Notifikasi channel aktif?**
```php
// Di file Notification, pastikan ada 'mail'
public function via(object $notifiable): array
{
    return ['mail', 'database']; // ← 'mail' harus ada
}
```

---

## 📋 Checklist Setup Email untuk Produksi

- [ ] **Email Sistem Setup**
  - [ ] Gmail/SMTP provider siap
  - [ ] 2FA aktif
  - [ ] App Password di-generate
  - [ ] MAIL_* di .env terisi lengkap
  - [ ] Config cache sudah di-clear

- [ ] **Email Karyawan**
  - [ ] Semua karyawan punya email di database
  - [ ] Email valid dan aktif
  - [ ] Tidak ada email NULL atau kosong

- [ ] **Queue Worker**
  - [ ] QUEUE_CONNECTION=database
  - [ ] Tabel jobs sudah di-migrate
  - [ ] Queue worker running (supervisor/systemd)
  - [ ] Auto-restart enabled

- [ ] **Testing**
  - [ ] Test kirim email manual ✅
  - [ ] Test notifikasi real scenario ✅
  - [ ] Email masuk ke inbox karyawan ✅
  - [ ] Cek spam folder jika perlu ✅

---

## 💡 Tips Best Practice

### 1. Gunakan Email Domain Sendiri (Production)
```env
# Lebih profesional dan tidak masuk spam
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="PT Your Company - Sistem Presensi"
```

### 2. Setup SPF/DKIM Record
Tambahkan di DNS domain Anda:
```
TXT @ "v=spf1 include:_spf.google.com ~all"
```

### 3. Monitor Email Delivery
```bash
# Cek jumlah email terkirim hari ini
php artisan tinker
```
```php
\DB::table('jobs')->whereDate('created_at', today())->count();
```

### 4. Batasi Email Testing
```env
# Development: kirim ke 1 email saja
MAIL_TO_TESTING=your-email@gmail.com

# Production: kirim ke email karyawan sebenarnya
MAIL_TO_TESTING=null
```

---

**Kesimpulan:**
- ✅ **1 Email Sistem** (MAIL_USERNAME) kirim ke **semua karyawan**
- ✅ Email karyawan **sudah ada di database**, tidak perlu setup apapun
- ✅ Karyawan hanya perlu **email aktif untuk menerima**
- ✅ Tidak perlu App Password untuk setiap karyawan
- ✅ Queue worker harus **selalu running** di production

---

**Update:** 26 Desember 2025  
**Status:** Ready for Production ✅
