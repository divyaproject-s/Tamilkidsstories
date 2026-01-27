<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include "../includes/db.php";

// Delete message if 'delete_id' is set
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $conn->query("DELETE FROM contacts WHERE id=$delete_id");
    header("Location: messages.php"); // refresh page
    exit;
}

// Fetch all messages
$result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Messages</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', sans-serif; }
body { background:#f5f7fa; display:flex; min-height:100vh; }

/* SIDEBAR */
.sidebar{ width:220px; background:#1e2a38; color:#fff; padding:25px 20px; }
.sidebar h2{ text-align:center; margin-bottom:40px; font-size:22px; }
.sidebar a{ display:block; color:#fff; text-decoration:none; padding:12px 15px; margin-bottom:10px; border-radius:6px; transition:0.2s; }
.sidebar a:hover{ background:rgba(255,255,255,0.1); }

/* MAIN */
.main{ flex:1; padding:25px; overflow-x:auto; }
.header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.logout-btn{ background:#ff4d4f; color:#fff; border:none; padding:8px 16px; border-radius:6px; text-decoration:none; }

/* EMAIL TABLE */
.email-table{ width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
.email-table th, .email-table td{ padding:15px 12px; text-align:left; border-bottom:1px solid #f0f0f0; }
.email-table th{ background:#f7f9fc; font-weight:600; color:#333; }
.email-table tr:hover{ background:#f0f4ff; cursor:pointer; }
.email-subject{ font-weight:500; color:#333; }
.email-preview{ color:#666; font-size:14px; max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.email-date{ color:#999; font-size:13px; }

/* DELETE ICON */
.delete-btn{
    background:none;
    border:none;
    color:#ff4d4f;
    cursor:pointer;
    font-size:16px;
    transition:0.2s;
}
.delete-btn:hover{ color:#e60000; }

/* MODAL */
.modal {
    display:none;
    position:fixed;
    z-index:1000;
    left:0; top:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.6);
    justify-content:center;
    align-items:center;
}
.modal-content {
    background:#fff;
    padding:25px;
    border-radius:10px;
    width:90%;
    max-width:600px;
    position:relative;
}
.close-btn {
    position:absolute;
    top:15px; right:15px;
    font-size:18px;
    background:none;
    border:none;
    cursor:pointer;
    color:#999;
    transition:0.2s;
}
.close-btn:hover{ color:#333; }
.modal h2{ margin-bottom:10px; color:#333; }
.modal p{ margin-bottom:15px; color:#555; }
.modal small{ color:#999; font-size:13px; }

/* RESPONSIVE */
@media(max-width:768px){
    .sidebar{ display:none; }
    .main{ padding:15px; }
    .email-preview{ max-width:150px; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="add-story.php"><i class="fa fa-plus"></i> Add Story</a>
    <a href="edit-story.php"><i class="fa fa-book"></i> Manage Stories</a>
    <a href="add-category.php"><i class="fa fa-tags"></i> Add Category</a>
    <a href="messages.php"><i class="fa fa-envelope"></i> User Messages</a>
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">
    <div class="header">
        <h1>📧 User Messages</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <table class="email-table">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Email</th>
                <th>Message</th>
                <th>Sent At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="email-row" 
                        data-name="<?= htmlspecialchars($row['name']) ?>" 
                        data-email="<?= htmlspecialchars($row['email']) ?>" 
                        data-message="<?= htmlspecialchars($row['message']) ?>" 
                        data-date="<?= $row['created_at'] ?>">
                        <td class="email-subject"><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td class="email-preview"><?= htmlspecialchars(substr($row['message'],0,60)) ?>...</td>
                        <td class="email-date"><?= $row['created_at'] ?></td>
                        <td>
                            <a href="messages.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this message?');">
                                <i class="fa fa-trash delete-btn"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px;">No messages found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODAL -->
<div class="modal" id="messageModal">
    <div class="modal-content">
        <button class="close-btn" id="closeModal">&times;</button>
        <h2 id="modalName"></h2>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p id="modalMessage"></p>
        <small id="modalDate"></small>
    </div>
</div>

<script>
// Modal functionality
const modal = document.getElementById('messageModal');
const closeModal = document.getElementById('closeModal');
const modalName = document.getElementById('modalName');
const modalEmail = document.getElementById('modalEmail');
const modalMessage = document.getElementById('modalMessage');
const modalDate = document.getElementById('modalDate');

document.querySelectorAll('.email-row').forEach(row => {
    row.addEventListener('click', e => {
        // Don't open modal if trash icon clicked
        if(e.target.classList.contains('delete-btn')) return;
        modalName.textContent = row.dataset.name;
        modalEmail.textContent = row.dataset.email;
        modalMessage.textContent = row.dataset.message;
        modalDate.textContent = "Sent at: " + row.dataset.date;
        modal.style.display = "flex";
    });
});

closeModal.addEventListener('click', () => modal.style.display = 'none');
window.addEventListener('click', e => { if(e.target == modal) modal.style.display = 'none'; });
</script>

</body>
</html>
