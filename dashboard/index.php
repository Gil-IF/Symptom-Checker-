    <?php
    session_start();

    // Cek apakah user sudah login
    if (!isset($_SESSION['npm'])) {
        header('Location: ../login.php');
        exit;
    }

    $page_title = 'Home - Symptom Checker';
    $npm = $_SESSION['npm'];
    $nama_user = $_SESSION['nama_panggilan'] ?? $npm;
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($page_title); ?></title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            :root {
                --bg: #f4f4f6;
                --white: #ffffff;
                --text: #2f3137;
                --shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
                --card-blue: #6ea3d9;
                --card-mint: #aee9cd;
                --accent: #cbb2e1;
                --yellow: #fff100;
                --radius: 26px;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Poppins', sans-serif;
                background: var(--bg);
                color: var(--text);
                min-height: 100vh;
            }

            a {
                text-decoration: none;
                color: inherit;
            }

            .wrap {
                max-width: 1280px;
                margin: 0 auto;
                padding: 12px 20px 24px;
                position: relative;
            }

            .breadcrumb {
                color: #8e8e98;
                font-size: 14px;
                margin: 2px 0 12px 2px;
                font-weight: 500;
            }

            .topbar {
                background: var(--white);
                min-height: 64px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 20px;
                border-radius: var(--radius);
                box-shadow: var(--shadow);
                margin-bottom: 28px;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 700;
                font-size: 18px;
            }

            .brand img {
                width: 34px;
                height: 34px;
                object-fit: contain;
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 28px;
                font-size: 16px;
                font-weight: 600;
            }

            .nav-links a {
                position: relative;
                padding-bottom: 4px;
                transition: color 0.2s;
            }

            .nav-links a:hover {
                color: #6ea3d9;
            }

            .nav-links a.active::after {
                content: '';
                position: absolute;
                left: 0;
                bottom: -2px;
                width: 100%;
                height: 4px;
                border-radius: 999px;
                background: #b8e0d1;
            }

            .hamburger {
                width: 48px;
                height: 48px;
                border: none;
                border-radius: 16px;
                background: var(--accent);
                display: grid;
                place-items: center;
                cursor: pointer;
                box-shadow: 0 6px 16px rgba(0,0,0,.12);
                transition: background 0.2s;
            }

            .hamburger:hover {
                background: #b794d4;
            }

            .hamburger span,
            .hamburger span::before,
            .hamburger span::after {
                content: '';
                display: block;
                width: 22px;
                height: 3px;
                border-radius: 999px;
                background: #37343f;
                position: relative;
            }

            .hamburger span::before {
                position: absolute;
                top: -7px;
                left: 0;
            }

            .hamburger span::after {
                position: absolute;
                top: 7px;
                left: 0;
            }

            .hero {
                position: relative;
                padding-bottom: 60px;
            }

            .hero h1 {
                font-size: 32px;
                font-weight: 700;
                line-height: 1.3;
                margin-bottom: 32px;
                color: #1c1f26;
            }
            

            .cards {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }

            .card {
                border-radius: var(--radius);
                padding: 32px 28px;
                min-height: 180px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: var(--shadow);
                transition: transform 0.2s ease, box-shadow 0.2s;
                cursor: pointer;
            }

            .card:hover {
                transform: translateY(-4px);
                box-shadow: 0 14px 28px rgba(0,0,0,0.12);
            }

            .card.blue {
                background: var(--card-blue);
                color: #fff;
            }

            .card.mint {
                background: var(--card-mint);
                color: #1a3b2e;
            }

            .card h2 {
                font-size: 24px;
                margin-bottom: 10px;
                font-weight: 700;
            }

            .card p {
                font-size: 14px;
                line-height: 1.6;
                max-width: 240px;
            }

            .wave {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                line-height: 0;
                pointer-events: none;
            }

            .wave svg {
                width: 100%;
                height: 80px;
                display: block;
            }

            @media (max-width: 768px) {
                .cards {
                    grid-template-columns: 1fr;
                }

                .hero h1 {
                    font-size: 26px;
                }

                .nav-links {
                    gap: 16px;
                }
            }
        </style>
    </head>
<body>

    <!-- ========== TOPBAR ========== -->
    <header class="topbar">
        <a href="index.php" class="brand">
            <img src="../assets/img/logo.png" alt="Logo Symptom Checker">
            <span>Symptom Checker</span>
        </a>

        <nav class="nav-links">
            <a href="../skrining/index.php">Checking</a>
            <a href="index.php" class="active">Home</a>
            <button class="hamburger" type="button" aria-label="Menu" onclick="openNavDrawer()">
                <span></span>
            </button>
        </nav>
    </header>

    <!-- ========== NAVIGASI DRAWER ========== -->
    <?php include 'navigasi.php'; ?>   <!-- ✅ sekarang benar, satu folder -->

    <!-- ========== KONTEN UTAMA ========== -->
    <div class="main-content">
        <div class="breadcrumb"></div>

        <div class="greeting">
            <h1>
                Hello, <?= htmlspecialchars($nama_user); ?>!<br>
                How do you feel today?
            </h1>
        </div>
<div class="breadcrumb"></div>  

        <section class="cards">
            <a href="../skrining/index.php" class="card blue">
                <div>
                    <h2>Start Checking</h2>
                    <p>Every feeling has meaning. Let's understand what you're experiencing.</p>
                </div>
                <img src="../assets/img/helpme.png" alt="Hero Image" style="width: 220px; height: auto; object-fit: contain;">
            </a>

            <a href="../riwayat/index.php" class="card mint">
                <div>
                    <h2>History Check</h2>
                    <p>Revisit your previous check results and see how your emotional condition changes over time.</p>
                </div>
                <img src="../assets/img/kertas.png" alt="History Image" style="width: 220px; height: auto; object-fit: contain;">
            </a>
        </section>
    </div>

</body>
    </html>