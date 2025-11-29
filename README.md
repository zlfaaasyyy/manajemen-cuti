<p align="center"><a href="#" target="_blank"><img src="https://www.google.com/search?q=https://placehold.co/400x120/473C33/FFFFFF%3Ftext%3DE-LEAVE%2BSYSTEM" alt="E-Leave System Logo"></a></p>

<h1 align="center">Sistem Manajemen Cuti Karyawan</h1>
<h4 align="center">Dibangun dengan Laravel & Tailwind CSS</h4>

<p align="center">
<img src="https://www.google.com/search?q=https://img.shields.io/badge/Laravel-10.x%2B-red.svg" alt="Laravel Version">
<img src="https://www.google.com/search?q=https://img.shields.io/badge/Tailwind%2520CSS-v3-blue.svg" alt="Tailwind CSS">
<img src="https://www.google.com/search?q=https://img.shields.io/badge/STATUS-Complete-success.svg" alt="Project Status">
<img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
</p>

Tentang Sistem (About This Project)

Sistem ini dirancang untuk mendigitalisasi dan menyederhanakan proses pengajuan, verifikasi, dan persetujuan cuti karyawan di lingkungan perusahaan. Tujuannya adalah memastikan alur cuti yang transparan dan akurat, serta memfasilitasi manajemen kuota cuti tahunan.

Sistem ini dibangun di atas Laravel, memanfaatkan fitur-fitur intinya untuk efisiensi pengembangan dan sintaks yang elegan.

Fitur Utama Proyek

Manajemen Berbasis Role: Mendukung 4 peran: Admin, HRD, Ketua Divisi, dan User.

Perhitungan Kuota: Kuota cuti tahunan (default 12 hari) dikelola dan dikurangi secara realtime setelah cuti disetujui HRD.

Alur Verifikasi Bertingkat: Pengajuan harus disetujui oleh Ketua Divisi, sebelum disetujui final oleh HRD.

Manajemen Master Data: CRUD untuk User dan Divisi.

Peran Pengguna & Hak Akses

Peran

Deskripsi

Hak Akses Khusus

Admin

Pengelola sistem utama (Superuser).

CRUD Manajemen User & Divisi. Mengawasi sistem dan laporan global. (Tidak mengajukan cuti)

HRD

Bertanggung jawab atas persetujuan final.

Verifikasi Final (Approve/Reject), Laporan Cuti Global, Daftar Karyawan Cuti Bulan Ini. (Hanya 1 slot & tidak dapat dihapus)

Ketua Divisi

Bertanggung jawab atas verifikasi awal timnya.

Verifikasi Awal (Approve/Reject) untuk anggota tim. Memiliki kuota cuti pribadi.

User (Karyawan)

Karyawan biasa.

Mengajukan cuti, Melihat sisa kuota, Melacak riwayat pengajuan.

🛠️ Instalasi dan Setup

Ikuti langkah-langkah standar instalasi Laravel untuk menjalankan proyek ini di lingkungan lokal Anda.

Persyaratan

PHP >= 8.1

Composer

Node.js & NPM / Yarn

Database (MySQL/MariaDB)

Langkah-langkah

Clone Repository:

git clone [URL_REPOSITORY_ANDA]
cd nama-folder-proyek


Instal Dependensi PHP:

composer install


Buat File Environment (.env):

cp .env.example .env
# Edit file .env dan atur DB_DATABASE, DB_USERNAME, DB_PASSWORD.


Generate Application Key & Frontend:

php artisan key:generate
npm install
npm run dev


Jalankan Migrasi dan Storage Link:

php artisan migrate --seed
php artisan storage:link # PENTING untuk foto profil


Jalankan Server Lokal:

php artisan serve


Sistem akan tersedia di http://127.0.0.1:8000.

🔑 Akun Uji Coba

Gunakan kredensial yang Anda sediakan di file seeder:

Role

Email (Contoh)

Password (Contoh)

Admin

admin@app.com

password

HRD

hrd@app.com

password

License

Proyek ini dirilis di bawah lisensi MIT.
