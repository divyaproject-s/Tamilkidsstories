<?php
include "includes/db.php";

$message = "";
$messageType = ""; // success or error

if(isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if mobile number already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0) {
        $message = "❌ Mobile number already exists!";
        $messageType = "error";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, mobile, email, gender, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $mobile, $email, $gender, $dob, $password);

        if($stmt->execute()) {
            $message = "✅ Registered successfully. Waiting for admin approval.";
            $messageType = "success";
        } else {
            $message = "❌ Something went wrong. Try again.";
            $messageType = "error";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Registration</title>
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

/* Increased width for registration form */
.login-box{ background:#fff; width:400px; padding:35px 30px; border-radius:18px; box-shadow:0 25px 50px rgba(0,0,0,.25); text-align:center; animation:zoomIn .8s ease; max-height: 90vh; overflow-y: auto; }
@keyframes zoomIn{ from{transform:scale(.8);opacity:0} to{transform:scale(1);opacity:1} }

.login-box h2{ font-size:28px; margin-bottom:10px; color:#333; }
.login-box p{ font-size:14px; color:#666; margin-bottom:25px; }

.input-box{ margin-bottom:12px; text-align: left; }
.input-box input, .input-box select{ width:100%; padding:12px 14px; border-radius:10px; border:1px solid #ddd; font-size:15px; }
.input-box input:focus, .input-box select:focus{ outline:none; border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,.3); }
.input-box label { font-size: 12px; color: #666; margin-bottom: 4px; display: block; margin-left: 5px; }

button{ width:100%; padding:12px; border:none; border-radius:25px; background:#667eea; color:#fff; font-size:16px; cursor:pointer; transition:.3s; margin-top: 10px; }
button:hover{ background:#5563d6; transform:translateY(-2px); }

.footer-text{ margin-top:18px; font-size:13px; color:#777; }
.footer-text a{ color:#667eea; text-decoration:none; }

.message { padding: 10px; border-radius: 10px; margin-bottom: 15px; font-size: 14px; position: relative; }
.message.error { color: red; background: #fee2e2; }
.message.success { color: green; background: #dcfce7; }
.close-btn { position: absolute; top: 5px; right: 10px; cursor: pointer; font-weight: bold; font-size: 16px; color: inherit; }

@media(max-width:420px){ .login-box{width:90%} }

/* Scrollbar styling for the box */
.login-box::-webkit-scrollbar { width: 6px; }
.login-box::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 3px; }
</style>
<script>
function closeMessage() {
    var msg = document.querySelector('.message');
    if(msg) msg.style.display = 'none';
}
</script>
</head>
<body>

<!-- Floating icons -->
<div class="star">⭐</div>
<div class="star">🌙</div>
<div class="star">✨</div>
<div class="star">🌟</div>

<div class="login-box">
    <h2>Join Us! 🚀</h2>
    <p>Create an account to start reading</p>

    <?php if($message != ""): ?>
        <div class="message <?= $messageType ?>">
            <?= $message ?>
            <span class="close-btn" onclick="closeMessage()">×</span>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-box">
            <input type="text" name="name" placeholder="👤 Full Name" required>
        </div>
        <div class="input-box">
            <input type="text" name="mobile" placeholder="📱 Mobile Number" required>
        </div>
        <div class="input-box">
            <input type="email" name="email" placeholder="📧 Email Address" required>
        </div>
        
        <div class="input-box" style="display: flex; gap: 10px;">
            <div style="flex: 1;">
                <select name="gender" required>
                    <option value="" disabled selected>⚧ Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div style="flex: 1;">
                 <input type="text" name="dob" placeholder="🎂 DOB" onfocus="(this.type='date')" onblur="(this.type='text')" required>
            </div>
        </div>

        <div class="input-box">
            <input type="password" name="password" placeholder="🔒 Password" required>
        </div>

        <button name="register">Register</button>
    </form>

    <div class="footer-text">
        Already have an account? <a href="login.php">Login here</a> <br><br>
        <a href="index.php">← Back to Home</a>
    </div>
</div>

</body>
</html>
