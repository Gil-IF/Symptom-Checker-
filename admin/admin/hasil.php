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
    <link rel="stylesheet" href="../assets/css/hasil.css" />
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