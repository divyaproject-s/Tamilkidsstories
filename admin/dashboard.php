<?php
session_start();
include "../includes/db.php";

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ===== PENDING USERS COUNT ===== */
$pendingResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users WHERE role='user' AND status='pending'"
);
$pendingUsers = mysqli_fetch_assoc($pendingResult)['total'];

/* ===== TOTAL USERS & ADMINS ===== */
$totalUsersResult  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='user'");
$totalAdminsResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='admin'");

$totalUsers  = mysqli_fetch_assoc($totalUsersResult)['total'];
$totalAdmins = mysqli_fetch_assoc($totalAdminsResult)['total'];

/* ===== FETCH ALL LIKED STORIES ===== */
$likedStoriesQuery = "
    SELECT s.id, s.title, COUNT(f.id) AS fav_count
    FROM favorites f
    INNER JOIN stories s ON f.story_id = s.id
    GROUP BY s.id, s.title
    ORDER BY fav_count DESC
";
$likedStoriesResult = mysqli_query($conn, $likedStoriesQuery);

// Get the top story for the card without advancing the pointer permanently
$favoriteStory = mysqli_fetch_assoc($likedStoriesResult);
if ($favoriteStory) {
    mysqli_data_seek($likedStoriesResult, 0); // Reset for the table later
}

/* ===== TOTAL FAVORITES COUNT ===== */
$totalFavResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM favorites");
$totalFavs = mysqli_fetch_assoc($totalFavResult)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f4f6fb;display:flex;min-height:100vh;}

/* ===== SIDEBAR ===== */
.sidebar{
    width:250px;
    background:linear-gradient(180deg,#667eea,#764ba2);
    color:#fff;
    padding:25px 20px;
}
.sidebar h2{text-align:center;margin-bottom:40px;}
.sidebar a{
    display:block;
    color:#fff;
    text-decoration:none;
    padding:14px 15px;
    margin-bottom:12px;
    border-radius:10px;
}
.sidebar a:hover{background:rgba(255,255,255,0.2);}

/* ===== MAIN ===== */
.main{flex:1;padding:30px;}
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}
.logout-btn{
    background:#ff5c5c;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;
}

/* ===== CARDS ===== */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:25px;
}
.card-link{text-decoration:none;color:inherit;}
.card{
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    transition:.3s;
}
.card:hover{transform:translateY(-6px);}
.card i{font-size:32px;color:#667eea;margin-bottom:15px;}
.card h3{margin-bottom:8px;color:#333;}
.card p{color:#555;font-size:14px;}

/* ===== BADGE ===== */
.badge{
    display:inline-block;
    background:#ff5c5c;
    color:#fff;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
    margin-left:6px;
}


/* ===== LIKED STORIES TABLE ===== */
.liked-stories-section{
    margin-top:40px;
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}
.liked-stories-section h2{
    margin-bottom:20px;
    color:#333;
    font-size:20px;
    border-bottom:2px solid #f4f6fb;
    padding-bottom:10px;
}
table{
    width:100%;
    border-collapse:collapse;
}
th, td{
    padding:14px;
    text-align:left;
    border-bottom:1px solid #f4f6fb;
}
th{color:#667eea;font-weight:600;}
.fav-pill{
    background:#fff0f0;
    color:#ff5c5c;
    padding:4px 12px;
    border-radius:20px;
    font-weight:600;
}

/* ===== RESPONSIVE ===== */
@media(max-width:768px){
    .sidebar{display:none;}
}
</style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <h2>Admin Panel</h2>

    <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="add-story.php"><i class="fa fa-plus"></i> Add Story</a>
    <a href="edit-story.php"><i class="fa fa-book"></i> Manage Stories</a>
    <a href="add-category.php"><i class="fa fa-tags"></i> Add Category</a>

    <a href="approve-users.php">
        <i class="fa fa-user-check"></i> Approve Users
        <?php if ($pendingUsers > 0): ?>
            <span class="badge"><?= $pendingUsers ?></span>
        <?php endif; ?>
    </a>

    <a href="manage-users.php">
        <i class="fa fa-users"></i> User / Admin List
        <span class="badge"><?= $totalUsers + $totalAdmins ?></span>
    </a>
    <a href="favorites.php"><i class="fa fa-heart"></i> Favorite Stories</a>
    <a href="messages.php"><i class="fa fa-envelope"></i> User Messages</a>
    <a href="../logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="main">

    <div class="header">
        <h1>Welcome Admin 👋</h1>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="cards">

        <a href="add-story.php" class="card-link">
            <div class="card">
                <i class="fa fa-plus-circle"></i>
                <h3>Add Story</h3>
                <p>Create and publish new kids stories</p>
            </div>
        </a>

        <a href="edit-story.php" class="card-link">
            <div class="card">
                <i class="fa fa-book-open"></i>
                <h3>Manage Stories</h3>
                <p>Edit or delete existing stories</p>
            </div>
        </a>

        <a href="add-category.php" class="card-link">
            <div class="card">
                <i class="fa fa-tags"></i>
                <h3>Add Category</h3>
                <p>Create story categories with image</p>
            </div>
        </a>

        <a href="approve-users.php" class="card-link">
            <div class="card">
                <i class="fa fa-user-check"></i>
                <h3>Approve Users</h3>
                <p><?= $pendingUsers ?> users waiting for approval</p>
            </div>
        </a>

        <a href="manage-users.php" class="card-link">
            <div class="card">
                <i class="fa fa-users"></i>
                <h3>User / Admin List</h3>
                <p>Total Users: <?= $totalUsers ?> | Admins: <?= $totalAdmins ?></p>
            </div>
        </a>

        <!-- ===== FAVORITE STORIES CARD ===== -->
        <a href="favorites.php" class="card-link">
            <div class="card">
                <i class="fa fa-heart" ></i>
                <h3>Story Favorites</h3>
                <p>Total Favorites: <?= $totalFavs ?></p>
                <?php if ($favoriteStory): ?>
                    <p style="font-size: 12px; margin-top: 5px; color: #888;">Top: <?= htmlspecialchars($favoriteStory['title']) ?> (<?= $favoriteStory['fav_count'] ?>)</p>
                <?php endif; ?>
            </div>
        </a>

        <a href="../index.php" class="card-link">
            <div class="card">
                <i class="fa fa-eye"></i>
                <h3>View User Page</h3>
                <p>Check how stories appear to users</p>
            </div>
        </a>

        <a href="messages.php" class="card-link">
            <div class="card">
                <i class="fa fa-envelope"></i>
                <h3>User Messages</h3>
                <p>View messages sent via Contact Form</p>
            </div>
        </a>

    </div>

    <!-- ===== LIKED STORIES LIST ===== -->
    <div class="liked-stories-section">
        <h2><i class="fa fa-heart" style="color:#ff5c5c;"></i> Most Liked Stories</h2>
        <?php if (mysqli_num_rows($likedStoriesResult) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Story Title</th>
                        <th>Favorite Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($likedStoriesResult)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><span class="fav-pill"><?= $row['fav_count'] ?> Favorites</span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color:#888;">No stories have been liked yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
