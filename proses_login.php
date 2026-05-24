<?php
/**
 * proses_login.php
 *
 * Hanya 3 tugas:
 *   1. Ambil input POST
 *   2. Panggil AuthController
 *   3. Redirect sesuai hasil
 *
 * Semua logika (query, verifikasi, session) ada di controllers/AuthController.php
 */

session_start();
require_once 'config/database.php';
require_once 'controllers/AuthController.php';

// Tolak selain POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Ambil input — sesuai name="login" di form login.php
$login    = trim($_POST['login'] ?? $_POST['npm'] ?? '');
$password = $_POST['password'] ?? '';

try {
    $auth   = new AuthController($pdo);
    $result = $auth->login($login, $password);

    if (!$result['success']) {
        header('Location: login.php?error=' . $result['error']);
        exit;
    }

    // Simpan ke session
    $auth->saveSession($result);

    // Redirect sesuai role
    $redirect = match ($result['role']) {
        'admin'     => 'admin/index.php',
        'mahasiswa' => 'dashboard/index.php',
        default     => 'login.php?error=invalid',
    };

    header('Location: ' . $redirect);
    exit;

} catch (Throwable $e) {
    error_log('Login error: ' . $e->getMessage());
    header('Location: login.php?error=server');
    exit;
}