<?php
session_start();
include "../includes/db.php";

/* ===== ADMIN CHECK ===== */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$success = "";
$error   = "";

/* ===== DELETE STORY ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare("SELECT image FROM stories WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $story = $stmt->get_result()->fetch_assoc();

    if ($story) {
        if ($story['image'] && file_exists("../uploads/stories/".$story['image'])) {
            unlink("../uploads/stories/".$story['image']);
        }
        $del = $conn->prepare("DELETE FROM stories WHERE id=?");
        $del->bind_param("i", $id);
        $del->execute();
        $success = "✅ Story deleted successfully!";
    }
}

/* ===== UPDATE STORY ===== */
if (isset($_POST['update_story'])) {

    $id          = (int)$_POST['story_id'];
    $title       = trim($_POST['title']);
    $content     = trim($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $video_link  = trim($_POST['video_link']);
    $image       = $_POST['old_image'];

    if ($title=="" || $content=="" || !$category_id) {
        $error = "All fields are required!";
    } else {

        /* IMAGE UPDATE */
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];

            if (!in_array($ext, $allowed)) {
                $error = "Invalid image format!";
            } else {
                if ($image && file_exists("../uploads/stories/".$image)) {
                    unlink("../uploads/stories/".$image);
                }
                $image = uniqid("story_", true).".".$ext;
                move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/stories/".$image);
            }
        }

        if (!$error) {
            $stmt = $conn->prepare(
                "UPDATE stories 
                 SET title=?, content=?, image=?, video_link=?, category_id=? 
                 WHERE id=?"
            );
            $stmt->bind_param(
                "ssssii",
                $title,
                $content,
                $image,
                $video_link,
                $category_id,
                $id
            );
            $stmt->execute();
            $success = "✅ Story updated successfully!";
        }
    }
}

/* ===== FETCH STORIES ===== */
$stories = $conn->query("
    SELECT s.*, c.name AS category_name
    FROM stories s
    LEFT JOIN categories c ON s.category_id = c.id
    ORDER BY s.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Stories</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{font-family:'Segoe UI',sans-serif;background:#f4f6fb;margin:0;}
.container{max-width:1200px;margin:25px auto;padding:0 20px;}

.header{
    background:#4f46e5;color:#fff;
    padding:16px 20px;border-radius:12px;
    display:flex;justify-content:space-between;align-items:center;
}
.header a{
    color:#fff;text-decoration:none;
    background:rgba(255,255,255,.2);
    padding:8px 14px;border-radius:8px;
}

.success{background:#e6fff0;color:#067647;padding:10px;border-radius:8px;margin:15px 0;}
.error{background:#ffe6e6;color:#b00000;padding:10px;border-radius:8px;margin:15px 0;}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:20px;
}
.card{
    background:#fff;padding:15px;
    border-radius:15px;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
}
.card img{
    width:100%;height:180px;
    object-fit:cover;border-radius:12px;
}
.card h3{margin:10px 0;}
.card p{font-size:14px;color:#555;height:50px;overflow:hidden;}

.category-tag{
    display:inline-block;
    background:#eef2ff;color:#4338ca;
    padding:4px 10px;border-radius:20px;
    font-size:12px;margin-bottom:6px;
}

.video-tag{
    display:inline-block;
    background:#dcfce7;color:#166534;
    padding:4px 10px;border-radius:20px;
    font-size:12px;margin-bottom:6px;
}

.actions{
    display:flex;justify-content:space-between;margin-top:10px;
}
.btn{
    padding:8px 14px;border:none;border-radius:8px;
    color:#fff;cursor:pointer;text-decoration:none;
}
.edit{background:#4f46e5;}
.delete{background:#ef4444;}

.modal{
    display:none;position:fixed;inset:0;
    background:rgba(0,0,0,.6);z-index:999;
}
.modal-box{
    background:#fff;max-width:600px;
    margin:60px auto;padding:25px;border-radius:15px;
}
.modal-box input,
.modal-box textarea,
.modal-box select{
    width:100%;padding:12px;
    margin-bottom:12px;border:1px solid #ccc;border-radius:8px;
}
.modal-box textarea{min-height:140px;}
.modal-box button{
    width:100%;background:#4f46e5;
    color:#fff;border:none;padding:12px;border-radius:30px;
}
.close{float:right;font-size:22px;cursor:pointer;}
</style>
</head>

<body>

<div class="container">

<div class="header">
    <h2>Manage Stories</h2>
    <a href="dashboard.php">← Dashboard</a>
</div>

<?php if($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
<?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

<div class="grid">
<?php while($s = $stories->fetch_assoc()): ?>
<div class="card">
    <img src="../uploads/stories/<?= htmlspecialchars($s['image']) ?>">

    <span class="category-tag"><?= htmlspecialchars($s['category_name']) ?></span>

    <?php if(!empty($s['video_link'])): ?>
        <span class="video-tag">🎬 Video</span>
    <?php endif; ?>

    <h3><?= htmlspecialchars($s['title']) ?></h3>
    <p><?= htmlspecialchars($s['content']) ?></p>

    <div class="actions">
        <button class="btn edit" onclick="openModal(<?= $s['id'] ?>)">Edit</button>
        <a class="btn delete" href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete this story?')">Delete</a>
    </div>
</div>

<!-- ===== EDIT MODAL ===== -->
<div class="modal" id="modal<?= $s['id'] ?>">
<div class="modal-box">
<span class="close" onclick="closeModal(<?= $s['id'] ?>)">&times;</span>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="story_id" value="<?= $s['id'] ?>">
<input type="hidden" name="old_image" value="<?= $s['image'] ?>">

<label>Title</label>
<input type="text" name="title" value="<?= htmlspecialchars($s['title']) ?>" required>

<label>Category</label>
<select name="category_id" required>
<?php
$cats = $conn->query("SELECT * FROM categories");
while($c = $cats->fetch_assoc()):
?>
<option value="<?= $c['id'] ?>" <?= $c['id']==$s['category_id']?'selected':'' ?>>
<?= htmlspecialchars($c['name']) ?>
</option>
<?php endwhile; ?>
</select>

<label>Content</label>
<textarea name="content" required><?= htmlspecialchars($s['content']) ?></textarea>

<label>Video Link (YouTube / MP4)</label>
<input type="url" name="video_link" value="<?= htmlspecialchars($s['video_link']) ?>">

<label>Change Image</label>
<input type="file" name="image">

<button name="update_story">Update Story</button>
</form>

</div>
</div>
<?php endwhile; ?>
</div>

</div>

<script>
function openModal(id){
  document.getElementById("modal"+id).style.display="block";
}
function closeModal(id){
  document.getElementById("modal"+id).style.display="none";
}
</script>

</body>
</html>
