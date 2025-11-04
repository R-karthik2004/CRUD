<?php
session_start();
require_once '../dbcon.php';
require '../auth_check.php';

// ✅ Check DB connection
$db = new Database();
$conn = $db->conn;
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


// ✅ Check if ID is set
$user_id = $_SESSION['user_id'];
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET['id']);

// ✅ OOP Prepared Statement for Delete
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    echo "<script>alert('🗑️ Contact deleted successfully!'); window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('❌ Error deleting contact!'); window.location='dashboard.php';</script>";
}

$stmt->close();
$conn->close();
exit();
?>
