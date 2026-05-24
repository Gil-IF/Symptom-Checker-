<?php
/**
 * proses_register.php
 *
 * Hanya 3 tugas:
 *   1. Ambil input POST
 *   2. Panggil AuthController
 *   3. Redirect sesuai hasil
 *
 * Semua logika (validasi, query, hash) ada di controllers/AuthController.php
 */

session_start();
require_once 'config/database.php';
require_once 'controllers/AuthController.php';

// Tolak selain POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// Ambil input — sesuai name di form register.php
$npm     = trim($_POST['npm']              ?? '');
$pw      =      $_POST['password']         ?? '';
$confirm =      $_POST['confirm_password'] ?? '';

try {
    $auth   = new AuthController($pdo);
    $result = $auth->register($npm, $pw, $confirm);

    if (!$result['success']) {
        // Kirim npm kembali agar field tidak kosong di form
        $query = http_build_query([
            'error' => $result['error'],
            'npm'   => $result['npm'],
        ]);
        header('Location: register.php?' . $query);
        exit;
    }

    // Berhasil → ke login dengan notif sukses
    header('Location: login.php?registered=1');
    exit;

} catch (Throwable $e) {
    error_log('Register error: ' . $e->getMessage());
    header('Location: register.php?error=server');
    exit;
}