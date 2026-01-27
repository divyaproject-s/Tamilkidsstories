<?php
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Demo credentials
    if ($username === "admin" && $password === "1234") {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
/* ===== GLOBAL ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    height: 100vh;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    justify-content: center;
    align-items: center;
}

/* ===== LOGIN CONTAINER ===== */
.login-wrapper {
    width: 100%;
    max-width: 420px;
    padding: 20px;
}

/* ===== GLASS CARD ===== */
.login-card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    padding: 45px 35px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    color: #fff;
    animation: fadeUp 0.8s ease;
}

/* ===== TITLE ===== */
.login-card h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 28px;
    font-weight: 600;
}

/* ===== INPUT GROUP ===== */
.input-group {
    position: relative;
    margin-bottom: 22px;
}

.input-group input {
    width: 100%;
    padding: 14px 45px 14px 45px;
    border-radius: 30px;
    border: none;
    outline: none;
    font-size: 15px;
}

.input-group i {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.input-group .icon-left {
    left: 16px;
}

.input-group .icon-right {
    right: 16px;
    cursor: pointer;
}

/* ===== ERROR ===== */
.error-msg {
    background: rgba(255, 0, 0, 0.15);
    color: #fff;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 15px;
    text-align: center;
    font-size: 14px;
}

/* ===== BUTTON ===== */
.login-btn {
    width: 100%;
    padding: 14px;
    border-radius: 30px;
    border: none;
    background: linear-gradient(135deg, #ff7a18, #ffb347);
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    margin-bottom: 10px;
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}

/* ===== FOOTER TEXT ===== */
.login-footer {
    text-align: center;
    margin-top: 18px;
    font-size: 13px;
    opacity: 0.85;
}

/* ===== ANIMATION ===== */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===== RESPONSIVE ===== */
@media(max-width: 480px) {
    .login-card {
        padding: 35px 25px;
    }
}
</style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <h2>Admin Login</h2>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="post">
            <div class="input-group">
                <i class="fa fa-user icon-left"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="fa fa-lock icon-left"></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fa fa-eye icon-right" onclick="togglePassword()"></i>
            </div>

            <button class="login-btn">Login</button>
        </form>

        <!-- Button to go to user page -->
        <form action="../index.php" method="get">
            <button class="login-btn" style="background: linear-gradient(135deg, #43cea2, #185a9d);">
                Go to User Page
            </button>
        </form>

        <div class="login-footer">
            © 2026 Tamil Kids Stories Admin
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
