<?php

// Login check removed for guest access

include "includes/db.php";
include "includes/header.php";

/* ===== Pagination Setup ===== */
$limit = 6; // Stories per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

/* ===== Optional: Filter by category ===== */
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

/* ===== Count total stories ===== */
if ($category_id > 0) {
    $stmt_count = $conn->prepare("SELECT COUNT(*) AS total FROM stories WHERE category_id=?");
    $stmt_count->bind_param("i", $category_id);
} else {
    $stmt_count = $conn->prepare("SELECT COUNT(*) AS total FROM stories");
}
$stmt_count->execute();
$total_row = $stmt_count->get_result()->fetch_assoc();
$total_stories = $total_row['total'];
$total_pages = max(1, ceil($total_stories / $limit));

/* ===== Fetch stories for current page ===== */
if ($category_id > 0) {
    $stmt = $conn->prepare(
        "SELECT id, title, content, image 
         FROM stories 
         WHERE category_id=? 
         ORDER BY id DESC 
         LIMIT ?, ?"
    );
    $stmt->bind_param("iii", $category_id, $offset, $limit);
} else {
    $stmt = $conn->prepare(
        "SELECT id, title, content, image 
         FROM stories 
         ORDER BY id DESC 
         LIMIT ?, ?"
    );
    $stmt->bind_param("ii", $offset, $limit);
}
$stmt->execute();
$stories = $stmt->get_result();

/* ===== Fetch category name for header ===== */
$cat_name = "All Stories";
if ($category_id > 0) {
    $cat_stmt = $conn->prepare("SELECT name FROM categories WHERE id=?");
    $cat_stmt->bind_param("i", $category_id);
    $cat_stmt->execute();
    $cat_row = $cat_stmt->get_result()->fetch_assoc();
    if ($cat_row) {
        $cat_name = $cat_row['name'];
    }
}

/* ===== USER FAVORITES ===== */
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
  <div class="container">
    <h2>📖 Latest Stories </h2>

    <div class="stories-wrapper">
      <?php if ($stories && $stories->num_rows > 0): ?>
        <?php while ($row = $stories->fetch_assoc()): ?>
          <div class="story-card">
            <img
              src="uploads/stories/<?php echo htmlspecialchars($row['image']); ?>"
              alt="<?php echo htmlspecialchars($row['title']); ?>"
            >
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p>
              <?php
                echo htmlspecialchars(
                  mb_substr(strip_tags($row['content']), 0, 120)
                );
              ?>...
            </p>
            <div class="card-footer" style="display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 20px;">
              <a href="story-details.php?id=<?php echo (int)$row['id']; ?>" class="btn" style="margin: 0;">
                Read More
              </a>
              <button class="fav-btn <?php echo in_array($row['id'], $fav_ids) ? 'active' : ''; ?>" data-id="<?php echo $row['id']; ?>" style="background: none; border: none; cursor: pointer; color: #ff6b6b; font-size: 1.4rem; transition: 0.3s; padding: 0; display: inline-flex; align-items: center;">
                <i class="<?php echo in_array($row['id'], $fav_ids) ? 'fa-solid fa-heart' : 'fa-regular fa-heart'; ?>"></i>
              </button>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No stories found.</p>
      <?php endif; ?>
    </div>

    <!-- ===== PAGINATION ===== -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">

      <?php if ($page > 1): ?>
        <a href="?page=1<?php echo $category_id ? "&category=$category_id" : ""; ?>">First</a>
        <a href="?page=<?php echo $page - 1; ?><?php echo $category_id ? "&category=$category_id" : ""; ?>">
          &laquo; Prev
        </a>
      <?php endif; ?>

      <?php
      $start = max(1, $page - 2);
      $end   = min($total_pages, $page + 2);
      for ($i = $start; $i <= $end; $i++):
      ?>
        <a
          href="?page=<?php echo $i; ?><?php echo $category_id ? "&category=$category_id" : ""; ?>"
          class="<?php echo $i == $page ? 'active' : ''; ?>"
        >
          <?php echo $i; ?>
        </a>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?><?php echo $category_id ? "&category=$category_id" : ""; ?>">
          Next &raquo;
        </a>
        <a href="?page=<?php echo $total_pages; ?><?php echo $category_id ? "&category=$category_id" : ""; ?>">
          Last
        </a>
      <?php endif; ?>

    </div>
    <?php endif; ?>

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
                if(data.message === "Not logged in") {
                    alert("Please login to add stories to favorites!");
                } else {
                    alert("Error: " + data.message);
                }
            }
        } catch (err) {
            console.error("AJAX Error:", err);
            alert("Error updating favorite. Please try again.");
        }
    }
});
</script>

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
  transition:transform .3s;
}
.story-card:hover{
  transform:translateY(-5px);
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
  font-size:.95rem;
}
.btn{
  display:inline-block;
  margin:15px 0;
  padding:8px 20px;
  background:#ff6b6b;
  color:#fff;
  border-radius:20px;
  text-decoration:none;
}
.btn:hover{
  background:#ff4b4b;
}
.fav-btn:hover {
  transform: scale(1.2);
}
.fav-btn.active i {
  color: #ff3b3b;
}

/* PAGINATION */
.pagination{
  display:flex;
  flex-wrap:wrap;
  justify-content:center;
  margin-top:30px;
  gap:5px;
}
.pagination a{
  padding:8px 12px;
  background:#f1f1f1;
  color:#333;
  text-decoration:none;
  border-radius:5px;
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
    font-size:.9rem;
    min-width:30px;
  }
}
</style>

<?php include "includes/footer.php"; ?>
