# ABS Cafe — Web App Halaman Admin (Back-Office)

Aplikasi web administrasi dan manajemen operasional **ABS Cafe** (*Accessories & Cafe*), Boyolangu, Kabupaten Tulungagung. Dikembangkan menggunakan **Laravel (PHP, Blade, CSS Tailwind, dan JavaScript)** dengan nuansa hangat ala coffee shop premium (palet coklat dan krem).

---

## Tema & Desain Visual

- **Nuansa:** Hangat, elegan, clean ala coffee shop modern.
- **Palet Warna:**
  - Coklat Tua (Primer, Header, Sidebar Aktif): `#4A1F14`
  - Coklat Sedang (Hover, Aksen): `#7A4A2B`
  - Emas / Karamel (Aksen, Badge, Highlight): `#D4A76A`
  - Krem Terang (Latar Belakang): `#F6EDE0`
  - Beige (Panel & Input): `#EFE0CC`
  - Putih Krem (Kartu Utama): `#FBF6EE`
  - Teks Utama: `#3B1E14`
  - Teks Sekunder: `#8A6A55`
  - Status Sukses: `#4F7A4A`
  - Status Peringatan: `#C98A2B`
  - Status Bahaya: `#B03A2E`
  - Status Info: `#5B7C99`
- **Tipografi:**
  - Judul / Heading: *Noto Serif* (serif elegan, self-hosted).
  - Isi / Tabel / Form: *Poppins* (sans-serif bersih, self-hosted).
- **Komponen:** Sudut membulat besar (`rounded-2xl` dan `rounded-full`), soft shadow, responsif untuk desktop dan mobile drawer.

---

## Kredensial Akun Demo (Administrator)

| Peran | Email | Username | Password |
|---|---|---|---|
| Admin Utama | `admin@abscafe.com` | `admin` | `admin123` |
| Kasir 1 | `siti.kasir@abscafe.com` | `siti_kasir` | `password` |
| Kasir 2 | `budi.kasir@abscafe.com` | `budi_kasir` | `password` |

> **Catatan:** Hak akses dashboard back-office dilindungi oleh middleware `admin.auth`. Hanya akun dengan peran **Admin** yang diizinkan mengakses menu administrator.

---

## Fitur & Halaman yang Dibuat

### 1. Login Admin (`/admin/login`)

- Layout dua kolom estetis dengan foto kafe dan tagline *Good Coffee, Good Mood*.
- Toggle sembunyikan/tampilkan kata sandi.
- Fitur *Remember Me*.
- Tombol otomatis untuk mengisi akun demo.

### 2. Dashboard (`/admin`)

- Salam personalisasi dan tanggal dalam bahasa Indonesia.
- Empat kartu statistik:
  - Pesanan Hari Ini.
  - Pendapatan Hari Ini.
  - Menunggu Pembayaran.
  - Menu Stok Menipis.
- Grafik interaktif tren pendapatan tujuh hari menggunakan Chart.js.
- Grafik donat penjualan per kategori.
- Daftar lima menu terlaris dan tabel pesanan terbaru.
- Panel peringatan stok di bawah batas minimum.

### 3. Pesanan (`/admin/pesanan`)

- Tabel pesanan lengkap dengan kode order, customer, meja/tipe, status pembayaran, dan alur sajian.
- Filter status pembayaran, status pesanan, tipe dine-in/take-away, serta pencarian.
- Modal detail pesanan berisi daftar item, catatan, pajak, diskon, timeline alur sajian, dan informasi kasir.
- Aksi mengubah status pesanan menjadi Diproses, Siap, atau Selesai.
- Pembatalan pesanan dengan alasan wajib.
- Cetak nota menggunakan printer struk termal.
- Ekspor data pesanan ke format CSV.

### 4. Katalog Menu (`/admin/menu`)

- Toggle tampilan grid kartu foto dan tabel rincian.
- Filter 14 kategori makanan dan minuman, seperti Coffee Base, Speciality, Milk, Tea, Snacks, dan Main Course.
- Pengurutan berdasarkan terlaris, nama, dan harga.
- Switch status ketersediaan Tersedia/Habis dan tanda Best Seller.
- Modal form tambah dan edit menu dengan URL foto, deskripsi, harga, satuan, dan stok.
- Aksi duplikasi menu dan hapus permanen dengan dialog konfirmasi.

### 5. Kategori Menu (`/admin/kategori`)

- Daftar 14 kategori kafe lengkap dengan ikon visual Lucide dan urutan tampil di POS.
- Proteksi integritas: kategori yang masih memiliki menu tidak dapat dihapus dan akan menampilkan peringatan.

### 6. Stok & Inventaris (`/admin/stok`)

- Status stok otomatis: Aman, Menipis, atau Habis.
- Modal aksi cepat untuk menambah stok, mengurangi stok, dan mengatur stok minimum.
- Modal riwayat audit pergerakan stok, termasuk log masuk, keluar, koreksi, dan pelakunya.

### 7. Promo & Diskon (`/admin/promo`)

- Kartu banner promo, seperti Buy 2 Get 1, diskon persentase, dan potongan nominal.
- Status promo berjalan, terjadwal, atau berakhir berdasarkan tanggal.
- Switch untuk mengaktifkan atau menonaktifkan promo.

### 8. Pengguna & Staf (`/admin/pengguna`)

- Tabel akun staf dengan peran Admin, Kasir, dan Staf Barista/Dapur.
- Tambah dan edit data staf.
- Mengaktifkan atau menonaktifkan akun.
- Fitur reset password.

### 9. Denah & Meja (`/admin/meja`)

- Grid kartu nomor meja dengan status Tersedia, Terpakai, dan Nonaktif.
- Informasi kapasitas kursi dan catatan lokasi, seperti Indoor, Outdoor, dan Lantai 2.
- Modal QR Code pemesanan meja dan tombol unduh gambar QR.

### 10. Pengaturan Cafe (`/admin/pengaturan`)

- **Tab Profil Cafe:** Nama, tagline, alamat Tulungagung, dan jam buka hari kerja serta akhir pekan.
- **Tab Pajak & Biaya:** Persentase pajak PB1 (10%) dan switch Service Charge.
- **Tab Pembayaran & QRIS:** Pilihan metode pembayaran kasir berupa Tunai, QRIS, dan Debit, serta preview QRIS.
- **Tab Format Nota:** Pengaturan teks header dan footer nota dengan pratinjau langsung (*Live Preview*) struk kasir.
- **Tab Tampilan:** Pengaturan banner headline beranda.
- **Tab Akun Saya:** Mengganti nama, email, dan password admin.

### 11. Log Aktivitas (`/admin/log`)

- Catatan audit sistem meliputi waktu, nama staf, peran, aksi, dan alamat IP.
- Filter kategori aktivitas dan ekspor data ke CSV.

---

## Cara Menjalankan Proyek di Laragon

1. Buka aplikasi **Laragon**, lalu klik **Start All** agar Apache/Nginx dan MySQL aktif.
2. Buka terminal di folder proyek:

   ```bash
   cd /d/laragon/www/ABS_Cafe
   ```

3. Jalankan migrasi dan seeder awal:

   ```bash
   php artisan migrate --seed
   ```

   Data migrasi dan seeder awal akan diisi sesuai konfigurasi proyek.

4. Jalankan server lokal:

   ```bash
   php artisan serve
   ```

5. Buka browser dan akses salah satu alamat berikut:
   - Halaman login admin: `http://127.0.0.1:8000/admin/login`
   - Jika virtual host Laragon sudah dikonfigurasi: `http://abscafe.test/admin/login`

6. Gunakan akun demo administrator:
   - Email: `admin@abscafe.com`
   - Password: `admin123`

---

## Foto Latar Landing Staf

Halaman `/staf` menggunakan foto lokal `landing.jpg` di root proyek sebagai file master. **Jangan mengubah atau menghapus file master tersebut.**

Untuk menghindari penyajian file master berukuran 6,7 MB, buat versi optimasi menggunakan perintah berikut setiap kali foto master diganti:

```bash
php artisan landing:optimize
```

Perintah tersebut membutuhkan **ImageMagick** dan menghasilkan:

- `public/images/landing-bg-768.webp` — versi mobile.
- `public/images/landing-bg-1280.webp` — versi tablet.
- `public/images/landing-bg-1920.webp` — versi desktop.
- `public/images/landing-bg-1920.jpg` — fallback untuk browser lama.

Setiap file ditargetkan berukuran di bawah 400 KB. Jika file optimasi tidak tersedia, halaman otomatis menggunakan gradasi Beige → Vanilla Custard → Apricot.

---

## Pengujian Otomatis

Proyek ini memiliki suite pengujian PHPUnit yang mencakup fitur tiga peran (Admin, Owner, dan Kasir), logika service (unit), alur lintas-fitur (integrasi), serta audit paritas skema `db_abscafe.sql` dengan migrasi.

**Berdasarkan dokumentasi proyek, suite pengujian mencakup 242 tes dan 699 assertion.** Jumlah ini perlu diverifikasi kembali jika pengujian atau kode berubah.

### Prasyarat

Buat database pengujian satu kali:

```sql
CREATE DATABASE IF NOT EXISTS abs_cafe_test
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Konfigurasi database pengujian tersedia di `phpunit.xml`.

Jalankan pengujian dengan:

```bash
php artisan test
```

Alternatif:

```bash
composer test
```

Tes menggunakan `RefreshDatabase` untuk mengelola data pengujian dan memiliki pengaman agar tidak menimpa database pengembangan.

Laporan lengkap mengenai cakupan, cara menjalankan pengujian, dan bug yang ditemukan atau diperbaiki tersedia di [`docs/TEST_REPORT.md`](docs/TEST_REPORT.md).

### Import Data Contoh (Opsional)

```bash
mysql -u root abs_cafe < db_abscafe.sql
```

Pastikan database tujuan sudah sesuai dan memiliki konfigurasi yang benar sebelum melakukan import.

---

## Pengembang

Dikembangkan untuk kerja sama proyek sekolah bersama **ABS Cafe Tulungagung** oleh **Fanverse Team, XI RPL 1, SMKN 1 Boyolangu**.


ppp