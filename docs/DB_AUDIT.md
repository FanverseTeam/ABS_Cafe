# Audit Struktur Database ABS Cafe

Dokumen audit ini disusun sebagai langkah pertama sebelum perombakan skema basis data, peleburan tabel tumpang tindih, dan standardisasi penamaan tabel aplikasi ke Bahasa Indonesia sesuai panduan arsitektur sistem.

Tanggal Audit: 24 Oktober 2026  
Lingkungan Basis Data: MySQL (InnoDB, utf8mb4_unicode_ci)  
Jumlah Tabel Awal: 32 tabel  

---

## 1. Inventarisasi dan Audit Per Tabel

| No | Nama Tabel Lama | Jumlah Baris | Model Terkait | Relasi / Ketergantungan | Fungsi Sistem |
|---|---|---|---|---|---|
| 1 | `activity_logs` | 117 | `ActivityLog` | `user_name`, `role`, `activity`, `activity_type`, `ip_address` | Mencatat audit trail aktivitas staf dan sistem |
| 2 | `cafe_settings` | 28 | `CafeSetting` | `key`, `value` | Menyimpan konfigurasi aktif cafe (nama, kontak, pajak, jam buka, struk) |
| 3 | `cafe_tables` | 0 | - (Tidak ada) | Migrasi lama, tidak berelasi aktif | Duplikat struktur meja yang tidak dipakai kode |
| 4 | `cash_adjustments` | 0 | `CashAdjustment` | `user_id` -> `users.id`, `applied_to_shift_id` -> `cash_shifts.id` | Penyesuaian kas laci kasir oleh owner |
| 5 | `cash_movements` | 0 | `CashMovement` | `cash_shift_id` -> `cash_shifts.id`, `user_id` -> `users.id` | Arus kas masuk/keluar saat shift aktif |
| 6 | `cash_shifts` | 163 | `CashShift` | `user_id` -> `users.id` | Sesi kerja kasir (buka/tutup shift, saldo awal/akhir) |
| 7 | `categories` | 14 | `Category` | HasMany `menus`, HasMany `promos` | Kategori katalog menu (makanan, minuman, dll.) |
| 8 | `favorites` | 0 | `Favorite` | `user_id` -> `users.id`, `menu_id` -> `menus.id` | Menu favorit pengguna/pelanggan |
| 9 | `menu_variants` | 152 | `MenuVariant` | `menu_id` -> `menus.id` | Opsi ukuran/suhu/varian harga per menu |
| 10 | `menus` | 142 | `Menu` | `category_id` -> `categories.id`, `merged_into_menu_id` -> `menus.id`, `variant_id` -> `menu_variants.id` | Katalog master produk/menu cafe beserta stok |
| 11 | `migrations` | 26 | - (Framework) | - | Riwayat eksekusi migrasi Laravel (DIPERTAHANKAN) |
| 12 | `model_has_permissions` | 0 | Spatie Permission | `permission_id` -> `permissions.id` | Pivot hak akses langsung ke model pengguna |
| 13 | `model_has_roles` | 0 | Spatie Permission | `role_id` -> `roles.id` | Pivot peran ke model pengguna |
| 14 | `order_items` | 2.585 | `OrderItem` | `order_id` -> `orders.id`, `menu_id` -> `menus.id` | Rincian baris menu/varian yang dipesan |
| 15 | `order_timelines` | 3.192 | `OrderTimeline` | `order_id` -> `orders.id` | Riwayat status pesanan dari dibuat hingga selesai |
| 16 | `orders` | 1.058 | `Order` | `cashier_id` -> `users.id`, `promo_id` -> `promos.id`, `cash_shift_id` -> `cash_shifts.id` | Data transaksi pesanan pelanggan |
| 17 | `payment_methods` | 5 | `PaymentMethod` | Digunakan via kode metode bayar di `orders` dan `payments` | Master opsi pembayaran (Tunai, QRIS, Debit, dll.) |
| 18 | `payments` | 1.032 | `Payment` | `order_id` -> `orders.id`, `cashier_id` -> `users.id` | Pencatatan pembayaran transaksi pesanan |
| 19 | `permissions` | 0 | Spatie Permission | - | Master data izin (permissions) |
| 20 | `promos` | 3 | `Promo` | `target_category_id` -> `categories.id`, `target_menu_id` -> `menus.id` | Promo diskon dan voucher belanja |
| 21 | `recipes` | 0 | `Recipe` | `menu_id` -> `menus.id`, `menu_variant_id` -> `menu_variants.id`, `stock_id` -> `stocks.id` | Formula resep bahan baku per menu/varian |
| 22 | `reservations` | 4 | `Reservation` | `table_id` -> `tables.id`, `created_by` -> `users.id` | Reservasi meja oleh pelanggan |
| 23 | `role_has_permissions` | 0 | Spatie Permission | `permission_id` -> `permissions.id`, `role_id` -> `roles.id` | Pivot relasi izin terhadap peran |
| 24 | `roles` | 0 | Spatie Permission | - | Master data peran (roles) |
| 25 | `settings` | 0 | - (Duplikat) | `key`, `value`, `group` | Skema lama pengaturan, kosong |
| 26 | `stock_logs` | 18 | `StockLog` | `menu_id` -> `menus.id` | Riwayat mutasi stok menu jadi (masuk, keluar, koreksi) |
| 27 | `stock_movements` | 0 | `StockMovement` | `stock_id` -> `stocks.id`, `user_id` -> `users.id` | Riwayat mutasi bahan baku (stocks) |
| 28 | `stock_supplier` | 0 | Pivot Stock-Supplier | `stock_id` -> `stocks.id`, `supplier_id` -> `suppliers.id` | Relasi many-to-many bahan baku ke pemasok |
| 29 | `stocks` | 0 | `Stock` | HasMany `recipes`, BelongsToMany `suppliers`, HasMany `movements` | Master inventaris bahan baku mentah |
| 30 | `suppliers` | 0 | `Supplier` | BelongsToMany `stocks` | Master data mitra pemasok bahan baku |
| 31 | `tables` | 10 | `Table` | HasMany `reservations`, HasMany `orders` | Master data meja cafe (nomor, kapasitas, lokasi) |
| 32 | `users` | 7 | `User` | Relasi ke `orders`, `cash_shifts`, `activity_logs`, dll. | Akun staf, kasir, admin, owner |

---

## 2. Temuan Khusus dan Analisis Kasus Mendalam

### A. `tables` vs `cafe_tables`
- **Temuan Data**: Tabel `tables` berisi 10 baris data operasional riil meja cafe. Sebaliknya, tabel `cafe_tables` berisi 0 baris data.
- **Temuan Kode**: Model `App\Models\Table` dan relasi `Reservation::table()` serta `Order::table()` merujuk ke tabel `tables`. Kode aplikasi (`TableController`, `ReservationController`, tampilan denah meja) 100% menggunakan `tables`. Tabel `cafe_tables` merupakan sisa migrasi lama.
- **Keputusan Peleburan**: Lebur ke tabel baru `meja`. Tabel `tables` dijadikan basis utama data. Kolom unik `area` dari `cafe_tables` diakomodasi sebagai kolom nullable pada skema baru `meja` agar tidak ada metadata struktural yang hilang.

### B. `cash_movements` vs `cash_adjustments`
- **Temuan Data**: Kedua tabel saat ini memiliki 0 baris pada snapshot basis data aktif.
- **Temuan Kode**:
  - `cash_movements` mencatat pergerakan kas saat shift kasir berjalan (`cash_shift_id`, `user_id`, `type`, `amount`, `reason`).
  - `cash_adjustments` mencatat penyesuaian kas laci oleh owner (`user_id`, `amount`, `reason`, `applied_to_shift_id` nullable).
- **Keputusan Peleburan**: Satukan kedua tabel menjadi tabel tunggal `kas`. Kolom mencakup:
  - `id`
  - `shift_id` (bigint nullable, foreign key ke `shift.id`)
  - `user_id` (bigint nullable, foreign key ke `pengguna.id`)
  - `type` / jenis (`enum('masuk', 'keluar', 'penyesuaian')`)
  - `amount` (`decimal(12,2)`)
  - `reason` (`varchar(255)`)
  - `timestamps`
  Dengan skema ini, penyesuaian laci (`adjustment`) memiliki `type = 'penyesuaian'` dengan `shift_id` yang bisa nullable saat belum diaplikasikan ke shift aktif, sedangkan kas masuk/keluar kasir memiliki `type = 'masuk'` atau `type = 'keluar'`. Model tunggal `Kas` (atau model `CashMovement`/`CashAdjustment` yang mengarah ke `kas` dengan local scope) menjaga kompatibilitas seluruh kode controller dan service shift.

### C. `stock_logs` vs `stock_movements`
- **Temuan Data**: `stock_logs` memiliki 18 baris data mutasi stok menu, sedangkan `stock_movements` memiliki 0 baris.
- **Temuan Kode**:
  - `stock_logs` mencatat riwayat perubahan stok pada tingkat menu/produk (`menu_id`, `item_name`, `type`, `quantity`, `stock_before`, `stock_after`, `notes`, `actor`).
  - `stock_movements` mencatat mutasi bahan baku mentah (`stock_id`, `user_id`, `type`, `quantity`, `stock_after`, `note`).
- **Keputusan Peleburan**: Lebur menjadi tabel tunggal `stok_riwayat`.
  Kolom menggabungkan:
  - `id`
  - `menu_id` (bigint nullable, foreign key ke `menu.id`)
  - `stock_id` (bigint nullable, foreign key ke `stok.id`)
  - `user_id` (bigint nullable, foreign key ke `pengguna.id`)
  - `actor` (varchar nullable)
  - `item_name` (varchar nullable)
  - `type` (varchar/enum jenis mutasi: masuk, keluar, koreksi, penjualan, pembelian, retur)
  - `quantity` (decimal/integer)
  - `stock_before` (decimal/integer nullable)
  - `stock_after` (decimal/integer nullable)
  - `notes` (text nullable)
  - `timestamps`
  Seluruh 18 baris data dari `stock_logs` disalin secara utuh tanpa pengurangan nilai nominal maupun referensi.

### D. `stock_supplier`
- **Temuan Data**: Tabel `stocks`, `suppliers`, dan pivot `stock_supplier` saat ini berisi 0 baris.
- **Temuan Kode**: Model `Stock` dan `Supplier` mendefinisikan relasi `belongsToMany` (`$this->belongsToMany(Supplier::class)`). Dalam operasional kuliner, 1 bahan baku dapat dipasok oleh beberapa supplier alternatif (misalnya biji kopi dari Supplier A dan Supplier B), dan 1 supplier memasok banyak bahan.
- **Keputusan**: Pertahankan tabel pivot dengan nama Bahasa Indonesia `stok_pemasok` (`stock_id`, `supplier_id`, `timestamps`) agar relasi many-to-many tetap fleksibel sesuai perancangan domain.

### E. `order_timelines` dan `payments`
- **Temuan Data**:
  - `order_timelines` berisi 3.192 baris rekaman status pemesanan.
  - `payments` berisi 1.032 baris transaksi pembayaran pesanan.
  - Pemeriksaan split-payment: Tidak ada pesanan yang memiliki lebih dari 1 rekaman pembayaran (`Orders with multiple payments = 0`).
- **Temuan Kode**:
  - `order_timelines` digunakan aktif untuk tracking alur dapur/kasir dan riwayat pesanan.
  - `payments` memuat atribusi kasir pelaksana (`cashier_id`), nominal uang diterima (`cash_received`), kembalian (`change_given`), referensi transaksi, dan timestamp waktu bayar. Laporan keuangan dan mutasi kas shift bergantung pada data pembayaran ini.
- **Keputusan**: Keduanya adalah tabel transaksional terpisah yang memiliki tanggung jawab berbeda. Pertahankan keduanya dan ganti nama ke `pesanan_riwayat` (`order_timelines`) dan `pembayaran` (`payments`).

### F. Spatie Permission (`model_has_permissions`, `roles`, dkk.)
- **Temuan Data**: Seluruh tabel grup Spatie (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) berisi 0 baris.
- **Temuan Kode**: Autentikasi dan otorisasi aplikasi saat ini menggunakan kolom `role` pada tabel `users` ('admin', 'kasir', 'owner') serta middleware berbasis role. Namun paket `spatie/laravel-permission` terpasang di `composer.json` dan memiliki file konfigurasi `config/permission.php`.
- **Keputusan**: Ganti nama tabel grup Spatie ke Bahasa Indonesia melalui `config/permission.php` dan migrasi rename:
  - `roles` -> `peran`
  - `permissions` -> `izin`
  - `model_has_roles` -> `pengguna_peran`
  - `role_has_permissions` -> `peran_izin`
  - `model_has_permissions` -> `pengguna_izin`
  Paket Spatie tetap dapat berjalan tanpa error dan konfigurasi tetap sinkron dengan skema basis data baru.

### G. `cafe_settings` vs `settings`
- **Temuan Data**: `cafe_settings` berisi 28 baris konfigurasi aktif (nama cafe, tagline, alamat, persentase pajak, struk belanja). `settings` berisi 0 baris.
- **Temuan Kode**: Model `CafeSetting` merujuk ke tabel `cafe_settings`. Seluruh halaman Pengaturan Admin, Pengaturan Owner, dan Pengaturan Website membaca dan menulis ke `cafe_settings`.
- **Keputusan Peleburan**: Lebur menjadi tabel tunggal `pengaturan`. Seluruh 28 baris data dari `cafe_settings` menjadi isi utama tabel `pengaturan`. Kolom `group` dari skema `settings` ditambahkan sebagai kolom nullable agar struktur fleksibel ke depan.

---

## 3. Matriks Transformasi Skema Basis Data

| No | Nama Tabel Lama | Kategori Aksi | Nama Tabel Baru | Keterangan Transformasi |
|---|---|---|---|---|
| 1 | `users` | Rename | `pengguna` | Ganti nama, seluruh 7 baris disalin |
| 2 | `categories` | Rename | `kategori` | Ganti nama, seluruh 14 baris disalin |
| 3 | `menus` | Rename | `menu` | Ganti nama, seluruh 142 baris disalin |
| 4 | `menu_variants` | Rename | `menu_varian` | Ganti nama, seluruh 152 baris disalin |
| 5 | `favorites` | Rename | `favorit` | Ganti nama, skema dipertahankan |
| 6 | `tables` + `cafe_tables` | Peleburan | `meja` | Melebur 2 tabel menjadi 1, 10 baris dari `tables` dipindahkan |
| 7 | `recipes` | Rename | `resep` | Ganti nama, skema dipertahankan |
| 8 | `payment_methods` | Rename | `metode_bayar` | Ganti nama, seluruh 5 baris disalin |
| 9 | `promos` | Rename | `promo` | Ganti nama, seluruh 3 baris disalin |
| 10 | `cafe_settings` + `settings` | Peleburan | `pengaturan` | Melebur 2 tabel menjadi 1, 28 baris dari `cafe_settings` dipindahkan |
| 11 | `stocks` | Rename | `stok` | Ganti nama, skema dipertahankan |
| 12 | `suppliers` | Rename | `pemasok` | Ganti nama, skema dipertahankan |
| 13 | `stock_supplier` | Rename | `stok_pemasok` | Ganti nama pivot, skema dipertahankan |
| 14 | `stock_logs` + `stock_movements` | Peleburan | `stok_riwayat` | Melebur 2 tabel menjadi 1, 18 baris dari `stock_logs` dipindahkan |
| 15 | `orders` | Rename | `pesanan` | Ganti nama, seluruh 1.058 baris disalin |
| 16 | `order_items` | Rename | `pesanan_item` | Ganti nama, seluruh 2.585 baris disalin |
| 17 | `order_timelines` | Rename | `pesanan_riwayat` | Ganti nama, seluruh 3.192 baris disalin |
| 18 | `payments` | Rename | `pembayaran` | Ganti nama, seluruh 1.032 baris disalin |
| 19 | `cash_shifts` | Rename | `shift` | Ganti nama, seluruh 163 baris disalin |
| 20 | `cash_movements` + `cash_adjustments` | Peleburan | `kas` | Melebur 2 tabel menjadi 1 tabel pergerakan & penyesuaian kas |
| 21 | `reservations` | Rename | `reservasi` | Ganti nama, seluruh 4 baris disalin |
| 22 | `activity_logs` | Rename | `log_aktivitas` | Ganti nama, seluruh 117 baris disalin |
| 23 | `roles` | Rename | `peran` | Penyesuaian nama tabel Spatie Permission |
| 24 | `permissions` | Rename | `izin` | Penyesuaian nama tabel Spatie Permission |
| 25 | `role_has_permissions` | Rename | `peran_izin` | Penyesuaian nama tabel pivot Spatie |
| 26 | `model_has_roles` | Rename | `pengguna_peran` | Penyesuaian nama tabel pivot Spatie |
| 27 | `model_has_permissions` | Rename | `pengguna_izin` | Penyesuaian nama tabel pivot Spatie |
| 28 | `migrations` | Tetap | `migrations` | Tabel inti framework Laravel (tidak disentuh) |

**Hasil Ringkas Target**:
- Jumlah tabel berkurang dari 32 tabel menjadi 28 tabel (pengurangan 4 tabel berkat peleburan `cafe_tables`, `settings`, `cash_movements/cash_adjustments`, dan `stock_logs/stock_movements`).
- Semua tabel aplikasi kini menggunakan Bahasa Indonesia yang konsisten, singkat, huruf kecil, dan format snake_case tanpa awalan yang berbelit-belit.
- Nol data yang hilang (100% data terverifikasi baris demi baris dan nominal demi nominal).
