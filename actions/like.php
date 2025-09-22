<?php
session_start();
require '/space/db.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "Not logged in"]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);
        $user_id = $_SESSION['user_id'];

        $check = $conn->prepare("SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?");
        $check->execute([$user_id, $post_id]);

        if ($check->rowCount() > 0) {
            $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?")
                 ->execute([$user_id, $post_id]);
            $liked = false;
        } else {
            $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)")
                 ->execute([$user_id, $post_id]);
            $liked = true;
        }

        $count = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
        $count->execute([$post_id]);
        $likeCount = $count->fetchColumn();

        echo json_encode([
            "success" => true,
            "liked"   => $liked,
            "count"   => $likeCount
        ]);
        exit;
    }

    echo json_encode(["success" => false, "message" => "Invalid request"]);
} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "message" => "PHP Error",
        "error"   => $e->getMessage()
    ]);
}
