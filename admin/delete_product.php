<?php
require_once '../dbcon.php';
$db = new Database();
$conn = $db->conn;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $conn->prepare("DELETE FROM products WHERE id=?");
    $query->bind_param("i", $id);
    $query->execute();

    header("Location: products.php?msg=Product deleted successfully!");
    exit;
}
?>
