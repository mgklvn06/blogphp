<?php
session_start();
require('../db.php');

if ($_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only!");
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");

$posts = $conn->query("SELECT p.*, u.username 
                       FROM posts p 
                       JOIN users u ON p.user_id = u.id 
                       ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
    }
    .sidebar {
      width: 220px;
      background: #1e1e2f;
      color: #fff;
      padding: 20px;
      display: flex;
      flex-direction: column;
    }
    .sidebar h2 {
      margin-bottom: 30px;
      text-align: center;
    }
    .sidebar a {
      color: #fff;
      text-decoration: none;
      padding: 10px;
      display: block;
      margin: 5px 0;
      border-radius: 6px;
      transition: 0.3s;
    }
    .sidebar a:hover {
      background: #3b3b5c;
    }

    .main {
      flex: 1;
      background: #f4f6f9;
      padding: 20px;
      overflow-y: auto;
    }
    .topbar {
      background: #fff;
      padding: 15px 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .topbar h1 { margin: 0; }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
      gap: 20px;
    }
    .card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      margin-top: 20px;
    }
    table th, table td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    table th {
      background: #f8f9fa;
    }
    .actions a {
      margin-right: 10px;
      color: #007bff;
      text-decoration: none;
    }
    .actions a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Admin</h2>
    <a href="/space/posts/posts_create.php">➕ Create Post</a>
    <a href="/space/auth/logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="topbar">
      <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
      <span>Role: <?php echo $_SESSION['role']; ?></span>
    </div>

    <div class="cards">
      <div class="card">
        <h2>Total Users</h2>
        <p><?php echo $users->rowCount(); ?></p>
      </div>
      <div class="card">
        <h2>Total Posts</h2>
        <p><?php echo $posts->rowCount(); ?></p>
      </div>
    </div>

    <h2>Recent Users</h2>
    <table>
      <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr>
      <?php while ($u = $users->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= $u['username'] ?></td>
          <td><?= $u['email'] ?></td>
          <td><?= $u['role'] ?></td>
          <td class="actions">
            <a href="../admin/edit_user.php?id=<?= $u['id'] ?>">Edit</a>
            <a href="../admin/delete_user.php?id=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>

    <h2>Recent Posts</h2>
    <table>
      <tr><th>ID</th><th>Title</th><th>Status</th><th>Author</th><th>Actions</th></tr>
      <?php while ($p = $posts->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= $p['title'] ?></td>
          <td><?= $p['status'] ?></td>
          <td><?= $p['username'] ?></td>
          <td class="actions">
            <a href="../posts/posts_update.php?id=<?= $p['id'] ?>">Edit</a>
            <a href="../posts/posts_delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete this post?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>
