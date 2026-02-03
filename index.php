<?php
// Login check removed for guest access
?>

<?php include "includes/header.php"; ?>

<!-- HERO SECTION -->
<section class="hero">
  <div class="hero-content">
    <h1>👶 குட்டி குட்டி கதைகள்</h1>
    <p>மகிழ்ச்சி • நீதி • கற்பனை</p>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'){ ?>
        <a href="admin/dashboard.php" class="btn">Admin Dashboard</a>
    <?php } else { ?>
        <a href="stories.php" class="btn">கதைகளை வாசிக்க</a>
    <?php } ?>

  </div>

  <div class="hero-image">
    <img src="assets/images/stories.jpg" alt="சிறுவர் கதைகள்" />
  </div>

  <div class="floating-elements">
    <span class="element star-1">⭐</span>
    <span class="element moon">🌙</span>
    <span class="element star-2">✨</span>
    <span class="element star-3">🌟</span>
  </div>
</section>

<?php include "includes/footer.php"; ?>
