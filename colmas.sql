-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2024 at 04:56 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `colmas`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `log_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` int(11) DEFAULT NULL,
  `causer_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_id`, `subject_type`, `causer_id`, `causer_type`, `properties`, `created_at`, `updated_at`) VALUES
(1, 'default', 'created', 1, 'App\\Models\\User', NULL, NULL, '{\"attributes\":{\"id\":1,\"name\":\"Super Admin\",\"staff_id\":100001,\"email\":\"hazimahpte@gmail.com\",\"email_verified_at\":\"2024-10-10 10:48:58\",\"password\":\"$2y$10$.Gk\\/kclqDEJbYZShzgIwEutOa8BkfdYNODyaP.UAXyVK6BQ5aQQOe\",\"position_id\":1,\"campus_id\":2,\"office_phone_no\":\"082000000\",\"publish_status\":\"Aktif\",\"remember_token\":null,\"deleted_at\":null,\"created_at\":\"2024-10-10 10:48:58\",\"updated_at\":\"2024-10-10 10:48:58\"}}', '2024-10-10 02:48:58', '2024-10-10 02:48:58'),
(2, 'default', 'updated', 1, 'App\\Models\\User', 1, 'App\\Models\\User', '{\"attributes\":{\"remember_token\":\"7fRO0jaZkGN7qwr7TjZX1zZD3O6oKEPC0kJpeDCzilwvtlzDTouS8rG8TSVO\"},\"old\":{\"remember_token\":null}}', '2024-10-10 02:49:03', '2024-10-10 02:49:03');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campuses`
--

CREATE TABLE `campuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campuses`
--

INSERT INTO `campuses` (`id`, `name`, `publish_status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Samarahan', 1, NULL, NULL, NULL),
(2, 'Samarahan 2', 1, NULL, NULL, NULL),
(3, 'Mukah', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `computer_labs`
--

CREATE TABLE `computer_labs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campus_id` bigint(20) UNSIGNED NOT NULL,
  `pemilik_id` bigint(20) UNSIGNED NOT NULL,
  `no_of_computer` int(11) NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `computer_labs`
--

INSERT INTO `computer_labs` (`id`, `code`, `name`, `campus_id`, `pemilik_id`, `no_of_computer`, `username`, `password`, `publish_status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'B4001', 'Makmal Komputer 1', 1, 3, 5, 'UiTM', 'uitm123', 1, NULL, NULL, NULL),
(2, 'B4002', 'Makmal Komputer 2', 2, 2, 3, 'UiTM 1', 'uitm123', 1, NULL, NULL, NULL),
(3, 'B4003', 'Makmal Komputer 3', 2, 2, 2, 'UiTM 1', 'uitm123', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `computer_lab_histories`
--

CREATE TABLE `computer_lab_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `computer_lab_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pc_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` bigint(20) UNSIGNED NOT NULL,
  `publish_status` int(11) NOT NULL,
  `month_year` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `computer_lab_histories`
--

INSERT INTO `computer_lab_histories` (`id`, `computer_lab_id`, `code`, `name`, `pc_no`, `owner`, `publish_status`, `month_year`, `action`, `created_at`, `updated_at`) VALUES
(1, 1, 'B4001', 'Makmal Komputer 1', '5', 3, 1, '2024-10-10 02:48:58', 'Tambah', '2024-10-10 02:48:58', '2024-10-10 02:48:58'),
(2, 2, 'B4002', 'Makmal Komputer 2', '3', 2, 1, '2024-10-10 02:48:58', 'Tambah', '2024-10-10 02:48:58', '2024-10-10 02:48:58'),
(3, 3, 'B4003', 'Makmal Komputer 3', '2', 2, 1, '2024-10-10 02:48:58', 'Tambah', '2024-10-10 02:48:58', '2024-10-10 02:48:58');

-- --------------------------------------------------------

--
-- Table structure for table `lab_checklists`
--

CREATE TABLE `lab_checklists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_checklists`
--

INSERT INTO `lab_checklists` (`id`, `title`, `publish_status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'PC dinomborkan', 1, NULL, NULL, NULL),
(2, 'Pintu Kayu', 1, NULL, NULL, NULL),
(3, 'Pintu Grill', 1, NULL, NULL, NULL),
(4, 'Penghawa Dingin', 1, NULL, NULL, NULL),
(5, 'Peraturan Makmal', 1, NULL, NULL, NULL),
(6, 'Tanda Nama Makmal', 1, NULL, NULL, NULL),
(7, 'Whiteboard', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lab_managements`
--

CREATE TABLE `lab_managements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `computer_lab_id` bigint(20) UNSIGNED NOT NULL,
  `lab_checklist_id` text COLLATE utf8mb4_unicode_ci,
  `software_id` text COLLATE utf8mb4_unicode_ci,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `computer_no` int(11) NOT NULL,
  `pc_maintenance_no` int(11) DEFAULT NULL,
  `pc_unmaintenance_no` int(11) DEFAULT NULL,
  `pc_damage_no` int(11) DEFAULT NULL,
  `remarks_submitter` text COLLATE utf8mb4_unicode_ci,
  `remarks_checker` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `checked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `checked_at` timestamp NULL DEFAULT NULL,
  `submitted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_records`
--

CREATE TABLE `maintenance_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `computer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lab_management_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_checklist_id` text COLLATE utf8mb4_unicode_ci,
  `aduan_unit_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vms_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `entry_option` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2024_08_13_005801_create_permission_tables', 1),
(4, '2024_08_13_011057_create_activity_log_table', 1),
(5, '2024_08_13_014639_create_positions_table', 1),
(6, '2024_08_13_014710_create_campuses_table', 1),
(7, '2024_08_13_015728_add_publish_status_to_roles_table', 1),
(8, '2024_08_13_015851_add_category_to_permissions_table', 1),
(9, '2024_09_05_084108_create_computer_labs_table', 1),
(10, '2024_09_05_084709_create_computer_lab_histories_table', 1),
(11, '2024_09_06_063657_create_software_table', 1),
(12, '2024_09_06_065440_create_work_checklists_table', 1),
(13, '2024_09_06_070515_create_lab_checklists_table', 1),
(14, '2024_09_08_143632_create_lab_managements_table', 1),
(15, '2024_09_08_144208_create_maintenance_records_table', 1),
(16, '2024_09_09_133447_create_announcements_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(4, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `category`) VALUES
(1, 'Tambah Pengguna', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Pengguna'),
(2, 'Edit Pengguna', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Pengguna'),
(3, 'Padam Pengguna', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Pengguna'),
(4, 'Lihat Pengguna', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Pengguna'),
(5, 'Tambah Kampus', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Kampus'),
(6, 'Edit Kampus', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Kampus'),
(7, 'Padam Kampus', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Kampus'),
(8, 'Lihat Kampus', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Kampus'),
(9, 'Tambah Makmal Komputer', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Makmal Komputer'),
(10, 'Edit Makmal Komputer', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Makmal Komputer'),
(11, 'Padam Makmal Komputer', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Makmal Komputer'),
(12, 'Lihat Makmal Komputer', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Makmal Komputer'),
(13, 'Tambah Perisian', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Perisian'),
(14, 'Edit Perisian', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Perisian'),
(15, 'Padam Perisian', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Perisian'),
(16, 'Lihat Perisian', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Perisian'),
(17, 'Tambah Senarai Semak Proses Kerja', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Senarai Semak Proses Kerja'),
(18, 'Edit Senarai Semak Proses Kerja', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Senarai Semak Proses Kerja'),
(19, 'Padam Senarai Semak Proses Kerja', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Senarai Semak Proses Kerja'),
(20, 'Lihat Senarai Semak Proses Kerja', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Senarai Semak Proses Kerja'),
(21, 'Tambah Senarai Semak Makmal', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Senarai Semak Makmal'),
(22, 'Edit Senarai Semak Makmal', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Senarai Semak Makmal'),
(23, 'Padam Senarai Semak Makmal', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Senarai Semak Makmal'),
(24, 'Lihat Senarai Semak Makmal', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Senarai Semak Makmal'),
(25, 'Tambah Rekod Selenggara Makmal', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Rekod Selenggara Makmal'),
(26, 'Edit Rekod Selenggara Makmal', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Rekod Selenggara Makmal'),
(27, 'Padam Rekod Selenggara Makmal', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Rekod Selenggara Makmal'),
(28, 'Lihat Rekod Selenggara Makmal', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Rekod Selenggara Makmal'),
(29, 'Tambah Rekod Selenggara Komputer', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Rekod Selenggara Komputer'),
(30, 'Edit Rekod Selenggara Komputer', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Rekod Selenggara Komputer'),
(31, 'Padam Rekod Selenggara Komputer', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Rekod Selenggara Komputer'),
(32, 'Lihat Rekod Selenggara Komputer', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Rekod Selenggara Komputer'),
(33, 'Tambah Jawatan', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Jawatan'),
(34, 'Edit Jawatan', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Jawatan'),
(35, 'Padam Jawatan', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Jawatan'),
(36, 'Lihat Jawatan', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Jawatan'),
(37, 'Tambah Makluman', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Makluman'),
(38, 'Edit Makluman', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Makluman'),
(39, 'Padam Makluman', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Makluman'),
(40, 'Lihat Makluman', 'web', '2024-10-10 02:48:58', '2024-10-10 02:48:58', 'Pengurusan Makluman');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `title`, `grade`, `publish_status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Pegawai Teknologi Maklumat', 'F41', 1, NULL, NULL, NULL),
(2, 'Penolong Pegawai Teknologi Maklumat Kanan', 'FA32', 1, NULL, NULL, NULL),
(3, 'Penolong Pegawai Teknologi Maklumat', 'FA29', 1, NULL, NULL, NULL),
(4, 'Juruteknik Komputer', 'FT22', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `publish_status`, `deleted_at`) VALUES
(1, 'Superadmin', 'web', NULL, NULL, 1, NULL),
(2, 'Admin', 'web', NULL, '2024-10-10 02:55:40', 0, NULL),
(3, 'Pegawai Penyemak', 'web', NULL, NULL, 1, NULL),
(4, 'Pemilik', 'web', NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(17, 1),
(17, 2),
(18, 1),
(18, 2),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(23, 1),
(23, 2),
(24, 1),
(24, 2),
(25, 1),
(25, 2),
(25, 3),
(25, 4),
(26, 1),
(26, 2),
(26, 3),
(26, 4),
(27, 1),
(27, 2),
(27, 3),
(27, 4),
(28, 1),
(28, 2),
(28, 3),
(28, 4),
(29, 1),
(29, 2),
(29, 3),
(29, 4),
(30, 1),
(30, 2),
(30, 3),
(30, 4),
(31, 1),
(31, 2),
(31, 3),
(31, 4),
(32, 1),
(32, 2),
(32, 3),
(32, 4),
(33, 1),
(33, 2),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(36, 1),
(36, 2),
(37, 1),
(37, 2),
(37, 3),
(38, 1),
(38, 2),
(38, 3),
(39, 1),
(39, 2),
(39, 3),
(40, 1),
(40, 2),
(40, 3);

-- --------------------------------------------------------

--
-- Table structure for table `software`
--

CREATE TABLE `software` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `software`
--

INSERT INTO `software` (`id`, `title`, `publish_status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Adobe Photoshop', 1, NULL, NULL, NULL),
(2, 'AutoCAD', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `staff_id` int(11) NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED NOT NULL,
  `campus_id` bigint(20) UNSIGNED NOT NULL,
  `office_phone_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `staff_id`, `email`, `email_verified_at`, `password`, `position_id`, `campus_id`, `office_phone_no`, `publish_status`, `remember_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 100001, 'hazimahpte@gmail.com', '2024-10-10 02:48:58', '$2y$10$.Gk/kclqDEJbYZShzgIwEutOa8BkfdYNODyaP.UAXyVK6BQ5aQQOe', 1, 2, '082000000', 1, '7fRO0jaZkGN7qwr7TjZX1zZD3O6oKEPC0kJpeDCzilwvtlzDTouS8rG8TSVO', NULL, '2024-10-10 02:48:58', '2024-10-10 02:48:58'),
(2, 'John Doe', 111111, 'john@gmail.com', '2024-10-10 02:48:58', '$2y$10$83tB2aNWpcuucn4O3.GFsuDX5/IRa1dKq7VsvIZZaaLifzN3KCKR2', 3, 2, '082111111', 1, NULL, NULL, NULL, NULL),
(3, 'Hiatus', 222222, 'hiatus@gmail.com', '2024-10-10 02:48:58', '$2y$10$FoY1.z9D2Xduu/oIymrEk.Pfa4j7FK22019ba.Xo5rAMqu8Gb1yU2', 2, 1, '082123456', 1, NULL, NULL, NULL, NULL),
(4, 'Smith', 333333, 'smith@gmail.com', '2024-10-10 02:48:58', '$2y$10$2O36cmAs91yjzD3za.4mx.x9dDmozUauQ15piTY6YXdhEASG4BBKu', 1, 1, '082123456', 1, NULL, NULL, NULL, NULL),
(5, 'James', 444444, 'james@gmail.com', '2024-10-10 02:48:58', '$2y$10$XRosyFsIecdZrJTXjhJ5P.BYEKX7e640iXCRPb7td0bedKuZUUs8u', 1, 2, '082123456', 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `work_checklists`
--

CREATE TABLE `work_checklists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_checklists`
--

INSERT INTO `work_checklists` (`id`, `title`, `publish_status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Disk Cleanup', 1, NULL, NULL, NULL),
(2, 'Scandisk', 1, NULL, NULL, NULL),
(3, 'Antivirus', 1, NULL, NULL, NULL),
(4, 'Windows Update', 1, NULL, NULL, NULL),
(5, 'Disk Defragment', 1, NULL, NULL, NULL),
(6, 'Rangkaian', 1, NULL, NULL, NULL),
(7, 'Ghosting', 0, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campuses`
--
ALTER TABLE `campuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `campuses_name_unique` (`name`);

--
-- Indexes for table `computer_labs`
--
ALTER TABLE `computer_labs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `computer_labs_name_campus_id_unique` (`name`,`campus_id`),
  ADD KEY `computer_labs_campus_id_foreign` (`campus_id`),
  ADD KEY `computer_labs_pemilik_id_foreign` (`pemilik_id`);

--
-- Indexes for table `computer_lab_histories`
--
ALTER TABLE `computer_lab_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `computer_lab_histories_computer_lab_id_foreign` (`computer_lab_id`);

--
-- Indexes for table `lab_checklists`
--
ALTER TABLE `lab_checklists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_checklists_title_unique` (`title`);

--
-- Indexes for table `lab_managements`
--
ALTER TABLE `lab_managements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_managements_computer_lab_id_start_time_end_time_unique` (`computer_lab_id`,`start_time`,`end_time`);

--
-- Indexes for table `maintenance_records`
--
ALTER TABLE `maintenance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_records_lab_management_id_foreign` (`lab_management_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `positions_grade_unique` (`grade`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `software`
--
ALTER TABLE `software`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `software_title_unique` (`title`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_staff_id_unique` (`staff_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `work_checklists`
--
ALTER TABLE `work_checklists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_checklists_title_unique` (`title`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `campuses`
--
ALTER TABLE `campuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `computer_labs`
--
ALTER TABLE `computer_labs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `computer_lab_histories`
--
ALTER TABLE `computer_lab_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `lab_checklists`
--
ALTER TABLE `lab_checklists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `lab_managements`
--
ALTER TABLE `lab_managements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `maintenance_records`
--
ALTER TABLE `maintenance_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `software`
--
ALTER TABLE `software`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `work_checklists`
--
ALTER TABLE `work_checklists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `computer_labs`
--
ALTER TABLE `computer_labs`
  ADD CONSTRAINT `computer_labs_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `computer_labs_pemilik_id_foreign` FOREIGN KEY (`pemilik_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `computer_lab_histories`
--
ALTER TABLE `computer_lab_histories`
  ADD CONSTRAINT `computer_lab_histories_computer_lab_id_foreign` FOREIGN KEY (`computer_lab_id`) REFERENCES `computer_labs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_records`
--
ALTER TABLE `maintenance_records`
  ADD CONSTRAINT `maintenance_records_lab_management_id_foreign` FOREIGN KEY (`lab_management_id`) REFERENCES `lab_managements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
