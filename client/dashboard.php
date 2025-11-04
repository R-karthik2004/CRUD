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

// ✅ Use OOP prepared statement
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, subject, message, created_at FROM contacts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Dashboard (OOP)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/flatly/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary">Welcome, <?= htmlspecialchars($_SESSION['name']); ?></h2>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <p>You are logged in as: <strong><?= htmlspecialchars($_SESSION['email']); ?></strong></p>

    <h4>Your Submitted Contacts</h4>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['subject']); ?></td>
                        <td><?= htmlspecialchars($row['message']); ?></td>
                        <td><?= $row['created_at']; ?></td>
                        <td>
                            <a href="edit_contact.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_contact.php?id=<?= $row['id']; ?>"
                               onclick="return confirm('Are you sure you want to delete this contact?');"
                               class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No contacts found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a class="btn btn-success mb-3" href="../client/ecommerce/index.php">Back</a>
</div>
</body>
</html>
