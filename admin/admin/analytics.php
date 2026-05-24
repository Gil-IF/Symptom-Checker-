<?php
// admin/analytics.php
session_start();

// Cek login admin
if (!isset($_SESSION['id_admin']) && !isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

$adminName = $_SESSION['admin']['username'] ?? $_SESSION['admin_username'] ?? 'Admin';

// Filter tahun (default tahun sekarang)
$tahun = (int) ($_GET['tahun'] ?? date('Y'));

// ============================================================
// QUERY DATA
// ============================================================
try {
    // Total skrining (semua waktu)
    $totalSkrining = (int) $pdo->query("SELECT COUNT(*) FROM skrining")->fetchColumn();

    // Total mahasiswa unik yang pernah skrining
    $totalUsers = (int) $pdo->query("SELECT COUNT(DISTINCT id_mahasiswa) FROM skrining")->fetchColumn();

    // Rata-rata skor per subskala
    $avgDepresi = (float) ($pdo->query("SELECT AVG(skor_depresi) FROM skrining")->fetchColumn() ?? 0);
    $avgAnxiety = (float) ($pdo->query("SELECT AVG(skor_anxiety) FROM skrining")->fetchColumn() ?? 0);
    $avgStress  = (float) ($pdo->query("SELECT AVG(skor_stress) FROM skrining")->fetchColumn() ?? 0);

    // Skrining bulan ini
    $bulanIni = date('m');
    $skriningBulanIni = (int) $pdo->query("SELECT COUNT(*) FROM skrining WHERE YEAR(tgl_skrining) = $tahun AND MONTH(tgl_skrining) = $bulanIni")->fetchColumn();

    // Tren bulanan dalam setahun
    $stmtTrend = $pdo->prepare("
        SELECT MONTH(tgl_skrining) AS bulan, COUNT(*) AS total
        FROM skrining
        WHERE YEAR(tgl_skrining) = :tahun
        GROUP BY bulan
        ORDER BY bulan
    ");
    $stmtTrend->execute(['tahun' => $tahun]);
    $trendData = $stmtTrend->fetchAll(PDO::FETCH_ASSOC);

    $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $trendSeries = array_fill(0, 12, 0);
    foreach ($trendData as $row) {
        $index = (int)$row['bulan'] - 1;
        $trendSeries[$index] = (int)$row['total'];
    }

    // Distribusi kategori Depresi
    $stmtDep = $pdo->query("
        SELECT kd.nama_kategori, COUNT(*) AS total
        FROM skrining s
        JOIN kategori_subskala kd ON kd.id_subskala = 'IDU001' AND s.skor_depresi BETWEEN kd.rentang_min AND kd.rentang_max
        GROUP BY kd.nama_kategori
        ORDER BY FIELD(kd.nama_kategori, 'Normal','Ringan','Sedang','Berat','Sangat Berat')
    ");
    $distDepresi = $stmtDep->fetchAll(PDO::FETCH_ASSOC);

    // Distribusi Anxiety
    $stmtAnx = $pdo->query("
        SELECT ka.nama_kategori, COUNT(*) AS total
        FROM skrining s
        JOIN kategori_subskala ka ON ka.id_subskala = 'IDU002' AND s.skor_anxiety BETWEEN ka.rentang_min AND ka.rentang_max
        GROUP BY ka.nama_kategori
        ORDER BY FIELD(ka.nama_kategori, 'Normal','Ringan','Sedang','Berat','Sangat Berat')
    ");
    $distAnxiety = $stmtAnx->fetchAll(PDO::FETCH_ASSOC);

    // Distribusi Stress
    $stmtStr = $pdo->query("
        SELECT ks.nama_kategori, COUNT(*) AS total
        FROM skrining s
        JOIN kategori_subskala ks ON ks.id_subskala = 'IDU003' AND s.skor_stress BETWEEN ks.rentang_min AND ks.rentang_max
        GROUP BY ks.nama_kategori
        ORDER BY FIELD(ks.nama_kategori, 'Normal','Ringan','Sedang','Berat','Sangat Berat')
    ");
    $distStress = $stmtStr->fetchAll(PDO::FETCH_ASSOC);

    // 5 skrining terbaru
    $recentStmt = $pdo->query("
        SELECT s.id_skrining, s.tgl_skrining, m.npm, m.nama,
               (s.skor_depresi + s.skor_anxiety + s.skor_stress) AS skor_total,
               COALESCE(kd.nama_kategori,'?') AS kategori_depresi,
               COALESCE(ka.nama_kategori,'?') AS kategori_anxiety,
               COALESCE(ks.nama_kategori,'?') AS kategori_stress
        FROM skrining s
        JOIN mahasiswa m ON m.id_mahasiswa = s.id_mahasiswa
        LEFT JOIN kategori_subskala kd ON kd.id_subskala = 'IDU001' AND s.skor_depresi BETWEEN kd.rentang_min AND kd.rentang_max
        LEFT JOIN kategori_subskala ka ON ka.id_subskala = 'IDU002' AND s.skor_anxiety BETWEEN ka.rentang_min AND ka.rentang_max
        LEFT JOIN kategori_subskala ks ON ks.id_subskala = 'IDU003' AND s.skor_stress BETWEEN ks.rentang_min AND ks.rentang_max
        ORDER BY s.tgl_skrining DESC
        LIMIT 5
    ");
    $recent = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    die('Query error: ' . $e->getMessage());
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

// Helper: ubah array distribusi jadi [nama => total]
function distToArray(array $data): array {
    $res = [];
    foreach ($data as $row) {
        $res[$row['nama_kategori']] = (int)$row['total'];
    }
    return $res;
}

$depData = distToArray($distDepresi);
$anxData = distToArray($distAnxiety);
$strData = distToArray($distStress);

$depLabels = array_keys($depData);
$depValues = array_values($depData);
$anxLabels = array_keys($anxData);
$anxValues = array_values($anxData);
$strLabels = array_keys($strData);
$strValues = array_values($strData);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Analytics - Admin Symptom Checker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="../assets/css/analytics.css" />
</head>
<body>
<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="index.php" class="brand">
            <img src="../assets/img/logo.png" alt="Logo">
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
                <a href="hasil.php"><span class="ico">🗂</span>Screening Results</a>
                <a href="analytics.php" class="active"><span class="ico">📊</span>Analytics</a>
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

    <main class="content">
        <div class="page-header">
            <h1>📊 Analytics</h1>
            <form class="filter-year" method="get">
                <label>Tahun:</label>
                <select name="tahun" onchange="this.form.submit()">
                    <?php for ($y = date('Y')-5; $y <= date('Y'); $y++): ?>
                        <option value="<?= $y; ?>" <?= $y == $tahun ? 'selected' : ''; ?>><?= $y; ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <!-- KPI -->
        <section class="cards">
            <div class="card blue">
                <div class="icon">📋</div>
                <div class="kpi">
                    <div class="label">Total Skrining</div>
                    <div class="value"><?= number_format($totalSkrining); ?></div>
                    <div class="sub">Semua waktu</div>
                </div>
            </div>
            <div class="card purple">
                <div class="icon">👥</div>
                <div class="kpi">
                    <div class="label">Mahasiswa Unik</div>
                    <div class="value"><?= number_format($totalUsers); ?></div>
                    <div class="sub">Pernah skrining</div>
                </div>
            </div>
            <div class="card red">
                <div class="icon">📅</div>
                <div class="kpi">
                    <div class="label">Bulan Ini</div>
                    <div class="value"><?= number_format($skriningBulanIni); ?></div>
                    <div class="sub"><?= date('F Y'); ?></div>
                </div>
            </div>
            <div class="card green">
                <div class="icon">📈</div>
                <div class="kpi">
                    <div class="label">Rata‑rata Skor</div>
                    <div class="value" style="font-size:20px;">D:<?= round($avgDepresi,1); ?> A:<?= round($avgAnxiety,1); ?> S:<?= round($avgStress,1); ?></div>
                    <div class="sub">Depresi / Kecemasan / Stres</div>
                </div>
            </div>
        </section>

        <!-- Tren & Recent -->
        <section class="grid-2">
            <div class="panel">
                <div class="panel-head">
                    <h2>Tren Bulanan (<?= $tahun; ?>)</h2>
                </div>
                <div class="chart-wrap">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            <div class="panel">
                <div class="panel-head">
                    <h2>Skrining Terbaru</h2>
                    <a href="hasil.php" class="btn-sm" style="text-decoration:none;">Lihat Semua</a>
                </div>
                <div class="table-wrapper" style="box-shadow:none; padding:0;">
                    <table>
                        <thead><tr><th>NPM</th><th>Tanggal</th><th>Risk</th></tr></thead>
                        <tbody>
                            <?php if (empty($recent)): ?>
                                <tr><td colspan="3" style="text-align:center; color:var(--muted)">Belum ada data.</td></tr>
                            <?php else: foreach ($recent as $r): 
                                $risk = getRiskLevel($r['kategori_depresi'], $r['kategori_anxiety'], $r['kategori_stress']);
                            ?>
                        <tr>
                            <td><?= htmlspecialchars($r['npm']); ?></td>
                            <td><?= date('d/m/Y', strtotime($r['tgl_skrining'])); ?></td>
                            <td><span class="badge <?= getRiskBadgeClass($risk); ?>"><?= $risk; ?></span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Pie Charts Kategori (FIXED) -->
        <section class="pie-row">
            <div class="pie-panel">
                <h3>Distribusi Depresi</h3>
                <div style="width:100%; height:200px; position:relative;">
                    <canvas id="pieDepresi" height="200"></canvas>
                </div>
            </div>
            <div class="pie-panel">
                <h3>Distribusi Kecemasan</h3>
                <div style="width:100%; height:200px; position:relative;">
                    <canvas id="pieAnxiety" height="200"></canvas>
                </div>
            </div>
            <div class="pie-panel">
                <h3>Distribusi Stres</h3>
                <div style="width:100%; height:200px; position:relative;">
                    <canvas id="pieStress" height="200"></canvas>
                </div>
            </div>
        </section>
    </main>
</div>

<script src="../assets/js/admin.js"></script>
</body>
</html>