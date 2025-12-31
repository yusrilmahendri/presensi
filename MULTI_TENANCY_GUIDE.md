# Sistem Multi-Tenancy dengan Super Admin

Sistem presensi sekarang mendukung multi-tenancy dengan Super Admin yang dapat mengelola multiple organizations.

## Struktur Role

### 1. **Super Admin**
- Tidak terikat ke organization manapun
- Dapat melihat dan mengelola semua organizations
- Dapat membuat dan mengelola admin untuk setiap organization
- Login: `superadmin@presensi.com` / `password`

### 2. **Admin** (Per Organization)
- Terikat ke satu organization
- Hanya dapat melihat data dari organization mereka
- Mengelola karyawan, shift, lokasi absen, dll di organization mereka
- Setiap organization bisa memiliki multiple admin

### 3. **Karyawan** (Per Organization)
- Terikat ke satu organization
- Hanya dapat absen dan mengajukan izin
- Hanya melihat data mereka sendiri

## Database Changes

### Tabel Baru:
- **organizations**: Menyimpan data UMKM, Instansi, Perusahaan, dll

### Tabel yang Diupdate:
- **users**: Ditambahkan `organization_id` dan role `super_admin`
- **shifts**: Ditambahkan `organization_id`
- **attendance_locations**: Ditambahkan `organization_id`
- **attendances**: Ditambahkan `organization_id`
- **leaves**: Ditambahkan `organization_id`

## Cara Kerja

### 1. Super Admin Creates Organization
```
1. Login sebagai super admin
2. Buka menu "Organizations"
3. Klik "New Organization"
4. Isi detail:
   - Name: "UMKM Warung Makan A"
   - Type: UMKM / Instansi / Perusahaan
   - Email, Phone, Address (opsional)
   - Max Users: Jumlah maksimal user (default 50)
5. Save
```

### 2. Super Admin Creates Admin untuk Organization
```
1. Buka menu "Karyawan" (Users)
2. Klik "New User"
3. Isi detail:
   - Name: "Admin UMKM A"
   - Email: "admin.umkma@example.com"
   - Organization: Pilih "UMKM Warung Makan A"
   - Role: Admin
   - Password: Set password
4. Save
```

### 3. Admin Manages Their Organization
```
1. Login sebagai admin organization
2. Admin hanya melihat data dari organization mereka:
   - Karyawan yang terdaftar di organization mereka
   - Shift yang dibuat untuk organization mereka
   - Data absensi karyawan organization mereka
   - Lokasi absen organization mereka
   - Pengajuan izin dari karyawan organization mereka
```

### 4. Data Isolation
Setiap data secara otomatis di-filter berdasarkan organization:
- Admin UMKM A **TIDAK BISA** melihat data UMKM B
- Admin Instansi X **TIDAK BISA** melihat data Instansi Y
- Karyawan hanya bisa absen di lokasi organization mereka

## Fitur Auto-Scope

Sistem menggunakan **Global Scope** untuk otomatis filter data:
- Ketika admin login → otomatis filter semua data by organization_id
- Ketika create data baru → otomatis set organization_id dari user login
- Super admin → tidak ada filter, bisa lihat semua

## Contoh Use Case

### UMKM Warung Makan A
- Admin: 2 orang (Pemilik & Manager)
- Karyawan: 10 orang (Kasir, Koki, Pelayan)
- Lokasi Absen: 1 (di warung)
- Shift: 2 (Pagi & Sore)

### UMKM Toko Roti B
- Admin: 1 orang (Pemilik)
- Karyawan: 5 orang
- Lokasi Absen: 1 (di toko)
- Shift: 1 (Full Day)

### Instansi Pemerintahan X
- Admin: 3 orang (Kabag Kepegawaian, Staff HRD)
- Karyawan: 100 orang (PNS)
- Lokasi Absen: 3 (Gedung A, B, C)
- Shift: 1 (Jam Kantor)

**Data masing-masing organization TERISOLASI sempurna!**

## Migration Commands

```bash
# Sudah dijalankan:
php artisan migrate
php artisan db:seed --class=SuperAdminSeeder

# Jika ingin rollback:
php artisan migrate:rollback --step=3
```

## Next Steps

1. ✅ Login sebagai super admin
2. ✅ Buat organization pertama
3. ✅ Buat admin untuk organization tersebut
4. ✅ Login sebagai admin → test data isolation
5. ✅ Buat karyawan untuk organization
6. ✅ Test absensi dan pengajuan izin

## Security Notes

- Organization ID disimpan di setiap record
- Global scope otomatis filter berdasarkan organization
- Middleware akan ditambahkan untuk extra security
- Super admin bypass semua scope untuk management purposes

## Limitations

- Setiap organization punya max_users (default 50)
- Super admin HARUS membuat organization dulu sebelum buat admin
- Admin tidak bisa pindah organization (harus dibuat user baru)
- Karyawan tidak bisa transfer antar organization
