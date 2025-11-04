<?php
session_start();
require_once '../dbcon.php';
require '../auth_check.php';

// Prevent back button caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// ✅ Database Connection
$db = new Database();
$conn = $db->conn;
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}

// ✅ Fetch CLIENT users
$clientStmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users WHERE role = 'client' ORDER BY id DESC");
$clientStmt->execute();
$clientResult = $clientStmt->get_result();

// ✅ Fetch ADMIN users
$adminStmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users WHERE role = 'admin' ORDER BY id DESC");
$adminStmt->execute();
$adminResult = $adminStmt->get_result();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Users List</title>
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
      <a href="index.php"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
      <a href="dashboard.php"><i class="fa-solid fa-address-book me-2"></i> Contacts</a>
      <a href="users.php" class="active"><i class="fa-solid fa-users me-2"></i> Users</a>
      <a href="products.php"><i class="fa-solid fa-box me-2"></i> Products</a>
      <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
    </nav>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="content-wrapper">
    <?php if (isset($_SESSION['message'])): ?>
    <?php 
      $msg = $_SESSION['message']; 
      $alertType = 'info';

      if (str_contains($msg, '✅')) $alertType = 'success';
      elseif (str_contains($msg, '❌')) $alertType = 'danger';
      elseif (str_contains($msg, '⚠️')) $alertType = 'warning';
    ?>
    <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="mb-0">Registered Users</h3>
      <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </button>
    </div>

    <!-- CLIENT USERS TABLE -->
    <div class="card">
      <div class="card-body">
      <h5 class="mb-3 text-primary"><i class="fa-solid fa-user"></i> Client Users</h5>
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($clientResult->num_rows > 0): ?>
            <?php while ($row = $clientResult->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['role']) ?></span></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                  <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                    <i class="fa-solid fa-pen"></i> Edit
                  </a>
                  <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                    <i class="fa-solid fa-trash"></i> Delete
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted">No client users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
    <br>
    <!-- ADMIN USERS TABLE -->
    <div class="card">
      <div class="card-body">
      <h5 class="mb-3 text-danger"><i class="fa-solid fa-user-shield"></i> Admin Users</h5>
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($adminResult->num_rows > 0): ?>
            <?php while ($row = $adminResult->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><span class="badge bg-danger"><?= htmlspecialchars($row['role']) ?></span></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                  <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                    <i class="fa-solid fa-pen"></i> Edit
                  </a>
                  <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this admin?')">
                    <i class="fa-solid fa-trash"></i> Delete
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted">No admin users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    </div>
  </div>

  <!-- LOGOUT MODAL -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">Are you sure you want to logout?</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <form action="../logout.php" method="POST" class="d-inline">
            <button type="submit" name="logout_btn" class="btn btn-danger">Logout</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>