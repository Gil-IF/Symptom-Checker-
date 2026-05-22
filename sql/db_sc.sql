-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 19 Bulan Mei 2026 pada 06.14
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
-- Basis data: `db_sc`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin1', '1sampai9'),
(2, 'admin2', '2sampai8'),
(3, 'admin3', '3sampai8'),
(4, 'admin4', '4sampai8');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_skrining`
--

CREATE TABLE `detail_skrining` (
  `id_detail` int(11) NOT NULL,
  `id_skrining` int(11) NOT NULL,
  `id_variabel` int(11) NOT NULL,
  `nilai` tinyint(3) UNSIGNED NOT NULL
) ;

--
-- Dumping data untuk tabel `detail_skrining`
--

INSERT INTO `detail_skrining` (`id_detail`, `id_skrining`, `id_variabel`, `nilai`) VALUES
(1, 1, 1, 0),
(2, 1, 2, 3),
(3, 1, 3, 0),
(4, 1, 4, 3),
(5, 1, 5, 3),
(6, 1, 6, 1),
(7, 1, 7, 1),
(8, 1, 8, 3),
(9, 1, 9, 1),
(10, 1, 10, 1),
(11, 1, 11, 0),
(12, 1, 12, 1),
(13, 1, 13, 0),
(14, 1, 14, 1),
(15, 1, 15, 1),
(16, 1, 16, 0),
(17, 1, 17, 1),
(18, 1, 18, 0),
(19, 1, 19, 3),
(20, 1, 20, 0),
(21, 1, 21, 3),
(22, 1, 22, 1),
(23, 1, 23, 0),
(24, 1, 24, 1),
(25, 1, 25, 0),
(26, 1, 26, 3),
(27, 1, 27, 0),
(28, 1, 28, 1),
(29, 1, 29, 1),
(30, 1, 30, 0),
(31, 1, 31, 3),
(32, 1, 32, 1),
(33, 1, 33, 3),
(34, 1, 34, 2),
(35, 1, 35, 3),
(36, 1, 36, 1),
(37, 1, 37, 0),
(38, 1, 38, 1),
(39, 1, 39, 2),
(40, 1, 40, 3),
(41, 1, 41, 2),
(42, 1, 42, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `level_risiko`
--

CREATE TABLE `level_risiko` (
  `id_level` int(11) NOT NULL,
  `nama_level` varchar(20) NOT NULL,
  `skor_min` int(11) NOT NULL,
  `skor_max` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `level_risiko`
--

INSERT INTO `level_risiko` (`id_level`, `nama_level`, `skor_min`, `skor_max`, `deskripsi`) VALUES
(1, 'Rendah', 0, 20, 'Kondisi kesehatan mental berada pada tingkat risiko rendah.'),
(2, 'Sedang', 21, 41, 'Terdapat indikasi gejala yang perlu diperhatikan lebih lanjut.'),
(3, 'Tinggi', 42, 126, 'Terdapat indikasi gejala yang cukup signifikan dan disarankan untuk berkonsultasi dengan tenaga profesional.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id_mahasiswa` int(11) NOT NULL,
  `npm` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`id_mahasiswa`, `npm`, `password`) VALUES
(1, '24081010149', '149'),
(2, '24081010225', '225'),
(3, '24081010238', '238'),
(4, '24081010252', '$2y$10$MtY6ehjn/2Vhc48UhBJj9OngE/VdGpK.kQ9Jb8N5cMgEbVSyAzI5m'),
(5, '24081010339', '339'),
(6, '24081010142', '142'),
(7, '24081010006', '$2y$10$isdoEuXGP/yQp/uZ8omkFOzpFYe3KrXh.Xv.qBLYuZal4dmfBC1EW');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekomendasi`
--

CREATE TABLE `rekomendasi` (
  `id_rekomendasi` int(11) NOT NULL,
  `id_level` int(11) NOT NULL,
  `teks_rekomendasi` text NOT NULL,
  `urutan_rekomendasi` tinyint(4) NOT NULL DEFAULT 1,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `rekomendasi`
--

INSERT INTO `rekomendasi` (`id_rekomendasi`, `id_level`, `teks_rekomendasi`, `urutan_rekomendasi`, `status_aktif`) VALUES
(1, 1, 'Pertahankan pola hidup sehat dengan tidur yang cukup, makan teratur, dan olahraga ringan secara rutin.', 1, 1),
(2, 1, 'Luangkan waktu untuk aktivitas yang Anda nikmati dan membantu menjaga keseimbangan emosional.', 2, 1),
(3, 1, 'Tetap jaga komunikasi dengan teman, keluarga, atau orang terdekat.', 3, 1),
(4, 2, 'Cobalah mengurangi sumber stres dan atur kembali prioritas aktivitas sehari-hari.', 1, 1),
(5, 2, 'Lakukan teknik relaksasi seperti pernapasan dalam, meditasi, atau olahraga ringan.', 2, 1),
(6, 2, 'Pertimbangkan untuk berkonsultasi dengan konselor kampus atau tenaga profesional jika gejala berlanjut.', 3, 1),
(7, 3, 'Disarankan untuk segera berkonsultasi dengan psikolog, psikiater, atau layanan konseling profesional.', 1, 1),
(8, 3, 'Jangan menghadapi kondisi ini sendirian. Hubungi orang terpercaya untuk mendapatkan dukungan.', 2, 1),
(9, 3, 'Jika gejala terasa sangat berat dan mengganggu aktivitas sehari-hari, segera cari bantuan profesional.', 3, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `skrining`
--

CREATE TABLE `skrining` (
  `id_skrining` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `tgl_skrining` datetime NOT NULL DEFAULT current_timestamp(),
  `skor_depresi` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `skor_anxiety` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `skor_stress` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `skor_total` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `id_level` int(11) NOT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `skrining`
--

INSERT INTO `skrining` (`id_skrining`, `id_mahasiswa`, `tgl_skrining`, `skor_depresi`, `skor_anxiety`, `skor_stress`, `skor_total`, `id_level`, `catatan`) VALUES
(1, 4, '2026-05-19 10:43:04', 19, 20, 16, 55, 3, 'Depresi: Sedang | Anxiety: Sangat Berat | Stress: Ringan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `variabel_skrining`
--

CREATE TABLE `variabel_skrining` (
  `id_variabel` int(11) NOT NULL,
  `no_item` tinyint(4) NOT NULL,
  `pertanyaan` text NOT NULL,
  `subskala` enum('depression','anxiety','stress') NOT NULL,
  `skor_min` tinyint(4) NOT NULL DEFAULT 0,
  `skor_max` tinyint(4) NOT NULL DEFAULT 3,
  `urutan_tampil` tinyint(4) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `variabel_skrining`
--

INSERT INTO `variabel_skrining` (`id_variabel`, `no_item`, `pertanyaan`, `subskala`, `skor_min`, `skor_max`, `urutan_tampil`, `status_aktif`) VALUES
(1, 1, 'Saya merasa bahwa diri saya menjadi marah karena hal-hal sepele.', 'stress', 0, 3, 1, 1),
(2, 2, 'Saya merasa bibir saya sering kering.', 'anxiety', 0, 3, 2, 1),
(3, 3, 'Saya sama sekali tidak dapat merasakan perasaan positif.', 'depression', 0, 3, 3, 1),
(4, 4, 'Saya mengalami kesulitan bernapas meskipun tidak melakukan aktivitas fisik.', 'anxiety', 0, 3, 4, 1),
(5, 5, 'Saya merasa sepertinya tidak kuat lagi untuk melakukan suatu kegiatan.', 'depression', 0, 3, 5, 1),
(6, 6, 'Saya cenderung bereaksi berlebihan terhadap suatu situasi.', 'stress', 0, 3, 6, 1),
(7, 7, 'Saya merasa goyah, misalnya kaki terasa akan copot.', 'anxiety', 0, 3, 7, 1),
(8, 8, 'Saya merasa sulit untuk bersantai.', 'stress', 0, 3, 8, 1),
(9, 9, 'Saya merasa cemas dan akan sangat lega jika semua ini berakhir.', 'anxiety', 0, 3, 9, 1),
(10, 10, 'Saya merasa tidak ada hal yang dapat diharapkan di masa depan.', 'depression', 0, 3, 10, 1),
(11, 11, 'Saya menemukan diri saya mudah merasa kesal.', 'stress', 0, 3, 11, 1),
(12, 12, 'Saya merasa telah menghabiskan banyak energi untuk merasa cemas.', 'anxiety', 0, 3, 12, 1),
(13, 13, 'Saya merasa sedih dan tertekan.', 'depression', 0, 3, 13, 1),
(14, 14, 'Saya menjadi tidak sabar ketika mengalami penundaan.', 'stress', 0, 3, 14, 1),
(15, 15, 'Saya merasa lemas seperti mau pingsan.', 'anxiety', 0, 3, 15, 1),
(16, 16, 'Saya merasa kehilangan minat terhadap banyak hal.', 'depression', 0, 3, 16, 1),
(17, 17, 'Saya merasa bahwa saya tidak berharga sebagai seorang manusia.', 'depression', 0, 3, 17, 1),
(18, 18, 'Saya merasa mudah tersinggung.', 'stress', 0, 3, 18, 1),
(19, 19, 'Saya berkeringat berlebihan meskipun tidak panas atau beraktivitas.', 'anxiety', 0, 3, 19, 1),
(20, 20, 'Saya merasa takut tanpa alasan yang jelas.', 'anxiety', 0, 3, 20, 1),
(21, 21, 'Saya merasa bahwa hidup tidak bermanfaat.', 'depression', 0, 3, 21, 1),
(22, 22, 'Saya merasa sulit untuk beristirahat.', 'stress', 0, 3, 22, 1),
(23, 23, 'Saya mengalami kesulitan dalam menelan.', 'anxiety', 0, 3, 23, 1),
(24, 24, 'Saya tidak dapat merasakan kenikmatan dari hal-hal yang saya lakukan.', 'depression', 0, 3, 24, 1),
(25, 25, 'Saya menyadari detak jantung saya meskipun tidak beraktivitas.', 'anxiety', 0, 3, 25, 1),
(26, 26, 'Saya merasa putus asa dan sedih.', 'depression', 0, 3, 26, 1),
(27, 27, 'Saya merasa sangat mudah marah.', 'stress', 0, 3, 27, 1),
(28, 28, 'Saya merasa hampir panik.', 'anxiety', 0, 3, 28, 1),
(29, 29, 'Saya merasa sulit untuk tenang setelah sesuatu membuat saya kesal.', 'stress', 0, 3, 29, 1),
(30, 30, 'Saya takut akan terhambat oleh tugas-tugas sepele.', 'anxiety', 0, 3, 30, 1),
(31, 31, 'Saya tidak merasa antusias terhadap apa pun.', 'depression', 0, 3, 31, 1),
(32, 32, 'Saya sulit bersabar saat menghadapi gangguan.', 'stress', 0, 3, 32, 1),
(33, 33, 'Saya merasa gelisah.', 'stress', 0, 3, 33, 1),
(34, 34, 'Saya merasa bahwa saya tidak berharga.', 'depression', 0, 3, 34, 1),
(35, 35, 'Saya tidak dapat memaklumi hal yang menghalangi pekerjaan saya.', 'stress', 0, 3, 35, 1),
(36, 36, 'Saya merasa sangat ketakutan.', 'anxiety', 0, 3, 36, 1),
(37, 37, 'Saya melihat tidak ada harapan untuk masa depan.', 'depression', 0, 3, 37, 1),
(38, 38, 'Saya merasa bahwa hidup tidak berarti.', 'depression', 0, 3, 38, 1),
(39, 39, 'Saya mudah merasa gelisah.', 'stress', 0, 3, 39, 1),
(40, 40, 'Saya khawatir akan situasi yang dapat membuat saya panik dan mempermalukan diri.', 'anxiety', 0, 3, 40, 1),
(41, 41, 'Saya merasa gemetar, misalnya pada tangan.', 'anxiety', 0, 3, 41, 1),
(42, 42, 'Saya merasa sulit untuk meningkatkan inisiatif dalam melakukan sesuatu.', 'depression', 0, 3, 42, 1);

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `detail_skrining`
--
ALTER TABLE `detail_skrining`
  ADD PRIMARY KEY (`id_detail`),
  ADD UNIQUE KEY `uk_skrining_variabel` (`id_skrining`,`id_variabel`),
  ADD KEY `fk_detail_variabel` (`id_variabel`);

--
-- Indeks untuk tabel `level_risiko`
--
ALTER TABLE `level_risiko`
  ADD PRIMARY KEY (`id_level`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id_mahasiswa`);

--
-- Indeks untuk tabel `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD PRIMARY KEY (`id_rekomendasi`),
  ADD KEY `fk_rekomendasi_level` (`id_level`);

--
-- Indeks untuk tabel `skrining`
--
ALTER TABLE `skrining`
  ADD PRIMARY KEY (`id_skrining`),
  ADD KEY `fk_skrining_mahasiswa` (`id_mahasiswa`),
  ADD KEY `fk_skrining_level` (`id_level`);

--
-- Indeks untuk tabel `variabel_skrining`
--
ALTER TABLE `variabel_skrining`
  ADD PRIMARY KEY (`id_variabel`),
  ADD UNIQUE KEY `no_item` (`no_item`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `detail_skrining`
--
ALTER TABLE `detail_skrining`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `level_risiko`
--
ALTER TABLE `level_risiko`
  MODIFY `id_level` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id_mahasiswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `rekomendasi`
--
ALTER TABLE `rekomendasi`
  MODIFY `id_rekomendasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `skrining`
--
ALTER TABLE `skrining`
  MODIFY `id_skrining` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `variabel_skrining`
--
ALTER TABLE `variabel_skrining`
  MODIFY `id_variabel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_skrining`
--
ALTER TABLE `detail_skrining`
  ADD CONSTRAINT `fk_detail_skrining` FOREIGN KEY (`id_skrining`) REFERENCES `skrining` (`id_skrining`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_variabel` FOREIGN KEY (`id_variabel`) REFERENCES `variabel_skrining` (`id_variabel`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD CONSTRAINT `fk_rekomendasi_level` FOREIGN KEY (`id_level`) REFERENCES `level_risiko` (`id_level`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `skrining`
--
ALTER TABLE `skrining`
  ADD CONSTRAINT `fk_skrining_level` FOREIGN KEY (`id_level`) REFERENCES `level_risiko` (`id_level`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_skrining_mahasiswa` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
