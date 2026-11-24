-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 24, 2026 at 03:48 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
-- Table structure for table `favorit`
--

CREATE TABLE `favorit` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `izin`
--

CREATE TABLE `izin` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kas`
--

CREATE TABLE `kas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT 'coffee',
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `name`, `icon`, `order`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Coffee Base', 'coffee', 1, 1, 'Espresso-based coffee, latte klasik, dan varian rasa pilihan', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(2, 'Speciality Drinks', 'sparkles', 2, 1, 'Minuman racikan spesial dan kreasi unik barista ABS Cafe (NEW)', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(3, 'Signature (Hot Only)', 'flame', 3, 1, 'Kopi seduh tradisional, rempah khas Nusantara, dan teh adeni hangat', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(4, 'Basic Milk', 'milk', 4, 1, 'Minuman susu creamy aneka rasa favorit (Ice & Hot)', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(5, 'Jus Series', 'glass-water', 5, 1, 'Jus buah segar dingin pelepas dahaga', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(6, 'Basic Tea', 'cup-soda', 6, 1, 'Teh seduh pilihan, milk tea, dan teh buah segar', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(7, 'Basic Squash', 'wine', 7, 1, 'Minuman soda squash dingin yang segar berkarbonasi (Ice Only)', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(8, 'Cream Series', 'ice-cream', 8, 1, 'Minuman manis berpadu krim lembut dingin (Ice Only)', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(9, 'Regal Series', 'cookie', 9, 1, 'Minuman spesial dengan remahan biskuit Marie Regal', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(10, 'Snacks', 'popcorn', 10, 1, 'Camilan gurih, manis, dan gorengan hangat pelengkap santai', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(11, 'Main Course', 'utensils', 11, 1, 'Makanan utama: aneka olahan nasi ayam, mie racikan, dan steak lezat', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(12, 'Speciality Food', 'chef-hat', 12, 1, 'Menu makanan spesial racikan andalan dapur ABS Cafe', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(13, 'Others', 'soup', 13, 1, 'Menu mie instan kuah/goreng dan porsi tambahan', '2026-10-15 14:06:21', '2026-10-15 14:06:21'),
(14, 'Signature (Nasi Arab)', 'crown', 14, 1, 'Nasi Arab rempah istimewa (Pre-order H-1, min. 3 porsi)', '2026-10-15 14:06:21', '2026-10-15 14:06:21');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `activity` text NOT NULL,
  `activity_type` varchar(255) NOT NULL DEFAULT 'sistem',
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `user_name`, `role`, `activity`, `activity_type`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 'Kasir ABS Cafe', 'kasir', 'Kasir Kasir ABS Cafe berhasil masuk ke dashboard', 'sistem', '127.0.0.1', '2026-11-24 02:01:14', '2026-11-24 02:01:14'),
(2, 'Kasir ABS Cafe', 'kasir', 'Kasir Kasir ABS Cafe keluar dari sistem', 'sistem', '127.0.0.1', '2026-11-24 02:01:54', '2026-11-24 02:01:54'),
(3, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe berhasil masuk ke dashboard', 'sistem', '127.0.0.1', '2026-11-24 02:01:59', '2026-11-24 02:01:59'),
(4, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe keluar dari sistem', 'sistem', '127.0.0.1', '2026-11-24 02:02:30', '2026-11-24 02:02:30'),
(5, 'Kasir ABS Cafe', 'kasir', 'Kasir Kasir ABS Cafe berhasil masuk ke dashboard', 'sistem', '127.0.0.1', '2026-11-24 02:02:36', '2026-11-24 02:02:36'),
(6, 'Kasir ABS Cafe', 'kasir', 'Kasir Kasir ABS Cafe keluar dari sistem', 'sistem', '127.0.0.1', '2026-11-24 02:45:38', '2026-11-24 02:45:38'),
(7, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe berhasil masuk ke dashboard', 'sistem', '127.0.0.1', '2026-11-24 02:45:43', '2026-11-24 02:45:43'),
(8, 'Admin Utama ABS Cafe', 'admin', 'Menghapus data penjaga kasir: Siti Rahma', 'pengguna', '127.0.0.1', '2026-11-24 02:46:29', '2026-11-24 02:46:29'),
(9, 'Admin Utama ABS Cafe', 'admin', 'Menghapus data penjaga kasir: Rian Pratama', 'pengguna', '127.0.0.1', '2026-11-24 02:46:33', '2026-11-24 02:46:33'),
(10, 'Admin Utama ABS Cafe', 'admin', 'Menghapus data penjaga kasir: Dinda Ayu', 'pengguna', '127.0.0.1', '2026-11-24 02:46:36', '2026-11-24 02:46:36'),
(11, 'Admin Utama ABS Cafe', 'admin', 'Memperbarui data penjaga kasir: Fitri', 'pengguna', '127.0.0.1', '2026-11-24 02:46:54', '2026-11-24 02:46:54'),
(12, 'Admin Utama ABS Cafe', 'admin', 'Memperbarui data penjaga kasir: Nisa', 'pengguna', '127.0.0.1', '2026-11-24 02:47:10', '2026-11-24 02:47:10'),
(13, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe keluar dari sistem', 'sistem', '127.0.0.1', '2026-11-24 02:47:16', '2026-11-24 02:47:16'),
(14, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe berhasil masuk ke dashboard', 'sistem', '127.0.0.1', '2026-11-24 02:47:24', '2026-11-24 02:47:24'),
(15, 'Admin Utama ABS Cafe', 'admin', 'Admin Admin Utama ABS Cafe keluar dari sistem', 'sistem', '127.0.0.1', '2026-11-24 02:47:43', '2026-11-24 02:47:43'),
(16, 'Kasir ABS Cafe', 'kasir', 'Kasir Kasir ABS Cafe berhasil masuk ke dashboard', 'sistem', '127.0.0.1', '2026-11-24 02:47:48', '2026-11-24 02:47:48');

-- --------------------------------------------------------

--
-- Table structure for table `meja`
--

CREATE TABLE `meja` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `number` int(11) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 2,
  `status` varchar(255) NOT NULL DEFAULT 'tersedia',
  `location_notes` varchar(255) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meja`
--

INSERT INTO `meja` (`id`, `number`, `capacity`, `status`, `location_notes`, `area`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'tersedia', 'Lantai 1 Indoor', NULL, NULL, '2026-10-11 23:40:53', '2026-11-02 07:06:53'),
(2, 2, 4, 'tersedia', 'Lantai 1 Indoor', NULL, NULL, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(3, 3, 6, 'tersedia', 'Lantai 1 Indoor', NULL, NULL, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(4, 4, 4, 'tersedia', 'Lantai 1 Indoor', NULL, NULL, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(5, 5, 2, 'tersedia', 'Outdoor Garden', NULL, NULL, '2026-10-11 23:40:53', '2026-10-12 10:28:56'),
(6, 6, 6, 'tersedia', 'Outdoor Garden', NULL, NULL, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(7, 7, 2, 'tersedia', 'Outdoor Garden', NULL, NULL, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(8, 8, 4, 'tersedia', 'Lantai 2 Mezanin', NULL, NULL, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(9, 9, 6, 'tersedia', 'Lantai 2 Mezanin', NULL, NULL, '2026-10-11 23:40:53', '2026-10-11 23:40:53'),
(10, 10, 4, 'tersedia', 'Lantai 2 Mezanin', NULL, NULL, '2026-10-11 23:40:53', '2026-10-11 23:40:53');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `merged_into_menu_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `variant_group` varchar(255) DEFAULT NULL,
  `variant_label` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_best_seller` tinyint(1) NOT NULL DEFAULT 0,
  `sold_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `merged_into_menu_id`, `category_id`, `name`, `variant_group`, `variant_label`, `price`, `description`, `image`, `is_available`, `is_best_seller`, `sold_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 1, 'Espresso (Hot)', 'Espresso', 'Hot', 10000.00, 'Ekstraksi kopi murni kaya crema dengan citarasa bold dan aromatik.', 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(2, NULL, 1, 'Americano (Ice)', 'Americano', 'Ice', 10000.00, 'Espresso murni diekstraksi berpadu air mineral dingin dan es batu segar.', 'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(3, NULL, 1, 'Long Black (Ice)', 'Long Black', 'Ice', 12000.00, 'Double shot espresso dituang di atas air es dingin dengan crema lembut terjaga.', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(4, NULL, 1, 'Vietnam Drip (Ice)', 'Vietnam Drip', 'Ice', 13000.00, 'Kopi seduh drip tradisional khas Vietnam dengan susu kental manis dan es batu.', 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(5, NULL, 1, 'Vietnam Drip (Hot)', 'Vietnam Drip', 'Hot', 12000.00, 'Seduhan kopi drip tradisional menetes hangat di atas susu kental manis legit.', 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(6, NULL, 1, 'Cappucino (Ice)', 'Cappucino', 'Ice', 15000.00, 'Espresso berpadu susu segar dingin dan lapisan busa foam lembut.', 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(7, NULL, 1, 'Cappucino (Hot)', 'Cappucino', 'Hot', 16000.00, 'Rasio presisi espresso hangat, steamed milk, dan busa susu tebal taburan bubuk coklat.', 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(8, NULL, 1, 'Brown Sugar Coffe Latte (Ice)', 'Brown Sugar Coffe Latte', 'Ice', 16000.00, 'Kopi susu segar dingin dengan manis legit gula aren murni Nusantara.', 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(9, NULL, 1, 'Avocado Latte (Ice)', 'Avocado Latte', 'Ice', 17000.00, 'Kombinasi alpukat creamy lembut berpadu espresso mantap dan susu dingin.', 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(10, NULL, 1, 'Caramel Latte (Ice)', 'Caramel Latte', 'Ice', 16000.00, 'Espresso dengan sirup karamel bakar aromatik dan susu segar dingin creamy.', 'https://images.unsplash.com/photo-1599398054066-846f28917f38?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(11, NULL, 1, 'Tiramisu Latte (Ice)', 'Tiramisu Latte', 'Ice', 18000.00, 'Cita rasa dessert tiramisu khas Italia berpadu espresso dan susu segar dingin.', 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(12, NULL, 1, 'Vanilla Latte (Ice)', 'Vanilla Latte', 'Ice', 16000.00, 'Espresso lembut dengan keharuman sirup vanila manis dan susu segar dingin.', 'https://images.unsplash.com/photo-1529892485617-25f63cd7b1e9?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(13, NULL, 2, 'Milo Ice Blend', NULL, NULL, 17000.00, 'Minuman cokelat Milo pekat diblender dingin dengan tekstur ice blend creamy dan foam. (NEW)', 'https://images.unsplash.com/photo-1579954115545-a95591f28bfc?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(14, NULL, 2, 'Milo Caramel Coffe', NULL, NULL, 18000.00, 'Perpaduan mewah Milo, sirup karamel gurih, dan shot espresso dingin. (NEW)', 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(15, NULL, 2, 'Spanish Latte (Ice)', 'Spanish Latte', 'Ice', 13000.00, 'Kopi susu khas Spanyol dengan manis legit susu kental manis dan es batu segar. (NEW)', 'https://images.unsplash.com/photo-1497636577773-f1231844b336?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(16, NULL, 2, 'Spanish Latte (Hot)', 'Spanish Latte', 'Hot', 14000.00, 'Kopi susu hangat khas Spanyol yang creamy, manis gurih, dan harum menenangkan. (NEW)', 'https://images.unsplash.com/photo-1518057111178-44a106bad636?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(17, NULL, 2, 'Sea Salt Spanish Latte (Ice)', 'Sea Salt Spanish Latte', 'Ice', 14000.00, 'Spanish latte berpadu krim sea salt gurih di lapisan atas yang menyegarkan. (NEW)', 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-11-02 07:43:15', NULL),
(18, NULL, 2, 'Oreo Spanish Latte (Ice)', 'Oreo Spanish Latte', 'Ice', 16000.00, 'Kopi susu Spanyol berpadu sensasi renyah remahan biskuit cokelat Oreo. (NEW)', 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(19, NULL, 2, 'Coffee Milk (Ice)', 'Coffee Milk', 'Ice', 16000.00, 'Kopi susu klasik racikan rahasia barista ABS Cafe, creamy dan pas. (NEW)', 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(20, NULL, 2, 'Choco Matcha (Ice)', 'Choco Matcha', 'Ice', 16000.00, 'Perpaduan dua rasa favorit: cokelat Belgia pekat dan matcha Jepang beraroma earthy. (NEW)', 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-11-02 07:43:15', NULL),
(21, NULL, 2, 'Matcha Coffee (Ice)', 'Matcha Coffee', 'Ice', 18000.00, 'Seduhan matcha Uji berpadu shot espresso murni dan susu segar dingin. (NEW)', 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(22, NULL, 2, 'Salted Brown Sugar Coffee (Ice)', 'Salted Brown Sugar Coffee', 'Ice', 15000.00, 'Kopi susu aren legit dengan sentuhan gurih garam laut premium. (NEW)', 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(23, NULL, 3, 'Kopi Tubruk', NULL, NULL, 6000.00, 'Kopi hitam tubruk tradisional aroma pekat dan rasa khas otentik.', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(24, NULL, 3, 'Kopi Tubruk Susu', NULL, NULL, 8000.00, 'Kopi tubruk tradisional diseduh berpadu manis legitnya susu kental manis hangat.', 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(25, NULL, 3, 'Kopi Arab', NULL, NULL, 12000.00, 'Kopi hitam hangat diracik dengan bumbu rempah khas Timur Tengah yang harum.', 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(26, NULL, 3, 'Kopi Arab Susu', NULL, NULL, 14000.00, 'Kopi Arab berempah dipadukan susu manis gurih nikmat di tubuh.', 'https://images.unsplash.com/photo-1518057111178-44a106bad636?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(27, NULL, 3, 'Kopi Jahe', NULL, NULL, 8000.00, 'Kopi hitam seduh dengan sari jahe segar penghangat tubuh alami.', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(28, NULL, 3, 'Kopi Susu Jahe', NULL, NULL, 10000.00, 'Kopi susu nikmat berpadu hangatnya jahe alami berkhasiat.', 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(29, NULL, 3, 'Susu Rempah ABS', NULL, NULL, 14000.00, 'Susu sapi murni hangat diracik aneka rempah tradisional khas ABS Cafe.', 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(30, NULL, 3, 'Teh Adeni', NULL, NULL, 20000.00, 'Teh susu rempah istimewa khas Yaman / Aden dengan racikan kapulaga dan cengkeh harum.', 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(31, NULL, 3, 'Jahe Merah', NULL, NULL, 6000.00, 'Seduhan jahe merah hangat murni tanpa pemanis berlebihan, sehat menyegarkan.', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(32, NULL, 3, 'Jahe Merah Susu', NULL, NULL, 8000.00, 'Seduhan jahe merah berpadu manis gurihnya susu kental manis hangat.', 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(33, NULL, 4, 'Milo Dino (Ice)', 'Milo Dino', 'Ice', 17000.00, 'Susu cokelat Milo dingin dengan taburan bubuk Milo melimpah di atasnya.', 'https://images.unsplash.com/photo-1579954115545-a95591f28bfc?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(34, NULL, 4, 'Matcha Milk (Ice)', 'Matcha Milk', 'Ice', 15000.00, 'Susu segar dingin berpadu bubuk matcha Jepang aromatik yang manis lembut.', 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(35, NULL, 4, 'Matcha Milk (Hot)', 'Matcha Milk', 'Hot', 16000.00, 'Susu matcha hangat berbusa lembut menenangkan dan kaya antioksidan.', 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(36, NULL, 4, 'Chocho Milk (Ice)', 'Chocho Milk', 'Ice', 16000.00, 'Susu cokelat dingin pekat yang manis, lembut, dan creamy di lidah.', 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(37, NULL, 4, 'Chocho Milk (Hot)', 'Chocho Milk', 'Hot', 17000.00, 'Cokelat pekat hangat dilelehkan dengan susu segar berbusa lembut.', 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(38, NULL, 4, 'Red Velvet Milk (Ice)', 'Red Velvet Milk', 'Ice', 14000.00, 'Minuman susu rasa kue red velvet manis gurih dengan warna merah menggoda dingin.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(39, NULL, 4, 'Red Velvet Milk (Hot)', 'Red Velvet Milk', 'Hot', 15000.00, 'Susu red velvet hangat dengan aroma vanila dan cokelat lembut menyenangkan.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(40, NULL, 4, 'Oreo Milk (Ice)', 'Oreo Milk', 'Ice', 16000.00, 'Susu segar dingin berpadu remahan biskuit Oreo manis renyah.', 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(41, NULL, 4, 'Avocado Milk (Ice)', 'Avocado Milk', 'Ice', 12000.00, 'Susu rasa alpukat dingin yang lembut, gurih, dan menyegarkan.', 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(42, NULL, 4, 'Avocado Milk (Hot)', 'Avocado Milk', 'Hot', 13000.00, 'Susu rasa alpukat hangat yang harum menenangkan saat cuaca dingin.', 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(43, NULL, 4, 'Strawberry Milk (Ice)', 'Strawberry Milk', 'Ice', 14000.00, 'Susu stroberi dingin manis segar bernuansa buah berry alami.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(44, NULL, 5, 'Jus Melon', NULL, NULL, 13000.00, 'Jus buah melon segar dingin manis alami kaya vitamin.', 'https://images.unsplash.com/photo-1528825871115-3581a5387919?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(45, NULL, 5, 'Jus Semangka', NULL, NULL, 13000.00, 'Jus semangka segar manis dingin penghilang dahaga nomor satu.', 'https://images.unsplash.com/photo-1589733955941-5eeaf752f6dd?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(46, NULL, 5, 'Jeruk Peras', NULL, NULL, 6000.00, 'Jeruk peras murni asam manis segar dengan es batu dingin.', 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(47, NULL, 5, 'Jus Lemon', NULL, NULL, 14000.00, 'Jus buah lemon asli segar kaya vitamin C disajikan dingin.', 'https://images.unsplash.com/photo-1534353473418-4cfa6c56fd38?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(48, NULL, 6, 'Original Tea (Ice)', 'Original Tea', 'Ice', 5000.00, 'Teh seduh melati wangi harum disajikan segar dingin.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(49, NULL, 6, 'Original Tea (Hot)', 'Original Tea', 'Hot', 5000.00, 'Teh melati seduh hangat dengan aroma wangi menenangkan.', 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(50, NULL, 6, 'Teh Tarik (Ice)', 'Teh Tarik', 'Ice', 8000.00, 'Teh susu kocok khas berbusa lembut disajikan dingin menyegarkan.', 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(51, NULL, 6, 'Teh Tarik (Hot)', 'Teh Tarik', 'Hot', 8000.00, 'Teh susu hangat yang creamy dengan aroma teh pekat legit.', 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(52, NULL, 6, 'Brown Sugar Milk Tea (Ice)', 'Brown Sugar Milk Tea', 'Ice', 14000.00, 'Milk tea dingin berpadu sirup gula aren legit manis gurih.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(53, NULL, 6, 'Brown Sugar Milk Tea (Hot)', 'Brown Sugar Milk Tea', 'Hot', 15000.00, 'Milk tea hangat aromatik dengan rasa manis legit gula aren.', 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-20 13:43:03', NULL),
(54, NULL, 6, 'Leci Tea (Ice)', 'Leci Tea', 'Ice', 12000.00, 'Teh seduh berpadu sirup rasa buah leci manis segar disajikan dingin.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(55, NULL, 6, 'Strawberry Tea (Ice)', 'Strawberry Tea', 'Ice', 12000.00, 'Teh buah rasa stroberi manis asam segar disajikan dingin.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(56, NULL, 6, 'Lemon Tea (Ice)', 'Lemon Tea', 'Ice', 12000.00, 'Teh melati dipadukan sari buah lemon segar kaya vitamin C dingin.', 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(57, NULL, 6, 'Thai Tea (Ice)', 'Thai Tea', 'Ice', 10000.00, 'Teh rempah khas Thailand oranye manis gurih berpadu susu kental manis dingin.', 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(58, NULL, 7, 'Soda Gembira', NULL, NULL, 16000.00, 'Minuman soda klasik berpadu sirup manis dan susu kental manis dingin menyegarkan.', 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(59, NULL, 7, 'Lemon Squash', NULL, NULL, 12000.00, 'Kesegaran soda berkarbonasi dengan perasan lemon asam manis dingin.', 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(60, NULL, 7, 'Leci Squash', NULL, NULL, 12000.00, 'Soda dingin dengan sirup leci harum segar bergelembung.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(61, NULL, 7, 'Strawberry Squash', NULL, NULL, 12000.00, 'Soda dingin berpadu sirup stroberi merah segar berkarbonasi.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(62, NULL, 8, 'Oreo Berry Cream', NULL, NULL, 17000.00, 'Paduan rasa buah beri, krim susu lembut, dan remahan biskuit Oreo dingin.', 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(63, NULL, 8, 'Matcha Cream', NULL, NULL, 16000.00, 'Teh hijau matcha dengan lapisan krim gurih lembut di atasnya dingin.', 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(64, NULL, 8, 'Spanish Latte Cream', NULL, NULL, 14000.00, 'Kopi susu Spanyol manis berpadu foam krim lembut tebal dingin. (NEW)', 'https://images.unsplash.com/photo-1497636577773-f1231844b336?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(65, NULL, 8, 'Chocho Cream', NULL, NULL, 17000.00, 'Minuman cokelat pekat dengan sentuhan krim lembut dingin meleleh.', 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(66, NULL, 8, 'Red Velvet Cream', NULL, NULL, 15000.00, 'Red velvet manis creamy dengan lapisan krim lembut di atasnya dingin.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(67, NULL, 8, 'Avocado Cream', NULL, NULL, 13000.00, 'Minuman rasa alpukat berpadu limpahan krim gurih manis dingin.', 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(68, NULL, 9, 'Caramel Regal', NULL, NULL, 16000.00, 'Susu karamel manis harum disajikan dengan topping remahan biskuit Marie Regal.', 'https://images.unsplash.com/photo-1599398054066-846f28917f38?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(69, NULL, 9, 'Vanilla Regal', NULL, NULL, 16000.00, 'Susu vanila lembut creamy berpadu biskuit Marie Regal renyah.', 'https://images.unsplash.com/photo-1529892485617-25f63cd7b1e9?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(70, NULL, 9, 'Brown Sugar Regal', NULL, NULL, 16000.00, 'Susu gula aren legit dipadukan biskuit Marie Regal favorit.', 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(71, NULL, 9, 'Spanish Regal Latte', NULL, NULL, 16000.00, 'Kopi susu Spanyol creamy dengan taburan biskuit Marie Regal lezat.', 'https://images.unsplash.com/photo-1497636577773-f1231844b336?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(72, NULL, 10, 'Bakso Goreng', NULL, NULL, 10000.00, 'Bakso sapi goreng renyah di luar kenyal di dalam dengan cocolan saus sambal.', 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(73, NULL, 10, 'Cireng', NULL, NULL, 10000.00, 'Aci digoreng krispi kenyal gurih disajikan dengan bumbu rujak pedas manis.', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(74, NULL, 10, 'Churros', NULL, NULL, 15000.00, 'Donat stik spanyol renyah taburan kayu manis gula halus dengan saus cokelat celup. (NEW)', 'https://images.unsplash.com/photo-1624300629298-e9de39c13be5?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(75, NULL, 10, 'Donat Kentang', NULL, NULL, 14000.00, 'Donat lembut berbahan kentang empuk dengan taburan gula bubuk halus.', 'https://images.unsplash.com/photo-1551106652-a5bcf4b29ab6?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(76, NULL, 10, 'Dimsum', NULL, NULL, 15000.00, 'Dimsum ayam kukus lembut juicy disajikan hangat dengan saus asam pedas.', 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(77, NULL, 10, 'Dimsum Mentai', NULL, NULL, 17000.00, 'Dimsum ayam lembut dengan siraman saus mentai gurih creamy dibakar torch. (NEW)', 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(78, NULL, 10, 'Dimsum Goreng', NULL, NULL, 18000.00, 'Dimsum ayam berbalut kulit renyah garing dengan saus cocolan istimewa.', 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(79, NULL, 10, 'Enoki Krispy', NULL, NULL, 12000.00, 'Jamur enoki berbalut tepung renyah gurih kriuk tahan lama.', 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(80, NULL, 10, 'Gyoza', NULL, NULL, 18000.00, 'Pangsit Jepang panggang isi daging ayam cincang berbumbu gurih sedap.', 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(81, NULL, 10, 'Kentang', NULL, NULL, 10000.00, 'French fries kentang stik goreng gurih renyah dengan saus sambal.', 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(82, NULL, 10, 'Kentang Balado', NULL, NULL, 12000.00, 'Kentang goreng renyah dibalut taburan bumbu balado pedas manis gurih.', 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(83, NULL, 10, 'Kebab', NULL, NULL, 15000.00, 'Tortila panggang gulung isi daging sapi berbumbu, selada, dan saus mayo melimpah.', 'https://images.unsplash.com/photo-1529006557810-274b9b2fc783?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(84, NULL, 10, 'Kroket', NULL, NULL, 15000.00, 'Kroket kentang lembut isi olahan daging gurih digoreng keemasan.', 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(85, NULL, 10, 'Lumpia Roll', NULL, NULL, 18000.00, 'Lumpia renyah gulung isi sayuran segar dan daging dengan cocolan saus lezat.', 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(86, NULL, 10, 'Mix Platter', NULL, NULL, 15000.00, 'Kombinasi hemat aneka camilan kentang, sosis, dan nugget dalam satu porsi mantap.', 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(87, NULL, 10, 'Mendoan', NULL, NULL, 10000.00, 'Tempe mendoan gurih khas digoreng setengah matang dengan sambal kecap pedas manis.', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(88, NULL, 10, 'Maryam Original', NULL, NULL, 12000.00, 'Roti canai/maryam gurih berlapis empuk dipanggang mentega harum.', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(89, NULL, 10, 'Maryam Coklat Keju', NULL, NULL, 15000.00, 'Roti maryam hangat dengan topping parutan keju gurih dan meses cokelat manis.', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(90, NULL, 10, 'Maryam Madu', NULL, NULL, 13000.00, 'Roti maryam hangat empuk disiram madu murni alami harum.', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(91, NULL, 10, 'Nugget', NULL, NULL, 12000.00, 'Nugget ayam gurih renyah digoreng garing keemasan dengan cocolan saus.', 'https://images.unsplash.com/photo-1562967914-608f82629710?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(92, NULL, 10, 'Omlet', NULL, NULL, 13000.00, 'Telur dadar omelet lembut isi sayuran dan bumbu gurih lezat.', 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(93, NULL, 10, 'Onion Rings', NULL, NULL, 16000.00, 'Bawang bombay iris berbalut tepung krispi renyah keemasan gurih. (NEW)', 'https://images.unsplash.com/photo-1639024471285-056685704935?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(94, NULL, 10, 'Pisang Goreng Wijen', NULL, NULL, 10000.00, 'Pisang kepok manis digoreng tepung renyah bertabur biji wijen sangrai.', 'https://images.unsplash.com/photo-1528751014936-863e6e7a319c?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(95, NULL, 10, 'Pisang Goreng Madu', NULL, NULL, 12000.00, 'Pisang goreng renyah disiram madu murni manis legit dan harum.', 'https://images.unsplash.com/photo-1528751014936-863e6e7a319c?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(96, NULL, 10, 'Pisang Goreng Coklat Keju', NULL, NULL, 15000.00, 'Pisang goreng renyah dengan taburan keju parut melimpah dan susu cokelat manis.', 'https://images.unsplash.com/photo-1528751014936-863e6e7a319c?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(97, NULL, 10, 'Risol Mayo', NULL, NULL, 15000.00, 'Risoles garing isi smoked beef, telur rebus, dan saus mayones creamy meleleh.', 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(98, NULL, 10, 'Risol Sayur', NULL, NULL, 15000.00, 'Risoles renyah isi tumisan wortel, kentang, dan bumbu rempah gurih sedap.', 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(99, NULL, 10, 'Roti Bakar Coklat Keju', NULL, NULL, 15000.00, 'Roti bakar mentega harum dengan isian meses cokelat tebal dan keju parut gurih.', 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(100, NULL, 10, 'Samboza', NULL, NULL, 17000.00, 'Pastel segitiga krispi isi olahan daging cincang bumbu kari aromatik khas Arab.', 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(101, NULL, 10, 'Sosis', NULL, NULL, 10000.00, 'Sosis sapi goreng gurih dengan saus sambal dan mayones.', 'https://images.unsplash.com/photo-1562967914-608f82629710?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(102, NULL, 10, 'Tahu Cabe Garam', NULL, NULL, 17000.00, 'Tahu krispi dipotong dadu ditumis cabe rawit merah, bawang putih, dan garam gurih. (NEW)', 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(103, NULL, 10, 'Tahu Petis', NULL, NULL, 12000.00, 'Tahu goreng hangat gurih disajikan dengan bumbu petis udang khas Jawa Timur.', 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(104, NULL, 10, 'Tahu Cocol', NULL, NULL, 10000.00, 'Tahu goreng renyah disajikan dengan cocolan sambal kecap pedas nikmat.', 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(105, NULL, 11, 'Nasi Goreng ABS', NULL, NULL, 16000.00, 'Nasi goreng racikan bumbu khas ABS Cafe disajikan dengan ayam suwir, acar, dan kerupuk.', 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(106, NULL, 11, 'Nasi Ayam Sambal Bawang', NULL, NULL, 16000.00, 'Nasi putih dengan ayam goreng renyah disiram ulekan sambal bawang pedas nendang.', 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(107, NULL, 11, 'Nasi Ayam Teriyaki', NULL, NULL, 16000.00, 'Nasi putih pulen dengan potongan ayam saus teriyaki manis gurih khas Jepang.', 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(108, NULL, 11, 'Nasi Ayam Lada Hitam', NULL, NULL, 16000.00, 'Nasi hangat dengan tumisan ayam saus lada hitam pedas aromatik dan paprika.', 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(109, NULL, 11, 'Nasi Ayam Mentega', NULL, NULL, 16000.00, 'Nasi dengan ayam goreng saus mentega harum gurih berpadu kecap manis lezat.', 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(110, NULL, 11, 'Nasi Ayam Pedas Manis', NULL, NULL, 16000.00, 'Nasi dengan potongan ayam berbumbu saus pedas manis mantap menggugah selera.', 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(111, NULL, 11, 'Nasi Ayam Saos Bangkok', NULL, NULL, 16000.00, 'Nasi ayam fillet krispi disiram saus Bangkok asam manis pedas segar.', 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(112, NULL, 11, 'Nasi Udang Saos Bangkok', NULL, NULL, 22000.00, 'Nasi dengan udang goreng tepung renyah dilumuri saus Bangkok asam manis segar.', 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(113, NULL, 11, 'Nasi Gila', NULL, NULL, 16000.00, 'Nasi putih dengan orak-arik telur, sosis, bakso, dan sayuran berbumbu pedas gurih.', 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(114, NULL, 11, 'Nasi Telur Sambal Bawang', NULL, NULL, 15000.00, 'Nasi putih hangat dengan telur ceplok krispi disiram sambal bawang pedas sedap.', 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(115, NULL, 11, 'Nasi Penyetan', NULL, NULL, 14000.00, 'Nasi hangat disajikan dengan tempe, tahu, telur penyet dan sambal terasi gurih.', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(116, NULL, 11, 'Mie Goreng ABS', NULL, NULL, 16000.00, 'Mie goreng bumbu racikan khas ABS Cafe dengan sayuran, telur, dan suwiran ayam.', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(117, NULL, 11, 'Mie Kuah ABS', NULL, NULL, 16000.00, 'Mie kuah kaldu gurih hangat dengan telur kocok, sayuran sawi segar, dan suwiran ayam.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(118, NULL, 11, 'Beef Steak', NULL, NULL, 27000.00, 'Steak daging sapi empuk dipanggang gurih disajikan dengan kentang goreng dan brown sauce.', 'https://images.unsplash.com/photo-1544025162-d76694265947?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(119, NULL, 11, 'Chicken Steak', NULL, NULL, 23000.00, 'Fillet ayam krispi tebal dengan saus steak gurih dan sayuran kentang goreng.', 'https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(120, NULL, 12, 'Nasi Ayam Katsu', NULL, NULL, 20000.00, 'Nasi putih pulen dengan ayam katsu renyah tebal dan saus istimewa. (NEW)', 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(121, NULL, 12, 'Nasi Goreng ABS Spesial', NULL, NULL, 23000.00, 'Nasi goreng porsi spesial lengkap dengan telur mata sapi, sosis, ayam suwir, dan kerupuk.', 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(122, NULL, 12, 'Mie Goreng ABS Spesial', NULL, NULL, 23000.00, 'Mie goreng bumbu spesial porsi mantap lengkap dengan telur, sosis, dan ayam.', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(123, NULL, 12, 'Mie Kuah ABS Spesial', NULL, NULL, 23000.00, 'Mie kuah kaldu spesial hangat lengkap dengan sosis, bakso, ayam suwir, dan telur.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(124, NULL, 13, 'Mie Goreng', NULL, NULL, 10000.00, 'Mie instan goreng disajikan dengan sawi hijau dan telur gurih.', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(125, NULL, 13, 'Mie Kuah Soto', NULL, NULL, 10000.00, 'Mie instan kuah soto hangat gurih dengan perasan jeruk nipis dan sayur.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(126, NULL, 13, 'Mie Kuah Ayam Bawang', NULL, NULL, 10000.00, 'Mie instan kuah rasa ayam bawang gurih berkaldu disajikan hangat.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(127, NULL, 13, 'Nasi', NULL, NULL, 5000.00, 'Porsi tambahan nasi putih pulen hangat.', 'https://images.unsplash.com/photo-1516684732162-798a0062be99?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(128, NULL, 13, 'Telur', NULL, NULL, 4000.00, 'Porsi tambahan telur mata sapi, telur dadar, atau telur rebus gurih.', 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(129, NULL, 14, 'Nasi Briyani', NULL, NULL, 30000.00, 'Nasi basmati bumbu briyani kaya rempah khas Timur Tengah dengan daging empuk. (Pre-order H-1, minimal order 3 porsi)', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(130, NULL, 14, 'Nasi Kabuli', NULL, NULL, 30000.00, 'Nasi kabuli gurih beraroma kapulaga dan kismis disajikan dengan daging lezat. (Pre-order H-1, minimal order 3 porsi)', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(131, NULL, 14, 'Nasi Khabsah', NULL, NULL, 30000.00, 'Nasi khabsah tradisional Arab Saudi berbumbu tomat, rempah khas, dan daging gurih. (Pre-order H-1, minimal order 3 porsi)', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=80', 1, 0, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL),
(132, NULL, 14, 'Nasi Mandhi', NULL, NULL, 30000.00, 'Nasi mandhi khas Yaman beraroma asap harum dengan daging empuk lembut istimewa. (Pre-order H-1, minimal order 3 porsi)', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=80', 1, 1, 0, '2026-10-15 14:06:21', '2026-10-15 14:06:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_variants`
--

CREATE TABLE `menu_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `metode_bayar`
--

CREATE TABLE `metode_bayar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` varchar(30) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `metode_bayar`
--

INSERT INTO `metode_bayar` (`id`, `name`, `code`, `type`, `description`, `instructions`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Tunai', 'tunai', 'cash', 'Pembayaran langsung dengan uang tunai.', 'Siapkan uang pas bila memungkinkan.', 1, 1, '2026-10-12 10:28:30', '2026-10-12 10:28:30');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_10_17_211147_create_cash_adjustments_table', 1),
(3, '0001_01_01_000000_create_users_table', 1),
(4, '2026_10_12_000001_create_abs_cafe_tables', 1),
(5, '2026_10_12_064009_create_permission_tables', 1),
(6, '2026_10_12_070000_add_profile_fields_to_users_table', 1),
(7, '2026_10_12_070001_create_categories_table', 1),
(8, '2026_10_12_070002_create_menus_table', 1),
(13, '2026_10_12_070007_create_cafe_tables_table', 1),
(14, '2026_10_12_070008_create_payment_methods_table', 1),
(15, '2026_10_12_070009_create_promos_table', 1),
(16, '2026_10_12_070010_create_settings_table', 1),
(17, '2026_10_12_070011_create_activity_logs_table', 1),
(18, '2026_10_13_000001_create_transaction_tables', 1),
(19, '2026_10_13_000002_create_kasir_tables', 1),
(20, '2026_10_13_000003_make_orders_payment_method_nullable', 1),
(21, '2026_10_17_211147_create_cash_adjustments_table', 1),
(23, '2026_10_20_080807_remove_landing_bg_url_setting', 1),
(24, '2026_10_20_083704_strip_emoji_from_stored_text', 1),
(25, '2026_10_20_201926_add_variant_merging_support', 3),
(26, '2026_10_21_082105_cleanup_database_schema_and_add_table_comments', 4),
(27, '2026_10_24_210000_sederhanakan_dan_lebur_tabel_database', 4),
(28, '2026_10_25_105343_2026_10_25_login_username_only', 4),
(29, '2026_10_25_121756_add_customer_contact_to_pesanan_table', 5),
(30, '2026_10_25_143500_perbarui_data_lokasi_kontak_abs_cafe', 5),
(31, '2026_10_25_152000_koreksi_koordinat_dan_tautan_peta_abs_cafe', 5),
(32, '2026_10_26_111200_perbarui_tautan_google_maps_abs_cafe', 5),
(33, '2026_10_26_112100_tambah_pengaturan_whatsapp_abs_cafe', 5),
(34, '2026_11_02_134037_ensure_customer_contact_in_pesanan_table', 6),
(35, '2026_11_10_000001_penyesuaian_skema_abs_cafe', 7),
(36, '2026_11_12_080832_flatten_menu_variants_to_menu_table', 8),
(37, '2026_10_12_070003_create_menu_variants_table', 9),
(38, '2026_11_19_082000_create_penjaga_kasir_table', 9),
(39, '2026_11_19_100551_add_end_datetime_to_reservasi_table', 9);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `cashier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `method` varchar(30) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `cash_received` decimal(12,2) NOT NULL DEFAULT 0.00,
  `change_given` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reference` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'berhasil',
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES
(1, 'cafe_name', 'ABS Cafe', NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(2, 'cafe_tagline', 'Good Coffee, Better Moments.', NULL, '2026-10-11 23:41:00', '2026-10-25 12:00:00'),
(3, 'cafe_address', 'Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219', NULL, '2026-10-11 23:41:00', '2026-10-25 14:30:00'),
(4, 'cafe_phone', '0896-2010-2010', NULL, '2026-10-11 23:41:00', '2026-10-25 14:30:00'),
(5, 'cafe_email', 'halo@abscafe.com', NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(6, 'tax_percent', '10', NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(7, 'service_charge_active', '0', NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(8, 'service_charge_percent', '5', NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(9, 'payment_cash', '1', NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(10, 'payment_qris', '0', NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(11, 'payment_debit', '0', NULL, '2026-10-11 23:41:00', '2026-10-11 23:41:00'),
(12, 'receipt_header', 'ABS CAFE - ACCESSORIES & CAFE\nJl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219\nTelp: 0896-2010-2010', NULL, '2026-10-11 23:41:01', '2026-10-25 14:30:00'),
(13, 'receipt_footer', 'Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @abs_tulungagung_24\n\"Good Coffee, Better Moments\"', NULL, '2026-10-11 23:41:01', '2026-10-25 12:00:00'),
(14, 'receipt_show_logo', '1', NULL, '2026-10-11 23:41:01', '2026-10-11 23:41:01'),
(15, 'banner_title', 'Nikmati Kopi Pilihan & Hidangan Spesial', NULL, '2026-10-11 23:41:01', '2026-10-15 14:15:51'),
(16, 'banner_subtitle', 'Tempat berkumpul hangat dengan aroma kopi terbaik di Boyolangu Tulungagung', NULL, '2026-10-11 23:41:01', '2026-10-11 23:41:01'),
(17, 'hours_weekday', '09:00 - 23:00', NULL, '2026-10-11 23:41:01', '2026-10-25 14:30:00'),
(18, 'hours_weekend', '09:00 - 23:00', NULL, '2026-10-11 23:41:01', '2026-10-25 14:30:00'),
(19, 'target_daily_sales', '2000000', NULL, '2026-10-12 09:43:05', '2026-10-12 09:43:05'),
(20, 'target_monthly_sales', '45000000', NULL, '2026-10-12 09:43:05', '2026-10-12 09:43:05'),
(21, 'profit_margin_percent', '40', NULL, '2026-10-12 09:43:05', '2026-10-12 09:43:05'),
(22, 'price_rounding', 'none', NULL, '2026-10-12 09:43:05', '2026-10-12 09:43:05'),
(25, 'notif_daily_report', '1', NULL, '2026-10-12 09:43:05', '2026-10-12 09:43:05'),
(26, 'notif_weekly_report', '0', NULL, '2026-10-12 09:43:05', '2026-10-12 09:43:05'),
(27, 'cafe_logo', 'images/logo.png', NULL, '2026-10-19 00:00:00', '2026-10-19 00:00:00'),
(28, 'landing_bg_url', NULL, NULL, '2026-10-20 00:54:48', '2026-10-20 00:54:48'),
(29, 'cafe_hours', 'Buka Setiap Hari 09.00 - 23.00 WIB', NULL, '2026-10-25 12:00:00', '2026-10-25 14:30:00'),
(30, 'cafe_maps_url', 'https://maps.app.goo.gl/mvEr6sWCacKH5j677', NULL, '2026-10-25 12:00:00', '2026-10-26 11:12:00'),
(31, 'cafe_instagram', 'abs_tulungagung_24', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(32, 'cafe_tiktok', 'abs.tulungagung', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(33, 'hero_badge', 'SELAMAT DATANG DI ABS CAFE', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(34, 'hero_title_prefix', 'Temukan Momen Terbaik di', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(35, 'hero_title_highlight', 'ABS Cafe', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(36, 'hero_description', 'Nikmati kopi berkualitas, makanan lezat, dan suasana nyaman yang cocok untuk bekerja, belajar, atau sekadar bersantai bersama orang-orang tercinta.', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(37, 'about_badge', 'TENTANG KAMI', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(38, 'about_title', 'ABS Cafe', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(39, 'about_description', 'ABS Cafe adalah tempat yang dirancang untuk memberikan pengalaman menikmati kopi terbaik dengan suasana yang hangat dan nyaman. Kami percaya, setiap cangkir kopi bisa menjadi awal dari cerita yang indah.', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(40, 'about_tagline', 'Good Coffee, Better Moments', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(41, 'feature_1_title', 'Kopi Berkualitas', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(42, 'feature_2_title', 'Menu Lezat', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(43, 'feature_3_title', 'Suasana Nyaman', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(44, 'feature_4_title', 'Cocok untuk Semua', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(45, 'atmosphere_badge', 'SUASANA', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(46, 'atmosphere_title', 'Nyaman & Elegan', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(47, 'atmosphere_description', 'Desain interior kami yang modern dengan sentuhan alami menciptakan suasana yang hangat dan menenangkan.', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(48, 'location_badge', 'LOKASI', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(49, 'location_title', 'Temukan Kami', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(50, 'social_title', 'Ikuti Kami di Sosial Media', NULL, '2026-10-25 12:00:00', '2026-10-25 12:00:00'),
(51, 'peta_lat', '-8.0646338', NULL, '2026-10-25 14:30:00', '2026-10-25 15:15:00'),
(52, 'peta_lng', '111.9111798', NULL, '2026-10-25 14:30:00', '2026-10-25 15:15:00'),
(53, 'last_order', '22.00 WIB', NULL, '2026-10-25 14:30:00', '2026-10-25 14:30:00'),
(54, 'whatsapp_nomor', '6289620102010', NULL, '2026-10-26 11:21:00', '2026-10-26 11:21:00'),
(55, 'whatsapp_pesan', 'Halo ABS Cafe, saya ingin bertanya.', NULL, '2026-10-26 11:21:00', '2026-10-26 11:21:00'),
(56, 'order_base_url', 'http://localhost:8000', NULL, '2026-11-09 04:51:20', '2026-11-09 04:51:20'),
(57, 'active_penjaga_kasir', 'Fitri', NULL, '2026-11-24 02:37:48', '2026-11-24 02:47:48');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'kasir',
  `phone` varchar(255) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'aktif',
  `avatar` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `name`, `username`, `email`, `role`, `phone`, `photo_path`, `status`, `avatar`, `last_login_at`, `email_verified_at`, `password`, `remember_token`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin Utama ABS Cafe', 'admin', 'admin@abscafe.com', 'admin', '089620102010', NULL, 'aktif', NULL, '2026-11-24 02:47:24', NULL, '$2y$12$RZ3FWEr2r3vHdsyQHoThUuv6uThVK4kXkiM5yrlQmn7oX7GB0pDM6', NULL, 1, '2026-10-11 23:40:51', '2026-11-24 02:47:24'),
(2, 'Owner ABS Cafe', 'owner', 'owner@abscafe.com', 'owner', '089620102010', NULL, 'aktif', NULL, NULL, NULL, '$2y$12$XLVal21JQ3qrnhQeWpGNkOOxmuw/AOTkMkKj7KLsn2U.6BdJ3h5x2', NULL, 1, '2026-10-12 09:41:10', '2026-10-12 09:41:10'),
(3, 'Kasir ABS Cafe', 'kasir', 'kasir@abscafe.com', 'kasir', '089620102010', NULL, 'aktif', NULL, '2026-11-24 02:47:48', NULL, '$2y$12$PR.oM/eNxonCTF4/hS9bRuQ3uJvfPuXEJUdDpemjfbBhfSG9CQJ8G', NULL, 1, '2026-10-12 10:28:30', '2026-11-24 02:47:48');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna_izin`
--

CREATE TABLE `pengguna_izin` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna_peran`
--

CREATE TABLE `pengguna_peran` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjaga_kasir`
--

CREATE TABLE `penjaga_kasir` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penjaga_kasir`
--

INSERT INTO `penjaga_kasir` (`id`, `name`, `phone`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Nisa', '081377889900', 'aktif', '2026-11-24 02:37:43', '2026-11-24 02:47:10'),
(5, 'Fitri', '087811223344', 'aktif', '2026-11-24 02:37:43', '2026-11-24 02:46:54');

-- --------------------------------------------------------

--
-- Table structure for table `peran`
--

CREATE TABLE `peran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peran_izin`
--

CREATE TABLE `peran_izin` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_code` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(30) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `table_number` int(11) DEFAULT NULL,
  `order_type` varchar(255) NOT NULL DEFAULT 'dine_in',
  `source` varchar(20) NOT NULL DEFAULT 'kasir',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `service_charge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `promo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cash_shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'menunggu_pembayaran',
  `order_status` varchar(255) NOT NULL DEFAULT 'baru',
  `cashier_name` varchar(255) DEFAULT NULL,
  `cashier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_item`
--

CREATE TABLE `pesanan_item` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_riwayat`
--

CREATE TABLE `pesanan_riwayat` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `actor` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'persen',
  `value` decimal(10,2) NOT NULL,
  `applies_to` varchar(255) NOT NULL DEFAULT 'semua',
  `target_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_menu_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `min_spend` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quota` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `party_size` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `reservation_end_date` date DEFAULT NULL,
  `reservation_end_time` time DEFAULT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'menunggu',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `opening_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `opening_cash_source` varchar(255) DEFAULT NULL,
  `expected_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `closing_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cash_difference` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_sales` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_transactions` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'buka',
  `is_auto_closed` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`id`, `user_id`, `opened_at`, `closed_at`, `opening_cash`, `opening_cash_source`, `expected_cash`, `closing_cash`, `cash_difference`, `total_sales`, `total_transactions`, `status`, `is_auto_closed`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, '2026-11-24 09:01:14', NULL, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0, 'buka', 0, 'Shift otomatis — sistem', '2026-11-24 02:01:14', '2026-11-24 02:01:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorit`
--
ALTER TABLE `favorit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `favorit_user_id_foreign` (`user_id`),
  ADD KEY `favorit_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `izin`
--
ALTER TABLE `izin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `izin_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `kas`
--
ALTER TABLE `kas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kas_shift_id_foreign` (`shift_id`),
  ADD KEY `kas_user_id_foreign` (`user_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meja`
--
ALTER TABLE `meja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_category_id_foreign` (`category_id`),
  ADD KEY `menu_merged_into_menu_id_foreign` (`merged_into_menu_id`);

--
-- Indexes for table `menu_variants`
--
ALTER TABLE `menu_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_variants_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `metode_bayar`
--
ALTER TABLE `metode_bayar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `metode_bayar_code_unique` (`code`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembayaran_order_id_foreign` (`order_id`),
  ADD KEY `pembayaran_cashier_id_foreign` (`cashier_id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengaturan_key_unique` (`key`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengguna_username_unique` (`username`);

--
-- Indexes for table `pengguna_izin`
--
ALTER TABLE `pengguna_izin`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `pengguna_izin_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `pengguna_peran`
--
ALTER TABLE `pengguna_peran`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `pengguna_peran_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `penjaga_kasir`
--
ALTER TABLE `penjaga_kasir`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `peran`
--
ALTER TABLE `peran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `peran_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `peran_izin`
--
ALTER TABLE `peran_izin`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `peran_izin_role_id_foreign` (`role_id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pesanan_order_code_unique` (`order_code`),
  ADD KEY `pesanan_promo_id_foreign` (`promo_id`),
  ADD KEY `pesanan_cash_shift_id_foreign` (`cash_shift_id`),
  ADD KEY `pesanan_cashier_id_foreign` (`cashier_id`);

--
-- Indexes for table `pesanan_item`
--
ALTER TABLE `pesanan_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_item_order_id_foreign` (`order_id`),
  ADD KEY `pesanan_item_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `pesanan_riwayat`
--
ALTER TABLE `pesanan_riwayat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_riwayat_order_id_foreign` (`order_id`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promo_code_unique` (`code`),
  ADD KEY `promo_target_category_id_foreign` (`target_category_id`),
  ADD KEY `promo_target_menu_id_foreign` (`target_menu_id`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservasi_code_unique` (`code`),
  ADD KEY `reservasi_table_id_foreign` (`table_id`),
  ADD KEY `reservasi_created_by_foreign` (`created_by`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shift_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorit`
--
ALTER TABLE `favorit`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `izin`
--
ALTER TABLE `izin`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kas`
--
ALTER TABLE `kas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `meja`
--
ALTER TABLE `meja`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `menu_variants`
--
ALTER TABLE `menu_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `metode_bayar`
--
ALTER TABLE `metode_bayar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penjaga_kasir`
--
ALTER TABLE `penjaga_kasir`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `peran`
--
ALTER TABLE `peran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan_item`
--
ALTER TABLE `pesanan_item`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan_riwayat`
--
ALTER TABLE `pesanan_riwayat`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorit`
--
ALTER TABLE `favorit`
  ADD CONSTRAINT `favorit_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorit_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kas`
--
ALTER TABLE `kas`
  ADD CONSTRAINT `kas_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shift` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `kategori` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_merged_into_menu_id_foreign` FOREIGN KEY (`merged_into_menu_id`) REFERENCES `menu` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_variants`
--
ALTER TABLE `menu_variants`
  ADD CONSTRAINT `menu_variants_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembayaran_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengguna_izin`
--
ALTER TABLE `pengguna_izin`
  ADD CONSTRAINT `pengguna_izin_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `izin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengguna_peran`
--
ALTER TABLE `pengguna_peran`
  ADD CONSTRAINT `pengguna_peran_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `peran` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peran_izin`
--
ALTER TABLE `peran_izin`
  ADD CONSTRAINT `peran_izin_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `izin` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peran_izin_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `peran` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_cash_shift_id_foreign` FOREIGN KEY (`cash_shift_id`) REFERENCES `shift` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pesanan_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pesanan_promo_id_foreign` FOREIGN KEY (`promo_id`) REFERENCES `promo` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pesanan_item`
--
ALTER TABLE `pesanan_item`
  ADD CONSTRAINT `pesanan_item_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pesanan_item_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan_riwayat`
--
ALTER TABLE `pesanan_riwayat`
  ADD CONSTRAINT `pesanan_riwayat_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promo`
--
ALTER TABLE `promo`
  ADD CONSTRAINT `promo_target_category_id_foreign` FOREIGN KEY (`target_category_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `promo_target_menu_id_foreign` FOREIGN KEY (`target_menu_id`) REFERENCES `menu` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservasi_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `meja` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `shift`
--
ALTER TABLE `shift`
  ADD CONSTRAINT `shift_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
