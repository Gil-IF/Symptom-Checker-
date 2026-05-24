<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['npm'])) {
    header('Location: ../login.php');
    exit;
}

$npm = $_SESSION['npm'];
$id_mahasiswa = $_SESSION['id_mahasiswa'] ?? null;

// Ambil nama dari database jika id_mahasiswa tersedia
$nama = $npm; // fallback default
if ($id_mahasiswa) {
    $stmt = $pdo->prepare("SELECT nama FROM mahasiswa WHERE id_mahasiswa = ?");
    $stmt->execute([$id_mahasiswa]);
    $nama = $stmt->fetchColumn() ?: $npm;
}

$page_title = 'Home - Symptom Checker';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

    <!-- ========== TOPBAR ========== -->
    <header class="topbar">
        <a href="index.php" class="brand">
            <img src="../assets/img/logo.png" alt="Logo Symptom Checker">
            <span>Symptom Checker</span>
        </a>

        <nav class="nav-links">
            <a href="../skrining/index.php">Checking</a>
            <a href="index.php" class="active">Home</a>
            <button class="hamburger" type="button" aria-label="Menu" onclick="openNavDrawer()">
                <span></span>
            </button>
        </nav>
    </header>

    <!-- ========== NAVIGASI DRAWER ========== -->
    <?php include 'navigasi.php'; ?>

    <!-- ========== KONTEN UTAMA ========== -->
    <div class="wrap">
        <div class="breadcrumb">Home</div>

        <div class="hero">
            <h1>Halo, <?= htmlspecialchars($nama); ?>!<br>Bagaimana perasaanmu hari ini?</h1>
        </div>

        <section class="cards">
            <a href="../skrining/index.php" class="card blue">
                <div>
                    <h2>Mulai Skrining</h2>
                    <p>Setiap perasaan memiliki arti. Mari pahami apa yang sedang Anda alami.</p>
                </div>
                <img src="../assets/img/helpme.png" alt="Ilustrasi Skrining">
            </a>

            <a href="../riwayat/index.php" class="card mint">
                <div>
                    <h2>Riwayat Skrining</h2>
                    <p>Lihat kembali hasil skrining sebelumnya dan pantau perubahan kondisi emosional Anda dari waktu ke waktu.</p>
                </div>
                <img src="../assets/img/kertas.png" alt="Ilustrasi Riwayat">
            </a>
        </section>
    </div>

    <!-- ========== GAMBAR BAWAH ========== -->
    <div class="bottom-illustration">
        <img src="../assets/img/rt2.png" alt="Ilustrasi peduli kesehatan mental">
    </div>

</body>
</html>