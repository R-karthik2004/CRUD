<?php
require_once  'dbcon.php';

// If not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Role-based restriction logic
$currentDir = basename(dirname($_SERVER['PHP_SELF'])); // e.g., 'admin' or 'client'

// ✅ If admin folder — allow only admin
if ($currentDir === 'admin' && $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// ✅ If client folder — allow only client
if ($currentDir === 'client' && $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit();
}
?>
