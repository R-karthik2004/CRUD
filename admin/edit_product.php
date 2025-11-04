<?php
require_once '../dbcon.php';
$db = new Database();
$conn = $db->conn;

$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if (isset($_POST['update'])) {
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $old_image = $_POST['old_image'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);
    } else {
        $image = $old_image;
    }

    $stmt = $conn->prepare("UPDATE products SET product_name=?, price=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $desc, $image, $id);
    $stmt->execute();

    header("Location: products.php?msg=Product updated successfully!");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card">
    <div class="card-header bg-warning text-white">
      <h4>Edit Product</h4>
    </div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label>Product Name</label>
          <input type="text" name="product_name" class="form-control" value="<?= $product['product_name']; ?>" required>
        </div>
        <div class="mb-3">
          <label>Price</label>
          <input type="number" name="price" class="form-control" value="<?= $product['price']; ?>" required>
        </div>
        <div class="mb-3">
          <label>Description</label>
          <textarea name="description" class="form-control" required><?= $product['description']; ?></textarea>
        </div>
        <div class="mb-3">
          <label>Change Image (optional)</label>
          <input type="file" name="image" class="form-control">
          <input type="hidden" name="old_image" value="<?= $product['image']; ?>">
          <img src="../uploads/<?= $product['image']; ?>" width="100" class="mt-2">
        </div>
        <button type="submit" name="update" class="btn btn-success">Update Product</button>
        <a href="products.php" class="btn btn-secondary">Back</a>
      </form>
    </div>
  </div>
</div>

</body>
</html>
