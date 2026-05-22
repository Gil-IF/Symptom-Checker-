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
    <style>
        :root {
            --bg: #f4f4f6;
            --white: #ffffff;
            --text: #2f3137;
            --accent: #cbb2e1;
            --card-blue: #6ea3d9;
            --radius: 20px;
            --shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        a { text-decoration: none; color: inherit; }

        /* Tombol kembali fixed di kiri atas */
        .back-link {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(6px);
            border-radius: 50%;
            font-size: 22px;
            font-weight: 600;
            color: #555;
            transition: 0.2s;
            line-height: 1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .back-link:hover {
            background: #fff;
            color: #222;
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }

        .profile-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 40px 32px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }

        .avatar-container {
            position: relative;
            margin: 0 auto 20px;
            width: 110px;
            height: 110px;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 8px 18px rgba(0,0,0,0.15);
            background: #fff;
        }

        .edit-avatar-btn {
            display: inline-block;
            background: var(--accent);
            color: #333;
            font-size: 12px;
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 20px;
            cursor: pointer;
            border: none;
            font-weight: 600;
        }

        .edit-avatar-btn:hover {
            background: #b794d4;
        }

        .name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .npm {
            font-size: 14px;
            color: #666;
            margin-bottom: 4px;
        }

        .email {
            font-size: 14px;
            color: #888;
            margin-bottom: 32px;
        }

        /* Pilihan avatar */
        .avatar-options {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 24px;
        }
        .avatar-options label {
            cursor: pointer;
        }
        .avatar-options input[type="radio"] {
            display: none;
        }
        .avatar-options img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid transparent;
            transition: 0.2s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .avatar-options input[type="radio"]:checked + img {
            border-color: #6ea3d9;
            box-shadow: 0 4px 14px rgba(110,163,217,0.4);
        }

        .menu {
            list-style: none;
            text-align: left;
            border-top: 1px solid #eee;
            padding-top: 16px;
        }
        .menu li {
            margin-bottom: 12px;
        }
        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 12px;
            transition: 0.2s;
            font-weight: 500;
        }
        .menu a:hover {
            background: #f7f7f9;
        }
        .menu .logout {
            color: #b00020;
            font-weight: 600;
        }
        .menu .icon {
            width: 24px;
            text-align: center;
            font-size: 18px;
        }
    </style>
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

<script>
    function toggleAvatarOptions() {
        var form = document.getElementById('avatarForm');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
</script>
</body>
</html>