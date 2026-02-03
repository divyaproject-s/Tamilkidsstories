<?php
session_start();
include "../includes/db.php";

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = "";

/* ===== ACTIONS ===== */
if (isset($_GET['approve_id'])) {
    $id = intval($_GET['approve_id']);
    $conn->query("UPDATE users SET status='approved' WHERE id=$id");
    $msg = "User approved successfully ✅";
}

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM users WHERE id=$id");
    $msg = "User deleted successfully ❌";
}


/* ===== ADD USER ===== */
if (isset($_POST['add_user'])) {
    $name     = trim($_POST['name']);
    $mobile   = trim($_POST['mobile']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];
    $status   = 'approved'; // Added by admin = auto-approved

    $check = $conn->prepare("SELECT id FROM users WHERE mobile=?");
    $check->bind_param("s", $mobile);
    $check->execute();
    if($check->get_result()->num_rows > 0) {
        $msg = "Error: Mobile number already exists! ⚠️";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, mobile, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $mobile, $email, $password, $role, $status);
        if($stmt->execute()) {
            $msg = "User added successfully ✅";
        } else {
            $msg = "Error adding user ⚠️";
        }
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
*{box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{margin:0;background:#f4f6fb;}

/* ===== NAVBAR ===== */
.navbar{
    background:#4f46e5;
    color:#fff;
    padding:16px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.navbar h2{margin:0;font-size:22px;}
.navbar a{
    background:#6366f1;
    color:#fff;
    text-decoration:none;
    padding:10px 16px;
    border-radius:10px;
}
.nav-btns{ display: flex; gap: 10px; }
.add-btn{ background: #16a34a !important; cursor: pointer; border: none; font-weight: 600; }

/* ===== CONTAINER ===== */
.container{padding:40px;}

/* ===== MESSAGE ===== */
.msg{
    text-align:center;
    margin-bottom:30px;
    color:#16a34a;
    font-weight:600;
}

/* ===== GRID ===== */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
    gap:30px;
}

/* ===== CARD ===== */
.card{
    background:#fff;
    border-radius:18px;
    padding:25px 20px;
    text-align:center;
    box-shadow:0 12px 30px rgba(0,0,0,.08);
}

/* ===== USER INFO ===== */
.username{
    font-size:20px;
    font-weight:600;
    color:#4f46e5;
    margin-bottom:6px;
}

.mobile{
    font-size:14px;
    color:#6b7280;
    margin-bottom:14px;
}

/* ===== BADGES ===== */
.badges{
    display:flex;
    justify-content:center;
    gap:10px;
    margin-bottom:18px;
}
.badge{
    padding:6px 14px;
    border-radius:20px;
    font-size:12px;
    color:#fff;
    font-weight:600;
}
.user{background:#16a34a;}
.admin{background:#2563eb;}
.approved{background:#22c55e;}
.pending{background:#f59e0b;}

/* ===== BUTTONS ===== */
.actions{
    display:flex;
    justify-content:center;
    gap:12px;
    margin-bottom:16px;
}
.btn{
    padding:9px 18px;
    border-radius:10px;
    font-size:14px;
    font-weight:600;
    text-decoration:none;
    color:#fff;
}
.approve{background:#22c55e;}
.delete{background:#ef4444;}


/* ===== MODAL ===== */
.modal{
    display:none;position:fixed;inset:0;
    background:rgba(0,0,0,.6);z-index:999;
}
.modal-box{
    background:#fff;max-width:500px;
    margin:60px auto;padding:25px;border-radius:15px;
}
.modal-box input,
.modal-box select{
    width:100%;padding:12px;
    margin-bottom:12px;border:1px solid #ccc;border-radius:8px;
}
.modal-box button{
    width:100%;background:#16a34a;
    color:#fff;border:none;padding:12px;border-radius:30px;
    font-weight: 600; cursor: pointer;
}
.close{float:right;font-size:22px;cursor:pointer;}
</style>
</head>

<body>

<div class="navbar">
    <h2>Manage Users / Admins</h2>
    <div class="nav-btns">
        <button class="add-btn btn" onclick="openAddModal()">+ Add User</button>
        <a href="dashboard.php">⬅ Dashboard</a>
    </div>
</div>

<div class="container">

<?php if($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>

<div class="grid">
<?php while($u = $users->fetch_assoc()): ?>
<div class="card">

    <div class="username"><?= htmlspecialchars($u['name']) ?></div>
    <div class="mobile"><i class="fa fa-phone"></i> <?= $u['mobile'] ?></div>

    <div class="badges">
        <span class="badge <?= $u['role']=='admin'?'admin':'user' ?>">
            <?= ucfirst($u['role']) ?>
        </span>
        <span class="badge <?= $u['status']=='approved'?'approved':'pending' ?>">
            <?= ucfirst($u['status']) ?>
        </span>
    </div>

    <div class="actions">
        <?php if($u['status']=='pending'): ?>
            <a class="btn approve" href="?approve_id=<?= $u['id'] ?>">Approve</a>
        <?php endif; ?>
        <a class="btn delete" href="?delete_id=<?= $u['id'] ?>"
           onclick="return confirm('Delete user?')">Delete</a>
    </div>


</div>
<?php endwhile; ?>
</div>

</div>

<!-- ===== ADD MODAL ===== -->
<div class="modal" id="addModal">
<div class="modal-box">
    <span class="close" onclick="closeAddModal()">&times;</span>
    <h3 style="margin-bottom: 20px;">+ Add New User/Admin</h3>
    
    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="mobile" placeholder="Mobile Number" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <button name="add_user">Create Account</button>
    </form>
</div>
</div>

<script>
function openAddModal(){
  document.getElementById("addModal").style.display="block";
}
function closeAddModal(){
  document.getElementById("addModal").style.display="none";
}
</script>
</body>
</html>
