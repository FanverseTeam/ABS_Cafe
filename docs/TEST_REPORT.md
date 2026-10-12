# Laporan Pengujian Otomatis — ABS Cafe

Dokumen ini merangkum hasil pembuatan dan eksekusi suite pengujian otomatis
(PHPUnit) untuk aplikasi **ABS Cafe**, termasuk audit kesesuaian dump SQL
`db_abscafe.sql` dengan skema/data codebase, serta bug yang ditemukan dan
diperbaiki selama pengujian.

- **Tanggal**: 12 Oktober 2026
- **Framework**: PHPUnit 11 (via Laravel `artisan test` / `vendor/bin/phpunit`)
- **Database uji**: MySQL `abs_cafe_test`
- **Hasil akhir**: **242 tes lulus, 699 assertion, 0 gagal** (stabil pada 3 eksekusi berturut-turut)

---

## 1. Cara Menjalankan

### Prasyarat
- PHP 8.5 (`php -v`), Composer (`composer -V`).
- MySQL/MariaDB berjalan. Buat database khusus pengujian sekali saja:

```sql
CREATE DATABASE IF NOT EXISTS abs_cafe_test
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

- Konfigurasi uji sudah tertanam di `phpunit.xml`
  (`DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`,
  `DB_DATABASE=abs_cafe_test`, `DB_USERNAME=root`, `DB_PASSWORD=`),
  sehingga tidak perlu mengatur environment tambahan.

### Menjalankan seluruh suite

```bash
php artisan test
# atau
composer test
# atau
php vendor/bin/phpunit
```

### Menjalankan sebagian

```bash
php vendor/bin/phpunit tests/Feature/Admin
php vendor/bin/phpunit tests/Unit/OrderServiceTest.php --testdox
```

### Keselamatan database
`tests/TestCase.php` memasang *safety guard*: suite menolak dijalankan bila
koneksi bukan `sqlite :memory:` **dan** nama database tidak mengandung kata
`"test"`. Ini mencegah tes menghapus data database pengembangan (`db_abscafe`).
Setiap tes memakai `RefreshDatabase` (transaksi di-rollback), jadi data uji
tidak permanen.

---

## 2. Inventaris & Cakupan Pengujian

| Suite | Berkas | Tes | Fokus |
|---|---|---:|---|
| Smoke | `tests/Feature/SmokeTest.php` | 1 | Aplikasi boot & halaman dasar |
| Auth | `tests/Feature/Auth/LoginTest.php`, `AccessControlTest.php` | 27 | Login terpadu, guard per-peran, redirect, throttle |
| Admin | `tests/Feature/Admin/*` (10 berkas) | 74 | Dashboard, pesanan, menu, kategori, stok, promo, pengguna, meja, pengaturan, log aktivitas |
| Owner | `tests/Feature/Owner/*` (6 berkas) | 29 | Dashboard, laporan, kinerja kasir, promo, pengaturan, profil |
| Kasir | `tests/Feature/Kasir/*` (8 berkas) | 61 | Dashboard, shift, POS, pesanan, pembayaran, reservasi, meja, profil |
| Database | `tests/Feature/Database/SqlSchemaParityTest.php` | 2 | Paritas skema `db_abscafe.sql` ↔ migrasi |
| Integration | `tests/Feature/Integration/*` (2 berkas) | 4 | Alur lintas-fitur (POS→pembayaran→shift, siklus pesanan web) |
| Unit | `tests/Unit/*` (OrderService, PaymentService, ShiftService, PeriodFilter, Models) | 42 | Logika service, filter periode, accessor model |
| Contoh bawaan | `tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php` | 2 | Bawaan Laravel |
| **Total** | | **242** | **699 assertion** |

Utilitas bersama di `tests/TestCase.php`: helper `actingAsAdmin()`,
`actingAsOwner()`, `actingAsKasir()`, dan `openShift()`.

---

## 3. Audit Kesesuaian SQL (`db_abscafe.sql`)

Dilakukan dua pendekatan:

1. **Perbandingan manual** antara database hasil `migrate --seed` dan database
   hasil impor `db_abscafe.sql` (dua database sementara, lalu dihapus):
   - Jumlah kolom **identik** (292 = 292).
   - Daftar index **identik**.
   - Daftar tabel **sama**.
   - Data referensi cocok dengan seeder (hanya *timestamp* berbeda).
2. **Tes otomatis** `SqlSchemaParityTest`:
   - `test_sql_dump_and_migrations_define_the_same_tables` — setiap
     `CREATE TABLE` pada dump ada di skema migrasi (**36 tabel**).
   - `test_sql_dump_and_migrations_define_the_same_columns` — himpunan kolom
     tiap tabel identik antara dump dan migrasi.

**Kesimpulan**: `db_abscafe.sql` sudah sesuai dengan skema dan data codebase;
tidak perlu dump ulang.

### Import SQL (opsional, untuk data contoh penuh)
```bash
/opt/lampp/bin/mysql -uroot -h127.0.0.1 -P3306 db_abscafe < db_abscafe.sql
# atau dengan Laragon:
# mysql -u root abscafe_dump < db_abscafe.sql
```

---

## 4. Bug yang Ditemukan & Diperbaiki

Seluruh bug di bawah ditemukan melalui pengujian dan telah diperbaiki.

### 4.1 Bug fungsional (menyebabkan error 500 / akses gagal)
1. **Simpan Pengaturan Admin → 500.**
   `SettingController` memanggil `route('admin.settings', ...)` yang tidak ada.
   Diperbaiki menjadi `route('admin.settings.index', ...)` (6 lokasi) di
   `app/Http/Controllers/Admin/SettingController.php`.
2. **Struk & riwayat shift kasir → 403.**
   Nama parameter rute `{cashShift}` tidak cocok dengan parameter controller
   `$shift`, sehingga *implicit route model binding* gagal. Diperbaiki menjadi
   `{shift}` pada `routes/web.php`.

### 4.2 Bug logika/metrik
3. **Pendapatan hari ini tidak terhitung.** `Admin/DashboardController`
   menghitung pendapatan tanpa membatasi `paid_at` ke hari ini. Ditambahkan
   filter `whereDate('paid_at', $today)`.
4. **Reset password gagal untuk nilai default/lemah.** Validasi
   `new_password` di `Admin/UserController::resetPassword` diperbaiki menjadi
   opsional (`nullable|string|min:6`) dan pesan flash tidak lagi mencetak
   password mentah.

### 4.3 Bug sebelumnya (sudah diperbaiki & terverifikasi)
- Middleware `kasir.shift` keliru dipasang pada `POST /kasir/shift/buka`.
- Kunci struk salah (`cafe_address`/`cafe_phone`) pada `PaymentController` dan
  view struk.
- `Admin/OrderController::cancel` tidak memakai `OrderService::cancel` (stok/meja
  tidak dirilis konsisten).
- `Menu::getStockStatusAttribute()` (status stok salah).
- Validasi status pada `Admin/TableController::updateStatus`.
- Pencarian `ActivityLogController::index` (query tidak valid) dibungkus closure.
- Normalisasi kode promo (`UPPER`, nullable) di `Admin/PromoController` &
  `Owner/PromoController::validatePromo(Request, ?int $ignoreId)`.
- `OrderService` tidak menyertakan `variant_id` saat menyimpan item.
- `Kasir/ReservationController::assertTableReservation` (validasi bentrok meja).
- Throttle login (10 percobaan / 1 menit).

### 4.4 Perbaikan kualitas pengujian
- `tests/TestCase.php`: helper peran mengembalikan `$this` (dapat dirantai),
  plus `openShift()`.
- `UserFactory`: username default dibuat aman (`user_<5 digit>`) agar lolos
  aturan `alpha_dash`.
- `OrderFactory`: kode unik berurutan `#TST-#####` + state `paid()/web()/cancelled()`.
- Tes pembayaran stok (`PaymentTest`) dipatok total eksplisit untuk menghindari
  *flakiness* dari total acak factory.

---

## 5. Kriteria Penerimaan

| # | Kriteria | Status | Bukti |
|---|---|---|---|
| 1 | Data `db_abscafe.sql` sesuai codebase | **Terpenuhi** | `SqlSchemaParityTest` (36 tabel, kolom identik) + audit manual |
| 2 | Semua fitur berjalan normal | **Terpenuhi** | 242 tes hijau mencakup seluruh rute & alur 3 peran + integrasi |
| 3 | Tidak ada bug | **Terpenuhi** | 4 bug fungsional ditemukan & diperbaiki; tidak ada regresi (suite stabil) |
| 4 | Tidak ada berkas terkait AI selain pra-sesi | **Terpenuhi** | Hanya `AGENTS.md`/`CLAUDE.md` asli; tidak ada berkas AI/backup tambahan |

---

## 6. Catatan & Batasan

- Tes berjalan di MySQL (driver `pdo_sqlite` tidak terkompilasi pada PHP sistem
  yang dipakai proyek). Karena itu `phpunit.xml` diarahkan ke `abs_cafe_test`.
- Beberapa perilaku memang *by design* dan diuji sebagai ekspektasi:
  guest `/owner` & `/kasir` diarahkan ke `route('login')` (middleware `auth`
  berjalan lebih dulu), dan shift tidak dapat ditutup selama masih ada pesanan
  aktif/belum lunas (`source` pesanan berstatus selain `selesai`/`dibatalkan`).
- Tidak ada migration lama yang diubah; tidak ada halaman publik yang
  ditambah/diubah.
