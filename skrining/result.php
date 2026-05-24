<?php
// skrining/result.php
session_start();
require_once '../config/database.php';

/*
|--------------------------------------------------------------------------
| 1. Autentikasi & Otorisasi
|--------------------------------------------------------------------------
*/
$isMahasiswa = isset($_SESSION['npm']) && isset($_SESSION['id_mahasiswa']);
$isAdmin     = isset($_SESSION['admin']) || isset($_SESSION['id_admin']);

if (!$isMahasiswa && !$isAdmin) {
    header('Location: ../login.php');
    exit;
}

// Ambil & validasi format ID skrining (contoh: IDS001)
$id_skrining = $_GET['id_skrining'] ?? '';
if (!preg_match('/^IDS\d{3}$/', $id_skrining)) {
    die('ID skrining tidak valid.');
}

/*
|--------------------------------------------------------------------------
| 2. Ambil data hasil skrining + data mahasiswa
|--------------------------------------------------------------------------
*/
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
    WHERE s.id_skrining = :id
");
$stmt->execute([':id' => $id_skrining]);
$hasil = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hasil) {
    die('Data hasil skrining tidak ditemukan.');
}

/*
|--------------------------------------------------------------------------
| 3. Otorisasi kepemilikan data
|--------------------------------------------------------------------------
*/
// Mahasiswa hanya boleh melihat datanya sendiri
if ($isMahasiswa && $hasil['id_mahasiswa'] != $_SESSION['id_mahasiswa']) {
    // Jika bukan admin dan bukan pemilik, akses ditolak
    die('Akses ditolak.');
}
// Admin boleh melihat data siapa pun

/*
|--------------------------------------------------------------------------
| 4. Hitung skor total
|--------------------------------------------------------------------------
*/
$skorTotal = $hasil['skor_depresi'] + $hasil['skor_anxiety'] + $hasil['skor_stress'];

/*
|--------------------------------------------------------------------------
| 5. Fungsi bantu: ambil kategori dari tabel kategori_subskala
|--------------------------------------------------------------------------
*/
function getKategori(PDO $pdo, string $idSubskala, int $skor): string {
    $stmt = $pdo->prepare("
        SELECT nama_kategori
        FROM kategori_subskala
        WHERE id_subskala = :id_subskala
          AND :skor BETWEEN rentang_min AND rentang_max
    ");
    $stmt->execute([':id_subskala' => $idSubskala, ':skor' => $skor]);
    return $stmt->fetchColumn() ?: 'Tidak diketahui';
}

$kategoriDepresi = getKategori($pdo, 'IDU001', $hasil['skor_depresi']);
$kategoriAnxiety = getKategori($pdo, 'IDU002', $hasil['skor_anxiety']);
$kategoriStress  = getKategori($pdo, 'IDU003', $hasil['skor_stress']);

/*
|--------------------------------------------------------------------------
| 6. Ambil rekomendasi berdasarkan kategori
|--------------------------------------------------------------------------
*/
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
    // Biarkan $rekomendasi tetap array kosong
}

/*
|--------------------------------------------------------------------------
| 7. Fungsi bantu: emoji berdasarkan kategori
|--------------------------------------------------------------------------
*/
function getEmoji(string $kategori): string {
    return match (true) {
        str_contains($kategori, 'Normal')       => '🙂',
        str_contains($kategori, 'Ringan')       => '😐',
        str_contains($kategori, 'Sedang')       => '😐',
        str_contains($kategori, 'Berat')        => '😟',
        str_contains($kategori, 'Sangat Berat') => '😟',
        default                                 => '?',
    };
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
    <link rel="stylesheet" href="../assets/css/result.css">

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
                <div class="warning">
                    <strong>Perhatian:</strong> Hasil skrining ini hanyalah indikasi awal dan tidak menggantikan peran ahli. Jika hasil skrining Anda menunjukkan tingkat berat hingga sangat berat, kami sangat menyarankan Anda untuk segera berkonsultasi dengan tenaga profesional.
                </div>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="../dashboard/index.php" class="btn">Kembali ke Dashboard</a>
        <a href="../riwayat/index.php" class="btn">Lihat Riwayat</a>
    </div>
</div>
</body>
</html>