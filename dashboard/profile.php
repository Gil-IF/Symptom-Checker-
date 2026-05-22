<?php
session_start();

if (!isset($_SESSION['npm'])) {
    header('Location: login.php');
    exit;
}

$npm = $_SESSION['npm'];
$nama = $_SESSION['nama_panggilan'] ?? $npm;
$email = $_SESSION['email'] ?? $npm . '@student.local';
$avatar = 'assets/img/avatar.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - Symptom Checker</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:'Poppins',sans-serif;
    background:#aee9cd;
    min-height:100vh;
    color:#222;
}
.container{
    max-width:520px;
    margin:0 auto;
    min-height:100vh;
    padding:24px;
    text-align:center;
}
.back{
    display:inline-block;
    float:left;
    font-size:34px;
    text-decoration:none;
    color:#222;
    line-height:1;
}
.avatar{
    width:96px;
    height:96px;
    border-radius:50%;
    object-fit:cover;
    margin:20px auto 10px;
    display:block;
    box-shadow:0 8px 18px rgba(0,0,0,.15);
}
.edit-btn{
    display:inline-block;
    background:#d8b7e8;
    color:#333;
    font-size:12px;
    padding:4px 12px;
    border-radius:999px;
    text-decoration:none;
    margin-bottom:8px;
}
.name{
    font-size:22px;
    font-weight:700;
}
.email{
    font-size:14px;
    opacity:.75;
    margin-bottom:48px;
}
.menu{
    list-style:none;
}
.menu li{
    margin-bottom:16px;
}
.menu a{
    text-decoration:none;
    color:#222;
    font-weight:500;
}
.menu a.logout{
    color:#b00020;
    font-weight:600;
}
</style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back">‹</a>

    <img src="<?php echo $avatar; ?>" alt="Avatar" class="avatar">
    <a href="#" class="edit-btn">Edit</a>

    <div class="name"><?php echo htmlspecialchars($nama); ?></div>
    <div class="email"><?php echo htmlspecialchars($email); ?></div>

    <ul class="menu">
        <li><a href="#">Personal Summary</a></li>
        <li><a href="../riwayat/index.php">History Check</a></li>
        <li><a href="#">Account & Security</a></li>
        <li><a href="../logout.php" class="logout">Logout</a></li>
    </ul>
</div>
</body>
</html>