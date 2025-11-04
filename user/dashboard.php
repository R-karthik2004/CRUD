<?php
session_start();
include_once '../dbcon.php';
require '../client/security.php';

$db = new Database();
$conn = $db->conn;
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// ✅ Fetch user's contact messages
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];
$result = mysqli_query($conn, "SELECT * FROM contacts WHERE user_id = '$user_id' ORDER BY created_at DESC");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/litera/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .card {
      border-radius: 1rem;
    }
    .btn-custom {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>👋 Welcome, <?= htmlspecialchars($user_name) ?></h3>
    <a href="../logout.php" class="btn btn-danger btn-custom">Logout</a>
  </div>

  <div class="card shadow p-4">
    <h4 class="text-primary mb-3">📩 Your Contact Submissions</h4>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['subject']) ?></td>
              <td><?= htmlspecialchars($row['message']) ?></td>
              <td><?= $row['created_at'] ?></td>
              <td>
                <a href="delete_message.php?id=<?= $row['id'] ?>" 
                   class="btn btn-sm btn-danger btn-custom"
                   onclick="return confirm('Delete this message?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center text-muted">No messages found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
