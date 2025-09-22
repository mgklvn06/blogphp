<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php
require('../layouts/navbar.php');
require('../db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($email) && !empty($password)) {
        $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO users (username, email, password) 
                    VALUES (:username, :email, :password)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindValue(":password", $hashedpassword);

            if ($stmt->execute()) {
                header("Location: ../auth/login.php");
            } else {
                echo "<p style='color:red;'>Registration failed ❌</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>All fields are required ⚠️</p>";
    }
}
?>
  <div class="form-container">
    <h2>Create Account</h2>
    <form action="" method="POST">
      <div class="form-group">
        <label for="username">Full Name</label>
        <input type="text" id="username" name="username" placeholder="Enter your name" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required>
      </div>

      <button type="submit" class="btnf">Register</button>
    </form>

    <div class="form-footer">
      <p>Already have an account? <a href="/space/auth/login.php">Login here</a></p>
    </div>
  </div>
<?php require('../layouts/footer.php');?>
</body>
</html>
