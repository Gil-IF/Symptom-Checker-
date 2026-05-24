<?php
/**
 * controllers/AuthController.php
 *
 * Menggabungkan semua logika dari:
 *  - proses_login.php   → method login(), loginAdmin(), loginMahasiswa()
 *  - proses_register.php → method register()
 *
 * File proses_login.php & proses_register.php tetap ada,
 * tapi isinya tinggal 3 langkah: ambil input → panggil controller → redirect.
 */

class AuthController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // =========================================================
    //  LOGIN
    // =========================================================

    /**
     * Entry point login — dipanggil dari proses_login.php
     *
     * @return array{success: bool, role: string, data: array, error: string}
     */
    public function login(string $login, string $password): array
    {
        // Validasi input kosong
        if ($login === '' || $password === '') {
            return $this->fail('empty');
        }

        // Cek admin dulu
        $result = $this->loginAdmin($login, $password);
        if ($result['success']) {
            return $result;
        }

        // Cek mahasiswa
        $result = $this->loginMahasiswa($login, $password);
        if ($result['success']) {
            return $result;
        }

        return $this->fail('invalid');
    }

    /**
     * Cek login di tabel admin (berdasarkan username).
     * Dipindahkan dari proses_login.php bagian 1.
     */
    private function loginAdmin(string $username, string $password): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id_admin, username, password
            FROM   admin
            WHERE  username = :login
            LIMIT  1
        ");
        $stmt->execute([':login' => $username]);
        $admin = $stmt->fetch();

        if (!$admin || !$this->verifyPassword($password, $admin['password'])) {
            return $this->fail('invalid');
        }

        return [
            'success' => true,
            'error'   => '',
            'role'    => 'admin',
            'data'    => [
                'id_admin'       => (int) $admin['id_admin'],
                'admin_username' => $admin['username'],
            ],
        ];
    }

    /**
     * Cek login di tabel mahasiswa (berdasarkan NPM).
     * Dipindahkan dari proses_login.php bagian 2.
     */
    private function loginMahasiswa(string $npm, string $password): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id_mahasiswa, npm, nama, password
            FROM   mahasiswa
            WHERE  npm = :login
            LIMIT  1
        ");
        $stmt->execute([':login' => $npm]);
        $mahasiswa = $stmt->fetch();

        if (!$mahasiswa || !$this->verifyPassword($password, $mahasiswa['password'])) {
            return $this->fail('invalid');
        }

        return [
            'success' => true,
            'error'   => '',
            'role'    => 'mahasiswa',
            'data'    => [
                'id_mahasiswa' => $mahasiswa['id_mahasiswa'],
                'npm'          => $mahasiswa['npm'],
                'nama'         => $mahasiswa['nama'] ?? $mahasiswa['npm'],
            ],
        ];
    }

    // =========================================================
    //  REGISTER
    // =========================================================

    /**
     * Registrasi mahasiswa baru — dipanggil dari proses_register.php
     *
     * @return array{success: bool, error: string, npm: string}
     */
    public function register(string $npm, string $password, string $confirm): array
    {
        // Validasi kosong
        if ($npm === '' || $password === '' || $confirm === '') {
            return $this->fail('empty', $npm);
        }

        // Panjang password minimal 6
        if (strlen($password) < 6) {
            return $this->fail('short_pw', $npm);
        }

        // Konfirmasi password harus cocok
        if ($password !== $confirm) {
            return $this->fail('mismatch', $npm);
        }

        // Cek NPM sudah terdaftar
        $cek = $this->pdo->prepare("
            SELECT npm FROM mahasiswa WHERE npm = :npm LIMIT 1
        ");
        $cek->execute([':npm' => $npm]);

        if ($cek->fetch()) {
            return $this->fail('exists', $npm);
        }

        // Hash & simpan
        $this->pdo->prepare("
            INSERT INTO mahasiswa (npm, password)
            VALUES (:npm, :password)
        ")->execute([
            ':npm'      => $npm,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return ['success' => true, 'error' => '', 'npm' => $npm];
    }

    // =========================================================
    //  SESSION
    // =========================================================

    /**
     * Simpan data ke $_SESSION setelah login berhasil.
     * Dipanggil dari proses_login.php setelah login() sukses.
     */
    public function saveSession(array $result): void
    {
        session_regenerate_id(true);

        $_SESSION['logged_in'] = true;
        $_SESSION['role']      = $result['role'];

        // Tulis semua data ke session sekaligus
        foreach ($result['data'] as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }

    // =========================================================
    //  PRIVATE HELPERS
    // =========================================================

    /**
     * Verifikasi password — mendukung bcrypt hash & plain text lama.
     *
     * Fix PHP 8.x: password_get_info() mengembalikan ['algo' => NULL]
     * untuk plain text (bukan 0 seperti PHP 7.x), maka kita cek
     * via prefix string langsung supaya benar di semua versi PHP.
     */
    private function verifyPassword(string $input, string $stored): bool
    {
        // Bcrypt hash: panjang 60 karakter & diawali '$2y$' atau '$2a$'
        $isBcrypt = strlen($stored) === 60
                 && (str_starts_with($stored, '$2y$')
                  || str_starts_with($stored, '$2a$'));

        if ($isBcrypt) {
            return password_verify($input, $stored);
        }

        // Plain text lama (timing-attack resistant)
        return hash_equals($stored, $input);
    }

    /**
     * Shortcut kembalikan array gagal.
     */
    private function fail(string $error, string $npm = ''): array
    {
        return [
            'success' => false,
            'error'   => $error,
            'role'    => '',
            'npm'     => $npm,
            'data'    => [],
        ];
    }
}