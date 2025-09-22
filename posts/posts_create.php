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
            header("Location: ../dashboard/admin.php");
            exit;
        } else {
            $error = "Error creating post.";
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
  <title>Create Post</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      background: #f4f6f9;
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
    .form-container {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      max-width: 700px;
      margin: auto;
    }
    h2 {
      margin-bottom: 20px;
    }
    label {
      font-weight: bold;
      margin-bottom: 5px;
      display: block;
    }
    input[type="text"],
    input[type="file"],
    textarea,
    select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 6px;
      border: 1px solid #ddd;
      font-size: 14px;
    }
    button {
      background: #007bff;
      color: #fff;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      font-size: 15px;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

  <div class="main">
    <div class="topbar">
      <h1>Create Post</h1>
      <span>Logged in as: <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)</span>
    </div>

    <div class="form-container">
      <h2>New Post</h2>
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
  </div>
</body>
</html>
