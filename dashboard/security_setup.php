<?php
// dashboard/security_setup.php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['npm'])) {
    header('Location: ../login.php');
    exit;
}

$npm = $_SESSION['npm'];
$id_mahasiswa = $_SESSION['id_mahasiswa'] ?? null;

// Ambil data user untuk cek apakah sudah punya pertanyaan
$stmt = $pdo->prepare("SELECT security_question FROM mahasiswa WHERE npm = ?");
$stmt->execute([$npm]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$existing_question = $user['security_question'] ?? '';

$questions = [
    1 => 'Nama hewan peliharaan pertama?',
    2 => 'Nama sekolah dasar?',
    3 => 'Kota kelahiran ibu?',
    4 => 'Makanan favorit saat kecil?',
    5 => 'Nama panggilan masa kecil?'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = $_POST['question'];
    $answer = $_POST['answer'];

    if (!isset($questions[$question_id]) || empty(trim($answer))) {
        $error = 'Pilih pertanyaan dan isi jawaban.';
    } else {
        $hash = password_hash(trim($answer), PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE mahasiswa SET security_question = ?, security_answer_hash = ? WHERE npm = ?");
        $stmt->execute([$questions[$question_id], $hash, $npm]);
        $success = 'Pertanyaan keamanan berhasil disimpan.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keamanan Akun</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-bottom: 20px; }
        select, input[type="text"] { width: 100%; padding: 12px; margin: 10px 0; border-radius: 12px; border: 2px solid #e6e6e6; font-size: 14px; }
        button { width: 100%; padding: 12px; border: none; border-radius: 999px; background: #6ea3d9; color: #fff; font-weight: 700; font-size: 16px; cursor: pointer; margin-top: 10px; }
        button:hover { background: #4a85ae; }
        .back { position: fixed; top: 20px; left: 20px; width: 36px; height: 36px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #333; font-size: 22px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <a href="../dashboard/profile.php" class="back">‹</a>
    <div class="card">
        <h2>Pertanyaan Keamanan</h2>
        <?php if (isset($success)) echo "<p style='color:green'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="post">
            <label>Pilih pertanyaan:</label>
            <select name="question" required>
                <option value="">-- Pilih --</option>
                <?php foreach ($questions as $id => $q): ?>
                    <option value="<?= $id ?>" <?= ($existing_question == $q) ? 'selected' : '' ?>><?= htmlspecialchars($q) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Jawaban:</label>
            <input type="text" name="answer" placeholder="Jawaban Anda" required>
            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>