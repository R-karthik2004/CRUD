<?php
session_start();

// Prevent browser cache (very important for back button issue)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once  '../dbcon.php';
require_once '../auth_check.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}

// ---------- Total Contacts Count ----------
$totalContacts = 0;
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM contacts");
$countStmt->execute();
$countResult = $countStmt->get_result();
if ($countRow = $countResult->fetch_assoc()) {
  $totalContacts = $countRow['total'];
}
$countStmt->close();

// ---------- Total Users Count (only clients) ----------
$totalusers = 0;
$countUsersStmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = ?");
$role = 'client';
$countUsersStmt->bind_param("s", $role);
$countUsersStmt->execute();
$countUsersResult = $countUsersStmt->get_result();

if ($countUsersRow = $countUsersResult->fetch_assoc()) {
  $totalusers = $countUsersRow['total'];
}

$countUsersStmt->close();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>

  <!-- Bootstrap + FontAwesome + Chart.js -->
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

  <!-- MAIN CONTENT -->
  <div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="mb-0">Admin Dashboard</h3>
      <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </button>
    </div>

    <!-- STAT CARDS -->
    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm">
          <div class="d-flex align-items-center">
            <div class="me-3 display-6 text-primary"><i class="fa-solid fa-user-plus"></i></div>
            <div>
              <small class="text-muted">Total Contacts</small>
              <span class="badge bg-info ms-2"><?= $totalContacts ?></span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm">
          <div class="d-flex align-items-center">
            <div class="me-3 display-6 text-success"><i class="fa-solid fa-user-plus"></i></div>
            <div>
              <small class="text-muted">User Registrations</small>
              <span class="badge bg-info ms-2"><?= $totalusers ?></span>

            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm">
          <div class="d-flex align-items-center">
            <div class="me-3 display-6 text-warning"><i class="fa-solid fa-chart-line"></i></div>
            <div>
              <small class="text-muted">Bounce Rate</small>
              <div class="h5 mb-0">23%</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm">
          <div class="d-flex align-items-center">
            <div class="me-3 display-6 text-danger"><i class="fa-solid fa-dollar-sign"></i></div>
            <div>
              <small class="text-muted">Sales</small>
              <div class="h5 mb-0">$2,340</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- LOGOUT CONFIRMATION MODAL -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to logout?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <form action="../logout.php" method="POST" class="d-inline">
            <button type="submit" name="logout_btn" class="btn btn-danger">Logout</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>