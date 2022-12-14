<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            margin: 0;
            padding: 0;
        }
        h1.mail-title {
            font-size: 1.5em;
            color: #36b3dd;
            margin: 0;
            padding: 0;
            width: 100%;
            text-align: center;
        }
        p.mail-content {
            margin: 20px auto;
            padding: 0;
            width: 100%;
            font-size: 1.2em;
            text-align: center;
        }
        a {
            display: block;
            width: max-content;
            color: #fff !important;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2em;
            text-align: center;
            padding: 10px 20px;
            margin: auto;
            border-radius: 20px;
            background-color: #36b3dd;
        }
    </style>
</head>
<body>
    <h1 class="mail-title">Reset Password</h1>
    <p class="mail-content">Hello {{ $username }}! Click the link below to reset your password</p>
    <a href="http://localhost:5173/recover-password?token={{ $token }}">Reset Password</a>
</body>
</html>
