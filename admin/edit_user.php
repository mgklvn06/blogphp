<?php
session_start();
require('/space/db.php');

if ($_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found!");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->bindParam(":role", $role, PDO::PARAM_STR);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: ../dashboard/admin.php");
        exit;
    } else {
        echo "Error updating role.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit User Role</title>
</head>
<body>
  <h2>Edit Role for <?php echo htmlspecialchars($user['username']); ?></h2>
  <form method="POST">
    <select name="role" required>
        <option value="creator" <?php if ($user['role']=="creator") echo "selected"; ?>>Creator</option>
        <option value="blogger" <?php if ($user['role']=="blogger") echo "selected"; ?>>Blogger</option>
        <option value="admin" <?php if ($user['role']=="admin") echo "selected"; ?>>Admin</option>
    </select>
    <button type="submit">Update</button>
  </form>
</body>
</html>
