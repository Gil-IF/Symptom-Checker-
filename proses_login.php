<?php
/**
 * proses_login.php
 * Menangani login untuk admin (username) dan mahasiswa (NPM)
 */

session_start();
require_once 'config/database.php';

// Hanya menerima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Ambil input (prioritas field 'login', jika tidak ada gunakan 'npm')
$login    = trim($_POST['login'] ?? $_POST['npm'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input kosong
if ($login === '' || $password === '') {
    header('Location: login.php?error=empty');
    exit;
}

/**
 * Periksa apakah string password tersimpan dalam bentuk bcrypt hash.
 * Fix PHP 8.x: password_get_info() mengembalikan ['algo' => NULL]
 * untuk string biasa (bukan 0 seperti PHP 7.x), sehingga perlu
 * menggunakan !empty() atau cek prefix langsung.
 */
function isHashedPassword(string $stored): bool
{
    // Bcrypt hash selalu diawali '$2y$' atau '$2a$' dan panjang 60 karakter
    return (
        strlen($stored) === 60 &&
        (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$2a$'))
    );
}

/**
 * Verifikasi password: mendukung bcrypt hash maupun plain text.
 */
function verifyPassword(string $input, string $stored): bool
{
    if (isHashedPassword($stored)) {
        return password_verify($input, $stored);
    }
    // Perbandingan aman untuk plain text (timing attack resistant)
    return hash_equals($stored, $input);
}

try {
    // ==================================================
    // 1. Cek di tabel admin (menggunakan username)
    // ==================================================
    $stmt = $pdo->prepare("
        SELECT id_admin, username, password
        FROM admin
        WHERE username = :login
        LIMIT 1
    ");
    $stmt->execute(['login' => $login]);
    $admin = $stmt->fetch();

    if ($admin && verifyPassword($password, $admin['password'])) {
        // Regenerasi ID session untuk keamanan
        session_regenerate_id(true);

        // Simpan data session admin
        $_SESSION['logged_in']      = true;
        $_SESSION['role']           = 'admin';
        $_SESSION['id_admin']       = (int) $admin['id_admin'];
        $_SESSION['admin_username'] = $admin['username'];

        // Langsung ke dashboard admin
        header('Location: admin/index.php');
        exit;
    }

    // ==================================================
    // 2. Cek di tabel mahasiswa (menggunakan NPM)
    // ==================================================
    $stmt = $pdo->prepare("
        SELECT id_mahasiswa, npm, password
        FROM mahasiswa
        WHERE npm = :login
        LIMIT 1
    ");
    $stmt->execute(['login' => $login]);
    $mahasiswa = $stmt->fetch();

if ($mahasiswa && verifyPassword($password, $mahasiswa['password'])) {
    session_regenerate_id(true);
    $_SESSION['logged_in']    = true;
    $_SESSION['role']         = 'mahasiswa';
    $_SESSION['id_mahasiswa'] = $mahasiswa['id_mahasiswa'];
    $_SESSION['npm']          = $mahasiswa['npm'];
    $_SESSION['nama']         = $mahasiswa['nama'];   // ← tambahkan ini
    // opsional: $_SESSION['nama'] = $mahasiswa['nama'];

    header('Location: dashboard/index.php');
    exit;
}

    // Jika tidak cocok di kedua tabel
    header('Location: login.php?error=invalid');
    exit;

} catch (Throwable $e) {
    // Log error secara aman (opsional)
    // error_log($e->getMessage());

    // Redirect dengan pesan server error
    header('Location: login.php?error=server');
    exit;
}