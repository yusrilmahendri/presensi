# 🏢 Brand Dinamis - Mengikuti Nama Organization

## ✅ Fitur Berhasil Diimplementasikan!

Sistem presensi sekarang akan menampilkan nama usaha/organization dari admin yang login, bukan lagi "Sistem Presensi" sebagai default.

### Contoh:
- Jika admin dari **Minoru Coffee** login → Brand akan menjadi **"Minoru Coffee"**
- Jika admin dari **PT. Teknologi Maju** login → Brand akan menjadi **"PT. Teknologi Maju"**
- Jika user tidak memiliki organization → Fallback ke **"Sistem Presensi"**

---

## 📋 Perubahan yang Dilakukan

### 1. **Admin Panel (Filament)**
**File:** `app/Providers/Filament/AdminPanelProvider.php`

Brand name sekarang dinamis:
```php
->brandName(function () {
    $user = auth()->user();
    if ($user && $user->organization) {
        return $user->organization->name;
    }
    return 'Sistem Presensi';
})
```

**Tampil di:**
- Sidebar admin panel
- Header/navigation bar
- Browser tab title (auto dari Filament)

---

### 2. **Dashboard Karyawan**
**File:** `resources/views/karyawan/dashboard.blade.php`

**Perubahan:**
- Title: `Dashboard - {{ $user->organization->name ?? 'Sistem Presensi' }}`
- Navbar Brand: `{{ $user->organization->name ?? 'Sistem Presensi' }}`

---

### 3. **Profile Karyawan**
**File:** `resources/views/karyawan/profile.blade.php`

**Perubahan:**
- Title: `Profile - {{ $user->organization->name ?? 'Sistem Presensi' }}`
- Navbar Brand: `{{ $user->organization->name ?? 'Sistem Presensi' }}`

---

### 4. **Pengajuan Izin**
**File:** `resources/views/karyawan/leaves/index.blade.php`
**Controller:** `app/Http/Controllers/LeaveController.php`

**Perubahan:**
- Controller: Menambahkan `'user'` ke compact data
- Title: `Pengajuan Izin - {{ $user->organization->name ?? 'Sistem Presensi' }}`
- Navbar Brand: `{{ $user->organization->name ?? 'Sistem Presensi' }}`

---

### 5. **Halaman Absensi**
**File:** `resources/views/attendance/index.blade.php`

**Perubahan:**
- Title: `{{ $user->organization->name ?? 'Sistem Presensi' }}`
- Header: `{{ $user->organization->name ?? 'Sistem Presensi' }}`

---

## 🎯 Cara Kerja

### Relasi Database:
```
User → organization_id → Organization
```

Setiap user memiliki `organization_id` yang merujuk ke tabel `organizations`.

### Logic:
1. Sistem mengambil user yang sedang login
2. Mengakses relasi `$user->organization`
3. Jika ada organization, ambil `$user->organization->name`
4. Jika tidak ada, gunakan default `'Sistem Presensi'`

---

## 🔧 Cara Mengatur Organization

### Via Admin Panel:
1. Login sebagai Super Admin
2. Buka menu **Organizations** (Manajemen Super Admin)
3. Buat atau edit organization
4. Isi nama organization (contoh: "Minoru Coffee", "PT. ABC", dll)
5. Assign user ke organization tersebut

### Via Database:
```sql
-- Update organization name
UPDATE organizations 
SET name = 'Minoru Coffee' 
WHERE id = 1;

-- Assign user ke organization
UPDATE users 
SET organization_id = 1 
WHERE id = 2;
```

---

## 📱 Tampilan di Berbagai Tempat

### Admin Panel (Filament):
- ✅ Sidebar logo/brand
- ✅ Navigation header
- ✅ Browser tab title

### Dashboard Karyawan:
- ✅ Browser tab title
- ✅ Navbar brand

### Profile Karyawan:
- ✅ Browser tab title
- ✅ Navbar brand

### Pengajuan Izin:
- ✅ Browser tab title
- ✅ Navbar brand

### Halaman Absensi:
- ✅ Browser tab title
- ✅ Header judul

---

## 🎨 Customization

### Menambahkan Logo Organization:
Edit `AdminPanelProvider.php`:
```php
->brandLogo(function () {
    $user = auth()->user();
    if ($user && $user->organization && $user->organization->logo) {
        return asset('storage/' . $user->organization->logo);
    }
    return null;
})
```

### Menambahkan Tagline/Subtitle:
Di view blade, tambahkan:
```blade
<h2>{{ $user->organization->name ?? 'Sistem Presensi' }}</h2>
<p class="text-muted">{{ $user->organization->tagline ?? 'Solusi Manajemen Kehadiran' }}</p>
```

---

## ✅ Testing

### Test Case 1: Admin dengan Organization
1. Login sebagai admin yang sudah di-assign ke organization
2. Cek brand di sidebar → Harus muncul nama organization
3. Buka dashboard karyawan → Navbar harus tampil nama organization

### Test Case 2: Admin tanpa Organization
1. Login sebagai admin tanpa organization_id
2. Brand harus fallback ke "Sistem Presensi"

### Test Case 3: Karyawan
1. Login sebagai karyawan
2. Buka halaman absensi → Header harus tampil nama organization
3. Buka dashboard → Navbar harus tampil nama organization

---

## 🚀 Production Notes

- ✅ Tidak perlu migration baru (gunakan field existing)
- ✅ Backward compatible (fallback ke "Sistem Presensi")
- ✅ No breaking changes
- ✅ Works dengan multi-tenancy

---

## 📝 Files Modified

1. `/app/Providers/Filament/AdminPanelProvider.php`
2. `/app/Http/Controllers/LeaveController.php`
3. `/resources/views/karyawan/dashboard.blade.php`
4. `/resources/views/karyawan/profile.blade.php`
5. `/resources/views/karyawan/leaves/index.blade.php`
6. `/resources/views/attendance/index.blade.php`

---

**Created:** 01 Januari 2026
**Version:** 1.0
**Status:** ✅ Production Ready
