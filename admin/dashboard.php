<?php
session_start();
require_once '../dbcon.php';
require '../mailer.php';

require '../auth_check.php';
// ---------- Check DB connection ----------
$db = new Database();
$conn = $db->conn;
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}

// ---------- Search Filter ----------
$search = "";
$whereClause = "";

if (!empty($_GET['search'])) {
  $search = trim($_GET['search']);
  $searchLike = "%{$search}%";
  $whereClause = "WHERE name LIKE ? OR email LIKE ? OR subject LIKE ?";
  $stmt = $conn->prepare("SELECT * FROM contacts $whereClause ORDER BY created_at ASC");
  $stmt->bind_param("sss", $searchLike, $searchLike, $searchLike);
} else {
  $stmt = $conn->prepare("SELECT * FROM contacts ORDER BY created_at DESC");
}

$stmt->execute();
$result = $stmt->get_result();

// ---------- Total Contacts Count ----------
$totalContacts = 0;
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM contacts");
$countStmt->execute();
$countResult = $countStmt->get_result();
if ($countRow = $countResult->fetch_assoc()) {
  $totalContacts = $countRow['total'];
}

$stmt->close();
$countStmt->close();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Admin Panel — Dashboard</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
      <a href="dashboard.php"><i class="fa-solid fa-address-book me-2"></i> Contacts
        <span class="badge bg-info ms-2"><?= $totalContacts ?></span>
      </a>
      <a href="users.php"><i class="fa-solid fa-users me-2"></i> Users</a>
      <a href="products.php"><i class="fa-solid fa-box me-2"></i> Products</a>
      <a href="../logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
    </nav>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="content-wrapper">
    <div class="topbar">
      <h3 class="mb-0">Admin Dashboard</h3>
      <div class="d-flex gap-2 align-items-center">
        <div class="search-box">
          <form method="GET" class="d-flex">
            <input name="search" class="form-control form-control-sm" placeholder="Search name, email, subject"
              value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-sm btn-primary ms-2" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
          </form>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
      </div>
    </div>

    <!-- ✅ SUCCESS MESSAGE DISPLAY -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ Contact deleted successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="row mb-3">
      <div class="col-md-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <div class="me-3 display-6 text-primary"><i class="fa-solid fa-address-book"></i></div>
            <div>
              <small class="text-muted">Total Contacts</small>
              <div class="h5 mb-0"><?= $totalContacts ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTACTS TABLE -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">All Contacts</h5>
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Date</th>
                <th style="width:150px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                      <?= htmlspecialchars($row['message']) ?>
                    </td>
                    <td><?= $row['created_at'] ?></td>
                    <td class="text-center">
                      <a href="reply_contact.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary mb-1"><i class="fa-solid fa-reply"></i></a>
                      <a href="delete_contact.php?id=<?= $row['id'] ?>&msg=deleted" onclick="return confirm('Are you sure you want to delete this contact?');"
                        class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center text-muted">No contacts found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
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
        <div class="modal-body">Are you sure you want to logout?</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>