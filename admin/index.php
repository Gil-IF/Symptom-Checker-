<?php
// admin/index.php
session_start();

// Cek login admin
if (!isset($_SESSION['id_admin']) && !isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

$adminName = $_SESSION['admin']['username'] ?? $_SESSION['admin_username'] ?? 'Admin';

// --- Ringkasan ---
$totalUsers = 0;
$totalScreening = 0;
$screeningToday = 0;
$activeQuestions = 0;
$highRisk = 0;

// Inisialisasi default (sebelum try, agar selalu terdefinisi)
$subskalaMap = ['IDU001' => 'Depresi', 'IDU002' => 'Kecemasan', 'IDU003' => 'Stres'];
$selectedSubskala = $_GET['subskala'] ?? 'IDU001';
if (!array_key_exists($selectedSubskala, $subskalaMap)) {
    $selectedSubskala = 'IDU001';
}
$selectedName = $subskalaMap[$selectedSubskala];
$allCategories = ['Normal','Ringan','Sedang','Berat','Sangat Berat'];
$distData = array_fill_keys($allCategories, 0);
$totalDist = 0;

try {
    $totalUsers = (int) $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();
    $totalScreening = (int) $pdo->query("SELECT COUNT(*) FROM skrining")->fetchColumn();
    $screeningToday = (int) $pdo->query("SELECT COUNT(*) FROM skrining WHERE DATE(tgl_skrining) = CURDATE()")->fetchColumn();
    $activeQuestions = (int) $pdo->query("SELECT COUNT(*) FROM variabel_skrining WHERE status_aktif = 1")->fetchColumn();

    // High Risk: skor masuk kategori Berat atau Sangat Berat di subskala manapun
    $stmt = $pdo->query("
        SELECT COUNT(*) FROM skrining s
        WHERE EXISTS (
            SELECT 1 FROM kategori_subskala k
            WHERE k.id_subskala = 'IDU001' AND s.skor_depresi BETWEEN k.rentang_min AND k.rentang_max AND k.nama_kategori IN ('Berat','Sangat Berat')
        )
        OR EXISTS (
            SELECT 1 FROM kategori_subskala k
            WHERE k.id_subskala = 'IDU002' AND s.skor_anxiety BETWEEN k.rentang_min AND k.rentang_max AND k.nama_kategori IN ('Berat','Sangat Berat')
        )
        OR EXISTS (
            SELECT 1 FROM kategori_subskala k
            WHERE k.id_subskala = 'IDU003' AND s.skor_stress BETWEEN k.rentang_min AND k.rentang_max AND k.nama_kategori IN ('Berat','Sangat Berat')
        )
    ");
    $highRisk = (int) $stmt->fetchColumn();

    // ============================================================
    // DISTRIBUSI KATEGORI untuk panel baru
    // ============================================================
    $subskalaMap = ['IDU001' => 'Depresi', 'IDU002' => 'Kecemasan', 'IDU003' => 'Stres'];
    $selectedSubskala = $_GET['subskala'] ?? 'IDU001';
    if (!array_key_exists($selectedSubskala, $subskalaMap)) {
        $selectedSubskala = 'IDU001';
    }
    $selectedName = $subskalaMap[$selectedSubskala];

    // ============================================================
    // DISTRIBUSI KATEGORI (panel baru)
    // ============================================================
    $stmtDist = $pdo->prepare("
        SELECT k.nama_kategori, COUNT(s.id_skrining) AS total
        FROM skrining s
        JOIN kategori_subskala k ON k.id_subskala = :subskala
            AND (
                CASE :subskala
                    WHEN 'IDU001' THEN s.skor_depresi
                    WHEN 'IDU002' THEN s.skor_anxiety
                    WHEN 'IDU003' THEN s.skor_stress
                END
            ) BETWEEN k.rentang_min AND k.rentang_max
        GROUP BY k.nama_kategori
        ORDER BY FIELD(k.nama_kategori, 'Normal','Ringan','Sedang','Berat','Sangat Berat')
    ");
    $stmtDist->execute(['subskala' => $selectedSubskala]);
    $distribusiRaw = $stmtDist->fetchAll(PDO::FETCH_KEY_PAIR);
    
    foreach ($allCategories as $cat) {
        $distData[$cat] = $distribusiRaw[$cat] ?? 0;
    }
    $totalDist = array_sum($distData);

} catch (Throwable $e) {
    // Jika error, biarkan default 0
    $selectedSubskala = 'IDU001';
    $selectedName = 'Depresi';
    $allCategories = ['Normal','Ringan','Sedang','Berat','Sangat Berat'];
    $distData = array_fill_keys($allCategories, 0);
    $totalDist = 0;
}

// Data grafik 7 hari terakhir
$labels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
$series = [0, 0, 0, 0, 0, 0, 0];
try {
    $stmt = $pdo->query("
        SELECT DATE(tgl_skrining) AS d, COUNT(*) AS total
        FROM skrining
        WHERE tgl_skrining >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(tgl_skrining)
        ORDER BY d ASC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $map = [];
    foreach ($rows as $r) {
        $map[$r['d']] = (int)$r['total'];
    }
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i day"));
        $idx = 6 - $i;
        $series[$idx] = $map[$date] ?? 0;
        $labels[$idx] = date('D', strtotime("-$i day"));
    }
} catch (Throwable $e) {}

// Recent screenings
$recent = [];
try {
    $stmt = $pdo->query("
        SELECT s.id_skrining, s.tgl_skrining, m.npm,
               kd.nama_kategori AS kategori_depresi,
               ka.nama_kategori AS kategori_anxiety,
               ks.nama_kategori AS kategori_stress
        FROM skrining s
        JOIN mahasiswa m ON m.id_mahasiswa = s.id_mahasiswa
        JOIN kategori_subskala kd ON kd.id_subskala = 'IDU001' AND s.skor_depresi BETWEEN kd.rentang_min AND kd.rentang_max
        JOIN kategori_subskala ka ON ka.id_subskala = 'IDU002' AND s.skor_anxiety BETWEEN ka.rentang_min AND ka.rentang_max
        JOIN kategori_subskala ks ON ks.id_subskala = 'IDU003' AND s.skor_stress BETWEEN ks.rentang_min AND ks.rentang_max
        ORDER BY s.tgl_skrining DESC
        LIMIT 5
    ");
    $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {}

// Fungsi badge (bisa Anda ganti nanti dengan DASS‑42 naming)
function badge_level(array $row): string
{
    foreach (['kategori_depresi','kategori_anxiety','kategori_stress'] as $k) {
        if (!empty($row[$k])) {
            $val = strtolower((string)$row[$k]);
            if (str_contains($val, 'berat')) return 'High Risk';
            if (str_contains($val, 'sedang') || str_contains($val, 'ringan')) return 'Medium Risk';
        }
    }
    return 'Low Risk';
}

function badge_class(string $badge): string
{
    return match ($badge) {
        'High Risk' => 'danger',
        'Medium Risk' => 'warn',
        default => 'success',
    };
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- ... CSS dan JS tetap sama ... -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Symptom Checker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        /* ... semua CSS seperti sebelumnya ... */
        :root{
            --bg:#f3f3f5;
            --panel:#ffffff;
            --text:#2f3137;
            --muted:#7a7f87;
            --shadow:0 10px 26px rgba(0,0,0,.08);
            --sidebar:#ffffff;
            --accent:#cbb2e1;
            --blue:#6ea3d9;
            --mint:#aee9cd;
            --danger:#ff7b7b;
            --warn:#ffd36d;
            --success:#aee9cd;
            --line:#ececf1;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}
        a{text-decoration:none;color:inherit}
        .layout{display:grid;grid-template-columns:280px 1fr;min-height:100vh}
        .sidebar{
            background:var(--sidebar);
            box-shadow:var(--shadow);
            padding:18px 16px;
            position:sticky; top:0; height:100vh;
            display:flex; flex-direction:column; gap:18px;
        }
        .brand{
            display:flex; align-items:center; gap:12px; padding:6px 4px 14px;
        }
        .brand img{width:44px;height:44px;object-fit:contain}
        .brand .title{font-size:24px;font-weight:800;line-height:1}
        .brand .sub{font-size:14px;line-height:1;color:#2f3137;opacity:.9}
        .menu-label{font-size:12px;color:#a3a3a8;font-weight:700;letter-spacing:.08em;margin:4px 0 8px 6px}
        .nav{display:flex;flex-direction:column;gap:8px}
        .nav a{
            display:flex; align-items:center; gap:12px;
            padding:14px 14px; border-radius:16px; color:#262626; font-weight:600;
        }
        .nav a.active,.nav a:hover{background:#efefef}
        .nav .ico{width:22px;text-align:center;font-size:20px}
        .sidebar-footer{margin-top:auto; display:flex; flex-direction:column; gap:10px}
        .admin-box{
            background:#efefef; border-radius:18px; padding:12px 14px; display:flex; gap:12px; align-items:center;
        }
        .avatar{width:38px;height:38px;border-radius:50%;background:#111;display:grid;place-items:center;color:#fff;font-weight:700}
        .admin-meta{line-height:1.2}
        .admin-meta .name{font-weight:700}
        .admin-meta .role{font-size:12px;color:var(--muted)}
        .logout{
            color:#e53935; font-weight:700; padding:10px 14px; border-radius:14px; display:flex; align-items:center; gap:12px;
        }
        .logout:hover{background:#fff1f1}

        .content{padding:22px 24px 28px}
        .topbar{
            display:flex; align-items:center; justify-content:space-between; gap:18px;
            margin-bottom:18px;
        }
        .hello h1{font-size:34px;line-height:1.1;margin-bottom:4px}
        .hello p{color:var(--muted);font-size:13px}
        .actions{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
        .search{
            width:min(360px, 44vw); background:#fff; border-radius:999px; box-shadow:var(--shadow);
            display:flex; align-items:center; gap:10px; padding:14px 18px;
        }
        .search input{border:none;outline:none;width:100%;font-family:inherit;font-size:14px}
        .icon-btn{width:44px;height:44px;border:none;border-radius:50%;background:#fff;box-shadow:var(--shadow);font-size:20px;cursor:pointer}

        .cards{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin:18px 0}
        .card{background:var(--panel);border-radius:20px;box-shadow:var(--shadow);padding:16px 18px;display:flex;gap:14px;align-items:center}
        .card .icon{width:54px;height:54px;border-radius:14px;display:grid;place-items:center;font-size:24px;flex:0 0 auto}
        .card .kpi .label{font-size:13px;font-weight:700;color:#222}
        .card .kpi .value{font-size:26px;font-weight:800;line-height:1.15;margin-top:2px}
        .card .kpi .sub{font-size:12px;color:var(--muted);margin-top:4px}
        .card.blue .icon{background:#dbe8ff;color:#3257ff}
        .card.purple .icon{background:#f0d4ff;color:#a323c8}
        .card.red .icon{background:#ffd8d8;color:#d93025}
        .card.green .icon{background:#e1f7df;color:#2e7d32}

        .grid-2{display:grid;grid-template-columns:1.3fr 1fr;gap:16px;margin-bottom:16px}
        .panel{background:var(--panel);border-radius:20px;box-shadow:var(--shadow);padding:16px 18px}
        .panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:10px}
        .panel-head h2{font-size:16px}
        .filters{display:flex;gap:10px}
        .filters select{border:1px solid #e6e6ea;border-radius:10px;padding:8px 10px;background:#fff;font-family:inherit}
        .chart-wrap{height:260px}
        .list{display:flex;flex-direction:column;gap:12px}
        .symptom-row{display:flex;align-items:center;gap:12px}
        .symptom-row .dot{width:10px;height:10px;border-radius:50%;background:#6ea3d9;flex:0 0 auto}
        .symptom-row .name{width:100px;font-size:13px}
        .symptom-row .bar{flex:1;height:8px;background:#f0f1f5;border-radius:999px;overflow:hidden}
        .symptom-row .bar span{display:block;height:100%;border-radius:999px}
        .symptom-row .count{width:60px;text-align:right;font-size:13px;color:var(--muted)}

        .grid-2b{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .donut-wrap{display:flex;align-items:center;gap:20px}
        .legend{display:flex;flex-direction:column;gap:10px}
        .legend-item{display:flex;align-items:center;gap:10px;font-size:13px}
        .legend-dot{width:10px;height:10px;border-radius:50%}
        .recent-list{display:flex;flex-direction:column;gap:12px}
        .recent-item{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--line)}
        .recent-item:last-child{border-bottom:none}
        .recent-left{display:flex;align-items:center;gap:12px}
        .recent-avatar{width:34px;height:34px;border-radius:50%;background:#111;color:#fff;display:grid;place-items:center;font-size:16px}
        .recent-meta{display:flex;flex-direction:column}
        .recent-meta .name{font-weight:700;font-size:14px}
        .recent-meta .time{font-size:12px;color:var(--muted)}
        .badge{padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700}
        .badge.success{background:#daf6db;color:#207a2a}
        .badge.warn{background:#fff0c2;color:#9a7100}
        .badge.danger{background:#ffd8d8;color:#b42318}
        .view-all{font-size:13px;color:#2d8cff;font-weight:700}

        @media (max-width: 1200px){
            .layout{grid-template-columns:1fr}
            .sidebar{position:relative;height:auto}
            .cards{grid-template-columns:repeat(2,1fr)}
            .grid-2,.grid-2b{grid-template-columns:1fr}
        }
        @media (max-width: 720px){
            .content{padding:16px}
            .topbar{flex-direction:column;align-items:stretch}
            .hello h1{font-size:28px}
            .cards{grid-template-columns:1fr}
            .search{width:100%}
        }
    </style>
</head>
<body>
<div class="layout">
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
                <a class="active" href="index.php"><span class="ico">⌂</span>Dashboard</a>
                <a href="users.php"><span class="ico">👥</span>Users</a>
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

    <main class="content">
        <section class="topbar" style="background:transparent;box-shadow:none;padding:0;">
            <div class="hello">
                <h1>Hello, Admin 👋</h1>
                <p>Welcome back to Symptom Checker Admin Dashboard</p>
            </div>
            <div class="actions">
                <div class="search">
                    <span>🔎</span>
                    <input type="text" placeholder="Search Something...">
                </div>
                <button class="icon-btn" aria-label="Notifications">🔔</button>
                <button class="icon-btn" aria-label="Profile">👤</button>
            </div>
        </section>

        <section class="cards">
            <div class="card blue">
                <div class="icon">👥</div>
                <div class="kpi">
                    <div class="label">Total Users</div>
                    <div class="value"><?= number_format($totalUsers); ?></div>
                    <div class="sub">Mahasiswa terdaftar</div>
                </div>
            </div>

            <div class="card purple">
                <div class="icon">☑</div>
                <div class="kpi">
                    <div class="label">Check Today</div>
                    <div class="value"><?= number_format($screeningToday); ?></div>
                    <div class="sub">Skrining hari ini</div>
                </div>
            </div>

            <div class="card red">
                <div class="icon">⚠</div>
                <div class="kpi">
                    <div class="label">High Risk</div>
                    <div class="value"><?= number_format($highRisk); ?></div>
                    <div class="sub">Indikasi berat / sangat berat</div>
                </div>
            </div>

            <div class="card green">
                <div class="icon">📄</div>
                <div class="kpi">
                    <div class="label">Active Questions</div>
                    <div class="value"><?= number_format($activeQuestions); ?></div>
                    <div class="sub">Pertanyaan aktif</div>
                </div>
            </div>
        </section>

        <section class="grid-2">
            <div class="panel">
                <div class="panel-head">
                    <h2>Check Overview</h2>
                    <div class="filters">
                        <select><option>This Week</option><option>This Month</option></select>
                        <select><option><?= date('Y'); ?></option></select>
                    </div>
                </div>
                <div class="chart-wrap">
                    <canvas id="overviewChart"></canvas>
                </div>
            </div>

            <!-- ============= PANEL DISTRIBUSI KATEGORI (Menggantikan Top Symptoms) ============= -->
            <div class="panel">
                <div class="panel-head">
                    <h2>Distribusi Kategori</h2>
                    <form method="get" class="filters">
                        <select name="subskala" onchange="this.form.submit()">
                            <?php foreach ($subskalaMap as $id => $nama): ?>
                                <option value="<?= $id; ?>" <?= $selectedSubskala == $id ? 'selected' : ''; ?>>
                                    <?= $nama; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <div class="list">
                    <?php 
                    // Warna untuk masing-masing kategori
                    $colors = [
                        'Normal'       => '#aee9cd',
                        'Ringan'       => '#cbb2e1',
                        'Sedang'       => '#ffd36d',
                        'Berat'        => '#ff7b7b',
                        'Sangat Berat' => '#d93025',
                    ];
                    foreach ($allCategories as $cat): 
                        $persen = $totalDist > 0 ? round(($distData[$cat] / $totalDist) * 100) : 0;
                        $color = $colors[$cat] ?? '#ccc';
                    ?>
                    <div class="symptom-row">
                        <div class="dot" style="background-color:<?= $color; ?>;"></div>
                        <div class="name"><?= $cat; ?></div>
                        <div class="bar">
                            <span style="width: <?= $persen; ?>%; background-color: <?= $color; ?>;"></span>
                        </div>
                        <div class="count"><?= $persen; ?>%</div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- =================== END PANEL DISTRIBUSI =================== -->
        </section>

        <section class="grid-2b">
            <div class="panel">
                <div class="panel-head">
                    <h2>Risk Level Distribution</h2>
                </div>
                <div class="donut-wrap">
                    <div style="width:180px;height:180px;">
                        <canvas id="riskChart"></canvas>
                    </div>
                    <div class="legend">
                        <div class="legend-item"><span class="legend-dot" style="background:#44c767"></span> Low Risk <b>&nbsp;<?= max(0, $totalScreening - $highRisk); ?></b></div>
                        <div class="legend-item"><span class="legend-dot" style="background:#ffcc33"></span> Medium Risk <b>&nbsp;0</b></div>
                        <div class="legend-item"><span class="legend-dot" style="background:#ff4d4d"></span> High Risk <b>&nbsp;<?= (int)$highRisk; ?></b></div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h2>Recent Screening</h2>
                    <a href="hasil.php" class="view-all">View All</a>
                </div>
                <div class="recent-list">
                    <?php if (empty($recent)): ?>
                        <div style="color:#666;padding:18px 0;">Belum ada data skrining.</div>
                    <?php else: ?>
                        <?php foreach ($recent as $row): ?>
                            <?php $badge = badge_level($row); ?>
                            <div class="recent-item">
                                <div class="recent-left">
                                    <div class="recent-avatar">◉</div>
                                    <div class="recent-meta">
                                        <div class="name"><?= htmlspecialchars($row['npm']); ?></div>
                                        <div class="time"><?= date('d M Y, H:i', strtotime($row['tgl_skrining'])); ?></div>
                                    </div>
                                </div>
                                <span class="badge <?= badge_class($badge); ?>"><?= htmlspecialchars($badge); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
const overviewCtx = document.getElementById('overviewChart');
new Chart(overviewCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels); ?>,
        datasets: [{
            label: 'Screening',
            data: <?= json_encode($series); ?>,
            borderColor: '#4f8cff',
            backgroundColor: 'rgba(79,140,255,.12)',
            tension: 0.35,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, grid: { color: '#eef0f4' } }
        }
    }
});

const riskCtx = document.getElementById('riskChart');
new Chart(riskCtx, {
    type: 'doughnut',
    data: {
        labels: ['Low', 'Medium', 'High'],
        datasets: [{
            data: [<?= max(0, $totalScreening - $highRisk); ?>, 0, <?= (int)$highRisk; ?>],
            backgroundColor: ['#44c767', '#ffcc33', '#ff4d4d'],
            borderWidth: 0,
            hoverOffset: 4,
            cutout: '78%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});
</script>
</body>
</html>