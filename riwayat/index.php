<?php
// riwayat/index.php
session_start();

if (!isset($_SESSION['npm']) || !isset($_SESSION['id_mahasiswa'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

$page_title = 'Riwayat Screening - Symptom Checker';
$npm = $_SESSION['npm'];
$id_mahasiswa = (string) $_SESSION['id_mahasiswa'];
$nama_user = $_SESSION['nama_panggilan'] ?? $npm;

/*
|--------------------------------------------------------------------------
| Ambil data riwayat skrining
|--------------------------------------------------------------------------
*/
try {
    // Query dengan join kategori_subskala
    $stmt = $pdo->prepare("
        SELECT
            s.id_skrining,
            s.tgl_skrining,
            s.skor_depresi,
            s.skor_anxiety,
            s.skor_stress,
            (s.skor_depresi + s.skor_anxiety + s.skor_stress) AS skor_total,
            kd.nama_kategori AS kategori_depresi,
            ka.nama_kategori AS kategori_anxiety,
            ks.nama_kategori AS kategori_stress
        FROM skrining s
        JOIN mahasiswa m ON m.id_mahasiswa = s.id_mahasiswa
        LEFT JOIN kategori_subskala kd
            ON kd.id_subskala = 'IDU001'
            AND s.skor_depresi BETWEEN kd.rentang_min AND kd.rentang_max
        LEFT JOIN kategori_subskala ka
            ON ka.id_subskala = 'IDU002'
            AND s.skor_anxiety BETWEEN ka.rentang_min AND ka.rentang_max
        LEFT JOIN kategori_subskala ks
            ON ks.id_subskala = 'IDU003'
            AND s.skor_stress BETWEEN ks.rentang_min AND ks.rentang_max
        WHERE s.id_mahasiswa = :id_mahasiswa
        ORDER BY s.tgl_skrining DESC
    ");
    $stmt->execute([':id_mahasiswa' => $id_mahasiswa]);
    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('Query error: ' . $e->getMessage());
}

// Jika masih kosong, coba query tanpa join untuk memastikan data ada
if (empty($riwayat)) {
    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM skrining WHERE id_mahasiswa = ?");
    $stmt2->execute([$id_mahasiswa]);
    $count = $stmt2->fetchColumn();
    if ($count > 0) {
        // Data ada tapi join gagal – fallback: ambil tanpa kategori
        $stmt = $pdo->prepare("
            SELECT id_skrining, tgl_skrining, skor_depresi, skor_anxiety, skor_stress,
                   (skor_depresi + skor_anxiety + skor_stress) AS skor_total
            FROM skrining
            WHERE id_mahasiswa = :id_mahasiswa
            ORDER BY tgl_skrining DESC
        ");
        $stmt->execute([':id_mahasiswa' => $id_mahasiswa]);
        $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Karena kategori tidak tersedia, kita isi dengan 'Tidak diketahui'
        foreach ($riwayat as &$item) {
            $item['kategori_depresi'] = 'Tidak diketahui';
            $item['kategori_anxiety'] = 'Tidak diketahui';
            $item['kategori_stress'] = 'Tidak diketahui';
        }
    }
}

/*
|--------------------------------------------------------------------------
| Ringkasan data
|--------------------------------------------------------------------------
*/
$total_screening = count($riwayat);
$last_screening = $total_screening > 0
    ? date('d F Y', strtotime($riwayat[0]['tgl_skrining']))
    : '-';

if ($total_screening > 0) {
    $last = $riwayat[0];
    $rata_level = ($last['kategori_depresi'] ?? '?') . ', ' . ($last['kategori_anxiety'] ?? '?') . ', ' . ($last['kategori_stress'] ?? '?');
} else {
    $rata_level = '-';
}

/*
|--------------------------------------------------------------------------
| Fungsi bantu
|--------------------------------------------------------------------------
*/
function getEmojiForCategories(string $depresi, string $anxiety, string $stress): string
{
    $levels = [$depresi, $anxiety, $stress];
    $hasBerat = false;
    $hasSedang = false;
    foreach ($levels as $l) {
        $l = strtolower($l);
        if (str_contains($l, 'berat')) $hasBerat = true;
        elseif (str_contains($l, 'sedang') || str_contains($l, 'ringan')) $hasSedang = true;
    }
    if ($hasBerat) return '😟';
    if ($hasSedang) return '😐';
    return '🙂';
}

function getPersentase(int $skor_total): int
{
    return (int) round(($skor_total / 126) * 100);
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
        /* CSS sama persis seperti sebelumnya */
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
        body { font-family: 'Poppins', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
        a { text-decoration: none; color: inherit; }
        .topbar { background: var(--white); min-height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; box-shadow: var(--shadow); position: sticky; top: 0; z-index: 100; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 18px; }
        .brand img { width: 34px; height: 34px; object-fit: contain; }
        .nav-links { display: flex; align-items: center; gap: 28px; font-size: 16px; font-weight: 600; }
        .nav-links a.active::after { content: ''; display: block; height: 4px; border-radius: 999px; background: #b8e0d1; margin-top: 4px; }
        .hamburger { width: 48px; height: 48px; border: none; border-radius: 16px; background: var(--accent); display: grid; place-items: center; cursor: pointer; }
        .hamburger span, .hamburger span::before, .hamburger span::after { content: ''; display: block; width: 22px; height: 3px; border-radius: 999px; background: #37343f; position: relative; }
        .hamburger span::before { position: absolute; top: -7px; left: 0; }
        .hamburger span::after  { position: absolute; top: 7px; left: 0; }
        .main-content { max-width: 1200px; margin: 0 auto; padding: 16px 20px 32px; }
        .breadcrumb { color: #8e8e98; font-size: 14px; margin-bottom: 16px; }
        .page-header h1 { font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .page-header .subtitle { font-size: 14px; color: #666; }
        .summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin: 24px 0 32px; }
        .summary-card { background: var(--card-blue); color: #fff; padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow); }
        .summary-card .label { font-size: 12px; opacity: 0.9; margin-bottom: 8px; }
        .summary-card .value { font-size: 28px; font-weight: 700; }
        .summary-card .desc { font-size: 11px; opacity: 0.85; margin-top: 6px; }
        .history-section { background: #fff; border-radius: 20px; padding: 24px; box-shadow: var(--shadow); }
        .history-title { font-size: 20px; font-weight: 700; margin-bottom: 20px; }
        .history-list { display: flex; flex-direction: column; gap: 18px; }
        .history-item { display: flex; justify-content: space-between; align-items: center; padding-bottom: 18px; border-bottom: 1px solid #eee; }
        .history-item:last-child { border-bottom: none; padding-bottom: 0; }
        .history-left { display: flex; align-items: center; gap: 16px; }
        .history-date { min-width: 120px; font-size: 12px; color: #666; }
        .history-emoji { font-size: 36px; }
        .history-detail h4 { font-size: 16px; margin-bottom: 4px; }
        .history-detail p { font-size: 13px; color: #666; max-width: 380px; }
        .history-badge { display: inline-block; margin-top: 6px; padding: 4px 10px; border-radius: 999px; background: #ffe082; font-size: 11px; font-weight: 600; }
        .history-score { text-align: right; }
        .history-score p { font-weight: 600; margin-top: 4px; }
        .history-link { display: inline-block; margin-top: 8px; font-size: 12px; color: #6ea3d9; font-weight: 600; }
        .empty-state { text-align: center; color: #666; padding: 40px 20px; }
        @media (max-width: 1024px) { .summary-cards { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .summary-cards { grid-template-columns: 1fr; } .history-item { flex-direction: column; align-items: flex-start; gap: 12px; } .history-score { text-align: left; } .history-left { flex-direction: column; align-items: flex-start; } }
    </style>
</head>
<body>

<header class="topbar">
    <a href="../dashboard/index.php" class="brand">
        <img src="../assets/img/logo.png" alt="Logo Symptom Checker">
        <span>Symptom Checker</span>
    </a>
    <nav class="nav-links">
        <a href="../skrining/index.php">Checking</a>
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
        <h1>Riwayat Screening</h1>
        <p class="subtitle">Lihat perjalanan kesehatan mentalmu dari waktu ke waktu</p>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="label">Total Screening</div>
            <div class="value"><?= $total_screening; ?></div>
            <div class="desc">Terakhir <?= htmlspecialchars($last_screening); ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Kategori Terakhir</div>
            <div class="value"><?= $total_screening > 0 ? 'Lihat' : '-'; ?></div>
            <div class="desc"><?= htmlspecialchars($rata_level); ?></div>
        </div>
        <div class="summary-card">
            <div class="label">User</div>
            <div class="value"><?= htmlspecialchars($npm); ?></div>
            <div class="desc">NPM mahasiswa</div>
        </div>
        <div class="summary-card">
            <div class="label">Instrumen</div>
            <div class="value">DASS-42</div>
            <div class="desc">42 item pertanyaan</div>
        </div>
    </div>

    <div class="history-section">
        <div class="history-title">Riwayat Screening</div>

        <?php if (empty($riwayat)): ?>
            <div class="empty-state">
                Belum ada riwayat screening. <br>
                <small>Pastikan Anda sudah menyelesaikan kuesioner.</small>
            </div>
        <?php else: ?>
            <div class="history-list">
                <?php foreach ($riwayat as $item): ?>
                    <?php
                        $kategori_depresi = $item['kategori_depresi'] ?? 'Tidak diketahui';
                        $kategori_anxiety = $item['kategori_anxiety'] ?? 'Tidak diketahui';
                        $kategori_stress = $item['kategori_stress'] ?? 'Tidak diketahui';
                        $skor_total = (int)($item['skor_total'] ?? 0);
                        $persen = getPersentase($skor_total);
                        $emoji = getEmojiForCategories($kategori_depresi, $kategori_anxiety, $kategori_stress);
                        $kategori_gabung = $kategori_depresi . ' / ' . $kategori_anxiety . ' / ' . $kategori_stress;
                    ?>
                    <div class="history-item">
                        <div class="history-left">
                            <div class="history-date">
                                <div><?= date('d M Y', strtotime($item['tgl_skrining'])); ?></div>
                                <div><?= date('H:i', strtotime($item['tgl_skrining'])); ?> WIB</div>
                            </div>
                            <div class="history-emoji"><?= $emoji; ?></div>
                            <div class="history-detail">
                                <h4>DASS-42 Screening</h4>
                                <p><?= htmlspecialchars($kategori_gabung); ?></p>
                                <span class="history-badge">
                                    <?= htmlspecialchars($kategori_gabung); ?>
                                </span>
                                <br>
                                <a href="../skrining/result.php?id_skrining=<?= $item['id_skrining']; ?>" class="history-link">
                                    Lihat Detail Hasil →
                                </a>
                            </div>
                        </div>
                        <div class="history-score">
                            <h5>Skor Total</h5>
                            <p><?= $skor_total; ?> (<?= $persen; ?>%)</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>