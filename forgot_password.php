<?php
// forgot_password.php
session_start();
require_once 'config/database.php';

$step = 1; // 1: input NPM, 2: tanya jawab, 3: reset form
$npm = '';
$question = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['npm'])) {
        // Step 1: cek NPM
        $npm = trim($_POST['npm']);
        $stmt = $pdo->prepare("SELECT security_question, security_answer_hash, id_mahasiswa FROM mahasiswa WHERE npm = ?");
        $stmt->execute([$npm]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && !empty($user['security_question'])) {
            $step = 2;
            $question = $user['security_question'];
            $_SESSION['reset_npm'] = $npm;
            $_SESSION['reset_id'] = $user['id_mahasiswa'];
        } else {
            $error = 'NPM tidak ditemukan atau belum mengatur pertanyaan keamanan.';
        }
    } elseif (isset($_POST['answer'])) {
        // Step 2: verifikasi jawaban
        $npm = $_SESSION['reset_npm'] ?? '';
        $stmt = $pdo->prepare("SELECT security_answer_hash FROM mahasiswa WHERE npm = ?");
        $stmt->execute([$npm]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($_POST['answer'], $user['security_answer_hash'])) {
            $step = 3; // boleh reset
        } else {
            $error = 'Jawaban salah.';
            $step = 2;
            $question = $pdo->query("SELECT security_question FROM mahasiswa WHERE npm = '$npm'")->fetchColumn();
        }
    } elseif (isset($_POST['new_password'])) {
        // Step 3: simpan password baru
        $npm = $_SESSION['reset_npm'] ?? '';
        $new_password = $_POST['new_password'];
        if (strlen($new_password) < 6) {
            $error = 'Password minimal 6 karakter.';
            $step = 3;
        } else {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE mahasiswa SET password = ? WHERE npm = ?");
            $stmt->execute([$hash, $npm]);
            unset($_SESSION['reset_npm'], $_SESSION['reset_id']);
            header('Location: login.php?reset=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); width: 100%; max-width: 400px; }
        h2 { margin-bottom: 20px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 12px; border: 2px solid #e6e6e6; font-size: 14px; }
        button { width: 100%; padding: 12px; border: none; border-radius: 999px; background: #6ea3d9; color: #fff; font-weight: 700; font-size: 16px; cursor: pointer; }
        .back { position: fixed; top: 20px; left: 20px; width: 36px; height: 36px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #333; font-size: 22px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <a href="login.php" class="back">‹</a>
    <div class="card">
        <h2>Lupa Password</h2>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <?php if ($step == 1): ?>
            <form method="post">
                <label>Masukkan NPM Anda:</label>
                <input type="text" name="npm" placeholder="NPM" value="<?= htmlspecialchars($npm) ?>" required>
                <button type="submit">Lanjut</button>
            </form>
        <?php elseif ($step == 2): ?>
            <form method="post">
                <p>Pertanyaan Keamanan:</p>
                <p><strong><?= htmlspecialchars($question) ?></strong></p>
                <input type="text" name="answer" placeholder="Jawaban Anda" required>
                <button type="submit">Verifikasi</button>
            </form>
        <?php elseif ($step == 3): ?>
            <form method="post">
                <p>Password Baru (min. 6 karakter):</p>
                <input type="password" name="new_password" placeholder="Password baru" required>
                <button type="submit">Simpan Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>