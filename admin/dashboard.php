<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include "../includes/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#f4f6fb;
    display:flex;
    min-height:100vh;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:250px;
    background:linear-gradient(180deg,#667eea,#764ba2);
    color:#fff;
    padding:25px 20px;
}

.sidebar h2{
    text-align:center;
    margin-bottom:40px;
}

.sidebar a{
    display:block;
    color:#fff;
    text-decoration:none;
    padding:14px 15px;
    margin-bottom:12px;
    border-radius:10px;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.2);
}

/* ===== MAIN ===== */
.main{
    flex:1;
    padding:30px;
}

/* ===== HEADER ===== */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.logout-btn{
    background:#ff5c5c;
    color:#fff;
    border:none;
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

.card-link{
    text-decoration:none;
    color:inherit;
    display:block;
}

.card{
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    transition:0.3s;
    cursor:pointer;
}

.card:hover{
    transform:translateY(-6px);
}

.card i{
    font-size:32px;
    color:#667eea;
    margin-bottom:15px;
}

.card h3{
    margin-bottom:8px;
    color:#333;
}

.card p{
    color:#555;
    font-size:14px;
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
    <a href="messages.php"><i class="fa fa-envelope"></i> User Messages</a>
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="main">

    <div class="header">
        <h1>Welcome Admin 👋</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="cards">

        <!-- ADD STORY -->
        <a href="add-story.php" class="card-link">
            <div class="card">
                <i class="fa fa-plus-circle"></i>
                <h3>Add Story</h3>
                <p>Create and publish new kids stories</p>
            </div>
        </a>

        <!-- MANAGE STORIES -->
        <a href="edit-story.php" class="card-link">
            <div class="card">
                <i class="fa fa-book-open"></i>
                <h3>Manage Stories</h3>
                <p>Edit or delete existing stories</p>
            </div>
        </a>

        <!-- ADD CATEGORY -->
        <a href="add-category.php" class="card-link">
            <div class="card">
                <i class="fa fa-tags"></i>
                <h3>Add Category</h3>
                <p>Create story categories with image</p>
            </div>
        </a>

        <!-- VIEW USER PAGE -->
        <a href="../index.php" class="card-link">
            <div class="card">
                <i class="fa fa-eye"></i>
                <h3>View User Page</h3>
                <p>Check how stories appear to users</p>
            </div>
        </a>

        <!-- USER MESSAGES -->
        <a href="messages.php" class="card-link">
            <div class="card">
                <i class="fa fa-envelope"></i>
                <h3>User Messages</h3>
                <p>View messages sent via Contact Form</p>
            </div>
        </a>

    </div>
</div>

</body>
</html>
