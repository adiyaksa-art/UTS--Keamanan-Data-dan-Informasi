-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 18, 2026 at 04:19 PM
-- Server version: 10.6.24-MariaDB-log
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kripto_si`
--

-- --------------------------------------------------------

--
-- Table structure for table `konten_terenkripsi`
--

CREATE TABLE `konten_terenkripsi` (
  `id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `ciphertext` text NOT NULL,
  `iv` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `konten_terenkripsi`
--

INSERT INTO `konten_terenkripsi` (`id`, `judul`, `ciphertext`, `iv`, `created_at`) VALUES
(1, 'Berita Hari ini', 'azSK7IcIQodyG+qWKhzWbd4RXBiY8Kf9NIVUFCKni8dG/qdGswL4R2/XPwg0GQr3fwRXT6iPza+hscd9AWxuY55iHPvnocH5m1gnOkV1kSg=', 'eU68TRG1x0HiWFzGJQDkZg==', '2026-11-18 14:26:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `konten_terenkripsi`
--
ALTER TABLE `konten_terenkripsi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `konten_terenkripsi`
--
ALTER TABLE `konten_terenkripsi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
