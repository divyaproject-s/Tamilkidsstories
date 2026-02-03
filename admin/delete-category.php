<?php
session_start();
include "../includes/db.php";

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get image name
    $res = $conn->query("SELECT image FROM categories WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();

        // Delete image file
        if (!empty($row['image']) && file_exists("../uploads/categories/" . $row['image'])) {
            unlink("../uploads/categories/" . $row['image']);
        }
    }

    // Delete category from DB
    $conn->query("DELETE FROM categories WHERE id=$id");
}

header("Location: add-category.php");
exit;
