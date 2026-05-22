<?php
// admin/hasil.php
session_start();

// Cek login admin
if (!isset($_SESSION['id_admin']) && !isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

$adminName = $_SESSION['admin']['username'] ?? $_SESSION['admin_username'] ?? 'Admin';

// Filter tanggal (opsional)
$filterStart = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
$filterEnd   = $_GET['end']   ?? date('Y-m-d');

try {
    // Query dengan join + COALESCE untuk menghindari null
    $sql = "
        SELECT 
            s.id_skrining,
            s.tgl_skrining,
            s.skor_depresi,
            s.skor_anxiety,
            s.skor_stress,
            (s.skor_depresi + s.skor_anxiety + s.skor_stress) AS skor_total,
            m.npm,
            m.nama,
            COALESCE(kd.nama_kategori, 'Tidak diketahui') AS kategori_depresi,
            COALESCE(ka.nama_kategori, 'Tidak diketahui') AS kategori_anxiety,
            COALESCE(ks.nama_kategori, 'Tidak diketahui') AS kategori_stress
        FROM skrining s
        JOIN mahasiswa m ON m.id_mahasiswa = s.id_mahasiswa
        LEFT JOIN kategori_subskala kd 
            ON kd.id_subskala = 'IDU001' 
            AND s.skor_depresi BETWEEN kd.rentang_min AND kd.rentang_max
        LEFT JOIN kategori_subskala ka 
            ON ka.id_subskala = 'IDU002' 
            AND s.skor_anxiety BETWEEN ka.rentang_min AND ka.rentang_max
        LEFT JOIN kategori_subskala ks 
            ON ks.id_subskala = 'IDU003' 
            AND s.skor_stress BETWEEN ks.rentang_min AND ks.rentang_max
        WHERE DATE(s.tgl_skrining) BETWEEN :start AND :end
        ORDER BY s.tgl_skrining DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':start' => $filterStart, ':end' => $filterEnd]);
    $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}

function getRiskLevel(?string $depresi, ?string $anxiety, ?string $stress): string
{
    // Prioritas: Sangat Berat > Berat > Sedang > Ringan > Normal
    $levels = [$depresi, $anxiety, $stress];

    if (in_array('Sangat Berat', $levels)) {
        return 'Sangat Berat';
    }
    if (in_array('Berat', $levels)) {
        return 'Berat';
    }
    if (in_array('Sedang', $levels)) {
        return 'Sedang';
    }
    if (in_array('Ringan', $levels)) {
        return 'Ringan';
    }
    return 'Normal';
}

function getRiskBadgeClass(string $risk): string
{
    return match ($risk) {
        'Sangat Berat' => 'badge-danger',
        'Berat'        => 'badge-danger',
        'Sedang'       => 'badge-warning',
        'Ringan'       => 'badge-warning',
        default        => 'badge-success'  // Normal
    };
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hasil Skrining - Admin Symptom Checker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* --- CSS Global (sama dengan dashboard) --- */
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
        .page-header{display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px}
        .page-header h1{font-size:28px; font-weight:700}

        /* Filter */
        .filter-form{display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; margin-bottom:20px}
        .filter-group{display:flex; flex-direction:column; gap:4px}
        .filter-group label{font-size:12px; font-weight:600; color:var(--muted)}
        .filter-group input{height:38px; padding:0 12px; border:1px solid #ddd; border-radius:8px; font-family:inherit}
        .btn{padding:8px 16px; border-radius:8px; font-weight:600; font-size:13px; cursor:pointer; border:none; transition:.2s}
        .btn-primary{background:var(--blue); color:#fff}
        .btn-primary:hover{opacity:.9}
        .btn-outline{background:#fff; border:1px solid #ddd; color:var(--text)}
        .btn-outline:hover{background:#f5f5f5}
        .btn-sm{padding:6px 12px; font-size:12px}

        /* Tabel */
        .table-wrapper{background:var(--panel); border-radius:20px; box-shadow:var(--shadow); padding:20px; overflow-x:auto}
        table{width:100%; border-collapse:collapse}
        th,td{padding:14px 12px; text-align:left; border-bottom:1px solid var(--line)}
        th{font-weight:700; font-size:13px; color:var(--muted)}
        td{font-size:14px}
        .badge{display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700}
        .badge-success{background:#daf6db; color:#207a2a}
        .badge-warning{background:#fff0c2; color:#9a7100}
        .badge-danger{background:#ffd8d8; color:#b42318}

        @media (max-width: 1200px){
            .layout{grid-template-columns:1fr}
            .sidebar{position:relative;height:auto}
        }
        @media (max-width: 720px){
            .content{padding:16px}
            .page-header h1{font-size:22px}
        }
    </style>
</head>
<body>
<div class="layout">
    <!-- Sidebar -->
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
                <a href="users.php"><span class="ico">👥</span>Users</a>
                <a href="hasil.php" class="active"><span class="ico">🗂</span>Screening Results</a>
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

    <!-- Main Content -->
    <main class="content">
        <div class="page-header">
            <h1>📋 Hasil Skrining</h1>
            <form class="filter-form" method="get">
                <div class="filter-group">
                    <label for="start">Dari Tanggal</label>
                    <input type="date" id="start" name="start" value="<?= htmlspecialchars($filterStart); ?>">
                </div>
                <div class="filter-group">
                    <label for="end">Sampai Tanggal</label>
                    <input type="date" id="end" name="end" value="<?= htmlspecialchars($filterEnd); ?>">
                </div>
                <button type="submit" class="btn btn-outline" style="height:38px;">Filter</button>
                <a href="hasil.php" class="btn btn-outline" style="height:38px;">Reset</a>
            </form>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>NPM / Nama</th>
                        <th>Tanggal Skrining</th>
                        <th>Risk Level</th>
                        <th>Skor Total</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($hasil)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:40px; color:var(--muted);">
                                Tidak ada hasil skrining pada periode ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($hasil as $row): ?>
                            <?php
                                $risk = getRiskLevel($row['kategori_depresi'], $row['kategori_anxiety'], $row['kategori_stress']);
                                $badgeClass = getRiskBadgeClass($risk);
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600;"><?= htmlspecialchars($row['npm']); ?></div>
                                    <div style="font-size:12px; color:var(--muted);"><?= htmlspecialchars($row['nama']); ?></div>
                                </td>
                                <td><?= date('d M Y, H:i', strtotime($row['tgl_skrining'])); ?></td>
                                <td><span class="badge <?= $badgeClass; ?>"><?= $risk; ?></span></td>
                                <td><?= $row['skor_total']; ?></td>
                                <td>
                                    <a href="../skrining/result.php?id_skrining=<?= urlencode($row['id_skrining']); ?>" 
                                       target="_blank" class="btn btn-primary btn-sm">🔍 Lihat Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>