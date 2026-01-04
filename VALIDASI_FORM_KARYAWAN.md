# 🔄 Validasi Conditional Form Karyawan Berdasarkan Pengaturan Admin

## 📋 Overview

Form tambah/edit karyawan kini secara otomatis menyesuaikan field **Jenis Kerja** dan **Shift** berdasarkan mode absensi yang diaktifkan oleh admin di **Pengaturan Organisasi**.

---

## 🎯 Skenario & Behavior

### 1️⃣ **Admin Hanya Mengaktifkan "Shift"**

**Pengaturan Admin:**
- ✅ Mode Absensi Shift (aktif)
- ❌ Mode Working Hours (nonaktif)

**Tampilan Form Karyawan:**
- ❌ Field "Jenis Kerja" **HIDDEN** (otomatis terisi `shift`)
- ✅ Field "Shift" **VISIBLE & REQUIRED**
- ✅ User WAJIB pilih shift

**Validasi:**
```
work_type = 'shift' (auto-filled)
shift_id = REQUIRED
```

---

### 2️⃣ **Admin Hanya Mengaktifkan "Working Hours"**

**Pengaturan Admin:**
- ❌ Mode Absensi Shift (nonaktif)
- ✅ Mode Working Hours (aktif)

**Tampilan Form Karyawan:**
- ❌ Field "Jenis Kerja" **HIDDEN** (otomatis terisi `working_hours`)
- ❌ Field "Shift" **HIDDEN**
- ✅ Karyawan otomatis pakai working hours mode

**Validasi:**
```
work_type = 'working_hours' (auto-filled)
shift_id = NULL (tidak dibutuhkan)
```

---

### 3️⃣ **Admin Mengaktifkan KEDUANYA** ⭐

**Pengaturan Admin:**
- ✅ Mode Absensi Shift (aktif)
- ✅ Mode Working Hours (aktif)

**Tampilan Form Karyawan:**
- ✅ Field "Jenis Kerja" **VISIBLE & REQUIRED**
  - Opsi: "🕒 Shift" atau "⏰ Working Hours"
- ✅ Field "Shift" **VISIBLE** jika pilih "Shift"
- ❌ Field "Shift" **HIDDEN** jika pilih "Working Hours"

**Validasi:**
```
Jika pilih "Shift":
  work_type = 'shift'
  shift_id = REQUIRED
  
Jika pilih "Working Hours":
  work_type = 'working_hours'
  shift_id = NULL
```

---

## 💡 Keuntungan

✅ **User-Friendly** - Form otomatis menyesuaikan dengan konfigurasi  
✅ **No Confusion** - User tidak bingung pilih mode yang tidak aktif  
✅ **Data Consistency** - work_type selalu sesuai dengan enabled_modes  
✅ **Auto-Fill** - Field tersembunyi otomatis terisi nilai yang benar  
✅ **Validation** - Field shift hanya required ketika diperlukan  

---

## 🔧 Technical Details

### File yang Diupdate:
- [`app/Filament/Resources/UserResource.php`](app/Filament/Resources/UserResource.php)

### Logic Implementasi:

#### 1. **Dynamic Options**
```php
->options(function () {
    $enabledModes = auth()->user()->organization->getEnabledModes();
    
    $options = [];
    if (in_array('shift', $enabledModes)) {
        $options['shift'] = '🕒 Shift - Absen berdasarkan jadwal shift';
    }
    if (in_array('working_hours', $enabledModes)) {
        $options['working_hours'] = '⏰ Working Hours - Absen fleksibel';
    }
    
    return $options;
})
```

#### 2. **Auto Default Value**
```php
->default(function () {
    $enabledModes = auth()->user()->organization->getEnabledModes();
    return $enabledModes[0] ?? 'shift'; // Ambil mode pertama yang aktif
})
```

#### 3. **Conditional Visibility**
```php
->visible(function ($get) {
    $enabledModes = auth()->user()->organization->getEnabledModes();
    
    // Tampilkan hanya jika ada > 1 mode
    return count($enabledModes) > 1;
})
```

#### 4. **Dehydrate Even When Hidden**
```php
->dehydrated() // Pastikan nilai tersimpan meskipun field hidden
```

#### 5. **Conditional Required**
```php
// Field Shift required hanya jika:
// 1. Hanya mode shift yang aktif, ATAU
// 2. User pilih work_type = 'shift'

->required(function ($get) {
    $enabledModes = auth()->user()->organization->getEnabledModes();
    
    if (count($enabledModes) === 1 && in_array('shift', $enabledModes)) {
        return true;
    }
    
    if ($get('work_type') === 'shift') {
        return true;
    }
    
    return false;
})
```

---

## 🧪 Testing Scenarios

### Test Case 1: Single Mode (Shift Only)
1. Login as Admin
2. Buka **Pengaturan Organisasi**
3. Set mode: ✅ Shift, ❌ Working Hours
4. Simpan
5. Buka form tambah karyawan
6. **Expected:**
   - Field "Jenis Kerja" tidak muncul
   - Field "Shift" muncul & required
   - Tidak bisa simpan tanpa pilih shift

### Test Case 2: Single Mode (Working Hours Only)
1. Login as Admin
2. Buka **Pengaturan Organisasi**
3. Set mode: ❌ Shift, ✅ Working Hours
4. Simpan
5. Buka form tambah karyawan
6. **Expected:**
   - Field "Jenis Kerja" tidak muncul
   - Field "Shift" tidak muncul
   - Bisa simpan tanpa pilih shift
   - work_type tersimpan sebagai 'working_hours'

### Test Case 3: Dual Mode (Both Active)
1. Login as Admin
2. Buka **Pengaturan Organisasi**
3. Set mode: ✅ Shift, ✅ Working Hours
4. Simpan
5. Buka form tambah karyawan
6. **Expected:**
   - Field "Jenis Kerja" muncul & required
   - Jika pilih "Shift" → Field "Shift" muncul & required
   - Jika pilih "Working Hours" → Field "Shift" hilang
   - Validasi sesuai pilihan

---

## 🐛 Troubleshooting

### Field tidak muncul/hilang sesuai harapan?

**Cek:**
1. Pengaturan mode di Organization Settings
```sql
SELECT name, enabled_attendance_modes FROM organizations WHERE id = 1;
```

2. Clear cache
```bash
php artisan filament:cache-clear
php artisan optimize:clear
```

### Validation error "shift wajib diisi" meskipun field hidden?

**Solusi:**
- Pastikan `->dehydrated()` ada di field `work_type`
- Pastikan default value ter-set dengan benar

---

## 📚 Related Files

1. [`app/Filament/Resources/UserResource.php`](app/Filament/Resources/UserResource.php) - Form definition
2. [`app/Models/Organization.php`](app/Models/Organization.php) - Model dengan methods:
   - `isShiftBased()`
   - `isWorkingHoursBased()`
   - `getEnabledModes()`
3. [`app/Filament/Pages/OrganizationSettings.php`](app/Filament/Pages/OrganizationSettings.php) - Pengaturan admin

---

## 🎉 Status

✅ **IMPLEMENTED & TESTED**

Fitur sudah aktif dan siap digunakan di production.

---

**Dibuat:** 4 Januari 2026  
**Versi:** 1.0  
**Status:** Active
