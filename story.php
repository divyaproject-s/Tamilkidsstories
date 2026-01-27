<?php
include "includes/db.php";
include "includes/header.php";

// ===== Fetch latest 6 stories =====
$limit = 6;

// Optional: Filter by category
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

if($category_id > 0){
    $stmt = $conn->prepare("SELECT id, title, content, image FROM stories WHERE category_id=? ORDER BY id DESC LIMIT ?");
    $stmt->bind_param("ii", $category_id, $limit);
} else {
    $stmt = $conn->prepare("SELECT id, title, content, image FROM stories ORDER BY id DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
}
$stmt->execute();
$stories = $stmt->get_result();

// Optional: Fetch category name for header
$cat_name = "All Stories";
if($category_id > 0){
    $cat_stmt = $conn->prepare("SELECT name FROM categories WHERE id=?");
    $cat_stmt->bind_param("i", $category_id);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();
    $cat_row = $cat_result->fetch_assoc();
    if($cat_row) $cat_name = $cat_row['name'];
}
?>

<section class="story-list">
  <div class="container">
    <h2>📖 <?= htmlspecialchars($cat_name) ?></h2>

    <div class="stories-wrapper">
      <?php while($row = $stories->fetch_assoc()): ?>
        <div class="story-card">
          <img src="uploads/stories/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p><?= substr(strip_tags($row['content']), 0, 120) ?>...</p>
          <a href="story-details.php?id=<?= $row['id'] ?>" class="btn">Read More</a>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<style>
.container{
  max-width: 1100px;
  margin: auto;
  padding: 20px;
}

.stories-wrapper{
  display: grid;
  grid-template-columns: repeat(1, 1fr); /* 1 column on small screens */
  gap: 25px;
}

@media(min-width: 768px){
  .stories-wrapper{
    grid-template-columns: repeat(2, 1fr); /* 2 columns on tablets */
  }
}

@media(min-width: 1024px){
  .stories-wrapper{
    grid-template-columns: repeat(3, 1fr); /* 3 columns on desktop */
  }
}

.story-card{
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 10px 20px rgba(0,0,0,.1);
  overflow: hidden;
  text-align: center;
  transition: transform 0.3s ease;
}

.story-card:hover{
  transform: translateY(-5px);
}

.story-card img{
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
}

.story-card h3{
  margin: 10px 0;
  font-size: 1.1rem;
  color: #ff6b6b;
}

.story-card p{
  padding: 0 10px 10px;
  font-size: 0.95rem;
  color: #555;
}

.btn{
  display: inline-block;
  margin: 10px 0 15px;
  padding: 8px 20px;
  background: #ff6b6b;
  color: #fff;
  border-radius: 20px;
  text-decoration: none;
  transition: background 0.3s ease;
}

.btn:hover{
  background: #ff4b4b;
}
</style>

<?php include "includes/footer.php"; ?>
