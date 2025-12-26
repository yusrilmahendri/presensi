# 🔄 FLOW PENGATURAN MODE ABSENSI

## 📋 Alur Kerja

### 1️⃣ Super Admin: Pilih Mode Absensi

**Lokasi:** Menu **Bisnis** → Edit Organization

**Yang Bisa Dilakukan:**
- ✅ Pilih mode: **Shift** atau **Working Hours**
- ✅ Atur semua konfigurasi organisasi
- ✅ Kelola semua organisasi di sistem

**Langkah:**
```
1. Login sebagai Super Admin
2. Sidebar → Manajemen Super Admin → Bisnis
3. Klik Edit pada organisasi
4. Scroll ke "Pengaturan Mode Absensi"
5. Pilih mode:
   - 🕒 Berbasis Shift
   - ⏰ Berbasis Jam Kerja
6. Jika pilih Working Hours → atur min hours & grace period
7. Simpan
```

---

### 2️⃣ Admin Biasa: Atur Konfigurasi Sesuai Mode

**Lokasi:** Menu **Pengaturan** → **Pengaturan Organisasi**

**Yang Bisa Dilakukan:**
- ❌ **TIDAK bisa** pilih/ganti mode (diatur Super Admin)
- ✅ **Bisa** atur konfigurasi sesuai mode aktif
- ✅ Update info organisasi (email, phone, alamat)

---

## 🎯 Berdasarkan Mode yang Dipilih Super Admin

### Jika Mode = **SHIFT** 🕒

**Tampilan untuk Admin:**
```
╔════════════════════════════════════════╗
║ Mode Absensi Aktif                     ║
║ 🕒 Berbasis Shift                      ║
║                                        ║
║ Karyawan check-in sesuai jadwal shift  ║
║ yang ditentukan.                       ║
╠════════════════════════════════════════╣
║ ℹ️ Mode ini diatur oleh Super Admin    ║
╠════════════════════════════════════════╣
║                                        ║
║ 📖 Informasi Mode Shift                ║
║                                        ║
║ Konfigurasi shift diatur di menu:     ║
║ → Pengaturan → Shift                   ║
║                                        ║
║ Di sana Anda bisa:                     ║
║ • Membuat jadwal shift baru           ║
║ • Mengatur jam masuk dan keluar       ║
║ • Menetapkan shift ke karyawan        ║
║                                        ║
║ Status yang dihitung otomatis:        ║
║ ✅ On Time (tepat waktu)              ║
║ ⏰ Late (terlambat)                   ║
║ 🌅 Early (lebih awal)                 ║
║ 💼 Overtime (lembur)                  ║
╚════════════════════════════════════════╝
```

**Yang Bisa Admin Lakukan:**
- ✅ Edit email, phone, alamat organisasi
- ✅ Lihat info bahwa mode Shift aktif
- ✅ Diarahkan ke menu Shift untuk konfigurasi
- ❌ **TIDAK ada** field jam kerja minimum/grace period

---

### Jika Mode = **WORKING HOURS** ⏰

**Tampilan untuk Admin:**
```
╔════════════════════════════════════════╗
║ Mode Absensi Aktif                     ║
║ ⏰ Berbasis Jam Kerja                  ║
║                                        ║
║ Karyawan bebas check-in jam berapa     ║
║ saja, checkout setelah mencapai jam    ║
║ kerja minimum.                         ║
╠════════════════════════════════════════╣
║ ℹ️ Mode ini diatur oleh Super Admin    ║
╠════════════════════════════════════════╣
║                                        ║
║ Jam Kerja Minimum  │  Grace Period     ║
║ ┌────────────┐     │  ┌────────────┐   ║
║ │ 8 jam/hari │     │  │ 2 jam      │   ║
║ └────────────┘     │  └────────────┘   ║
║                                        ║
║ 📊 Preview Konfigurasi                 ║
║                                        ║
║ Konfigurasi Aktif:                     ║
║ • Jam kerja minimum: 8 jam            ║
║ • Grace period: 2 jam                 ║
║ • Maksimal sebelum lembur: 10 jam     ║
║                                        ║
║ Contoh Kasus:                          ║
║ Karyawan check-in jam 08:00            ║
║                                        ║
║ • 14:00 (6 jam) → ❌ Ditolak          ║
║ • 16:30 (8.5 jam) → ✅ Boleh checkout ║
║ • 18:00 (10 jam) → ✅ Belum lembur    ║
║ • 19:00 (11 jam) → ✅ + 1 jam lembur  ║
╚════════════════════════════════════════╝
```

**Yang Bisa Admin Lakukan:**
- ✅ Edit email, phone, alamat organisasi
- ✅ Lihat info bahwa mode Working Hours aktif
- ✅ **Atur jam kerja minimum** (1-12 jam)
- ✅ **Atur grace period** (0-4 jam)
- ✅ Lihat preview otomatis dengan contoh kasus

---

## 🎭 Perbedaan Akses

| Fitur | Super Admin | Admin Biasa |
|-------|-------------|-------------|
| **Pilih Mode** | ✅ Ya | ❌ Tidak |
| **Lihat Mode Aktif** | ✅ Ya | ✅ Ya |
| **Atur Info Organisasi** | ✅ Ya | ✅ Ya |
| **Atur Jam Kerja (jika working_hours)** | ✅ Ya | ✅ Ya |
| **Ganti Mode** | ✅ Ya | ❌ Tidak |
| **Akses Menu Bisnis** | ✅ Ya | ❌ Tidak |

---

## 📍 Lokasi Menu

### Super Admin
```
📁 Manajemen Super Admin
   🏢 Bisnis  ← PILIH MODE DI SINI
      ├─ Lihat semua organisasi
      ├─ Edit organisasi
      └─ Pilih mode: Shift atau Working Hours

📁 Pengaturan
   ⚙️ Pengaturan Organisasi  ← ATUR KONFIGURASI
      ├─ Lihat mode aktif (read-only di bagian atas)
      └─ Atur konfigurasi sesuai mode
```

### Admin Biasa
```
📁 Pengaturan
   ⚙️ Pengaturan Organisasi  ← ATUR KONFIGURASI SAJA
      ├─ Lihat mode aktif (tidak bisa ganti)
      └─ Atur konfigurasi sesuai mode yang dipilih Super Admin

   🕒 Shift  ← (jika mode = shift)
      └─ Atur jadwal shift
```

---

## 🔒 Keamanan & Validasi

### Validasi Backend
1. ✅ Admin biasa **tidak bisa** update `attendance_mode`
2. ✅ Field konfigurasi hanya muncul jika mode sesuai
3. ✅ Validasi min/max value untuk semua input
4. ✅ Mode default: `shift` (backward compatible)

### UI/UX
1. ✅ Info jelas: "Mode ini diatur oleh Super Admin"
2. ✅ Field hanya muncul untuk mode yang aktif
3. ✅ Preview real-time untuk working hours
4. ✅ Helper text yang jelas
5. ✅ Title halaman dinamis sesuai mode

---

## 💡 Contoh Skenario

### Skenario 1: Organisasi Mode Shift

**Super Admin:**
1. Buka menu Bisnis → Edit "PT ABC"
2. Pilih mode: "🕒 Berbasis Shift"
3. Simpan

**Admin PT ABC:**
1. Buka Pengaturan → Pengaturan Organisasi
2. Lihat: "Mode Shift aktif"
3. Tidak ada field jam kerja minimum
4. Diarahkan ke menu Shift untuk atur jadwal
5. Bisa update email/phone organisasi

---

### Skenario 2: Organisasi Mode Working Hours

**Super Admin:**
1. Buka menu Bisnis → Edit "Startup XYZ"
2. Pilih mode: "⏰ Berbasis Jam Kerja"
3. Set min hours: 8, grace: 2
4. Simpan

**Admin Startup XYZ:**
1. Buka Pengaturan → Pengaturan Organisasi
2. Lihat: "Mode Working Hours aktif"
3. Ada field:
   - Jam Kerja Minimum (bisa edit)
   - Grace Period (bisa edit)
4. Lihat preview otomatis
5. Ubah min hours jadi 7, grace 3
6. Simpan → berhasil!

---

### Skenario 3: Super Admin Ganti Mode

**Super Admin:**
1. Organisasi awalnya mode Shift
2. Ganti ke Working Hours
3. Set konfigurasi awal
4. Simpan

**Admin:**
1. Refresh halaman
2. Lihat perubahan otomatis
3. Sekarang bisa atur jam kerja minimum
4. Field shift hilang

---

## 🚀 Keuntungan Arsitektur Ini

### Untuk Super Admin
- ✅ Kontrol penuh atas mode organisasi
- ✅ Bisa set konfigurasi default
- ✅ Manajemen terpusat

### Untuk Admin Biasa
- ✅ Interface sederhana (hanya lihat yang relevan)
- ✅ Tidak bingung dengan opsi yang tidak perlu
- ✅ Fokus pada konfigurasi operasional
- ✅ Tidak bisa salah pilih mode

### Untuk Karyawan
- ✅ Sistem konsisten
- ✅ Tidak ada kebingungan
- ✅ Absensi sesuai mode yang ditentukan

---

## 📞 FAQ

**Q: Admin bisa ganti mode?**
A: Tidak. Hanya Super Admin yang bisa pilih/ganti mode.

**Q: Apa yang terjadi jika Super Admin ganti mode?**
A: Admin langsung lihat perubahan. Field yang muncul otomatis menyesuaikan.

**Q: Apakah data hilang jika ganti mode?**
A: Tidak. Data attendance tetap aman. Hanya konfigurasi yang berubah.

**Q: Admin bisa lihat mode apa yang aktif?**
A: Ya, di bagian atas halaman ada info mode aktif.

**Q: Jika mode Shift, apakah ada konfigurasi?**
A: Konfigurasi shift di menu terpisah (Pengaturan → Shift).

---

**Created**: 26 Desember 2025
**Version**: 3.0.0 (Permission-based configuration)
