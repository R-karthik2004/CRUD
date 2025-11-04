<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_unset();
session_destroy();

// Prevent browser cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Redirect to login
header("Location: index.php");
exit;
?>
