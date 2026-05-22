<?php
/**
 * skrining/process.php
 * Menyimpan hasil skrining DASS-42 ke database.
 */

session_start();

// ---------------------------------------------------------------------------
// 1. Autentikasi & Otorisasi
// ---------------------------------------------------------------------------
// Pastikan pengguna sudah login dan memiliki session npm serta id_mahasiswa
if (!isset($_SESSION['npm'], $_SESSION['id_mahasiswa'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

// ---------------------------------------------------------------------------
// 2. Validasi jawaban yang ada di session
// ---------------------------------------------------------------------------
if (
    !isset($_SESSION['jawaban'])
    || !is_array($_SESSION['jawaban'])
    || empty($_SESSION['jawaban'])
) {
    header('Location: step.php?page=1');
    exit;
}

// Ambil id_mahasiswa dari session (format custom: IDMxxx)
$id_mahasiswa = (string) $_SESSION['id_mahasiswa'];
$jawaban      = $_SESSION['jawaban'];

// ---------------------------------------------------------------------------
// 3. Validasi keberadaan id_mahasiswa di database
// ---------------------------------------------------------------------------
try {
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE id_mahasiswa = ?");
    $checkStmt->execute([$id_mahasiswa]);

    if ($checkStmt->fetchColumn() == 0) {
        // ID mahasiswa tidak valid – kemungkinan session rusak atau data dihapus
        // Hapus session dan arahkan ke login
        session_destroy();
        header('Location: ../login.php?error=invalid');
        exit;
    }
} catch (Throwable $e) {
    die('Terjadi kesalahan saat memverifikasi data pengguna.');
}

// ---------------------------------------------------------------------------
// 4. Ambil semua variabel aktif dari database
// ---------------------------------------------------------------------------
try {
    $stmt = $pdo->query("
        SELECT id_variabel, id_subskala
        FROM variabel_skrining
        WHERE status_aktif = 1
        ORDER BY urutan_tampil ASC
    ");
    $variabel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('Gagal mengambil data pertanyaan: ' . $e->getMessage());
}

if (!$variabel) {
    die('Tidak ada data pada tabel variabel_skrining.');
}

// ---------------------------------------------------------------------------
// 5. Validasi jawaban lengkap
// ---------------------------------------------------------------------------
foreach ($variabel as $v) {
    if (!array_key_exists($v['id_variabel'], $jawaban)) {
        die('Masih ada pertanyaan yang belum dijawab. Silakan kembali ke skrining.');
    }
}

// ---------------------------------------------------------------------------
// 6. Hitung skor per subskala
// ---------------------------------------------------------------------------
$skor_depresi = 0;
$skor_anxiety = 0;
$skor_stress  = 0;

foreach ($variabel as $v) {
    $id_variabel = (string) $v['id_variabel'];
    $id_subskala = (string) $v['id_subskala'];
    $nilai       = (int) $jawaban[$id_variabel];

    if ($nilai < 0 || $nilai > 3) {
        die('Nilai jawaban tidak valid.');
    }

    switch ($id_subskala) {
        case 'IDU001': // Depression
            $skor_depresi += $nilai;
            break;
        case 'IDU002': // Anxiety
            $skor_anxiety += $nilai;
            break;
        case 'IDU003': // Stress
            $skor_stress += $nilai;
            break;
        default:
            die('Subskala tidak dikenal pada variabel_skrining.');
    }
}

// ---------------------------------------------------------------------------
// 7. Fungsi pembantu untuk membuat ID custom
// ---------------------------------------------------------------------------
function generateCustomId(PDO $pdo, string $table, string $column, string $prefix, int $padLength = 3): string
{
    $like = $prefix . '%';
    $offset = strlen($prefix) + 1;

    $sql = "
        SELECT `$column`
        FROM `$table`
        WHERE `$column` LIKE :like
        ORDER BY CAST(SUBSTRING(`$column`, $offset) AS UNSIGNED) DESC
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':like' => $like]);
    $lastId = $stmt->fetchColumn();

    $nextNumber = 1;

    if ($lastId && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $lastId, $match)) {
        $nextNumber = ((int) $match[1]) + 1;
    }

    return $prefix . str_pad((string) $nextNumber, $padLength, '0', STR_PAD_LEFT);
}

// ---------------------------------------------------------------------------
// 8. Simpan ke database
// ---------------------------------------------------------------------------
try {
    $pdo->beginTransaction();

    // 8a. ID skrining custom (IDSxxx)
    $id_skrining = generateCustomId($pdo, 'skrining', 'id_skrining', 'IDS', 3);

    // 8b. Simpan header skrining
    $stmtInsert = $pdo->prepare("
        INSERT INTO skrining
            (id_skrining, id_mahasiswa, tgl_skrining, skor_depresi, skor_anxiety, skor_stress, catatan)
        VALUES
            (:id_skrining, :id_mahasiswa, NOW(), :skor_depresi, :skor_anxiety, :skor_stress, :catatan)
    ");
    $stmtInsert->execute([
        ':id_skrining'  => $id_skrining,
        ':id_mahasiswa' => $id_mahasiswa,
        ':skor_depresi' => $skor_depresi,
        ':skor_anxiety' => $skor_anxiety,
        ':skor_stress'  => $skor_stress,
        ':catatan'      => 'Hasil skrining DASS-42'
    ]);

    // 8c. Simpan detail jawaban
    $stmtDetail = $pdo->prepare("
        INSERT INTO detail_skrining (id_detail, id_skrining, id_variabel, nilai)
        VALUES (:id_detail, :id_skrining, :id_variabel, :nilai)
    ");

    foreach ($variabel as $v) {
        $id_variabel = (string) $v['id_variabel'];
        $nilai       = (int) $jawaban[$id_variabel];
        $id_detail   = generateCustomId($pdo, 'detail_skrining', 'id_detail', 'IDD', 3);

        $stmtDetail->execute([
            ':id_detail'   => $id_detail,
            ':id_skrining' => $id_skrining,
            ':id_variabel' => $id_variabel,
            ':nilai'       => $nilai
        ]);
    }

    $pdo->commit();

    // 9. Bersihkan session jawaban dan arahkan ke hasil
    unset($_SESSION['jawaban']);
    $_SESSION['id_skrining_terakhir'] = $id_skrining;

    header('Location: result.php?id_skrining=' . urlencode($id_skrining));
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log error (jika diinginkan) dan beri tahu pengguna
    error_log('Error simpan skrining: ' . $e->getMessage());
    die('Gagal menyimpan hasil skrining. Silakan coba lagi atau hubungi administrator.');
}