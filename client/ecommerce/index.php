<?php
session_start();
require_once '../../dbcon.php';
require '../../mailer.php';
require '../../auth_check.php';

// ✅ Database Connection
$db = new Database();
$conn = $db->conn;
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}

$adminEmail = 'rkarthik0806@gmail.com';

// =============================
// 🧱 CLASS: Contact Form Handler
// =============================
class ContactHandler
{
  private $conn;
  private $adminEmail;

  public function __construct($conn, $adminEmail)
  {
    $this->conn = $conn;
    $this->adminEmail = $adminEmail;
  }

  private function validate($data)
  {
    $errors = [];
    if (empty($data['name'])) $errors[] = "Name required";
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required";
    if (empty($data['subject'])) $errors[] = "Subject required";
    if (empty($data['message']) || strlen($data['message']) < 5) $errors[] = "Message too short";
    return $errors;
  }

  private function saveToDatabase($data)
  {
    $stmt = $this->conn->prepare("INSERT INTO contacts (user_id, name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
    $user_id = $_SESSION['user_id'] ?? null;
    $stmt->bind_param("isssss", $user_id, $data['name'], $data['email'], $data['phone'], $data['subject'], $data['message']);
    return $stmt->execute();
  }

  private function sendMailNotification($data)
  {
    $mailSub = "New contact form: " . $data['subject'];
    $mailBody = "
            <h3>📬 New Contact Form Submitted</h3>
            <p><strong>Name:</strong> {$data['name']}</p>
            <p><strong>Email:</strong> {$data['email']}</p>
            <p><strong>Phone:</strong> {$data['phone']}</p>
            <p><strong>Subject:</strong> {$data['subject']}</p>
            <p><strong>Message:</strong><br>" . htmlspecialchars($data['message']) . "</p>
        ";
    return sendMail($this->adminEmail, $mailSub, $mailBody);
  }

  public function handleFormSubmission($formData)
  {
    $errors = $this->validate($formData);
    if (!empty($errors)) return ['errors' => $errors, 'success' => ''];

    if ($this->saveToDatabase($formData)) {
      $sent = $this->sendMailNotification($formData);
      return $sent
        ? ['errors' => [], 'success' => "✅ Thank you — your message has been sent successfully."]
        : ['errors' => ["⚠️ Message saved but email failed to send."], 'success' => ''];
    } else {
      return ['errors' => ["❌ Database Error: " . $this->conn->error], 'success' => ''];
    }
  }
}

$contactHandler = new ContactHandler($conn, $adminEmail);
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
  $formData = [
    'name' => trim($_POST['name'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'phone' => trim($_POST['phone'] ?? ''),
    'subject' => trim($_POST['subject'] ?? ''),
    'message' => trim($_POST['message'] ?? '')
  ];
  $result = $contactHandler->handleFormSubmission($formData);
  $errors = $result['errors'];
  $success = $result['success'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechZone - Electronics Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>

<body>

  <!-- 🔝 NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">TechZone</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#products">Products</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
          <li class="nav-item me-3">
            <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#cartPanel">
              <i class="fa-solid fa-cart-shopping"></i>
            </button>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" data-bs-toggle="dropdown">
              <i class="fa-solid fa-user"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="../../logout.php">Logout</a></li>
              <li><a class="dropdown-item" href="../dashboard.php">Dashboard</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- 🏠 HERO -->
  <section class="hero">
    <div class="hero-content">
      <h1 class="display-4 fw-bold">Welcome to <span class="text-primary">TechZone</span></h1>
      <p class="lead mb-4">Your one-stop shop for the latest electronics and gadgets</p>
      <a href="#products" class="btn btn-primary btn-lg">Shop Now</a>
    </div>
  </section>

  <!-- 🛒 PRODUCTS -->
  <section id="products" class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center mb-4 fw-bold text-uppercase">Available Products</h2>
      <div class="row g-4">
        <?php
        $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
        if ($result && $result->num_rows > 0):
          while ($p = $result->fetch_assoc()):
        ?>
            <div class="col-md-3">
              <div class="card product-card h-100 text-center">
                <img src="../../uploads/<?= htmlspecialchars($p['image'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($p['product_name']) ?>" class="card-img-top">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($p['product_name']) ?></h5>
                  <p class="text-muted">₹<?= number_format($p['price'], 2) ?></p>
                  <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-dark btn-sm">Add to Cart</button>
                    <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#productModal<?= $p['id'] ?>">View Details</Details></button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="productModal<?= $p['id'] ?>" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><?= htmlspecialchars($p['product_name']) ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body text-center">
                    <img src="../../uploads/<?= htmlspecialchars($p['image'] ?? 'default.jpg') ?>" class="img-fluid rounded mb-3" alt="<?= htmlspecialchars($p['product_name']) ?>">
                    <h5 class="text-primary mb-2">₹<?= number_format($p['price'], 2) ?></h5>
                    <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-dark">Add to Cart</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile;
        else: ?>
          <p class="text-center text-muted">No products available right now.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- 📞 CONTACT -->
  <section id="contact" class="py-5">
    <div class="container">
      <h2 class="text-center mb-4 fw-bold text-uppercase">Contact Us</h2>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <?php if ($errors) foreach ($errors as $e) echo "<div class='alert alert-danger'>$e</div>"; ?>
          <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
          <form method="POST">
            <input name="name" class="form-control mb-2" placeholder="Your name" required>
            <input name="email" type="email" class="form-control mb-2" placeholder="Your email" required>
            <input name="phone" class="form-control mb-2" placeholder="Phone (optional)">
            <input name="subject" class="form-control mb-2" placeholder="Subject" required>
            <textarea name="message" class="form-control mb-2" rows="4" placeholder="Your message..." required></textarea>
            <button type="submit" name="submit_contact" class="btn btn-dark w-100">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- ⚙️ FOOTER -->
  <footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; <?= date('Y'); ?> TechZone Electronics | All Rights Reserved</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>