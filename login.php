<?php
// login.php
session_start();

// Jika sudah login, langsung ke dashboard
if (!empty($_SESSION['logged_in'])) {
    if (($_SESSION['role'] ?? '') === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: dashboard/index.php');
    }
    exit;
}

// Peta kode error → pesan ramah
$error_messages = [
    'empty'   => 'NPM dan password tidak boleh kosong.',
    'invalid' => 'NPM atau password salah. Silakan coba lagi.',
    'server'  => 'Terjadi kesalahan server. Coba beberapa saat lagi.',
];

$error_code   = $_GET['error']      ?? '';
$error_msg    = $error_messages[$error_code] ?? '';
$registered   = isset($_GET['registered']) && $_GET['registered'] === '1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symptom Checker - Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
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
            <a href="login.php" class="active">Login</a>
        </nav>
    </header>

    <!-- ── MAIN ── -->
    <main class="page">
        <section class="card">

            <!-- Ilustrasi kiri -->
            <div class="left">
                <img src="assets/img/tatap_awal.png" alt="Ilustrasi Login">
            </div>

            <!-- Form kanan -->
            <div class="right">
                <div class="welcome">
                    <h1>Welcome back!</h1>
                    <p>Take your time, we're here for you</p>
                </div>

                <?php if ($registered): ?>
                <div class="alert-success" role="alert">
                    ✅ Akun berhasil dibuat! Silakan login dengan NPM Anda.
                </div>
                <?php endif; ?>

                <?php if ($error_msg !== ''): ?>
                <div class="alert-error" role="alert">
                    <span class="alert-icon">⚠️</span>
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
                <?php endif; ?>

                <form action="proses_login.php" method="post" id="loginForm" novalidate>

                    <div class="field">
                        <span class="icon">@</span>
                        <input
                type="text"
                name="login"
                placeholder="NPM / Username"
                title="Masukkan NPM (mahasiswa) atau username (admin)"
                autocomplete="username" 
                required
                class="<?php echo $error_msg ? 'has-error' : ''; ?>"
                value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"
                        >
                    </div>

                    <div class="field">
                        <span class="icon">🔒</span>
                        <input
                            type="password"
                            name="password"
                            id="passwordInput"
                            placeholder="Password"
                            autocomplete="current-password"
                            required
                            class="<?php echo $error_msg ? 'has-error' : ''; ?>"
                        >
                        <button type="button" class="toggle-pw" id="togglePw" aria-label="Tampilkan password">
                            👁️
                        </button>
                    </div>

                    <a href="forgot_password.php" class="forgot">Forgot password?</a>

                    <div class="btn-wrap">
                        <button type="submit" class="btn-login" id="loginBtn">
                            <span class="spinner" id="spinner"></span>
                            Login
                        </button>
                    </div>

                </form>

                <div class="divider">continue with</div>

                <div class="signup">
                    Don't have an account?<a href="register.php?fresh=1">Register</a>
                </div>
            </div>

        </section>
    </main>
    <script src="assets/js/login.js"></script>
</body>
</html>