<?php
session_start();
require_once '../dbcon.php';
require '../auth_check.php';

$db = new Database();
$conn = $db->conn;

// ✅ Get user data
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        $_SESSION['message'] = "⚠️ User not found!";
        header("Location: users.php");
        exit();
    }
} else {
    $_SESSION['message'] = "⚠️ User ID not provided!";
    header("Location: users.php");
    exit();
}

// ✅ Update user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    $updateStmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $updateStmt->bind_param("sssi", $name, $email, $role, $id);

    if ($updateStmt->execute()) {
        $_SESSION['message'] = "✅ User updated successfully!";
    } else {
        $_SESSION['message'] = "❌ Error updating user!";
    }

    $updateStmt->close();
    header("Location: users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h3>Edit User</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">ID</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['id']) ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Client</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Update</button>
                <a href="users.php" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</body>

</html>
