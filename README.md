<div align="center">

# ☕ ABS Cafe & Accessories — Enterprise Point of Sale & Customer Platform

**Platform Web Manajemen Kafe Terintegrasi (Self-Order Pelanggan, POS Kasir Cerdas, & Back-Office Analytics)**  
*Modern, Cepat, Responsif Mobile & Desktop, dan Dirancang Khusus untuk Operasional Kafe Profesional.*

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Status](https://img.shields.io/badge/Production-Ready-2E7D32?style=for-the-badge)](#)

---

### 📍 Profil Kafe
**ABS Cafe (Accessories & Cafe)**  
Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219  
📞 **Kontak / WhatsApp:** 0896-2010-2010  
⏰ **Jam Operasional:** Setiap Hari, 09.00 - 23.00 WIB (*Last Order: 22.00 WIB*)

</div>

---

## 🌟 Gambaran Umum Sistem

**ABS Cafe Web App** adalah ekosistem digital *all-in-one* yang menggabungkan kemudahan pemesanan mandiri oleh pelanggan (*Customer Web Ordering & Dine-In Table QR*) dengan keandalan operasional kasir (*Cashier POS & Shift Management*) serta ketajaman laporan bisnis untuk pemilik kafe (*Owner & Admin Management*).

Didesain dengan pendekatan visual **Warm Coffee Shop Aesthetic** (kombinasi warna coklat espresso, karamel emas, dan latar krem hangat), aplikasi ini memberikan pengalaman pengguna yang sangat ramah pengguna (*user-friendly*), cepat, serta optimal diakses lewat smartphone maupun desktop monitor POS.

---

## 🚀 Fitur-Fitur Utama & Pembaruan Terkini

### 1. 📱 Portal Pelanggan (Customer Web & QR Self-Order)
- **Menu Favorit Otomatis (Penjualan Terbanyak Harian):**
  - Pelanggan langsung disajikan **Top 3 Menu Favorit** yang diperbarui otomatis secara real-time berdasarkan penjualan riil (`sold_count` tertinggi) — **100% tanpa manipulasi input manual**.
- **Pemesanan QR Meja (Dine-In) & Take-Away:**
  - Pelanggan cukup scan QR meja untuk langsung membuka katalog digital dan memasukkan pesanan.
  - Ringkasan keranjang belanja instan dengan penghitungan pajak otomatis (PB1 10%).
- **Sistem Reservasi Meja Online:**
  - Booking meja lengkap dengan pemilihan jam, tanggal, jumlah tamu, dan area (*Indoor / Outdoor / Lantai 2*).
- **Pembayaran Tunai Kasir (Cash Only):**
  - Alur transaksi ringkas: setelah pesan di web, pelanggan menerima kode QR tiket pesanan untuk langsung diverifikasi dan dibayar di meja kasir.

### 2. ⚡ Portal Kasir Cerdas (Modern POS & Cashier Portal)
- **Pergantian Shift Tanpa Logout (Seamless Cashier Switching):**
  - **Tidak ada lagi blokir "Shift Belum Dibuka" atau keharusan logout:** Shift laci kas kini berjalan otomatis terus menerus di latar belakang (*continuous background shift*).
  - Kasir cukup mengganti **Nama Pemegang Akun Kasir** melalui dropdown di Topbar atau Drawer navigasi samping.
  - Daftar nama kasir diambil langsung dari master staf aktif yang didaftarkan oleh Administrator.
  - Struk pembayaran, log transaksi, dan timeline pesanan otomatis mencatat nama kasir yang sedang bertugas saat itu.
- **POS Cepat & Scan QR Tiket:**
  - Kasir dapat membuat pesanan manual langsung untuk customer yang datang langsung ke meja bar.
  - Fitur **Scan QR Pesanan Pelanggan** untuk verifikasi instan status pesanan menjadi *"Sedang Diproses"* dapur/bar.
- **Pencetakan Nota Termal:**
  - Cetak struk kasir (*thermal receipt*) standar 58mm/80mm dengan pratinjau langsung.
- **Catatan Kas Laci (Cash In / Cash Out):**
  - Kasir dapat mencatat penyesuaian uang modal kas kecil tanpa mengganggu kelancaran transaksi.

### 3. 📊 Portal Owner (Eksekutif & Analisis Finansial)
- **Executive Dashboard:**
  - Pantau total omset kotor (*Gross Revenue*), total pesanan, rata-rata nilai transaksi (*Average Order Value*), dan tingkat okupansi meja hari ini.
- **Laporan Penjualan & Laba/Rugi:**
  - Grafik tren pendapatan 7 hari & bulanan.
  - Pemecahan penjualan per kategori makanan/minuman dan kinerja staf kasir.
- **Ekspor Laporan:**
  - Ekspor riwayat transaksi kasir ke format CSV / Excel / PDF untuk pembukuan akuntansi kafe.

### 4. ⚙️ Portal Administrator (Manajemen & Konfigurasi Penuh)
- **Manajemen Katalog Menu Dinamis:**
  - Tambah, edit, upload foto, set harga, kelola varian, dan duplikasi menu dalam hitungan detik.
  - Switch ketersediaan cepat (*Tersedia / Habis*) dan label *Best Seller*.
  - Mekanisme **Soft-Delete** yang aman: menu yang dihapus tidak merusak riwayat transaksi lampau.
- **Kategori & Denah Meja (QR Generator):**
  - Pengelompokan kategori (Coffee Base, Milk Based, Snack, Main Course, dll.).
  - Denah nomor meja lengkap dengan kapasitas, area, dan generator QR code meja otomatis yang siap cetak.
- **Manajemen Staf & Hak Akses:**
  - Kelola pengguna dengan login berbasis *Username* (aman & praktis tanpa email).
  - Reset kata sandi staf instan oleh Admin.
- **Pengaturan Bisnis Terpusat:**
  - Konfigurasi profil kafe, persentase pajak PB1, format teks header/footer nota struk, dan banner beranda.
- **Audit Log Aktivitas:**
  - Rekaman jejak audit sistem: mencatat siapa, waktu, IP address, dan aktivitas operasional penting.

---

## 🔐 Kredensial Akun Resmi Operasional

Aplikasi telah dibersihkan dari seluruh data dummy uji coba dan disiapkan akun resmi siap pakai:

| Peran | Username | Kata Sandi | Hak Akses Utama |
|---|---|---|---|
| **Administrator** | `admin` | `admin123` | Konfigurasi penuh, staf, katalog menu, meja, log sistem |
| **Owner** | `owner` | `owner123` | Laporan finansial, analitik omset, evaluasi operasional |
| **Kasir** | `kasir` | `kasir123` | POS transaksi, bayar tunai, scan tiket QR, pilih nama staf bertugas |

> 💡 **Alur Pergantian Shift Kasir:** Kasir login satu kali dengan akun `kasir / kasir123`. Ketika pergantian shift tiba, kasir berikutnya tidak perlu keluar (*logout*) — cukup klik dropdown kasir di bagian atas layar dan pilih nama kasir yang bertugas!

---

## 🛠️ Arsitektur & Teknologi

- **Backend Framework:** [Laravel 11](https://laravel.com) dengan arsitektur Service-Repository pattern untuk skalabilitas transaksi tinggi.
- **Database Engine:** MySQL 8.0+ / MariaDB dengan dukungan relasi InnoDB, foreign key constraints, dan soft-delete.
- **Frontend & Styling:** Blade Templating Engine, [Tailwind CSS 3](https://tailwindcss.com), Alpine.js, dan Lucide Icons.
- **Visual & Charts:** [Chart.js](https://www.chartjs.org/) untuk visualisasi performa penjualan.
- **Font Stack:** *Noto Serif* (heading kafe mewah) & *Poppins* (antarmuka bersih & mudah dibaca).

---

## 📦 Panduan Instalasi & Menjalankan Proyek

### 1. Kebutuhan Sistem
- PHP >= 8.2 (Ekstensi: `pdo_mysql`, `mbstring`, `gd`, `bcmath`, `curl`)
- MySQL >= 8.0 atau MariaDB >= 10.4
- Composer 2.x
- Node.js >= 18.x & NPM

### 2. Langkah Pemasangan (Localhost / Laragon / XAMPP)

1. **Clone repository:**
   ```bash
   git clone https://github.com/username/ABS_Cafe.git
   cd ABS_Cafe
   ```

2. **Pasang dependensi PHP & JavaScript:**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Konfigurasi Environment (`.env`):**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Pastikan pengaturan database di file `.env` sudah sesuai:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=abs_cafe
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Migrasi Database & Data Master Resmi:**
   ```bash
   php artisan migrate --seed
   ```
   *Atau import langsung file basis data resmi:*
   ```bash
   mysql -u root abs_cafe < db_abscafe.sql
   ```

5. **Jalankan Aplikasi:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat langsung diakses di peramban:
   - **Pelanggan (Katalog & Reservasi):** `http://127.0.0.1:8000`
   - **Portal Kasir:** `http://127.0.0.1:8000/kasir`
   - **Portal Admin:** `http://127.0.0.1:8000/admin`
   - **Portal Owner:** `http://127.0.0.1:8000/owner`

---

## 🧪 Jaminan Kualitas & Pengujian Otomatis

Aplikasi ini dilengkapi pengujian otomatis (*automated testing*) menyeluruh menggunakan PHPUnit untuk menjamin keandalan transaksi kasir, kalkulasi pajak, dan integritas database:

```bash
php artisan test
```

- **Feature Tests:** POS Kasir, Alur Pembayaran Tunai, Validasi Menu Soft-Delete, dan Algoritma Menu Terfavorit.
- **Database Parity Tests:** Menjamin skema tabel migrasi 100% konsisten dengan `db_abscafe.sql`.

---

## 👥 Pengembang & Hak Cipta

Dikembangkan dengan dedikasi penuh untuk kemajuan digitalisasi **ABS Cafe Tulungagung** oleh:  
**Tim Pengembang Fanverse (XI RPL 1, SMKN 1 Boyolangu)**  
*Bekerja sama secara profesional untuk menghadirkan solusi teknologi F&B modern.*

© 2026 ABS Cafe & Accessories. All Rights Reserved.
