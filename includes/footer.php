<footer>
  <p>© 2026 Tamil Kids Stories ❤️</p>
</footer>

<!-- GUEST TIMER MODAL -->
<?php if(!isset($_SESSION['user_id'])): ?>
<div id="guest-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center; flex-direction:column;">
  <div style="background:#fff; padding:40px; border-radius:20px; text-align:center; max-width:400px; width:90%; box-shadow:0 0 20px rgba(255,255,255,0.2);">
    <h2 style="color:#ff6f00; margin-bottom:15px; font-size:2rem;">⏳ Time's Up!</h2>
    <p style="font-size:1.1rem; color:#555; margin-bottom:25px;">
      You've enjoyed 10 minutes of free stories! <br>
      Please <strong>Register</strong> or <strong>Login</strong> to continue reading unlimited stories.
    </p>
    <a href="register.php" class="btn" style="display:block; margin-bottom:10px; background:#4CAF50;">Register Now</a>
    <a href="login.php" style="color:#667eea; text-decoration:underline;">Already have an account? Login</a>
  </div>
</div>
<?php endif; ?>

<script src="assets/js/guest-timer.js"></script>
</body>
</html>
