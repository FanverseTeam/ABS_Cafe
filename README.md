# ABS Cafe ☕

**ABS Cafe** adalah proyek pengembangan aplikasi pemesanan menu berbasis web yang bekerja sama dengan ABS ACC & Cafe. Aplikasi ini dikembangkan oleh **Fanverse Team, XI RPL 1, SMKN 1 Boyolangu** sebagai proyek sekolah bersama client.

## 🎯 Tujuan Project

* Mempermudah pelanggan dalam melihat dan memesan menu kafe.
* Membantu pengelolaan menu dan pesanan.
* Meningkatkan efisiensi pelayanan kafe.
* Memberikan pengalaman pemesanan yang praktis melalui website.

## 👥 Tim Pengembang

**Fanverse Team**
XI RPL 1 — SMKN 1 Boyolangu

## 🛠️ Teknologi

* PHP
* Laravel
* MySQL
* HTML, CSS, dan JavaScript

## ⚙️ Instalasi Project

### 1. Clone Repository

```bash
git clone https://github.com/FanverseTeam/ABS_Cafe.git
cd ABS_Cafe
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Siapkan File Environment

```bash
copy .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Konfigurasi Database

Buka file `.env`, lalu sesuaikan konfigurasi database MySQL dengan pengaturan lokal masing-masing anggota tim.

### 6. Jalankan Migration

```bash
php artisan migrate
```

Jalankan perintah ini setelah database dibuat dan konfigurasi koneksi sudah benar.

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Buka alamat lokal yang ditampilkan di terminal untuk mengakses aplikasi.

## 🤝 Kolaborasi

Project ini dikembangkan secara kolaboratif oleh anggota Fanverse Team menggunakan Git dan GitHub.

Sebelum mengirim perubahan, ambil versi terbaru:

```bash
git pull origin main
```

Setelah menyelesaikan perubahan:

```bash
git add .
git commit -m "Deskripsi perubahan"
git push origin main
```

Pastikan perubahan sudah diuji sebelum dikirim ke branch utama.

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan proyek sekolah dan kerja sama dengan ABS ACC & Cafe.

Test