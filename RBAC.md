# Role-Based Access Control (RBAC) Documentation

## Overview
Sistem ini mengimplementasikan RBAC menggunakan Laravel Policies untuk kontrol akses yang granular dan terstruktur.

## Roles

### 1. Super Admin (`super_admin`)
**Akses Penuh:**
- ✅ **Organizations** - CRUD semua bisnis (UMKM, Instansi, Perusahaan)
- ✅ **Admin Accounts** - CRUD admin bisnis
- ✅ **Dashboard** - Statistik bisnis dan admin

**Tidak Bisa Akses:**
- ❌ Karyawan management
- ❌ Shift management
- ❌ Lokasi absen
- ❌ Data absensi
- ❌ Pengajuan izin

**Login:**
- URL: `/admin/login` atau `/login`
- Credentials: username/email + password

---

### 2. Admin Bisnis (`admin`)
**Akses Penuh (dalam organization):**
- ✅ **Karyawan** - CRUD karyawan di bisnis mereka
- ✅ **Shift** - CRUD shift
- ✅ **Lokasi Absen** - CRUD lokasi
- ✅ **Data Absensi** - View, edit, delete absensi
- ✅ **Pengajuan Izin** - View, approve/reject izin karyawan
- ✅ **Dashboard** - Statistik absensi dan izin

**Tidak Bisa Akses:**
- ❌ Organizations management
- ❌ Admin accounts lain
- ❌ Data di luar organization mereka

**Login:**
- URL: `/admin/login` atau `/login`
- Credentials: username/email + password

---

### 3. Karyawan (`karyawan`)
**Akses:**
- ✅ **Dashboard** - Lihat status absensi sendiri
- ✅ **Absensi** - Check in/out via GPS
- ✅ **Pengajuan Izin** - Submit izin (sakit, cuti, dll)
- ✅ **Profile** - Update profile sendiri
- ✅ **Export** - Export data absensi sendiri (Excel/PDF)

**Login:**
- URL: `/login`
- Credentials: NIK/NIP/email + password

---

## Policies Implementation

### OrganizationPolicy
```php
- viewAny()   → Super Admin only
- view()      → Super Admin only
- create()    → Super Admin only
- update()    → Super Admin only
- delete()    → Super Admin only (jika tidak ada users)
```

### UserPolicy
```php
- viewAny()   → Super Admin (admin) | Admin (karyawan)
- view()      → Berdasarkan organization & role
- create()    → Super Admin (admin) | Admin (karyawan)
- update()    → Tidak bisa update diri sendiri
- delete()    → Tidak bisa delete diri sendiri atau super admin
```

### ShiftPolicy
```php
- viewAny()   → Admin only (not super admin)
- view()      → Admin dalam organization yang sama
- create()    → Admin only
- update()    → Admin dalam organization yang sama
- delete()    → Admin (jika shift tidak digunakan user)
```

### AttendanceLocationPolicy
```php
- viewAny()   → Admin only (not super admin)
- view()      → Admin dalam organization yang sama
- create()    → Admin only
- update()    → Admin dalam organization yang sama
- delete()    → Admin (jika lokasi tidak ada attendances)
```

### AttendancePolicy
```php
- viewAny()   → Admin only (not super admin)
- view()      → Admin dalam organization yang sama
- create()    → Admin only
- update()    → Admin dalam organization yang sama
- delete()    → Admin dalam organization yang sama
```

### LeavePolicy
```php
- viewAny()   → Admin only (not super admin)
- view()      → Admin dalam organization yang sama
- create()    → Admin only
- update()    → Admin dalam organization yang sama
- delete()    → Admin dalam organization yang sama
- approve()   → Admin (hanya untuk leave dengan status pending)
```

---

## Gates

### Custom Gates
```php
Gate::define('manage-organizations')  → Super Admin only
Gate::define('manage-admins')        → Super Admin only
Gate::define('manage-employees')     → Admin only
Gate::define('manage-attendance')    → Admin only
Gate::define('approve-leaves')       → Admin only
```

### Usage Example
```php
// Di Controller
if (Gate::allows('manage-organizations')) {
    // Aksi untuk manage organizations
}

// Di Blade
@can('manage-admins')
    <!-- Tombol tambah admin -->
@endcan

// Di Resource
public static function canViewAny(): bool
{
    return Gate::allows('manage-employees');
}
```

---

## Data Isolation

### Organization-based Scoping
Semua model (kecuali Organization dan User dengan role super_admin) otomatis di-filter berdasarkan `organization_id` menggunakan **BelongsToOrganization Trait**.

```php
// Global Scope otomatis aktif
Shift::all();  // Hanya shift dari organization user yang login

// Bypass scope (hanya untuk super admin)
Shift::withoutGlobalScope('organization')->get();
```

### Proteksi Delete
- **Organizations**: Tidak bisa dihapus jika memiliki users
- **Shift**: Tidak bisa dihapus jika digunakan oleh users
- **AttendanceLocation**: Tidak bisa dihapus jika memiliki attendances
- **User**: Tidak bisa hapus diri sendiri atau super admin

---

## Security Rules

### Super Admin
1. ✅ Tidak terikat ke organization (`organization_id = null`)
2. ✅ Bypass semua organization scopes
3. ✅ Hanya bisa manage organizations & admin accounts
4. ❌ Tidak bisa dihapus oleh siapapun
5. ❌ Tidak bisa edit oleh admin biasa

### Admin Bisnis
1. ✅ Terikat ke satu organization
2. ✅ Hanya lihat data dalam organization mereka
3. ✅ Bisa manage karyawan dalam organization
4. ❌ Tidak bisa edit/delete diri sendiri
5. ❌ Tidak bisa akses data organization lain

### Karyawan
1. ✅ Terikat ke satu organization
2. ✅ Hanya akses data diri sendiri
3. ✅ Submit absensi dan izin
4. ❌ Tidak bisa akses panel admin

---

## Testing RBAC

### Test Super Admin Access
```bash
# Login sebagai super admin
Email: superadmin@presensi.com
Password: Bismillah@1

# Cek akses:
- ✅ Menu "Bisnis" visible
- ✅ Menu "Admin Bisnis" visible
- ❌ Menu karyawan, shift, lokasi, absensi TIDAK visible
```

### Test Admin Access
```bash
# Login sebagai admin bisnis
# Cek akses:
- ✅ Menu karyawan, shift, lokasi visible
- ✅ Hanya lihat data organization sendiri
- ❌ Menu "Bisnis" TIDAK visible
- ❌ Tidak bisa edit admin lain
```

### Test Karyawan Access
```bash
# Login sebagai karyawan di /login
# Cek akses:
- ✅ Dashboard karyawan
- ✅ Absensi check in/out
- ✅ Pengajuan izin
- ❌ Tidak bisa akses /admin
```

---

## Troubleshooting

### Policy Tidak Bekerja
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Gate Tidak Terdaftar
Cek `App\Providers\AppServiceProvider::boot()` - pastikan semua policies dan gates sudah didaftarkan.

### Unauthorized Access
Pastikan:
1. User memiliki role yang benar
2. Organization_id terisi dengan benar
3. Policy method return true untuk action tersebut

---

## Best Practices

1. **Selalu gunakan Policy** untuk authorization logic
2. **Gunakan Gates** untuk action yang tidak terikat ke model tertentu
3. **Test RBAC** setelah setiap perubahan
4. **Clear cache** setelah update policy
5. **Log unauthorized attempts** untuk audit trail

---

## Future Enhancements

Possible improvements:
- [ ] Audit log untuk semua actions
- [ ] Permission-based access (lebih granular dari role)
- [ ] API rate limiting per role
- [ ] Two-factor authentication untuk super admin
- [ ] Session timeout berbeda per role
