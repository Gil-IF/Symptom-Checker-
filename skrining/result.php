<?php
// skrining/result.php
session_start();
require_once '../config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['npm']) || !isset($_SESSION['id_mahasiswa'])) {
    header('Location: ../login.php');
    exit;
}

$id_skrining = $_GET['id_skrining'] ?? '';

// Validasi format ID
if (!preg_match('/^IDS\d{3}$/', $id_skrining)) {
    die('ID skrining tidak valid.');
}

// Ambil data header skrining + data mahasiswa
$stmt = $pdo->prepare("
    SELECT
        s.id_skrining,
        s.id_mahasiswa,
        s.tgl_skrining,
        s.skor_depresi,
        s.skor_anxiety,
        s.skor_stress,
        m.npm,
        m.nama
    FROM skrining s
    JOIN mahasiswa m ON m.id_mahasiswa = s.id_mahasiswa
    WHERE s.id_skrining = :id_skrining
");
$stmt->execute([':id_skrining' => $id_skrining]);
$hasil = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hasil) {
    die('Data hasil skrining tidak ditemukan.');
}

// ---------------------------------------------------------------------------
// Otorisasi: hanya mahasiswa pemilik data atau admin yang boleh melihat
// ---------------------------------------------------------------------------
$isAdmin   = ($_SESSION['role'] ?? '') === 'admin';
$isPemilik = ($_SESSION['id_mahasiswa'] === $hasil['id_mahasiswa']);
if (!$isAdmin && !$isPemilik) {
    header('Location: ../dashboard/index.php');
    exit;
}

// ---------------------------------------------------------------------------
// Hitung skor total
// ---------------------------------------------------------------------------
$skorTotal = $hasil['skor_depresi'] + $hasil['skor_anxiety'] + $hasil['skor_stress'];

// ---------------------------------------------------------------------------
// Tentukan kategori untuk masing-masing subskala dari tabel kategori_subskala
// ---------------------------------------------------------------------------
function getKategori(PDO $pdo, string $idSubskala, int $skor): string {
    $stmt = $pdo->prepare("
        SELECT nama_kategori
        FROM kategori_subskala
        WHERE id_subskala = :id_subskala
          AND :skor BETWEEN rentang_min AND rentang_max
    ");
    $stmt->execute([':id_subskala' => $idSubskala, ':skor' => $skor]);
    $result = $stmt->fetchColumn();
    return $result ?: 'Tidak diketahui';
}

$kategoriDepresi = getKategori($pdo, 'IDU001', $hasil['skor_depresi']);
$kategoriAnxiety = getKategori($pdo, 'IDU002', $hasil['skor_anxiety']);
$kategoriStress  = getKategori($pdo, 'IDU003', $hasil['skor_stress']);

// ---------------------------------------------------------------------------
// Ambil rekomendasi berdasarkan kategori
// ---------------------------------------------------------------------------
$rekomendasi = [];
try {
    $stmtRekom = $pdo->prepare("
        SELECT r.teks_rekomendasi
        FROM rekomendasi r
        JOIN kategori_subskala k ON r.id_kategori = k.id_kategori
        WHERE (k.nama_kategori = :depresi AND k.id_subskala = 'IDU001')
           OR (k.nama_kategori = :anxiety AND k.id_subskala = 'IDU002')
           OR (k.nama_kategori = :stress  AND k.id_subskala = 'IDU003')
        ORDER BY r.urutan ASC
    ");
    $stmtRekom->execute([
        ':depresi' => $kategoriDepresi,
        ':anxiety' => $kategoriAnxiety,
        ':stress'  => $kategoriStress,
    ]);
    $rekomendasi = $stmtRekom->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {
    $rekomendasi = [];
}

// Fungsi untuk memilih emoji
function getEmoji(string $kategori): string {
    $k = strtolower($kategori);
    if (str_contains($k, 'normal')) return '🙂';
    if (str_contains($k, 'ringan')) return '😐';
    if (str_contains($k, 'sedang')) return '😐';
    if (str_contains($k, 'berat'))  return '😟';
    if (str_contains($k, 'sangat berat')) return '😟';
    return '😟';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Skrining DASS-42</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
            margin: 0; padding: 20px;
            color: #2f3137;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        h1 { text-align: center; }
        .info { text-align: center; color: #666; margin-bottom: 20px; }
        .skor-box {
            background: #6ea3d9;
            color: white;
            padding: 15px 25px;
            border-radius: 16px;
            display: inline-block;
            margin: 0 auto 20px;
            font-size: 24px;
            font-weight: bold;
        }
        .kategori-container {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .kategori {
            text-align: center;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 12px;
            flex: 1;
            margin: 0 10px;
        }
        .kategori .emoji { font-size: 36px; }
        .kategori .nama { font-weight: bold; margin-top: 8px; }
        .rekomendasi {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .rekomendasi h3 { margin-top: 0; }
        .rekomendasi ul { padding-left: 20px; }
        .btn {
            display: inline-block;
            background: #a9e2d3;
            color: #27313a;
            padding: 10px 20px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Hasil Skrining DASS-42</h1>
    <div class="info">
        <p><strong>Nama:</strong> <?= htmlspecialchars($hasil['nama']); ?> (<?= htmlspecialchars($hasil['npm']); ?>)</p>
        <p><strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($hasil['tgl_skrining'])); ?> WIB</p>
    </div>

    <div style="text-align: center;">
        <div class="skor-box">Skor Total: <?= $skorTotal; ?></div>
    </div>

    <div class="kategori-container">
        <div class="kategori">
            <div class="emoji"><?= getEmoji($kategoriDepresi); ?></div>
            <div class="nama">Depresi</div>
            <div><?= htmlspecialchars($kategoriDepresi); ?></div>
        </div>
        <div class="kategori">
            <div class="emoji"><?= getEmoji($kategoriAnxiety); ?></div>
            <div class="nama">Kecemasan</div>
            <div><?= htmlspecialchars($kategoriAnxiety); ?></div>
        </div>
        <div class="kategori">
            <div class="emoji"><?= getEmoji($kategoriStress); ?></div>
            <div class="nama">Stres</div>
            <div><?= htmlspecialchars($kategoriStress); ?></div>
        </div>
    </div>

    <?php if (!empty($rekomendasi)): ?>
    <div class="rekomendasi">
        <h3>Rekomendasi untuk Anda</h3>
        <ul>
            <?php foreach ($rekomendasi as $r): ?>
                <li><?= htmlspecialchars($r); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="../dashboard/index.php" class="btn">Kembali ke Dashboard</a>
        <a href="../riwayat/index.php" class="btn">Lihat Riwayat</a>
    </div>
</div>
</body>
</html>