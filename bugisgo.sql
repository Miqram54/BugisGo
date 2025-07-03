-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 11:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bugisgo`
--

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `pembayaranID` int(11) NOT NULL,
  `pemesananID` int(11) DEFAULT NULL,
  `metodePembayaran` varchar(50) DEFAULT NULL,
  `statusPembayaran` varchar(20) DEFAULT NULL,
  `tanggalPembayaran` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `pemesananID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `tiketID` int(11) NOT NULL,
  `jumlahTiket` int(11) NOT NULL,
  `totalHarga` double NOT NULL,
  `statusPembayaran` varchar(50) NOT NULL,
  `tanggalPemesanan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`pemesananID`, `userID`, `tiketID`, `jumlahTiket`, `totalHarga`, `statusPembayaran`, `tanggalPemesanan`) VALUES
(1, 1, 1, 2, 200000, 'lunas', '2025-03-27'),
(2, 2, 6, 1, 120000, 'belum bayar', '2025-03-26'),
(3, 3, 1, 3, 300000, 'lunas', '2025-03-25'),
(4, 0, 6, 1, 120000, 'pending', '2025-03-27'),
(5, 0, 6, 1, 150000, 'pending', '2025-03-28'),
(6, 0, 7, 1, 200000, 'pending', '2025-03-28'),
(7, 0, 6, 1, 150000, 'pending', '2025-03-28'),
(8, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(9, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(10, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(11, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(12, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(13, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(14, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(15, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(16, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(17, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(18, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(19, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(20, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(21, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(22, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(23, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(24, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(25, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(26, 2, 6, 1, 150000, 'pending', '2025-03-29'),
(27, 2, 6, 1, 150000, 'pending', '2025-03-29');

-- --------------------------------------------------------

--
-- Table structure for table `tiket`
--

CREATE TABLE `tiket` (
  `tiketID` int(11) NOT NULL,
  `namaWahana` varchar(100) NOT NULL,
  `harga` double NOT NULL,
  `jamOperasional` varchar(50) NOT NULL,
  `status` enum('tersedia','habis') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tiket`
--

INSERT INTO `tiket` (`tiketID`, `namaWahana`, `harga`, `jamOperasional`, `status`) VALUES
(6, 'Senin - Kamiss', 150000, '09:00 - 17:00', 'tersedia'),
(7, 'Sabtu - Minggu', 200000, '07:00 - 15:00', 'tersedia'),
(8, 'Gazebo', 200000, '-', 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `ulasanID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `namaWahana` varchar(255) NOT NULL,
  `rating` int(1) NOT NULL,
  `komentar` text DEFAULT NULL,
  `tanggalUlasan` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`ulasanID`, `userID`, `namaWahana`, `rating`, `komentar`, `tanggalUlasan`) VALUES
(10, 1, 'Wahana Air', 4, 'Seru banget! Saya suka bermain di sini.', '2025-03-27 19:18:12'),
(11, 1, 'Roller Coaster', 5, 'Tantangan yang luar biasa! Harus dicoba!', '2025-03-27 19:18:12'),
(12, 1, 'Kolam Renang', 3, 'Cukup menyenangkan, namun perlu perbaikan pada fasilitas.', '2025-03-27 19:18:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pengunjung','pengelola') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2a$12$BrCa3g/50ncirgmmWpyfzehcmlclQZFbhuFxmULx.BUs049cc6tyO', 'admin'),
(2, 'alyaa', 'alya@gmail.com', '$2y$10$VB2XZLV7m.8WcrdUKz1Gz.iVvordmBdMc0SEXJ8eVZH34U80EwUzC', 'pengunjung'),
(5, 'pengelola', 'tess@gmail.com', '$2y$10$3afOjySAHoXykrDuWb.hEe8M.g0cJWSIYcgHqDMOgCf6is6JjbrRi', 'pengelola');

-- --------------------------------------------------------

--
-- Table structure for table `wahana`
--

CREATE TABLE `wahana` (
  `wahanaID` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `persyaratanUsia` int(11) DEFAULT NULL,
  `persyaratanTinggi` int(11) DEFAULT NULL,
  `rating` double DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wahana`
--

INSERT INTO `wahana` (`wahanaID`, `nama`, `deskripsi`, `persyaratanUsia`, `persyaratanTinggi`, `rating`, `gambar`) VALUES
(1, 'Wahana Air', 'Wahana air yang seru untuk segala usia, cocok untuk keluarga dan anak-anak.', 4, 120, 4.5, 'images/reikaa.png'),
(2, 'Roller Coaster', 'Wahana ekstrem yang cocok untuk dewasa dengan berbagai macam tantangan.', 15, 140, 4.8, 'images/bugis-waterpark_20170630_080630.jpg'),
(3, 'Kolam Renang', 'Kolam renang yang luas dan nyaman untuk relaksasi.', 4, 110, 0, 'images/Tiket-Bugis-Waterpark-Adventure-a5e3129c-48d6-4140-8b05-b3e949512edf.webp');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`pembayaranID`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`pemesananID`);

--
-- Indexes for table `tiket`
--
ALTER TABLE `tiket`
  ADD PRIMARY KEY (`tiketID`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`ulasanID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wahana`
--
ALTER TABLE `wahana`
  ADD PRIMARY KEY (`wahanaID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `pemesananID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tiket`
--
ALTER TABLE `tiket`
  MODIFY `tiketID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `ulasanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wahana`
--
ALTER TABLE `wahana`
  MODIFY `wahanaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
