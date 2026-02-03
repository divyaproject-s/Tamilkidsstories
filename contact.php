<?php
include "includes/db.php";
include "includes/header.php"; // session is already started here

/* ===============================
   FORM SUBMIT HANDLING
================================ */
$success = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = "⚠️ All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠️ Please enter a valid email address.";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $_SESSION['contact_success'] = true;
            header("Location: contact.php");
            exit;
        } else {
            $error = "❌ Something went wrong. Please try again.";
        }
    }
}

/* Show success message once */
if (isset($_SESSION['contact_success'])) {
    $success = true;
    unset($_SESSION['contact_success']);
}
?>



<style>
*{ box-sizing:border-box; margin:0; padding:0; font-family:'Comic Sans MS','Segoe UI',sans-serif; }

.contact-section{
  min-height:80vh;
  background:linear-gradient(270deg,#e0f2fe,#fff7e6,#fde68a);
  background-size:600% 600%;
  animation:bgMove 14s ease infinite;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:40px 15px;
}
@keyframes bgMove{
  0%{background-position:0% 50%}
  50%{background-position:100% 50%}
  100%{background-position:0% 50%}
}

.contact-container{
  background:#fff;
  max-width:520px;
  width:100%;
  padding:30px;
  border-radius:25px;
  box-shadow:0 20px 40px rgba(0,0,0,.15);
  text-align:center;
  animation:popIn 1s ease;
}
@keyframes popIn{
  0%{opacity:0; transform:scale(.8)}
  100%{opacity:1; transform:scale(1)}
}

.contact-container h1{
  color:#ff6f00;
  font-size:36px;
  margin-bottom:10px;
  animation:bounceTitle 2s infinite;
}
@keyframes bounceTitle{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-8px)}
}

.contact-container p{
  color:#555;
  font-size:17px;
  margin-bottom:15px;
}

/* messages */
.success-msg{
  background:#dcfce7;
  color:#166534;
  padding:12px;
  border-radius:12px;
  margin:15px 0;
}
.error-msg{
  background:#fee2e2;
  color:#991b1b;
  padding:12px;
  border-radius:12px;
  margin:15px 0;
}

/* animals */
.fun-icons{
  display:flex;
  justify-content:center;
  gap:18px;
  margin-bottom:18px;
}
.fun-icons img{
  width:70px;
  animation:floatAnimal 3s ease-in-out infinite;
}
.fun-icons img:nth-child(2){animation-delay:.6s}
.fun-icons img:nth-child(3){animation-delay:1.2s}
@keyframes floatAnimal{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-15px)}
}

/* form */
.contact-form{
  margin-top:20px;
}
.contact-form input,
.contact-form textarea{
  width:100%;
  padding:12px 14px;
  margin-bottom:14px;
  border-radius:14px;
  border:2px solid #e5e7eb;
  font-size:15px;
}
.contact-form input:focus,
.contact-form textarea:focus{
  outline:none;
  border-color:#fb923c;
  box-shadow:0 0 0 3px rgba(251,146,60,.3);
}

.btn{
  width:100%;
  padding:14px;
  background:#fb923c;
  color:#fff;
  font-size:18px;
  border:none;
  border-radius:30px;
  cursor:pointer;
  transition:.3s;
}
.btn:hover{
  background:#f97316;
  transform:scale(1.05);
}

@media(max-width:480px){
  .contact-container{ padding:20px; }
}
</style>

<section class="contact-section">
  <div class="contact-container">

    <div class="fun-icons">
      <img src="https://cdn-icons-png.flaticon.com/512/3069/3069172.png" alt="Animal 1">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616554.png" alt="Animal 2">
      <img src="https://cdn-icons-png.flaticon.com/512/1998/1998610.png" alt="Animal 3">
    </div>

    <h1>📬 Contact Us</h1>
    <p>Share your ideas, feedback, or favorite stories 🌈</p>

    <?php if($success): ?>
      <div class="success-msg">🎉 Message sent successfully!</div>
    <?php endif; ?>

    <?php if($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="contact-form">
      <input type="text" name="name" placeholder="👦 Your Name" required>
      <input type="email" name="email" placeholder="📧 Your Email" required>
      <textarea name="message" placeholder="💬 Your Message" rows="5" required></textarea>
      <button class="btn">🚀 Send Message</button>
    </form>

  </div>
</section>

<?php include "includes/footer.php"; ?>
