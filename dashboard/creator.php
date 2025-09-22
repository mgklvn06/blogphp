<?php
session_start();
require('../db.php');

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

if (!in_array($_SESSION['role'], ['admin', 'creator'])) {
    die("Access denied. You do not have permission to create posts.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $tags = trim($_POST['tags']);
    $category = trim($_POST['category']);
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $image;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $error = "Error uploading image.";
        }
    }

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO posts 
            (user_id, title, content, tags, category, image, status, created_at) 
            VALUES (:user_id, :title, :content, :tags, :category, :image, :status, NOW())");

        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":tags", $tags);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":status", $status);

        if ($stmt->execute()) {
            header("Location: /space/dashboard/creator.php");
            exit;
        } else {
            $error = "Error creating post.";
        }
    } else {
        $error = "Title and content are required.";
    }
}

$stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->bindParam(":uid", $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Creator Dashboard</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      background: #f4f6f9;
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
      text-align: center;
      margin-bottom: 30px;
    }
    .sidebar a {
      color: #fff;
      text-decoration: none;
      padding: 10px;
      margin: 5px 0;
      border-radius: 6px;
      transition: 0.3s;
    }
    .sidebar a:hover {
      background: #3b3b5c;
    }

    .main {
      flex: 1;
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
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .form-container, .posts-container {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      max-width: 800px;
      margin: 20px auto;
    }
    label { font-weight: bold; margin-bottom: 5px; display: block; }
    input[type="text"], input[type="file"], textarea, select {
      width: 100%; padding: 10px; margin-bottom: 15px;
      border-radius: 6px; border: 1px solid #ddd; font-size: 14px;
    }
    button {
      background: #007bff; color: #fff; border: none;
      padding: 12px 20px; border-radius: 8px; font-size: 15px;
      cursor: pointer; transition: 0.3s;
    }
    button:hover { background: #0056b3; }
    .error { color: red; margin-bottom: 15px; }
    .post { border-bottom: 1px solid #eee; padding: 10px 0; }
    .post h3 { margin: 0; }
    .actions a { margin-right: 10px; color: #007bff; text-decoration: none; }
    .actions a:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>Creator</h2>
    <a href="#create">✍️ Create Post</a>
    <a href="#myposts">📄 My Posts</a>
    <a href="../auth/logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="topbar">
      <h1>Creator Dashboard</h1>
      <span>Logged in as: <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)</span>
    </div>

    <div class="form-container" id="create">
      <h2>Create New Post</h2>
      <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="POST" enctype="multipart/form-data">
          <label>Title:</label>
          <input type="text" name="title" required>

          <label>Content:</label>
          <textarea name="content" rows="6" required></textarea>

          <label>Tags:</label>
          <input type="text" name="tags" placeholder="comma,separated,tags">

          <label>Category:</label>
          <input type="text" name="category">

          <label>Image:</label>
          <input type="file" name="image">

          <label>Status:</label>
          <select name="status">
              <option value="draft">Draft</option>
              <option value="published">Published</option>
          </select>

          <button type="submit">Create Post</button>
      </form>
    </div>

    <div class="posts-container" id="myposts">
      <h2>My Posts</h2>
      <?php if (count($posts) === 0): ?>
        <p>No posts yet.</p>
      <?php endif; ?>
      <?php foreach ($posts as $p): ?>
        <div class="post">
          <h3><?= htmlspecialchars($p['title']) ?></h3>
          <p>Status: <?= $p['status'] ?> | Created: <?= $p['created_at'] ?></p>
          <?php if ($p['image']): ?>
            <img src="../uploads/<?= $p['image'] ?>" width="150"><br>
          <?php endif; ?>
          <div class="actions">
            <a href="../posts/posts_update.php?id=<?= $p['id'] ?>">Edit</a>
            <a href="../posts/posts_delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete this post?')">Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
