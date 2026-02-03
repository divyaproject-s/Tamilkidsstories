<?php
include "includes/db.php";
include "includes/header.php";

$story_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$story_id) die("Story ID missing");

/* FETCH STORY */
$stmt = $conn->prepare("SELECT * FROM stories WHERE id=?");
$stmt->bind_param("i", $story_id);
$stmt->execute();
$story = $stmt->get_result()->fetch_assoc();
if (!$story) die("Story not found");

/* CATEGORY */
$stmt2 = $conn->prepare("SELECT name FROM categories WHERE id=?");
$stmt2->bind_param("i", $story['category_id']);
$stmt2->execute();
$cat = $stmt2->get_result()->fetch_assoc();
$cat_name = $cat ? $cat['name'] : "Category";

/* ===== USER FAVORITES (Safe for Guest) ===== */
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$fav_ids = [];

if ($user_id > 0) {
    $res = $conn->prepare("SELECT story_id FROM favorites WHERE user_id=?");
    $res->bind_param("i", $user_id);
    $res->execute();
    $result = $res->get_result();
    while ($r = $result->fetch_assoc()) $fav_ids[] = $r['story_id'];
}
?>

<!-- ================= HERO ================= -->
<section class="story-hero" style="background-image:url('uploads/stories/<?= htmlspecialchars($story['image']) ?>')">
  <div class="animal left-animal">🐰</div>
  <div class="animal right-animal">🦁</div>
  
  <div class="hero-overlay">
    <a href="category.php?id=<?= $story['category_id'] ?>" class="back-link">
      ← Back to <?= htmlspecialchars($cat_name) ?>
    </a>

    <button class="fav-float <?= in_array($story['id'], $fav_ids) ? 'active' : '' ?>" data-id="<?= $story['id'] ?>">
      <i class="<?= in_array($story['id'], $fav_ids) ? 'fas fa-heart' : 'far fa-heart' ?>"></i>
    </button>

    <h1><?= htmlspecialchars($story['title']) ?></h1>
  </div>
</section>

<!-- ================= CONTENT ================= -->
<section class="story-body">
  <div class="container">

    <?php if ($user_id > 0): ?>
      <?php if(!empty($story['video_link'])): ?>
      <div class="card video-card">
        <?php
        $video = $story['video_link'];
        if(strpos($video, 'youtu') !== false){
          preg_match('/(youtu\.be\/|v=)([^&]+)/', $video, $m);
          $vid = $m[2] ?? '';
        ?>
          <iframe src="https://www.youtube.com/embed/<?= $vid ?>" allowfullscreen></iframe>
        <?php } elseif(strpos($video, 'drive.google.com') !== false) { 
          preg_match('/[-\w]{25,}/', $video, $m);
          $drive_id = $m[0] ?? '';
        ?>
          <iframe src="https://drive.google.com/file/d/<?= $drive_id ?>/preview" allowfullscreen></iframe>
        <?php } else { ?>
          <video controls>
            <source src="<?= htmlspecialchars($video) ?>" type="video/mp4">
          </video>
        <?php } ?>
      </div>
      <?php endif; ?>

      <div class="card content-card">
          <?= nl2br(htmlspecialchars($story['content'])) ?>
        </div>
    <?php else: ?>
      <!-- Login Required for Guests -->
      <div class="card content-card" style="text-align: center; padding: 80px 40px;">
        <i class="fas fa-lock" style="font-size: 4rem; color: #ff6f61; margin-bottom: 20px;"></i>
        <h2 style="color: #333; margin-bottom: 15px;">Read Full Story</h2>
        <p style="font-size: 1.2rem; color: #666; margin-bottom: 30px;">
          Please login to enjoy this child-friendly story to the end!
        </p>
        <a href="login.php" class="btn" style="background: #ff6f61; padding: 15px 40px; font-size: 1.1rem;">
          Login to Continue
        </a>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- ================= AJAX ================= -->
<script>
document.querySelectorAll('.fav-float').forEach(btn => {
    btn.onclick = async () => {
        const storyId = btn.dataset.id;
        const icon = btn.querySelector('i');

        try {
            const response = await fetch("favorites.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "story_id=" + storyId
            });

            const data = await response.json();

            if (data.status === "added") {
                btn.classList.add("active");
                if(icon) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                }
            } else if (data.status === "removed") {
                btn.classList.remove("active");
                if(icon) {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                }
            } else if (data.status === "error") {
                alert("Please login to favorite stories!");
                window.location.href = "login.php";
            }
        } catch (err) {
            console.error("AJAX Error:", err);
        }
    }
});
</script>

<?php include "includes/footer.php"; ?>