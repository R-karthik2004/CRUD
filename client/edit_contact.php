<?php
session_start();
require_once '../dbcon.php';
require '../auth_check.php';

// ✅ Check DB connection
$db = new Database();
$conn = $db->conn;
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// ✅ Fetch existing contact (OOP)
$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email, phone, subject, message FROM contacts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid Request");
}

$row = $result->fetch_assoc();

// ✅ Update logic
if (isset($_POST['update'])) {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $update = $conn->prepare("UPDATE contacts SET email=?, phone=?, subject=?, message=? WHERE id=? AND user_id=?");
    $update->bind_param("ssssii", $email, $phone, $subject, $message, $id, $user_id);

    if ($update->execute()) {
        header('Location: dashboard.php');
        exit;
    } else {
        $msg = "Update failed: " . $conn->error;
    }

    $update->close();
}
?>

<!doctype html>
<html lang="en">
<head>
  <title>Edit Contact (OOP)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/flatly/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 col-md-6">
  <div class="card p-4 shadow">
    <h3 class="text-center mb-3">Edit Contact</h3>
    <?php if(isset($msg)) echo "<div class='alert alert-danger'>$msg</div>"; ?>
    <form method="POST">
      <input type="email" name="email" value="<?= htmlspecialchars($row['email']); ?>" class="form-control mb-2" required>
      <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']); ?>" class="form-control mb-2" required>
      <input type="text" name="subject" value="<?= htmlspecialchars($row['subject']); ?>" class="form-control mb-2" required>
      <textarea name="message" class="form-control mb-3" rows="4" required><?= htmlspecialchars($row['message']); ?></textarea>
      <button type="submit" name="update" class="btn btn-primary w-100">Update</button>
      <a href="dashboard.php" class="btn btn-secondary mt-2 w-100">Back</a>
    </form>
  </div>
</div>
</body>
</html>
