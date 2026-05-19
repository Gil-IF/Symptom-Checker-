<?php
session_start();
$page_title = "About - Symptom Checker";
$is_logged_in = isset($_SESSION['npm']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title); ?></title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --blue:        #5e9fc9;
            --blue-dark:   #4a85ae;
            --blue-light:  #7ab8d9;
            --teal:        #7ecbb8;
            --teal-light:  #a8ddd0;
            --green-wave:  #a8d9c2;
            --purple-card: #c9b3d8;
            --white:       #ffffff;
            --dark:        #2c3e50;
            --bg:          #eef3f8;
            --shadow:      0 8px 24px rgba(0,0,0,0.08);
            --radius:      22px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--dark);
            line-height: 1.6;
        }

        a { text-decoration: none; color: inherit; }

        /* ── TOPBAR ─────────────────────────────────────── */
        .topbar {
            background: var(--white);
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            position: sticky;
            top: 0;
            z-index: 100;
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
            font-size: 15px;
            font-weight: 600;
            list-style: none;
        }

        .nav-links a {
            position: relative;
            padding-bottom: 4px;
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--blue); }

        .nav-links a.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100%;
            height: 3px;
            border-radius: 999px;
            background: var(--teal);
        }

        /* ── BREADCRUMB ─────────────────────────────────── */
        .breadcrumb {
            max-width: 1060px;
            margin: 0 auto;
            padding: 12px 20px 4px;
            font-size: 13px;
            color: #888;
            font-weight: 500;
        }

        /* ── PAGE WRAPPER ────────────────────────────────── */
        .page-wrapper {
            max-width: 1060px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }

        /* ═══════════════════════════════════════════════════
           SECTION 1 — HERO
        ═══════════════════════════════════════════════════ */
        .hero-card {
            background: linear-gradient(140deg, var(--blue-light) 0%, var(--blue) 50%, var(--blue-dark) 100%);
            border-radius: var(--radius) var(--radius) 0 0;
            padding: 40px 40px 0;
            display: flex;
            align-items: flex-start;
            gap: 32px;
            overflow: hidden;
            position: relative;
            min-height: 320px;
        }

        .hero-text {
            flex: 1;
            padding-bottom: 70px;
            z-index: 2;
        }

        .hero-text h1 {
            font-size: 28px;
            font-weight: 900;
            color: var(--white);
            margin-bottom: 16px;
        }

        .hero-text p {
            font-size: 14px;
            line-height: 1.9;
            color: rgba(255,255,255,0.93);
            text-align: justify;
        }

        .hero-illus {
            flex-shrink: 0;
            width: 200px;
            z-index: 2;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 8px;
        }

        .hero-illus img {
            width: 180px;
            height: 180px;
            object-fit: contain;
            filter: drop-shadow(0 10px 22px rgba(0,0,0,0.22));
        }

        .hero-waves {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            line-height: 0;
        }

        /* ═══════════════════════════════════════════════════
           SECTION 2 — WHY
        ═══════════════════════════════════════════════════ */
        .why-section {
            background: linear-gradient(135deg, #6dbfac 0%, var(--blue-light) 100%);
            padding: 40px;
            display: flex;
            align-items: center;
            gap: 32px;
            position: relative;
            overflow: hidden;
        }

        .why-illus {
            flex-shrink: 0;
            width: 200px;
            display: flex;
            justify-content: center;
        }

        .why-illus img {
            width: 180px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.2));
        }

        .why-text p {
            font-size: 14px;
            line-height: 2;
            color: rgba(255,255,255,0.95);
            text-align: justify;
        }

        .why-waves {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            line-height: 0;
        }

        /* ═══════════════════════════════════════════════════
           SECTION 3 — HOW IT WORKS
        ═══════════════════════════════════════════════════ */
        .how-section {
            background: linear-gradient(160deg, var(--teal-light) 0%, var(--blue-light) 100%);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .how-section h2 {
            text-align: center;
            font-size: 22px;
            font-weight: 900;
            color: var(--white);
            margin-bottom: 24px;
        }

        .steps-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            max-width: 700px;
            margin: 0 auto;
        }

        .step-item {
            background: var(--purple-card);
            border-radius: 14px;
            padding: 16px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .step-item h3 {
            font-size: 14px;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: 6px;
        }

        .step-item p {
            font-size: 13px;
            line-height: 1.65;
            color: rgba(44,62,80,0.8);
        }

        /* ═══════════════════════════════════════════════════
           SECTION 4 — CTA
        ═══════════════════════════════════════════════════ */
        .cta-section {
            background: var(--white);
            border-radius: 0 0 var(--radius) var(--radius);
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 6px 24px rgba(94,159,201,0.12);
        }

        .cta-section h2 {
            font-size: 22px;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .cta-section p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.65;
        }

        .cta-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-bottom: 16px;
        }

        .cta-logo img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .cta-logo span {
            font-size: 13px;
            font-weight: 800;
            color: var(--dark);
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 180px;
            height: 50px;
            padding: 0 32px;
            border: none;
            border-radius: 999px;
            background: var(--teal);
            color: var(--dark);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(0,0,0,0.12);
            transition: 0.2s ease;
            text-decoration: none;
        }

        .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.16);
            background: #6dbfac;
        }

        /* ── RESPONSIVE ─────────────────────────────────── */
        @media (max-width: 768px) {
            .hero-card,
            .why-section {
                flex-direction: column;
                padding: 28px 24px 0;
                min-height: auto;
            }

            .hero-illus,
            .why-illus {
                width: 140px;
                margin: 0 auto;
            }

            .hero-illus img,
            .why-illus img {
                width: 140px;
                height: 140px;
            }

            .how-section {
                padding: 28px 24px;
            }

            .cta-section {
                padding: 28px 24px;
            }

            .nav-links {
                gap: 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <!-- ── TOPBAR ── -->
    <header class="topbar">
        <a href="index.php" class="brand">
            <img src="assets/img/logo.png" alt="Logo Symptom Checker">
            <span>Symptom Checker</span>
        </a>

        <nav>
            <ul class="nav-links">
                <li><a href="about.php" class="active">About</a></li>
                <?php  ?>
                    <li><a href="login.php">Login</a></li>
                <?php  ?>
            </ul>
        </nav>
    </header>

    <div class="breadcrumb">Home / About</div>

    <div class="page-wrapper">

        <!-- ══ SECTION 1 : HERO ══ -->
        <div class="hero-card">
            <div class="hero-text">
                <h1>About Us</h1>
                <p>
                    Symptom Checker is a digital platform designed to help users identify early signs of mental health
                    conditions based on the symptoms they are experiencing. Through a simple screening process, users
                    answer a series of questions about their emotional state, sleep patterns, anxiety levels, mood, and
                    daily habits. Based on these answers, the system will provide an initial overview of the possible
                    psychological condition they are experiencing, along with guidance or recommendations for initial
                    steps the user can take.
                </p>
            </div>

            <div class="hero-illus">
                <img src="assets/img/logo.png" alt="Ilustrasi Symptom Checker">
            </div>

            <div class="hero-waves">
                <svg viewBox="0 0 1100 55" preserveAspectRatio="none" width="100%" height="55">
                    <path d="M0 30 L160 12 L330 42 L520 8 L700 38 L890 6 L1100 30 L1100 55 L0 55 Z"
                          fill="#a8ddd0" opacity=".55"/>
                    <path d="M0 40 L200 18 L400 46 L600 14 L800 44 L1000 16 L1100 38 L1100 55 L0 55 Z"
                          fill="#6dbfac" opacity=".4"/>
                </svg>
            </div>
        </div>

        <!-- ══ SECTION 2 : WHY ══ -->
        <div class="why-section">
            <div class="why-illus">
                <img src="assets/img/help_people.png" alt="Ilustrasi membantu">
            </div>

            <div class="why-text">
                <p>
                    Despite easy access to information online, many people experience confusion when trying to understand
                    their mental health. A single symptom can often point to multiple possible conditions, leading to
                    conflicting information and even undue anxiety. Furthermore, not everyone has easy access to direct
                    consultation with a professional due to financial constraints, time constraints, or fear of the social
                    stigma that often surrounds mental health issues. This is the foundation for the introduction of the
                    Symptom Checker, a more practical, easy-to-understand, and accessible initial screening tool.
                </p>
            </div>

            <div class="why-waves">
                <svg viewBox="0 0 1100 48" preserveAspectRatio="none" width="100%" height="48">
                    <path d="M0 24 L170 9 L340 34 L520 6 L710 32 L900 5 L1100 26 L1100 48 L0 48 Z"
                          fill="#9ec8da" opacity=".5"/>
                    <path d="M0 32 L210 12 L420 38 L620 10 L820 36 L1020 12 L1100 30 L1100 48 L0 48 Z"
                          fill="#7ab8d9" opacity=".4"/>
                </svg>
            </div>
        </div>

        <!-- ══ SECTION 3 : HOW IT WORKS ══ -->
        <div class="how-section">
            <h2>How This System Works</h2>
            <div class="steps-list">
                <div class="step-item">
                    <h3>1. Answer Questions</h3>
                    <p>Users complete several questions regarding their emotional state, sleep patterns, stress levels, anxiety, and daily habits.</p>
                </div>
                <div class="step-item">
                    <h3>2. Answer Analysis by the System</h3>
                    <p>Processes the answers based on structured mental health screening indicators.</p>
                </div>
                <div class="step-item">
                    <h3>3. Screening Results</h3>
                    <p>Users will obtain an initial overview of their possible mental health condition.</p>
                </div>
                <div class="step-item">
                    <h3>4. Recommendations</h3>
                    <p>The system provides suggestions for initial steps to take, including a recommendation to consult a professional if necessary.</p>
                </div>
            </div>
        </div>

        <!-- ══ SECTION 4 : CTA ══ -->
        <div class="cta-section">
            <h2>Recognize Your Condition Early</h2>
            <p>Take a few minutes to understand what you're feeling and get appropriate initial guidance.</p>
            <div class="cta-logo">
                <img src="assets/img/logo.png" alt="Logo Symptom Checker">
                <span>Symptom Checker</span>
            </div>
            <?php if (!$is_logged_in): ?>
                <a href="login.php" class="cta-btn">Start Now</a>
            <?php endif; ?>
        </div>

    </div><!-- /page-wrapper -->
</body>
</html>