<?php
// admin_panel/reset_password.php
session_start();
require_once '../config/database.php';

// Daftar NPM yang boleh jadi admin
$admin_npms = ['12345678', '87654321']; // ganti dengan NPM admin

if (!isset($_SESSION['npm']) || !in_array($_SESSION['npm'], $admin_npms)) {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['npm'])) {
    $npm = $_POST['npm'];
    $new_password = $_POST['new_password'];
    if (!empty($npm) && !empty($new_password)) {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE mahasiswa SET password = ? WHERE npm = ?");
        $stmt->execute([$hash, $npm]);
        $msg = "Password untuk NPM $npm berhasil direset.";
    } else {
        $msg = 'Mohon isi NPM dan password baru.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); max-width: 400px; width: 100%; }
        input, button { width: 100%; padding: 12px; margin: 8px 0; border-radius: 12px; border: 2px solid #e6e6e6; font-size: 14px; }
        button { background: #6ea3d9; color: #fff; font-weight: 700; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Reset Password (Admin)</h2>
        <?php if (isset($msg)) echo "<p>$msg</p>"; ?>
        <form method="post">
            <input type="text" name="npm" placeholder="NPM Mahasiswa" required>
            <input type="password" name="new_password" placeholder="Password Baru" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>