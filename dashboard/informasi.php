<?php
// dashboard/informasi.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['npm']) || !isset($_SESSION['id_mahasiswa'])) {
    header('Location: ../login.php');
    exit;
}

$npm = $_SESSION['npm'];
$id_mahasiswa = $_SESSION['id_mahasiswa'];
$success = '';
$error = '';

// Ambil data user (nama, email, created_at)
$stmt = $pdo->prepare("SELECT nama, npm, created_at FROM mahasiswa WHERE id_mahasiswa = ?");
$stmt->execute([$id_mahasiswa]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Data pengguna tidak ditemukan.');
}

$nama = $user['nama'] ?? $npm;
$email = $user['email'] ?? $npm . '@student.local';
$tgl_daftar = $user['created_at'] ?? '-';

// Tanggal skrining terakhir
$stmt2 = $pdo->prepare("SELECT MAX(tgl_skrining) FROM skrining WHERE id_mahasiswa = ?");
$stmt2->execute([$id_mahasiswa]);
$tgl_skrining_terakhir = $stmt2->fetchColumn();
$tgl_skrining_terakhir = $tgl_skrining_terakhir ? date('d F Y H:i', strtotime($tgl_skrining_terakhir)) : 'Belum pernah';

// Proses ubah nama
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ubah_nama'])) {
    $nama_baru = trim($_POST['nama']);
    if (empty($nama_baru)) {
        $error = 'Nama tidak boleh kosong.';
    } else {
        $stmt = $pdo->prepare("UPDATE mahasiswa SET nama = ? WHERE id_mahasiswa = ?");
        $stmt->execute([$nama_baru, $id_mahasiswa]);
        $_SESSION['nama_panggilan'] = $nama_baru;
        $nama = $nama_baru;
        $success = 'Nama berhasil diperbarui.';
    }
}

// Proses ubah password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ubah_password'])) {
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $password_konfirmasi = $_POST['password_konfirmasi'] ?? '';

    // Ambil password lama dari database
    $stmt = $pdo->prepare("SELECT password FROM mahasiswa WHERE id_mahasiswa = ?");
    $stmt->execute([$id_mahasiswa]);
    $stored_password = $stmt->fetchColumn();

    if (empty($stored_password)) {
        $error = 'Data password tidak ditemukan. Hubungi admin.';
    } else {
        $is_valid = false;

        // Cek apakah password di DB adalah hash (dimulai dengan $2y$)
        if (str_starts_with($stored_password, '$2y$') || str_starts_with($stored_password, '$2a$')) {
            // Sudah hash → gunakan password_verify()
            $is_valid = password_verify($password_lama, $stored_password);
        } else {
            // Masih plain text → bandingkan langsung
            $is_valid = ($password_lama === $stored_password);

            // Jika valid, langsung update ke hash agar ke depannya aman
            if ($is_valid) {
                $new_hash = password_hash($password_lama, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE mahasiswa SET password = ? WHERE id_mahasiswa = ?");
                $stmt->execute([$new_hash, $id_mahasiswa]);
                // Tidak perlu memberi tahu user
            }
        }

        if (!$is_valid) {
            $error = 'Password lama salah.';
        } elseif (strlen($password_baru) < 6) {
            $error = 'Password baru minimal 6 karakter.';
        } elseif ($password_baru !== $password_konfirmasi) {
            $error = 'Konfirmasi password tidak cocok.';
        } else {
            $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE mahasiswa SET password = ? WHERE id_mahasiswa = ?");
            $stmt->execute([$hash_baru, $id_mahasiswa]);
            $success = 'Password berhasil diubah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Akun - Symptom Checker</title>
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .back-link:hover {
            background: #fff;
            color: #222;
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 32px 30px;
            max-width: 460px;
            width: 100%;
        }

        h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
        }
        .form-group input {
            width: 100%;
            padding: 12px 40px 12px 16px;
            border: 2px solid #e6e6e6;
            border-radius: 14px;
            font-size: 14px;
            transition: 0.2s;
        }
        .form-group input:focus {
            border-color: #6ea3d9;
            outline: none;
        }

        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 38px;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            opacity: 0.6;
            padding: 4px;
            line-height: 1;
        }
        .toggle-pw:hover {
            opacity: 1;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 999px;
            background: #6ea3d9;
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 4px;
        }
        .btn:hover { background: #4a85ae; }

        .divider {
            border-top: 1px solid #eee;
            margin: 24px 0;
        }

        .info-box {
            background: #f9f9f9;
            border-radius: 14px;
            padding: 16px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .info-item:last-child { margin-bottom: 0; }
        .info-item .label { color: #666; }
        .info-item .value { font-weight: 600; }

        .message {
            padding: 10px 14px;
            border-radius: 12px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: 500;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #444;
        }
    </style>
</head>
<body>

    <a href="../dashboard/profile.php" class="back-link" title="Kembali">‹</a>

    <div class="card">
        <h2>⚙️ Informasi Akun</h2>

        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Form Ubah Nama -->
        <div class="section-title">Ubah Nama</div>
        <form method="post">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
            </div>
            <button type="submit" name="ubah_nama" class="btn">Simpan Nama</button>
        </form>

        <div class="divider"></div>

        <!-- Form Ubah Password -->
        <div class="section-title">Ubah Password</div>
        <form method="post">
            <div class="form-group">
                <label>Password Lama</label>
                <input type="password" name="password_lama" id="pwLama" required>
                <button type="button" class="toggle-pw" onclick="togglePassword('pwLama', this)">👁️</button>
            </div>
            <div class="form-group">
                <label>Password Baru (min. 6 karakter)</label>
                <input type="password" name="password_baru" id="pwBaru" required>
                <button type="button" class="toggle-pw" onclick="togglePassword('pwBaru', this)">👁️</button>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="password_konfirmasi" id="pwKonfirmasi" required>
                <button type="button" class="toggle-pw" onclick="togglePassword('pwKonfirmasi', this)">👁️</button>
            </div>
            <button type="submit" name="ubah_password" class="btn">Ganti Password</button>
        </form>

        <div class="divider"></div>

        <!-- Informasi Akun -->
        <div class="section-title">Detail Akun</div>
        <div class="info-box">
            <div class="info-item">
                <span class="label">NPM</span>
                <span class="value"><?= htmlspecialchars($npm) ?></span>
            </div>
            <div class="info-item">
                <span class="label">Email</span>
                <span class="value"><?= htmlspecialchars($email) ?></span>
            </div>
            <div class="info-item">
                <span class="label">Akun dibuat</span>
                <span class="value"><?= $tgl_daftar !== '-' ? date('d M Y', strtotime($tgl_daftar)) : '-' ?></span>
            </div>
            <div class="info-item">
                <span class="label">Skrining terakhir</span>
                <span class="value"><?= htmlspecialchars($tgl_skrining_terakhir) ?></span>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁️';
            }
        }
    </script>
</body>
</html>