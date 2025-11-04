<?php
session_start();
require_once '../dbcon.php';
require '../auth_check.php';

$db = new Database();
$conn = $db->conn;

// ✅ success message
$msg = "";
if (isset($_GET['msg'])) {
  $msg = $_GET['msg'];
}

// ✅ Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <!-- SIDEBAR -->
  <aside class="app-sidebar">
    <div class="brand text-white px-3">
      <i class="fa-solid fa-chart-simple me-2"></i> My Admin
    </div>
    <nav class="mt-3">
      <a href="index.php" class="active"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
      <a href="dashboard.php"><i class="fa-solid fa-address-book me-2"></i> Contacts</a>
      <a href="users.php"><i class="fa-solid fa-users me-2"></i> Users</a>
      <a href="products.php"><i class="fa-solid fa-box me-2"></i> Products</a>
      <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
    </nav>
  </aside>


<!-- Main Content -->
<div class="content-wrapper" style="margin-left:250px;padding:30px;">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Products List</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
      <i class="fa-solid fa-plus"></i> Add Product
    </button>
  </div>

  <!-- ✅ Success Message -->
  <?php if ($msg): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Product Table -->
  <div class="card">
    <div class="card-body">
      <table class="table table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Price</th>
            <th>Description</th>
            <th>Image</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id']; ?></td>
              <td><?= htmlspecialchars($row['product_name']); ?></td>
              <td>₹<?= htmlspecialchars($row['price']); ?></td>
              <td><?= htmlspecialchars($row['description']); ?></td>
              <td><img src="../uploads/<?= $row['image']; ?>" width="60"></td>
              <td>
                <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>
                <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?');"><i class="fa-solid fa-trash"></i></a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center text-muted">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ✅ Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="save_product.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="product_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label>Upload Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="save" class="btn btn-success">Add Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
