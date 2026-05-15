<?php
// login.php
// Sesuaikan action form ke file proses login Anda, misalnya proses_login.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symptom Checker - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f5f7fb;
        }

        .topbar {
            height: 84px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 22px;
            color: #2f343a;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 44px;
            font-size: 18px;
            font-weight: 600;
        }

        .nav a {
            text-decoration: none;
            color: #30353b;
            position: relative;
            padding-bottom: 6px;
        }

        .nav a.active::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 4px;
            border-radius: 999px;
            background: #a9e2d3;
        }

        .page {
            min-height: calc(100vh - 84px);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: min(1180px, 100%);
            min-height: 670px;
            border-radius: 32px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            box-shadow: 0 14px 30px rgba(20, 36, 70, 0.14);
            background: linear-gradient(90deg, #63c0ba 0 48%, #6da3d8 48% 100%);
        }

        .left {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }

        .left img {
            width: 100%;
            max-width: 520px;
            height: auto;
            display: block;
        }

        .right {
            padding: 54px 52px 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome {
            text-align: center;
            margin-bottom: 18px;
        }

        .welcome h1 {
            font-size: 38px;
            line-height: 1;
            margin-bottom: 6px;
            color: #27313a;
        }

        .welcome p {
            font-size: 15px;
            color: #2f3842;
        }

        .field {
            position: relative;
            margin-top: 18px;
        }

        .field input {
            width: 100%;
            height: 58px;
            border: none;
            outline: none;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            padding: 0 20px 0 58px;
            font-size: 16px;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.5), 0 8px 18px rgba(0,0,0,.08);
        }

        .field .icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            opacity: .5;
            font-size: 18px;
        }

        .forgot {
            text-align: right;
            margin-top: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #26323c;
            text-decoration: none;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            align-self: center;
            margin-top: 22px;
            min-width: 160px;
            height: 56px;
            padding: 0 36px;
            border: none;
            border-radius: 999px;
            background: #a9e2d3;
            color: #27313a;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(0,0,0,.12);
            transition: .2s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 22px;
            color: #283039;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            background: rgba(18,30,48,.45);
            flex: 1;
        }

        .google-login {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .google-btn {
            width: 64px;
            height: 64px;
            border: none;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 18px rgba(0,0,0,.14);
            display: grid;
            place-items: center;
            cursor: pointer;
        }

        .signup {
            text-align: center;
            margin-top: 14px;
            font-size: 14px;
            color: #26323c;
        }

        .signup a {
            text-decoration: none;
            color: inherit;
            font-weight: 700;
            margin-left: 8px;
        }

        @media (max-width: 980px) {
            .card {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .left {
                padding: 22px;
            }

            .right {
                padding: 34px 24px 34px;
            }

            .welcome h1 {
                font-size: 32px;
            }
        }

        @media (max-width: 640px) {
            .topbar {
                padding: 0 16px;
                height: 74px;
            }

            .brand {
                font-size: 18px;
                gap: 10px;
            }

            .nav {
                gap: 18px;
                font-size: 15px;
            }

            .card {
                border-radius: 24px;
            }

            .left img {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">
            <img src="assets/img/logo.png" alt="Logo Symptom Checker">
            <span>Symptom Checker</span>
        </div>
        <nav class="nav">
            <a href="about.php">About</a>
            <a href="login.php" class="active">Login</a>
        </nav>
    </header>

    <main class="page">
        <section class="card">
            <div class="left">
                <img src="assets/img/tatap_awal.png" alt="Ilustrasi Login">
            </div>

            <div class="right">
                <div class="welcome">
                    <h1>Welcome back!</h1>
                    <p>Take your time, we’re here for you</p>
                </div>

                <form action="proses_login.php" method="post">
                    <div class="field">
                        <span class="icon">@</span>
                        <input type="text" name="npm" placeholder="NPM" required>
                    </div>

                    <div class="field">
                        <span class="icon">🔒</span>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <a href="#" class="forgot">Forgot password?</a>

                    <button type="submit" class="btn-login">Login</button>
                </form>

                <div class="divider">continue with</div>

                <div class="google-login">
                    <button type="button" class="google-btn" aria-label="Login with Google">
                        <svg width="32" height="32" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.655 32.91 29.251 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.94 3.043l5.657-5.657C33.95 6.053 29.227 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.652-.389-3.917z"/>
                            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 16.108 18.961 12 24 12c3.059 0 5.842 1.154 7.94 3.043l5.657-5.657C33.95 6.053 29.227 4 24 4c-7.732 0-14.415 4.36-17.694 10.691z"/>
                            <path fill="#4CAF50" d="M24 44c5.1 0 9.729-1.953 13.267-5.142l-6.113-5.158C29.02 35.292 26.715 36 24 36c-5.231 0-9.625-3.072-11.286-7.436l-6.522 5.025C9.429 39.556 16.227 44 24 44z"/>
                            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.044 12.044 0 0 1-4.149 5.7l.003-.002 6.113 5.158C36.835 37.918 44 32 44 24c0-1.341-.138-2.652-.389-3.917z"/>
                        </svg>
                    </button>
                </div>

                <div class="signup">
                    Don’t have an account?<a href="register.php">Register</a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
