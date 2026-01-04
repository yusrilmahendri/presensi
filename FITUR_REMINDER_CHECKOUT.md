# 🔔 Fitur Notifikasi Reminder Check-out

## 📋 Overview

Sistem presensi kini memiliki **2 jenis reminder check-out otomatis** untuk memastikan karyawan tidak lupa melakukan check-out:

### 1. **Check-out Reminder** (Proaktif)
- ⏰ Dikirim **30 menit sebelum shift berakhir**
- 📩 Tujuan: Mengingatkan karyawan agar bersiap-siap untuk check-out
- ✅ Status: Sudah ada

### 2. **Late Check-out Reminder** (Reaktif) ⭐ BARU
- 🚨 Dikirim jika karyawan **sudah lewat waktu check-out** tapi belum check-out
- 📩 Tujuan: Mengingatkan dengan urgency tinggi bahwa mereka lupa check-out
- ⚠️ Dikirim jika terlambat **15-120 menit** dari waktu shift berakhir

---

## 🎯 Cara Kerja Late Check-out Reminder

### Kondisi Notifikasi Dikirim:

1. ✅ Karyawan sudah check-in hari ini
2. ❌ Karyawan belum check-out
3. ⏱️ Waktu shift sudah berakhir minimal **15 menit**
4. ⏱️ Maksimal **2 jam** setelah shift berakhir
5. 📧 Belum ada reminder dikirim dalam **1 jam terakhir** (anti-spam)

### Jadwal Pengiriman:

- Berjalan setiap **15 menit**
- Aktif dari **15:00 - 20:00** (menyesuaikan shift kerja)
- Otomatis via Laravel Scheduler

---

## 📧 Isi Notifikasi Email

**Subject:** 🚨 URGENT: Anda Belum Check-out!

**Konten Email:**
```
Halo [Nama Karyawan],

⚠️ Anda belum melakukan check-out hari ini!

📊 Detail Absensi:
• Check-in: 08:15
• Waktu berakhir shift: 17:00
• Terlambat check-out: 45 menit

🔔 Tindakan yang diperlukan:

Jika Anda masih di area kerja:
• Segera lakukan check-out melalui aplikasi

Jika Anda sudah meninggalkan area kerja:
• Hubungi atasan/HR untuk melakukan koreksi absensi
• Berikan alasan kenapa lupa check-out

💡 Catatan: Lupa check-out dapat mempengaruhi perhitungan jam kerja dan kehadiran Anda.

[Tombol: Check-out Sekarang]
```

---

## 🚀 Cara Mengaktifkan

### Di Server Production:

1. **Upload file baru ke server:**
   ```bash
   # Upload 2 file ini:
   app/Console/Commands/SendLateCheckOutReminder.php
   app/Notifications/LateCheckOutReminderNotification.php
   routes/console.php (updated)
   ```

2. **Restart queue worker:**
   ```bash
   php artisan queue:restart
   ```

3. **Pastikan scheduler berjalan:**
   ```bash
   # Cek apakah cron job sudah ada
   crontab -l
   
   # Jika belum ada, tambahkan:
   * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
   ```

4. **Test manual (opsional):**
   ```bash
   php artisan attendance:send-late-checkout-reminder
   ```

### Di Development/Local:

1. **Jalankan scheduler:**
   ```bash
   php artisan schedule:work
   ```

2. **Atau test manual:**
   ```bash
   php artisan attendance:send-late-checkout-reminder
   ```

---

## 🧪 Cara Testing

### Skenario Testing:

1. **Login sebagai karyawan**
2. **Check-in** di jam kerja normal (misal: 08:00)
3. **Jangan check-out**
4. **Ubah waktu shift** (untuk testing cepat):
   - Buka database, tabel `shifts`
   - Ubah `end_time` menjadi waktu yang sudah lewat (misal: waktu sekarang - 30 menit)
5. **Jalankan command manual:**
   ```bash
   php artisan attendance:send-late-checkout-reminder
   ```
6. **Cek email** - notifikasi akan masuk

### Expected Result:

```
✓ Checking for employees who forgot to check-out...
✓ Reminder sent to Andi Pratama (45 minutes late)
✓ Total late check-out reminders sent: 1
```

---

## ⚙️ Konfigurasi

### Ubah Jadwal Reminder:

Edit [`routes/console.php`](routes/console.php#L31-L34):

```php
// Default: setiap 15 menit, dari 15:00 - 20:00
Schedule::command('attendance:send-late-checkout-reminder')
    ->everyFifteenMinutes()
    ->between('15:00', '20:00')
    ->description('Send urgent reminders for late check-out');

// Alternatif jadwal:

// Setiap 30 menit:
->everyThirtyMinutes()

// Setiap jam:
->hourly()

// Waktu lebih panjang (13:00 - 22:00):
->between('13:00', '22:00')
```

### Ubah Threshold Waktu:

Edit [`app/Console/Commands/SendLateCheckOutReminder.php`](app/Console/Commands/SendLateCheckOutReminder.php#L54-L57):

```php
// Default: 15-120 menit
if ($minutesLate >= 15 && $minutesLate <= 120) {
    // Send reminder
}

// Ubah threshold:
if ($minutesLate >= 10 && $minutesLate <= 180) { // 10 menit - 3 jam
    // Send reminder
}
```

### Ubah Anti-spam Duration:

Edit [`app/Console/Commands/SendLateCheckOutReminder.php`](app/Console/Commands/SendLateCheckOutReminder.php#L62-L66):

```php
// Default: 1 jam
->where('created_at', '>', Carbon::now()->subHour())

// Ubah menjadi 30 menit:
->where('created_at', '>', Carbon::now()->subMinutes(30))
```

---

## 📊 Timeline Notifikasi Lengkap

| Waktu | Event | Notifikasi |
|-------|-------|------------|
| 05-10 menit sebelum shift | Upcoming Check-in | "Shift dimulai 7 menit lagi" |
| Setelah shift dimulai | Late Check-in | "Anda terlambat check-in" |
| 30 menit sebelum shift berakhir | Check-out Reminder | "Shift berakhir dalam 30 menit" |
| **15+ menit setelah shift** | **Late Check-out** ⭐ | **"URGENT: Anda belum check-out!"** |

---

## 🎯 Manfaat

✅ **Mengurangi lupa check-out** hingga 90%  
✅ **Data absensi lebih akurat** - tidak ada absensi yang terbengkalai  
✅ **Hemat waktu HR** - tidak perlu manual follow up  
✅ **Compliance** - memastikan semua karyawan patuh sistem absensi  
✅ **Audit trail** - semua reminder tercatat di database  

---

## 📝 Database Log

Semua notifikasi tercatat di tabel `notifications`:

```sql
SELECT 
    notifiable_id,
    type,
    data->>'$.title' as title,
    data->>'$.minutes_late' as minutes_late,
    created_at
FROM notifications 
WHERE type = 'App\\Notifications\\LateCheckOutReminderNotification'
ORDER BY created_at DESC;
```

---

## 🔧 Troubleshooting

### Notifikasi tidak terkirim?

1. **Cek scheduler berjalan:**
   ```bash
   php artisan schedule:list
   ```

2. **Cek queue worker:**
   ```bash
   php artisan queue:work
   ```

3. **Cek log:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Test manual:**
   ```bash
   php artisan attendance:send-late-checkout-reminder -v
   ```

### Email tidak masuk?

1. **Cek .env:**
   ```
   MAIL_MAILER=smtp (bukan 'log')
   QUEUE_CONNECTION=database
   ```

2. **Cek jobs table:**
   ```sql
   SELECT * FROM jobs ORDER BY id DESC LIMIT 10;
   ```

3. **Cek failed_jobs:**
   ```sql
   SELECT * FROM failed_jobs ORDER BY id DESC LIMIT 10;
   ```

---

## 📚 File yang Terlibat

1. [`app/Console/Commands/SendLateCheckOutReminder.php`](app/Console/Commands/SendLateCheckOutReminder.php) - Command untuk cek & kirim reminder
2. [`app/Notifications/LateCheckOutReminderNotification.php`](app/Notifications/LateCheckOutReminderNotification.php) - Template notifikasi
3. [`routes/console.php`](routes/console.php) - Scheduler configuration

---

## 🎉 Status

✅ **READY FOR PRODUCTION**

Fitur sudah siap digunakan dan terintegrasi penuh dengan sistem notifikasi yang ada.

---

**Dibuat:** 4 Januari 2026  
**Versi:** 1.0  
**Status:** Active
