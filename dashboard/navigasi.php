<?php
// navigasi.php
// Sidebar / navigation drawer untuk dashboard

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$npm = $_SESSION['npm'] ?? '240810xxxxxx';
$nama_panggilan = $_SESSION['nama_panggilan'] ?? 'Zeeka';
$email = $_SESSION['email'] ?? 'zeeka@gmail.com';
$avatar = $_SESSION['avatar'] ?? 'assets/img/avatar.png';
?>

<style>
  :root{
    --nav-purple:#cfb4df;
    --nav-purple-light:#dcc4ea;
    --text:#222;
    --muted:#4a4a4a;
    --active:#e9dbf4;
    --shadow:0 10px 28px rgba(0,0,0,.12);
  }

  .side-toggle-btn{
    position:fixed;
    top:18px;
    right:18px;
    z-index:1101;
    border:none;
    background:var(--nav-purple);
    width:54px;
    height:54px;
    border-radius:16px;
    display:grid;
    place-items:center;
    cursor:pointer;
    box-shadow:var(--shadow);
  }

  .side-toggle-btn span,
  .side-toggle-btn span::before,
  .side-toggle-btn span::after{
    content:'';
    display:block;
    width:22px;
    height:3px;
    border-radius:999px;
    background:#222;
    position:relative;
  }
  .side-toggle-btn span::before{position:absolute;top:-7px;left:0}
  .side-toggle-btn span::after{position:absolute;top:7px;left:0}

  .nav-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.18);
    opacity:0;
    pointer-events:none;
    transition:.25s ease;
    z-index:1100;
  }

  .nav-overlay.show{
    opacity:1;
    pointer-events:auto;
  }

  .nav-drawer{
    position:fixed;
    top:0;
    right:-380px;
    width:360px;
    max-width:92vw;
    height:100vh;
    background:var(--nav-purple);
    box-shadow:var(--shadow);
    z-index:1110;
    transition:right .28s ease;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    border-top-left-radius:26px;
    border-bottom-left-radius:26px;
  }

  .nav-drawer.open{
    right:0;
  }

  .drawer-top{
    padding:22px 20px 18px;
    position:relative;
  }

  .drawer-close{
    border:none;
    background:transparent;
    font-size:34px;
    line-height:1;
    cursor:pointer;
    color:#1f1f1f;
    padding:0;
    margin-bottom:8px;
  }

  .profile-box{
    display:flex;
    align-items:center;
    gap:14px;
    margin-top:4px;
  }

  .profile-box img{
    width:64px;
    height:64px;
    border-radius:50%;
    object-fit:cover;
    box-shadow:0 6px 14px rgba(0,0,0,.16);
    background:#fff;
  }

  .profile-info .name{
    font-weight:700;
    color:var(--text);
    font-size:18px;
    line-height:1.1;
  }

  .profile-info .mail{
    font-size:13px;
    color:#2e2e2e;
    opacity:.85;
    margin-top:4px;
  }

  .drawer-menu{
    list-style:none;
    margin:12px 0 0;
    padding:0 0 20px;
  }

  .drawer-menu li a{
    display:flex;
    align-items:center;
    gap:14px;
    text-decoration:none;
    color:var(--text);
    font-weight:700;
    padding:14px 22px;
    margin:0 10px 6px;
    border-radius:12px;
    transition:.2s ease;
  }

  .drawer-menu li a:hover{
    background:rgba(255,255,255,.18);
  }

  .drawer-menu li a.active{
    background:var(--active);
  }

  .drawer-menu .icon{
    width:26px;
    text-align:center;
    font-size:22px;
    flex:0 0 26px;
  }

  .drawer-footer{
    margin-top:auto;
    padding:18px 20px 24px;
    font-size:12px;
    color:#2a2a2a;
    opacity:.75;
  }

  body.nav-open{
    overflow:hidden;
  }
</style>

<button class="side-toggle-btn" type="button" aria-label="Buka navigasi" onclick="openNavDrawer()">
  <span></span>
</button>

<div class="nav-overlay" id="navOverlay" onclick="closeNavDrawer()"></div>

<aside class="nav-drawer" id="navDrawer" aria-label="Navigasi samping">
  <div class="drawer-top">
    <button class="drawer-close" type="button" aria-label="Tutup navigasi" onclick="closeNavDrawer()">&lsaquo;</button>

    <div class="profile-box">
      <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar pengguna">
      <div class="profile-info">
        <div class="name"><?php echo htmlspecialchars($nama_panggilan); ?></div>
        <div class="mail"><?php echo htmlspecialchars($email); ?></div>
      </div>
    </div>
  </div>

  <ul class="drawer-menu">
    <li><a href="profile.php"><span class="icon">👤</span>Profile</a></li>
    <li><a href="index.php" class="active"><span class="icon">🏠</span>Home</a></li>
    <li><a href="../skrining/index.php"><span class="icon">📝</span>Start Checking</a></li>
    <li><a href="../riwayat/index.php"><span class="icon">⏳</span>History</a></li>
    <li><a href="../about.php"><span class="icon">❓</span>About</a></li>
  </ul>

  <div class="drawer-footer">
    NPM: <?php echo htmlspecialchars($npm); ?>
  </div>
</aside>

<script>
  function openNavDrawer() {
    document.getElementById('navDrawer').classList.add('open');
    document.getElementById('navOverlay').classList.add('show');
    document.body.classList.add('nav-open');
  }

  function closeNavDrawer() {
    document.getElementById('navDrawer').classList.remove('open');
    document.getElementById('navOverlay').classList.remove('show');
    document.body.classList.remove('nav-open');
  }
</script>
