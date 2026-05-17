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
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Poppins', Arial, sans-serif;
    min-height: 100vh;
    background: #f5f7fb;
}

/* ── TOPBAR ── */
.topbar {
    height: 84px;
    background: #ffffff;
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    box-shadow: 0 4px 18px rgba(0,0,0,.12);
    position: sticky; top: 0; z-index: 10;
}
.brand {
    display: flex; align-items: center; gap: 12px;
    font-weight: 700; font-size: 22px; color: #2f343a;
    text-decoration: none;
}
.brand img { width: 42px; height: 42px; object-fit: contain; }
.nav { display: flex; align-items: center; gap: 44px; font-size: 18px; font-weight: 600; }
.nav a {
    text-decoration: none; color: #30353b;
    position: relative; padding-bottom: 6px;
}
.nav a.active::after {
    content: ""; position: absolute; left: 0; bottom: 0;
    width: 100%; height: 4px; border-radius: 999px; background: #a9e2d3;
}

/* ── PAGE ── */
.page {
    min-height: calc(100vh - 84px);
    display: grid; place-items: center; padding: 24px;
}

/* ── CARD ── */
.card {
    width: min(1180px, 100%);
    min-height: 560px;
    border-radius: 32px; overflow: hidden;
    display: grid; grid-template-columns: 1.05fr .95fr;
    box-shadow: 0 14px 30px rgba(20,36,70,.14);
    background: linear-gradient(90deg, #63c0ba 0 48%, #6da3d8 48% 100%);
}

/* ── SISI KIRI ── */
.left {
    display: flex; align-items: center;
    justify-content: center; padding: 28px;
}
.left img {
    width: 100%; max-width: 380px; height: auto;
    filter: drop-shadow(0 12px 28px rgba(0,0,0,.18));
}

/* ── SISI KANAN ── */
.right {
    padding: 54px 52px 42px;
    display: flex; flex-direction: column; justify-content: center;
}
.welcome { text-align: center; margin-bottom: 22px; }
.welcome h1 { font-size: 34px; line-height: 1; margin-bottom: 6px; color: #27313a; }
.welcome p  { font-size: 15px; color: #2f3842; }

/* ── ALERT ── */
.alert-error {
    display: flex; align-items: center; gap: 10px;
    background: rgba(220,53,69,.12);
    border: 1px solid rgba(220,53,69,.35);
    border-radius: 14px; padding: 12px 16px; margin-bottom: 10px;
    color: #7b1a24; font-size: 14px; font-weight: 600;
    animation: shake .35s ease;
}
@keyframes shake {
    0%,100%{transform:translateX(0)}
    20%    {transform:translateX(-6px)}
    40%    {transform:translateX(6px)}
    60%    {transform:translateX(-4px)}
    80%    {transform:translateX(4px)}
}

/* ── FIELD ── */
.field { position: relative; margin-top: 16px; }
.field input {
    width: 100%; height: 58px; border: none; outline: none;
    border-radius: 18px; background: rgba(255,255,255,.96);
    padding: 0 48px 0 58px; font-size: 16px;
    font-family: 'Poppins', Arial, sans-serif;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.5), 0 8px 18px rgba(0,0,0,.08);
    transition: box-shadow .2s;
}
.field input:focus {
    box-shadow: inset 0 0 0 2px #a9e2d3, 0 8px 18px rgba(0,0,0,.08);
}
.field input.has-error {
    box-shadow: inset 0 0 0 2px rgba(220,53,69,.5), 0 8px 18px rgba(0,0,0,.08);
}
.field .icon {
    position: absolute; left: 18px; top: 50%;
    transform: translateY(-50%); font-size: 18px; opacity: .5; pointer-events: none;
}
.toggle-pw {
    position: absolute; right: 16px; top: 50%;
    transform: translateY(-50%); background: none; border: none;
    cursor: pointer; font-size: 17px; opacity: .45; transition: opacity .2s;
}
.toggle-pw:hover { opacity: .8; }

/* ── TOMBOL ── */
.btn-register {
    display: inline-flex; align-items: center; justify-content: center;
    align-self: center; margin-top: 26px;
    min-width: 180px; height: 56px; padding: 0 36px;
    border: none; border-radius: 999px;
    background: #a9e2d3; color: #27313a;
    font-size: 18px; font-weight: 700;
    font-family: 'Poppins', Arial, sans-serif;
    cursor: pointer; box-shadow: 0 8px 18px rgba(0,0,0,.12);
    transition: .2s ease;
}
.btn-register:hover  { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(0,0,0,.16); }
.btn-register:active { transform: translateY(0); }
.btn-register:disabled { opacity: .7; cursor: not-allowed; transform: none; }
.btn-register .spinner {
    display: none; width: 20px; height: 20px;
    border: 3px solid rgba(39,49,58,.25); border-top-color: #27313a;
    border-radius: 50%; margin-right: 8px;
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── LINK KE LOGIN ── */
.to-login { text-align: center; margin-top: 16px; font-size: 14px; color: #26323c; }
.to-login a { text-decoration: none; color: inherit; font-weight: 700; margin-left: 6px; }

/* ── POPUP ── */
.popup-overlay {
    position: fixed; inset: 0;
    background: rgba(39,49,58,.45); backdrop-filter: blur(4px);
    display: none; place-items: center; z-index: 100;
}
.popup-overlay.show { display: grid; }
.popup-box {
    background: linear-gradient(145deg, #e8d8f8, #d4c8f0);
    border-radius: 24px; padding: 36px 40px 32px;
    text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,.2);
    animation: popIn .3s cubic-bezier(.34,1.56,.64,1);
    max-width: 300px; width: 90%;
}
@keyframes popIn {
    from { transform:scale(.7); opacity:0; }
    to   { transform:scale(1);  opacity:1; }
}
.popup-icon { font-size: 2.6rem; margin-bottom: 12px; }
.popup-box h3 { font-size: 1rem; font-weight: 700; color: #4a3770; margin-bottom: 18px; line-height: 1.5; }
.btn-ok {
    background: #7c5cbf; color: #fff;
    height: 44px; padding: 0 36px; border: none; border-radius: 999px;
    font-size: 15px; font-weight: 700; font-family: 'Poppins', Arial, sans-serif;
    cursor: pointer; transition: .2s; box-shadow: 0 6px 16px rgba(124,92,191,.3);
}
.btn-ok:hover { background: #6a4caa; transform: translateY(-2px); }

/* ── RESPONSIVE ── */
@media (max-width: 980px) {
    .card { grid-template-columns: 1fr; min-height: auto; }
    .left { padding: 22px; }
    .right { padding: 34px 24px 34px; }
    .welcome h1 { font-size: 28px; }
}
@media (max-width: 640px) {
    .topbar { padding: 0 16px; height: 74px; }
    .brand { font-size: 18px; gap: 10px; }
    .nav { gap: 18px; font-size: 15px; }
    .card { border-radius: 24px; }
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

<script>
// ── Toggle show/hide password ─────────────────────────────
document.querySelectorAll('.toggle-pw').forEach(btn => {
    btn.addEventListener('click', function () {
        const inp  = document.getElementById(this.dataset.target);
        const show = inp.type === 'password';
        inp.type       = show ? 'text' : 'password';
        this.textContent = show ? '🙈' : '👁️';
    });
});

// ── Popup ─────────────────────────────────────────────────
function showPopup(msg) {
    document.getElementById('popupMsg').textContent = msg;
    document.getElementById('popupOverlay').classList.add('show');
}
function closePopup() {
    document.getElementById('popupOverlay').classList.remove('show');
}
document.getElementById('popupOverlay').addEventListener('click', function (e) {
    if (e.target === this) closePopup();
});

// ── Validasi client-side sebelum submit ───────────────────
document.getElementById('regForm').addEventListener('submit', function (e) {
    const npm = this.npm.value.trim();
    const pw  = document.getElementById('pw1').value;
    const cpw = document.getElementById('pw2').value;

    if (!npm || !pw || !cpw) {
        e.preventDefault();
        showPopup('Please complete your data!');
        return;
    }
    if (pw.length < 6) {
        e.preventDefault();
        showPopup('Password minimal 6 karakter.');
        return;
    }
    if (pw !== cpw) {
        e.preventDefault();
        showPopup('Password dan konfirmasi tidak cocok!');
        return;
    }

    // Validasi lolos — tampilkan loading spinner
    const btn    = document.getElementById('regBtn');
    const spinner = document.getElementById('spinner');
    btn.disabled             = true;
    spinner.style.display    = 'inline-block';
    btn.lastChild.textContent = ' Mendaftarkan...';
});
</script>
</body>
</html>