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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f5f7fb;
        }

        /* ── TOPBAR ── */
        .topbar {
            height: 84px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 22px;
            color: #2f343a;
            text-decoration: none;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 44px;
            font-size: 18px;
            font-weight: 600;
        }

        .nav a {
            text-decoration: none;
            color: #30353b;
            position: relative;
            padding-bottom: 6px;
        }

        .nav a.active::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 4px;
            border-radius: 999px;
            background: #a9e2d3;
        }

        /* ── LAYOUT ── */
        .page {
            min-height: calc(100vh - 84px);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: min(1580px, 100%);
            min-height: 770px;
            border-radius: 102px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            box-shadow: 0 14px 30px rgba(20, 36, 70, 0.14);
            background: linear-gradient(90deg, #63c0ba 0 48%, #6da3d8 48% 100%);
        }

.left {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;                       /* ← hapus padding */
    /* opsional: beri posisi relative */
    position: relative;
}

.left img {
    width: 100%;                      /* lebar penuh area kiri */
    height: 100%;                     /* tinggi penuh area kiri */
    object-fit: cover;                /* gambar mengisi tanpa distorsi */
    display: block;
    border-top-left-radius: 32px;     /* jika kartu punya radius 32px */
    border-bottom-left-radius: 32px;
}
        /* ── SISI KANAN (form) ── */
        .right {
            padding: 54px 52px 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome {
            text-align: center;
            margin-bottom: 18px;
        }

        .welcome h1 {
            font-size: 38px;
            line-height: 1;
            margin-bottom: 6px;
            color: #27313a;
        }

        .welcome p {
            font-size: 15px;
            color: #2f3842;
        }

        /* ── PESAN ERROR ── */
        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(220, 53, 69, 0.12);
            border: 1px solid rgba(220, 53, 69, 0.35);
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 6px;
            color: #7b1a24;
            font-size: 14px;
            font-weight: 600;
            animation: shake 0.35s ease;
        }

        .alert-success {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(40, 167, 69, 0.12);
            border: 1px solid rgba(40, 167, 69, 0.35);
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 6px;
            color: #145220;
            font-size: 14px;
            font-weight: 600;
        }

        .alert-error .alert-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%       { transform: translateX(-6px); }
            40%       { transform: translateX(6px); }
            60%       { transform: translateX(-4px); }
            80%       { transform: translateX(4px); }
        }

        /* ── FIELD ── */
        .field {
            position: relative;
            margin-top: 18px;
        }

        .field input {
            width: 100%;
            height: 58px;
            border: none;
            outline: none;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            padding: 0 20px 0 58px;
            font-size: 16px;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.5), 0 8px 18px rgba(0,0,0,.08);
            transition: box-shadow .2s;
        }

        .field input:focus {
            box-shadow: inset 0 0 0 2px #a9e2d3, 0 8px 18px rgba(0,0,0,.08);
        }

        /* input merah saat ada error */
        .field input.has-error {
            box-shadow: inset 0 0 0 2px rgba(220,53,69,.5), 0 8px 18px rgba(0,0,0,.08);
        }

        .field .icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            opacity: .5;
            font-size: 18px;
        }

        /* ── TOGGLE SHOW PASSWORD ── */
        .toggle-pw {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            opacity: .45;
            font-size: 18px;
            padding: 4px;
            transition: opacity .2s;
        }
        .toggle-pw:hover { opacity: .8; }

        .forgot {
            text-align: right;
            margin-top: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #26323c;
            text-decoration: none;
            display: block;
        }

        /* ── TOMBOL LOGIN ── */
        .btn-wrap {
            text-align: center;
            margin-top: 22px;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
            height: 56px;
            padding: 0 36px;
            border: none;
            border-radius: 999px;
            background: #a9e2d3;
            color: #27313a;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(0,0,0,.12);
            transition: .2s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0,0,0,.16);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* ── LOADING STATE ── */
        .btn-login .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(39,49,58,.3);
            border-top-color: #27313a;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            margin-right: 8px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── DIVIDER & GOOGLE ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 22px;
            color: #283039;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            background: rgba(18,30,48,.45);
            flex: 1;
        }

        .google-login {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .google-btn {
            width: 64px;
            height: 64px;
            border: none;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 18px rgba(0,0,0,.14);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
        }

        .google-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px rgba(0,0,0,.18);
        }

        .signup {
            text-align: center;
            margin-top: 14px;
            font-size: 14px;
            color: #26323c;
        }

        .signup a {
            text-decoration: none;
            color: inherit;
            font-weight: 700;
            margin-left: 8px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 980px) {
            .card {
                grid-template-columns: 1fr;
                min-height: auto;
            }
            .left { padding: 22px; }
            .right { padding: 34px 24px 34px; }
            .welcome h1 { font-size: 32px; }
        }

        @media (max-width: 640px) {
            .topbar { padding: 0 16px; height: 74px; }
            .brand { font-size: 18px; gap: 10px; }
            .nav { gap: 18px; font-size: 15px; }
            .card { border-radius: 24px; }
            .left img { max-width: 100%; }
        }
    </style>
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

    <script>
        // ── Toggle Show/Hide Password ──────────────────────────────────────────
        const pwInput  = document.getElementById('passwordInput');
        const togglePw = document.getElementById('togglePw');

        togglePw.addEventListener('click', () => {
            const isHidden = pwInput.type === 'password';
            pwInput.type       = isHidden ? 'text' : 'password';
            togglePw.textContent = isHidden ? '🙈' : '👁️';
        });

        // ── Loading state saat submit ──────────────────────────────────────────
        const loginForm = document.getElementById('loginForm');
        const loginBtn  = document.getElementById('loginBtn');
        const spinner   = document.getElementById('spinner');

        loginForm.addEventListener('submit', () => {
            loginBtn.disabled        = true;
            spinner.style.display    = 'block';
            loginBtn.lastChild.textContent = ' Memproses...';
        });
    </script>
</body>
</html>