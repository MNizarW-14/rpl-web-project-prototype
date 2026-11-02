-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2026 at 07:51 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `published_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `category_id`, `title`, `excerpt`, `content`, `image_url`, `is_featured`, `published_at`) VALUES
(1, 1, 'Evaluasi Transparansi Alokasi Anggaran Fasilitas Ormawa Kampus 2026', 'Hasil penelusuran mendalam tim pers terkait alokasi dana kegiatan mahasiswa dan infrastruktur laboratorium di semester ganjil ini.', '<p>Ini adalah isi detail dari artikel berita. Dalam kerja jurnalistik pers mahasiswa, bagian ini memuat fakta-fakta lapangan secara mendalam, termasuk latar belakang masalah, kronologi, serta dampaknya bagi civitas akademika.</p><br><p>Berbagai pihak turut memberikan suaranya terkait isu ini. \"Kami berharap kebijakan ini tidak hanya sekadar wacana, tapi direalisasikan dengan transparan,\" ujar salah satu perwakilan mahasiswa saat diwawancarai oleh tim redaksi.</p><br><p>Liputan ini akan terus dipantau perkembangannya dan tim redaksi membuka ruang diskusi terbuka bagi pihak-pihak terkait untuk memberikan hak jawab atau opini tambahannya.</p>', 'https://picsum.photos/800/500?random=1', 1, '2026-10-26 08:00:00'),
(2, 2, 'Ujian Akhir Semester Dijadwalkan Berbasis Proyek Interaktif', 'Rektorat resmi menetapkan ujian tulis diganti dengan asesmen proyek.', '<p>Ini adalah isi detail dari artikel berita. Dalam kerja jurnalistik pers mahasiswa, bagian ini memuat fakta-fakta lapangan secara mendalam, termasuk latar belakang masalah, kronologi, serta dampaknya bagi civitas akademika.</p><br><p>Berbagai pihak turut memberikan suaranya terkait isu ini. \"Kami berharap kebijakan ini tidak hanya sekadar wacana, tapi direalisasikan dengan transparan,\" ujar salah satu perwakilan mahasiswa saat diwawancarai oleh tim redaksi.</p><br><p>Liputan ini akan terus dipantau perkembangannya dan tim redaksi membuka ruang diskusi terbuka bagi pihak-pihak terkait untuk memberikan hak jawab atau opini tambahannya.</p>', 'https://picsum.photos/150/150?random=2', 0, '2026-10-25 10:00:00'),
(3, 3, 'Tantangan Mahasiswa dalam Menghadapi Disrupsi AI di Perkuliahan', 'Apakah AI mematikan kreativitas atau justru mempercepat inovasi? Berikut pandangannya.', '<p>Ini adalah isi detail dari artikel berita. Dalam kerja jurnalistik pers mahasiswa, bagian ini memuat fakta-fakta lapangan secara mendalam, termasuk latar belakang masalah, kronologi, serta dampaknya bagi civitas akademika.</p><br><p>Berbagai pihak turut memberikan suaranya terkait isu ini. \"Kami berharap kebijakan ini tidak hanya sekadar wacana, tapi direalisasikan dengan transparan,\" ujar salah satu perwakilan mahasiswa saat diwawancarai oleh tim redaksi.</p><br><p>Liputan ini akan terus dipantau perkembangannya dan tim redaksi membuka ruang diskusi terbuka bagi pihak-pihak terkait untuk memberikan hak jawab atau opini tambahannya.</p>', 'https://picsum.photos/150/150?random=3', 0, '2026-10-24 14:00:00'),
(4, 4, 'Pameran Karya Mahasiswa Desain Tampilkan Isu Lingkungan', 'Mahasiswa DKV menyulap limbah plastik menjadi instalasi seni bernilai tinggi.', '<p>Ini adalah isi detail dari artikel berita. Dalam kerja jurnalistik pers mahasiswa, bagian ini memuat fakta-fakta lapangan secara mendalam, termasuk latar belakang masalah, kronologi, serta dampaknya bagi civitas akademika.</p><br><p>Berbagai pihak turut memberikan suaranya terkait isu ini. \"Kami berharap kebijakan ini tidak hanya sekadar wacana, tapi direalisasikan dengan transparan,\" ujar salah satu perwakilan mahasiswa saat diwawancarai oleh tim redaksi.</p><br><p>Liputan ini akan terus dipantau perkembangannya dan tim redaksi membuka ruang diskusi terbuka bagi pihak-pihak terkait untuk memberikan hak jawab atau opini tambahannya.</p>', 'https://picsum.photos/150/150?random=4', 0, '2026-10-23 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `color_class` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `color_class`) VALUES
(1, 'Laporan Utama', 'text-[#B01050]'),
(2, 'Kampus', 'text-[#B01050]'),
(3, 'Opini', 'text-[#000000]'),
(4, 'Seni Budaya', 'text-[#B01050]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

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
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
