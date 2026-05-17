<?php
// proses_register.php
session_start();
require_once 'config/database.php'; // PDO → $pdo, database: db_sc

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// ── Ambil & bersihkan input ───────────────────────────────
$npm              = trim($_POST['npm']              ?? '');
$password         = $_POST['password']              ?? '';
$confirm_password = $_POST['confirm_password']      ?? '';

// ── Validasi server-side ──────────────────────────────────
if ($npm === '' || $password === '' || $confirm_password === '') {
    header('Location: register.php?error=empty');
    exit;
}

if (strlen($password) < 6) {
    header('Location: register.php?error=short_pw&npm=' . urlencode($npm));
    exit;
}

if ($password !== $confirm_password) {
    header('Location: register.php?error=mismatch&npm=' . urlencode($npm));
    exit;
}

// ── Cek NPM sudah terdaftar ───────────────────────────────
try {
    $cek = $pdo->prepare("SELECT npm FROM mahasiswa WHERE npm = :npm LIMIT 1");
    $cek->execute([':npm' => $npm]);

    if ($cek->fetch()) {
        // NPM sudah ada
        header('Location: register.php?error=exists&npm=' . urlencode($npm));
        exit;
    }

    // ── Hash password & simpan ────────────────────────────
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO mahasiswa (npm, password) VALUES (:npm, :password)");
    $stmt->execute([
        ':npm'      => $npm,
        ':password' => $hashed,
    ]);

    // ── Berhasil → redirect ke login dengan pesan sukses ──
    header('Location: login.php?registered=1');
    exit;

} catch (PDOException $e) {
    // Catat error di log server (jangan tampilkan ke user)
    error_log('Register error: ' . $e->getMessage());
    header('Location: register.php?error=server');
    exit;
}