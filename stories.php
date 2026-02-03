<?php

// Login check removed for guest access

include "includes/db.php";
include "includes/header.php";

/* ===== FETCH ALL CATEGORIES ===== */
$cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<style>
/* ===== CATEGORY GRID ===== */
.category-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
  gap:20px;
  margin-bottom:35px;
}

.cat-card{
  background:#fff;
  border-radius:20px;
  padding:20px 10px;
  text-align:center;
  text-decoration:none;
  color:#333;
  box-shadow:0 10px 25px rgba(0,0,0,0.1);
  transition:.3s;
}

.cat-card img{
  width:120px;
  height:120px;
  border-radius:50%;
  object-fit:cover;
  margin-bottom:15px;
  box-shadow:0 6px 15px rgba(0,0,0,0.2);
}

.cat-card h4{
  font-size:15px;
  font-weight:600;
}

.cat-card:hover{
  background:linear-gradient(135deg,#6a7de8,#7f3fd4);
  color:#fff;
  transform:translateY(-6px);
}
</style>

<section class="stories-section">
  <h2>📖 தமிழ் சிறுவர் கதைகள்</h2>

  <div class="category-grid">
    <?php if($cat_result && $cat_result->num_rows > 0): ?>
        <?php while($cat = $cat_result->fetch_assoc()): ?>
            <a href="category.php?id=<?php echo (int)$cat['id']; ?>" class="cat-card">
                <img 
                  src="uploads/categories/<?php echo htmlspecialchars($cat['image']); ?>" 
                  alt="<?php echo htmlspecialchars($cat['name']); ?>"
                >
                <h4><?php echo htmlspecialchars($cat['name']); ?></h4>
            </a>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No categories found.</p>
    <?php endif; ?>
  </div>
</section>

<?php include "includes/footer.php"; ?>
