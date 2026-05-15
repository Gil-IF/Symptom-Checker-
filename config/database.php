<?php
/**
 * config/database.php
 * File koneksi database menggunakan PDO
 */

$host = 'localhost';
$dbname = 'db_sc';      // Ganti sesuai nama database Anda
$username = 'root';
$password = '';         // Kosongkan jika root tanpa password
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Tampilkan error sebagai exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Hasil query dalam bentuk associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Gunakan prepared statement native
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}
?>