<?php
session_start();
require('../config/db.php');

if ($_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE posts SET title=?, content=?, status=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $content, $status, $id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error updating post.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Post</title></head>
<body>
<h2>Edit Post</h2>
<form method="POST">
    <input type="text" name="title" value="<?php echo $post['title']; ?>" required><br>
    <textarea name="content" required><?php echo $post['content']; ?></textarea><br>
    <select name="status">
        <option value="draft" <?php if ($post['status']=="draft") echo "selected"; ?>>Draft</option>
        <option value="published" <?php if ($post['status']=="published") echo "selected"; ?>>Published</option>
    </select>
    <button type="submit">Update</button>
</form>
</body>
</html>
