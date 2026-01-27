<?php include "config.php"; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?= SITE_NAME ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/script.js" defer></script>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">
</head>
<body>

<header class="navbar">
  <h1>🌟 Tamil Kids Stories</h1>

  <!-- Hamburger button for mobile -->
  <button class="navbar-toggler">
    <span></span>
    <span></span>
    <span></span>
  </button>

  <!-- Nav links wrapped for toggle -->
  <nav class="nav-links">
    <a href="index.php">Home</a>
    <a href="stories.php">Stories</a>
    <a href="story.php">Story</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
  </nav>
</header>
