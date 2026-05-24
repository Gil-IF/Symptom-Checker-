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
    <link rel="stylesheet" href="../assets/css/riwayat.css">

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