<?php
// skrining/result.php
session_start();

if (!isset($_SESSION['npm']) || !isset($_SESSION['id_mahasiswa'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

$id_skrining = isset($_GET['id_skrining'])
    ? (int) $_GET['id_skrining']
    : (int) ($_SESSION['id_skrining_terakhir'] ?? 0);

if ($id_skrining <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        s.*,
        m.npm,
        lr.nama_level,
        lr.skor_min,
        lr.skor_max
    FROM skrining s
    JOIN mahasiswa m ON s.id_mahasiswa = m.id_mahasiswa
    JOIN level_risiko lr ON s.id_level = lr.id_level
    WHERE s.id_skrining = :id_skrining
      AND s.id_mahasiswa = :id_mahasiswa
    LIMIT 1
");
$stmt->execute([
    ':id_skrining' => $id_skrining,
    ':id_mahasiswa' => (int) $_SESSION['id_mahasiswa']
]);

$data = $stmt->fetch();

if (!$data) {
    die('Data hasil skrining tidak ditemukan.');
}

function kategoriDepresi(int $skor): string
{
    if ($skor <= 9) return 'Normal';
    if ($skor <= 13) return 'Mild';
    if ($skor <= 20) return 'Moderate';
    if ($skor <= 27) return 'Severe';
    return 'Extremely Severe';
}

function kategoriAnxiety(int $skor): string
{
    if ($skor <= 7) return 'Normal';
    if ($skor <= 9) return 'Mild';
    if ($skor <= 14) return 'Moderate';
    if ($skor <= 19) return 'Severe';
    return 'Extremely Severe';
}

function kategoriStress(int $skor): string
{
    if ($skor <= 14) return 'Normal';
    if ($skor <= 18) return 'Mild';
    if ($skor <= 25) return 'Moderate';
    if ($skor <= 33) return 'Severe';
    return 'Extremely Severe';
}

$stmtRec = $pdo->prepare("
    SELECT teks_rekomendasi
    FROM rekomendasi
    WHERE id_level = :id_level AND status_aktif = 1
    ORDER BY urutan_rekomendasi ASC
");
$stmtRec->execute([':id_level' => (int) $data['id_level']]);
$rekomendasi = $stmtRec->fetchAll();

$catDepresi = kategoriDepresi((int)$data['skor_depresi']);
$catAnxiety = kategoriAnxiety((int)$data['skor_anxiety']);
$catStress  = kategoriStress((int)$data['skor_stress']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Skrining</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins',sans-serif;
            background:#f4f4f6;
            color:#2f3137;
        }
        .container {
            max-width:1100px;
            margin:0 auto;
            padding:20px;
        }
        .card {
            background:#fff;
            border-radius:24px;
            box-shadow:0 12px 24px rgba(0,0,0,.12);
            padding:32px;
        }
        h1 {
            margin-bottom:18px;
            font-size:32px;
        }
        .summary {
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:16px;
            margin:20px 0 28px;
        }
        .box {
            background:#f9f9f9;
            border:2px solid #e6e6e6;
            border-radius:16px;
            padding:18px;
        }
        .box strong {
            display:block;
            margin-bottom:8px;
        }
        .level {
            background:#a9e2d3;
            padding:16px 20px;
            border-radius:16px;
            font-weight:700;
            margin-bottom:24px;
        }
        ul {
            margin-left:20px;
            line-height:1.8;
        }
        .btn {
            display:inline-block;
            margin-top:24px;
            background:#6ea3d9;
            color:#fff;
            text-decoration:none;
            padding:12px 22px;
            border-radius:999px;
            font-weight:700;
        }
        @media (max-width:768px) {
            .summary { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Hasil Skrining</h1>

        <div class="level">
            Tingkat Risiko Keseluruhan: <?= htmlspecialchars($data['nama_level']); ?>
            (Skor Total: <?= (int)$data['skor_total']; ?>)
        </div>

        <div class="summary">
            <div class="box">
                <strong>Depresi</strong>
                Skor: <?= (int)$data['skor_depresi']; ?><br>
                Kategori: <?= htmlspecialchars($catDepresi); ?>
            </div>

            <div class="box">
                <strong>Anxiety</strong>
                Skor: <?= (int)$data['skor_anxiety']; ?><br>
                Kategori: <?= htmlspecialchars($catAnxiety); ?>
            </div>

            <div class="box">
                <strong>Stress</strong>
                Skor: <?= (int)$data['skor_stress']; ?><br>
                Kategori: <?= htmlspecialchars($catStress); ?>
            </div>

            <div class="box">
                <strong>NPM</strong>
                <?= htmlspecialchars($data['npm']); ?><br>
                Tanggal: <?= htmlspecialchars($data['tgl_skrining']); ?>
            </div>
        </div>

        <div class="box">
            <strong>Catatan Hasil</strong>
            <?= htmlspecialchars($data['catatan']); ?>
        </div>

        <div class="box" style="margin-top:20px;">
            <strong>Rekomendasi</strong>
            <ul>
                <?php foreach ($rekomendasi as $rec): ?>
                    <li><?= htmlspecialchars($rec['teks_rekomendasi']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <a href="../dashboard/index.php" class="btn">Kembali ke Dashboard</a>
    </div>
</div>
</body>
</html>