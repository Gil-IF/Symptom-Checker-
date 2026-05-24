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
    <link rel="stylesheet" href="assets/css/about.css">
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
                <h1>Tentang Kami</h1>
                <p>
            Symptom Checker adalah platform digital yang membantu pengguna mengenali 
            tingkat depresi, kecemasan, dan stres menggunakan kuesioner standar 
            <strong>DASS‑42</strong> (<em>Depression Anxiety Stress Scales</em>). 
            Melalui proses skrining sederhana, pengguna menjawab 42 pertanyaan yang 
            mengukur kondisi emosional, pola tidur, tingkat kecemasan, suasana hati, 
            dan kebiasaan sehari‑hari. Sistem kemudian menganalisis jawaban dan 
            memberikan gambaran tingkat keparahan masing‑masing aspek dari normal, ringan, sedang, 
            berat, atau sangat berat beserta rekomendasi langkah awal yang dapat diambil.
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
                    Meskipun akses informasi daring sangat mudah, banyak orang merasa bingung saat mencoba memahami
                    kesehatan mental mereka. Satu gejala saja sering kali dapat mengarah pada berbagai kemungkinan kondisi, yang menyebabkan
                    informasi yang bertentangan dan bahkan kecemasan yang berlebihan. Selain itu, tidak semua orang memiliki akses mudah untuk
                    berkonsultasi langsung dengan seorang profesional karena kendala finansial, keterbatasan waktu, atau ketakutan akan
                    stigma sosial yang sering melekat pada masalah kesehatan mental. Hal inilah yang menjadi dasar diperkenalkannya
                    Symptom Checker, sebuah alat skrining awal yang lebih praktis, mudah dipahami, dan dapat diakses.
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
    <h2>Cara Kerja Sistem</h2>
    <div class="steps-list">
        <div class="step-item">
            <h3>1. Isi Kuesioner DASS‑42</h3>
            <p>Pengguna menjawab 42 pertanyaan yang mengukur depresi, kecemasan, dan stres.</p>
        </div>
        <div class="step-item">
            <h3>2. Analisis oleh Sistem</h3>
            <p>Sistem menghitung skor berdasarkan jawaban dan mengkategorikan tingkat keparahan.</p>
        </div>
        <div class="step-item">
            <h3>3. Hasil Skrining</h3>
            <p>Pengguna memperoleh gambaran tingkat depresi, kecemasan, dan stres beserta interpretasinya.</p>
        </div>
        <div class="step-item">
            <h3>4. Rekomendasi</h3>
            <p>Sistem memberikan saran langkah awal, termasuk anjuran konsultasi profesional jika diperlukan.</p>
        </div>
    </div>
</div>

<!-- ══ SECTION 4 : CTA ══ -->
<div class="cta-section">
    <h2>Kenali Kondisi Anda Lebih Awal</h2>
    <p>
        Luangkan beberapa menit untuk menjawab 42 pertanyaan 
        <strong>DASS‑42</strong> dan dapatkan gambaran awal tentang 
        tingkat depresi, kecemasan, serta stres yang mungkin Anda alami.
    </p>
    <div class="cta-logo">
        <img src="assets/img/logo.png" alt="Logo Symptom Checker">
        <span>Symptom Checker</span>
    </div>

    <?php if (!$is_logged_in): ?>
        <a href="login.php" class="cta-btn">Mulai Sekarang</a>
    <?php else: ?>
        <a href="skrining/index.php" class="cta-btn">Mulai Skrining</a>
    <?php endif; ?>
</div>