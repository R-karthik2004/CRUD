<?php
session_start();
require_once 'dbcon.php';

$message = '';

// ✅ Initialize Database
$db = new Database();
$conn = $db->conn;

// ✅ Check Database Connection
if (!$conn) {
    die("Database connection failed!");
}

// ✅ Login Class
class UserLogin {
    private $conn;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
    }

    public function login($email, $password) {
        // Prepare SQL Query
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // 🔹 If email not found
        if ($result->num_rows == 0) {
            return "⚠️ Email not registered!";
        }

        $user = $result->fetch_assoc();

        // 🔹 Check password (works for hashed or plain)
        if (password_verify($password, $user['password']) || $password === $user['password']) {

            // ✅ Set session values
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // ✅ Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
                exit;
            } else {
                header("Location: client/ecommerce/index.php");
                exit;
            }

        } else {
            // ❌ Wrong password
            return "❌ Invalid Password!";
        }
    }
}

// ✅ Handle Form Submission
if (isset($_POST['login'])) {
    $userLogin = new UserLogin($conn);
    $message = $userLogin->login(trim($_POST['email']), trim($_POST['password']));
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/flatly/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-lg">
        <div class="card-header text-center bg-primary text-white">
          <h4>User Login</h4>
        </div>
        <div class="card-body">

          <?php if ($message != ''): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($message); ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-grid">
              <button type="submit" name="login" class="btn btn-success">Login</button>
            </div>
          </form>

          <div class="text-center mt-3">
            <a href="register.php">New user? Register here</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
