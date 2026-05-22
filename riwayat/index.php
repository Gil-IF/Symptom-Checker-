<?php
// riwayat/index.php
session_start();

if (!isset($_SESSION['npm']) || !isset($_SESSION['id_mahasiswa'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

$page_title = 'Riwayat Skrining - Symptom Checker';
$npm = $_SESSION['npm'];
$id_mahasiswa = (string) $_SESSION['id_mahasiswa'];
$nama_user = $_SESSION['nama_panggilan'] ?? $npm;

/*
|--------------------------------------------------------------------------
| Ambil data riwayat skrining
|--------------------------------------------------------------------------
*/
try {
    $stmt = $pdo->prepare("
        SELECT
            s.id_skrining,
            s.tgl_skrining,
            s.skor_depresi,
            s.skor_anxiety,
            s.skor_stress,
            (s.skor_depresi + s.skor_anxiety + s.skor_stress) AS skor_total
        FROM skrining s
        WHERE s.id_mahasiswa = :id_mahasiswa
        ORDER BY s.tgl_skrining DESC
    ");
    $stmt->execute([':id_mahasiswa' => $id_mahasiswa]);
    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('Query error: ' . $e->getMessage());
}

$total_screening = count($riwayat);
$last_screening = $total_screening > 0
    ? date('d F Y', strtotime($riwayat[0]['tgl_skrining']))
    : '-';

/*
|--------------------------------------------------------------------------
| Fungsi bantu untuk kategori & emoticon
|--------------------------------------------------------------------------
*/
function getKategori(int $skor): array {
    if ($skor <= 9)  return ['ringan', 'Ringan'];
    if ($skor <= 18) return ['sedang', 'Sedang'];
    return ['berat', 'Berat'];
}

function getWarnaKategori(string $level): string {
    return match ($level) {
        'ringan' => '#a8e6cf',  // hijau muda
        'sedang' => '#ffd54f',  // kuning
        'berat'  => '#f48fb1',  // pink/merah muda
        default  => '#e0e0e0',
    };
}

function getEmoji(int $skor_depresi, int $skor_anxiety, int $skor_stress): array {
    $max_level = 'ringan';
    $levels = [
        getKategori($skor_depresi)[0],
        getKategori($skor_anxiety)[0],
        getKategori($skor_stress)[0],
    ];
    if (in_array('berat', $levels)) $max_level = 'berat';
    elseif (in_array('sedang', $levels)) $max_level = 'sedang';

    return match ($max_level) {
        'berat'  => ['😟', 'Perlu perhatian'],
        'sedang' => ['😐', 'Cukup stabil'],
        default  => ['🙂', 'Kondisi baik'],
    };
}

function getPersentaseSubskala(int $skor): int {
    // Maksimal per subskala DASS-42 adalah 42 (14 item x 3)
    return (int) round(($skor / 42) * 100);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg: #f5f5f5;
            --white: #ffffff;
            --text: #2f3137;
            --shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            --accent: #cbb2e1;
            --card-blue: #6ea3d9;
            --radius: 20px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; }
        a { text-decoration: none; color: inherit; }

        /* TOPBAR */
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
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 18px; }
        .brand img { width: 34px; height: 34px; object-fit: contain; }
        .nav-links { display: flex; align-items: center; gap: 28px; font-size: 16px; font-weight: 600; }
        .nav-links a { position: relative; padding-bottom: 4px; }
        .nav-links a.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 4px;
            border-radius: 999px;
            background: #b8e0d1;
        }
        .hamburger {
            width: 48px; height: 48px; border: none; border-radius: 16px;
            background: var(--accent); display: grid; place-items: center; cursor: pointer;
        }
        .hamburger span, .hamburger span::before, .hamburger span::after {
            content: ''; display: block; width: 22px; height: 3px; border-radius: 999px;
            background: #37343f; position: relative;
        }
        .hamburger span::before { position: absolute; top: -7px; left: 0; }
        .hamburger span::after  { position: absolute; top: 7px; left: 0; }

        /* MAIN */
        .main-content {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 16px 20px 32px;
            flex: 1;
        }
        .breadcrumb { color: #8e8e98; font-size: 14px; margin-bottom: 16px; font-weight: 500; }
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .page-header .subtitle { font-size: 14px; color: #666; }

        /* SUMMARY CARDS */
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
        }
        .summary-card .label { font-size: 12px; opacity: 0.9; margin-bottom: 8px; }
        .summary-card .value { font-size: 28px; font-weight: 700; }
        .summary-card .desc { font-size: 11px; opacity: 0.85; margin-top: 6px; }

        /* HISTORY SECTION */
        .history-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
        }
        .history-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .history-list { display: flex; flex-direction: column; gap: 16px; }

        /* HISTORY ITEM */
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            background: #f9f9f9;
            border-radius: 16px;
            border: 1px solid #eee;
            transition: all 0.25s ease;
        }
        .history-item:hover {
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
            transform: translateY(-2px);
            border-color: #d0d0d0;
        }

        /* BAGIAN KIRI: EMOTICON + BADGE SKOR */
        .history-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .emoji-wrapper {
            position: relative;
            cursor: default;
        }
        .emoji {
            font-size: 42px;
            line-height: 1;
            transition: transform 0.2s;
        }
        .history-item:hover .emoji {
            transform: scale(1.1);
        }
        .emoji-tooltip {
            position: absolute;
            bottom: -24px;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }
        .emoji-wrapper:hover .emoji-tooltip {
            opacity: 1;
        }

        .history-badge {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .tanggal {
            font-size: 12px;
            color: #888;
            margin-bottom: 2px;
        }
        .skor-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
        }
        .skor-item .label { color: #555; min-width: 85px; }
        .skor-item .nilai { font-weight: 700; color: #1c1f26; min-width: 30px; }

        /* PROGRESS BAR MINI */
        .mini-progress {
            width: 80px;
            height: 6px;
            background: #e6e6e6;
            border-radius: 999px;
            overflow: hidden;
        }
        .mini-progress-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        .total-skor {
            margin-top: 6px;
            font-size: 15px;
            font-weight: 700;
            color: #6ea3d9;
        }

        /* TOMBOL DETAIL (kanan) */
        .btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            background: #a9e2d3;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            color: #2f3137;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: all 0.25s ease;
        }
        .btn-detail:hover {
            background: #8fd3c2;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }

        .empty-state { text-align: center; color: #666; padding: 40px 20px; }

        @media (max-width: 1024px) { .summary-cards { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) {
            .summary-cards { grid-template-columns: 1fr; }
            .history-item { flex-direction: column; align-items: flex-start; gap: 14px; }
            .btn-detail { align-self: flex-end; }
            .history-left { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <a href="../dashboard/index.php" class="brand">
        <img src="../assets/img/logo.png" alt="Logo Symptom Checker">
        <span>Symptom Checker</span>
    </a>
    <nav class="nav-links">
        <a href="../skrining/index.php">Skrining</a>
        <a href="../dashboard/index.php">Home</a>
        <button class="hamburger" type="button" aria-label="Menu" onclick="openNavDrawer()">
            <span></span>
        </button>
    </nav>
</header>

<?php include '../dashboard/navigasi.php'; ?>

<div class="main-content">
    <div class="breadcrumb">Riwayat</div>
    <div class="page-header">
        <h1>Riwayat Skrining</h1>
        <p class="subtitle">Lihat perjalanan kesehatan mentalmu dari waktu ke waktu</p>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="label">Total Skrining</div>
            <div class="value"><?= $total_screening; ?></div>
            <div class="desc">Terakhir <?= htmlspecialchars($last_screening); ?></div>
        </div>
        <div class="summary-card">
            <div class="label">User</div>
            <div class="value"><?= htmlspecialchars($npm); ?></div>
            <div class="desc">NPM</div>
        </div>
        <div class="summary-card">
            <div class="label">Instrumen</div>
            <div class="value">DASS-42</div>
            <div class="desc">42 pertanyaan</div>
        </div>
        <div class="summary-card">
            <div class="label">Skala</div>
            <div class="value">3</div>
            <div class="desc">Depresi, Anxiety, Stress</div>
        </div>
    </div>

    <div class="history-section">
        <div class="history-title">Riwayat Skrining</div>

        <?php if (empty($riwayat)): ?>
            <div class="empty-state">
                <i class="fa-regular fa-folder-open" style="font-size: 48px; color: #ccc; margin-bottom: 12px; display: block;"></i>
                Belum ada riwayat skrining.<br>
                <small>Silakan lakukan skrining terlebih dahulu.</small>
            </div>
        <?php else: ?>
            <div class="history-list">
                <?php foreach ($riwayat as $item): ?>
                    <?php
                        $skor_depresi  = (int)($item['skor_depresi'] ?? 0);
                        $skor_anxiety  = (int)($item['skor_anxiety'] ?? 0);
                        $skor_stress   = (int)($item['skor_stress'] ?? 0);
                        $skor_total    = (int)($item['skor_total'] ?? 0);
                        $tgl_formatted = date('d M Y', strtotime($item['tgl_skrining']));

                        // Ambil kategori & warna
                        $kat_depresi  = getKategori($skor_depresi);
                        $kat_anxiety  = getKategori($skor_anxiety);
                        $kat_stress   = getKategori($skor_stress);
                        $warna_depresi = getWarnaKategori($kat_depresi[0]);
                        $warna_anxiety = getWarnaKategori($kat_anxiety[0]);
                        $warna_stress  = getWarnaKategori($kat_stress[0]);

                        // Emoticon
                        [$emoji_icon, $emoji_text] = getEmoji($skor_depresi, $skor_anxiety, $skor_stress);

                        // Persentase untuk progress bar
                        $p_depresi = getPersentaseSubskala($skor_depresi);
                        $p_anxiety = getPersentaseSubskala($skor_anxiety);
                        $p_stress  = getPersentaseSubskala($skor_stress);
                    ?>
                    <div class="history-item">
                        <div class="history-left">
                            <!-- EMOTICON + TOOLTIP -->
                            <div class="emoji-wrapper">
                                <span class="emoji" title="<?= htmlspecialchars($emoji_text); ?>"><?= $emoji_icon; ?></span>
                                <span class="emoji-tooltip"><?= htmlspecialchars($emoji_text); ?></span>
                            </div>

                            <!-- BADGE SKOR -->
                            <div class="history-badge">
                                <div class="tanggal"><?= $tgl_formatted; ?></div>

                                <div class="skor-item">
                                    <span class="label">Depresi :</span>
                                    <span class="nilai"><?= $skor_depresi; ?></span>
                                    <div class="mini-progress">
                                        <div class="mini-progress-fill" style="width: <?= $p_depresi; ?>%; background: <?= $warna_depresi; ?>;"></div>
                                    </div>
                                    <span style="font-size: 11px; color: #666;">(<?= $kat_depresi[1]; ?>)</span>
                                </div>

                                <div class="skor-item">
                                    <span class="label">Kecemasan :</span>
                                    <span class="nilai"><?= $skor_anxiety; ?></span>
                                    <div class="mini-progress">
                                        <div class="mini-progress-fill" style="width: <?= $p_anxiety; ?>%; background: <?= $warna_anxiety; ?>;"></div>
                                    </div>
                                    <span style="font-size: 11px; color: #666;">(<?= $kat_anxiety[1]; ?>)</span>
                                </div>

                                <div class="skor-item">
                                    <span class="label">Stress :</span>
                                    <span class="nilai"><?= $skor_stress; ?></span>
                                    <div class="mini-progress">
                                        <div class="mini-progress-fill" style="width: <?= $p_stress; ?>%; background: <?= $warna_stress; ?>;"></div>
                                    </div>
                                    <span style="font-size: 11px; color: #666;">(<?= $kat_stress[1]; ?>)</span>
                                </div>

                                <div class="total-skor">
                                    Total Skor: <?= $skor_total; ?>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL DETAIL -->
                        <a href="../skrining/result.php?id_skrining=<?= $item['id_skrining']; ?>" class="btn-detail">
                            <i class="fa-regular fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>