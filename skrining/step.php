<?php
// skrining/step.php
session_start();

/*
|--------------------------------------------------------------------------
| Cek login
|--------------------------------------------------------------------------
*/
$isMahasiswaLogin =
    (isset($_SESSION['logged_in']) && ($_SESSION['role'] ?? '') === 'mahasiswa')
    || isset($_SESSION['npm'])
    || isset($_SESSION['id_mahasiswa']);

if (!$isMahasiswaLogin) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

if (!isset($_SESSION['jawaban']) || !is_array($_SESSION['jawaban'])) {
    $_SESSION['jawaban'] = [];
}

$per_page = 7;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

/*
|--------------------------------------------------------------------------
| Hitung total pertanyaan aktif
|--------------------------------------------------------------------------
*/
$countStmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM variabel_skrining
    WHERE status_aktif = 1
");
$countData = $countStmt->fetch(PDO::FETCH_ASSOC);
$total_questions = (int) ($countData['total'] ?? 0);

if ($total_questions <= 0) {
    die('Tidak ada pertanyaan aktif pada tabel variabel_skrining.');
}

$total_pages = (int) ceil($total_questions / $per_page);

if ($page > $total_pages) {
    header('Location: process.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Simpan jawaban dari halaman ini
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nilai']) && is_array($_POST['nilai'])) {
        foreach ($_POST['nilai'] as $id_variabel => $nilai) {
            $id_variabel = trim((string) $id_variabel);
            $nilai = (int) $nilai;

            if ($nilai >= 0 && $nilai <= 3) {
                $_SESSION['jawaban'][$id_variabel] = $nilai;
            }
        }
    }

    $next_page = $page + 1;

    if ($next_page > $total_pages) {
        header('Location: process.php');
    } else {
        header('Location: step.php?page=' . $next_page);
    }
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil pertanyaan untuk halaman aktif
|--------------------------------------------------------------------------
*/
$offset = ($page - 1) * $per_page;

$stmt = $pdo->prepare("
    SELECT
        v.id_variabel,
        v.no_item,
        v.pertanyaan,
        v.id_subskala,
        s.nama_id AS nama_subskala
    FROM variabel_skrining v
    JOIN subskala s ON s.id_subskala = v.id_subskala
    WHERE v.status_aktif = 1
    ORDER BY v.urutan_tampil ASC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$questions) {
    header('Location: process.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Progress bar
|--------------------------------------------------------------------------
*/
$progress = (($page - 1) / $total_pages) * 100;

/*
|--------------------------------------------------------------------------
| Opsi jawaban DASS
|--------------------------------------------------------------------------
*/
$options = [
    0 => 'Tidak pernah',
    1 => 'Kadang-kadang',
    2 => 'Lumayan sering',
    3 => 'Sering sekali'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skrining - Halaman <?= $page; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #6ea3d9;   /* ★ biru penuh */
            color: #2f3137;
            min-height: 100vh;
        }

        /* ── SCREEN (full page) ── */
        .screen {
            background: #6ea3d9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }

        .breadcrumb {
            color: #ffffff;
            font-size: 14px;
            margin-bottom: 12px;
            max-width: 1200px;
            width: 100%;
        }

        .card {
            width: 100%;
            max-width: 1200px;
            background: #ffffff;
            border-radius: 22px;
            box-shadow: 0 12px 24px rgba(0,0,0,.12);
            padding: 40px 48px;
            display: flex;
            flex-direction: column;
            min-height: 620px;
        }

        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .header img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
        }

        .progress-info {
            text-align: center;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 600;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: #e6e6e6;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .progress-fill {
            height: 100%;
            background: #a9e2d3;
        }

        .question-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .question-box {
            background: #f9f9f9;
            border: 2px solid #e6e6e6;
            border-radius: 16px;
            padding: 18px 22px;
        }

        .question-head {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #555;
        }

        .question-text {
            font-size: 20px;
            line-height: 1.6;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .option {
            position: relative;
        }

        .option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .option-label {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            background: #ffffff;
            border: 2px solid #e6e6e6;
            border-radius: 14px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .option-label:hover {
            border-color: #6ea3d9;
            background: #f7fbff;
        }

        .option input[type="radio"]:checked + .option-label {
            border-color: #6ea3d9;
            background: #eef6ff;
            box-shadow: 0 4px 12px rgba(110, 163, 217, 0.15);
        }

        .score {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #6ea3d9;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .label-text {
            font-size: 14px;
            line-height: 1.4;
            color: #2f3137;
            font-weight: 500;
        }

        .option input[type="radio"]:checked + .option-label .label-text {
            font-weight: 600;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 30px;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 150px;
            height: 56px;
            padding: 0 36px;
            border: none;
            border-radius: 999px;
            background: #a9e2d3;
            color: #2f3137;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(0,0,0,.12);
            transition: 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-back {
            background: #d9d9d9;
        }

        @media (max-width: 768px) {
            .screen { padding: 16px; }
            .card { padding: 24px; min-height: auto; }
            .question-text { font-size: 18px; }
            .options { grid-template-columns: 1fr; }
            .actions { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>

    <section class="screen">
        <div class="breadcrumb">
            Skrining (Halaman <?= $page; ?> dari <?= $total_pages; ?>)
        </div>

        <div class="card">
            <div class="header">
                <img src="../assets/img/logo.png" alt="Logo">
                <h1>Symptom Checker</h1>
            </div>

            <div class="progress-info">
                Pertanyaan <?= (($page - 1) * $per_page) + 1; ?> - <?= min($page * $per_page, $total_questions); ?> dari <?= $total_questions; ?>
            </div>

            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $progress; ?>%;"></div>
            </div>

            <form method="post">
                <div class="question-list">
                    <?php foreach ($questions as $q): ?>
                        <?php
                            $idVariabel = (string) $q['id_variabel'];
                            $checkedVal = $_SESSION['jawaban'][$idVariabel] ?? null;
                        ?>
                        <div class="question-box">
                            <div class="question-head">
                                No. <?= htmlspecialchars($q['no_item']); ?> 
                            </div>

                            <div class="question-text">
                                <?= htmlspecialchars($q['pertanyaan']); ?>
                            </div>

                            <div class="options">
                                <?php foreach ($options as $value => $label): ?>
                                    <?php
                                        $input_id = 'q_' . $idVariabel . '_' . $value;
                                        $checked = ($checkedVal !== null && (int)$checkedVal === $value);
                                    ?>
                                    <div class="option">
                                        <input
                                            type="radio"
                                            id="<?= htmlspecialchars($input_id); ?>"
                                            name="nilai[<?= htmlspecialchars($idVariabel); ?>]"
                                            value="<?= $value; ?>"
                                            <?= $checked ? 'checked' : ''; ?>
                                            required
                                        >
                                        <label for="<?= htmlspecialchars($input_id); ?>" class="option-label">
                                            <span class="score"><?= $value; ?></span>
                                            <span class="label-text"><?= htmlspecialchars($label); ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="actions">
                    <?php if ($page > 1): ?>
                        <a href="step.php?page=<?= $page - 1; ?>" class="btn btn-back">Kembali</a>
                    <?php else: ?>
                        <a href="index.php" class="btn btn-back">Kembali</a>
                    <?php endif; ?>

                    <button type="submit" class="btn">
                        <?= ($page == $total_pages) ? 'Selesai' : 'Lanjut'; ?>
                    </button>
                </div>
            </form>
        </div>
    </section>

</body>
</html>