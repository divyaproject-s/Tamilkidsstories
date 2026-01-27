 <?php
session_start();

// Set timeout duration (in seconds)
$timeout_duration = 600; // 10 minutes

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Check for timeout
if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session expired
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
