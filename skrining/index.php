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
    <title>Skrining – Symptom Checker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/skrining.css">

</head>
<body>

    <section class="screen">
        <div class="breadcrumb">  </div>

        <div class="card">
            <div class="header">
                <img src="../assets/img/logo.png" alt="Logo Symptom Checker">
                <h1>Symptom Checker</h1>
            </div>

            <div class="content">
                <p>
                    Luangkan waktu sejenak untuk memahami kondisi Anda saat ini.<br>
                    Jawab setiap pertanyaan dengan jujur agar hasilnya benar-benar menggambarkan apa yang Anda rasakan.
                </p>

                <div class="warning">
                    <strong>Perhatian:</strong> Hasil skrining ini hanyalah indikasi awal dan tidak menggantikan peran ahli. Jika hasil skrining Anda menunjukkan tingkat berat hingga sangat berat, kami sangat menyarankan Anda untuk segera berkonsultasi dengan tenaga profesional.
                </div>
            </div>

            <div class="actions">
                <a href="../dashboard/index.php" class="btn">Kembali</a>
                <a href="step.php?no=1" class="btn">Lanjut</a>
            </div>
        </div>
    </section>

</body>
</html>