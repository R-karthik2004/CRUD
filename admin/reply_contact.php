<?php
session_start();
require_once '../dbcon.php';
require '../mailer.php'; // mailer function include
require '../auth_check.php';

// ---------- Check DB connection ----------
$db = new Database();
$conn = $db->conn;
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


// ✅ Validate and fetch contact
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prepare and execute query safely
$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Invalid Contact ID");
}

$row = $result->fetch_assoc();
$stmt->close();

// ✅ Handle reply form
if (isset($_POST['send_reply'])) {
    $reply = trim($_POST['reply']);
    $to = $row['email'];
    $subject = "Re: " . $row['subject'];
    $body = "<p><b>Admin Reply:</b></p><p>" . htmlspecialchars($reply) . "</p>";

    if (sendMail($to, $subject, $body)) {
        $msg = "✅ Reply sent successfully!";
    } else {
        $msg = "❌ Failed to send reply.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reply to Contact</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/litera/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 col-md-6">
  <div class="card p-4 shadow">
    <h4>📧 Reply to <?= htmlspecialchars($row['name']); ?></h4>
    <p><strong>Email:</strong> <?= htmlspecialchars($row['email']); ?></p>
    <p><strong>Subject:</strong> <?= htmlspecialchars($row['subject']); ?></p>
    <p><strong>Message:</strong><br><?= nl2br(htmlspecialchars($row['message'])); ?></p>
    <hr>
    <?php if(isset($msg)) echo "<div class='alert alert-info'>$msg</div>"; ?>
    <form method="POST">
      <textarea name="reply" class="form-control mb-3" rows="5" placeholder="Type your reply here..." required></textarea>
      <button type="submit" name="send_reply" class="btn btn-primary w-100">Send Reply</button>
      <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Back</a>
    </form>
  </div>
</div>
</body>
</html>
