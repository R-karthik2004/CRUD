<?php
session_start();
require_once '../dbcon.php';
require '../auth_check.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $db = new Database();
    $conn = $db->conn;

    // Delete user (contacts auto delete because of ON DELETE CASCADE)
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ User deleted successfully!";
    } else {
        $_SESSION['message'] = "❌ Error deleting user!";
    }

    $stmt->close();
    header("Location: users.php");
    exit();
} else {
    $_SESSION['message'] = "⚠️ Invalid user ID.";
    header("Location: users.php");
    exit();
}
?>
