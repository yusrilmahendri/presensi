# 📖 Panduan Admin: Mengatur Mode Absensi

## 🎯 Langkah-Langkah

### 1. Login ke Panel Admin
- Akses: `localhost:8000/admin` (atau domain Anda)
- Login dengan akun **Admin** atau **Super Admin**

### 2. Buka Menu Pengaturan Organisasi

#### Cara 1: Via Sidebar (Untuk Admin Biasa) ⭐ RECOMMENDED
```
Sidebar → Pengaturan → Pengaturan Organisasi
```

#### Cara 2: Via Menu Bisnis (Untuk Super Admin)
```
Sidebar → Manajemen Super Admin → Bisnis → Edit Organisasi
```

### 3. Scroll ke "Pengaturan Mode Absensi"
Anda akan melihat section khusus untuk pengaturan mode absensi

#### Opsi 1: Mode Shift (Default)
```
✅ Pilih: "🕒 Berbasis Shift"
💡 Cocok untuk: Pabrik, Retail, Kantor dengan jam tetap
```

**Cara Kerja:**
- Karyawan check-in sesuai jam shift
- Ada validasi keterlambatan
- Status: Late, On Time, Early

---

#### Opsi 2: Mode Jam Kerja (Flexible)
```
✅ Pilih: "⏰ Berbasis Jam Kerja"
💡 Cocok untuk: Startup, Remote Work, Creative Agency
```

**Pengaturan Tambahan:**

| Field | Default | Keterangan |
|-------|---------|-----------|
| Jam Kerja Minimum | 8 jam | Minimal jam kerja sebelum bisa checkout |
| Grace Period | 2 jam | Toleransi sebelum dihitung lembur |

**Cara Kerja:**
- Karyawan bebas check-in jam berapa saja
- Checkout dibatasi setelah bekerja minimal X jam
- Lembur otomatis jika melebihi (min_hours + grace_period)

**Contoh Real:**
```
⚙️ Setting: Min 8 jam, Grace 2 jam
→ Maksimal 10 jam sebelum lembur

📅 Karyawan check-in jam 09:00
• 15:00 (6 jam) ❌ Ditolak! Kurang 2 jam lagi
• 17:00 (8 jam) ✅ Boleh checkout
• 19:00 (10 jam) ✅ Boleh checkout, belum lembur
• 20:00 (11 jam) ✅ Checkout + 1 jam lembur otomatis
```

### 5. Preview Otomatis
Saat mengatur, sistem akan menampilkan:
- ✅ Penjelasan cara kerja
- ✅ Contoh kasus nyata
- ✅ Tips penggunaan

### 6. Simpan
Klik tombol **"Simpan"** di bagian bawah form

---

## 📊 Melihat Mode Aktif

Di halaman tabel Organizations, Anda bisa:
- ✅ Lihat kolom **"Mode Absensi"**
- ✅ Filter organisasi berdasarkan mode
- ✅ Badge warna:
  - 🔵 **Shift** = Mode Shift
  - 🟢 **Jam Kerja** = Mode Working Hours

---

## ⚠️ Hal Penting

1. **Perubahan Langsung Berlaku**
   - Setelah save, mode langsung aktif
   - Tidak perlu restart server

2. **Backward Compatible**
   - Organisasi existing tetap pakai mode Shift
   - Tidak mengubah data yang sudah ada

3. **Validasi Ketat**
   - Min working hours: 1-12 jam
   - Grace period: 0-4 jam
   - Semua input sudah divalidasi

4. **Lembur Otomatis**
   - Sistem otomatis catat overtime
   - Admin tinggal approve/reject
   - Lihat di menu "Laporan Overtime"

---

## 💡 Tips Pemilihan Mode

### Pilih Mode Shift Jika:
- ✅ Jam kerja pasti (contoh: 08:00-17:00)
- ✅ Perlu kontrol keterlambatan ketat
- ✅ Budaya kerja formal
- ✅ Industri: Pabrik, Retail, Healthcare

### Pilih Mode Jam Kerja Jika:
- ✅ Fleksibilitas jam kerja
- ✅ Remote work / WFH
- ✅ Fokus pada produktivitas, bukan jam
- ✅ Industri: IT, Startup, Creative, Consulting

---

## 🆘 Troubleshooting

**Q: Karyawan complain tidak bisa checkout?**
- A: Cek mode working_hours → belum mencapai jam minimum

**Q: Lembur tidak tercatat?**
- A: Pastikan checkout melebihi (min + grace period)
- A: Cek menu "Laporan Overtime" → status pending

**Q: Ingin ubah mode di tengah jalan?**
- A: Bisa! Langsung edit dan save
- A: Tidak akan rusak data attendance yang lama

**Q: Berapa setting ideal?**
- A: Min 8 jam, Grace 2 jam (total max 10 jam)
- A: Sesuaikan dengan kebijakan perusahaan

---

## 📞 Support

Jika ada kendala, hubungi tim IT atau lihat dokumentasi lengkap di:
- `FITUR_MODE_ABSENSI.md`

**Update**: 26 Desember 2025
