<?php
session_start();
require_once 'config/database.php';  // pastikan path benar

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $npm      = trim($_POST['npm'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input kosong
    if ($npm === '' || $password === '') {
        header('Location: login.php?error=empty');
        exit;
    }

    // Cari user berdasarkan NPM di tabel mahasiswa
    $stmt = $pdo->prepare("SELECT id_mahasiswa, npm, password FROM mahasiswa WHERE npm = :npm");
    $stmt->execute(['npm' => $npm]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $storedPassword = $user['password'];
        $loginSuccess = false;

        // Cek apakah password di database sudah di-hash (bcrypt dimulai dengan $2y$)
        if (str_starts_with($storedPassword, '$2y$')) {
            // Verifikasi dengan password_verify
            if (password_verify($password, $storedPassword)) {
                $loginSuccess = true;
            }
        } else {
            // Password masih plain text (lama) – bandingkan langsung
            if ($password === $storedPassword) {
                $loginSuccess = true;

                // Opsional: update password ke hash agar lebih aman
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE mahasiswa SET password = :hashed WHERE npm = :npm");
                $updateStmt->execute([
                    'hashed' => $hashed,
                    'npm'    => $npm
                ]);
            }
        }

        if ($loginSuccess) {
            // Login sukses – simpan data ke session
            $_SESSION['id_mahasiswa'] = $user['id_mahasiswa'];
            $_SESSION['npm'] = $user['npm'];

            // Redirect ke dashboard
            header('Location: dashboard/index.php');
            exit;
        }
    }

    // Jika NPM tidak ditemukan atau password salah
    header('Location: login.php?error=invalid');
    exit;
} else {
    // Akses langsung tanpa POST
    header('Location: login.php');
    exit;
}
?>