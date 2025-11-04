<?php
session_start();
require_once '../dbcon.php';
require '../auth_check.php';

// ---------- Check DB connection ----------
$db = new Database();
$conn = $db->conn;
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


// ✅ Delete contact safely using OOP MySQLi
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Check DB connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // ✅ Prepared statement - SQL injection safe
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// ✅ Redirect back to dashboard
header('Location: dashboard.php?msg=deleted');
exit;
?>
