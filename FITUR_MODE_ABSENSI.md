# FITUR MODE ABSENSI: SHIFT vs WORKING HOURS

## 📋 Deskripsi Fitur

Sistem presensi kini mendukung 2 mode absensi yang dapat dikonfigurasi oleh admin untuk setiap organisasi melalui panel admin.

### 1. **Mode Shift** (Default)
Mode berbasis jadwal shift dengan jam masuk dan keluar yang tetap.

### 2. **Mode Working Hours** (Flexible)
Mode berbasis jam kerja dengan waktu check-in yang fleksibel.

---

## ⚙️ Cara Mengatur Mode Absensi (Admin)

### Melalui Panel Admin

1. **Login sebagai Super Admin**
2. **Buka menu "Bisnis" / "Organizations"**
3. **Edit atau Buat Organisasi Baru**
4. **Scroll ke section "Pengaturan Mode Absensi"**
5. **Pilih Mode yang Diinginkan:**
   - **🕒 Berbasis Shift** - Untuk jadwal tetap (pabrik, retail, kantor formal)
   - **⏰ Berbasis Jam Kerja** - Untuk waktu fleksibel (startup, remote work, creative)

6. **Jika memilih "Berbasis Jam Kerja", atur:**
   - **Jam Kerja Minimum**: Berapa jam minimal karyawan harus bekerja (default: 8 jam)
   - **Grace Period**: Toleransi jam sebelum dihitung lembur (default: 2 jam)

7. **Lihat Preview/Contoh** yang ditampilkan secara otomatis
8. **Klik Simpan**

### Fitur Form Admin

- ✅ **Live Preview**: Melihat contoh langsung saat mengatur konfigurasi
- ✅ **Conditional Fields**: Field muncul otomatis sesuai mode yang dipilih
- ✅ **Helper Text**: Panduan jelas untuk setiap pengaturan
- ✅ **Validasi**: Minimum dan maximum value sudah diatur
- ✅ **Filter**: Filter organisasi berdasarkan mode absensi di tabel

---

## 🎯 Mode Working Hours - Detail Implementasi

### Konsep
- **Karyawan bebas check-in jam berapa saja** (tidak ada validasi keterlambatan)
- **Check-out hanya bisa dilakukan setelah mencapai jam kerja minimum**
- **Grace period** sebelum dihitung sebagai lembur
- **Lembur otomatis dihitung** jika melebihi jam kerja maksimal

### Konfigurasi

Setiap organisasi dapat mengatur:

| Parameter | Default | Keterangan |
|-----------|---------|-----------|
| `attendance_mode` | `shift` | Mode absensi: `shift` atau `working_hours` |
| `min_working_hours` | `8` | Jam kerja minimum per hari |
| `grace_period_hours` | `2` | Toleransi jam sebelum dihitung lembur |

### Contoh Skenario

**Konfigurasi:**
- Jam kerja minimum: 8 jam
- Grace period: 2 jam
- Maksimal sebelum lembur: 10 jam (8 + 2)

**Skenario 1: Karyawan Normal**
```
Check-in  : 08:00 WIB
Check-out : 16:30 WIB (setelah 8.5 jam)
Status    : ✅ Sukses checkout (sudah 8 jam lebih)
Lembur    : ❌ Tidak ada (belum melewati 10 jam)
```

**Skenario 2: Karyawan Pulang Cepat**
```
Check-in  : 08:00 WIB
Check-out : 14:00 WIB (baru 6 jam)
Status    : ❌ Ditolak! Belum mencapai 8 jam
Pesan     : "Kurang 2 jam lagi untuk mencapai jam kerja minimum"
```

**Skenario 3: Karyawan Rajin (dalam grace period)**
```
Check-in  : 08:00 WIB
Check-out : 17:45 WIB (setelah 9.75 jam)
Status    : ✅ Sukses checkout
Lembur    : ❌ Tidak ada (masih dalam grace period 10 jam)
```

**Skenario 4: Karyawan Lembur**
```
Check-in  : 08:00 WIB
Check-out : 19:00 WIB (setelah 11 jam)
Status    : ✅ Sukses checkout
Lembur    : ✅ 1 jam lembur (11 - 10 = 1 jam)
Catatan   : Overtime otomatis tercatat, menunggu approval admin
```

**Skenario 5: Check-in Siang**
```
Check-in  : 13:00 WIB
Check-out : 21:30 WIB (setelah 8.5 jam)
Status    : ✅ Sukses checkout (sudah 8 jam lebih)
Lembur    : ❌ Tidak ada
```

---

## 🔧 Cara Menggunakan (Untuk Developer)

### 1. Via Database (Manual)

```sql
-- Aktifkan mode working_hours untuk organisasi tertentu
UPDATE organizations 
SET 
    attendance_mode = 'working_hours',
    min_working_hours = 8,
    grace_period_hours = 2
WHERE id = 1;
```

### 2. Via Panel Admin (Recommended)

Gunakan panel admin Filament seperti dijelaskan di atas. Lebih mudah dan aman.

---

## 📊 Perbedaan Mode Shift vs Working Hours

| Aspek | Mode Shift | Mode Working Hours |
|-------|-----------|-------------------|
| **Check-in** | Validasi keterlambatan berdasarkan shift | Bebas jam berapa saja |
| **Status Check-in** | `late`, `on_time`, `early` | `flexible` |
| **Check-out** | Bisa checkout kapan saja | Harus sudah kerja minimal X jam |
| **Lembur** | Dihitung setelah jam shift berakhir | Dihitung setelah (min_hours + grace_period) |
| **Cocok untuk** | Pabrik, retail, kantor formal | Startup, remote work, creative agency |

---

## 💡 Validasi dan Pesan Error

### Check-out Sebelum Jam Minimum

```
⏰ Belum mencapai jam kerja minimum!

📋 Jam kerja minimum: 8 jam
⏱️ Anda telah bekerja: 6 jam 30 menit
⚠️ Kurang: 1 jam 30 menit lagi

💡 Silakan tunggu hingga mencapai jam kerja minimum.
```

### Lembur Terdeteksi Otomatis

Sistem akan:
1. ✅ Checkout berhasil
2. 📝 Membuat record overtime otomatis
3. ⏳ Status overtime: `pending` (menunggu approval admin)
4. 💰 Multiplier dihitung otomatis (1.5x, 1.75x, atau 2.0x)

---

## 🔐 Keamanan & Audit

- ✅ Semua validasi dilakukan di backend
- ✅ Face detection tetap aktif untuk kedua mode
- ✅ GPS validation tetap berlaku
- ✅ Audit log mencatat semua perubahan
- ✅ Anti-spoofing tetap berjalan

---

## 📝 File yang Dimodifikasi

### 1. Migration
- `database/migrations/2025_12_26_190340_add_attendance_mode_to_organizations_table.php`
  - Menambahkan kolom: `attendance_mode`, `min_working_hours`, `grace_period_hours`

### 2. Models
- `app/Models/Organization.php`
  - Tambah fillable fields
  - Tambah helper methods: `isShiftBased()`, `isWorkingHoursBased()`, `getMaxWorkHoursBeforeOvertime()`

### 3. Controllers
- `app/Http/Controllers/AttendanceController.php`
  - Update logika check-in untuk mode working_hours
  - Update validasi check-out dengan jam kerja minimum

### 4. Widgets
- `app/Filament/Widgets/QuickCheckInOut.php`
  - Update widget quick check-in/out untuk kedua mode

### 5. Observers
- `app/Observers/AttendanceObserver.php`
  - Update deteksi lembur otomatis untuk kedua mode

### 6. Resources (Admin Panel) ⭐ NEW!
- `app/Filament/Resources/OrganizationResource.php`
  - **Form**: Section "Pengaturan Mode Absensi" dengan live preview
  - **Table**: Kolom badge untuk menampilkan mode absensi
  - **Filter**: Filter berdasarkan mode absensi

---

## 🎓 Best Practices

### Untuk Admin
1. Pilih mode yang sesuai dengan budaya kerja organisasi
2. Set `min_working_hours` sesuai regulasi (default 8 jam)
3. Berikan `grace_period` yang reasonable (2-3 jam)
4. Monitor overtime yang ter-generate otomatis
5. Approve/reject overtime request secara berkala

### Untuk Karyawan (Mode Working Hours)
1. Check-in di jam berapa saja sesuai kebutuhan
2. Pastikan bekerja minimal sesuai jam yang ditentukan
3. Lihat sisa jam kerja sebelum mencoba checkout
4. Overtime otomatis tercatat jika melewati batas maksimal

---

## 🚀 Upgrade Notes

- ✅ Migration sudah dijalankan
- ✅ Backward compatible dengan sistem shift yang ada
- ✅ Default mode: `shift` (tidak mengubah perilaku existing)
- ✅ Tidak perlu update data existing

---

## 📞 Support

Jika ada pertanyaan atau issue terkait fitur ini, silakan hubungi tim development.

**Created**: 26 Desember 2025
**Version**: 1.0.0
