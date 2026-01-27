<?php
session_start();
include "../includes/db.php";

/* ===== ADMIN CHECK ===== */
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$success = "";
$error   = "";

/* ===== PRESERVE VALUES ===== */
$title = $content = $video_link = "";
$category_id = "";

/* ===== FETCH CATEGORIES ===== */
$catResult = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");

/* ===== HANDLE FORM SUBMIT ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title']);
    $content     = trim($_POST['content']);
    $video_link  = trim($_POST['video_link']);
    $category_id = (int)$_POST['category_id'];

    if ($title === "" || $content === "" || !$category_id) {
        $error = "Please fill all required fields!";
    }
    elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        $error = "Please upload a valid image!";
    }
    else {

        $file = $_FILES['image'];
        $allowedExt  = ['jpg','jpeg','png','gif'];
        $allowedMime = ['image/jpeg','image/png','image/gif'];

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $error = "Only JPG, PNG, GIF images allowed!";
        }
        else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedMime)) {
                $error = "Invalid image type!";
            }
            else {

                $uploadDir = "../uploads/stories/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $imgName = uniqid("story_", true) . "." . $ext;

                if (move_uploaded_file($file['tmp_name'], $uploadDir.$imgName)) {

                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

                    /* ===== INSERT STORY (CORRECT) ===== */
                    $stmt = $conn->prepare(
                        "INSERT INTO stories 
                        (title, slug, content, image, video_link, category_id)
                        VALUES (?, ?, ?, ?, ?, ?)"
                    );

                    $stmt->bind_param(
                        "sssssi",
                        $title,
                        $slug,
                        $content,
                        $imgName,
                        $video_link,
                        $category_id
                    );

                    if ($stmt->execute()) {
                        $success = "✅ Story added successfully!";
                        $title = $content = $video_link = "";
                        $category_id = "";
                    } else {
                        $error = "Database error. Try again.";
                    }

                } else {
                    $error = "Image upload failed!";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Story</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f4f6fb;min-height:100vh;}

.navbar{
    display:flex;justify-content:space-between;align-items:center;
    background:#667eea;color:#fff;padding:12px 20px;
    border-radius:10px;margin:30px auto 20px;max-width:650px;
}
.navbar .title{font-size:20px;font-weight:600;}
.navbar a{color:#fff;text-decoration:none;font-weight:600;
    background:#5563c1;padding:8px 14px;border-radius:8px;}

.container{
    max-width:650px;margin:0 auto 40px;background:#fff;
    padding:35px;border-radius:18px;
    box-shadow:0 15px 35px rgba(0,0,0,.1);
}

.form-group{margin-bottom:20px;}
.form-group label{display:block;margin-bottom:6px;font-weight:600;color:#555;}
.form-group input,
.form-group textarea,
.form-group select{
    width:100%;padding:14px;border-radius:10px;border:1px solid #ccc;
}
textarea{min-height:140px;}

.btn{
    width:100%;padding:14px;
    background:linear-gradient(135deg,#ff7a18,#ffb347);
    color:#fff;border:none;border-radius:30px;
    font-size:16px;font-weight:600;cursor:pointer;
}

.success-msg{background:#e6fff0;color:#1e7e34;padding:10px;border-radius:10px;margin-bottom:15px;text-align:center;font-weight:600;}
.error-msg{background:#ffe6e6;color:#b30000;padding:10px;border-radius:10px;margin-bottom:15px;text-align:center;font-weight:600;}

.preview{margin-top:10px;display:none;}
.preview img{max-width:100%;border-radius:12px;}
</style>
</head>

<body>

<div class="navbar">
    <div class="title">➕ Add New Story</div>
    <a href="dashboard.php"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="container">

<?php if($success): ?><div class="success-msg"><?= $success ?></div><?php endif; ?>
<?php if($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">

<div class="form-group">
    <label>Story Title</label>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>
</div>

<div class="form-group">
    <label>Category</label>
    <select name="category_id" required>
        <option value="">Select Category</option>
        <?php 
        $catResult->data_seek(0);
        while($cat = $catResult->fetch_assoc()): ?>
            <option value="<?= $cat['id'] ?>" <?= ($category_id==$cat['id'])?"selected":"" ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<div class="form-group">
    <label>Story Content</label>
    <textarea name="content" required><?= htmlspecialchars($content) ?></textarea>
</div>

<div class="form-group">
    <label>Video Link (YouTube / MP4 URL)</label>
    <input type="url" name="video_link" placeholder="https://youtube.com/..." value="<?= htmlspecialchars($video_link) ?>">
</div>

<div class="form-group">
    <label>Story Image</label>
    <input type="file" name="image" accept="image/*" onchange="previewImage(event)" required>
    <div class="preview" id="preview">
        <img id="previewImg">
    </div>
</div>

<button class="btn" type="submit">Add Story</button>

</form>
</div>

<script>
function previewImage(e){
    document.getElementById("preview").style.display="block";
    document.getElementById("previewImg").src=URL.createObjectURL(e.target.files[0]);
}
</script>

</body>
</html>
