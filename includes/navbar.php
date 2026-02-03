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
    <a href="story.php">Latest Story</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
    <a href="favorites-list.php">Favorites</a>
    
    <?php
    $navName = "Guest";
    if (isset($_SESSION['name'])) {
        $navName = $_SESSION['name'];
    } elseif (isset($_SESSION['user_id'])) {
        // Fallback: Fetch name if not in session
        include_once "includes/db.php";
        $stmtName = $conn->prepare("SELECT name FROM users WHERE id=?");
        $stmtName->bind_param("i", $_SESSION['user_id']);
        $stmtName->execute();
        $resName = $stmtName->get_result();
        if ($u = $resName->fetch_assoc()) {
            $navName = $u['name'];
            $_SESSION['name'] = $navName;
        }
    }
    ?>
    <span class="nav-separator"></span>
    <span style="color:white; font-weight:bold; margin-left:5px; margin-right:10px;">
      Welcome, <?= htmlspecialchars($navName) ?>
    </span>
    
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="logout.php" class="btn-logout" style="background:#ff3b3b; padding:8px 15px; border-radius:20px; font-size:0.9rem;">Logout</a>
    <?php else: ?>
      <a href="login.php" class="btn-logout" style="background:#4CAF50; padding:8px 15px; border-radius:20px; font-size:0.9rem;">Login</a>
    <?php endif; ?>
  </nav>
</header>
