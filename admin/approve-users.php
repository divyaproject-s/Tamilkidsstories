<?php
session_start();
include "../includes/db.php";

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ===== APPROVE USER ===== */
if(isset($_GET['approve_id'])){
    $user_id = intval($_GET['approve_id']);
    $stmt = $conn->prepare("UPDATE users SET status='approved' WHERE id=?");
    $stmt->bind_param("i", $user_id);
    if($stmt->execute()){
        $msg = "User ID $user_id approved successfully ✅";
    } else {
        $msg = "Error approving user ID $user_id";
    }
}

/* ===== FETCH PENDING USERS ===== */
$pendingUsersResult = $conn->query("SELECT id, name, mobile FROM users WHERE role='user' AND status='pending'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Approve Users</title>

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

/* ===== GRID ===== */
.container{ padding:30px; }

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:20px;
}

/* ===== USER CARD ===== */
.card{
    background:#fff;
    border-radius:14px;
    padding:20px;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
    text-align:center;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 15px 30px rgba(0,0,0,.12);
}

.card img{
    width:100%;
    height:150px;
    object-fit:cover;
    border-radius:10px;
    margin-bottom:10px;
}

/* ===== TEXT ===== */
.card h4{ margin:10px 0; color:#4338ca; }
.card p{ margin:6px 0; color:#555; font-size:14px; }

/* ===== APPROVE BUTTON ===== */
.approve-btn{
    display:inline-block;
    margin-top:12px;
    padding:8px 16px;
    background:#16a34a;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
    font-weight:bold;
    transition:0.3s;
}
.approve-btn:hover{ background:#15803d; }

/* ===== MESSAGE ===== */
.msg{
    text-align:center;
    color:green;
    margin-bottom:20px;
    font-weight:bold;
}

/* ===== NO USERS ===== */
.no-users{
    text-align:center;
    color:#777;
    margin-top:50px;
    font-size:18px;
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <span>Approve Users</span>
    <a href="dashboard.php" class="back-btn">⬅ Dashboard</a>
</div>

<div class="container">
    <?php if(isset($msg)) echo "<p class='msg'>{$msg}</p>"; ?>

    <?php if($pendingUsersResult->num_rows > 0): ?>
    <div class="grid">
        <?php while($user = $pendingUsersResult->fetch_assoc()): ?>
        <div class="card">
            <!-- Placeholder image for user -->
            <img src="https://via.placeholder.com/300x150?text=User" alt="User Image">
            <h4><?= htmlspecialchars($user['name']) ?></h4>
            <p><i class="fa fa-mobile"></i> <?= htmlspecialchars($user['mobile']) ?></p>
            <a class="approve-btn" href="approve-users.php?approve_id=<?= $user['id'] ?>">Approve</a>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
        <p class="no-users">No pending users ✅</p>
    <?php endif; ?>
</div>

</body>
</html>
