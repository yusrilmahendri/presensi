# 🎯 SISTEM MODE ABSENSI - ROLE BASED

## 📋 Konsep Sistem

### Super Admin: Aktifkan Mode
**Super Admin mengaktifkan SALAH SATU mode untuk organisasi:**
- ✅ Mode Shift ATAU
- ✅ Mode Working Hours

### Admin: Konfigurasi Sesuai Mode Aktif
**Admin hanya bisa lihat & atur fitur sesuai mode yang diaktifkan Super Admin**

---

## 🔄 Flow Sistem

```
SUPER ADMIN
    ↓
Pilih Mode: Shift atau Working Hours
    ↓
Mode Aktif di Database
    ↓
    ├─→ Jika SHIFT aktif
    │       ↓
    │   ADMIN lihat:
    │   ✅ Menu Shift (tampil)
    │   ✅ Pengaturan Organisasi (info saja)
    │   ❌ Pengaturan Jam Kerja (tidak tampil)
    │
    └─→ Jika WORKING HOURS aktif
            ↓
        ADMIN lihat:
        ✅ Pengaturan Jam Kerja (tampil, bisa edit)
        ❌ Menu Shift (tidak tampil)
```

---

## 👑 SUPER ADMIN

### Lokasi
**Menu:** Bisnis → Edit Organization

### Yang Tampil
```
╔══════════════════════════════════════════╗
║ Pengaturan Mode Absensi                  ║
║ 🔧 Aktifkan salah satu mode untuk       ║
║ organisasi ini                           ║
╠══════════════════════════════════════════╣
║ Mode Absensi                             ║
║ ┌──────────────────────────────────────┐ ║
║ │ 🕒 Berbasis Shift ▼                  │ ║
║ └──────────────────────────────────────┘ ║
║ 💡 Mode Shift: Menu Shift akan tampil   ║
║    untuk Admin                           ║
╚══════════════════════════════════════════╝
```

### Opsi Mode

#### Opsi 1: Mode Shift
```
Mode: 🕒 Berbasis Shift

Efek untuk Admin:
✅ Menu "Shift" TAMPIL di sidebar
✅ Admin bisa kelola jadwal shift
✅ Karyawan absen berdasarkan shift
❌ Pengaturan jam kerja TERSEMBUNYI
```

#### Opsi 2: Mode Working Hours
```
Mode: ⏰ Berbasis Jam Kerja

Setting:
• Jam Kerja Minimum: 8 jam
• Grace Period: 2 jam

Efek untuk Admin:
✅ Menu "Pengaturan Jam Kerja" TAMPIL
✅ Admin bisa atur min hours & grace period
✅ Karyawan absen fleksibel
❌ Menu "Shift" TERSEMBUNYI
```

---

## 👨‍💼 ADMIN

### Sidebar Dinamis Berdasarkan Mode

#### Jika Mode = SHIFT
```
📁 Pengaturan
   ⚙️ Pengaturan Organisasi  ← Info organisasi
   📍 Lokasi Absen
   📅 Hari Libur
   🕒 Shift                   ← MUNCUL! Bisa atur shift
```

#### Jika Mode = WORKING HOURS
```
📁 Pengaturan
   ⏰ Pengaturan Jam Kerja    ← MUNCUL! Bisa atur jam kerja
   📍 Lokasi Absen
   📅 Hari Libur
   (Menu Shift TIDAK MUNCUL)
```

---

## 📱 Tampilan untuk Admin

### Scenario 1: Mode Shift Aktif

**Halaman: Pengaturan Organisasi**
```
╔══════════════════════════════════════════╗
║ Informasi Organisasi                     ║
╠══════════════════════════════════════════╣
║ Email, Phone, Alamat...                  ║
╠══════════════════════════════════════════╣
║ Mode Absensi Aktif                       ║
║ 🕒 Mode Shift                            ║
║                                          ║
║ ✅ Karyawan check-in sesuai shift        ║
║ ✅ Kelola shift di menu Shift            ║
║ ✅ Status keterlambatan otomatis         ║
╠══════════════════════════════════════════╣
║ ℹ️ Mode ini diatur oleh Super Admin      ║
╠══════════════════════════════════════════╣
║ 📋 Cara Menggunakan Mode Shift           ║
║                                          ║
║ 1️⃣ Buka menu Pengaturan → Shift         ║
║ 2️⃣ Buat jadwal shift                    ║
║ 3️⃣ Tetapkan shift ke karyawan           ║
║ 4️⃣ Karyawan absen sesuai shift          ║
╚══════════════════════════════════════════╝
```

**Menu Shift (TAMPIL)**
- Admin bisa buat shift baru
- Atur jam masuk/keluar per shift
- Assign shift ke karyawan

---

### Scenario 2: Mode Working Hours Aktif

**Halaman: Pengaturan Jam Kerja**
```
╔══════════════════════════════════════════╗
║ Pengaturan Jam Kerja                     ║
╠══════════════════════════════════════════╣
║ Email, Phone, Alamat...                  ║
╠══════════════════════════════════════════╣
║ Mode Absensi Aktif                       ║
║ ⏰ Mode Jam Kerja                        ║
║                                          ║
║ ✅ Karyawan bebas check-in               ║
║ ✅ Checkout setelah minimal X jam        ║
║ ✅ Atur konfigurasi di bawah ini         ║
╠══════════════════════════════════════════╣
║ ℹ️ Mode ini diatur oleh Super Admin      ║
╠══════════════════════════════════════════╣
║ Jam Kerja Minimum  │  Grace Period       ║
║ ┌────────────┐     │  ┌────────────┐     ║
║ │ 8 jam/hari │     │  │ 2 jam      │     ║
║ └────────────┘     │  └────────────┘     ║
║                                          ║
║ 📊 Preview Konfigurasi                   ║
║ • Min: 8 jam                             ║
║ • Grace: 2 jam                           ║
║ • Max sebelum lembur: 10 jam            ║
║                                          ║
║ Contoh: Check-in 08:00                   ║
║ • 14:00 (6 jam) → ❌ Ditolak            ║
║ • 16:30 (8.5 jam) → ✅ Sukses           ║
║ • 19:00 (11 jam) → ✅ + 1 jam lembur    ║
╚══════════════════════════════════════════╝
```

**Menu Shift (TIDAK TAMPIL)**
- Menu shift disembunyikan otomatis
- Admin fokus pada konfigurasi jam kerja

---

## 💡 Contoh Kasus Nyata

### Kasus 1: Pabrik (Mode Shift)

**Super Admin Setup:**
1. Buka Bisnis → Edit "PT Pabrik ABC"
2. Pilih mode: "🕒 Berbasis Shift"
3. Simpan

**Admin PT Pabrik ABC:**
1. Login → Lihat menu "Shift" di sidebar
2. Buka menu Shift
3. Buat 3 shift:
   - Shift Pagi: 07:00 - 15:00
   - Shift Siang: 15:00 - 23:00
   - Shift Malam: 23:00 - 07:00
4. Assign shift ke karyawan
5. ✅ Karyawan absen sesuai shift mereka

**Yang TIDAK Ada:**
- ❌ Menu "Pengaturan Jam Kerja"
- ❌ Field jam kerja minimum
- ❌ Field grace period

---

### Kasus 2: Startup IT (Mode Working Hours)

**Super Admin Setup:**
1. Buka Bisnis → Edit "Startup XYZ"
2. Pilih mode: "⏰ Berbasis Jam Kerja"
3. Set min: 8 jam, grace: 2 jam
4. Simpan

**Admin Startup XYZ:**
1. Login → Lihat menu "Pengaturan Jam Kerja"
2. Buka halaman tersebut
3. Ubah konfigurasi:
   - Min: 7 jam (lebih fleksibel)
   - Grace: 3 jam
4. Simpan
5. ✅ Karyawan bisa check-in jam berapa saja
6. ✅ Checkout setelah 7 jam kerja

**Yang TIDAK Ada:**
- ❌ Menu "Shift" di sidebar
- ❌ Form pembuatan shift
- ❌ Assignment shift ke karyawan

---

### Kasus 3: Super Admin Ganti Mode

**Sebelum: Mode Shift**
- Admin lihat menu Shift
- Karyawan absen by shift

**Super Admin: Ganti ke Working Hours**
1. Edit organisasi
2. Pilih "⏰ Berbasis Jam Kerja"
3. Set konfigurasi
4. Simpan

**Setelah: Mode Working Hours**
- Menu Shift **HILANG** dari Admin
- Menu Pengaturan Jam Kerja **MUNCUL**
- Admin bisa atur min hours & grace period
- Karyawan sekarang absen fleksibel

---

## 🎭 Perbandingan Tampilan

| Aspek | Mode Shift | Mode Working Hours |
|-------|-----------|-------------------|
| **Menu Shift** | ✅ Tampil | ❌ Tersembunyi |
| **Menu Pengaturan Jam Kerja** | ❌ Tersembunyi | ✅ Tampil |
| **Field Min Hours** | ❌ Tidak ada | ✅ Ada & bisa edit |
| **Field Grace Period** | ❌ Tidak ada | ✅ Ada & bisa edit |
| **Kelola Shift** | ✅ Bisa | ❌ Tidak bisa |
| **Assign Shift** | ✅ Bisa | ❌ Tidak bisa |
| **Preview Working Hours** | ❌ Tidak ada | ✅ Ada |

---

## ⚙️ Teknis Implementasi

### 1. Menu Shift Hide/Show
```php
// ShiftResource.php
public static function shouldRegisterNavigation(): bool
{
    // Super Admin selalu bisa lihat
    if (auth()->user()->isSuperAdmin()) {
        return true;
    }
    
    // Admin hanya lihat jika mode = shift
    if (auth()->user()->organization) {
        return auth()->user()->organization->attendance_mode === 'shift';
    }
    
    return false;
}
```

### 2. Dynamic Navigation Label
```php
// OrganizationSettings.php
public static function getNavigationLabel(): string
{
    if (auth()->user()->isSuperAdmin()) {
        return 'Pengaturan Organisasi';
    }
    
    $mode = auth()->user()->organization->attendance_mode ?? 'shift';
    
    if ($mode === 'working_hours') {
        return 'Pengaturan Jam Kerja';
    }
    
    return 'Pengaturan Organisasi';
}
```

### 3. Conditional Fields
```php
// Field hanya muncul sesuai mode
->visible(function ($get) {
    if (auth()->user()->isSuperAdmin()) {
        return ($get('attendance_mode') ?? 'shift') === 'working_hours';
    }
    
    $org = auth()->user()->organization;
    return ($org->attendance_mode ?? 'shift') === 'working_hours';
})
```

---

## 🎯 Keuntungan Sistem Ini

### Untuk Super Admin
✅ Kontrol penuh atas mode organisasi
✅ Satu tombol untuk aktifkan/nonaktifkan fitur
✅ Konsisten di seluruh sistem

### Untuk Admin
✅ Interface **super clean** (hanya lihat yang relevan)
✅ **Tidak bingung** dengan menu yang tidak perlu
✅ Fokus pada fitur yang aktif
✅ **Tidak bisa salah** karena otomatis

### Untuk Karyawan
✅ Sistem konsisten sesuai mode
✅ Tidak ada kebingungan
✅ Absensi sesuai aturan organisasi

---

## 📞 FAQ

**Q: Admin bisa ganti mode?**
A: ❌ Tidak. Hanya Super Admin yang bisa aktifkan/ganti mode.

**Q: Jika mode shift, apakah admin bisa lihat working hours?**
A: ❌ Tidak. Menu dan field working hours disembunyikan otomatis.

**Q: Jika mode working hours, apakah menu shift hilang?**
A: ✅ Ya. Menu Shift tidak muncul di sidebar Admin.

**Q: Apa yang terjadi jika Super Admin ganti mode?**
A: Menu dan field langsung berubah sesuai mode baru. Admin perlu refresh browser.

**Q: Apakah data shift hilang jika ganti ke working hours?**
A: ❌ Tidak hilang. Data shift tetap ada di database, hanya tersembunyi.

**Q: Bisa aktifkan kedua mode sekaligus?**
A: ❌ Tidak. Hanya SATU mode yang bisa aktif per organisasi.

---

**Created**: 26 Desember 2025  
**Version**: 4.0.0 (Feature Toggle System)
