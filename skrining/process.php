<?php
// skrining/process.php
session_start();

if (!isset($_SESSION['npm']) || !isset($_SESSION['id_mahasiswa'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

if (!isset($_SESSION['jawaban']) || !is_array($_SESSION['jawaban']) || count($_SESSION['jawaban']) === 0) {
    header('Location: step.php?page=1');
    exit;
}

$id_mahasiswa = (int) $_SESSION['id_mahasiswa'];
$jawaban = $_SESSION['jawaban'];

/*
|--------------------------------------------------------------------------
| Ambil semua variabel aktif dari database
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT id_variabel, subskala
    FROM variabel_skrining
    WHERE status_aktif = 1
    ORDER BY urutan_tampil ASC
");
$variabel = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$variabel) {
    die('Tidak ada data pada tabel variabel_skrining.');
}

/*
|--------------------------------------------------------------------------
| Validasi apakah semua pertanyaan sudah dijawab
|--------------------------------------------------------------------------
*/
if (count($jawaban) < count($variabel)) {
    die('Masih ada pertanyaan yang belum dijawab. Silakan kembali ke skrining.');
}

/*
|--------------------------------------------------------------------------
| Hitung skor per subskala
|--------------------------------------------------------------------------
*/
$skor_depresi = 0;
$skor_anxiety = 0;
$skor_stress  = 0;

foreach ($variabel as $v) {
    $id_variabel = (int) $v['id_variabel'];
    $subskala    = strtolower(trim($v['subskala']));

    if (!isset($jawaban[$id_variabel])) {
        die('Ada pertanyaan yang belum dijawab. Silakan ulangi skrining.');
    }

    $nilai = (int) $jawaban[$id_variabel];

    if ($nilai < 0 || $nilai > 3) {
        die('Nilai jawaban tidak valid.');
    }

    switch ($subskala) {
        case 'depression':
            $skor_depresi += $nilai;
            break;

        case 'anxiety':
            $skor_anxiety += $nilai;
            break;

        case 'stress':
            $skor_stress += $nilai;
            break;

        default:
            die('Subskala tidak dikenal pada variabel_skrining.');
    }
}

$skor_total = $skor_depresi + $skor_anxiety + $skor_stress;

/*
|--------------------------------------------------------------------------
| Fungsi kategori DASS-42
|--------------------------------------------------------------------------
| Catatan:
| - DASS-42 menggunakan skor mentah (raw score).
| - Kategori berikut umum dipakai untuk interpretasi.
|--------------------------------------------------------------------------
*/
function kategoriDepresi(int $skor): string
{
    if ($skor <= 9)  return 'Normal';
    if ($skor <= 13) return 'Ringan';
    if ($skor <= 20) return 'Sedang';
    if ($skor <= 27) return 'Berat';
    return 'Sangat Berat';
}

function kategoriAnxiety(int $skor): string
{
    if ($skor <= 7)  return 'Normal';
    if ($skor <= 9)  return 'Ringan';
    if ($skor <= 14) return 'Sedang';
    if ($skor <= 19) return 'Berat';
    return 'Sangat Berat';
}

function kategoriStress(int $skor): string
{
    if ($skor <= 14) return 'Normal';
    if ($skor <= 18) return 'Ringan';
    if ($skor <= 25) return 'Sedang';
    if ($skor <= 33) return 'Berat';
    return 'Sangat Berat';
}

$kategori_depresi = kategoriDepresi($skor_depresi);
$kategori_anxiety = kategoriAnxiety($skor_anxiety);
$kategori_stress  = kategoriStress($skor_stress);

/*
|--------------------------------------------------------------------------
| Tentukan level risiko keseluruhan dari tabel level_risiko
|--------------------------------------------------------------------------
*/
$stmtLevel = $pdo->prepare("
    SELECT id_level, nama_level
    FROM level_risiko
    WHERE :skor BETWEEN skor_min AND skor_max
    LIMIT 1
");
$stmtLevel->execute([':skor' => $skor_total]);
$level = $stmtLevel->fetch(PDO::FETCH_ASSOC);

if (!$level) {
    // fallback jika data level_risiko belum sesuai
    if ($skor_total <= 20) {
        $level = ['id_level' => 1, 'nama_level' => 'Rendah'];
    } elseif ($skor_total <= 41) {
        $level = ['id_level' => 2, 'nama_level' => 'Sedang'];
    } else {
        $level = ['id_level' => 3, 'nama_level' => 'Tinggi'];
    }
}

/*
|--------------------------------------------------------------------------
| Simpan ke database
|--------------------------------------------------------------------------
*/
$catatan = "Depresi: {$kategori_depresi} | Anxiety: {$kategori_anxiety} | Stress: {$kategori_stress}";

try {
    $pdo->beginTransaction();

    // Simpan ringkasan skrining
    $stmtInsert = $pdo->prepare("
        INSERT INTO skrining
            (id_mahasiswa, tgl_skrining, skor_depresi, skor_anxiety, skor_stress, skor_total, id_level, catatan)
        VALUES
            (:id_mahasiswa, NOW(), :skor_depresi, :skor_anxiety, :skor_stress, :skor_total, :id_level, :catatan)
    ");

    $stmtInsert->execute([
        ':id_mahasiswa' => $id_mahasiswa,
        ':skor_depresi' => $skor_depresi,
        ':skor_anxiety' => $skor_anxiety,
        ':skor_stress'  => $skor_stress,
        ':skor_total'   => $skor_total,
        ':id_level'     => $level['id_level'],
        ':catatan'      => $catatan
    ]);

    $id_skrining = (int) $pdo->lastInsertId();

    // Simpan detail jawaban
    $stmtDetail = $pdo->prepare("
        INSERT INTO detail_skrining (id_skrining, id_variabel, nilai)
        VALUES (:id_skrining, :id_variabel, :nilai)
    ");

    foreach ($jawaban as $id_variabel => $nilai) {
        $id_variabel = (int) $id_variabel;
        $nilai = (int) $nilai;

        $stmtDetail->execute([
            ':id_skrining' => $id_skrining,
            ':id_variabel' => $id_variabel,
            ':nilai'       => $nilai
        ]);
    }

    $pdo->commit();

    // Bersihkan session jawaban
    unset($_SESSION['jawaban']);
    $_SESSION['id_skrining_terakhir'] = $id_skrining;

    header('Location: result.php?id_skrining=' . $id_skrining);
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die('Gagal menyimpan hasil skrining: ' . $e->getMessage());
}