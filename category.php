<?php
include "includes/db.php";
include "includes/header.php";

// Get category ID safely
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch category name
$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$cat_result = $stmt->get_result();
$cat = $cat_result->fetch_assoc();
if (!$cat) { die("Invalid category"); }

// Fetch stories in this category
$stmt2 = $conn->prepare("SELECT id, title, content, image FROM stories WHERE category_id = ? ORDER BY id DESC");
$stmt2->bind_param("i", $category_id);
$stmt2->execute();
$stories = $stmt2->get_result();

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

<section class="story-list">
  <div class="stories-container">
    <h2>📖 <?= htmlspecialchars($cat['name']) ?></h2>

    <div class="stories-wrapper">
      <?php while($row = $stories->fetch_assoc()): ?>
        <div class="list-story-card">
          <img src="uploads/stories/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">

          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p><?= substr(strip_tags($row['content']),0,120) ?>...</p>

          <div class="story-actions" style="display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 10px;">
            <a href="story-details.php?id=<?= $row['id'] ?>" class="btn" style="margin: 0;">Read More</a>
            <button class="fav-btn <?= in_array($row['id'], $fav_ids) ? 'active' : '' ?>" data-id="<?= $row['id'] ?>" style="background: none; border: none; cursor: pointer; color: #ff6b6b; font-size: 1.4rem; transition: 0.3s; padding: 0; display: inline-flex; align-items: center;">
              <i class="<?= in_array($row['id'], $fav_ids) ? 'fa-solid fa-heart' : 'fa-regular fa-heart' ?>"></i>
            </button>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<script>
document.querySelectorAll('.fav-btn').forEach(btn => {
    btn.onclick = async () => {
        const storyId = btn.dataset.id;

        try {
            const response = await fetch("favorites.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "story_id=" + storyId
            });

            const data = await response.json();

            if (data.status === "added") {
                btn.classList.add("active");
                btn.querySelector('i').className = "fa-solid fa-heart";
                btn.querySelector('i').style.color = "#ff3b3b";
            } else if (data.status === "removed") {
                btn.classList.remove("active");
                btn.querySelector('i').className = "fa-regular fa-heart";
                btn.querySelector('i').style.color = "#ff6b6b";
            } else if (data.status === "error") {
                alert("Please login to add stories to favorites!");
                window.location.href = "login.php";
            }
        } catch (err) {
            console.error("AJAX Error:", err);
            alert("Error updating favorite. Please try again.");
        }
    }
});
</script>

<?php include "includes/footer.php"; ?>
