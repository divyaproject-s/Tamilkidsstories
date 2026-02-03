<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";
?>
<!DOCTYPE html>
<html>
<head>
  <title><?= SITE_NAME ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/script.js" defer></script>
  <!-- FontAwesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">
</head>
<body>

<?php if (!isset($hide_top_navbar) || !$hide_top_navbar): ?>
  <?php include "includes/navbar.php"; ?>
<?php endif; ?>
