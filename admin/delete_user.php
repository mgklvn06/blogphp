<?php
session_start();
require('/space/db.php');

if ($_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id == $_SESSION['user_id']) {
        die(" You cannot delete your own account.");
    }

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: ../dashboard/admin.php");
            exit;
        } else {
            echo "⚠️ Error deleting user.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
