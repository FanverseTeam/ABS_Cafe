-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 09, 2026 at 07:14 AM
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
-- Table structure for table `favorit`
--

CREATE TABLE `favorit` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `menu_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `izin`
--

CREATE TABLE `izin` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kas`
--

CREATE TABLE `kas` (
  `id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'coffee',
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
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

-- --------------------------------------------------------

--
-- Table structure for table `meja`
--

CREATE TABLE `meja` (
  `id` bigint UNSIGNED NOT NULL,
  `number` int NOT NULL,
  `capacity` int NOT NULL DEFAULT '2',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `location_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
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
  `id` bigint UNSIGNED NOT NULL,
  `merged_into_menu_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `variant_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variant_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_best_seller` tinyint(1) NOT NULL DEFAULT '0',
  `sold_count` int NOT NULL DEFAULT '0',
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
-- Table structure for table `metode_bayar`
--

CREATE TABLE `metode_bayar` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
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
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
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
(36, '2026_11_12_080832_flatten_menu_variants_to_menu_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `cashier_id` bigint UNSIGNED DEFAULT NULL,
  `method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `cash_received` decimal(12,2) NOT NULL DEFAULT '0.00',
  `change_given` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'berhasil',
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
(28, 'landing_bg_url', 'https://www.google.com/url?sa=t&source=web&rct=j&url=https%3A%2F%2Finterbox.id%2Farticle%2Ffurniture-cafe&ved=0CBcQjRxqGAoTCIDHjb-wx5cDFQAAAAAdAAAAABCHAg&opi=89978449', NULL, '2026-10-20 00:54:48', '2026-10-20 00:54:48'),
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
(56, 'order_base_url', 'https://pieces-completed-cosmetics-poster.trycloudflare.com', NULL, '2026-11-09 04:51:20', '2026-11-09 04:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kasir',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `name`, `username`, `email`, `role`, `phone`, `photo_path`, `status`, `avatar`, `last_login_at`, `email_verified_at`, `password`, `remember_token`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin Utama ABS Cafe', 'admin', 'admin@abscafe.com', 'admin', '081234567890', NULL, 'aktif', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80', '2026-11-09 04:39:23', NULL, '$2y$12$UZBi.muML8SGayBP0E4v8OeyL4Hmp.mVTooJ9x85mMN8FfHAx3WXa', 'niNUg0sE7NfoAFcchTZcppcI98RAc74z1yMl9ybpqpFI4MMvmOWNLn2gJRCA', 1, '2026-10-11 23:40:51', '2026-11-09 04:39:23'),
(2, 'Siti Rahma', 'siti_kasir', 'siti.kasir@abscafe.com', 'kasir', '082198765432', NULL, 'aktif', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&auto=format&fit=crop&q=80', '2026-10-12 08:28:55', NULL, '$2y$12$CoJbuFzwccyDME.KloAGmeVHq2KFDw7yVfiWUiq8HjGq9fqjTGu8G', NULL, 1, '2026-10-11 23:40:51', '2026-10-12 10:28:55'),
(3, 'Budi Santoso', 'budi_kasir', 'budi.kasir@abscafe.com', 'kasir', '081377889900', NULL, 'aktif', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80', '2026-11-02 07:59:28', NULL, '$2y$12$9Du1IPOvJpdRAzFIbPMNwu6Tlv1PrL0GnPwP7XsONa20U1d6zD4TG', NULL, 1, '2026-10-11 23:40:52', '2026-11-02 07:59:28'),
(4, 'Wahyu Hidayat', 'wahyu_barista', 'wahyu.barista@abscafe.com', 'staf', '085712345678', NULL, 'aktif', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&auto=format&fit=crop&q=80', '2026-10-12 04:28:55', NULL, '$2y$12$xH7Mp6MnNXB6n.HeBpvig.mM1EBsPyvl1HfU8b0cX.tC0BruCcwrS', NULL, 1, '2026-10-11 23:40:52', '2026-10-12 10:28:55'),
(5, 'Dewi Lestari', 'dewi_kitchen', 'dewi.kitchen@abscafe.com', 'staf', '089633445566', NULL, 'nonaktif', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&auto=format&fit=crop&q=80', '2026-10-09 10:28:56', NULL, '$2y$12$tgSoZeswRc04m215waBxdOzuhemWuwdg2GZsF.0ZIv7rud1HWiVke', NULL, 1, '2026-10-11 23:40:52', '2026-10-12 10:28:56'),
(6, 'Owner ABS Cafe', 'owner', 'owner@abscafe.com', 'owner', '081100112233', NULL, 'aktif', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&auto=format&fit=crop&q=80', '2026-10-17 14:02:56', NULL, '$2y$12$ps78oxU2WP44jCYOPpIAC.1dpr8.2HJvrgYBZ8yu0lNlBG5Gr8/Hu', 'nFdGD3QFPKN2eahFaVvkyTgwe2cN1vX1jYWVCJQ8P9B6D7QDiSwfXCwrCKjB', 1, '2026-10-12 09:41:10', '2026-10-17 14:02:56'),
(7, 'Kasir Demo ABS Cafe', 'kasir', 'kasir@abscafe.com', 'kasir', '081255667788', NULL, 'aktif', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&auto=format&fit=crop&q=80', '2026-10-17 14:40:06', NULL, '$2y$12$kGxF/EsiCMwT7KSLnhXEDOgFRCmPVED6VkJcBnY9VUoOlM1UIEarC', 'MGBFQ8exXI87pGpWcwdQ6ksbSoZqDtFzWOqIfwmVd5Hf3yPVbduhmYrfxVxq', 1, '2026-10-12 10:28:30', '2026-10-17 14:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna_izin`
--

CREATE TABLE `pengguna_izin` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna_peran`
--

CREATE TABLE `pengguna_peran` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peran`
--

CREATE TABLE `peran` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peran_izin`
--

CREATE TABLE `peran_izin` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` bigint UNSIGNED NOT NULL,
  `order_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_number` int DEFAULT NULL,
  `order_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dine_in',
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kasir',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `service_charge` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `promo_id` bigint UNSIGNED DEFAULT NULL,
  `cash_shift_id` bigint UNSIGNED DEFAULT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_pembayaran',
  `order_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `cashier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cashier_id` bigint UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_item`
--

CREATE TABLE `pesanan_item` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `menu_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_riwayat`
--

CREATE TABLE `pesanan_riwayat` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'persen',
  `value` decimal(10,2) NOT NULL,
  `applies_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'semua',
  `target_category_id` bigint UNSIGNED DEFAULT NULL,
  `target_menu_id` bigint UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `min_spend` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quota` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promo`
--

INSERT INTO `promo` (`id`, `title`, `code`, `description`, `type`, `value`, `applies_to`, `target_category_id`, `target_menu_id`, `start_date`, `end_date`, `banner`, `is_active`, `min_spend`, `quota`, `created_at`, `updated_at`) VALUES
(1, 'Buy 2 Get 1 Free All Coffee', 'BELI2G1', 'Beli 2 cup menu coffee apa saja, dapatkan gratis 1 Americano atau Latte setiap hari!', 'buy_x_get_y', 100.00, 'kategori', 1, NULL, '2026-10-01', '2026-10-31', 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80', 1, 0.00, NULL, '2026-10-11 23:41:00', '2026-10-12 10:28:56'),
(2, 'Diskon 20% Pelajar & Mahasiswa Tulungagung', 'PELAJAR20', 'Tunjukkan kartu pelajar atau KTM kampus Boyolangu/Tulungagung untuk diskon seluruh menu minuman.', 'persen', 20.00, 'semua', NULL, NULL, '2026-10-07', '2026-11-06', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&auto=format&fit=crop&q=80', 1, 30000.00, NULL, '2026-10-11 23:41:00', '2026-10-12 10:28:56'),
(3, 'Cashback Rp 10.000 Paket Ngemil Hemat', 'NGEMIL10', 'Potongan langsung Rp 10.000 untuk pembelian bundling 1 Makanan + 1 Minuman apa saja.', 'nominal', 10000.00, 'semua', NULL, NULL, '2026-10-10', '2026-10-26', 'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=800&auto=format&fit=crop&q=80', 1, 45000.00, NULL, '2026-10-11 23:41:00', '2026-10-12 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_size` int UNSIGNED NOT NULL DEFAULT '1',
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `table_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `opening_cash` decimal(12,2) NOT NULL DEFAULT '0.00',
  `opening_cash_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_cash` decimal(12,2) NOT NULL DEFAULT '0.00',
  `closing_cash` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cash_difference` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_sales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_transactions` int UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'buka',
  `is_auto_closed` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--

-- --------------------------------------------------------

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `izin`
--
ALTER TABLE `izin`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kas`
--
ALTER TABLE `kas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meja`
--
ALTER TABLE `meja`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `metode_bayar`
--
ALTER TABLE `metode_bayar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `peran`
--
ALTER TABLE `peran`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan_item`
--
ALTER TABLE `pesanan_item`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan_riwayat`
--
ALTER TABLE `pesanan_riwayat`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

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
