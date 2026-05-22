-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 22 Bulan Mei 2026 pada 20.37
-- Versi server: 10.6.25-MariaDB-log
-- Versi PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_hc`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` varchar(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Akun administrator sistem';

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `created_at`) VALUES
('IDA001', 'admin1', 'admin1234-', '2026-05-22 07:53:00'),
('IDA002', 'admin2', 'admin1234-', '2026-05-22 07:53:00'),
('IDA003', 'admin3', 'admin1234-', '2026-05-22 07:53:00'),
('IDA004', 'admin4', 'admin1234-', '2026-05-22 07:53:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_skrining`
--

CREATE TABLE `detail_skrining` (
  `id_detail` varchar(10) NOT NULL,
  `id_skrining` varchar(10) NOT NULL,
  `id_variabel` varchar(10) NOT NULL,
  `nilai` tinyint(3) UNSIGNED NOT NULL
) ;

--
-- Dumping data untuk tabel `detail_skrining`
--

INSERT INTO `detail_skrining` (`id_detail`, `id_skrining`, `id_variabel`, `nilai`) VALUES
('IDD001', 'IDS001', 'IDV001', 3),
('IDD002', 'IDS001', 'IDV002', 3),
('IDD003', 'IDS001', 'IDV003', 3),
('IDD004', 'IDS001', 'IDV004', 3),
('IDD005', 'IDS001', 'IDV005', 3),
('IDD006', 'IDS001', 'IDV006', 3),
('IDD007', 'IDS001', 'IDV007', 3),
('IDD008', 'IDS001', 'IDV008', 3),
('IDD009', 'IDS001', 'IDV009', 3),
('IDD010', 'IDS001', 'IDV010', 3),
('IDD011', 'IDS001', 'IDV011', 3),
('IDD012', 'IDS001', 'IDV012', 3),
('IDD013', 'IDS001', 'IDV013', 3),
('IDD014', 'IDS001', 'IDV014', 3),
('IDD015', 'IDS001', 'IDV015', 3),
('IDD016', 'IDS001', 'IDV016', 3),
('IDD017', 'IDS001', 'IDV017', 3),
('IDD018', 'IDS001', 'IDV018', 3),
('IDD019', 'IDS001', 'IDV019', 3),
('IDD020', 'IDS001', 'IDV020', 3),
('IDD021', 'IDS001', 'IDV021', 3),
('IDD022', 'IDS001', 'IDV022', 3),
('IDD023', 'IDS001', 'IDV023', 3),
('IDD024', 'IDS001', 'IDV024', 3),
('IDD025', 'IDS001', 'IDV025', 3),
('IDD026', 'IDS001', 'IDV026', 3),
('IDD027', 'IDS001', 'IDV027', 3),
('IDD028', 'IDS001', 'IDV028', 3),
('IDD029', 'IDS001', 'IDV029', 3),
('IDD030', 'IDS001', 'IDV030', 3),
('IDD031', 'IDS001', 'IDV031', 3),
('IDD032', 'IDS001', 'IDV032', 3),
('IDD033', 'IDS001', 'IDV033', 3),
('IDD034', 'IDS001', 'IDV034', 3),
('IDD035', 'IDS001', 'IDV035', 3),
('IDD036', 'IDS001', 'IDV036', 3),
('IDD037', 'IDS001', 'IDV037', 3),
('IDD038', 'IDS001', 'IDV038', 3),
('IDD039', 'IDS001', 'IDV039', 3),
('IDD040', 'IDS001', 'IDV040', 3),
('IDD041', 'IDS001', 'IDV041', 3),
('IDD042', 'IDS001', 'IDV042', 3),
('IDD043', 'IDS002', 'IDV001', 3),
('IDD044', 'IDS002', 'IDV002', 3),
('IDD045', 'IDS002', 'IDV003', 3),
('IDD046', 'IDS002', 'IDV004', 3),
('IDD047', 'IDS002', 'IDV005', 3),
('IDD048', 'IDS002', 'IDV006', 3),
('IDD049', 'IDS002', 'IDV007', 3),
('IDD050', 'IDS002', 'IDV008', 3),
('IDD051', 'IDS002', 'IDV009', 3),
('IDD052', 'IDS002', 'IDV010', 3),
('IDD053', 'IDS002', 'IDV011', 3),
('IDD054', 'IDS002', 'IDV012', 3),
('IDD055', 'IDS002', 'IDV013', 3),
('IDD056', 'IDS002', 'IDV014', 3),
('IDD057', 'IDS002', 'IDV015', 3),
('IDD058', 'IDS002', 'IDV016', 3),
('IDD059', 'IDS002', 'IDV017', 3),
('IDD060', 'IDS002', 'IDV018', 3),
('IDD061', 'IDS002', 'IDV019', 3),
('IDD062', 'IDS002', 'IDV020', 3),
('IDD063', 'IDS002', 'IDV021', 3),
('IDD064', 'IDS002', 'IDV022', 3),
('IDD065', 'IDS002', 'IDV023', 3),
('IDD066', 'IDS002', 'IDV024', 3),
('IDD067', 'IDS002', 'IDV025', 3),
('IDD068', 'IDS002', 'IDV026', 3),
('IDD069', 'IDS002', 'IDV027', 3),
('IDD070', 'IDS002', 'IDV028', 3),
('IDD071', 'IDS002', 'IDV029', 3),
('IDD072', 'IDS002', 'IDV030', 3),
('IDD073', 'IDS002', 'IDV031', 3),
('IDD074', 'IDS002', 'IDV032', 3),
('IDD075', 'IDS002', 'IDV033', 3),
('IDD076', 'IDS002', 'IDV034', 3),
('IDD077', 'IDS002', 'IDV035', 3),
('IDD078', 'IDS002', 'IDV036', 3),
('IDD079', 'IDS002', 'IDV037', 3),
('IDD080', 'IDS002', 'IDV038', 3),
('IDD081', 'IDS002', 'IDV039', 3),
('IDD082', 'IDS002', 'IDV040', 3),
('IDD083', 'IDS002', 'IDV041', 3),
('IDD084', 'IDS002', 'IDV042', 3),
('IDD085', 'IDS003', 'IDV001', 0),
('IDD086', 'IDS003', 'IDV002', 0),
('IDD087', 'IDS003', 'IDV003', 0),
('IDD088', 'IDS003', 'IDV004', 0),
('IDD089', 'IDS003', 'IDV005', 0),
('IDD090', 'IDS003', 'IDV006', 0),
('IDD091', 'IDS003', 'IDV007', 0),
('IDD092', 'IDS003', 'IDV008', 0),
('IDD093', 'IDS003', 'IDV009', 0),
('IDD094', 'IDS003', 'IDV010', 0),
('IDD095', 'IDS003', 'IDV011', 0),
('IDD096', 'IDS003', 'IDV012', 0),
('IDD097', 'IDS003', 'IDV013', 0),
('IDD098', 'IDS003', 'IDV014', 0),
('IDD099', 'IDS003', 'IDV015', 0),
('IDD100', 'IDS003', 'IDV016', 0),
('IDD101', 'IDS003', 'IDV017', 0),
('IDD102', 'IDS003', 'IDV018', 0),
('IDD103', 'IDS003', 'IDV019', 0),
('IDD104', 'IDS003', 'IDV020', 0),
('IDD105', 'IDS003', 'IDV021', 0),
('IDD106', 'IDS003', 'IDV022', 0),
('IDD107', 'IDS003', 'IDV023', 0),
('IDD108', 'IDS003', 'IDV024', 0),
('IDD109', 'IDS003', 'IDV025', 0),
('IDD110', 'IDS003', 'IDV026', 0),
('IDD111', 'IDS003', 'IDV027', 0),
('IDD112', 'IDS003', 'IDV028', 0),
('IDD113', 'IDS003', 'IDV029', 0),
('IDD114', 'IDS003', 'IDV030', 0),
('IDD115', 'IDS003', 'IDV031', 0),
('IDD116', 'IDS003', 'IDV032', 0),
('IDD117', 'IDS003', 'IDV033', 0),
('IDD118', 'IDS003', 'IDV034', 0),
('IDD119', 'IDS003', 'IDV035', 0),
('IDD120', 'IDS003', 'IDV036', 0),
('IDD121', 'IDS003', 'IDV037', 0),
('IDD122', 'IDS003', 'IDV038', 0),
('IDD123', 'IDS003', 'IDV039', 0),
('IDD124', 'IDS003', 'IDV040', 0),
('IDD125', 'IDS003', 'IDV041', 0),
('IDD126', 'IDS003', 'IDV042', 0),
('IDD127', 'IDS004', 'IDV001', 2),
('IDD128', 'IDS004', 'IDV002', 2),
('IDD129', 'IDS004', 'IDV003', 2),
('IDD130', 'IDS004', 'IDV004', 2),
('IDD131', 'IDS004', 'IDV005', 2),
('IDD132', 'IDS004', 'IDV006', 2),
('IDD133', 'IDS004', 'IDV007', 2),
('IDD134', 'IDS004', 'IDV008', 2),
('IDD135', 'IDS004', 'IDV009', 2),
('IDD136', 'IDS004', 'IDV010', 2),
('IDD137', 'IDS004', 'IDV011', 2),
('IDD138', 'IDS004', 'IDV012', 2),
('IDD139', 'IDS004', 'IDV013', 2),
('IDD140', 'IDS004', 'IDV014', 2),
('IDD141', 'IDS004', 'IDV015', 2),
('IDD142', 'IDS004', 'IDV016', 2),
('IDD143', 'IDS004', 'IDV017', 2),
('IDD144', 'IDS004', 'IDV018', 2),
('IDD145', 'IDS004', 'IDV019', 2),
('IDD146', 'IDS004', 'IDV020', 2),
('IDD147', 'IDS004', 'IDV021', 2),
('IDD148', 'IDS004', 'IDV022', 2),
('IDD149', 'IDS004', 'IDV023', 2),
('IDD150', 'IDS004', 'IDV024', 2),
('IDD151', 'IDS004', 'IDV025', 2),
('IDD152', 'IDS004', 'IDV026', 2),
('IDD153', 'IDS004', 'IDV027', 2),
('IDD154', 'IDS004', 'IDV028', 2),
('IDD155', 'IDS004', 'IDV029', 2),
('IDD156', 'IDS004', 'IDV030', 2),
('IDD157', 'IDS004', 'IDV031', 2),
('IDD158', 'IDS004', 'IDV032', 2),
('IDD159', 'IDS004', 'IDV033', 2),
('IDD160', 'IDS004', 'IDV034', 2),
('IDD161', 'IDS004', 'IDV035', 2),
('IDD162', 'IDS004', 'IDV036', 2),
('IDD163', 'IDS004', 'IDV037', 2),
('IDD164', 'IDS004', 'IDV038', 2),
('IDD165', 'IDS004', 'IDV039', 2),
('IDD166', 'IDS004', 'IDV040', 2),
('IDD167', 'IDS004', 'IDV041', 2),
('IDD168', 'IDS004', 'IDV042', 2),
('IDD169', 'IDS005', 'IDV001', 1),
('IDD170', 'IDS005', 'IDV002', 1),
('IDD171', 'IDS005', 'IDV003', 1),
('IDD172', 'IDS005', 'IDV004', 1),
('IDD173', 'IDS005', 'IDV005', 1),
('IDD174', 'IDS005', 'IDV006', 1),
('IDD175', 'IDS005', 'IDV007', 1),
('IDD176', 'IDS005', 'IDV008', 1),
('IDD177', 'IDS005', 'IDV009', 1),
('IDD178', 'IDS005', 'IDV010', 1),
('IDD179', 'IDS005', 'IDV011', 1),
('IDD180', 'IDS005', 'IDV012', 1),
('IDD181', 'IDS005', 'IDV013', 1),
('IDD182', 'IDS005', 'IDV014', 1),
('IDD183', 'IDS005', 'IDV015', 1),
('IDD184', 'IDS005', 'IDV016', 1),
('IDD185', 'IDS005', 'IDV017', 1),
('IDD186', 'IDS005', 'IDV018', 1),
('IDD187', 'IDS005', 'IDV019', 1),
('IDD188', 'IDS005', 'IDV020', 1),
('IDD189', 'IDS005', 'IDV021', 1),
('IDD190', 'IDS005', 'IDV022', 1),
('IDD191', 'IDS005', 'IDV023', 1),
('IDD192', 'IDS005', 'IDV024', 1),
('IDD193', 'IDS005', 'IDV025', 1),
('IDD194', 'IDS005', 'IDV026', 1),
('IDD195', 'IDS005', 'IDV027', 1),
('IDD196', 'IDS005', 'IDV028', 1),
('IDD197', 'IDS005', 'IDV029', 1),
('IDD198', 'IDS005', 'IDV030', 1),
('IDD199', 'IDS005', 'IDV031', 1),
('IDD200', 'IDS005', 'IDV032', 1),
('IDD201', 'IDS005', 'IDV033', 1),
('IDD202', 'IDS005', 'IDV034', 1),
('IDD203', 'IDS005', 'IDV035', 1),
('IDD204', 'IDS005', 'IDV036', 1),
('IDD205', 'IDS005', 'IDV037', 1),
('IDD206', 'IDS005', 'IDV038', 1),
('IDD207', 'IDS005', 'IDV039', 1),
('IDD208', 'IDS005', 'IDV040', 1),
('IDD209', 'IDS005', 'IDV041', 1),
('IDD210', 'IDS005', 'IDV042', 1),
('IDD211', 'IDS006', 'IDV001', 1),
('IDD212', 'IDS006', 'IDV002', 1),
('IDD213', 'IDS006', 'IDV003', 1),
('IDD214', 'IDS006', 'IDV004', 1),
('IDD215', 'IDS006', 'IDV005', 1),
('IDD216', 'IDS006', 'IDV006', 1),
('IDD217', 'IDS006', 'IDV007', 1),
('IDD218', 'IDS006', 'IDV008', 1),
('IDD219', 'IDS006', 'IDV009', 1),
('IDD220', 'IDS006', 'IDV010', 1),
('IDD221', 'IDS006', 'IDV011', 1),
('IDD222', 'IDS006', 'IDV012', 1),
('IDD223', 'IDS006', 'IDV013', 1),
('IDD224', 'IDS006', 'IDV014', 1),
('IDD225', 'IDS006', 'IDV015', 1),
('IDD226', 'IDS006', 'IDV016', 1),
('IDD227', 'IDS006', 'IDV017', 1),
('IDD228', 'IDS006', 'IDV018', 1),
('IDD229', 'IDS006', 'IDV019', 1),
('IDD230', 'IDS006', 'IDV020', 1),
('IDD231', 'IDS006', 'IDV021', 1),
('IDD232', 'IDS006', 'IDV022', 1),
('IDD233', 'IDS006', 'IDV023', 1),
('IDD234', 'IDS006', 'IDV024', 1),
('IDD235', 'IDS006', 'IDV025', 1),
('IDD236', 'IDS006', 'IDV026', 1),
('IDD237', 'IDS006', 'IDV027', 1),
('IDD238', 'IDS006', 'IDV028', 1),
('IDD239', 'IDS006', 'IDV029', 1),
('IDD240', 'IDS006', 'IDV030', 1),
('IDD241', 'IDS006', 'IDV031', 1),
('IDD242', 'IDS006', 'IDV032', 1),
('IDD243', 'IDS006', 'IDV033', 1),
('IDD244', 'IDS006', 'IDV034', 1),
('IDD245', 'IDS006', 'IDV035', 1),
('IDD246', 'IDS006', 'IDV036', 1),
('IDD247', 'IDS006', 'IDV037', 1),
('IDD248', 'IDS006', 'IDV038', 1),
('IDD249', 'IDS006', 'IDV039', 1),
('IDD250', 'IDS006', 'IDV040', 1),
('IDD251', 'IDS006', 'IDV041', 1),
('IDD252', 'IDS006', 'IDV042', 1),
('IDD253', 'IDS007', 'IDV001', 2),
('IDD254', 'IDS007', 'IDV002', 2),
('IDD255', 'IDS007', 'IDV003', 2),
('IDD256', 'IDS007', 'IDV004', 2),
('IDD257', 'IDS007', 'IDV005', 2),
('IDD258', 'IDS007', 'IDV006', 2),
('IDD259', 'IDS007', 'IDV007', 2),
('IDD260', 'IDS007', 'IDV008', 2),
('IDD261', 'IDS007', 'IDV009', 2),
('IDD262', 'IDS007', 'IDV010', 2),
('IDD263', 'IDS007', 'IDV011', 2),
('IDD264', 'IDS007', 'IDV012', 2),
('IDD265', 'IDS007', 'IDV013', 2),
('IDD266', 'IDS007', 'IDV014', 2),
('IDD267', 'IDS007', 'IDV015', 2),
('IDD268', 'IDS007', 'IDV016', 2),
('IDD269', 'IDS007', 'IDV017', 2),
('IDD270', 'IDS007', 'IDV018', 2),
('IDD271', 'IDS007', 'IDV019', 2),
('IDD272', 'IDS007', 'IDV020', 2),
('IDD273', 'IDS007', 'IDV021', 2),
('IDD274', 'IDS007', 'IDV022', 2),
('IDD275', 'IDS007', 'IDV023', 2),
('IDD276', 'IDS007', 'IDV024', 2),
('IDD277', 'IDS007', 'IDV025', 2),
('IDD278', 'IDS007', 'IDV026', 2),
('IDD279', 'IDS007', 'IDV027', 2),
('IDD280', 'IDS007', 'IDV028', 2),
('IDD281', 'IDS007', 'IDV029', 2),
('IDD282', 'IDS007', 'IDV030', 2),
('IDD283', 'IDS007', 'IDV031', 2),
('IDD284', 'IDS007', 'IDV032', 2),
('IDD285', 'IDS007', 'IDV033', 2),
('IDD286', 'IDS007', 'IDV034', 2),
('IDD287', 'IDS007', 'IDV035', 2),
('IDD288', 'IDS007', 'IDV036', 2),
('IDD289', 'IDS007', 'IDV037', 2),
('IDD290', 'IDS007', 'IDV038', 2),
('IDD291', 'IDS007', 'IDV039', 2),
('IDD292', 'IDS007', 'IDV040', 2),
('IDD293', 'IDS007', 'IDV041', 2),
('IDD294', 'IDS007', 'IDV042', 2),
('IDD295', 'IDS008', 'IDV001', 3),
('IDD296', 'IDS008', 'IDV002', 3),
('IDD297', 'IDS008', 'IDV003', 3),
('IDD298', 'IDS008', 'IDV004', 3),
('IDD299', 'IDS008', 'IDV005', 3),
('IDD300', 'IDS008', 'IDV006', 3),
('IDD301', 'IDS008', 'IDV007', 3),
('IDD302', 'IDS008', 'IDV008', 3),
('IDD303', 'IDS008', 'IDV009', 3),
('IDD304', 'IDS008', 'IDV010', 3),
('IDD305', 'IDS008', 'IDV011', 3),
('IDD306', 'IDS008', 'IDV012', 3),
('IDD307', 'IDS008', 'IDV013', 3),
('IDD308', 'IDS008', 'IDV014', 3),
('IDD309', 'IDS008', 'IDV015', 3),
('IDD310', 'IDS008', 'IDV016', 3),
('IDD311', 'IDS008', 'IDV017', 3),
('IDD312', 'IDS008', 'IDV018', 3),
('IDD313', 'IDS008', 'IDV019', 3),
('IDD314', 'IDS008', 'IDV020', 3),
('IDD315', 'IDS008', 'IDV021', 3),
('IDD316', 'IDS008', 'IDV022', 3),
('IDD317', 'IDS008', 'IDV023', 3),
('IDD318', 'IDS008', 'IDV024', 3),
('IDD319', 'IDS008', 'IDV025', 3),
('IDD320', 'IDS008', 'IDV026', 3),
('IDD321', 'IDS008', 'IDV027', 3),
('IDD322', 'IDS008', 'IDV028', 3),
('IDD323', 'IDS008', 'IDV029', 3),
('IDD324', 'IDS008', 'IDV030', 3),
('IDD325', 'IDS008', 'IDV031', 3),
('IDD326', 'IDS008', 'IDV032', 3),
('IDD327', 'IDS008', 'IDV033', 3),
('IDD328', 'IDS008', 'IDV034', 3),
('IDD329', 'IDS008', 'IDV035', 3),
('IDD330', 'IDS008', 'IDV036', 3),
('IDD331', 'IDS008', 'IDV037', 3),
('IDD332', 'IDS008', 'IDV038', 3),
('IDD333', 'IDS008', 'IDV039', 3),
('IDD334', 'IDS008', 'IDV040', 3),
('IDD335', 'IDS008', 'IDV041', 3),
('IDD336', 'IDS008', 'IDV042', 3),
('IDD337', 'IDS009', 'IDV001', 1),
('IDD338', 'IDS009', 'IDV002', 3),
('IDD339', 'IDS009', 'IDV003', 1),
('IDD340', 'IDS009', 'IDV004', 0),
('IDD341', 'IDS009', 'IDV005', 0),
('IDD342', 'IDS009', 'IDV006', 1),
('IDD343', 'IDS009', 'IDV007', 1),
('IDD344', 'IDS009', 'IDV008', 2),
('IDD345', 'IDS009', 'IDV009', 2),
('IDD346', 'IDS009', 'IDV010', 1),
('IDD347', 'IDS009', 'IDV011', 1),
('IDD348', 'IDS009', 'IDV012', 1),
('IDD349', 'IDS009', 'IDV013', 3),
('IDD350', 'IDS009', 'IDV014', 1),
('IDD351', 'IDS009', 'IDV015', 1),
('IDD352', 'IDS009', 'IDV016', 2),
('IDD353', 'IDS009', 'IDV017', 0),
('IDD354', 'IDS009', 'IDV018', 1),
('IDD355', 'IDS009', 'IDV019', 1),
('IDD356', 'IDS009', 'IDV020', 1),
('IDD357', 'IDS009', 'IDV021', 2),
('IDD358', 'IDS009', 'IDV022', 1),
('IDD359', 'IDS009', 'IDV023', 0),
('IDD360', 'IDS009', 'IDV024', 1),
('IDD361', 'IDS009', 'IDV025', 0),
('IDD362', 'IDS009', 'IDV026', 1),
('IDD363', 'IDS009', 'IDV027', 1),
('IDD364', 'IDS009', 'IDV028', 1),
('IDD365', 'IDS009', 'IDV029', 2),
('IDD366', 'IDS009', 'IDV030', 1),
('IDD367', 'IDS009', 'IDV031', 3),
('IDD368', 'IDS009', 'IDV032', 2),
('IDD369', 'IDS009', 'IDV033', 1),
('IDD370', 'IDS009', 'IDV034', 1),
('IDD371', 'IDS009', 'IDV035', 1),
('IDD372', 'IDS009', 'IDV036', 1),
('IDD373', 'IDS009', 'IDV037', 0),
('IDD374', 'IDS009', 'IDV038', 2),
('IDD375', 'IDS009', 'IDV039', 1),
('IDD376', 'IDS009', 'IDV040', 0),
('IDD377', 'IDS009', 'IDV041', 1),
('IDD378', 'IDS009', 'IDV042', 0),
('IDD379', 'IDS010', 'IDV001', 1),
('IDD380', 'IDS010', 'IDV002', 1),
('IDD381', 'IDS010', 'IDV003', 2),
('IDD382', 'IDS010', 'IDV004', 1),
('IDD383', 'IDS010', 'IDV005', 2),
('IDD384', 'IDS010', 'IDV006', 1),
('IDD385', 'IDS010', 'IDV007', 0),
('IDD386', 'IDS010', 'IDV008', 0),
('IDD387', 'IDS010', 'IDV009', 2),
('IDD388', 'IDS010', 'IDV010', 3),
('IDD389', 'IDS010', 'IDV011', 0),
('IDD390', 'IDS010', 'IDV012', 3),
('IDD391', 'IDS010', 'IDV013', 0),
('IDD392', 'IDS010', 'IDV014', 1),
('IDD393', 'IDS010', 'IDV015', 1),
('IDD394', 'IDS010', 'IDV016', 0),
('IDD395', 'IDS010', 'IDV017', 1),
('IDD396', 'IDS010', 'IDV018', 0),
('IDD397', 'IDS010', 'IDV019', 1),
('IDD398', 'IDS010', 'IDV020', 2),
('IDD399', 'IDS010', 'IDV021', 3),
('IDD400', 'IDS010', 'IDV022', 1),
('IDD401', 'IDS010', 'IDV023', 0),
('IDD402', 'IDS010', 'IDV024', 3),
('IDD403', 'IDS010', 'IDV025', 2),
('IDD404', 'IDS010', 'IDV026', 1),
('IDD405', 'IDS010', 'IDV027', 2),
('IDD406', 'IDS010', 'IDV028', 3),
('IDD407', 'IDS010', 'IDV029', 1),
('IDD408', 'IDS010', 'IDV030', 0),
('IDD409', 'IDS010', 'IDV031', 3),
('IDD410', 'IDS010', 'IDV032', 2),
('IDD411', 'IDS010', 'IDV033', 1),
('IDD412', 'IDS010', 'IDV034', 2),
('IDD413', 'IDS010', 'IDV035', 1),
('IDD414', 'IDS010', 'IDV036', 1),
('IDD415', 'IDS010', 'IDV037', 0),
('IDD416', 'IDS010', 'IDV038', 1),
('IDD417', 'IDS010', 'IDV039', 0),
('IDD418', 'IDS010', 'IDV040', 3),
('IDD419', 'IDS010', 'IDV041', 0),
('IDD420', 'IDS010', 'IDV042', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_subskala`
--

CREATE TABLE `kategori_subskala` (
  `id_kategori` varchar(10) NOT NULL,
  `id_subskala` varchar(10) NOT NULL,
  `nama_kategori` varchar(30) NOT NULL,
  `rentang_min` tinyint(3) UNSIGNED NOT NULL,
  `rentang_max` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Kategorisasi skor per subskala — cutoff resmi DASS-42';

--
-- Dumping data untuk tabel `kategori_subskala`
--

INSERT INTO `kategori_subskala` (`id_kategori`, `id_subskala`, `nama_kategori`, `rentang_min`, `rentang_max`) VALUES
('IDK001', 'IDU001', 'Normal', 0, 9),
('IDK002', 'IDU001', 'Ringan', 10, 13),
('IDK003', 'IDU001', 'Sedang', 14, 20),
('IDK004', 'IDU001', 'Berat', 21, 27),
('IDK005', 'IDU001', 'Sangat Berat', 28, 100),
('IDK006', 'IDU002', 'Normal', 0, 7),
('IDK007', 'IDU002', 'Ringan', 8, 9),
('IDK008', 'IDU002', 'Sedang', 10, 14),
('IDK009', 'IDU002', 'Berat', 15, 19),
('IDK010', 'IDU002', 'Sangat Berat', 20, 100),
('IDK011', 'IDU003', 'Normal', 0, 14),
('IDK012', 'IDU003', 'Ringan', 15, 18),
('IDK013', 'IDU003', 'Sedang', 19, 25),
('IDK014', 'IDU003', 'Berat', 26, 33),
('IDK015', 'IDU003', 'Sangat Berat', 34, 100);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id_mahasiswa` varchar(10) NOT NULL,
  `npm` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Data mahasiswa pengguna aplikasi';

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`id_mahasiswa`, `npm`, `nama`, `password`, `created_at`, `security_question`, `security_answer_hash`) VALUES
('IDM001', '24081010149', 'Achmad Azkal Fatich', '$2y$10$DarITuRB5Jg9VvhvAZhPdeXVkxJaZX.Md/WwF08qg0qoTrileEXIm', '2026-05-22 07:53:01', 'Nama sekolah dasar?', '$2y$10$ChVDHXv86z8yus0Qb2YaouOzh/8WxEksAEpXYaFaP4W48e1gz3J4e'),
('IDM002', '24081010225', 'Clarista Nailah Sari Paramita', 'Mhs1234-', '2026-05-22 07:53:01', NULL, NULL),
('IDM003', '24081010238', 'Anisa Nur Azizah', 'Mhs1234-', '2026-05-22 07:53:01', NULL, NULL),
('IDM004', '24081010252', 'Gilbert Christian Rumtotmey', '$2y$10$2hTdq1NBS5IJffgvaY4DiuQp7I9Q63PBPlm65mO1EsWBJEGTSnG0i', '2026-05-22 07:53:01', 'Nama hewan peliharaan pertama?', '$2y$10$y0/IuC0XOA.CYgDF6zMTGu1plSgOJtRJXLRxBn22y4QFhy9HYEToC'),
('IDM005', '24081010339', 'Imroatus Saadah', 'Mhs1234-', '2026-05-22 07:53:01', NULL, NULL),
('IDM006', '24081010142', 'Weka Wijaya', 'Mhs1234-', '2026-05-22 07:53:01', NULL, NULL),
('IDM007', '24081010333', 'Diana padash', '$2y$10$yho9Zt3DRbzj.i21RsmbG.k1B5QKbLwSmm6pFr689oq4u2rDObTVm', '2026-05-22 08:24:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekomendasi`
--

CREATE TABLE `rekomendasi` (
  `id_rekomendasi` varchar(10) NOT NULL,
  `id_kategori` varchar(10) NOT NULL,
  `teks_rekomendasi` text NOT NULL,
  `urutan` tinyint(4) NOT NULL DEFAULT 1,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Rekomendasi per kategori subskala DASS-42';

--
-- Dumping data untuk tabel `rekomendasi`
--

INSERT INTO `rekomendasi` (`id_rekomendasi`, `id_kategori`, `teks_rekomendasi`, `urutan`, `status_aktif`) VALUES
('IDR001', 'IDK001', 'Pertahankan pola hidup sehat dengan tidur cukup, makan teratur, dan olahraga ringan.', 1, 1),
('IDR002', 'IDK001', 'Luangkan waktu untuk aktivitas yang Anda nikmati.', 2, 1),
('IDR003', 'IDK002', 'Coba identifikasi pemicu perasaan negatif dan diskusikan dengan orang terdekat.', 1, 1),
('IDR004', 'IDK002', 'Tingkatkan aktivitas fisik ringan seperti jalan kaki 30 menit per hari.', 2, 1),
('IDR005', 'IDK003', 'Pertimbangkan untuk berkonsultasi dengan konselor atau psikolog.', 1, 1),
('IDR006', 'IDK003', 'Batasi konsumsi media sosial dan pastikan kualitas tidur yang baik.', 2, 1),
('IDR007', 'IDK004', 'Segera konsultasikan kondisi ini dengan psikolog atau psikiater.', 1, 1),
('IDR008', 'IDK004', 'Jangan menghadapi kondisi ini sendirian, hubungi orang yang dipercaya.', 2, 1),
('IDR009', 'IDK005', 'Diperlukan penanganan profesional segera. Hubungi layanan kesehatan mental.', 1, 1),
('IDR010', 'IDK005', 'Jika ada pikiran menyakiti diri sendiri, segera hubungi hotline kesehatan jiwa.', 2, 1),
('IDR011', 'IDK006', 'Pertahankan gaya hidup aktif dan teknik relaksasi seperti meditasi ringan.', 1, 1),
('IDR012', 'IDK007', 'Latih teknik pernapasan dalam (4-7-8 breathing) saat merasa cemas.', 1, 1),
('IDR013', 'IDK007', 'Kurangi konsumsi kafein dan pastikan tidur yang cukup.', 2, 1),
('IDR014', 'IDK008', 'Pertimbangkan sesi konseling untuk mengelola kecemasan secara terstruktur.', 1, 1),
('IDR015', 'IDK008', 'Coba progressive muscle relaxation atau mindfulness harian.', 2, 1),
('IDR016', 'IDK009', 'Konsultasikan dengan profesional kesehatan mental untuk evaluasi lebih lanjut.', 1, 1),
('IDR017', 'IDK009', 'Hindari situasi yang memicu kecemasan berlebih sementara mencari bantuan.', 2, 1),
('IDR018', 'IDK010', 'Segera cari bantuan profesional. Kecemasan ini dapat mengganggu fungsi sehari-hari.', 1, 1),
('IDR019', 'IDK010', 'Pertimbangkan terapi kognitif-perilaku (CBT) dengan psikolog terlisensi.', 2, 1),
('IDR020', 'IDK011', 'Pertahankan manajemen waktu yang baik dan prioritaskan istirahat berkualitas.', 1, 1),
('IDR021', 'IDK012', 'Atur kembali prioritas tugas dan coba teknik manajemen stres seperti journaling.', 1, 1),
('IDR022', 'IDK012', 'Pastikan ada waktu untuk hobi dan sosialisasi dengan teman.', 2, 1),
('IDR023', 'IDK013', 'Evaluasi sumber stres utama dan buat rencana konkret untuk mengatasinya.', 1, 1),
('IDR024', 'IDK013', 'Lakukan olahraga rutin dan teknik relaksasi minimal 20 menit per hari.', 2, 1),
('IDR025', 'IDK014', 'Konsultasikan dengan konselor kampus atau tenaga profesional.', 1, 1),
('IDR026', 'IDK014', 'Pertimbangkan untuk mengurangi beban tugas sementara dan minta dukungan sosial.', 2, 1),
('IDR027', 'IDK015', 'Segera cari bantuan profesional. Stres berat berisiko mempengaruhi kesehatan fisik.', 1, 1),
('IDR028', 'IDK015', 'Jangan tunda: hubungi psikolog, psikiater, atau layanan konseling profesional.', 2, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `skrining`
--

CREATE TABLE `skrining` (
  `id_skrining` varchar(10) NOT NULL,
  `id_mahasiswa` varchar(10) NOT NULL,
  `tgl_skrining` datetime NOT NULL DEFAULT current_timestamp(),
  `skor_depresi` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `skor_anxiety` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `skor_stress` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Header sesi skrining. skor_total & kategori dihitung via VIEW';

--
-- Dumping data untuk tabel `skrining`
--

INSERT INTO `skrining` (`id_skrining`, `id_mahasiswa`, `tgl_skrining`, `skor_depresi`, `skor_anxiety`, `skor_stress`, `catatan`) VALUES
('IDS001', 'IDM004', '2026-05-22 14:57:04', 42, 45, 39, 'Hasil skrining DASS-42'),
('IDS002', 'IDM004', '2026-05-22 14:59:39', 42, 45, 39, 'Hasil skrining DASS-42'),
('IDS003', 'IDM001', '2026-05-22 16:12:49', 0, 0, 0, 'Hasil skrining DASS-42'),
('IDS004', 'IDM002', '2026-05-22 16:15:34', 28, 30, 26, 'Hasil skrining DASS-42'),
('IDS005', 'IDM002', '2026-05-22 16:38:41', 14, 15, 13, 'Hasil skrining DASS-42'),
('IDS006', 'IDM001', '2026-05-22 16:40:39', 14, 15, 13, 'Hasil skrining DASS-42'),
('IDS007', 'IDM001', '2026-05-22 16:42:23', 28, 30, 26, 'Hasil skrining DASS-42'),
('IDS008', 'IDM001', '2026-05-22 16:43:10', 42, 45, 39, 'Hasil skrining DASS-42'),
('IDS009', 'IDM001', '2026-05-23 00:51:39', 17, 14, 16, 'Hasil skrining DASS-42'),
('IDS010', 'IDM004', '2026-05-23 03:34:16', 23, 20, 11, 'Hasil skrining DASS-42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `subskala`
--

CREATE TABLE `subskala` (
  `id_subskala` varchar(10) NOT NULL,
  `kode` enum('depression','anxiety','stress') NOT NULL,
  `nama_id` varchar(50) NOT NULL,
  `jumlah_item` tinyint(4) NOT NULL DEFAULT 14
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master 3 subskala DASS-42';

--
-- Dumping data untuk tabel `subskala`
--

INSERT INTO `subskala` (`id_subskala`, `kode`, `nama_id`, `jumlah_item`) VALUES
('IDU001', 'depression', 'Depresi', 14),
('IDU002', 'anxiety', 'Kecemasan', 14),
('IDU003', 'stress', 'Stres', 14);

-- --------------------------------------------------------

--
-- Struktur dari tabel `variabel_skrining`
--

CREATE TABLE `variabel_skrining` (
  `id_variabel` varchar(10) NOT NULL,
  `no_item` tinyint(4) NOT NULL,
  `pertanyaan` text NOT NULL,
  `id_subskala` varchar(10) NOT NULL,
  `urutan_tampil` tinyint(4) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='42 item pertanyaan instrumen DASS-42';

--
-- Dumping data untuk tabel `variabel_skrining`
--

INSERT INTO `variabel_skrining` (`id_variabel`, `no_item`, `pertanyaan`, `id_subskala`, `urutan_tampil`, `status_aktif`) VALUES
('IDV001', 1, 'Saya merasa bahwa diri saya menjadi marah karena hal-hal sepele.', 'IDU003', 1, 1),
('IDV002', 2, 'Saya merasa bibir saya sering kering.', 'IDU002', 2, 1),
('IDV003', 3, 'Saya sama sekali tidak dapat merasakan perasaan positif.', 'IDU001', 3, 1),
('IDV004', 4, 'Saya mengalami kesulitan bernapas meskipun tidak melakukan aktivitas fisik.', 'IDU002', 4, 1),
('IDV005', 5, 'Saya merasa sepertinya tidak kuat lagi untuk melakukan suatu kegiatan.', 'IDU001', 5, 1),
('IDV006', 6, 'Saya cenderung bereaksi berlebihan terhadap suatu situasi.', 'IDU003', 6, 1),
('IDV007', 7, 'Saya merasa goyah, misalnya kaki terasa akan copot.', 'IDU002', 7, 1),
('IDV008', 8, 'Saya merasa sulit untuk bersantai.', 'IDU003', 8, 1),
('IDV009', 9, 'Saya merasa cemas dan akan sangat lega jika semua ini berakhir.', 'IDU002', 9, 1),
('IDV010', 10, 'Saya merasa tidak ada hal yang dapat diharapkan di masa depan.', 'IDU001', 10, 1),
('IDV011', 11, 'Saya menemukan diri saya mudah merasa kesal.', 'IDU003', 11, 1),
('IDV012', 12, 'Saya merasa telah menghabiskan banyak energi untuk merasa cemas.', 'IDU002', 12, 1),
('IDV013', 13, 'Saya merasa sedih dan tertekan.', 'IDU001', 13, 1),
('IDV014', 14, 'Saya menjadi tidak sabar ketika mengalami penundaan.', 'IDU003', 14, 1),
('IDV015', 15, 'Saya merasa lemas seperti mau pingsan.', 'IDU002', 15, 1),
('IDV016', 16, 'Saya merasa kehilangan minat terhadap banyak hal.', 'IDU001', 16, 1),
('IDV017', 17, 'Saya merasa bahwa saya tidak berharga sebagai seorang manusia.', 'IDU001', 17, 1),
('IDV018', 18, 'Saya merasa mudah tersinggung.', 'IDU003', 18, 1),
('IDV019', 19, 'Saya berkeringat berlebihan meskipun tidak panas atau beraktivitas.', 'IDU002', 19, 1),
('IDV020', 20, 'Saya merasa takut tanpa alasan yang jelas.', 'IDU002', 20, 1),
('IDV021', 21, 'Saya merasa bahwa hidup tidak bermanfaat.', 'IDU001', 21, 1),
('IDV022', 22, 'Saya merasa sulit untuk beristirahat.', 'IDU003', 22, 1),
('IDV023', 23, 'Saya mengalami kesulitan dalam menelan.', 'IDU002', 23, 1),
('IDV024', 24, 'Saya tidak dapat merasakan kenikmatan dari hal-hal yang saya lakukan.', 'IDU001', 24, 1),
('IDV025', 25, 'Saya menyadari detak jantung saya meskipun tidak beraktivitas.', 'IDU002', 25, 1),
('IDV026', 26, 'Saya merasa putus asa dan sedih.', 'IDU001', 26, 1),
('IDV027', 27, 'Saya merasa sangat mudah marah.', 'IDU003', 27, 1),
('IDV028', 28, 'Saya merasa hampir panik.', 'IDU002', 28, 1),
('IDV029', 29, 'Saya merasa sulit untuk tenang setelah sesuatu membuat saya kesal.', 'IDU003', 29, 1),
('IDV030', 30, 'Saya takut akan terhambat oleh tugas-tugas sepele.', 'IDU002', 30, 1),
('IDV031', 31, 'Saya tidak merasa antusias terhadap apa pun.', 'IDU001', 31, 1),
('IDV032', 32, 'Saya sulit bersabar saat menghadapi gangguan.', 'IDU003', 32, 1),
('IDV033', 33, 'Saya merasa gelisah.', 'IDU003', 33, 1),
('IDV034', 34, 'Saya merasa bahwa saya tidak berharga.', 'IDU001', 34, 1),
('IDV035', 35, 'Saya tidak dapat memaklumi hal yang menghalangi pekerjaan saya.', 'IDU003', 35, 1),
('IDV036', 36, 'Saya merasa sangat ketakutan.', 'IDU002', 36, 1),
('IDV037', 37, 'Saya melihat tidak ada harapan untuk masa depan.', 'IDU001', 37, 1),
('IDV038', 38, 'Saya merasa bahwa hidup tidak berarti.', 'IDU001', 38, 1),
('IDV039', 39, 'Saya mudah merasa gelisah.', 'IDU003', 39, 1),
('IDV040', 40, 'Saya khawatir akan situasi yang dapat membuat saya panik dan mempermalukan diri.', 'IDU002', 40, 1),
('IDV041', 41, 'Saya merasa gemetar, misalnya pada tangan.', 'IDU002', 41, 1),
('IDV042', 42, 'Saya merasa sulit untuk meningkatkan inisiatif dalam melakukan sesuatu.', 'IDU001', 42, 1);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_hasil_skrining`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_hasil_skrining` (
`id_skrining` varchar(10)
,`id_mahasiswa` varchar(10)
,`npm` varchar(20)
,`nama` varchar(100)
,`tgl_skrining` datetime
,`skor_depresi` tinyint(3) unsigned
,`skor_anxiety` tinyint(3) unsigned
,`skor_stress` tinyint(3) unsigned
,`skor_total` int(5) unsigned
,`id_kat_depresi` varchar(10)
,`kategori_depresi` varchar(30)
,`id_kat_anxiety` varchar(10)
,`kategori_anxiety` varchar(30)
,`id_kat_stress` varchar(10)
,`kategori_stress` varchar(30)
);

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `uq_admin_username` (`username`);

--
-- Indeks untuk tabel `detail_skrining`
--
ALTER TABLE `detail_skrining`
  ADD PRIMARY KEY (`id_detail`),
  ADD UNIQUE KEY `uq_skrining_variabel` (`id_skrining`,`id_variabel`),
  ADD KEY `fk_ds_variabel` (`id_variabel`);

--
-- Indeks untuk tabel `kategori_subskala`
--
ALTER TABLE `kategori_subskala`
  ADD PRIMARY KEY (`id_kategori`),
  ADD KEY `fk_kat_subskala` (`id_subskala`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id_mahasiswa`),
  ADD UNIQUE KEY `uq_mahasiswa_npm` (`npm`);

--
-- Indeks untuk tabel `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD PRIMARY KEY (`id_rekomendasi`),
  ADD KEY `fk_rek_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `skrining`
--
ALTER TABLE `skrining`
  ADD PRIMARY KEY (`id_skrining`),
  ADD KEY `fk_sk_mahasiswa` (`id_mahasiswa`);

--
-- Indeks untuk tabel `subskala`
--
ALTER TABLE `subskala`
  ADD PRIMARY KEY (`id_subskala`),
  ADD UNIQUE KEY `uq_subskala_kode` (`kode`);

--
-- Indeks untuk tabel `variabel_skrining`
--
ALTER TABLE `variabel_skrining`
  ADD PRIMARY KEY (`id_variabel`),
  ADD UNIQUE KEY `uq_no_item` (`no_item`),
  ADD KEY `fk_var_subskala` (`id_subskala`);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_hasil_skrining`
--
DROP TABLE IF EXISTS `v_hasil_skrining`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_hasil_skrining`  AS SELECT `s`.`id_skrining` AS `id_skrining`, `s`.`id_mahasiswa` AS `id_mahasiswa`, `m`.`npm` AS `npm`, `m`.`nama` AS `nama`, `s`.`tgl_skrining` AS `tgl_skrining`, `s`.`skor_depresi` AS `skor_depresi`, `s`.`skor_anxiety` AS `skor_anxiety`, `s`.`skor_stress` AS `skor_stress`, `s`.`skor_depresi`+ `s`.`skor_anxiety` + `s`.`skor_stress` AS `skor_total`, `kd`.`id_kategori` AS `id_kat_depresi`, `kd`.`nama_kategori` AS `kategori_depresi`, `ka`.`id_kategori` AS `id_kat_anxiety`, `ka`.`nama_kategori` AS `kategori_anxiety`, `ks`.`id_kategori` AS `id_kat_stress`, `ks`.`nama_kategori` AS `kategori_stress` FROM ((((`skrining` `s` join `mahasiswa` `m` on(`m`.`id_mahasiswa` = `s`.`id_mahasiswa`)) join `kategori_subskala` `kd` on(`kd`.`id_subskala` = 'IDU001' and `s`.`skor_depresi` between `kd`.`rentang_min` and `kd`.`rentang_max`)) join `kategori_subskala` `ka` on(`ka`.`id_subskala` = 'IDU002' and `s`.`skor_anxiety` between `ka`.`rentang_min` and `ka`.`rentang_max`)) join `kategori_subskala` `ks` on(`ks`.`id_subskala` = 'IDU003' and `s`.`skor_stress` between `ks`.`rentang_min` and `ks`.`rentang_max`)) ;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_skrining`
--
ALTER TABLE `detail_skrining`
  ADD CONSTRAINT `fk_ds_skrining` FOREIGN KEY (`id_skrining`) REFERENCES `skrining` (`id_skrining`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ds_variabel` FOREIGN KEY (`id_variabel`) REFERENCES `variabel_skrining` (`id_variabel`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kategori_subskala`
--
ALTER TABLE `kategori_subskala`
  ADD CONSTRAINT `fk_kat_subskala` FOREIGN KEY (`id_subskala`) REFERENCES `subskala` (`id_subskala`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD CONSTRAINT `fk_rek_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_subskala` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `skrining`
--
ALTER TABLE `skrining`
  ADD CONSTRAINT `fk_sk_mahasiswa` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `variabel_skrining`
--
ALTER TABLE `variabel_skrining`
  ADD CONSTRAINT `fk_var_subskala` FOREIGN KEY (`id_subskala`) REFERENCES `subskala` (`id_subskala`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
