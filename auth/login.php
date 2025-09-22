<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
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
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        try {
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role']; 

                    // if ($user['role'] === 'admin') {
                    //     header("Location: ./dashboard/admin.php"); 
                    // } elseif ($user['role'] === 'creator') {
                    //     header("Location: ./dashboard/creator.php"); 
                    // } else {
                    //     header("Location: ./dashboard/bloger.php"); 
                    // }
                    // exit;
                    header("Location: ../blog.php");
                    } else {
                    echo "<p style='color:red;'>❌ Password did not verify</p>";
                }
            } else {
                echo "<p style='color:red;'>⚠️ No account found with that email</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>⚠️ Both fields are required</p>";
    }
}
?>

  <div class="form-container">
    <form action="" method="POST">
    <h2>Login form</h2>
     
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required>
      </div>

      <button type="submit" class="btnf">Login</button>
    </form>

    <div class="form-footer">
      <p>Don.t have an account? <a href="/space/auth/register.php">Register here</a></p>
    </div>
  </div>
<?php require('../layouts/footer.php');?>
</body>
</html>
