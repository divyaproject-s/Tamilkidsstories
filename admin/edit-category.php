<?php
session_start();
include "../includes/db.php";

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Edit Category";

// Validate ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: add-category.php");
    exit;
}
$id = intval($_GET['id']);

// Fetch category
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0){
    exit("Category not found!");
}
$cat = $result->fetch_assoc();

// Handle form submission
$success = $error = "";
if(isset($_POST['update'])){
    $name = trim($_POST['name']);
    
    if(empty($name)){
        $error = "Category name cannot be empty!";
    } else {
        // Image handling
        if(!empty($_FILES['image']['name'])){
            $img = time()."_".$_FILES['image']['name'];
            $uploadDir = "../uploads/categories/";
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir.$img);

            // Optional: delete old image
            if(!empty($cat['image']) && file_exists($uploadDir.$cat['image'])){
                unlink($uploadDir.$cat['image']);
            }

            $stmt = $conn->prepare("UPDATE categories SET name=?, image=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $img, $id);
        } else {
            $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
            $stmt->bind_param("si", $name, $id);
        }

        if($stmt->execute()){
            $success = "Category updated successfully!";
            // Refresh data
            $stmt2 = $conn->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $cat = $stmt2->get_result()->fetch_assoc();
        } else {
            $error = "Error updating category: ".$conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $pageTitle ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
        /* Topbar */
        .topbar {
            background: #6a7de8;
            color: white;
            padding: 15px 20px;
            font-size: 18px;
        }
        .container { max-width: 500px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        form { display: flex; flex-direction: column; gap: 15px; }
        input[type="text"], input[type="file"], textarea { padding: 10px; border-radius: 5px; border: 1px solid #ccc; width: 100%; }
        button { padding: 10px; background: #6a7de8; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #5a6cd8; }
        .message { text-align: center; font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
        img { max-width: 100px; display: block; margin-top: 5px; }
        a { color: #6a7de8; text-decoration: none; }
    </style>
</head>
<body>

<div class="topbar">Admin Panel - Edit Category</div>

<div class="container">

    <?php if($success) echo "<p class='message success'>$success</p>"; ?>
    <?php if($error) echo "<p class='message error'>$error</p>"; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Category Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" required>

        <label>Category Image</label>
        <input type="file" name="image">
        <?php if(!empty($cat['image']) && file_exists("../uploads/categories/".$cat['image'])): ?>
            <img src="../uploads/categories/<?= $cat['image'] ?>" alt="Current Image">
        <?php endif; ?>

        <button name="update">Update Category</button>
    </form>

    <p style="text-align:center; margin-top:15px;">
        <a href="add-category.php">← Back to Manage Categories</a>
    </p>
</div>

</body>
</html>
