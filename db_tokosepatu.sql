-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 01:03 AM
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
-- Database: `db_tokosepatu`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `Id_sepatu` int(11) NOT NULL,
  `Merk_sepatu` varchar(50) DEFAULT NULL,
  `Jenis_sepatu` varchar(50) DEFAULT NULL,
  `No_sepatu` int(11) DEFAULT NULL,
  `Stok` int(11) DEFAULT NULL,
  `Id_pemasok` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`Id_sepatu`, `Merk_sepatu`, `Jenis_sepatu`, `No_sepatu`, `Stok`, `Id_pemasok`) VALUES
(1, 'Converse', 'Casual', 40, 28, 1),
(2, 'Adidas', 'Running', 42, 11, 3),
(3, 'Warrior', 'Casual', 38, 3, 2),
(4, 'Nike', 'Running', 43, 11, 3);

-- --------------------------------------------------------

--
-- Table structure for table `det_pembelian`
--

CREATE TABLE `det_pembelian` (
  `Id_sepatu` int(11) NOT NULL,
  `No_nota` int(11) NOT NULL,
  `Jumlah` decimal(10,0) DEFAULT NULL,
  `Harga` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `det_pembelian`
--

INSERT INTO `det_pembelian` (`Id_sepatu`, `No_nota`, `Jumlah`, `Harga`) VALUES
(3, 1, 500, 150000),
(3, 2, 3, 170000),
(4, 1, 3, 300000);

-- --------------------------------------------------------

--
-- Table structure for table `det_penjualan`
--

CREATE TABLE `det_penjualan` (
  `Id_sepatu` int(11) NOT NULL,
  `No_nota` int(11) NOT NULL,
  `Jumlah` decimal(10,0) DEFAULT NULL,
  `Harga` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `det_penjualan`
--

INSERT INTO `det_penjualan` (`Id_sepatu`, `No_nota`, `Jumlah`, `Harga`) VALUES
(1, 1, 2, 250000),
(3, 1, 3, 140000);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `Id_pelanggan` int(11) NOT NULL,
  `Nama` varchar(50) DEFAULT NULL,
  `Alamat` varchar(100) DEFAULT NULL,
  `No_telp` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`Id_pelanggan`, `Nama`, `Alamat`, `No_telp`) VALUES
(1, 'Eunn', 'Jl. Belimbing No.10', '085591135389');

-- --------------------------------------------------------

--
-- Table structure for table `pemasok`
--

CREATE TABLE `pemasok` (
  `Id_pemasok` int(11) NOT NULL,
  `Nama` varchar(50) DEFAULT NULL,
  `Alamat` varchar(100) DEFAULT NULL,
  `No_telp` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemasok`
--

INSERT INTO `pemasok` (`Id_pemasok`, `Nama`, `Alamat`, `No_telp`) VALUES
(1, 'PT. BERKAH JAYA', 'Jl. Belimbing No.40', '087463438261'),
(2, 'PT. MAKMUR JAYA', 'Jl. Pahlawan No.10', '089463848363'),
(3, 'PT. JAYAPURA', 'Jl. Ambon No.97', '085591135382');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `No_nota` int(11) NOT NULL,
  `Tanggal` date DEFAULT NULL,
  `Id_pemasok` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`No_nota`, `Tanggal`, `Id_pemasok`) VALUES
(1, '2025-04-27', 3),
(2, '2025-04-28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`username`, `password`) VALUES
('Lumm', '$2y$10$3hdl3LqC0U2xNoP6eISBJu5pqePOVHMm4OjTxalWr.XE8kzy7u/cG');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `No_nota` int(11) NOT NULL,
  `Id_pelanggan` int(11) DEFAULT NULL,
  `Tgl_nota` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`No_nota`, `Id_pelanggan`, `Tgl_nota`) VALUES
(1, 1, '2025-04-27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`Id_sepatu`),
  ADD KEY `Id_pemasok` (`Id_pemasok`);

--
-- Indexes for table `det_pembelian`
--
ALTER TABLE `det_pembelian`
  ADD PRIMARY KEY (`Id_sepatu`,`No_nota`),
  ADD KEY `No_nota` (`No_nota`);

--
-- Indexes for table `det_penjualan`
--
ALTER TABLE `det_penjualan`
  ADD PRIMARY KEY (`Id_sepatu`,`No_nota`),
  ADD KEY `No_nota` (`No_nota`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`Id_pelanggan`);

--
-- Indexes for table `pemasok`
--
ALTER TABLE `pemasok`
  ADD PRIMARY KEY (`Id_pemasok`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`No_nota`),
  ADD KEY `Id_pemasok` (`Id_pemasok`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`No_nota`),
  ADD KEY `Id_Pelanggan` (`Id_pelanggan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `Id_sepatu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `Id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pemasok`
--
ALTER TABLE `pemasok`
  MODIFY `Id_pemasok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `No_nota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `No_nota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`Id_pemasok`) REFERENCES `pemasok` (`Id_pemasok`);

--
-- Constraints for table `det_pembelian`
--
ALTER TABLE `det_pembelian`
  ADD CONSTRAINT `det_pembelian_ibfk_1` FOREIGN KEY (`Id_sepatu`) REFERENCES `barang` (`Id_sepatu`),
  ADD CONSTRAINT `det_pembelian_ibfk_2` FOREIGN KEY (`No_nota`) REFERENCES `pembelian` (`No_nota`);

--
-- Constraints for table `det_penjualan`
--
ALTER TABLE `det_penjualan`
  ADD CONSTRAINT `det_penjualan_ibfk_1` FOREIGN KEY (`Id_sepatu`) REFERENCES `barang` (`Id_sepatu`),
  ADD CONSTRAINT `det_penjualan_ibfk_2` FOREIGN KEY (`No_nota`) REFERENCES `penjualan` (`No_nota`);

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`Id_pemasok`) REFERENCES `pemasok` (`Id_pemasok`);

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `Id_Pelanggan` FOREIGN KEY (`Id_pelanggan`) REFERENCES `pelanggan` (`Id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
