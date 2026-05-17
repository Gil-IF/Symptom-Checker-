<?php
session_start();

if (!isset($_SESSION['npm'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checking - Symptom Checker</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f4f6;
    color: #2f3137;
}
.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 12px 16px 24px;
}
.breadcrumb {
    color: #b5b5b5;
    font-size: 14px;
    margin-bottom: 8px;
}
.screen {
    background: #6ea3d9;
    border-radius: 0;
    padding: 32px;
    min-height: calc(100vh - 90px);
}
.card {
    background: #ffffff;
    border-radius: 22px;
    min-height: 620px;
    box-shadow: 0 12px 24px rgba(0,0,0,.12);
    padding: 40px 48px;
    display: flex;
    flex-direction: column;
}
.header {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    margin-bottom: 80px;
}
.header img {
    width: 70px;
    height: 70px;
    object-fit: contain;
}
.header h1 {
    font-size: 28px;
    font-weight: 700;
}
.content {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 0 80px;
}
.content p {
    font-size: 28px;
    line-height: 1.7;
    font-weight: 500;
    max-width: 900px;
}
.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 40px;
}
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 150px;
    height: 58px;
    padding: 0 36px;
    border: none;
    border-radius: 999px;
    background: #a9e2d3;
    color: #2f3137;
    font-size: 20px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 8px 18px rgba(0,0,0,.15);
    transition: .2s ease;
}
.btn:hover {
    transform: translateY(-2px);
}
@media (max-width: 768px) {
    .screen { padding: 16px; }
    .card { padding: 24px; min-height: auto; }
    .header { margin-bottom: 40px; }
    .content { padding: 0 10px; }
    .content p { font-size: 22px; }
    .actions { margin-top: 30px; }
    .btn { min-width: 120px; height: 50px; font-size: 18px; }
}
</style>
</head>
<body>
<div class="container">
    <div class="breadcrumb">Checking</div>

    <section class="screen">
        <div class="card">
            <div class="header">
                <img src="../assets/img/logo.png" alt="Logo Symptom Checker">
                <h1>Symptom Checker</h1>
            </div>

            <div class="content">
                <p>
                    Take a moment to understand your condition.<br>
                    Answer each question honestly, so the results are
                    more in line with how you feel.
                </p>
            </div>

            <div class="actions">
                <a href="../dashboard/index.php" class="btn">Back</a>
                <a href="step.php?no=1" class="btn">Next</a>
            </div>
        </div>
    </section>
</div>
</body>
</html>