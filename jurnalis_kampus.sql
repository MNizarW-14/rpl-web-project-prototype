-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2026 at 04:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jurnalis_kampus`
--
CREATE DATABASE IF NOT EXISTS `jurnalis_kampus` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `jurnalis_kampus`;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `image_caption` varchar(255) DEFAULT NULL,
  `status` enum('draft','terbit') NOT NULL DEFAULT 'terbit',
  `views` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `category_id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `image_url`, `is_featured`, `published_at`, `image_caption`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Evaluasi Transparansi Alokasi Anggaran Fasilitas Ormawa Kampus 2026', 'artikel-1', 'Hasil penelusuran mendalam tim pers terkait alokasi dana kegiatan mahasiswa dan infrastruktur laboratorium di semester ganjil ini.', '<p>Ini adalah isi detail dari artikel berita. Dalam kerja jurnalistik pers mahasiswa, bagian ini memuat fakta-fakta lapangan secara mendalam, termasuk latar belakang masalah, kronologi, serta dampaknya bagi civitas akademika.</p><br><p>Berbagai pihak turut memberikan suaranya terkait isu ini. \"Kami berharap kebijakan ini tidak hanya sekadar wacana, tapi direalisasikan dengan transparan,\" ujar salah satu perwakilan mahasiswa saat diwawancarai oleh tim redaksi.</p><br><p>Liputan ini akan terus dipantau perkembangannya dan tim redaksi membuka ruang diskusi terbuka bagi pihak-pihak terkait untuk memberikan hak jawab atau opini tambahannya.</p>', 'https://picsum.photos/800/500?random=1', 1, '2026-10-26 08:00:00', NULL, 'terbit', 0, '2026-11-10 03:39:45', '2026-11-10 03:39:57'),
(2, 2, 1, 'Ujian Akhir Semester Dijadwalkan Berbasis Proyek Interaktif', 'artikel-2', 'Rektorat resmi menetapkan ujian tulis diganti dengan asesmen proyek.', '<p>Ini adalah isi detail dari artikel berita. Dalam kerja jurnalistik pers mahasiswa, bagian ini memuat fakta-fakta lapangan secara mendalam, termasuk latar belakang masalah, kronologi, serta dampaknya bagi civitas akademika.</p><br><p>Berbagai pihak turut memberikan suaranya terkait isu ini. \"Kami berharap kebijakan ini tidak hanya sekadar wacana, tapi direalisasikan dengan transparan,\" ujar salah satu perwakilan mahasiswa saat diwawancarai oleh tim redaksi.</p><br><p>Liputan ini akan terus dipantau perkembangannya dan tim redaksi membuka ruang diskusi terbuka bagi pihak-pihak terkait untuk memberikan hak jawab atau opini tambahannya.</p>', 'https://picsum.photos/150/150?random=2', 0, '2026-10-25 10:00:00', NULL, 'terbit', 0, '2026-11-10 03:39:45', '2026-11-10 03:39:57'),
(3, 3, 1, 'Tantangan Mahasiswa dalam Menghadapi Disrupsi AI di Perkuliahan', 'artikel-3', 'Apakah AI mematikan kreativitas atau justru mempercepat inovasi? Berikut pandangannya.', '<p>Ini adalah isi detail dari artikel berita. Dalam kerja jurnalistik pers mahasiswa, bagian ini memuat fakta-fakta lapangan secara mendalam, termasuk latar belakang masalah, kronologi, serta dampaknya bagi civitas akademika.</p><br><p>Berbagai pihak turut memberikan suaranya terkait isu ini. \"Kami berharap kebijakan ini tidak hanya sekadar wacana, tapi direalisasikan dengan transparan,\" ujar salah satu perwakilan mahasiswa saat diwawancarai oleh tim redaksi.</p><br><p>Liputan ini akan terus dipantau perkembangannya dan tim redaksi membuka ruang diskusi terbuka bagi pihak-pihak terkait untuk memberikan hak jawab atau opini tambahannya.</p>', 'https://picsum.photos/150/150?random=3', 0, '2026-10-24 14:00:00', NULL, 'terbit', 0, '2026-11-10 03:39:45', '2026-11-10 03:39:57'),
(4, 4, 1, 'Pameran Karya Mahasiswa Desain Tampilkan Isu Lingkungan', 'artikel-4', 'Mahasiswa DKV menyulap limbah plastik menjadi instalasi seni bernilai tinggi.', '<p>Ini adalah isi detail dari artikel berita. Dalam kerja jurnalistik pers mahasiswa, bagian ini memuat fakta-fakta lapangan secara mendalam, termasuk latar belakang masalah, kronologi, serta dampaknya bagi civitas akademika.</p><br><p>Berbagai pihak turut memberikan suaranya terkait isu ini. \"Kami berharap kebijakan ini tidak hanya sekadar wacana, tapi direalisasikan dengan transparan,\" ujar salah satu perwakilan mahasiswa saat diwawancarai oleh tim redaksi.</p><br><p>Liputan ini akan terus dipantau perkembangannya dan tim redaksi membuka ruang diskusi terbuka bagi pihak-pihak terkait untuk memberikan hak jawab atau opini tambahannya.</p>', 'https://picsum.photos/150/150?random=4', 0, '2026-10-23 09:00:00', NULL, 'terbit', 0, '2026-11-10 03:39:45', '2026-11-10 03:39:57');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `color_class` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `color_class`, `description`) VALUES
(1, 'Laporan Utama', 'laporan-utama', 'text-[#B01050]', NULL),
(2, 'Kampus', 'kampus', 'text-[#B01050]', NULL),
(3, 'Opini', 'opini', 'text-[#000000]', NULL),
(4, 'Seni Budaya', 'seni-budaya', 'text-[#B01050]', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `prodi` varchar(100) NOT NULL,
  `nim` varchar(30) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `pengantar` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('baru','diproses','diterima','ditolak') NOT NULL DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','editor','penulis') NOT NULL DEFAULT 'penulis',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Tim Redaksi', 'redaksi@jurnalisnuansa.test', 'belum-dipakai', 'admin', '2026-11-10 03:39:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_articles_slug` (`slug`),
  ADD KEY `fk_articles_author` (`author_id`),
  ADD KEY `fk_articles_category` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_slug` (`slug`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_submissions_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_articles_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_submissions_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
