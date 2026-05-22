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
    <style>
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
        .page-header{display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap}
        .page-header h1{font-size:28px; font-weight:700}
        .filter-year{display:flex; gap:10px; align-items:center}
        .filter-year select{padding:8px 12px; border-radius:8px; border:1px solid #ddd; font-family:inherit}
        .btn-sm{padding:6px 12px; font-size:12px; border-radius:8px; border:none; cursor:pointer; background:var(--blue); color:#fff; font-weight:600}

        .cards{display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px}
        .card{background:var(--panel); border-radius:20px; box-shadow:var(--shadow); padding:16px 18px; display:flex; gap:14px; align-items:center}
        .card .icon{width:54px;height:54px;border-radius:14px;display:grid;place-items:center;font-size:24px;flex:0 0 auto}
        .card.blue .icon{background:#dbe8ff;color:#3257ff}
        .card.purple .icon{background:#f0d4ff;color:#a323c8}
        .card.red .icon{background:#ffd8d8;color:#d93025}
        .card.green .icon{background:#e1f7df;color:#2e7d32}
        .card .kpi .label{font-size:13px;font-weight:700;color:#222}
        .card .kpi .value{font-size:26px;font-weight:800;line-height:1.15;margin-top:2px}
        .card .kpi .sub{font-size:12px;color:var(--muted);margin-top:4px}

        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px; margin-bottom:24px}
        .panel{background:var(--panel);border-radius:20px;box-shadow:var(--shadow);padding:16px 18px}
        .panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:10px}
        .panel-head h2{font-size:16px}
        .chart-wrap{height:280px}

        .pie-row{display:grid;grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px}
        .pie-panel{background:var(--panel);border-radius:20px;box-shadow:var(--shadow);padding:16px 18px; text-align:center}
        .pie-panel h3{margin-bottom:12px; font-size:15px}

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
            .cards{grid-template-columns:repeat(2,1fr)}
            .grid-2{grid-template-columns:1fr}
            .pie-row{grid-template-columns:1fr}
        }
        @media (max-width: 720px){
            .content{padding:16px}
            .cards{grid-template-columns:1fr}
        }
    </style>
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

<script>
// Data dari PHP
const depLabels = <?= json_encode($depLabels); ?>;
const depValues = <?= json_encode($depValues); ?>;
const anxLabels = <?= json_encode($anxLabels); ?>;
const anxValues = <?= json_encode($anxValues); ?>;
const strLabels = <?= json_encode($strLabels); ?>;
const strValues = <?= json_encode($strValues); ?>;
const bulanLabels = <?= json_encode($bulanLabels); ?>;
const trendSeries = <?= json_encode($trendSeries); ?>;

// Warna default
const colors = ['#aee9cd','#cbb2e1','#6ea3d9','#ffd36d','#ff7b7b'];

// Fungsi buat doughnut chart dengan fallback
function createDoughnut(canvasId, labels, values) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    let finalLabels = labels;
    let finalValues = values;
    if (labels.length === 0 || values.every(v => v === 0)) {
        finalLabels = ['Tidak ada data'];
        finalValues = [1];
    }

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: finalLabels,
            datasets: [{
                data: finalValues,
                backgroundColor: colors.slice(0, finalLabels.length),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 } }
                }
            }
        }
    });
}

// Tren bar chart
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Jumlah Skrining',
                data: trendSeries,
                backgroundColor: 'rgba(110,163,217,0.6)',
                borderColor: '#6ea3d9',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#eef0f4' } }
            }
        }
    });
}

// Inisialisasi ketiga pie chart
createDoughnut('pieDepresi', depLabels, depValues);
createDoughnut('pieAnxiety', anxLabels, anxValues);
createDoughnut('pieStress', strLabels, strValues);
</script>
</body>
</html>