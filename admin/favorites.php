<?php
session_start();
include "../includes/db.php";

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ===== FETCH ALL STORIES WITH FAVORITE COUNTS ===== */
$sql = "
    SELECT s.id, s.title, s.image, COUNT(f.id) AS fav_count
    FROM stories s
    LEFT JOIN favorites f ON s.id = f.story_id
    GROUP BY s.id, s.title, s.image
    ORDER BY fav_count DESC, s.title ASC
";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Favorite Stories</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', sans-serif;
}

body{
    background:#f4f6fb;
}

/* ===== NAVBAR ===== */
.navbar{
    background:#4f46e5;
    color:#fff;
    padding:16px 30px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.navbar h2{
    margin:0;
    font-size:22px;
    font-weight:600;
}

.dashboard-btn{
    background:#6366f1;
    color:#fff;
    text-decoration:none;
    padding:10px 16px;
    border-radius:10px;
    font-size:14px;
    transition:0.3s;
}

.dashboard-btn:hover{
    background:#818cf8;
}

/* ===== PAGE CONTAINER ===== */
.container{
    padding:40px;
}

/* ===== GRID ===== */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:25px;
}

/* ===== CARD ===== */
.card{
    background:#fff;
    border-radius:16px;
    box-shadow:0 12px 25px rgba(0,0,0,.08);
    overflow:hidden;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-6px);
    box-shadow:0 18px 35px rgba(0,0,0,.12);
}

.card img{
    width:100%;
    height:180px;
    object-fit:cover;
    background:#eee;
}

.card-body{
    padding:18px;
}

.card-body h3{
    font-size:16px;
    margin-bottom:8px;
    color:#333;
}

/* ===== FAVORITE COUNT ===== */
.fav-count{
    color:#f59e0b;
    font-weight:600;
    font-size:14px;
}

/* ===== EMPTY ===== */
.empty{
    text-align:center;
    color:#6b7280;
    font-size:18px;
    margin-top:80px;
}
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <h2>⭐ Favorite Stories</h2>
    <a href="dashboard.php" class="dashboard-btn">⬅ Dashboard</a>
</div>

<div class="container">

    <div class="grid">

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($story = mysqli_fetch_assoc($result)): ?>

            <?php
            /* ===== IMAGE PATH FIX ===== */
            $imagePath = "../uploads/stories/" . $story['image'];
            if (!empty($story['image']) && file_exists($imagePath)) {
                $image = $imagePath;
            } else {
                $image = "../assets/no-image.png";
            }
            ?>

            <div class="card">
                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($story['title']) ?>">
                <div class="card-body">
                    <h3><?= htmlspecialchars($story['title']) ?></h3>
                    <div class="fav-count">
                        <i class="fa fa-heart"></i>
                        <?= $story['fav_count'] ?> Favorites
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty">No favorite stories found ⭐</div>
    <?php endif; ?>

    </div>

</div>

</body>
</html>
