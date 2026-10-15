# ABS Cafe — Web App Halaman Admin (Back-Office) ☕👜

Aplikasi web administrasi dan manajemen operasional **ABS Cafe** (tagline: *Accessories & Cafe*), Boyolangu, Kabupaten Tulungagung. Dikembangkan menggunakan **Laravel (PHP, Blade, CSS Tailwind, dan JavaScript)** dengan nuansa hangat ala coffee shop premium (palet coklat & krem).

---

## 🎨 Tema & Desain Visual
- **Nuansa**: Hangat, elegan, clean ala coffee shop modern.
- **Palet Warna**:
  - Coklat Tua (Primer, Header, Sidebar Aktif): `#4A1F14`
  - Coklat Sedang (Hover, Aksen): `#7A4A2B`
  - Emas / Karamel (Aksen, Badge, Highlight): `#D4A76A`
  - Krem Terang (Latar Belakang): `#F6EDE0`
  - Beige (Panel & Input): `#EFE0CC`
  - Putih Krem (Kartu Utama): `#FBF6EE`
  - Teks: Utama `#3B1E14`, Sekunder `#8A6A55`
  - Status: Sukses `#4F7A4A`, Peringatan `#C98A2B`, Bahaya `#B03A2E`, Info `#5B7C99`
- **Tipografi**:
  - Judul / Heading: *Playfair Display* (Serif elegan)
  - Isi / Tabel / Form: *Poppins* (Sans-serif bersih)
- **Komponen**: Sudut membulat besar (`rounded-2xl` & `rounded-full`), soft shadow, responsif desktop & mobile drawer.

---

## 🔑 Kredensial Akun Demo (Administrator)
| Peran | Email | Username | Password |
|---|---|---|---|
| **Admin Utama** | `admin@abscafe.com` | `admin` | `admin123` |
| **Kasir 1** | `siti.kasir@abscafe.com` | `siti_kasir` | `password` |
| **Kasir 2** | `budi.kasir@abscafe.com` | `budi_kasir` | `password` |

> *Catatan*: Hak akses dashboard back-office dilindungi oleh middleware `admin.auth`, hanya akun dengan peran **Admin** yang diizinkan mengakses menu administrator.

---

## 📋 Fitur & Halaman yang Dibuat

1. **Login Admin (`/admin/login`)**:
   - Layout 2 kolom estetis dengan foto kafe dan tagline *"Good Coffee, Good Mood"*.
   - Toggle sembunyikan/tampilkan kata sandi, remember me, dan tombol otomatis isi akun demo.
2. **Dashboard (`/admin`)**:
   - Salam personalisasi dan tanggal bahasa Indonesia.
   - 4 Kartu Statistik (Pesanan Hari Ini, Pendapatan Hari Ini, Menunggu Pembayaran, Menu Stok Menipis).
   - Grafik interaktif tren pendapatan 7 hari (Chart.js) dan grafik donat penjualan per kategori.
   - Daftar Top 5 Menu Terlaris dan Tabel Pesanan Terbaru.
   - Panel peringatan stok di bawah batas minimum.
3. **Pesanan (`/admin/pesanan`)**:
   - Tabel pesanan lengkap dengan kode order, customer, meja/tipe, status bayar & alur sajian.
   - Filter status pembayaran, status pesanan, tipe dine-in/take-away, dan pencarian.
   - Modal detail pesanan (daftar item, catatan, pajak, diskon, timeline alur sajian, info kasir).
   - Aksi ubah status pesanan (Diproses / Siap / Selesai), batalkan pesanan (alasan wajib), dan cetak nota struk termal.
   - Ekspor data pesanan ke format CSV.
4. **Katalog Menu (`/admin/menu`)**:
   - Toggle tampilan Grid kartu foto dan Tabel rincian.
   - Filter 14 kategori minuman & makanan (Coffee Base, Speciality, Milk, Tea, Snacks, Main Course, dll) & sorting (Terlaris, Nama, Harga).
   - Switch status ketersediaan (Tersedia / Habis) dan tanda *Best Seller*.
   - Modal form tambah & edit menu dengan URL foto, deskripsi, harga, satuan, dan stok.
   - Aksi duplikasi menu dan hapus permanen dengan dialog konfirmasi.
5. **Kategori Menu (`/admin/kategori`)**:
   - Daftar 14 kategori kafe lengkap dengan ikon visual Lucide dan urutan tampil di POS.
   - Proteksi integritas: kategori yang masih berisi menu tidak dapat dihapus (muncul peringatan keamanan).
6. **Stok & Inventaris (`/admin/stok`)**:
   - Status otomatis (Aman, Menipis, Habis).
   - Modal aksi cepat: Tambah Stok (+ Masuk), Kurangi Stok (- Keluar), dan Atur Stok Minimum.
   - Modal riwayat lengkap audit pergerakan stok (log masuk/keluar/koreksi beserta pelakunya).
7. **Promo & Diskon (`/admin/promo`)**:
   - Kartu banner promo (Buy 2 Get 1, diskon persen, potongan nominal).
   - Status berjalan/terjadwal/berakhir otomatis berdasarkan tanggal.
   - Switch aktif/nonaktif promo.
8. **Pengguna & Staf (`/admin/pengguna`)**:
   - Tabel akun staf (Admin, Kasir, Staf Barista/Dapur).
   - Tambah dan edit staf, aktifkan/nonaktifkan akun, serta fitur reset password.
9. **Denah & Meja (`/admin/meja`)**:
   - Grid kartu nomor meja dengan status Tersedia, Terpakai, dan Nonaktif.
   - Kapasitas kursi dan catatan lokasi (Indoor, Outdoor, Lantai 2).
   - Modal QR Code pemesanan meja dan tombol unduh file QR gambar.
10. **Pengaturan Cafe (`/admin/pengaturan`)**:
    - **Tab Profil Cafe**: Nama, tagline, alamat Tulungagung, jam buka hari kerja & akhir pekan.
    - **Tab Pajak & Biaya**: Persentase pajak PB1 (10%) dan switch Service Charge.
    - **Tab Pembayaran & QRIS**: Pilihan metode di kasir (Tunai, QRIS, Debit) dan preview QRIS.
    - **Tab Format Nota**: Pengaturan teks header & footer nota dengan **Pratinjau Langsung (Live Preview)** struk kasir.
    - **Tab Tampilan**: Banner headline beranda.
    - **Tab Akun Saya**: Ganti nama, email, dan password admin.
11. **Log Aktivitas (`/admin/log`)**:
    - Catatan audit sistem (waktu, nama staf, peran, aksi, alamat IP).
    - Filter kategori aktivitas & ekspor file CSV.

---

## 🚀 Cara Menjalankan Proyek di Laragon

1. Buka aplikasi **Laragon** dan klik **Start All** (Apache/Nginx & MySQL aktif).
2. Buka terminal di folder proyek (`d:\laragon\www\ABS_Cafe`):
   ```bash
   php artisan migrate --seed
   ```
   *(Data migrasi dan seeder awal akan otomatis terisi)*
3. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
4. Buka browser dan akses:
   - **Halaman Login Admin**: [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login) atau [http://abscafe.test/admin/login](http://abscafe.test/admin/login)
   - Gunakan email `admin@abscafe.com` dan password `admin123`.

---

## 🧪 Pengujian Otomatis

Proyek ini memiliki suite pengujian PHPUnit (242 tes, 699 assertion) yang mencakup
seluruh fitur 3 peran (Admin, Owner, Kasir), logika service (unit), alur lintas-fitur
(integrasi), serta audit paritas skema `db_abscafe.sql` dengan migrasi.

**Prasyarat**: buat database uji sekali saja —

```sql
CREATE DATABASE IF NOT EXISTS abs_cafe_test
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Konfigurasi sudah tertanam di `phpunit.xml`, lalu jalankan:

```bash
php artisan test      # atau: composer test
```

Tes berjalan dengan `RefreshDatabase` (data di-rollback) dan memiliki pengaman agar
tidak menimpa database pengembangan. Laporan lengkap (cakupan, cara menjalankan,
dan daftar bug yang ditemukan/diperbaiki) tersedia di
[`docs/TEST_REPORT.md`](docs/TEST_REPORT.md).

**Import data contoh (opsional)**:

```bash
mysql -u root abs_cafe < db_abscafe.sql
```

---

## 👥 Pengembang
Dikembangkan untuk kerja sama proyek sekolah bersama **ABS Cafe Tulungagung** oleh **Fanverse Team, XI RPL 1, SMKN 1 Boyolangu**.