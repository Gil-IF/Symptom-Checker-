<?php
// register.php
session_start();
// Jika sudah login DAN tidak ada flag ?fresh=1, baru redirect ke dashboard
if (!empty($_SESSION['npm']) && empty($_GET['fresh'])) {
    header('Location: dashboard/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Symptom Checker – Register</title>
<link rel="stylesheet" href="assets/css/register.css"/>
</head>
<body>

<!-- ── TOPBAR ── -->
<header class="topbar">
    <a href="index.php" class="brand">
        <img src="assets/img/logo.png" alt="Logo Symptom Checker">
        <span>Symptom Checker</span>
    </a>
    <nav class="nav">
        <a href="about.php">About</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<main class="page">
    <section class="card">

        <!-- Kiri: logo sebagai ilustrasi -->
        <div class="left">
            <img src="assets/img/logo.png" alt="Ilustrasi Symptom Checker">
        </div>

        <!-- Kanan: form register -->
        <div class="right">
            <div class="welcome">
                <h1>Create Account</h1>
                <p>Masukkan NPM dan buat password untuk mendaftar</p>
            </div>

            <?php
            /* Pesan error dari redirect proses_register.php */
            $error_map = [
                'empty'    => 'NPM dan password tidak boleh kosong.',
                'short_pw' => 'Password minimal 6 karakter.',
                'mismatch' => 'Password dan konfirmasi tidak cocok.',
                'exists'   => 'NPM sudah terdaftar. Silakan login.',
                'server'   => 'Terjadi kesalahan server. Coba beberapa saat lagi.',
            ];
            $err = $_GET['error'] ?? '';
            if ($err && isset($error_map[$err])): ?>
                <div class="alert-error">
                    ⚠️ <?php echo htmlspecialchars($error_map[$err]); ?>
                </div>
            <?php endif; ?>

            <form action="proses_register.php" method="post" id="regForm" novalidate>

                <!-- NPM -->
                <div class="field">
                    <span class="icon">@</span>
                    <input
                        type="text"
                        name="npm"
                        placeholder="NPM"
                        autocomplete="username"
                        required
                        <?php if ($err): ?>class="has-error"<?php endif; ?>
                        value="<?php echo htmlspecialchars($_GET['npm'] ?? ''); ?>"
                    >
                </div>

                <!-- Password -->
                <div class="field">
                    <span class="icon">🔒</span>
                    <input
                        type="password"
                        name="password"
                        id="pw1"
                        placeholder="Password (min. 6 karakter)"
                        autocomplete="new-password"
                        required
                        <?php if ($err): ?>class="has-error"<?php endif; ?>
                    >
                    <button type="button" class="toggle-pw" data-target="pw1">👁️</button>
                </div>

                <!-- Konfirmasi Password -->
                <div class="field">
                    <span class="icon">🔑</span>
                    <input
                        type="password"
                        name="confirm_password"
                        id="pw2"
                        placeholder="Konfirmasi Password"
                        autocomplete="new-password"
                        required
                        <?php if ($err): ?>class="has-error"<?php endif; ?>
                    >
                    <button type="button" class="toggle-pw" data-target="pw2">👁️</button>
                </div>

                <button type="submit" class="btn-register" id="regBtn">
                    <span class="spinner" id="spinner"></span>
                    Daftar
                </button>
            </form>

            <div class="to-login">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </div>

    </section>
</main>

<!-- ── POPUP VALIDASI ── -->
<div class="popup-overlay" id="popupOverlay">
    <div class="popup-box">
        <div class="popup-icon">📋</div>
        <h3 id="popupMsg">Please complete your data!</h3>
        <button class="btn-ok" onclick="closePopup()">OK</button>
    </div>
</div>

<script src="assets/js/register.js"></script>
</body>
</html>