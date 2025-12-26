# 🎯 SISTEM DUAL MODE ABSENSI - DOKUMENTASI LENGKAP

## 📌 Konsep Utama

### Role & Permission

**1. Super Admin**
- ✅ Aktifkan/nonaktifkan mode absensi (shift dan/atau jam kerja)
- ✅ Bisa aktifkan **KEDUA MODE sekaligus**
- ❌ **TIDAK BISA** setting jam kerja minimum dan grace period

**2. Admin**
- ✅ **Hanya Admin yang bisa** atur jam kerja minimum & grace period
- ✅ Lihat mode yang diaktifkan Super Admin
- ❌ Tidak bisa ganti mode (hanya Super Admin)

---

## 🔄 Flow Sistem

```
SUPER ADMIN
    ↓
Centang Mode: [✓] Shift [✓] Working Hours
    ↓
Database: enabled_attendance_modes = ["shift", "working_hours"]
    ↓
    ├─→ Jika SHIFT diaktifkan
    │       ↓
    │   ADMIN lihat:
    │   ✅ Menu Shift (tampil)
    │   ✅ Bisa kelola shift
    │
    └─→ Jika WORKING HOURS diaktifkan
            ↓
        ADMIN lihat:
        ✅ Field Jam Kerja Minimum (bisa edit)
        ✅ Field Grace Period (bisa edit)
        ✅ Preview konfigurasi
```

---

## 👑 SUPER ADMIN

### Lokasi
**Menu:** Pengaturan → Pengaturan Organisasi

### Yang Tampil

```
╔════════════════════════════════════════════╗
║ Pengaturan Absensi                         ║
║ 🔧 Aktifkan fitur absensi untuk organisasi║
║ ini. Anda bisa mengaktifkan shift, jam    ║
║ kerja, atau keduanya.                      ║
╠════════════════════════════════════════════╣
║ Fitur Absensi yang Diaktifkan             ║
║                                            ║
║ ☑️ 🕒 Mode Shift                           ║
║    Absensi berdasarkan jadwal shift       ║
║    Karyawan check-in/out sesuai jadwal    ║
║    shift. Menu Shift akan muncul untuk    ║
║    Admin.                                  ║
║                                            ║
║ ☑️ ⏰ Mode Jam Kerja                       ║
║    Waktu fleksibel dengan minimum jam     ║
║    Karyawan bebas check-in, checkout      ║
║    setelah jam minimum. Admin bisa atur   ║
║    konfigurasi jam kerja.                  ║
║                                            ║
║ 💡 Anda bisa mengaktifkan kedua mode      ║
║    sekaligus                               ║
╚════════════════════════════════════════════╝
```

### Opsi Pilihan

#### ✅ Hanya Mode Shift
```
[✓] Mode Shift
[ ] Mode Jam Kerja

Efek untuk Admin:
✅ Menu "Shift" TAMPIL
✅ Admin bisa kelola shift
❌ Field jam kerja minimum TERSEMBUNYI
❌ Field grace period TERSEMBUNYI
```

#### ✅ Hanya Mode Jam Kerja
```
[ ] Mode Shift
[✓] Mode Jam Kerja

Efek untuk Admin:
❌ Menu "Shift" TERSEMBUNYI
✅ Field jam kerja minimum TAMPIL (Admin edit)
✅ Field grace period TAMPIL (Admin edit)
✅ Preview konfigurasi TAMPIL
```

#### ✅ KEDUA MODE Aktif
```
[✓] Mode Shift
[✓] Mode Jam Kerja

Efek untuk Admin:
✅ Menu "Shift" TAMPIL
✅ Field jam kerja minimum TAMPIL (Admin edit)
✅ Field grace period TAMPIL (Admin edit)
✅ Admin bisa kelola KEDUA sistem
```

**❗ Penting untuk Super Admin:**
- Super Admin **HANYA AKTIFKAN** fitur
- Field jam kerja minimum & grace period **DISABLED** untuk Super Admin
- **Admin yang mengatur** nilai konfigurasinya

---

## 👨‍💼 ADMIN

### Scenario 1: Hanya Mode Shift Aktif

**Halaman: Pengaturan Organisasi**
```
╔════════════════════════════════════════════╗
║ Pengaturan Absensi                         ║
╠════════════════════════════════════════════╣
║ Mode Absensi yang Aktif                    ║
║                                            ║
║ **Mode yang Diaktifkan oleh Super Admin:** ║
║                                            ║
║ 🕒 **Mode Shift**                          ║
║ ✅ Absensi berdasarkan jadwal shift        ║
║ ✅ Kelola shift di menu Shift              ║
╠════════════════════════════════════════════╣
║ Cara Menggunakan Mode Shift                ║
║                                            ║
║ **📋 Langkah Konfigurasi:**                ║
║ 1️⃣ Buka menu Pengaturan → Shift           ║
║ 2️⃣ Buat jadwal shift                      ║
║ 3️⃣ Tetapkan shift ke karyawan             ║
║ 4️⃣ Karyawan absen sesuai jam shift        ║
╚════════════════════════════════════════════╝
```

**Sidebar Admin:**
```
📁 Pengaturan
   ⚙️ Pengaturan Organisasi
   🕒 Shift  ← TAMPIL
   📍 Lokasi Absen
   📅 Hari Libur
```

---

### Scenario 2: Hanya Mode Jam Kerja Aktif

**Halaman: Pengaturan Jam Kerja**
```
╔════════════════════════════════════════════╗
║ Pengaturan Absensi                         ║
╠════════════════════════════════════════════╣
║ Mode Absensi yang Aktif                    ║
║                                            ║
║ **Mode yang Diaktifkan oleh Super Admin:** ║
║                                            ║
║ ⏰ **Mode Jam Kerja**                      ║
║ ✅ Karyawan bebas check-in                 ║
║ ✅ Atur konfigurasi jam kerja di bawah     ║
╠════════════════════════════════════════════╣
║ Jam Kerja Minimum  │  Grace Period        ║
║ ┌────────────┐     │  ┌────────────┐      ║
║ │ 8 jam/hari │     │  │ 2 jam      │      ║
║ └────────────┘     │  └────────────┘      ║
║                                            ║
║ ℹ️ Informasi                               ║
║ **Anda bisa mengatur konfigurasi jam      ║
║ kerja di atas.**                           ║
║                                            ║
║ Preview Konfigurasi                        ║
║ • Min: 8 jam                               ║
║ • Grace: 2 jam                             ║
║ • Max sebelum lembur: 10 jam              ║
║                                            ║
║ Contoh: Check-in 08:00                     ║
║ • 14:00 (6 jam) → ❌ Ditolak              ║
║ • 16:30 (8.5 jam) → ✅ Sukses             ║
║ • 19:00 (11 jam) → ✅ + 1 jam lembur      ║
╚════════════════════════════════════════════╝
```

**Sidebar Admin:**
```
📁 Pengaturan
   ⏰ Pengaturan Jam Kerja  ← TAMPIL
   📍 Lokasi Absen
   📅 Hari Libur
   (Menu Shift TIDAK MUNCUL)
```

---

### Scenario 3: KEDUA Mode Aktif

**Halaman: Pengaturan Organisasi**
```
╔════════════════════════════════════════════╗
║ Pengaturan Absensi                         ║
╠════════════════════════════════════════════╣
║ Mode Absensi yang Aktif                    ║
║                                            ║
║ **Mode yang Diaktifkan oleh Super Admin:** ║
║                                            ║
║ 🕒 **Mode Shift**                          ║
║ ✅ Absensi berdasarkan jadwal shift        ║
║ ✅ Kelola shift di menu Shift              ║
║                                            ║
║ ⏰ **Mode Jam Kerja**                      ║
║ ✅ Karyawan bebas check-in                 ║
║ ✅ Atur konfigurasi jam kerja di bawah     ║
╠════════════════════════════════════════════╣
║ Jam Kerja Minimum  │  Grace Period        ║
║ ┌────────────┐     │  ┌────────────┐      ║
║ │ 8 jam/hari │     │  │ 2 jam      │      ║
║ └────────────┘     │  └────────────┘      ║
║                                            ║
║ (+ Preview + Info Shift)                   ║
╚════════════════════════════════════════════╝
```

**Sidebar Admin:**
```
📁 Pengaturan
   ⚙️ Pengaturan Organisasi
   🕒 Shift  ← TAMPIL
   📍 Lokasi Absen
   📅 Hari Libur
```

---

## 💡 Contoh Kasus Nyata

### Kasus 1: Pabrik 24 Jam (Shift Only)

**Super Admin Setup:**
1. Buka Pengaturan Organisasi
2. Centang: **[✓] Mode Shift**
3. Uncentang: **[ ] Mode Jam Kerja**
4. Simpan

**Admin PT Pabrik:**
1. Login → Lihat menu "Shift"
2. Buat 3 shift:
   - Pagi: 07:00 - 15:00
   - Siang: 15:00 - 23:00
   - Malam: 23:00 - 07:00
3. Assign shift ke karyawan
4. ✅ Karyawan absen sesuai shift

**Yang TIDAK Ada:**
- ❌ Field jam kerja minimum
- ❌ Field grace period
- ❌ Preview konfigurasi

---

### Kasus 2: Startup Remote (Jam Kerja Only)

**Super Admin Setup:**
1. Buka Pengaturan Organisasi
2. Uncentang: **[ ] Mode Shift**
3. Centang: **[✓] Mode Jam Kerja**
4. Simpan

**Admin Startup:**
1. Login → Lihat "Pengaturan Jam Kerja"
2. Atur konfigurasi:
   - Min: 7 jam
   - Grace: 3 jam
3. Simpan
4. ✅ Karyawan bisa check-in jam berapa saja
5. ✅ Checkout setelah 7 jam kerja

**Yang TIDAK Ada:**
- ❌ Menu "Shift"
- ❌ Form pembuatan shift

---

### Kasus 3: Perusahaan Hybrid (KEDUA Mode Aktif)

**Super Admin Setup:**
1. Buka Pengaturan Organisasi
2. Centang: **[✓] Mode Shift**
3. Centang: **[✓] Mode Jam Kerja**
4. Simpan

**Admin Perusahaan:**
1. Login → Lihat menu "Shift" DAN field jam kerja
2. **Untuk tim Office:** Buat shift pagi 08:00-17:00
3. **Untuk tim Remote:** Atur min 8 jam, grace 2 jam
4. Karyawan office: absen by shift
5. Karyawan remote: absen fleksibel
6. ✅ Sistem support KEDUA mode sekaligus!

---

## 🔒 Permission Matrix

| Fitur | Super Admin | Admin |
|-------|-------------|-------|
| **Aktifkan Mode Shift** | ✅ | ❌ |
| **Aktifkan Mode Jam Kerja** | ✅ | ❌ |
| **Set Jam Kerja Minimum** | ❌ DISABLED | ✅ |
| **Set Grace Period** | ❌ DISABLED | ✅ |
| **Kelola Shift** | ✅ | ✅ (jika mode shift aktif) |
| **Lihat Preview** | ❌ Tidak perlu | ✅ |

---

## ⚙️ Teknis Implementasi

### 1. Database

**Column:** `enabled_attendance_modes` (JSON)

**Possible Values:**
```json
["shift"]                      // Hanya shift
["working_hours"]              // Hanya jam kerja
["shift", "working_hours"]     // Keduanya
```

### 2. Model Organization

```php
// Helper Methods
public function isShiftBased(): bool
{
    $modes = $this->enabled_attendance_modes ?? ['shift'];
    return in_array('shift', $modes);
}

public function isWorkingHoursBased(): bool
{
    $modes = $this->enabled_attendance_modes ?? ['shift'];
    return in_array('working_hours', $modes);
}

public function getEnabledModes(): array
{
    return $this->enabled_attendance_modes ?? ['shift'];
}
```

### 3. Menu Visibility (ShiftResource)

```php
public static function shouldRegisterNavigation(): bool
{
    // Super Admin selalu lihat
    if (auth()->user()->isSuperAdmin()) {
        return true;
    }
    
    // Admin hanya jika shift aktif
    return auth()->user()->organization->isShiftBased();
}
```

### 4. Field Visibility (OrganizationSettings)

**Super Admin:**
```php
Forms\Components\CheckboxList::make('enabled_attendance_modes')
    ->visible(fn () => auth()->user()->isSuperAdmin())
```

**Admin:**
```php
Forms\Components\TextInput::make('min_working_hours')
    ->disabled(fn () => auth()->user()->isSuperAdmin()) // Super Admin DISABLED
    ->visible(function ($get) {
        if (auth()->user()->isSuperAdmin()) {
            $modes = $get('enabled_attendance_modes') ?? ['shift'];
            return in_array('working_hours', $modes);
        }
        return auth()->user()->organization->isWorkingHoursBased();
    })
```

### 5. Save Logic

```php
// Super Admin: save modes
if (auth()->user()->isSuperAdmin()) {
    $updateData['enabled_attendance_modes'] = $data['enabled_attendance_modes'];
}

// Admin: save konfigurasi (BUKAN Super Admin)
if (!auth()->user()->isSuperAdmin() && $organization->isWorkingHoursBased()) {
    $updateData['min_working_hours'] = $data['min_working_hours'];
    $updateData['grace_period_hours'] = $data['grace_period_hours'];
}
```

---

## 📞 FAQ

**Q: Super Admin bisa setting jam kerja minimum?**
A: ❌ Tidak. Field tersebut DISABLED untuk Super Admin. Hanya Admin yang bisa setting.

**Q: Admin bisa aktifkan/nonaktifkan mode?**
A: ❌ Tidak. Hanya Super Admin yang bisa aktifkan mode.

**Q: Bisa aktifkan kedua mode sekaligus?**
A: ✅ Ya! Super Admin bisa centang shift dan jam kerja sekaligus.

**Q: Jika kedua mode aktif, bagaimana karyawan absen?**
A: Tergantung karyawan memiliki shift atau tidak:
- **Punya shift** → Absen by shift
- **Tidak punya shift** → Absen fleksibel (jam kerja)

**Q: Jika Super Admin uncheck semua mode?**
A: ❌ Tidak bisa. Minimal 1 mode harus aktif (validasi `minItems(1)`).

**Q: Data shift hilang jika mode shift dinonaktifkan?**
A: ❌ Tidak hilang. Data tetap ada di database, hanya menu tersembunyi.

---

## ✅ Checklist Testing

**Super Admin:**
- [ ] Bisa aktifkan hanya mode shift
- [ ] Bisa aktifkan hanya mode jam kerja  
- [ ] Bisa aktifkan kedua mode sekaligus
- [ ] Field jam kerja minimum DISABLED
- [ ] Field grace period DISABLED
- [ ] Tidak bisa uncheck semua mode

**Admin (Mode Shift Only):**
- [ ] Menu "Shift" MUNCUL
- [ ] Bisa buat shift baru
- [ ] Field jam kerja minimum TERSEMBUNYI
- [ ] Field grace period TERSEMBUNYI

**Admin (Mode Jam Kerja Only):**
- [ ] Menu "Shift" TERSEMBUNYI
- [ ] Field jam kerja minimum TAMPIL & EDITABLE
- [ ] Field grace period TAMPIL & EDITABLE
- [ ] Preview konfigurasi TAMPIL

**Admin (Kedua Mode):**
- [ ] Menu "Shift" MUNCUL
- [ ] Field jam kerja TAMPIL & EDITABLE
- [ ] Bisa atur kedua sistem

---

**Created**: 26 Desember 2025  
**Version**: 5.0.0 (Multi-Mode dengan Role-Based Configuration)
