# Sistem Manajemen Cuti Karyawan (E-Leave System)

Aplikasi web berbasis Laravel untuk mendigitalisasi proses pengajuan dan persetujuan cuti karyawan dengan alur multilevel yang rapi, transparan, dan akurat.

## 📌 Deskripsi Proyek
Sistem Manajemen Cuti Karyawan (E-Leave System) dikembangkan untuk membantu perusahaan mengelola proses cuti secara digital dan efisien. Dibangun menggunakan Laravel dan Tailwind CSS, sistem ini mendukung alur persetujuan dua tingkat, otomatisasi kuota, hingga pembuatan dokumen cuti berbentuk PDF. Tujuan utama mencakup pengelolaan kuota realtime, persetujuan berjenjang, dan penghapusan proses berbasis kertas.

## 🔐 Peran Pengguna & Fitur Utama
### Admin
- CRUD User & Divisi  
- Statistik global  
- Daftar karyawan baru (< 1 tahun)

### HRD
- Verifikasi final (Level 2)  
- Bulk approve/reject  
- Dashboard laporan  
- Tidak dapat dihapus

### Ketua Divisi
- Verifikasi awal (Level 1)  
- Dashboard tim  
- Kuota pribadi  
- Bisa mengajukan cuti pribadi

### User
- Pengajuan cuti  
- Melihat kuota realtime  
- Riwayat pengajuan

## ⚙️ Fungsionalitas Utama
| Modul | Deskripsi |
|-------|-----------|
| Pengajuan | Validasi overlap + hitung hari kerja |
| Kuota | Berkurang hanya setelah approved final |
| Verifikasi | Catatan wajib saat reject |
| PDF | Cetak Surat Cuti |
| Foto Profil | Replace otomatis foto lama |

## 🛠️ Instalasi & Setup (Full Dalam Satu Blok)
Semua langkah instalasi digabung menjadi satu alur tanpa dipisah agar mudah di-copy.

### **Langkah Setup**
**1. Clone Repositori**
```bash
git clone [URL_REPOSITORY_ANDA]
cd nama-folder-proyek
```

**2. Instal Dependensi Backend & Frontend**
```bash
composer install
npm install
```

**3. Konfigurasi Environment**
```bash
cp .env.example .env
php artisan key:generate

Edit file .env dan sesuaikan:
# DB_HOST
# DB_DATABASE
# DB_USERNAME
# DB_PASSWORD
```

**4. Migrasi Database + Seeder Default**
```bash
php artisan migrate --seed
```

**5. Setup Storage (Foto Profil)**
```bash
php artisan storage:link
```

**6. Jalankan Server Backend & Frontend**
```bash
npm run dev
php artisan serve
```

### Akses di: http://127.0.0.1:8000/
