<?php
include "includes/db.php";
include "includes/header.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$favStoriesResult = null;
if ($user_id > 0) {
    /* ===== FETCH FAVORITE STORIES OF THIS USER ===== */
    $stmt = $conn->prepare("
        SELECT s.id, s.title, s.image, s.category_id
        FROM favorites f
        JOIN stories s ON f.story_id = s.id
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $favStoriesResult = $stmt->get_result();
}
?>

<h1 style="margin-top:20px;">❤️ My Favorite Stories</h1>

<?php if($user_id == 0): ?>
    <div style="text-align:center; padding:50px;">
        <h2>🔒 Login Required</h2>
        <p>Please <a href="login.php" style="color:#ff9800; font-weight:bold;">Login</a> to view your favorite stories.</p>
    </div>
<?php elseif($favStoriesResult && $favStoriesResult->num_rows > 0): ?>
    <div class="fav-grid">
        <?php while($story = $favStoriesResult->fetch_assoc()): ?>
        <div class="fav-card">
            <?php if($story['image']): ?>
            <img src="uploads/stories/<?= htmlspecialchars($story['image']) ?>" alt="<?= htmlspecialchars($story['title']) ?>">
            <?php else: ?>
            <i class="fa fa-book" style="font-size:48px;color:#ff9800;margin-top:20px;"></i>
            <?php endif; ?>
            <h3><?= htmlspecialchars($story['title']) ?></h3>
            <a href="story-details.php?id=<?= $story['id'] ?>">Read Story</a>
        </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p style="text-align:center;">You haven't liked any stories yet 😢</p>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
