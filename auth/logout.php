<?php
session_start();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
    <link rel="stylesheet" href="../style.css">
    <meta http-equiv="refresh" content="3;url=/space/auth/login.php">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f9f9f9;
        }
        .logout-box {
            text-align: center;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .logout-box h1 {
            color: #333;
        }
        .logout-box p {
            margin: 10px 0;
            color: #555;
        }
        .logout-box a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            text-decoration: none;
            background: #007BFF;
            color: #fff;
            border-radius: 5px;
        }
        .logout-box a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="logout-box">
        <h1>✅ You’ve been logged out</h1>
        <p>You will be redirected to the login page in a few seconds.</p>
        <p><a href="/space/auth/login.php">Go to Login</a></p>
    </div>
</body>
</html>
