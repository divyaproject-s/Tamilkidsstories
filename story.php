<?php
include "includes/db.php";
include "includes/header.php";

// ===== Pagination Setup =====
$limit = 6; // Stories per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Optional: Filter by category
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// ===== Count total stories =====
if($category_id > 0){
    $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM stories WHERE category_id=?");
    $stmt_count->bind_param("i", $category_id);
} else {
    $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM stories");
}
$stmt_count->execute();
$total_result = $stmt_count->get_result();
$total_row = $total_result->fetch_assoc();
$total_stories = $total_row['total'];
$total_pages = ceil($total_stories / $limit);

// ===== Fetch stories for current page =====
if($category_id > 0){
    $stmt = $conn->prepare("SELECT id, title, content, image FROM stories WHERE category_id=? ORDER BY id DESC LIMIT ?, ?");
    $stmt->bind_param("iii", $category_id, $offset, $limit);
} else {
    $stmt = $conn->prepare("SELECT id, title, content, image FROM stories ORDER BY id DESC LIMIT ?, ?");
    $stmt->bind_param("ii", $offset, $limit);
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
          <p><?= substr(strip_tags($row['content']),0,120) ?>...</p>
          <a href="story-details.php?id=<?= $row['id'] ?>" class="btn">Read More</a>
        </div>
      <?php endwhile; ?>
    </div>

    <!-- Enhanced Pagination -->
    <div class="pagination">
      <?php if($page > 1): ?>
        <a href="?page=1<?= $category_id ? "&category=$category_id" : "" ?>">First</a>
        <a href="?page=<?= $page-1 ?><?= $category_id ? "&category=$category_id" : "" ?>">&laquo; Prev</a>
      <?php endif; ?>

      <?php
      // Show pages around current page (max 5 pages)
      $start = max(1, $page - 2);
      $end = min($total_pages, $page + 2);
      for($i=$start; $i<=$end; $i++):
      ?>
        <a href="?page=<?= $i ?><?= $category_id ? "&category=$category_id" : "" ?>" class="<?= $i==$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>

      <?php if($page < $total_pages): ?>
        <a href="?page=<?= $page+1 ?><?= $category_id ? "&category=$category_id" : "" ?>">Next &raquo;</a>
        <a href="?page=<?= $total_pages ?><?= $category_id ? "&category=$category_id" : "" ?>">Last</a>
      <?php endif; ?>
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
@media(min-width:768px){
  .stories-wrapper{ grid-template-columns:1fr 1fr; }
}
@media(min-width:1024px){
  .stories-wrapper{ grid-template-columns:1fr 1fr 1fr; }
}
.story-card{
  background:#fff;
  border-radius:20px;
  box-shadow:0 10px 20px rgba(0,0,0,.1);
  overflow:hidden;
  text-align:center;
  transition: transform 0.3s ease;
}
.story-card:hover{
  transform: translateY(-5px);
}
.story-card img{
  width:100%;
  height:200px;
  object-fit:cover;
}
.story-card h3{
  margin:10px 0;
}
.story-card p{
  padding:0 10px;
  font-size:0.95rem;
}
.btn{
  display:inline-block;
  margin:15px 0;
  padding:8px 20px;
  background:#ff6b6b;
  color:#fff;
  border-radius:20px;
  text-decoration:none;
  transition: background 0.3s ease;
}
.btn:hover{
  background:#ff4b4b;
}

/* Enhanced Pagination Styles */
.pagination{
  display:flex;
  flex-wrap:wrap;
  justify-content:center;
  margin-top:30px;
  gap:5px;
}
.pagination a{
  display:inline-block;
  padding:8px 12px;
  background:#f1f1f1;
  color:#333;
  text-decoration:none;
  border-radius:5px;
  transition: background 0.3s;
  min-width:40px;
  text-align:center;
}
.pagination a.active{
  background:#ff6b6b;
  color:#fff;
}
.pagination a:hover:not(.active){
  background:#ddd;
}
@media(max-width:480px){
  .pagination a{
    padding:6px 8px;
    font-size:0.9rem;
    min-width:30px;
  }
}
</style>

<?php include "includes/footer.php"; ?>
