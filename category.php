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
?>

<section class="story-list">
  <div class="container">
    <h2>📖 <?= htmlspecialchars($cat['name']) ?></h2>

    <div class="stories-wrapper">
      <?php while($row = $stories->fetch_assoc()): ?>
        <div class="story-card">
          <img src="uploads/stories/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">

          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p><?= substr(strip_tags($row['content']),0,120) ?>...</p>

          <div class="story-actions">
            <a href="story-details.php?id=<?= $row['id'] ?>" class="btn">Read More</a>
            <!-- Heart removed -->
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<style>
.container{
  max-width:1100px;
  margin:auto;
  padding:20px;
}
.stories-wrapper{
  display:grid;
  grid-template-columns:1fr;
  gap:25px;
}
@media(min-width:768px){ .stories-wrapper{ grid-template-columns:1fr 1fr; } }
@media(min-width:1024px){ .stories-wrapper{ grid-template-columns:1fr 1fr 1fr; } }

.story-card{
  background:#fff;
  border-radius:20px;
  box-shadow:0 10px 20px rgba(0,0,0,.1);
  overflow:hidden;
  text-align:center;
  transition: transform 0.3s ease;
}
.story-card:hover{ transform: translateY(-5px); }
.story-card img{ width:100%; height:200px; object-fit:cover; }
.story-card h3{ margin:10px 0; }
.story-card p{ padding:0 10px; font-size:0.95rem; }

.story-actions{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:10px;
  margin-top:10px;
}
.btn{
  display:inline-block;
  padding:8px 20px;
  background:#ff6b6b;
  color:#fff;
  border-radius:20px;
  text-decoration:none;
  transition: background 0.3s ease;
}
.btn:hover{ background:#ff4b4b; }
</style>

<?php include "includes/footer.php"; ?>
