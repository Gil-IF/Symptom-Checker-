<?php
session_start();

if (!isset($_SESSION['npm'])) {
    header('Location: login.php');
    exit;
}

$npm = $_SESSION['npm'];
$nama = $_SESSION['nama_panggilan'] ?? $npm;
$email = $_SESSION['email'] ?? $npm . '@student.local';

// Avatar default jika belum diatur
$avatar = $_SESSION['avatar'] ?? '../assets/img/Profile.png';

// Proses perubahan avatar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['avatar'])) {
    $allowed = [
        '../assets/img/boy.png',
        '../assets/img/girl.png',
        '../assets/img/Profile.png'
    ];
    $newAvatar = $_POST['avatar'];
    if (in_array($newAvatar, $allowed)) {
        $_SESSION['avatar'] = $newAvatar;
        $avatar = $newAvatar;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Symptom Checker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>

    <!-- Tombol kembali fixed di kiri atas, di luar card -->
    <a href="../dashboard/index.php" class="back-link" title="Kembali">‹</a>

    <div class="profile-card">
        <!-- Avatar -->
        <div class="avatar-container">
            <img src="<?= htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar-img">
        </div>

        <!-- Tombol untuk menampilkan pilihan avatar -->
        <button class="edit-avatar-btn" onclick="toggleAvatarOptions()">Ubah Avatar</button>

        <!-- Form pilihan avatar (hidden by default) -->
        <form method="post" id="avatarForm" style="display: none;">
            <div class="avatar-options">
                <label>
                    <input type="radio" name="avatar" value="../assets/img/boy.png"
                        <?= ($avatar === '../assets/img/boy.png') ? 'checked' : ''; ?>
                        onchange="this.form.submit()">
                    <img src="../assets/img/boy.png" alt="Cowok">
                </label>
                <label>
                    <input type="radio" name="avatar" value="../assets/img/girl.png"
                        <?= ($avatar === '../assets/img/girl.png') ? 'checked' : ''; ?>
                        onchange="this.form.submit()">
                    <img src="../assets/img/girl.png" alt="Cewek">
                </label>
                <label>
                    <input type="radio" name="avatar" value="../assets/img/Profile.png"
                        <?= ($avatar === '../assets/img/Profile.png') ? 'checked' : ''; ?>
                        onchange="this.form.submit()">
                    <img src="../assets/img/Profile.png" alt="Default">
                </label>
            </div>
        </form>

        <div class="name"><?= htmlspecialchars($nama); ?></div>
        <div class="npm">NPM: <?= htmlspecialchars($npm); ?></div>
        <div class="email"><?= htmlspecialchars($email); ?></div>

<ul class="menu">
    <li><a href="informasi.php"><span class="icon">📊</span> Informasi Pribadi</a></li>
    <li><a href="../riwayat/index.php"><span class="icon">📋</span> Riwayat Skrining</a></li>
    <li><a href="security_setup.php"><span class="icon">🔒</span> Lupa Password ?</a></li>
    <li><a href="../logout.php" class="logout"><span class="icon">🚪</span> Keluar</a></li>
</ul>
    </div>

<script src="../assets/js/dashboard.js"></script>
</script>
</body>
</html>