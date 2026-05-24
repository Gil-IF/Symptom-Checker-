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

// Fungsi menentukan label badge berdasarkan data kategori
function badge_level(array $row): string
{
    // Prioritas: sangat berat > berat > sedang > ringan > normal
    // Urutan pengecekan harus dari yang paling spesifik dulu!
    $levelPriority = ['Sangat Berat', 'Berat', 'Sedang', 'Ringan', 'Normal'];
    
    foreach (['kategori_depresi', 'kategori_anxiety', 'kategori_stress'] as $key) {
        if (!empty($row[$key])) {
            $val = trim((string)$row[$key]);
            // Bandingkan case‑insensitive
            foreach ($levelPriority as $label) {
                if (strcasecmp($val, $label) === 0) {
                    // Konversi ke istilah dashboard (opsional: langsung kembalikan $label)
                    return match ($label) {
                        'Sangat Berat' => 'Danger',
                        'Berat'        => 'High Risk',
                        'Sedang'       => 'Medium Risk',
                        'Ringan'       => 'Low Risk',
                        'Normal'       => 'Safe',
                    };
                }
            }
        }
    }
    return 'Safe'; // fallback jika tidak ditemukan
}

// Fungsi mengembalikan kelas CSS untuk badge
function badge_class(string $badge): string
{
    return match ($badge) {
        'Danger'      => 'danger',   // Sangat Berat
        'High Risk'   => 'danger',   // Berat
        'Medium Risk' => 'warn',     // Sedang
        'Low Risk'    => 'success',  // Ringan
        'Safe'        => 'success',  // Normal
        default       => 'success',
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
    <link rel="stylesheet" href="../assets/css/admin.css" />
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