<?php
// dashboard/index.php
$page_title = 'Home - Symptom Checker';
$nama_user = 'Zeeka'; // bisa diganti dari session, mis. $_SESSION['nama_panggilan']
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f4f4f6;
      --nav:#ffffff;
      --text:#2f3137;
      --muted:#777;
      --shadow:0 8px 18px rgba(0,0,0,.12);
      --card-blue:#6ea3d9;
      --card-mint:#aee9cd;
      --accent:#cbb2e1;
      --purple:#ceb2df;
      --yellow:#fff100;
      --deep:#111;
    }

    *{box-sizing:border-box;margin:0;padding:0}
    body{
      font-family:'Poppins',sans-serif;
      background:var(--bg);
      color:var(--text);
      min-height:100vh;
    }

    .wrap{
      max-width:1280px;
      margin:0 auto;
      padding:12px 16px 24px;
    }

    .breadcrumb{
      color:#b5b5b5;
      font-size:14px;
      margin:2px 0 8px 2px;
    }

    .topbar {
      background: var(--nav);
      min-height: 64px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 18px 0 16px;
      box-shadow: var(--shadow);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 28px;
      font-size: 18px;
      font-weight: 600;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--text);
      position: relative;
      padding-bottom: 4px;
      opacity: .95;
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

    .nav {
      background:var(--nav);
      border-radius:0;
      min-height:64px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:0 18px 0 16px;
      box-shadow:var(--shadow);
      position:sticky;
      top:0;
      z-index:10;
    }

    .brand{
      display:flex;
      align-items:center;
      gap:10px;
      font-weight:700;
      font-size:18px;
    }

    .brand img{
      width:34px;
      height:34px;
      object-fit:contain;
    }

    .menu{
      display:flex;
      align-items:center;
      gap:28px;
      font-size:18px;
      font-weight:600;
    }

    .menu a{
      text-decoration:none;
      color:var(--text);
      position:relative;
      padding-bottom:4px;
      opacity:.95;
    }

    .menu a.active::after{
      content:'';
      position:absolute;
      left:0;
      bottom:-2px;
      width:100%;
      height:4px;
      border-radius:999px;
      background:#b8e0d1;
    }

    .hamburger{
      width:54px;
      height:54px;
      border:none;
      border-radius:16px;
      background:var(--accent);
      display:grid;
      place-items:center;
      cursor:pointer;
      box-shadow:0 6px 16px rgba(0,0,0,.12);
    }

    .hamburger span,
    .hamburger span::before,
    .hamburger span::after{
      content:'';
      display:block;
      width:22px;
      height:3px;
      border-radius:999px;
      background:#37343f;
      position:relative;
    }
    .hamburger span::before{position:absolute;top:-7px;left:0}
    .hamburger span::after{position:absolute;top:7px;left:0}

    .hero{
      margin-top:16px;
      background:#fff;
      border-radius:0;
      box-shadow:var(--shadow);
      padding:26px 32px 0;
      overflow:hidden;
      position:relative;
      min-height:520px;
    }

    .hero h1{
      font-size:40px;
      line-height:1.15;
      font-weight:700;
      margin-bottom:18px;
      letter-spacing:-.5px;
    }

    .cards{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:26px;
      margin-top:28px;
      position:relative;
      z-index:2;
    }

    .card{
      border-radius:26px;
      padding:26px 24px;
      min-height:190px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
    }

    .card.blue{background:var(--card-blue)}
    .card.mint{background:var(--card-mint)}

    .card h2{
      font-size:24px;
      line-height:1.1;
      margin-bottom:12px;
      color:#111;
    }

    .card p{
      font-size:14px;
      line-height:1.6;
      color:#172026;
      max-width:220px;
    }

    .icon-box{
      flex:0 0 auto;
      width:112px;
      height:112px;
      display:grid;
      place-items:center;
    }

    .icon-box svg{
      width:100%;
      height:100%;
      display:block;
    }

    .wave{
      position:absolute;
      left:0;
      bottom:0;
      width:100%;
      line-height:0;
      z-index:1;
    }

    .wave svg{display:block;width:100%;height:88px}

    @media (max-width: 900px){
      .cards{grid-template-columns:1fr}
      .hero{padding:22px 20px 0;min-height:auto}
      .hero h1{font-size:32px}
    }

    @media (max-width: 640px){
      .wrap{padding:10px 10px 20px}
      .menu{gap:18px;font-size:15px}
      .brand{font-size:16px}
      .hamburger{width:48px;height:48px;border-radius:14px}
      .hero h1{font-size:28px}
      .card{padding:20px;min-height:unset;flex-direction:row}
      .card h2{font-size:21px}
      .card p{max-width:unset}
      .icon-box{width:88px;height:88px}
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="breadcrumb">Home</div>

    <header class="topbar">
      <a href="../index.php" class="brand">
        <img src="../assets/img/logo.png" alt="Logo Symptom Checker">
        <span>Symptom Checker</span>
      </a>

      <nav class="nav-links">
        <a href="../skrining/index.php">Checking</a>
        <a href="index.php" class="active">Home</a>
        <a href="../about.php">Language</a>
        <button class="hamburger" type="button" aria-label="Menu">
          <span></span>
        </button>
      </nav>
    </header>

    <main class="hero">
      <h1>Hello, <?php echo htmlspecialchars($nama_user); ?>!<br>How do you feel today?</h1>

      <section class="cards">
        <article class="card blue">
          <div>
            <h2>Start Checking</h2>
            <p>Every feeling has meaning. Let’s understand what you’re experiencing</p>
          </div>
          <div class="icon-box" aria-hidden="true">
            <!-- Brain icon -->
            <svg viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg">
              <g fill="none" stroke="<?php echo $yellow ?? '#fff100'; ?>" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M34 64c-8-6-10-18-5-26 4-7 13-11 21-9 3-8 12-13 20-13 13 0 24 10 25 23 8 2 14 10 14 19 0 7-4 14-10 17 3 5 4 11 2 17-3 8-11 13-19 13-4 7-12 11-20 11-12 0-22-8-25-19-8-1-15-7-18-15-2-7-1-14 3-19 3-4 8-7 12-9z"/>
                <path d="M56 36c4 6 7 13 7 20"/>
                <path d="M44 49c6 2 11 7 14 13"/>
                <path d="M78 41c-2 5-5 10-10 14"/>
                <path d="M88 58c-6 1-12 4-16 9"/>
                <path d="M39 81c6-1 12 0 17 4"/>
                <path d="M64 86c3-6 8-11 14-14"/>
                <path d="M78 28c2 4 3 8 3 13"/>
              </g>
            </svg>
          </div>
        </article>

        <article class="card mint">
          <div>
            <h2>History Check</h2>
            <p>Revisit your previous check results and see how your emotional condition changes over time</p>
          </div>
          <div class="icon-box" aria-hidden="true">
            <!-- Receipt icon -->
            <svg viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg">
              <g fill="none" stroke="<?php echo $yellow ?? '#fff100'; ?>" stroke-width="7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M38 20h44l10 10v62a8 8 0 0 1-8 8H38a8 8 0 0 1-8-8V28a8 8 0 0 1 8-8z"/>
                <path d="M82 20v12h12"/>
                <path d="M44 46h34"/>
                <path d="M44 60h34"/>
                <path d="M44 74h20"/>
                <path d="M86 84v16c0 4 3 8 8 8 4 0 8-3 8-8V84"/>
                <path d="M102 84h-12"/>
              </g>
            </svg>
          </div>
        </article>
      </section>

      <div class="wave" aria-hidden="true">
        <svg viewBox="0 0 1280 90" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
          <path d="M0 55 L160 74 L320 56 L480 35 L640 58 L800 38 L960 48 L1120 36 L1280 50 L1280 90 L0 90 Z" fill="#cdb4e6"/>
        </svg>
      </div>
    </main>
  </div>
</body>
</html>
