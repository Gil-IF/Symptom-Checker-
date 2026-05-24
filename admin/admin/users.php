<?php
// admin/users.php
session_start();

// Cek login admin
if (!isset($_SESSION['id_admin']) && !isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

$adminName = $_SESSION['admin']['username'] ?? $_SESSION['admin_username'] ?? 'Admin';
$message = '';
$error = '';

// Proses tambah / edit / reset password user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $npm = trim($_POST['npm'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $password = $_POST['password'] ?? '';
        $id_mahasiswa = $_POST['id_mahasiswa'] ?? '';

        // Validasi
        if ($npm === '' || $nama === '') {
            $error = 'NPM dan Nama wajib diisi.';
        } elseif ($action === 'add' && $password === '') {
            $error = 'Password wajib diisi untuk user baru.';
        } elseif (!preg_match('/^\d{10,12}$/', $npm)) {
            $error = 'NPM harus berupa angka 10-12 digit.';
        } else {
            try {
                if ($action === 'add') {
                    // Cek NPM sudah ada
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE npm = ?");
                    $stmt->execute([$npm]);
                    if ($stmt->fetchColumn() > 0) {
                        $error = 'NPM sudah terdaftar.';
                    } else {
                        // Generate ID mahasiswa baru (IDMxxx)
                        $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(id_mahasiswa, 4) AS UNSIGNED)) FROM mahasiswa");
                        $lastNum = $stmt->fetchColumn();
                        $nextNum = ($lastNum ? $lastNum + 1 : 1);
                        $newId = 'IDM' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO mahasiswa (id_mahasiswa, npm, nama, password, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $stmt->execute([$newId, $npm, $nama, $hashedPassword]);
                        $message = 'Mahasiswa berhasil ditambahkan.';
                    }
                } elseif ($action === 'edit' && $id_mahasiswa) {
                    // Cek apakah mahasiswa ada
                    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa = ?");
                    $stmt->execute([$id_mahasiswa]);
                    if (!$stmt->fetch()) {
                        $error = 'Mahasiswa tidak ditemukan.';
                    } else {
                        $sql = "UPDATE mahasiswa SET npm = ?, nama = ?";
                        $params = [$npm, $nama];

                        // Update password hanya jika diisi
                        if (!empty($password)) {
                            $sql .= ", password = ?";
                            $params[] = password_hash($password, PASSWORD_DEFAULT);
                        }
                        $sql .= " WHERE id_mahasiswa = ?";
                        $params[] = $id_mahasiswa;

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $message = 'Data mahasiswa berhasil diperbarui.';
                    }
                }
            } catch (PDOException $e) {
                $error = 'Gagal menyimpan: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'reset_password') {
        // Reset password oleh admin
        $id_mahasiswa = $_POST['id_mahasiswa'] ?? '';
        $new_password = $_POST['password'] ?? '';

        if (empty($id_mahasiswa)) {
            $error = 'ID mahasiswa tidak valid.';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password baru minimal 6 karakter.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id_mahasiswa, nama FROM mahasiswa WHERE id_mahasiswa = ?");
                $stmt->execute([$id_mahasiswa]);
                $user = $stmt->fetch();
                if (!$user) {
                    $error = 'Mahasiswa tidak ditemukan.';
                } else {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE mahasiswa SET password = ? WHERE id_mahasiswa = ?");
                    $stmt->execute([$hashed, $id_mahasiswa]);
                    $message = 'Password untuk ' . htmlspecialchars($user['nama']) . ' berhasil direset.';
                }
            } catch (PDOException $e) {
                $error = 'Gagal mereset password: ' . $e->getMessage();
            }
        }
    }
}

// Proses hapus user
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM skrining WHERE id_mahasiswa = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $error = 'Tidak dapat menghapus mahasiswa yang memiliki data skrining. Hapus skrining terlebih dahulu.';
        } else {
            $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE id_mahasiswa = ?");
            $stmt->execute([$id]);
            $message = 'Mahasiswa berhasil dihapus.';
        }
    } catch (PDOException $e) {
        $error = 'Gagal menghapus: ' . $e->getMessage();
    }
}

// Ambil data mahasiswa
$users = [];
try {
    $stmt = $pdo->query("SELECT id_mahasiswa, npm, nama, created_at FROM mahasiswa ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Gagal mengambil data: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Users - Admin Symptom Checker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* (CSS tetap sama seperti kode sebelumnya) */
        :root{
            --bg:#f3f3f5; --panel:#ffffff; --text:#2f3137; --muted:#7a7f87;
            --shadow:0 10px 26px rgba(0,0,0,.08); --sidebar:#ffffff;
            --accent:#cbb2e1; --blue:#6ea3d9; --danger:#ff7b7b;
            --warn:#ffd36d; --success:#aee9cd; --line:#ececf1;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}
        a{text-decoration:none;color:inherit}
        .layout{display:grid;grid-template-columns:280px 1fr;min-height:100vh}
        .sidebar{
            background:var(--sidebar); box-shadow:var(--shadow); padding:18px 16px;
            position:sticky; top:0; height:100vh; display:flex; flex-direction:column; gap:18px;
        }
        .brand{display:flex; align-items:center; gap:12px; padding:6px 4px 14px;}
        .brand img{width:44px;height:44px;object-fit:contain}
        .brand .title{font-size:24px;font-weight:800;line-height:1}
        .brand .sub{font-size:14px;line-height:1;color:#2f3137;opacity:.9}
        .menu-label{font-size:12px;color:#a3a3a8;font-weight:700;letter-spacing:.08em;margin:4px 0 8px 6px}
        .nav{display:flex;flex-direction:column;gap:8px}
        .nav a{display:flex; align-items:center; gap:12px; padding:14px 14px; border-radius:16px; color:#262626; font-weight:600;}
        .nav a.active,.nav a:hover{background:#efefef}
        .nav .ico{width:22px;text-align:center;font-size:20px}
        .sidebar-footer{margin-top:auto; display:flex; flex-direction:column; gap:10px}
        .admin-box{background:#efefef; border-radius:18px; padding:12px 14px; display:flex; gap:12px; align-items:center;}
        .avatar{width:38px;height:38px;border-radius:50%;background:#111;display:grid;place-items:center;color:#fff;font-weight:700}
        .admin-meta{line-height:1.2}
        .admin-meta .name{font-weight:700}
        .admin-meta .role{font-size:12px;color:var(--muted)}
        .logout{color:#e53935; font-weight:700; padding:10px 14px; border-radius:14px; display:flex; align-items:center; gap:12px;}
        .logout:hover{background:#fff1f1}

        .content{padding:22px 24px 28px}
        .page-header{display:flex; justify-content:space-between; align-items:center; margin-bottom:24px}
        .page-header h1{font-size:28px; font-weight:700}
        .btn{padding:10px 20px; border-radius:12px; font-weight:600; font-size:14px; cursor:pointer; border:none; transition:.2s}
        .btn-primary{background:var(--blue); color:#fff}
        .btn-primary:hover{opacity:.9}
        .btn-danger{background:#ff4d4d; color:#fff}
        .btn-warning{background:#ffc107; color:#000}
        .alert{padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:500}
        .alert-error{background:#ffe0e0; color:#b71c1c}
        .alert-success{background:#e0ffe0; color:#1b5e20}
        .table-wrapper{background:var(--panel); border-radius:20px; box-shadow:var(--shadow); padding:20px; overflow-x:auto}
        table{width:100%; border-collapse:collapse}
        th,td{padding:14px 12px; text-align:left; border-bottom:1px solid var(--line)}
        th{font-weight:700; font-size:13px; color:var(--muted)}
        td{font-size:14px}
        .actions{display:flex; gap:8px}
        .actions button,.actions a{background:none; border:none; cursor:pointer; font-size:18px; padding:4px 8px; border-radius:8px}
        .actions .edit-btn:hover{background:#e3f2fd}
        .actions .delete-btn:hover{background:#ffebee}
        .actions .reset-btn:hover{background:#fff3e0}

        /* Modal */
        .modal-overlay{position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; z-index:1000; visibility:hidden; opacity:0; transition:.2s}
        .modal-overlay.active{visibility:visible; opacity:1}
        .modal{background:#fff; border-radius:24px; width:min(480px,90vw); padding:24px; box-shadow:0 20px 40px rgba(0,0,0,.2)}
        .modal h2{margin-bottom:16px}
        .form-group{margin-bottom:14px}
        .form-group label{display:block; font-weight:600; font-size:13px; margin-bottom:6px}
        .form-group input{width:100%; padding:12px 14px; border:1px solid #ddd; border-radius:12px; font-family:inherit; font-size:14px}
        .form-group input[readonly]{background:#f0f0f0; color:#555}
        .form-actions{display:flex; gap:12px; justify-content:flex-end; margin-top:18px}
        @media (max-width:1200px){ .layout{grid-template-columns:1fr} .sidebar{position:relative;height:auto} }
        @media (max-width:720px){ .content{padding:16px} }
    </style>
</head>
<body>
<div class="layout">
    <!-- SIDEBAR (sama dengan sebelumnya) -->
    <aside class="sidebar">
        <a href="index.php" class="brand">
            <img src="../assets/img/logo.png" alt="Logo Symptom Checker">
            <div>
                <div class="title">Symptom</div>
                <div class="sub">Checker</div>
            </div>
        </a>
        <div>
            <div class="menu-label">MENU</div>
            <nav class="nav">
                <a href="index.php"><span class="ico">⌂</span>Dashboard</a>
                <a href="users.php" class="active"><span class="ico">👥</span>Users</a>
                <a href="hasil.php"><span class="ico">🗂</span>Screening Results</a>
                <a href="analytics.php"><span class="ico">📊</span>Analytics</a>
                <a href="settings.php"><span class="ico">⚙</span>Settings</a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <div class="admin-box">
                <div class="avatar">A</div>
                <div class="admin-meta">
                    <div class="name"><?= htmlspecialchars($adminName); ?></div>
                    <div class="role">Super Admin</div>
                </div>
            </div>
            <a class="logout" href="../logout.php">⤿ Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="content">
        <div class="page-header">
            <h1>👥 Kelola Mahasiswa</h1>
            <button class="btn btn-primary" onclick="openAddModal()">+ Tambah Mahasiswa</button>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NPM</th>
                        <th>Nama</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:30px; color:var(--muted)">Belum ada mahasiswa terdaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['id_mahasiswa']); ?></td>
                            <td><?= htmlspecialchars($u['npm']); ?></td>
                            <td><?= htmlspecialchars($u['nama']); ?></td>
                            <td><?= date('d M Y', strtotime($u['created_at'])); ?></td>
                            <td class="actions">
                                <button class="edit-btn" title="Edit"
                                    onclick='openEditModal(<?= json_encode($u); ?>)'>✏️</button>
                                <button class="reset-btn" title="Reset Password"
                                    onclick='openResetModal(<?= json_encode($u); ?>)'>🔑</button>
                                <a href="?action=delete&id=<?= urlencode($u['id_mahasiswa']); ?>" 
                                   class="delete-btn" title="Hapus"
                                   onclick="return confirm('Yakin ingin menghapus <?= htmlspecialchars($u['nama']); ?>?')">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- MODAL TAMBAH / EDIT (sebelumnya, tidak diubah) -->
<div class="modal-overlay" id="userModal">
    <div class="modal">
        <h2 id="modalTitle">Tambah Mahasiswa</h2>
        <form method="post" action="users.php">
            <input type="hidden" name="action" id="modalAction" value="add">
            <input type="hidden" name="id_mahasiswa" id="modalId">

            <div class="form-group">
                <label for="npm">NPM</label>
                <input type="text" name="npm" id="modalNpm" placeholder="Masukkan NPM" required maxlength="20">
            </div>

            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="modalNama" placeholder="Masukkan nama" required maxlength="100">
            </div>

            <div class="form-group">
                <label for="password">Password <small id="passwordHint">(Min. 6 karakter)</small></label>
                <input type="password" name="password" id="modalPassword" placeholder="Password" minlength="6">
            </div>

            <div class="form-actions">
                <button type="button" class="btn" onclick="closeModal()" style="background:#eee">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL RESET PASSWORD (baru) -->
<div class="modal-overlay" id="resetModal">
    <div class="modal">
        <h2>🔑 Reset Password Mahasiswa</h2>
        <form method="post" action="users.php">
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="id_mahasiswa" id="resetId">

            <div class="form-group">
                <label>NPM</label>
                <input type="text" id="resetNpm" readonly>
            </div>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" id="resetNama" readonly>
            </div>

            <div class="form-group">
                <label for="newPassword">Password Baru <small>(Min. 6 karakter)</small></label>
                <input type="password" name="password" id="newPassword" placeholder="Masukkan password baru" minlength="6" required>
            </div>

            <div class="form-actions">
                <button type="button" class="btn" onclick="closeResetModal()" style="background:#eee">Batal</button>
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('userModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalAction = document.getElementById('modalAction');
    const modalId = document.getElementById('modalId');
    const modalNpm = document.getElementById('modalNpm');
    const modalNama = document.getElementById('modalNama');
    const modalPassword = document.getElementById('modalPassword');
    const passwordHint = document.getElementById('passwordHint');

    function openAddModal() {
        modalTitle.textContent = 'Tambah Mahasiswa';
        modalAction.value = 'add';
        modalId.value = '';
        modalNpm.value = '';
        modalNama.value = '';
        modalPassword.value = '';
        modalPassword.required = true;
        passwordHint.textContent = '(Min. 6 karakter, wajib)';
        modal.classList.add('active');
    }

    function openEditModal(user) {
        modalTitle.textContent = 'Edit Mahasiswa';
        modalAction.value = 'edit';
        modalId.value = user.id_mahasiswa;
        modalNpm.value = user.npm;
        modalNama.value = user.nama;
        modalPassword.value = '';
        modalPassword.required = false;
        passwordHint.textContent = '(Kosongkan jika tidak ingin mengubah)';
        modal.classList.add('active');
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    // RESET MODAL
    const resetModal = document.getElementById('resetModal');
    const resetId = document.getElementById('resetId');
    const resetNpm = document.getElementById('resetNpm');
    const resetNama = document.getElementById('resetNama');
    const newPassword = document.getElementById('newPassword');

    function openResetModal(user) {
        resetId.value = user.id_mahasiswa;
        resetNpm.value = user.npm;
        resetNama.value = user.nama;
        newPassword.value = '';
        resetModal.classList.add('active');
    }

    function closeResetModal() {
        resetModal.classList.remove('active');
    }

    // Tutup modal jika klik di luar
    window.onclick = function(event) {
        if (event.target === modal) closeModal();
        if (event.target === resetModal) closeResetModal();
    }
</script>
</body>
</html>