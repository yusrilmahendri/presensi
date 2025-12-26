# 🎯 CARA MENGAKSES PENGATURAN MODE ABSENSI

## Untuk Admin Biasa (Bukan Super Admin)

### 📍 Lokasi Menu
Menu pengaturan mode absensi sekarang ada di:

```
Sidebar → Pengaturan → Pengaturan Organisasi
```

### 🔍 Cara Mengakses

1. **Login ke panel admin** (`localhost:8000/admin`)
2. **Lihat sidebar kiri**
3. **Klik menu "Pengaturan"** (biasanya di bagian bawah)
4. **Klik "Pengaturan Organisasi"** (ikon ⚙️)

### 📋 Yang Bisa Diatur

Di halaman **Pengaturan Organisasi**, Anda bisa mengatur:

#### Section 1: Informasi Organisasi
- ✅ Email Bisnis
- ✅ No. Telepon
- ✅ Alamat

#### Section 2: Pengaturan Mode Absensi ⭐
- ✅ **Mode Absensi** (Dropdown)
  - 🕒 Berbasis Shift
  - ⏰ Berbasis Jam Kerja
  
- ✅ **Jam Kerja Minimum** (jika pilih Jam Kerja)
- ✅ **Grace Period** (jika pilih Jam Kerja)
- ✅ **Preview/Contoh** otomatis

---

## Untuk Super Admin

### Pilihan 1: Via Menu Bisnis (Kelola Semua Organisasi)
```
Sidebar → Manajemen Super Admin → Bisnis
→ Edit organisasi tertentu
→ Atur mode absensi
```

### Pilihan 2: Via Pengaturan Organisasi (Organisasi Sendiri)
```
Sidebar → Pengaturan → Pengaturan Organisasi
→ Atur mode absensi untuk organisasi sendiri
```

---

## 🖼️ Preview Menu

### Sebelum (Hanya ada Shift di Pengaturan):
```
📁 Pengaturan
   📍 Lokasi Absen
   📅 Hari Libur
   🕒 Shift          ← Ini hanya untuk atur jadwal shift
```

### Sesudah (Ada Pengaturan Organisasi):
```
📁 Pengaturan
   ⚙️ Pengaturan Organisasi  ← BARU! Di sini atur mode absensi
   📍 Lokasi Absen
   📅 Hari Libur
   🕒 Shift
```

---

## 📝 Langkah Lengkap Mengatur Mode Working Hours

### Step 1: Buka Menu
1. Login sebagai Admin
2. Sidebar → **Pengaturan** → **Pengaturan Organisasi**

### Step 2: Scroll ke "Pengaturan Mode Absensi"
Anda akan melihat form dengan:
- Dropdown "Mode Absensi"
- Helper text yang menjelaskan

### Step 3: Pilih Mode
Klik dropdown → Pilih **"⏰ Berbasis Jam Kerja"**

### Step 4: Atur Konfigurasi
Akan muncul 2 field tambahan:
- **Jam Kerja Minimum**: Masukkan 8 (atau sesuai kebutuhan)
- **Grace Period**: Masukkan 2 (atau sesuai kebutuhan)

### Step 5: Lihat Preview
Sistem otomatis menampilkan contoh:
```
✅ Karyawan bebas check-in jam berapa saja
✅ Checkout setelah bekerja minimal 8 jam
⏱️ Lembur otomatis jika lebih dari 10 jam

Contoh: Check-in jam 08:00
• Checkout jam 14:00 (6 jam) ❌ Ditolak!
• Checkout jam 16:30 (8.5 jam) ✅ Sukses
• Checkout jam 19:00 (11 jam) ✅ + 1 jam lembur
```

### Step 6: Simpan
Klik tombol **"💾 Simpan Pengaturan"** di bawah

---

## ⚠️ Troubleshooting

### Menu "Pengaturan Organisasi" Tidak Muncul?

**Solusi:**
1. Pastikan Anda login sebagai **Admin** atau **Super Admin** (bukan Karyawan)
2. Refresh browser (Ctrl+R atau Cmd+R)
3. Clear cache browser
4. Logout dan login ulang
5. Jalankan command:
   ```bash
   php artisan filament:cache-components
   php artisan optimize:clear
   ```

### Masih Tidak Muncul?

Cek di database apakah role Anda benar:
```sql
SELECT name, role FROM users WHERE id = YOUR_USER_ID;
```

Role yang bisa akses:
- ✅ `admin`
- ✅ `super_admin`
- ❌ `karyawan` (tidak bisa akses)

---

## 📞 Quick Reference

| Pertanyaan | Jawaban |
|------------|---------|
| **Di mana menu pengaturan?** | Sidebar → Pengaturan → Pengaturan Organisasi |
| **Siapa yang bisa akses?** | Admin dan Super Admin |
| **Apakah perlu restart server?** | Tidak, langsung aktif setelah save |
| **Bisa ubah mode di tengah jalan?** | Ya, bisa kapan saja |
| **Apakah data lama hilang?** | Tidak, aman |

---

## 🎓 Video Tutorial (Konsep)

```
1. Login → Admin panel muncul
2. Lihat sidebar kiri
3. Cari grup menu "Pengaturan"
4. Klik "Pengaturan Organisasi"
5. Scroll ke "Pengaturan Mode Absensi"
6. Pilih mode yang diinginkan
7. Atur konfigurasi (jika working hours)
8. Klik "Simpan Pengaturan"
9. ✅ Done! Mode aktif
```

---

**Update**: 26 Desember 2025
**Versi**: 2.0.0 (dengan menu Pengaturan Organisasi)
