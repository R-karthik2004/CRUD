<?php
require_once 'dbcon.php';
$message = '';

$db = new Database();
$conn = $db->conn;

if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

class UserRegistration {
    private $conn;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
    }

    public function register($name, $email, $password, $confirmPassword) {
        if ($password !== $confirmPassword) {
            return "❌ Passwords do not match!";
        }

        // Check if email already exists
        $checkStmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            return "⚠️ Email already registered!";
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user (default role: client)
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            return "✅ Registration successful! You can now <a href='index.php'>login</a>.";
        } else {
            return "❌ Registration failed! Please try again.";
        }
    }
}

// Handle form submit
if (isset($_POST['register'])) {
    $registerUser = new UserRegistration($conn);
    $message = $registerUser->register($_POST['name'], $_POST['email'], $_POST['password'], $_POST['confirm_password']);
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>User Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/flatly/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-lg">
        <div class="card-header text-center bg-success text-white">
          <h4>User Registration</h4>
        </div>
        <div class="card-body">

          <?php if ($message != ''): ?>
            <div class="alert alert-info text-center"><?= $message; ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label>Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Confirm Password</label>
              <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="d-grid">
              <button type="submit" name="register" class="btn btn-primary">Register</button>
            </div>
          </form>

          <div class="text-center mt-3">
            <a href="index.php">Already have an account? Login</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
