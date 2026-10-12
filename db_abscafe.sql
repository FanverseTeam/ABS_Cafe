-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 12, 2026 at 08:12 AM
-- Server version: 8.0.30
-- PHP Version: 8.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_abscafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `activity` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sistem',
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_name`, `role`, `activity`, `activity_type`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 'Siti Rahma', 'kasir', 'Kasir Siti Rahma mengonfirmasi lunas pesanan #AB261001 (QRIS)', 'pesanan', '192.168.1.10', '2026-10-11 23:29:01', '2026-10-11 23:41:01'),
(2, 'Budi Santoso', 'kasir', 'Kasir Budi mengonfirmasi lunas pesanan #AB261002 (Tunai)', 'pesanan', '192.168.1.10', '2026-10-11 23:16:01', '2026-10-11 23:41:01'),
(3, 'Admin Utama', 'admin', 'Admin membatalkan pesanan #AB261005: \"Pelanggan mendadak pergi\"', 'pesanan', '192.168.1.10', '2026-10-11 22:41:01', '2026-10-11 23:41:01'),
(4, 'Admin Utama', 'admin', 'Mengubah harga menu Latte dari Rp 20.000 menjadi Rp 22.000', 'menu', '192.168.1.10', '2026-10-11 20:41:01', '2026-10-11 23:41:01'),
(5, 'Admin Utama', 'admin', 'Mengaktifkan promo \"Buy 2 Get 1 Free All Coffee\"', 'promo', '192.168.1.10', '2026-10-11 18:41:01', '2026-10-11 23:41:01'),
(6, 'Admin Utama', 'admin', 'Menambah stok Caramel Latte sebanyak +30 porsi', 'stok', '192.168.1.10', '2026-10-11 17:41:01', '2026-10-11 23:41:01'),
(7, 'Admin Utama', 'admin', 'Koreksi inventaris fisik: ABS Canvas Tote Bag (-2 pcs rusak)', 'stok', '192.168.1.10', '2026-10-10 23:41:01', '2026-10-11 23:41:01'),
(8, 'Admin Utama', 'admin', 'Mengubah status meja 10 menjadi nonaktif (perbaikan kursi)', 'meja', '192.168.1.10', '2026-10-10 23:41:01', '2026-10-11 23:41:01'),
(9, 'Admin Utama', 'admin', 'Admin Utama berhasil login ke sistem', 'sistem', '192.168.1.10', '2026-10-11 15:41:01', '2026-10-11 23:41:01'),
(10, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe berhasil masuk ke dashboard', 'sistem', '127.0.0.1', '2026-10-12 00:04:22', '2026-10-12 00:04:22'),
(11, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe keluar dari sistem', 'sistem', '127.0.0.1', '2026-10-12 00:20:51', '2026-10-12 00:20:51'),
(12, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe berhasil masuk ke dashboard', 'sistem', '127.0.0.1', '2026-10-12 07:48:20', '2026-10-12 07:48:20'),
(13, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe keluar dari sistem', 'sistem', '127.0.0.1', '2026-10-12 08:00:39', '2026-10-12 08:00:39');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cafe_settings`
--

CREATE TABLE `cafe_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cafe_settings`
--

INSERT INTO `cafe_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'cafe_name', 'ABS Cafe', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(2, 'cafe_tagline', 'Accessories & Cafe', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(3, 'cafe_address', 'Jl. Ki Mangunsarkoro No. 88, Beji, Kec. Boyolangu, Kabupaten Tulungagung, Jawa Timur 66233', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(4, 'cafe_phone', '0812-3456-7890', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(5, 'cafe_email', 'halo@abscafe.com', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(6, 'tax_percent', '10', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(7, 'service_charge_active', '0', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(8, 'service_charge_percent', '5', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(9, 'payment_cash', '1', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(10, 'payment_qris', '1', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(11, 'payment_debit', '1', '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(12, 'receipt_header', 'ABS CAFE - ACCESSORIES & CAFE\nJl. Ki Mangunsarkoro No. 88, Boyolangu, Tulungagung\nTelp: 0812-3456-7890', '2026-10-11 23:41:01', '2026-10-11 23:41:01'),
(13, 'receipt_footer', 'Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @abscafe.tulungagung\n\"Good Coffee, Good Mood\"', '2026-10-11 23:41:01', '2026-10-11 23:41:01'),
(14, 'receipt_show_logo', '1', '2026-10-11 23:41:01', '2026-10-11 23:41:01'),
(15, 'banner_title', 'Nikmati Kopi Pilihan & Aksesoris Estetis', '2026-10-11 23:41:01', '2026-10-11 23:41:01'),
(16, 'banner_subtitle', 'Tempat berkumpul hangat dengan aroma kopi terbaik di Boyolangu Tulungagung', '2026-10-11 23:41:01', '2026-10-11 23:41:01'),
(17, 'hours_weekday', '09:00 - 22:00', '2026-10-11 23:41:01', '2026-10-11 23:41:01'),
(18, 'hours_weekend', '09:00 - 23:30', '2026-10-11 23:41:01', '2026-10-11 23:41:01');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Coffee',
  `order` int NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `order`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Coffee', 'coffee', 1, 1, 'Racikan kopi pilihan dari biji kopi arabika & robusta lokal berkualitas', '2026-10-11 23:40:52', '2026-10-11 23:40:52'),
(2, 'Non Coffee', 'cup-soda', 2, 1, 'Minuman segar, artisan tea, matcha, dan cokelat Belgia premium', '2026-10-11 23:40:52', '2026-10-11 23:40:52'),
(3, 'Food', 'utensils', 3, 1, 'Makanan berat, pastry hangat, dan camilan renyah pelengkap ngopi', '2026-10-11 23:40:52', '2026-10-11 23:40:52'),
(4, 'Merch', 'shopping-bag', 4, 1, 'Aksesoris eksklusif ABS Cafe: tumbler, tote bag, dan whole beans', '2026-10-11 23:40:52', '2026-10-11 23:40:52');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_best_seller` tinyint(1) NOT NULL DEFAULT '0',
  `stock` int NOT NULL DEFAULT '50',
  `min_stock` int NOT NULL DEFAULT '10',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'porsi',
  `sold_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `category_id`, `name`, `price`, `description`, `image`, `is_available`, `is_best_seller`, `stock`, `min_stock`, `unit`, `sold_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'Caramel Latte', 24000.00, 'Espresso double shot dengan sirup karamel bakar dan susu segar creamy dengan foam lembut.', 'https://images.unsplash.com/photo-1599398054066-846f28917f38?w=500&auto=format&fit=crop&q=80', 1, 1, 45, 15, 'porsi', 142, '2026-10-11 23:40:52', '2026-10-11 23:40:52'),
(2, 1, 'Cafe Latte', 22000.00, 'Espresso klasik seimbang dipadukan dengan steamed fresh milk beraroma harum.', 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=500&auto=format&fit=crop&q=80', 1, 1, 50, 15, 'porsi', 188, '2026-10-11 23:40:52', '2026-10-11 23:40:52'),
(3, 1, 'Americano', 18000.00, 'Espresso murni diekstraksi sempurna dengan air mineral segar, citarasa bold dan fruity note.', 'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=500&auto=format&fit=crop&q=80', 1, 0, 60, 20, 'porsi', 95, '2026-10-11 23:40:52', '2026-10-11 23:40:52'),
(4, 1, 'Cappuccino', 23000.00, 'Rasio presisi antara espresso, steamed milk, dan busa susu tebal taburan bubuk coklat.', 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=500&auto=format&fit=crop&q=80', 1, 1, 38, 10, 'porsi', 120, '2026-10-11 23:40:52', '2026-10-11 23:40:52'),
(5, 1, 'Aren Brown Sugar Coffee', 20000.00, 'Kopi susu khas Nusantara dengan manis legit gula aren murni asli Blitar/Tulungagung.', 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=500&auto=format&fit=crop&q=80', 1, 1, 8, 15, 'porsi', 210, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(6, 2, 'Matcha Latte', 25000.00, 'Bubuk matcha Uji Jepang premium dipadukan dengan fresh milk lembut dan aroma earthy manis.', 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=80', 1, 1, 22, 10, 'porsi', 130, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(7, 2, 'Signature Belgian Chocolate', 24000.00, 'Cokelat Belgia pekat 70% dilelehkan bersama susu segar hangat atau dingin.', 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=500&auto=format&fit=crop&q=80', 1, 0, 30, 12, 'porsi', 78, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(8, 2, 'Berry Sparkle Artisan Tea', 21000.00, 'Seduhan teh bunga telang & hibiscus dengan sirup stroberi segar dan soda menyegarkan.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 0, 4, 10, 'porsi', 56, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(9, 3, 'Butter Croissant', 18000.00, 'Pastry berlapis renyah luar lembut dalam dipanggang hangat dengan butter Prancis harum.', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500&auto=format&fit=crop&q=80', 1, 1, 14, 10, 'pcs', 115, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(10, 3, 'Nasi Goreng ABS Spesial', 28000.00, 'Nasi goreng racikan bumbu khas ABS Cafe disajikan dengan telur mata sapi, ayam suwir, dan kerupuk.', 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=500&auto=format&fit=crop&q=80', 1, 1, 25, 10, 'porsi', 165, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(11, 3, 'Kentang Goreng Truffle', 20000.00, 'Kentang stik renyah dibumbui minyak truffle aromatik, taburan peterseli, dan saus keju gurih.', 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=500&auto=format&fit=crop&q=80', 1, 0, 35, 15, 'porsi', 98, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(12, 3, 'Chicken Katsu Curry Rice', 32000.00, 'Fillet ayam krispi tebal dengan kuah kari Jepang gurih kental dan nasi pulen.', 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=500&auto=format&fit=crop&q=80', 0, 0, 0, 8, 'porsi', 42, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(13, 4, 'ABS Tumbler Thermal 500ml', 85000.00, 'Tumbler stainless SUS 304 tahan dingin/panas 12 jam, grafir laser eksklusif logo ABS Cafe.', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80', 1, 1, 18, 5, 'pcs', 44, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(14, 4, 'ABS Canvas Tote Bag Natural', 45000.00, 'Tas kanvas tebal 12oz sablon line-art estetis ABS Cafe Boyolangu.', 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=500&auto=format&fit=crop&q=80', 1, 0, 3, 10, 'pcs', 28, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(15, 4, 'Biji Kopi House Blend 250g', 65000.00, 'Biji kopi sangrai fresh medium roast 70% Arabika Ijen & 30% Robusta Dampit, note nutty chocolatey.', 'https://images.unsplash.com/photo-1587734195503-904fca47e0e9?w=500&auto=format&fit=crop&q=80', 1, 0, 20, 8, 'pack', 52, '2026-10-11 23:40:53', '2026-10-11 23:40:53');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_12_000001_create_abs_cafe_tables', 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_number` int DEFAULT NULL,
  `order_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dine_in',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `service_charge` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'qris',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_pembayaran',
  `order_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diproses',
  `cashier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `customer_name`, `table_number`, `order_type`, `subtotal`, `tax`, `service_charge`, `discount`, `total`, `payment_method`, `payment_status`, `order_status`, `cashier_name`, `paid_at`, `cancel_reason`, `created_at`, `updated_at`) VALUES
(1, '#AB261001', 'Rian Pratama', 1, 'dine_in', 24000.00, 2400.00, 0.00, 0.00, 26400.00, 'qris', 'lunas', 'diproses', 'Siti Rahma', '2026-10-11 23:25:53', NULL, '2026-10-10 21:40:53', '2026-10-11 23:40:54'),
(2, '#AB261002', 'Dinda Ayu', 2, 'dine_in', 82000.00, 8200.00, 0.00, 0.00, 90200.00, 'tunai', 'lunas', 'siap', 'Budi Santoso', '2026-10-11 23:15:54', NULL, '2026-10-10 22:40:54', '2026-10-11 23:40:54'),
(3, '#AB261003', 'Fajar Nugraha', 3, 'dine_in', 69000.00, 6900.00, 0.00, 0.00, 75900.00, 'debit', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 00:40:54', NULL, '2026-10-10 23:40:54', '2026-10-11 23:40:54'),
(4, '#AB261004', 'Nadia Salsabila', NULL, 'take_away', 48000.00, 4800.00, 0.00, 10000.00, 42800.00, 'qris', 'menunggu_pembayaran', 'diproses', NULL, NULL, NULL, '2026-10-11 00:40:54', '2026-10-11 23:40:55'),
(5, '#AB261005', 'Bagas Aditya', 5, 'dine_in', 46000.00, 4600.00, 0.00, 0.00, 50600.00, 'tunai', 'dibatalkan', 'selesai', 'Siti Rahma', '2026-10-11 02:40:55', 'Pelanggan membatalkan pesanan karena urusan mendadak.', '2026-10-11 01:40:55', '2026-10-11 23:40:55'),
(6, '#AB261006', 'Clara Anindya', 6, 'dine_in', 274000.00, 27400.00, 0.00, 0.00, 301400.00, 'debit', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 03:40:55', NULL, '2026-10-11 02:40:55', '2026-10-11 23:40:55'),
(7, '#AB261007', 'Dimas Wicaksono', 7, 'dine_in', 24000.00, 2400.00, 0.00, 0.00, 26400.00, 'qris', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 04:40:55', NULL, '2026-10-11 03:40:55', '2026-10-11 23:40:55'),
(8, '#AB261008', 'Mega Putri', 8, 'dine_in', 82000.00, 8200.00, 0.00, 0.00, 90200.00, 'tunai', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 05:40:55', NULL, '2026-10-11 04:40:55', '2026-10-11 23:40:55'),
(9, '#AB261009', 'Hendra Setiawan', NULL, 'take_away', 69000.00, 6900.00, 0.00, 0.00, 75900.00, 'debit', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 06:40:56', NULL, '2026-10-11 05:40:56', '2026-10-11 23:40:56'),
(10, '#AB261010', 'Anisa Maulida', 1, 'dine_in', 48000.00, 4800.00, 0.00, 0.00, 52800.00, 'qris', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 07:40:56', NULL, '2026-10-11 06:40:56', '2026-10-11 23:40:56'),
(11, '#AB261011', 'Yogi Saputra', 2, 'dine_in', 46000.00, 4600.00, 0.00, 0.00, 50600.00, 'tunai', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 08:40:56', NULL, '2026-10-11 07:40:56', '2026-10-11 23:40:56'),
(12, '#AB261012', 'Kurnia Sandy', 3, 'dine_in', 274000.00, 27400.00, 0.00, 0.00, 301400.00, 'debit', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 09:40:56', NULL, '2026-10-11 08:40:56', '2026-10-11 23:40:56'),
(13, '#AB261013', 'Bayu Pamungkas', 4, 'dine_in', 24000.00, 2400.00, 0.00, 0.00, 26400.00, 'qris', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 10:40:57', NULL, '2026-10-11 09:40:57', '2026-10-11 23:40:57'),
(14, '#AB261014', 'Tiara Anggraini', NULL, 'take_away', 82000.00, 8200.00, 0.00, 0.00, 90200.00, 'tunai', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 11:40:57', NULL, '2026-10-11 10:40:57', '2026-10-11 23:40:57'),
(15, '#AB261015', 'Rizky Firmansyah', 6, 'dine_in', 69000.00, 6900.00, 0.00, 0.00, 75900.00, 'debit', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 12:40:57', NULL, '2026-10-11 11:40:57', '2026-10-11 23:40:57'),
(16, '#AB261016', 'Salsabila Putri', 7, 'dine_in', 48000.00, 4800.00, 0.00, 0.00, 52800.00, 'qris', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 13:40:58', NULL, '2026-10-11 12:40:58', '2026-10-11 23:40:58'),
(17, '#AB261017', 'Zaky Alfarizi', 8, 'dine_in', 46000.00, 4600.00, 0.00, 0.00, 50600.00, 'tunai', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 14:40:58', NULL, '2026-10-11 13:40:58', '2026-10-11 23:40:58'),
(18, '#AB261018', 'Nadira Ramadhani', 9, 'dine_in', 274000.00, 27400.00, 0.00, 0.00, 301400.00, 'debit', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 15:40:58', NULL, '2026-10-11 14:40:58', '2026-10-11 23:40:58'),
(19, '#AB261019', 'Ilham Prasetyo', NULL, 'take_away', 24000.00, 2400.00, 0.00, 0.00, 26400.00, 'qris', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 16:40:58', NULL, '2026-10-11 15:40:58', '2026-10-11 23:40:58'),
(20, '#AB261020', 'Novi Safitri', 2, 'dine_in', 82000.00, 8200.00, 0.00, 0.00, 90200.00, 'tunai', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 17:40:59', NULL, '2026-10-11 16:40:59', '2026-10-11 23:40:59'),
(21, '#AB261021', 'Doni Kusuma', 3, 'dine_in', 69000.00, 6900.00, 0.00, 0.00, 75900.00, 'debit', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 18:40:59', NULL, '2026-10-11 17:40:59', '2026-10-11 23:40:59'),
(22, '#AB261022', 'Rani Octaviana', 4, 'dine_in', 48000.00, 4800.00, 0.00, 0.00, 52800.00, 'qris', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 19:40:59', NULL, '2026-10-11 18:40:59', '2026-10-11 23:40:59'),
(23, '#AB261023', 'Eko Purnomo', 5, 'dine_in', 46000.00, 4600.00, 0.00, 0.00, 50600.00, 'tunai', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 20:40:59', NULL, '2026-10-11 19:40:59', '2026-10-11 23:40:59'),
(24, '#AB261024', 'Vina Melati', NULL, 'take_away', 274000.00, 27400.00, 0.00, 0.00, 301400.00, 'debit', 'lunas', 'selesai', 'Budi Santoso', '2026-10-11 21:40:59', NULL, '2026-10-11 20:40:59', '2026-10-11 23:41:00'),
(25, '#AB261025', 'Arif Budiman', 7, 'dine_in', 24000.00, 2400.00, 0.00, 0.00, 26400.00, 'qris', 'lunas', 'selesai', 'Siti Rahma', '2026-10-11 22:41:00', NULL, '2026-10-11 21:41:00', '2026-10-11 23:41:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `menu_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `subtotal` decimal(12,2) NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `name`, `price`, `quantity`, `subtotal`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Caramel Latte', 24000.00, 1, 24000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:54', '2026-10-11 23:40:54'),
(2, 2, 3, 'Americano', 18000.00, 2, 36000.00, NULL, '2026-10-11 23:40:54', '2026-10-11 23:40:54'),
(3, 2, 4, 'Cappuccino', 23000.00, 2, 46000.00, NULL, '2026-10-11 23:40:54', '2026-10-11 23:40:54'),
(4, 3, 5, 'Aren Brown Sugar Coffee', 20000.00, 1, 20000.00, NULL, '2026-10-11 23:40:54', '2026-10-11 23:40:54'),
(5, 3, 6, 'Matcha Latte', 25000.00, 1, 25000.00, NULL, '2026-10-11 23:40:54', '2026-10-11 23:40:54'),
(6, 3, 7, 'Signature Belgian Chocolate', 24000.00, 1, 24000.00, NULL, '2026-10-11 23:40:54', '2026-10-11 23:40:54'),
(7, 4, 7, 'Signature Belgian Chocolate', 24000.00, 2, 48000.00, NULL, '2026-10-11 23:40:55', '2026-10-11 23:40:55'),
(8, 5, 9, 'Butter Croissant', 18000.00, 1, 18000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:55', '2026-10-11 23:40:55'),
(9, 5, 10, 'Nasi Goreng ABS Spesial', 28000.00, 1, 28000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:55', '2026-10-11 23:40:55'),
(10, 6, 11, 'Kentang Goreng Truffle', 20000.00, 2, 40000.00, NULL, '2026-10-11 23:40:55', '2026-10-11 23:40:55'),
(11, 6, 12, 'Chicken Katsu Curry Rice', 32000.00, 2, 64000.00, NULL, '2026-10-11 23:40:55', '2026-10-11 23:40:55'),
(12, 6, 13, 'ABS Tumbler Thermal 500ml', 85000.00, 2, 170000.00, NULL, '2026-10-11 23:40:55', '2026-10-11 23:40:55'),
(13, 7, 1, 'Caramel Latte', 24000.00, 1, 24000.00, NULL, '2026-10-11 23:40:55', '2026-10-11 23:40:55'),
(14, 8, 3, 'Americano', 18000.00, 2, 36000.00, NULL, '2026-10-11 23:40:55', '2026-10-11 23:40:55'),
(15, 8, 4, 'Cappuccino', 23000.00, 2, 46000.00, NULL, '2026-10-11 23:40:55', '2026-10-11 23:40:55'),
(16, 9, 5, 'Aren Brown Sugar Coffee', 20000.00, 1, 20000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:56', '2026-10-11 23:40:56'),
(17, 9, 6, 'Matcha Latte', 25000.00, 1, 25000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:56', '2026-10-11 23:40:56'),
(18, 9, 7, 'Signature Belgian Chocolate', 24000.00, 1, 24000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:56', '2026-10-11 23:40:56'),
(19, 10, 7, 'Signature Belgian Chocolate', 24000.00, 2, 48000.00, NULL, '2026-10-11 23:40:56', '2026-10-11 23:40:56'),
(20, 11, 9, 'Butter Croissant', 18000.00, 1, 18000.00, NULL, '2026-10-11 23:40:56', '2026-10-11 23:40:56'),
(21, 11, 10, 'Nasi Goreng ABS Spesial', 28000.00, 1, 28000.00, NULL, '2026-10-11 23:40:56', '2026-10-11 23:40:56'),
(22, 12, 11, 'Kentang Goreng Truffle', 20000.00, 2, 40000.00, NULL, '2026-10-11 23:40:56', '2026-10-11 23:40:56'),
(23, 12, 12, 'Chicken Katsu Curry Rice', 32000.00, 2, 64000.00, NULL, '2026-10-11 23:40:56', '2026-10-11 23:40:56'),
(24, 12, 13, 'ABS Tumbler Thermal 500ml', 85000.00, 2, 170000.00, NULL, '2026-10-11 23:40:56', '2026-10-11 23:40:56'),
(25, 13, 1, 'Caramel Latte', 24000.00, 1, 24000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:57', '2026-10-11 23:40:57'),
(26, 14, 3, 'Americano', 18000.00, 2, 36000.00, NULL, '2026-10-11 23:40:57', '2026-10-11 23:40:57'),
(27, 14, 4, 'Cappuccino', 23000.00, 2, 46000.00, NULL, '2026-10-11 23:40:57', '2026-10-11 23:40:57'),
(28, 15, 5, 'Aren Brown Sugar Coffee', 20000.00, 1, 20000.00, NULL, '2026-10-11 23:40:57', '2026-10-11 23:40:57'),
(29, 15, 6, 'Matcha Latte', 25000.00, 1, 25000.00, NULL, '2026-10-11 23:40:57', '2026-10-11 23:40:57'),
(30, 15, 7, 'Signature Belgian Chocolate', 24000.00, 1, 24000.00, NULL, '2026-10-11 23:40:57', '2026-10-11 23:40:57'),
(31, 16, 7, 'Signature Belgian Chocolate', 24000.00, 2, 48000.00, NULL, '2026-10-11 23:40:58', '2026-10-11 23:40:58'),
(32, 17, 9, 'Butter Croissant', 18000.00, 1, 18000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:58', '2026-10-11 23:40:58'),
(33, 17, 10, 'Nasi Goreng ABS Spesial', 28000.00, 1, 28000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:58', '2026-10-11 23:40:58'),
(34, 18, 11, 'Kentang Goreng Truffle', 20000.00, 2, 40000.00, NULL, '2026-10-11 23:40:58', '2026-10-11 23:40:58'),
(35, 18, 12, 'Chicken Katsu Curry Rice', 32000.00, 2, 64000.00, NULL, '2026-10-11 23:40:58', '2026-10-11 23:40:58'),
(36, 18, 13, 'ABS Tumbler Thermal 500ml', 85000.00, 2, 170000.00, NULL, '2026-10-11 23:40:58', '2026-10-11 23:40:58'),
(37, 19, 1, 'Caramel Latte', 24000.00, 1, 24000.00, NULL, '2026-10-11 23:40:58', '2026-10-11 23:40:58'),
(38, 20, 3, 'Americano', 18000.00, 2, 36000.00, NULL, '2026-10-11 23:40:59', '2026-10-11 23:40:59'),
(39, 20, 4, 'Cappuccino', 23000.00, 2, 46000.00, NULL, '2026-10-11 23:40:59', '2026-10-11 23:40:59'),
(40, 21, 5, 'Aren Brown Sugar Coffee', 20000.00, 1, 20000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:59', '2026-10-11 23:40:59'),
(41, 21, 6, 'Matcha Latte', 25000.00, 1, 25000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:59', '2026-10-11 23:40:59'),
(42, 21, 7, 'Signature Belgian Chocolate', 24000.00, 1, 24000.00, 'Less sweet, ice dipisah', '2026-10-11 23:40:59', '2026-10-11 23:40:59'),
(43, 22, 7, 'Signature Belgian Chocolate', 24000.00, 2, 48000.00, NULL, '2026-10-11 23:40:59', '2026-10-11 23:40:59'),
(44, 23, 9, 'Butter Croissant', 18000.00, 1, 18000.00, NULL, '2026-10-11 23:40:59', '2026-10-11 23:40:59'),
(45, 23, 10, 'Nasi Goreng ABS Spesial', 28000.00, 1, 28000.00, NULL, '2026-10-11 23:40:59', '2026-10-11 23:40:59'),
(46, 24, 11, 'Kentang Goreng Truffle', 20000.00, 2, 40000.00, NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(47, 24, 12, 'Chicken Katsu Curry Rice', 32000.00, 2, 64000.00, NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(48, 24, 13, 'ABS Tumbler Thermal 500ml', 85000.00, 2, 170000.00, NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(49, 25, 1, 'Caramel Latte', 24000.00, 1, 24000.00, 'Less sweet, ice dipisah', '2026-10-11 23:41:00', '2026-10-11 23:41:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_timelines`
--

CREATE TABLE `order_timelines` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_timelines`
--

INSERT INTO `order_timelines` (`id`, `order_id`, `status`, `actor`, `description`, `logged_at`) VALUES
(1, 1, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 1', '2026-10-10 21:40:53'),
(2, 1, 'LUNAS', 'Siti Rahma', 'Pembayaran qris lunas diverifikasi kasir', '2026-10-11 23:25:53'),
(3, 2, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 2', '2026-10-10 22:40:54'),
(4, 2, 'LUNAS', 'Budi Santoso', 'Pembayaran tunai lunas diverifikasi kasir', '2026-10-11 23:15:54'),
(5, 2, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 23:25:54'),
(6, 3, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 3', '2026-10-10 23:40:54'),
(7, 3, 'LUNAS', 'Siti Rahma', 'Pembayaran debit lunas diverifikasi kasir', '2026-10-11 00:40:54'),
(8, 3, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 00:50:54'),
(9, 3, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 01:25:54'),
(10, 4, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari take_away ', '2026-10-11 00:40:54'),
(11, 5, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 5', '2026-10-11 01:40:55'),
(12, 5, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 02:50:55'),
(13, 5, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 03:25:55'),
(14, 5, 'DIBATALKAN', 'Admin ABS Cafe', 'Alasan: Pelanggan membatalkan pesanan karena urusan mendadak.', '2026-10-11 02:40:55'),
(15, 6, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 6', '2026-10-11 02:40:55'),
(16, 6, 'LUNAS', 'Budi Santoso', 'Pembayaran debit lunas diverifikasi kasir', '2026-10-11 03:40:55'),
(17, 6, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 03:50:55'),
(18, 6, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 04:25:55'),
(19, 7, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 7', '2026-10-11 03:40:55'),
(20, 7, 'LUNAS', 'Siti Rahma', 'Pembayaran qris lunas diverifikasi kasir', '2026-10-11 04:40:55'),
(21, 7, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 04:50:55'),
(22, 7, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 05:25:55'),
(23, 8, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 8', '2026-10-11 04:40:55'),
(24, 8, 'LUNAS', 'Budi Santoso', 'Pembayaran tunai lunas diverifikasi kasir', '2026-10-11 05:40:55'),
(25, 8, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 05:50:55'),
(26, 8, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 06:25:55'),
(27, 9, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari take_away ', '2026-10-11 05:40:56'),
(28, 9, 'LUNAS', 'Siti Rahma', 'Pembayaran debit lunas diverifikasi kasir', '2026-10-11 06:40:56'),
(29, 9, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 06:50:56'),
(30, 9, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 07:25:56'),
(31, 10, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 1', '2026-10-11 06:40:56'),
(32, 10, 'LUNAS', 'Budi Santoso', 'Pembayaran qris lunas diverifikasi kasir', '2026-10-11 07:40:56'),
(33, 10, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 07:50:56'),
(34, 10, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 08:25:56'),
(35, 11, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 2', '2026-10-11 07:40:56'),
(36, 11, 'LUNAS', 'Siti Rahma', 'Pembayaran tunai lunas diverifikasi kasir', '2026-10-11 08:40:56'),
(37, 11, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 08:50:56'),
(38, 11, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 09:25:56'),
(39, 12, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 3', '2026-10-11 08:40:56'),
(40, 12, 'LUNAS', 'Budi Santoso', 'Pembayaran debit lunas diverifikasi kasir', '2026-10-11 09:40:56'),
(41, 12, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 09:50:56'),
(42, 12, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 10:25:56'),
(43, 13, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 4', '2026-10-11 09:40:57'),
(44, 13, 'LUNAS', 'Siti Rahma', 'Pembayaran qris lunas diverifikasi kasir', '2026-10-11 10:40:57'),
(45, 13, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 10:50:57'),
(46, 13, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 11:25:57'),
(47, 14, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari take_away ', '2026-10-11 10:40:57'),
(48, 14, 'LUNAS', 'Budi Santoso', 'Pembayaran tunai lunas diverifikasi kasir', '2026-10-11 11:40:57'),
(49, 14, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 11:50:57'),
(50, 14, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 12:25:57'),
(51, 15, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 6', '2026-10-11 11:40:57'),
(52, 15, 'LUNAS', 'Siti Rahma', 'Pembayaran debit lunas diverifikasi kasir', '2026-10-11 12:40:57'),
(53, 15, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 12:50:57'),
(54, 15, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 13:25:57'),
(55, 16, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 7', '2026-10-11 12:40:58'),
(56, 16, 'LUNAS', 'Budi Santoso', 'Pembayaran qris lunas diverifikasi kasir', '2026-10-11 13:40:58'),
(57, 16, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 13:50:58'),
(58, 16, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 14:25:58'),
(59, 17, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 8', '2026-10-11 13:40:58'),
(60, 17, 'LUNAS', 'Siti Rahma', 'Pembayaran tunai lunas diverifikasi kasir', '2026-10-11 14:40:58'),
(61, 17, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 14:50:58'),
(62, 17, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 15:25:58'),
(63, 18, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 9', '2026-10-11 14:40:58'),
(64, 18, 'LUNAS', 'Budi Santoso', 'Pembayaran debit lunas diverifikasi kasir', '2026-10-11 15:40:58'),
(65, 18, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 15:50:58'),
(66, 18, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 16:25:58'),
(67, 19, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari take_away ', '2026-10-11 15:40:58'),
(68, 19, 'LUNAS', 'Siti Rahma', 'Pembayaran qris lunas diverifikasi kasir', '2026-10-11 16:40:58'),
(69, 19, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 16:50:58'),
(70, 19, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 17:25:58'),
(71, 20, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 2', '2026-10-11 16:40:59'),
(72, 20, 'LUNAS', 'Budi Santoso', 'Pembayaran tunai lunas diverifikasi kasir', '2026-10-11 17:40:59'),
(73, 20, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 17:50:59'),
(74, 20, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 18:25:59'),
(75, 21, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 3', '2026-10-11 17:40:59'),
(76, 21, 'LUNAS', 'Siti Rahma', 'Pembayaran debit lunas diverifikasi kasir', '2026-10-11 18:40:59'),
(77, 21, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 18:50:59'),
(78, 21, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 19:25:59'),
(79, 22, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 4', '2026-10-11 18:40:59'),
(80, 22, 'LUNAS', 'Budi Santoso', 'Pembayaran qris lunas diverifikasi kasir', '2026-10-11 19:40:59'),
(81, 22, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 19:50:59'),
(82, 22, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 20:25:59'),
(83, 23, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 5', '2026-10-11 19:40:59'),
(84, 23, 'LUNAS', 'Siti Rahma', 'Pembayaran tunai lunas diverifikasi kasir', '2026-10-11 20:40:59'),
(85, 23, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 20:50:59'),
(86, 23, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 21:25:59'),
(87, 24, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari take_away ', '2026-10-11 20:40:59'),
(88, 24, 'LUNAS', 'Budi Santoso', 'Pembayaran debit lunas diverifikasi kasir', '2026-10-11 21:40:59'),
(89, 24, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 21:50:59'),
(90, 24, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 22:25:59'),
(91, 25, 'DIBUAT', 'Sistem Meja', 'Pesanan dibuat dari dine_in Meja 7', '2026-10-11 21:41:00'),
(92, 25, 'LUNAS', 'Siti Rahma', 'Pembayaran qris lunas diverifikasi kasir', '2026-10-11 22:41:00'),
(93, 25, 'SIAP', 'Barista / Kitchen', 'Pesanan siap disajikan ke meja', '2026-10-11 22:51:00'),
(94, 25, 'SELESAI', 'Staf Floor', 'Pelanggan telah menikmati hidangan', '2026-10-11 23:26:00');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promos`
--

CREATE TABLE `promos` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `applies_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'semua',
  `target_category_id` bigint UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `min_spend` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promos`
--

INSERT INTO `promos` (`id`, `title`, `description`, `type`, `value`, `applies_to`, `target_category_id`, `start_date`, `end_date`, `banner`, `is_active`, `min_spend`, `created_at`, `updated_at`) VALUES
(1, 'Buy 2 Get 1 Free All Coffee', 'Beli 2 cup menu coffee apa saja, dapatkan gratis 1 Americano atau Latte setiap hari!', 'buy_x_get_y', 100.00, 'kategori', 1, '2026-10-01', '2026-10-31', 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80', 1, 0.00, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(2, 'Diskon 20% Pelajar & Mahasiswa Tulungagung', 'Tunjukkan kartu pelajar atau KTM kampus Boyolangu/Tulungagung untuk diskon seluruh menu minuman.', 'persen', 20.00, 'semua', NULL, '2026-10-07', '2026-11-06', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&auto=format&fit=crop&q=80', 1, 30000.00, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(3, 'Cashback Rp 10.000 Paket Ngemil Hemat', 'Potongan langsung Rp 10.000 untuk pembelian bundling 1 Makanan + 1 Minuman apa saja.', 'nominal', 10000.00, 'semua', NULL, '2026-10-10', '2026-10-26', 'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=800&auto=format&fit=crop&q=80', 1, 45000.00, '2026-10-11 23:41:00', '2026-10-11 23:41:00');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('HxkwItiVQCIwIY26TAKcQjgpS5o2HQFrOjolAB9x', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJGMXhSeEk5TFFzcGRYZnRZWVFocHlGMXNrNGJ5amhzVnVIWW5zbEpnIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2Ficy1jYWZlLnRlc3QiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1791549548),
('Z4rC8tXVHxv3EUjsQGinqCgLmRIdQID044ft8ArJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJwNVk1bDRqYVpRZEFBQmRqUjhuYW9TTldUMnBTUUVSeEQ1OG5OTDZIIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1791549399);

-- --------------------------------------------------------

--
-- Table structure for table `stock_logs`
--

CREATE TABLE `stock_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `menu_id` bigint UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `stock_before` int NOT NULL,
  `stock_after` int NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_logs`
--

INSERT INTO `stock_logs` (`id`, `menu_id`, `item_name`, `type`, `quantity`, `stock_before`, `stock_after`, `notes`, `actor`, `created_at`, `updated_at`) VALUES
(1, 1, 'Caramel Latte (Bahan Sirup)', 'masuk', 30, 15, 45, 'Restock sirup Monin Caramel & fresh milk Greenfields', 'Admin ABS Cafe', '2026-10-11 17:41:00', '2026-10-11 23:41:00'),
(2, 5, 'Aren Brown Sugar Coffee', 'keluar', 3, 11, 8, 'Pengurangan otomatis dari pesanan meja', 'Sistem Kasir', '2026-10-11 21:41:00', '2026-10-11 23:41:00'),
(3, 14, 'ABS Canvas Tote Bag Natural', 'koreksi', -2, 5, 3, 'Koreksi fisik: 2 pcs cacat cetak sablon disisihkan', 'Admin ABS Cafe', '2026-10-10 23:41:00', '2026-10-11 23:41:00');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` bigint UNSIGNED NOT NULL,
  `number` int NOT NULL,
  `capacity` int NOT NULL DEFAULT '4',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `location_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `number`, `capacity`, `status`, `location_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'tersedia', 'Lantai 1 Indoor', '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(2, 2, 4, 'terpakai', 'Lantai 1 Indoor', '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(3, 3, 6, 'tersedia', 'Lantai 1 Indoor', '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(4, 4, 4, 'terpakai', 'Lantai 1 Indoor', '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(5, 5, 2, 'tersedia', 'Outdoor Garden', '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(6, 6, 6, 'tersedia', 'Outdoor Garden', '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(7, 7, 2, 'tersedia', 'Outdoor Garden', '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(8, 8, 4, 'tersedia', 'Lantai 2 Mezanin', '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(9, 9, 6, 'tersedia', 'Lantai 2 Mezanin', '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(10, 10, 4, 'nonaktif', 'Lantai 2 Mezanin', '2026-10-11 23:40:53', '2026-10-11 23:40:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staf',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `role`, `phone`, `status`, `avatar`, `last_login_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Utama ABS Cafe', 'admin', 'admin@abscafe.com', 'admin', '081234567890', 'aktif', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80', '2026-10-12 07:48:19', NULL, '$2y$12$xEF0tE10j4xqERztQC6LF.AZptZkGAu06hnth2nZWhMpjh/0WWyrK', '5OUeOUSlWXMqsRRjev9wNfmwq4q0K19tcK2uCKsbOg4EcsCNDeWXmd1hwr5L', '2026-10-11 23:40:51', '2026-10-12 07:48:19'),
(2, 'Siti Rahma', 'siti_kasir', 'siti.kasir@abscafe.com', 'kasir', '082198765432', 'aktif', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&auto=format&fit=crop&q=80', '2026-10-11 21:40:51', NULL, '$2y$12$yVYCotIQN6BR4HxALpq98umVL22EF3s5XMxw6uwKlTpsYHLymdEmW', NULL, '2026-10-11 23:40:51', '2026-10-11 23:40:51'),
(3, 'Budi Santoso', 'budi_kasir', 'budi.kasir@abscafe.com', 'kasir', '081377889900', 'aktif', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80', '2026-10-11 19:40:52', NULL, '$2y$12$1Amrd3mG4RtkOeGYNUwK..ZJUTcJge0rYO9VEedaXIDv0td/lckrK', NULL, '2026-10-11 23:40:52', '2026-10-11 23:40:52'),
(4, 'Wahyu Hidayat', 'wahyu_barista', 'wahyu.barista@abscafe.com', 'staf', '085712345678', 'aktif', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&auto=format&fit=crop&q=80', '2026-10-11 17:40:52', NULL, '$2y$12$CAyKX4Ga2bJqPZHtUh90dO71MfRWNC7sqV7LKmVt4i1XkK6e.l6zq', NULL, '2026-10-11 23:40:52', '2026-10-11 23:40:52'),
(5, 'Dewi Lestari', 'dewi_kitchen', 'dewi.kitchen@abscafe.com', 'staf', '089633445566', 'nonaktif', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&auto=format&fit=crop&q=80', '2026-10-08 23:40:52', NULL, '$2y$12$.YxrNyajYKXEOwhdtgHBceMFCEHCtUIAO9Kk0sKGWSka4FFsdJIRS', NULL, '2026-10-11 23:40:52', '2026-10-11 23:40:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cafe_settings`
--
ALTER TABLE `cafe_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cafe_settings_key_unique` (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menus_category_id_foreign` (`category_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_code_unique` (`order_code`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `order_timelines`
--
ALTER TABLE `order_timelines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_timelines_order_id_foreign` (`order_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `promos`
--
ALTER TABLE `promos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promos_target_category_id_foreign` (`target_category_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_logs_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tables_number_unique` (`number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cafe_settings`
--
ALTER TABLE `cafe_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `order_timelines`
--
ALTER TABLE `order_timelines`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `promos`
--
ALTER TABLE `promos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_timelines`
--
ALTER TABLE `order_timelines`
  ADD CONSTRAINT `order_timelines_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promos`
--
ALTER TABLE `promos`
  ADD CONSTRAINT `promos_target_category_id_foreign` FOREIGN KEY (`target_category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD CONSTRAINT `stock_logs_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
