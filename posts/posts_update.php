<?php
session_start();
require('../db.php');

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->bindParam(":id", $post_id, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Post not found.");
}

if ($_SESSION['role'] !== 'admin' && $post['user_id'] != $_SESSION['user_id']) {
    die("Access denied. You cannot edit this post.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $tags = trim($_POST['tags']);
    $category = trim($_POST['category']);
    $status = $_POST['status'];

    $image = $post['image']; 
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $image;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $error = "Error uploading new image.";
        }
    }

    if (!empty($title) && !empty($content)) {
        $update = $conn->prepare("UPDATE posts 
            SET title = :title, content = :content, tags = :tags, category = :category, 
                image = :image, status = :status 
            WHERE id = :id");

        $update->bindParam(":title", $title);
        $update->bindParam(":content", $content);
        $update->bindParam(":tags", $tags);
        $update->bindParam(":category", $category);
        $update->bindParam(":image", $image);
        $update->bindParam(":status", $status);
        $update->bindParam(":id", $post_id, PDO::PARAM_INT);

        if ($update->execute()) {
            header("Location: creator.php#myposts");
            exit;
        } else {
            $error = "Error updating post.";
        }
    } else {
        $error = "Title and content are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Post</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      padding: 20px;
    }
    .container {
      background: #fff;
      max-width: 700px;
      margin: auto;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 { margin-bottom: 20px; }
    label { font-weight: bold; margin: 10px 0 5px; display: block; }
    input[type="text"], input[type="file"], textarea, select {
      width: 100%; padding: 10px; margin-bottom: 15px;
      border-radius: 6px; border: 1px solid #ccc; font-size: 14px;
    }
    button {
      background: #007bff; color: white;
      border: none; padding: 12px 20px;
      border-radius: 8px; cursor: pointer;
    }
    button:hover { background: #0056b3; }
    .error { color: red; margin-bottom: 15px; }
    .current-img img { max-width: 200px; margin-top: 10px; border-radius: 6px; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Post</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>

        <label>Content:</label>
        <textarea name="content" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>

        <label>Tags:</label>
        <input type="text" name="tags" value="<?= htmlspecialchars($post['tags']) ?>">

        <label>Category:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($post['category']) ?>">

        <label>Current Image:</label>
        <div class="current-img">
          <?php if ($post['image']): ?>
            <img src="../uploads/<?= $post['image'] ?>" alt="Post image">
          <?php else: ?>
            <p>No image uploaded.</p>
          <?php endif; ?>
        </div>

        <label>Change Image:</label>
        <input type="file" name="image">

        <label>Status:</label>
        <select name="status">
            <option value="draft" <?= $post['status']=='draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= $post['status']=='published' ? 'selected' : '' ?>>Published</option>
        </select>

        <button type="submit">Update Post</button>
    </form>
  </div>
</body>
</html>
