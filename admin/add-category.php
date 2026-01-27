<?php
session_start();
include "../includes/db.php";
if (!isset($_SESSION['admin'])) exit;

/* ================= ADD CATEGORY ================= */
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $img = "";

    if (!empty($_FILES['image']['name'])) {
        $img = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/categories/" . $img);
    }

    $conn->query("INSERT INTO categories (name,image) VALUES ('$name','$img')");
    header("Location: add-category.php");
    exit;
}

/* ================= EDIT LOAD ================= */
$editMode = false;
$editId = $editName = $editImage = "";

if (isset($_GET['edit'])) {
    $editMode = true;
    $editId = intval($_GET['edit']);
    $cat = $conn->query("SELECT * FROM categories WHERE id=$editId")->fetch_assoc();
    $editName = $cat['name'];
    $editImage = $cat['image'];
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);

    if (!empty($_FILES['image']['name'])) {
        $img = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/categories/" . $img);
        $conn->query("UPDATE categories SET name='$name', image='$img' WHERE id=$id");
    } else {
        $conn->query("UPDATE categories SET name='$name' WHERE id=$id");
    }

    header("Location: add-category.php");
    exit;
}

/* ================= FETCH ================= */
$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Categories</title>

<style>
*{ box-sizing:border-box; }

body{
    margin:0;
    font-family:Segoe UI, sans-serif;
    background:#f4f6fb;
}

/* ===== HEADER ===== */
.header{
    background:#4338ca;
    color:#fff;
    padding:16px 25px;
    font-size:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.back-btn{
    background:rgba(255,255,255,.2);
    color:#fff;
    text-decoration:none;
    padding:8px 14px;
    border-radius:8px;
    font-size:14px;
}
.back-btn:hover{ background:rgba(255,255,255,.35); }

/* ===== ADD BOX ===== */
.add-box{
    background:#fff;
    max-width:480px;
    margin:30px auto;
    padding:22px;
    border-radius:16px;
    box-shadow:0 12px 30px rgba(0,0,0,.1);
}

.add-box h3{
    margin-top:0;
    text-align:center;
}

.add-box input,
.add-box button{
    width:100%;
    padding:12px;
    margin-top:12px;
    border-radius:8px;
}

.add-box input{ border:1px solid #ccc; }

.add-box button{
    background:#16a34a;
    color:#fff;
    border:none;
    font-size:15px;
    cursor:pointer;
}

/* ===== GRID ===== */
.container{ padding:30px; }

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:20px;
}

.card{
    background:#fff;
    border-radius:14px;
    padding:15px;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
    text-align:center;
}

.card img{
    width:100%;
    height:150px;
    object-fit:cover;
    border-radius:10px;
}

.card h4{ margin:10px 0; }

.card a{
    display:inline-block;
    margin:4px;
    padding:6px 14px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
}

.edit{ background:#4f46e5;color:#fff; }
.delete{ background:#e11d48;color:#fff; }

/* ===== EDIT MODAL ===== */
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.6);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:9999;
}

.form-box{
    background:#fff;
    width:360px;
    padding:22px;
    border-radius:16px;
}

.form-box h3{ text-align:center; margin-top:0; }

.form-box input{
    width:100%;
    padding:10px;
    margin-bottom:12px;
    border-radius:8px;
    border:1px solid #ccc;
}

.preview{
    width:100%;
    height:160px;
    object-fit:contain;
    border:1px solid #ddd;
    border-radius:10px;
    margin-bottom:10px;
}

.form-box button{
    width:100%;
    padding:12px;
    background:#2563eb;
    color:#fff;
    border:none;
    border-radius:8px;
}

.close{
    text-align:center;
    margin-top:10px;
}
.close a{
    text-decoration:none;
    color:#555;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <span>Manage Categories</span>
    <a href="dashboard.php" class="back-btn">⬅ Dashboard</a>
</div>

<!-- ADD CATEGORY (NOT POPUP) -->
<div class="add-box">
    <h3>Add Category</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Category name" required>
        <input type="file" name="image" required>
        <button name="add">Add Category</button>
    </form>
</div>

<!-- CATEGORY GRID -->
<div class="container">
    <div class="grid">
        <?php while($row = $categories->fetch_assoc()): ?>
        <div class="card">
            <img src="../uploads/categories/<?= $row['image'] ?: 'https://via.placeholder.com/300x200' ?>">
            <h4><?= htmlspecialchars($row['name']) ?></h4>
            <a href="?edit=<?= $row['id'] ?>" class="edit">Edit</a>
            <a href="delete-category.php?id=<?= $row['id'] ?>"
               class="delete"
               onclick="return confirm('Delete this category?')">
               Delete
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- EDIT POPUP -->
<?php if($editMode): ?>
<div class="modal">
    <form class="form-box" method="post" enctype="multipart/form-data">
        <h3>Edit Category</h3>

        <input type="hidden" name="id" value="<?= $editId ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($editName) ?>" required>
        <input type="file" name="image">

        <?php if($editImage): ?>
            <img src="../uploads/categories/<?= $editImage ?>" class="preview">
        <?php endif; ?>

        <button name="update">Update Category</button>
        <div class="close"><a href="add-category.php">Cancel</a></div>
    </form>
</div>
<?php endif; ?>

</body>
</html>
