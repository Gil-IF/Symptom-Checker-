<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symptom Checker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        /* Seluruh halaman menjadi biru */
        body {
            background-color: #6FA8DC;
        }

        /* Link menutupi seluruh halaman */
        .full-page-link {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100vh;
            text-decoration: none;
            color: inherit;
        }

        /* Konten tengah */
        .container {
            text-align: center;
        }

        .container img {
            width: 300px;
            margin-bottom: 20px;
        }

        .container h1 {
            color: #D5F5E3;
            font-size: 64px;
            font-weight: bold;
            letter-spacing: 3px;
        }

        /* Efek hover */
        .full-page-link:hover {
            opacity: 0.95;
            transition: 0.3s;
        }
    </style>
</head>
<body>

<a href="login.php" class="full-page-link">
    <div class="container">
        <img src="assets/img/logo.png" alt="Logo Symptom Checker">
        <h1>SYMPTOM CHECKER</h1>
        <p class="click-text">Click Anywhere to Continue</p>
    </div>
</a>

</body>
</html>