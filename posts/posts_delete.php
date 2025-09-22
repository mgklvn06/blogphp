<?php
session_start();
require('../db.php');

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = :id");
$stmt->bindParam(":id", $post_id, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Post not found.");
}

if ($_SESSION['role'] !== 'admin' && $post['user_id'] != $_SESSION['user_id']) {
    die("You do not have permission to delete this post.");
}

$delete = $conn->prepare("DELETE FROM posts WHERE id = :id");
$delete->bindParam(":id", $post_id, PDO::PARAM_INT);

if ($delete->execute()) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../dashboard/admin.php");
    } else {
        header("Location: /space/dashboard/creator.php#myposts");
    }
    exit;
} else {
    echo "Error deleting post.";
}
