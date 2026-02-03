<?php
session_start();
include "includes/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mobile   = trim($_POST['mobile']);
    $password = trim($_POST['password']);

    if ($mobile == "" || $password == "") {
        $error = "⚠️ Please fill all fields";
    } else {

        $stmt = $conn->prepare("SELECT id, role, status, password, name FROM users WHERE mobile=?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();

            if (!password_verify($password, $user['password'])) {
                $error = "❌ Wrong password";
            } elseif ($user['status'] !== 'approved') {
                $error = "⏳ Waiting for admin approval";
            } else {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            }
        } else {
            $error = "❌ Mobile number not registered";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{ margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
body{ height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#667eea,#764ba2); overflow:hidden; }
.star{ position:absolute; font-size:22px; animation:float 6s infinite ease-in-out; }
.star:nth-child(1){top:20%;left:15%;}
.star:nth-child(2){top:70%;left:20%;animation-delay:1s;}
.star:nth-child(3){top:30%;right:20%;animation-delay:2s;}
.star:nth-child(4){bottom:15%;right:15%;animation-delay:3s;}
@keyframes float{ 0%,100%{transform:translateY(0)} 50%{transform:translateY(-20px)} }
.login-box{ background:#fff; width:360px; padding:35px 30px; border-radius:18px; box-shadow:0 25px 50px rgba(0,0,0,.25); text-align:center; animation:zoomIn .8s ease; }
@keyframes zoomIn{ from{transform:scale(.8);opacity:0} to{transform:scale(1);opacity:1} }
.login-box h2{ font-size:28px; margin-bottom:10px; color:#333; }
.login-box p{ font-size:14px; color:#666; margin-bottom:25px; }
.input-box{ margin-bottom:15px; }
.input-box input{ width:100%; padding:12px 14px; border-radius:10px; border:1px solid #ddd; font-size:15px; }
.input-box input:focus{ outline:none; border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,.3); }
button{ width:100%; padding:12px; border:none; border-radius:25px; background:#667eea; color:#fff; font-size:16px; cursor:pointer; transition:.3s; }
button:hover{ background:#5563d6; transform:translateY(-2px); }
.footer-text{ margin-top:18px; font-size:13px; color:#777; }
.footer-text a{ color:#667eea; text-decoration:none; }
@media(max-width:420px){ .login-box{width:90%} }
</style>
</head>
<body>

<!-- Floating icons -->
<div class="star">⭐</div>
<div class="star">🌙</div>
<div class="star">✨</div>
<div class="star">🌟</div>

<div class="login-box">
    <h2>Welcome Back! 👋</h2>
    <p>Login to continue your story journey</p>

    <?php if($error): ?>
        <p style="color:red; background:#fee2e2; padding:10px; border-radius:10px;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="input-box">
            <input type="text" name="mobile" placeholder="📱 Mobile Number" required>
        </div>
        <div class="input-box">
            <input type="password" name="password" placeholder="🔒 Password" required>
        </div>
        <button name="login">Login</button>
    </form>

    <div class="footer-text">
        Don't have an account? <a href="register.php">Register</a> <br><br>
        <a href="index.php">← Back to Home</a>
    </div>
</div>

</body>
</html>
