-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 04:21 AM
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
-- Database: `wesco`
--

-- --------------------------------------------------------

--
-- Table structure for table `afrn`
--

CREATE TABLE `afrn` (
  `id_afrn` int(11) NOT NULL,
  `tgl_afrn` date NOT NULL,
  `no_afrn` varchar(25) NOT NULL,
  `no_bpp` int(25) NOT NULL,
  `id_bridger` int(11) NOT NULL,
  `id_transportir` int(10) NOT NULL,
  `id_destinasi` int(10) NOT NULL,
  `id_tangki` int(10) NOT NULL,
  `dibuat` varchar(25) NOT NULL,
  `diperiksa` varchar(25) NOT NULL,
  `disetujui` varchar(25) NOT NULL,
  `rit` int(11) NOT NULL,
  `id_jarak_t1` int(10) UNSIGNED DEFAULT NULL,
  `id_bon` int(11) DEFAULT NULL,
  `id_driver` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `afrn`
--

INSERT INTO `afrn` (`id_afrn`, `tgl_afrn`, `no_afrn`, `no_bpp`, `id_bridger`, `id_transportir`, `id_destinasi`, `id_tangki`, `dibuat`, `diperiksa`, `disetujui`, `rit`, `id_jarak_t1`, `id_bon`, `id_driver`) VALUES
(1, '2025-05-22', '111', 11111, 9, 11111, 1111, 111, 'rendy', 'rendy', 'rendy', 2, 1, NULL, NULL),
(12, '2025-05-28', 'AFRN-1748416854', 0, 9, 3, 3, 4, 'rendy krisna', 'krisna', 'krisna', 3, 2, NULL, 0),
(13, '2025-05-28', 'AFRN-1748416937', 0, 11, 4, 6, 4, 'rendy krisna', 'krisna', 'krisna', 3, 3, NULL, NULL),
(14, '2025-05-28', 'AFRN-1748416989', 12345, 10, 4, 6, 4, 'rendy krisna', 'krisna', 'krisna', 4, 4, NULL, NULL),
(15, '2025-05-29', 'AFRN-1748494015', 123, 10, 4, 3, 3, 'rendy', 'rendy', 'rendt', 2, 5, NULL, NULL),
(16, '2025-05-29', 'AFRN-1748495645', 11222333, 9, 3, 2, 3, 'rendy', 'rendy', 'rendt', 2, NULL, NULL, NULL),
(17, '2025-05-29', 'AFRN-1748541168', 2147483647, 10, 4, 3, 3, 'rendy krisna', 'krisna', 'krisna', 2, NULL, NULL, NULL),
(18, '2025-05-30', 'AFRN-1748613945', 2147483647, 10, 4, 3, 2, 'rendy krisna', 'hasto', 'hasto', 2, NULL, NULL, NULL),
(19, '2025-05-30', 'AFRN-1748614726', 2147483647, 9, 3, 6, 3, 'rendy krisna', 'rahma', 'rahma', 2, NULL, NULL, NULL),
(20, '2025-06-01', 'AFRN-1748777205', 1234567890, 11, 4, 5, 3, 'rendy krisna', 'rahma', 'rahma', 2, NULL, 11, NULL),
(21, '2025-06-01', 'AFRN-1748783848', 2147483647, 11, 4, 4, 3, 'rendy krisna', 'hasto', 'hasto', 2, NULL, 7, NULL),
(22, '2025-06-01', 'AFRN-1748784050', 23239273, 11, 4, 4, 4, 'rahma', 'rahma', 'rahma', 4, NULL, 6, NULL),
(23, '2025-06-01', 'AFRN-1748785532', 2147483647, 11, 4, 5, 3, 'rendy krisna', 'hasto', 'hasto', 3, NULL, 5, NULL),
(24, '2025-06-01', 'AFRN-1748786978', 2147483647, 11, 4, 6, 4, 'rendy krisna', 'rahma', 'krisna', 33, NULL, 8, NULL),
(26, '2025-06-04', 'AFRN-1749014993', 128131, 9, 3, 2, 2, 'rendy krisna', 'hasto', 'krisna', 2, NULL, 3, NULL),
(27, '2025-06-06', 'AFRN-1749215517', 0, 12, 4, 5, 4, 'rahma', 'rahma', 'rahma', 2, NULL, 13, NULL),
(28, '2025-06-06', 'AFRN-1749232922', 2147483647, 12, 4, 6, 4, 'rahma', 'hasto', 'hasto', 2, NULL, NULL, NULL),
(29, '2025-06-06', 'AFRN-1749232983', 2147483647, 12, 4, 6, 4, 'rahma', 'hasto', 'hasto', 2, NULL, NULL, NULL),
(30, '2025-06-06', 'AFRN-1749233033', 2147483647, 10, 4, 2, 2, 'rahma', 'krisna', 'hasto', 2, NULL, NULL, NULL),
(31, '2025-06-06', 'AFRN-1749233092', 534342, 12, 4, 6, 4, 'rahma', 'hasto', 'krisna', 2, NULL, NULL, NULL),
(32, '2025-06-06', 'AFRN-1749233585', 34234342, 11, 4, 6, 4, 'rendy krisna', 'rahma', 'rahma', 2, NULL, 15, NULL),
(33, '2025-06-06', 'AFRN-1749235502', 2147483647, 11, 4, 5, 4, 'rendy krisna', 'rahma', 'hasto', 2, NULL, 16, NULL),
(34, '2025-06-08', 'AFRN-1749382059', 2147483647, 12, 4, 7, 6, 'hermanto', 'hermanto', 'hermanto', 2, NULL, 17, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bon`
--

CREATE TABLE `bon` (
  `id_bon` int(10) NOT NULL,
  `no_afrn` varchar(20) NOT NULL,
  `tgl_rekam` date NOT NULL,
  `jlh_pengisian` float NOT NULL,
  `meter_awal` float NOT NULL,
  `total_meter_akhir` float NOT NULL,
  `masuk_dppu` time NOT NULL,
  `mulai_pengisian` time NOT NULL,
  `selesai_pengisian` time NOT NULL,
  `water_cont_ter` time NOT NULL,
  `keluar_dppu` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bon`
--

INSERT INTO `bon` (`id_bon`, `no_afrn`, `tgl_rekam`, `jlh_pengisian`, `meter_awal`, `total_meter_akhir`, `masuk_dppu`, `mulai_pengisian`, `selesai_pengisian`, `water_cont_ter`, `keluar_dppu`) VALUES
(3, 'AFRN-1748495645', '2024-09-17', 1, 5400, 6300, '11:11:00', '11:11:00', '11:11:00', '11:11:00', '11:11:00'),
(4, 'AFRN-1748494015', '2025-05-26', 200, 22, 222, '13:11:00', '16:15:00', '17:11:00', '13:16:00', '13:14:00'),
(5, 'AFRN-1748416937', '2025-05-26', -100, 300, 200, '00:25:00', '04:19:00', '03:19:00', '01:19:00', '03:19:00'),
(6, 'AFRN-1748614726', '2025-06-01', 1000000, 111111, 1111110, '18:18:00', '18:18:00', '21:18:00', '20:18:00', '21:18:00'),
(7, 'AFRN-1748777205', '2025-06-01', 100, 100, 200, '18:32:00', '20:32:00', '22:32:00', '22:32:00', '20:32:00'),
(8, 'AFRN-1748416854', '2025-06-04', 999000, 1000, 1000000, '11:27:00', '11:27:00', '11:27:00', '11:27:00', '11:27:00'),
(9, 'AFRN-1748416854', '2025-06-04', 999999, 1, 1000000, '11:28:00', '11:28:00', '11:29:00', '11:31:00', '11:30:00'),
(10, 'AFRN-1748786978', '2025-06-04', 99000, 1000, 100000, '11:45:00', '11:45:00', '11:46:00', '11:49:00', '11:45:00'),
(11, 'AFRN-1749014993', '2025-06-04', 9000, 10000, 19000, '12:30:00', '12:31:00', '12:33:00', '12:31:00', '12:34:00'),
(12, 'AFRN-1749014993', '2025-06-04', 192434, 10000, 202434, '12:32:00', '12:32:00', '15:32:00', '12:33:00', '15:32:00'),
(13, 'AFRN-1749215517', '2025-06-06', 1980000, 20000, 2000000, '20:13:00', '20:13:00', '20:15:00', '20:13:00', '20:13:00'),
(14, 'AFRN-1749233092', '2025-06-06', 280000, 20000, 300000, '01:05:00', '01:07:00', '02:05:00', '01:06:00', '01:08:00'),
(15, 'AFRN-1749233585', '2025-06-06', 19980000, 20000, 20000000, '01:17:00', '01:17:00', '03:17:00', '01:18:00', '02:17:00'),
(16, 'AFRN-1749235502', '2025-06-06', 234221, 1211210, 1445430, '01:48:00', '01:48:00', '01:49:00', '01:45:00', '01:45:00'),
(17, 'AFRN-1749382059', '2025-06-08', 280000, 20000, 300000, '18:28:00', '20:30:00', '19:30:00', '20:31:00', '19:29:00');

-- --------------------------------------------------------

--
-- Table structure for table `bridger`
--

CREATE TABLE `bridger` (
  `id_bridger` int(10) NOT NULL,
  `id_trans` int(10) NOT NULL,
  `no_polisi` varchar(12) NOT NULL,
  `no_sertifikat` varchar(25) NOT NULL,
  `id_tipe_bridger` int(1) NOT NULL,
  `volume` int(11) NOT NULL,
  `tgl_serti_awal` varchar(25) NOT NULL,
  `tgl_serti_akhir` varchar(25) NOT NULL,
  `tera1` varchar(100) DEFAULT NULL,
  `tera2` varchar(100) DEFAULT NULL,
  `tera3` varchar(100) DEFAULT NULL,
  `tera4` varchar(100) DEFAULT NULL,
  `id_driver` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bridger`
--

INSERT INTO `bridger` (`id_bridger`, `id_trans`, `no_polisi`, `no_sertifikat`, `id_tipe_bridger`, `volume`, `tgl_serti_awal`, `tgl_serti_akhir`, `tera1`, `tera2`, `tera3`, `tera4`, `id_driver`) VALUES
(9, 3, 'B 9706 MODIF', '', 1, 1800, '', '2025-05-26', '12,3', '12,6', '12,7', '12,9', 1),
(10, 4, 'B 9706 UEM', '', 1, 2400, '', '2025-05-26', '12,6', '12,3', '12,7', '2', 1),
(11, 4, 'B 9706 INALU', '', 1, 1800, '', '2025-05-28', '12,4', '12,5', '12,7', '2', 1),
(12, 4, 'B 9706 ESSSS', '', 1, 32, '', '2025-06-01', '1', '2', '12,7', '2', 1),
(13, 4, 'B 9706 MODIF', '', 1, 32, '', '2025-06-07', '300', '400', '300', '300', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `destinasi`
--

CREATE TABLE `destinasi` (
  `id_destinasi` int(10) NOT NULL,
  `nama_destinasi` varchar(50) NOT NULL,
  `alamat_destinasi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinasi`
--

INSERT INTO `destinasi` (`id_destinasi`, `nama_destinasi`, `alamat_destinasi`) VALUES
(1, 'Destinasi A', 'Alamat Destinasi B'),
(2, 'PERTAMINA', 'JLN HASANUDIN'),
(3, 'COBA TES', 'COBA TES'),
(4, 'COBA RENDY', 'COBA RENDY'),
(5, 'COBA RENDY', 'COBA RENDY'),
(6, 'PT INALUM', 'PT INAMUN'),
(7, 'COBA LAS', 'COBA LASH'),
(8, 'PT MULTIMAS', 'kuala tanjung'),
(9, 'WILMAR NABATI', 'KUALA TANJUNG\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE `driver` (
  `id_bridger` int(10) NOT NULL,
  `nama_driver` varchar(25) NOT NULL,
  `no_ktp` int(16) NOT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `id_driver` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver`
--

INSERT INTO `driver` (`id_bridger`, `nama_driver`, `no_ktp`, `nama_lengkap`, `alamat`, `tempat_lahir`, `tanggal_lahir`, `id_driver`) VALUES
(0, '', 0, NULL, NULL, NULL, NULL, 1),
(11, 'rendy', 23232, NULL, NULL, NULL, NULL, 2),
(12, 'rendy', 123, NULL, NULL, NULL, NULL, 3),
(13, 'RANDY', 21212, 'RANDY KRNA', 'KUALA', 'kuala tanjung', '2025-06-07', 4),
(9, 'jaki driver', 11122, 'jaki', 'kuala tajung', 'kuala', '2025-06-07', 5),
(12, 'jiyan', 1221, 'jiyan', 'kuala', 'kuala', '2025-06-07', 6),
(12, 'rhama', 323131, 'rahma', 'kual', 'kual', '2025-06-07', 8);

-- --------------------------------------------------------

--
-- Table structure for table `jarak_cair_t1`
--

CREATE TABLE `jarak_cair_t1` (
  `id_jarak_cair_t1` int(10) NOT NULL,
  `jarak_cair_komp1` float NOT NULL,
  `dencity_cair_komp1` float NOT NULL,
  `temp_cair_komp_komp1` float NOT NULL,
  `jarak_cair_komp2` float NOT NULL,
  `dencity_cair_komp2` float NOT NULL,
  `temp_cair_komp_komp2` float NOT NULL,
  `jarak_cair_komp3` float NOT NULL,
  `dencity_cair_komp3` float NOT NULL,
  `temp_cair_komp_komp3` float NOT NULL,
  `jarak_cair_komp4` float NOT NULL,
  `dencity_cair_komp4` float NOT NULL,
  `temp_cair_komp_komp4` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jarak_cair_t1`
--

INSERT INTO `jarak_cair_t1` (`id_jarak_cair_t1`, `jarak_cair_komp1`, `dencity_cair_komp1`, `temp_cair_komp_komp1`, `jarak_cair_komp2`, `dencity_cair_komp2`, `temp_cair_komp_komp2`, `jarak_cair_komp3`, `dencity_cair_komp3`, `temp_cair_komp_komp3`, `jarak_cair_komp4`, `dencity_cair_komp4`, `temp_cair_komp_komp4`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(4, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(6, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(7, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(8, 1, 1, 1, 11, 1, 1, 1, 1, 1, 1, 1, 1),
(9, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(10, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(11, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9),
(12, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9),
(23, 22, 0.791, 30, 22, 0.791, 30, 22, 0.791, 30, 22, 0.791, 30),
(24, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30),
(25, 333, 0.791, 30, 333, 0.791, 30, 33, 0.791, 30, 333, 0.791, 30),
(26, 222, 0.791, 30, 222, 0.791, 30, 222, 0.791, 30, 222, 0.791, 30),
(27, 2222, 0.791, 30, 2222, 0.791, 30, 2222, 0.791, 30, 2222, 0.791, 30),
(28, 222, 0.791, 30, 2222, 0.791, 30, 22, 0.791, 30, 2222, 0.791, 30),
(29, 2222, 0.791, 30, 222, 0.791, 30, 222, 0.791, 30, 2222, 0.791, 30),
(30, 222, 0.791, 30, 222, 0.791, 30, 22, 0.791, 30, 222, 0.791, 30),
(31, 0, 0.791, 30, 0, 0.791, 30, 0, 0.791, 30, 0, 0.791, 30),
(45, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30),
(46, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30),
(47, 0, 0.791, 30, 0, 0.791, 30, 0, 0.791, 30, 0, 0.791, 30),
(48, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30),
(49, 0, 0.791, 30, 0, 0.791, 30, 0, 0.791, 30, 0, 0.791, 30),
(50, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30),
(51, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30, 2, 0.791, 30);

-- --------------------------------------------------------

--
-- Table structure for table `jarak_t1`
--

CREATE TABLE `jarak_t1` (
  `id_jarak_t1` int(10) UNSIGNED NOT NULL,
  `jarak_komp1` float NOT NULL,
  `temp_komp1` float NOT NULL,
  `jarak_komp2` float NOT NULL,
  `temp_komp2` float NOT NULL,
  `jarak_komp3` float NOT NULL,
  `temp_komp3` float NOT NULL,
  `jarak_komp4` float NOT NULL,
  `temp_komp4` float NOT NULL,
  `id_ukur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jarak_t1`
--

INSERT INTO `jarak_t1` (`id_jarak_t1`, `jarak_komp1`, `temp_komp1`, `jarak_komp2`, `temp_komp2`, `jarak_komp3`, `temp_komp3`, `jarak_komp4`, `temp_komp4`, `id_ukur`) VALUES
(1, 186.5, 3, 194.3, 2, 207.4, 2, 0, 2, NULL),
(2, 186.5, 331, 194.3, 333, 207.4, 333, 0, 333, NULL),
(3, 186.5, 222, 194.3, 222, 207.4, 222, 0, 222, NULL),
(4, 186.5, 22222, 194.3, 22222, 207.4, 2222, 0, 2222, NULL),
(5, 186.5, 222, 194.3, 222, 207.4, 222, 0, 222, NULL),
(6, 186.5, 222, 194.3, 2222, 207.4, 222, 0, 222, NULL),
(7, 186.5, 222, 194.3, 2222, 207.4, 222, 0, 22, NULL),
(8, 186.5, 2, 194.3, 2, 207.4, 2, 0, 2, NULL),
(22, 186.5, 2, 194.3, 2, 207.4, 2, 0, 2, NULL),
(23, 186.5, 2, 194.3, 2, 207.4, 2, 0, 2, NULL),
(24, 186.5, 2, 194.3, 2, 207.4, 2, 0, 2, NULL),
(25, 186.5, 2, 194.3, 2, 207.4, 2, 0, 2, NULL),
(26, 186.5, 2, 194.3, 2, 207.4, 2, 0, 2, NULL),
(27, 186.5, 3, 194.3, 2, 207.4, 2, 0, 2, NULL),
(28, 186.5, 3, 194.3, 2, 207.4, 3, 0, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `penomoran`
--

CREATE TABLE `penomoran` (
  `id_penomoran` int(11) NOT NULL,
  `agenda` varchar(50) NOT NULL,
  `nomor_urut` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `penomoran`
--

INSERT INTO `penomoran` (`id_penomoran`, `agenda`, `nomor_urut`) VALUES
(1, 'AFRN', 5);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id_role`, `nama_role`) VALUES
(1, 'Driver'),
(2, 'pegawai');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `role`) VALUES
(1, 'Driver'),
(2, 'Pegawai');

-- --------------------------------------------------------

--
-- Table structure for table `salib_ukur`
--

CREATE TABLE `salib_ukur` (
  `id_ukur` int(10) NOT NULL,
  `id_afrn` int(11) NOT NULL,
  `id_trans` int(10) NOT NULL,
  `id_jarak_t1` int(10) UNSIGNED DEFAULT NULL,
  `ket_jarak_t1` varchar(250) NOT NULL,
  `id_jarak_cair_t1` int(10) NOT NULL,
  `ket_jarak_cair_t1` varchar(250) NOT NULL,
  `diperiksa_t1` varchar(250) NOT NULL,
  `diperiksa_segel` varchar(250) NOT NULL,
  `id_bon` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salib_ukur`
--

INSERT INTO `salib_ukur` (`id_ukur`, `id_afrn`, `id_trans`, `id_jarak_t1`, `ket_jarak_t1`, `id_jarak_cair_t1`, `ket_jarak_cair_t1`, `diperiksa_t1`, `diperiksa_segel`, `id_bon`) VALUES
(10, 22, 0, 1, 'siap', 24, 'siap', 'rendy tes', 'Hermanto Purba', 3),
(11, 23, 0, 2, 'tes', 25, 'tes', 'rendy', 'tes', 5),
(12, 24, 0, 3, 'NYOBA TES TES', 26, 'NYOBA TES TES', 'Hermanto Purba', 'Hermanto Purba', 4),
(13, 24, 0, 4, 'dddd', 27, 'ddd', 'dddd', 'Hermanto Purba', 7),
(14, 24, 0, 5, 'hallo', 28, 'hallo', 'Hermanto Purba', 'Hermanto Purba', NULL),
(15, 24, 0, 6, 'tesss', 29, 'ayyya', 'eeeee', 'Hermanto Purba', NULL),
(16, 24, 0, 7, 'ddd', 30, 'dddd', 'Hermanto Purba', 'Hermanto Purba', 6),
(17, 24, 0, 8, 'terbaru', 31, 'terbaru', 'terbaru', 'Hermanto Purba', NULL),
(18, 26, 0, 22, 'dddd', 45, 'ddd', 'Hermanto Purba', 'Hermanto Purba', NULL),
(19, 27, 0, 23, 'tes tes akhir', 46, 'testes akhir', 'Hermanto Purba', 'Hermanto Purba', NULL),
(20, 31, 0, 24, 'tetststs', 47, 'tetssst', 'Hermanto Purba', 'Hermanto Purba', NULL),
(21, 32, 0, 25, 'jarkakeluar', 48, 'jarakkeluar', 'Hermanto Purba', 'Hermanto Purba', NULL),
(22, 32, 0, 26, 'wdwdwdwd', 49, 'wdwdwddwd', 'hermanto purba', 'Hermanto Purba', NULL),
(23, 33, 0, 27, 'tes jam keluar', 50, 'tes jam keluar', '', 'Hermanto Purba', NULL),
(24, 34, 0, 28, 'mntap', 51, 'mantap', 'hermanto', 'Hermanto Purba', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `segel`
--

CREATE TABLE `segel` (
  `id_segel` int(10) NOT NULL,
  `id_ukur` int(5) NOT NULL,
  `mainhole1` varchar(20) NOT NULL,
  `mainhole2` varchar(25) NOT NULL,
  `mainhole3` varchar(25) NOT NULL,
  `mainhole4` varchar(25) NOT NULL,
  `bottom_load_cov1` varchar(25) NOT NULL,
  `bottom_load_cov2` varchar(25) NOT NULL,
  `bottom_load_cov3` varchar(25) NOT NULL,
  `bottom_load_cov4` varchar(25) NOT NULL,
  `bottom_load_cov5` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `segel`
--

INSERT INTO `segel` (`id_segel`, `id_ukur`, `mainhole1`, `mainhole2`, `mainhole3`, `mainhole4`, `bottom_load_cov1`, `bottom_load_cov2`, `bottom_load_cov3`, `bottom_load_cov4`, `bottom_load_cov5`) VALUES
(6, 10, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(7, 11, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(8, 12, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(9, 13, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(10, 14, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(11, 15, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(12, 16, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(13, 17, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(14, 18, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(15, 19, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(16, 20, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(17, 21, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(18, 22, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(19, 23, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023'),
(20, 24, 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023', 'SKH-000023');

-- --------------------------------------------------------

--
-- Table structure for table `tangki`
--

CREATE TABLE `tangki` (
  `id_tangki` int(10) NOT NULL,
  `no_tangki` int(10) NOT NULL,
  `no_bacth` varchar(20) NOT NULL,
  `source` varchar(20) NOT NULL,
  `doc_url` varchar(50) NOT NULL,
  `test_report_no` int(10) NOT NULL,
  `test_report_let` varchar(10) NOT NULL,
  `test_report_date` date NOT NULL,
  `density` float NOT NULL,
  `temperature` float NOT NULL,
  `cu` float NOT NULL,
  `water_contamination_ter` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tangki`
--

INSERT INTO `tangki` (`id_tangki`, `no_tangki`, `no_bacth`, `source`, `doc_url`, `test_report_no`, `test_report_let`, `test_report_date`, `density`, `temperature`, `cu`, `water_contamination_ter`) VALUES
(1, 1, 'SKH/T1', '1', 'uploads/1748236348_downloaded_file.pdf', 1, 'PL2301/TR2', '2024-09-14', 0.0003, 0.03, 0.0003, 0.03),
(2, 2, 'SKH/T2', '2', '', 2, 'PL2301/TR2', '2024-09-14', 0.0002, 0.02, 0.0002, 0.02),
(3, 106, 'SKH/T1', 'SKH/T.107/091', '', 1266, 'PL0000', '2025-05-26', 0.79, 0, 80, 3333),
(4, 106, 'TES DULU', 'SKH/T.107/091', '', 1266, 'PL0000', '2025-05-26', 0.79, 0.03, 0.0003, 3333),
(5, 102, 'SKH/T-106-102', '4000000', '', 221, 'PL0000', '2025-06-07', 22, 23, 11, 122),
(6, 303, 'SKH/T-106-303', '4000000', '', 1266, 'PL0000', '2025-06-07', 0.303, 3, 5, 33),
(7, 303, 'SKH/T-106-303', '4000000', '', 1266, 'PL0000', '2025-06-07', 0.303, 3, 5, 33),
(8, 303, 'SKH/T-106-180', '3000', '', 221, 'PL0000', '2025-06-07', 0.0002, 30, 5, 122),
(9, 404, 'SKH/T-106-180', '3000', '', 2, 'PL0000', '2025-06-08', 2, 2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `transportir`
--

CREATE TABLE `transportir` (
  `id_trans` int(10) NOT NULL,
  `nama_trans` varchar(50) NOT NULL,
  `alamat_trans` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transportir`
--

INSERT INTO `transportir` (`id_trans`, `nama_trans`, `alamat_trans`) VALUES
(2, 'PT Transportir A', 'Jl. Gn. Sahari No.1, Ancol, Kec. Pademangan, Jakar'),
(3, 'PT TES COBA', 'COBA'),
(4, 'PT INALUM PERSERO', 'KUALA TANJUNG'),
(5, 'PT COBA', 'KUALA TANJUNG'),
(6, 'PT MULTIMAS', 'kuala Tanjung');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` bigint(20) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL,
  `gambar_ttd` varchar(255) DEFAULT NULL,
  `gambar_stempel` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_lengkap`, `alamat`, `tempat_lahir`, `tanggal_lahir`, `username`, `password`, `id_role`, `gambar_ttd`, `gambar_stempel`) VALUES
(1, 'Pengemudi', 'Jl. Raya No. 123, Jakarta', 'Jakarta', '1990-01-01', 'driver', '5f4dcc3b5aa765d61d8327deb882cf99', 1, NULL, NULL),
(3, 'Driver 1', 'Medan', 'Medan', '2024-08-16', 'driver1', '5f4dcc3b5aa765d61d8327deb882cf99', 1, NULL, NULL),
(4, 'John Doe', 'Medan', 'Medan', '1990-07-09', 'john', '5f4dcc3b5aa765d61d8327deb882cf99', 1, NULL, NULL),
(5, 'a', 'a', 'a', '2024-09-10', 'a', '5f4dcc3b5aa765d61d8327deb882cf99', 1, NULL, NULL),
(6, 'a', 'a', 'a', '2024-09-10', 'b', '5f4dcc3b5aa765d61d8327deb882cf99', 1, NULL, NULL),
(7, 'v', 'v', 'v', '2024-09-15', 'v', '5f4dcc3b5aa765d61d8327deb882cf99', 1, NULL, NULL),
(8, 'Rendy Krisna', 'kuala tanjung', 'kuala tanjung', '2025-05-23', 'rendy2005', '25d55ad283aa400af464c76d713c07ad', 1, NULL, NULL),
(10, 'Muhammad Rendy Krisna', 'jasadn', 'kuala tanjung', '2025-06-06', 'hrpurba', '$2y$10$b6MhLcY63FTyefOtYT5X9.MCh9yH4KYXvJk2kSjhDFlXUGtHgAYGW', 2, '', ''),
(11, 'Muhammad Rendy Krisna', 'pembangunan', 'kuala tanjung', '2025-06-06', 'Rendytes', '$2y$10$sXiBtLpUxUMncaf73c2lXOcZsWQFJ7.9PAWJo.mFqwpTQpE8eMj8y', 2, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `alamat`, `tempat_lahir`, `tanggal_lahir`, `username`, `password`, `id_role`) VALUES
(1, 'Surya', 'Jl. Merdeka No. 10', 'Medan', '1990-01-01', 'surya', 'hashed_password', 1),
(2, 'Wahyu', 'Jl. Sudirman No. 15', 'Jakarta', '1989-02-02', NULL, 'hashed_password', 1),
(3, 'Randy', 'Jl. Gatot Subroto No. 5', 'Bandung', '2003-03-10', NULL, 'hashed_password', 1),
(4, 'Moch Aby Gazal', 'Jl. Imam Bonjol No. 20', 'Surabaya', '1990-01-01', 'magazal', 'hashed_password', 2),
(5, 'Hermanto Purba', 'Jl. Pahlawan No. 1', 'Medan', '1990-01-01', 'hpurba', 'hashed_password', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `afrn`
--
ALTER TABLE `afrn`
  ADD PRIMARY KEY (`id_afrn`),
  ADD KEY `fk_afrn_bridger` (`id_bridger`),
  ADD KEY `fk_afrn_id_bon` (`id_bon`);

--
-- Indexes for table `bon`
--
ALTER TABLE `bon`
  ADD PRIMARY KEY (`id_bon`);

--
-- Indexes for table `bridger`
--
ALTER TABLE `bridger`
  ADD PRIMARY KEY (`id_bridger`),
  ADD KEY `fk_bridger_transportir` (`id_trans`);

--
-- Indexes for table `destinasi`
--
ALTER TABLE `destinasi`
  ADD PRIMARY KEY (`id_destinasi`);

--
-- Indexes for table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`id_driver`);

--
-- Indexes for table `jarak_cair_t1`
--
ALTER TABLE `jarak_cair_t1`
  ADD PRIMARY KEY (`id_jarak_cair_t1`);

--
-- Indexes for table `jarak_t1`
--
ALTER TABLE `jarak_t1`
  ADD PRIMARY KEY (`id_jarak_t1`);

--
-- Indexes for table `penomoran`
--
ALTER TABLE `penomoran`
  ADD PRIMARY KEY (`id_penomoran`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `salib_ukur`
--
ALTER TABLE `salib_ukur`
  ADD PRIMARY KEY (`id_ukur`),
  ADD KEY `id_afrn` (`id_afrn`),
  ADD KEY `id_jarak_cair_t1` (`id_jarak_cair_t1`),
  ADD KEY `fk_jarak_t1` (`id_jarak_t1`),
  ADD KEY `fk_id_bon` (`id_bon`);

--
-- Indexes for table `segel`
--
ALTER TABLE `segel`
  ADD PRIMARY KEY (`id_segel`),
  ADD KEY `fk_segel_salibukur` (`id_ukur`);

--
-- Indexes for table `tangki`
--
ALTER TABLE `tangki`
  ADD PRIMARY KEY (`id_tangki`);

--
-- Indexes for table `transportir`
--
ALTER TABLE `transportir`
  ADD PRIMARY KEY (`id_trans`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_role` (`id_role`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `afrn`
--
ALTER TABLE `afrn`
  MODIFY `id_afrn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `bon`
--
ALTER TABLE `bon`
  MODIFY `id_bon` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `bridger`
--
ALTER TABLE `bridger`
  MODIFY `id_bridger` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `destinasi`
--
ALTER TABLE `destinasi`
  MODIFY `id_destinasi` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `driver`
--
ALTER TABLE `driver`
  MODIFY `id_driver` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `jarak_cair_t1`
--
ALTER TABLE `jarak_cair_t1`
  MODIFY `id_jarak_cair_t1` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `jarak_t1`
--
ALTER TABLE `jarak_t1`
  MODIFY `id_jarak_t1` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `penomoran`
--
ALTER TABLE `penomoran`
  MODIFY `id_penomoran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `salib_ukur`
--
ALTER TABLE `salib_ukur`
  MODIFY `id_ukur` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `segel`
--
ALTER TABLE `segel`
  MODIFY `id_segel` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tangki`
--
ALTER TABLE `tangki`
  MODIFY `id_tangki` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `transportir`
--
ALTER TABLE `transportir`
  MODIFY `id_trans` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `afrn`
--
ALTER TABLE `afrn`
  ADD CONSTRAINT `fk_afrn_bridger` FOREIGN KEY (`id_bridger`) REFERENCES `bridger` (`id_bridger`),
  ADD CONSTRAINT `fk_afrn_id_bon` FOREIGN KEY (`id_bon`) REFERENCES `bon` (`id_bon`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `bridger`
--
ALTER TABLE `bridger`
  ADD CONSTRAINT `fk_bridger_transportir` FOREIGN KEY (`id_trans`) REFERENCES `transportir` (`id_trans`);

--
-- Constraints for table `salib_ukur`
--
ALTER TABLE `salib_ukur`
  ADD CONSTRAINT `fk_id_bon` FOREIGN KEY (`id_bon`) REFERENCES `bon` (`id_bon`),
  ADD CONSTRAINT `fk_jarak_t1` FOREIGN KEY (`id_jarak_t1`) REFERENCES `jarak_t1` (`id_jarak_t1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `salib_ukur_ibfk_1` FOREIGN KEY (`id_afrn`) REFERENCES `afrn` (`id_afrn`),
  ADD CONSTRAINT `salib_ukur_ibfk_2` FOREIGN KEY (`id_jarak_cair_t1`) REFERENCES `jarak_cair_t1` (`id_jarak_cair_t1`);

--
-- Constraints for table `segel`
--
ALTER TABLE `segel`
  ADD CONSTRAINT `fk_segel_salibukur` FOREIGN KEY (`id_ukur`) REFERENCES `salib_ukur` (`id_ukur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
