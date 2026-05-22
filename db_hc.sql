-- ============================================================
--  db_hc  |  Normalized 3NF  |  DASS-42 Compliant
--  ID kustom diberikan manual : IDA..., IDM..., IDU..., dst.
--  Versi  : 3.0  |  2026
--  Bisa langsung dijalankan TANPA mengubah delimiter
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET foreign_key_checks = 0;

DROP DATABASE IF EXISTS db_hc;
CREATE DATABASE db_hc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_hc;

-- ============================================================
-- 1. admin
-- ============================================================
CREATE TABLE admin (
  id_admin    VARCHAR(10)  NOT NULL,
  username    VARCHAR(50)  NOT NULL,
  password    VARCHAR(255) NOT NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_admin),
  UNIQUE KEY uq_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Akun administrator sistem';

INSERT INTO admin (id_admin, username, password) VALUES
('IDA001', 'admin1', '$2y$10$HASH_GANTI_DENGAN_BCRYPT_1'),
('IDA002', 'admin2', '$2y$10$HASH_GANTI_DENGAN_BCRYPT_2'),
('IDA003', 'admin3', '$2y$10$HASH_GANTI_DENGAN_BCRYPT_3'),
('IDA004', 'admin4', '$2y$10$HASH_GANTI_DENGAN_BCRYPT_4');

-- ============================================================
-- 2. mahasiswa
-- ============================================================
CREATE TABLE mahasiswa (
  id_mahasiswa  VARCHAR(10)  NOT NULL,
  npm           VARCHAR(20)  NOT NULL,
  nama          VARCHAR(100) NOT NULL,
  password      VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_mahasiswa),
  UNIQUE KEY uq_mahasiswa_npm (npm)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Data mahasiswa pengguna aplikasi';

INSERT INTO mahasiswa (id_mahasiswa, npm, nama, password) VALUES
('IDM001', '24081010149', 'Achmad Azkal Fatich',          '$2y$10$HASH_149'),
('IDM002', '24081010225', 'Clarista Nailah Sari Paramita', '$2y$10$HASH_225'),
('IDM003', '24081010238', 'Anisa Nur Azizah',              '$2y$10$HASH_238'),
('IDM004', '24081010252', 'Gilbert Christian Rumtotmey',   '$2y$10$HASH_252'),
('IDM005', '24081010339', 'Imroatus Saadah',               '$2y$10$HASH_339'),
('IDM006', '24081010142', 'Weka Wijaya',                   '$2y$10$HASH_142');

-- ============================================================
-- 3. subskala  (master 3 subskala DASS-42)
-- ============================================================
CREATE TABLE subskala (
  id_subskala   VARCHAR(10) NOT NULL,
  kode          ENUM('depression','anxiety','stress') NOT NULL,
  nama_id       VARCHAR(50) NOT NULL,
  jumlah_item   TINYINT     NOT NULL DEFAULT 14,
  PRIMARY KEY (id_subskala),
  UNIQUE KEY uq_subskala_kode (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Master 3 subskala DASS-42';

INSERT INTO subskala (id_subskala, kode, nama_id, jumlah_item) VALUES
('IDU001', 'depression', 'Depresi',   14),
('IDU002', 'anxiety',    'Kecemasan', 14),
('IDU003', 'stress',     'Stres',     14);

-- ============================================================
-- 4. kategori_subskala
-- ============================================================
CREATE TABLE kategori_subskala (
  id_kategori   VARCHAR(10)      NOT NULL,
  id_subskala   VARCHAR(10)      NOT NULL,
  nama_kategori VARCHAR(30)      NOT NULL,
  rentang_min   TINYINT UNSIGNED NOT NULL,
  rentang_max   TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (id_kategori),
  CONSTRAINT fk_kat_subskala FOREIGN KEY (id_subskala)
    REFERENCES subskala (id_subskala) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Kategorisasi skor per subskala — cutoff resmi DASS-42';

INSERT INTO kategori_subskala (id_kategori, id_subskala, nama_kategori, rentang_min, rentang_max) VALUES
-- Depresi (IDU001)
('IDK001', 'IDU001', 'Normal',       0,  9),
('IDK002', 'IDU001', 'Ringan',      10, 13),
('IDK003', 'IDU001', 'Sedang',      14, 20),
('IDK004', 'IDU001', 'Berat',       21, 27),
('IDK005', 'IDU001', 'Sangat Berat',28, 42),
-- Kecemasan (IDU002)
('IDK006', 'IDU002', 'Normal',       0,  7),
('IDK007', 'IDU002', 'Ringan',       8,  9),
('IDK008', 'IDU002', 'Sedang',      10, 14),
('IDK009', 'IDU002', 'Berat',       15, 19),
('IDK010', 'IDU002', 'Sangat Berat',20, 42),
-- Stres (IDU003)
('IDK011', 'IDU003', 'Normal',       0, 14),
('IDK012', 'IDU003', 'Ringan',      15, 18),
('IDK013', 'IDU003', 'Sedang',      19, 25),
('IDK014', 'IDU003', 'Berat',       26, 33),
('IDK015', 'IDU003', 'Sangat Berat',34, 42);

-- ============================================================
-- 5. variabel_skrining  (42 item DASS-42)
-- ============================================================
CREATE TABLE variabel_skrining (
  id_variabel   VARCHAR(10)  NOT NULL,
  no_item       TINYINT      NOT NULL,
  pertanyaan    TEXT         NOT NULL,
  id_subskala   VARCHAR(10)  NOT NULL,
  urutan_tampil TINYINT      NOT NULL,
  status_aktif  TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id_variabel),
  UNIQUE KEY uq_no_item (no_item),
  CONSTRAINT fk_var_subskala FOREIGN KEY (id_subskala)
    REFERENCES subskala (id_subskala) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='42 item pertanyaan instrumen DASS-42';

INSERT INTO variabel_skrining (id_variabel, no_item, pertanyaan, id_subskala, urutan_tampil) VALUES
('IDV001',  1, 'Saya merasa bahwa diri saya menjadi marah karena hal-hal sepele.','IDU003',1),
('IDV002',  2, 'Saya merasa bibir saya sering kering.','IDU002',2),
('IDV003',  3, 'Saya sama sekali tidak dapat merasakan perasaan positif.','IDU001',3),
('IDV004',  4, 'Saya mengalami kesulitan bernapas meskipun tidak melakukan aktivitas fisik.','IDU002',4),
('IDV005',  5, 'Saya merasa sepertinya tidak kuat lagi untuk melakukan suatu kegiatan.','IDU001',5),
('IDV006',  6, 'Saya cenderung bereaksi berlebihan terhadap suatu situasi.','IDU003',6),
('IDV007',  7, 'Saya merasa goyah, misalnya kaki terasa akan copot.','IDU002',7),
('IDV008',  8, 'Saya merasa sulit untuk bersantai.','IDU003',8),
('IDV009',  9, 'Saya merasa cemas dan akan sangat lega jika semua ini berakhir.','IDU002',9),
('IDV010', 10, 'Saya merasa tidak ada hal yang dapat diharapkan di masa depan.','IDU001',10),
('IDV011', 11, 'Saya menemukan diri saya mudah merasa kesal.','IDU003',11),
('IDV012', 12, 'Saya merasa telah menghabiskan banyak energi untuk merasa cemas.','IDU002',12),
('IDV013', 13, 'Saya merasa sedih dan tertekan.','IDU001',13),
('IDV014', 14, 'Saya menjadi tidak sabar ketika mengalami penundaan.','IDU003',14),
('IDV015', 15, 'Saya merasa lemas seperti mau pingsan.','IDU002',15),
('IDV016', 16, 'Saya merasa kehilangan minat terhadap banyak hal.','IDU001',16),
('IDV017', 17, 'Saya merasa bahwa saya tidak berharga sebagai seorang manusia.','IDU001',17),
('IDV018', 18, 'Saya merasa mudah tersinggung.','IDU003',18),
('IDV019', 19, 'Saya berkeringat berlebihan meskipun tidak panas atau beraktivitas.','IDU002',19),
('IDV020', 20, 'Saya merasa takut tanpa alasan yang jelas.','IDU002',20),
('IDV021', 21, 'Saya merasa bahwa hidup tidak bermanfaat.','IDU001',21),
('IDV022', 22, 'Saya merasa sulit untuk beristirahat.','IDU003',22),
('IDV023', 23, 'Saya mengalami kesulitan dalam menelan.','IDU002',23),
('IDV024', 24, 'Saya tidak dapat merasakan kenikmatan dari hal-hal yang saya lakukan.','IDU001',24),
('IDV025', 25, 'Saya menyadari detak jantung saya meskipun tidak beraktivitas.','IDU002',25),
('IDV026', 26, 'Saya merasa putus asa dan sedih.','IDU001',26),
('IDV027', 27, 'Saya merasa sangat mudah marah.','IDU003',27),
('IDV028', 28, 'Saya merasa hampir panik.','IDU002',28),
('IDV029', 29, 'Saya merasa sulit untuk tenang setelah sesuatu membuat saya kesal.','IDU003',29),
('IDV030', 30, 'Saya takut akan terhambat oleh tugas-tugas sepele.','IDU002',30),
('IDV031', 31, 'Saya tidak merasa antusias terhadap apa pun.','IDU001',31),
('IDV032', 32, 'Saya sulit bersabar saat menghadapi gangguan.','IDU003',32),
('IDV033', 33, 'Saya merasa gelisah.','IDU003',33),
('IDV034', 34, 'Saya merasa bahwa saya tidak berharga.','IDU001',34),
('IDV035', 35, 'Saya tidak dapat memaklumi hal yang menghalangi pekerjaan saya.','IDU003',35),
('IDV036', 36, 'Saya merasa sangat ketakutan.','IDU002',36),
('IDV037', 37, 'Saya melihat tidak ada harapan untuk masa depan.','IDU001',37),
('IDV038', 38, 'Saya merasa bahwa hidup tidak berarti.','IDU001',38),
('IDV039', 39, 'Saya mudah merasa gelisah.','IDU003',39),
('IDV040', 40, 'Saya khawatir akan situasi yang dapat membuat saya panik dan mempermalukan diri.','IDU002',40),
('IDV041', 41, 'Saya merasa gemetar, misalnya pada tangan.','IDU002',41),
('IDV042', 42, 'Saya merasa sulit untuk meningkatkan inisiatif dalam melakukan sesuatu.','IDU001',42);

-- ============================================================
-- 6. skrining  (header sesi skrining)
-- ============================================================
CREATE TABLE skrining (
  id_skrining   VARCHAR(10)      NOT NULL,
  id_mahasiswa  VARCHAR(10)      NOT NULL,
  tgl_skrining  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  skor_depresi  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  skor_anxiety  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  skor_stress   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  catatan       TEXT,
  PRIMARY KEY (id_skrining),
  CONSTRAINT fk_sk_mahasiswa FOREIGN KEY (id_mahasiswa)
    REFERENCES mahasiswa (id_mahasiswa) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Header sesi skrining. skor_total & kategori dihitung via VIEW';

-- ============================================================
-- 7. detail_skrining  (jawaban per item per sesi)
-- ============================================================
CREATE TABLE detail_skrining (
  id_detail    VARCHAR(10)      NOT NULL,
  id_skrining  VARCHAR(10)      NOT NULL,
  id_variabel  VARCHAR(10)      NOT NULL,
  nilai        TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (id_detail),
  UNIQUE KEY uq_skrining_variabel (id_skrining, id_variabel),
  CONSTRAINT fk_ds_skrining FOREIGN KEY (id_skrining)
    REFERENCES skrining (id_skrining) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ds_variabel FOREIGN KEY (id_variabel)
    REFERENCES variabel_skrining (id_variabel) ON UPDATE CASCADE,
  CONSTRAINT chk_nilai CHECK (nilai IN (0,1,2,3))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Jawaban per item pertanyaan per sesi skrining';

-- ============================================================
-- 8. rekomendasi  (per kategori subskala)
-- ============================================================
CREATE TABLE rekomendasi (
  id_rekomendasi    VARCHAR(10)  NOT NULL,
  id_kategori       VARCHAR(10)  NOT NULL,
  teks_rekomendasi  TEXT         NOT NULL,
  urutan            TINYINT      NOT NULL DEFAULT 1,
  status_aktif      TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id_rekomendasi),
  CONSTRAINT fk_rek_kategori FOREIGN KEY (id_kategori)
    REFERENCES kategori_subskala (id_kategori)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Rekomendasi per kategori subskala DASS-42';

INSERT INTO rekomendasi (id_rekomendasi, id_kategori, teks_rekomendasi, urutan) VALUES
-- Depresi Normal (IDK001)
('IDR001', 'IDK001','Pertahankan pola hidup sehat dengan tidur cukup, makan teratur, dan olahraga ringan.',1),
('IDR002', 'IDK001','Luangkan waktu untuk aktivitas yang Anda nikmati.',2),
-- Depresi Ringan (IDK002)
('IDR003', 'IDK002','Coba identifikasi pemicu perasaan negatif dan diskusikan dengan orang terdekat.',1),
('IDR004', 'IDK002','Tingkatkan aktivitas fisik ringan seperti jalan kaki 30 menit per hari.',2),
-- Depresi Sedang (IDK003)
('IDR005', 'IDK003','Pertimbangkan untuk berkonsultasi dengan konselor atau psikolog.',1),
('IDR006', 'IDK003','Batasi konsumsi media sosial dan pastikan kualitas tidur yang baik.',2),
-- Depresi Berat (IDK004)
('IDR007', 'IDK004','Segera konsultasikan kondisi ini dengan psikolog atau psikiater.',1),
('IDR008', 'IDK004','Jangan menghadapi kondisi ini sendirian, hubungi orang yang dipercaya.',2),
-- Depresi Sangat Berat (IDK005)
('IDR009', 'IDK005','Diperlukan penanganan profesional segera. Hubungi layanan kesehatan mental.',1),
('IDR010', 'IDK005','Jika ada pikiran menyakiti diri sendiri, segera hubungi hotline kesehatan jiwa.',2),
-- Kecemasan Normal (IDK006)
('IDR011', 'IDK006','Pertahankan gaya hidup aktif dan teknik relaksasi seperti meditasi ringan.',1),
-- Kecemasan Ringan (IDK007)
('IDR012', 'IDK007','Latih teknik pernapasan dalam (4-7-8 breathing) saat merasa cemas.',1),
('IDR013', 'IDK007','Kurangi konsumsi kafein dan pastikan tidur yang cukup.',2),
-- Kecemasan Sedang (IDK008)
('IDR014', 'IDK008','Pertimbangkan sesi konseling untuk mengelola kecemasan secara terstruktur.',1),
('IDR015', 'IDK008','Coba progressive muscle relaxation atau mindfulness harian.',2),
-- Kecemasan Berat (IDK009)
('IDR016', 'IDK009','Konsultasikan dengan profesional kesehatan mental untuk evaluasi lebih lanjut.',1),
('IDR017', 'IDK009','Hindari situasi yang memicu kecemasan berlebih sementara mencari bantuan.',2),
-- Kecemasan Sangat Berat (IDK010)
('IDR018', 'IDK010','Segera cari bantuan profesional. Kecemasan ini dapat mengganggu fungsi sehari-hari.',1),
('IDR019', 'IDK010','Pertimbangkan terapi kognitif-perilaku (CBT) dengan psikolog terlisensi.',2),
-- Stres Normal (IDK011)
('IDR020', 'IDK011','Pertahankan manajemen waktu yang baik dan prioritaskan istirahat berkualitas.',1),
-- Stres Ringan (IDK012)
('IDR021', 'IDK012','Atur kembali prioritas tugas dan coba teknik manajemen stres seperti journaling.',1),
('IDR022', 'IDK012','Pastikan ada waktu untuk hobi dan sosialisasi dengan teman.',2),
-- Stres Sedang (IDK013)
('IDR023', 'IDK013','Evaluasi sumber stres utama dan buat rencana konkret untuk mengatasinya.',1),
('IDR024', 'IDK013','Lakukan olahraga rutin dan teknik relaksasi minimal 20 menit per hari.',2),
-- Stres Berat (IDK014)
('IDR025', 'IDK014','Konsultasikan dengan konselor kampus atau tenaga profesional.',1),
('IDR026', 'IDK014','Pertimbangkan untuk mengurangi beban tugas sementara dan minta dukungan sosial.',2),
-- Stres Sangat Berat (IDK015)
('IDR027', 'IDK015','Segera cari bantuan profesional. Stres berat berisiko mempengaruhi kesehatan fisik.',1),
('IDR028', 'IDK015','Jangan tunda: hubungi psikolog, psikiater, atau layanan konseling profesional.',2);

-- ============================================================
-- VIEW: Hasil skrining lengkap
-- ============================================================
CREATE OR REPLACE VIEW v_hasil_skrining AS
SELECT
  s.id_skrining,
  s.id_mahasiswa,
  m.npm,
  m.nama,
  s.tgl_skrining,
  s.skor_depresi,
  s.skor_anxiety,
  s.skor_stress,
  (s.skor_depresi + s.skor_anxiety + s.skor_stress) AS skor_total,
  kd.id_kategori  AS id_kat_depresi,
  kd.nama_kategori AS kategori_depresi,
  ka.id_kategori  AS id_kat_anxiety,
  ka.nama_kategori AS kategori_anxiety,
  ks.id_kategori  AS id_kat_stress,
  ks.nama_kategori AS kategori_stress
FROM skrining s
JOIN mahasiswa m ON m.id_mahasiswa = s.id_mahasiswa
JOIN kategori_subskala kd
  ON kd.id_subskala = 'IDU001'
 AND s.skor_depresi  BETWEEN kd.rentang_min AND kd.rentang_max
JOIN kategori_subskala ka
  ON ka.id_subskala = 'IDU002'
 AND s.skor_anxiety  BETWEEN ka.rentang_min AND ka.rentang_max
JOIN kategori_subskala ks
  ON ks.id_subskala = 'IDU003'
 AND s.skor_stress   BETWEEN ks.rentang_min AND ks.rentang_max;

SET foreign_key_checks = 1;