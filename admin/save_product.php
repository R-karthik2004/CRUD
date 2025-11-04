<?php
require_once '../dbcon.php';
$db = new Database();
$conn = $db->conn;

if (isset($_POST['save'])) {
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];

    $image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, price, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $desc, $image);
        $stmt->execute();
        header("Location: products.php?msg=Product added successfully!");
        exit;
    } else {
        header("Location: products.php?msg=Image upload failed!");
    }
}
?>
