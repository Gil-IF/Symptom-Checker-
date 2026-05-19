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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f6;
            color: #2f3137;
        }

        /* Layar penuh biru */
        .screen {
            background: #6ea3d9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }

        .breadcrumb {
            color: #ffffff;
            font-size: 14px;
            margin-bottom: 12px;
            max-width: 900px;
            width: 100%;
        }

        .card {
            background: #ffffff;
            border-radius: 22px;
            box-shadow: 0 12px 24px rgba(0,0,0,.12);
            padding: 40px 48px;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 900px;
        }

        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            margin-bottom: 60px;
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
            padding: 0 20px;
            margin-bottom: 40px;
        }

        .content p {
            font-size: 24px;
            line-height: 1.7;
            font-weight: 500;
            max-width: 700px;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
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
            .card { padding: 24px; }
            .header { margin-bottom: 40px; }
            .content { padding: 0 10px; }
            .content p { font-size: 20px; }
            .btn { min-width: 120px; height: 50px; font-size: 18px; }
        }
    </style>
</head>
<body>

    <section class="screen">
        <div class="breadcrumb">Checking</div>

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

</body>
</html>