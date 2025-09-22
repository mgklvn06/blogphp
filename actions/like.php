<?php
session_start();
header('Content-Type: application/json'); // force JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/error.log");

require __DIR__ . '/../db.php'; // adjust path if needed

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    try {
        // Check if user already liked
        $check = $conn->prepare("SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?");
        $check->execute([$user_id, $post_id]);

        if ($check->rowCount() > 0) {
            // Unlike
            $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?")
                 ->execute([$user_id, $post_id]);
            $liked = false;
        } else {
            // Like
            $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)")
                 ->execute([$user_id, $post_id]);
            $liked = true;
        }

        // Get updated count
        $count = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
        $count->execute([$post_id]);
        $likeCount = $count->fetchColumn();

        echo json_encode([
            "success" => true,
            "liked" => $liked,
            "count" => $likeCount
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

echo json_encode(["success" => false, "message" => "Invalid request"]);
