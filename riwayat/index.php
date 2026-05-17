<?php
session_start();

if (!isset($_SESSION['npm'])) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Riwayat Screening - Symptom Checker';
$npm = $_SESSION['npm'];
$nama_user = $_SESSION['nama_panggilan'] ?? $npm;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title); ?></title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg: #f5f5f5;
            --white: #ffffff;
            --text: #2f3137;
            --shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            --accent: #cbb2e1;
            --card-blue: #6ea3d9;
            --card-mint: #aee9cd;
            --yellow: #fff100;
            --radius: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ========== TOPBAR (sama dengan Home) ========== */
        .topbar {
            background: var(--white);
            min-height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 18px;
        }

        .brand img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            font-size: 16px;
            font-weight: 600;
        }

        .nav-links a {
            position: relative;
            padding-bottom: 4px;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #6ea3d9;
        }

        .nav-links a.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100%;
            height: 4px;
            border-radius: 999px;
            background: #b8e0d1;
        }

        .hamburger {
            width: 48px;
            height: 48px;
            border: none;
            border-radius: 16px;
            background: var(--accent);
            display: grid;
            place-items: center;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(0,0,0,.12);
            transition: background 0.2s;
        }

        .hamburger:hover {
            background: #b794d4;
        }

        .hamburger span,
        .hamburger span::before,
        .hamburger span::after {
            content: '';
            display: block;
            width: 22px;
            height: 3px;
            border-radius: 999px;
            background: #37343f;
            position: relative;
        }

        .hamburger span::before {
            position: absolute;
            top: -7px;
            left: 0;
        }

        .hamburger span::after {
            position: absolute;
            top: 7px;
            left: 0;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 20px 32px;
        }

        /* Breadcrumb */
        .breadcrumb {
            color: #8e8e98;
            font-size: 14px;
            margin-bottom: 16px;
            font-weight: 500;
        }

        /* Header halaman */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .page-header .subtitle {
            font-size: 14px;
            color: #666;
        }

        /* Cards ringkasan */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .summary-card {
            background: var(--card-blue);
            color: #fff;
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .summary-card i {
            font-size: 24px;
        }

        .summary-card .label {
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .summary-card .value {
            font-size: 28px;
            font-weight: 700;
        }

        .summary-card .desc {
            font-size: 11px;
            opacity: 0.8;
        }

        /* Graph */
        .graph-box {
            background: var(--card-mint);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: var(--shadow);
        }

        .graph-title {
            font-weight: 600;
            margin-bottom: 16px;
        }

        .graph-canvas {
            position: relative;
            height: 180px;
            display: flex;
            align-items: flex-end;
            gap: 20px;
            margin-top: 30px;
            padding-left: 60px;
        }

        .bar {
            flex: 1;
            background: #fff;
            border-radius: 12px 12px 0 0;
            height: var(--h);
            position: relative;
            min-width: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: height 0.3s;
        }

        .bar::after {
            content: attr(data-label);
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .y-axis {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 12px;
            color: #333;
            padding: 5px 0;
        }

        /* History list */
        .history-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .history-title {
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .history-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            border-bottom: 1px solid #eee;
        }

        .history-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .history-left {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .history-date {
            text-align: center;
            min-width: 70px;
        }

        .history-date i {
            color: #888;
            margin-bottom: 4px;
        }

        .history-date .date {
            font-size: 12px;
            color: #666;
        }

        .history-emoji {
            font-size: 36px;
        }

        .history-detail h4 {
            font-size: 16px;
            margin-bottom: 4px;
        }

        .history-detail p {
            font-size: 13px;
            color: #666;
            max-width: 300px;
        }

        .history-badge {
            display: inline-block;
            padding: 3px 10px;
            background: #ffd54f;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 6px;
        }

        .history-score {
            text-align: right;
            min-width: 100px;
        }

        .history-score h5 {
            font-size: 13px;
            font-weight: 600;
        }

        .history-score p {
            font-size: 14px;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .summary-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .summary-cards {
                grid-template-columns: 1fr;
            }

            .history-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .history-score {
                text-align: left;
            }

            .nav-links {
                gap: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- ========== TOPBAR ========== -->
    <header class="topbar">
        <a href="../dashboard/index.php" class="brand">
            <img src="../assets/img/logo.png" alt="Logo Symptom Checker">
            <span>Symptom Checker</span>
        </a>

        <nav class="nav-links">
            <a href="../skrining/index.php">Checking</a>
            <a href="../dashboard/index.php">Home</a>
            <button
                class="hamburger"
                type="button"
                aria-label="Menu"
                onclick="openNavDrawer()">
                <span></span>
            </button>
        </nav>
    </header>

    <!-- ========== KONTEN UTAMA ========== -->
    <div class="main-content">
        <div class="breadcrumb">Riwayat</div>

        <div class="page-header">
            <h1>Riwayat Screening</h1>
            <p class="subtitle">Lihat perjalanan kesehatan mentalmu dari waktu ke waktu</p>
        </div>

        <!-- Ringkasan -->
        <div class="summary-cards">
            <div class="summary-card">
                <i class="fa-regular fa-clipboard"></i>
                <div class="label">Total Screening</div>
                <div class="value">4</div>
                <div class="desc">Terakhir 29 April 2026</div>
            </div>

            <div class="summary-card">
                <i class="fa-solid fa-chart-pie"></i>
                <div class="label">Rata-rata Indikasi</div>
                <div class="value">Ringan</div>
                <div class="desc">Dari seluruh screening</div>
            </div>

            <div class="summary-card">
                <i class="fa-solid fa-chart-line"></i>
                <div class="label">Perkembangan</div>
                <div class="value">Membaik</div>
                <div class="desc">Dibanding sebelumnya</div>
            </div>

            <div class="summary-card">
                <i class="fa-regular fa-file-lines"></i>
                <div class="label">Konsisten</div>
                <div class="value">2</div>
                <div class="desc">Bulan beruntun screening</div>
            </div>
        </div>

        <!-- Grafik -->
        <div class="graph-box">
            <div class="graph-title">Perkembangan Tingkat Indikasi</div>
            <div class="graph-canvas">
                <div class="y-axis">
                    <span>Berat</span>
                    <span>Sedang</span>
                    <span>Ringan</span>
                </div>
                <!-- Contoh 4 batang -->
                <div class="bar" style="--h: 40%;" data-label="Jan"></div>
                <div class="bar" style="--h: 65%;" data-label="Feb"></div>
                <div class="bar" style="--h: 50%;" data-label="Mar"></div>
                <div class="bar" style="--h: 35%;" data-label="Apr"></div>
            </div>
        </div>

        <!-- Riwayat List -->
        <div class="history-section">
            <div class="history-title">Riwayat Screening</div>
            <div class="history-list">
                <!-- Item 1 -->
                <div class="history-item">
                    <div class="history-left">
                        <div class="history-date">
                            <i class="fa-regular fa-calendar"></i>
                            <div class="date">25 April 2026</div>
                            <div class="date">14:15 WIB</div>
                        </div>
                        <div class="history-emoji">🙂</div>
                        <div class="history-detail">
                            <h4>Anxiety</h4>
                            <p>Tingkat indikasi sedang, beberapa gejala mulai berkurang dibanding sebelumnya</p>
                            <span class="history-badge">Sedang</span>
                        </div>
                    </div>
                    <div class="history-score">
                        <h5>Skor Indikasi</h5>
                        <p>33% / 100%</p>
                    </div>
                </div>

                <!-- Item 2 (sama untuk contoh) -->
                <div class="history-item">
                    <div class="history-left">
                        <div class="history-date">
                            <i class="fa-regular fa-calendar"></i>
                            <div class="date">18 April 2026</div>
                            <div class="date">10:30 WIB</div>
                        </div>
                        <div class="history-emoji">😐</div>
                        <div class="history-detail">
                            <h4>Stress</h4>
                            <p>Beberapa gejala stres terdeteksi, disarankan untuk relaksasi rutin</p>
                            <span class="history-badge">Ringan</span>
                        </div>
                    </div>
                    <div class="history-score">
                        <h5>Skor Indikasi</h5>
                        <p>21% / 100%</p>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="history-item">
                    <div class="history-left">
                        <div class="history-date">
                            <i class="fa-regular fa-calendar"></i>
                            <div class="date">10 April 2026</div>
                            <div class="date">09:00 WIB</div>
                        </div>
                        <div class="history-emoji">😟</div>
                        <div class="history-detail">
                            <h4>Anxiety</h4>
                            <p>Indikasi cukup tinggi, disarankan konsultasi lebih lanjut</p>
                            <span class="history-badge">Berat</span>
                        </div>
                    </div>
                    <div class="history-score">
                        <h5>Skor Indikasi</h5>
                        <p>68% / 100%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>