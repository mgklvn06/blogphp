<?php
session_start();
header('Content-Type: application/json'); // ✅ force JSON output
error_reporting(E_ALL);
ini_set('display_errors', 0); // ✅ prevent raw HTML error output
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/error.log");

require __DIR__ . '/../db.php'; // ✅ safer relative path

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['comment'])) {
    $postId = intval($_POST['post_id']);
    $comment = trim($_POST['comment']);
    $userId = $_SESSION['user_id'];

    if ($comment !== '') {
        try {
            $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$postId, $userId, $comment]);

            echo json_encode([
                "success" => true,
                "username" => $_SESSION['username'],
                "comment" => htmlspecialchars($comment),
                "time" => "Just now"
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Database error",
                "error" => $e->getMessage() 
            ]);
        }
        exit;
    }
}

echo json_encode(["success" => false, "message" => "Invalid request"]);
