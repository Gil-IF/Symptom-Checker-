<?php
// navigasi.php
// Sidebar / navigation drawer – digunakan di dashboard & halaman internal

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$npm = $_SESSION['npm'];
$id_mahasiswa = $_SESSION['id_mahasiswa'] ?? null;

// Ambil nama dari database jika id_mahasiswa tersedia
$nama = $npm; // fallback default
if ($id_mahasiswa) {
    $stmt = $pdo->prepare("SELECT nama FROM mahasiswa WHERE id_mahasiswa = ?");
    $stmt->execute([$id_mahasiswa]);
    $nama = $stmt->fetchColumn() ?: $npm;
}

// Avatar: jika user sudah memilih di profile, gunakan itu. Jika tidak, tampilkan default.
$avatar = $_SESSION['avatar'] ?? '../assets/img/profile.png';
// Contoh file avatar yang bisa dipilih: avatar-cowok.png, avatar-cewek.png
?>
<link rel="stylesheet" href="../assets/css/navigasi.css">

<!-- Tombol pemicu navigasi (biasanya sudah ada di topbar, tapi sebagai fallback) -->
<button class="side-toggle-btn" type="button" aria-label="Buka navigasi" onclick="openNavDrawer()">
    <span></span>
</button>

<div class="nav-overlay" id="navOverlay" onclick="closeNavDrawer()"></div>

<aside class="nav-drawer" id="navDrawer" aria-label="Navigasi samping">
    <div class="drawer-top">
        <button class="drawer-close" type="button" aria-label="Tutup navigasi" onclick="closeNavDrawer()">&lsaquo;</button>

        <div class="profile-box">
            <!-- Avatar dari session. Jika ingin mengubahnya, buka halaman Profile -->
            <img src="<?= htmlspecialchars($avatar); ?>" alt="Avatar pengguna">
            <div class="profile-info">
                <div class="name"><?= htmlspecialchars($nama); ?></div>
                <div class="npm-display"><?= htmlspecialchars($npm); ?></div>
            </div>
        </div>
    </div>

    <ul class="drawer-menu">
        <li><a href="../dashboard/profile.php"><span class="icon">👤</span> Profile</a></li>
        <li><a href="../dashboard/index.php" class="active"><span class="icon">🏠</span> Home</a></li>
        <li><a href="../skrining/index.php"><span class="icon">📝</span> Mulai Skrining</a></li>
        <li><a href="../riwayat/index.php"><span class="icon">⏳</span> Riwayat</a></li>
        <li><a href="../about.php"><span class="icon">❓</span> Tentang</a></li>
        <li><a href="../logout.php"><span class="icon">🚪</span> Keluar</a></li>
    </ul>

    <div class="drawer-footer">
        &copy; Symptom Checker
    </div>
</aside>

<script src="../assets/js/dashboard.js"></script>