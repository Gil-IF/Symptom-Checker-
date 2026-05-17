<?php
// about.php - Symptom Checker About Page
$page_title = "About - Symptom Checker";
$logo_path = "assets/img/logo.png";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --blue-main:   #6aaad4;
      --blue-dark:   #5592bb;
      --blue-light:  #a8d0e6;
      --green-wave:  #a8d9c2;
      --pink-heart:  #e8a8c8;
      --green-brain: #a8d9b8;
      --text-white:  #ffffff;
      --text-dark:   #2c3e50;
      --nav-bg:      #ffffff;
      --nav-border:  #e0e0e0;
    }

    body {
      font-family: 'Nunito', sans-serif;
      background-color: #f0f4f8;
      color: var(--text-dark);
      min-height: 100vh;
    }

    /* ── NAVBAR ── */
    nav {
      background: var(--nav-bg);
      border-bottom: 1px solid var(--nav-border);
      padding: 0 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 68px;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }

    .nav-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .nav-brand img {
      width: 38px;
      height: 38px;
      object-fit: contain;
      border-radius: 50%;
    }

    /* Fallback icon jika logo.png tidak ada */
    .nav-brand .logo-fallback {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--pink-heart) 50%, var(--green-brain) 50%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    .nav-brand span {
      font-size: 1.1rem;
      font-weight: 800;
      color: var(--text-dark);
      letter-spacing: -0.3px;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 2rem;
      list-style: none;
    }

    .nav-links a {
      text-decoration: none;
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--text-dark);
      position: relative;
      padding-bottom: 4px;
      transition: color 0.2s;
    }

    .nav-links a.active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 100%;
      height: 3px;
      background: var(--green-wave);
      border-radius: 2px;
    }

    .nav-links a:hover {
      color: var(--blue-dark);
    }

    /* ── BREADCRUMB ── */
    .breadcrumb {
      padding: 6px 2.5rem;
      font-size: 0.78rem;
      color: #888;
    }

    /* ── MAIN WRAPPER ── */
.page-wrapper {
  padding: 0 3rem 3rem;
  max-width: 1500px;
  margin: 0 auto;
}

.hero-card {
  background: linear-gradient(135deg, var(--blue-main) 0%, var(--blue-dark) 100%);
  border-radius: 20px;
  padding: 3rem 4rem 0;
  display: flex;
  align-items: flex-end;
  gap: 2rem;
  overflow: hidden;
  position: relative;
  min-height: 560px;
  width: 100%;
}

.hero-illustration {
  flex-shrink: 0;
  width: 320px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  padding-top: 0;
  position: relative;
  z-index: 2;
}

.hero-illustration img {
  width: 300px;
  height: 300px;
  object-fit: contain;
  filter: drop-shadow(0px 16px 28px rgba(0,0,0,0.25));
}
    /* Teks kiri */
    .hero-text {
      flex: 1;
      padding-bottom: 9.5rem;
      position: relative;
      z-index: 2;
    }

    .hero-text h1 {
      font-size: 2rem;
      font-weight: 800;
      color: var(--text-white);
      margin-bottom: 1.2rem;
      letter-spacing: -0.5px;
    }

    .hero-text p {
      font-size: 1.50rem;
      line-height: 1.85;
      color: rgba(255,255,255,0.93);
      text-align: justify;
      max-width: 560px;
    }

    /* Ilustrasi kanan */
     .hero-illustration {
      flex-shrink: 0;
      width: 300px;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding-top: 1rem;
      position: relative;
      z-index: 2;
    }
 
    .hero-illustration img {
      width: 280px;
      height: 280px;
      object-fit: contain;
      filter: drop-shadow(0px 16px 28px rgba(0,0,0,0.25));
    }

    /* SVG Heart+Brain illustration */
    .heart-brain-svg {
      width: 400px;
      height: 400px;
      filter: drop-shadow(4px 6px 12px rgba(0,0,0,0.18));
    }

    /* Wave decoration bawah */
    .hero-waves {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      line-height: 0;
    }

    /* ── SECTION BERIKUTNYA ── */
    .features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.2rem;
      margin-top: 2rem;
    }

    .feature-card {
      background: #fff;
      border-radius: 14px;
      padding: 1.5rem 1.2rem;
      box-shadow: 0 2px 12px rgba(100,160,210,0.1);
      text-align: center;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .feature-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 20px rgba(100,160,210,0.18);
    }

    .feature-icon {
      font-size: 2rem;
      margin-bottom: 0.8rem;
    }

    .feature-card h3 {
      font-size: 0.95rem;
      font-weight: 800;
      color: var(--blue-dark);
      margin-bottom: 0.4rem;
    }

    .feature-card p {
      font-size: 0.82rem;
      color: #666;
      line-height: 1.6;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 700px) {
      .hero-card {
        flex-direction: column;
        align-items: center;
        padding: 2rem 1.5rem 0;
        min-height: auto;
      }
      .hero-text { padding-bottom: 1rem; }
      .hero-illustration { width: 150px; }
      .heart-brain-svg { width: 150px; height: 150px; }
    }
  </style>
</head>
<body>

  <!-- ── NAVBAR ── -->
  <nav>
    <a href="index.php" class="nav-brand">
      <?php if (file_exists($logo_path)): ?>
        <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Symptom Checker Logo" />
      <?php else: ?>
        <!-- Fallback SVG logo jika file logo.png belum ada -->
        <svg class="logo-fallback" viewBox="0 0 38 38" width="38" height="38" xmlns="http://www.w3.org/2000/svg">
          <circle cx="19" cy="19" r="19" fill="none"/>
          <!-- Setengah hati (kiri) -->
          <path d="M19 28 C19 28 6 20 6 13 C6 9 9 7 12 7 C15 7 17 9 19 12" fill="#e8a8c8"/>
          <!-- Setengah otak (kanan) -->
          <path d="M19 12 C21 9 23 7 26 7 C29 7 32 9 32 13 C32 20 19 28 19 28" fill="#a8d9b8"/>
          <!-- Garis tengah -->
          <line x1="19" y1="10" x2="19" y2="28" stroke="#fff" stroke-width="1.5" stroke-dasharray="2,2"/>
        </svg>
      <?php endif; ?>
      <span>Symptom Checker</span>
    </a>

    <ul class="nav-links">
      <li><a href="about.php" class="active">About</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
  </nav>

  <!-- ── BREADCRUMB ── -->
  <div class="breadcrumb">About</div>

  <!-- ── KONTEN ── -->
  <div class="page-wrapper">

    <!-- Hero Card -->
    <div class="hero-card">

      <!-- Teks -->
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

      <!-- Ilustrasi Heart + Brain -->
      <div class="hero-illustration">
        <img src="assets/img/logo.png" alt="Logo Symptom Checker">
      </img>
      </div>

      <!-- Wave bawah -->
      <div class="hero-waves">
        <svg viewBox="0 0 1100 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" width="100%" height="80">
          <!-- Gelombang hijau belakang -->
          <path d="M0 50 L180 20 L360 60 L540 15 L720 55 L900 10 L1100 45 L1100 80 L0 80 Z"
                fill="#a8d9c2" opacity="0.6"/>
          <!-- Gelombang biru depan -->
          <path d="M0 60 L200 30 L400 65 L600 25 L800 60 L1000 28 L1100 55 L1100 80 L0 80 Z"
                fill="#5592bb" opacity="0.4"/>
        </svg>
      </div>
    </div>


  </div><!-- /page-wrapper -->

</body>
</html>