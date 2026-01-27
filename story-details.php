<?php
include "includes/db.php";
include "includes/header.php";

$story_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$story_id) die("Story ID missing");

/* FETCH STORY */
$stmt = $conn->prepare("SELECT * FROM stories WHERE id=?");
$stmt->bind_param("i", $story_id);
$stmt->execute();
$story = $stmt->get_result()->fetch_assoc();
if (!$story) die("Story not found");

/* CATEGORY */
$stmt2 = $conn->prepare("SELECT name FROM categories WHERE id=?");
$stmt2->bind_param("i", $story['category_id']);
$stmt2->execute();
$cat = $stmt2->get_result()->fetch_assoc();
$cat_name = $cat ? $cat['name'] : "Category";

/* FAV */
$fav_ids = [];
$ip = $_SERVER['REMOTE_ADDR'];
$res = $conn->query("SELECT story_id FROM favourites WHERE user_ip='$ip'");
while($r=$res->fetch_assoc()) $fav_ids[]=$r['story_id'];
?>

<!-- ================= HERO ================= -->
<section class="story-hero"
  style="background-image:url('uploads/stories/<?= htmlspecialchars($story['image']) ?>')">

  <!-- 🐰 LEFT ANIMAL -->
  <div class="animal left-animal">🐰</div>

  <!-- 🦁 RIGHT ANIMAL -->
  <div class="animal right-animal">🦁</div>

  <div class="hero-overlay">

    <a href="category.php?id=<?= $story['category_id'] ?>" class="back-link">
      ← Back to <?= htmlspecialchars($cat_name) ?>
    </a>

    <button class="fav-float <?= in_array($story['id'],$fav_ids)?'active':'' ?>"
            data-id="<?= $story['id'] ?>">
      <?= in_array($story['id'],$fav_ids)?'❤️':'♡' ?>
    </button>

    <h1><?= htmlspecialchars($story['title']) ?></h1>
  </div>
</section>

<!-- ================= CONTENT ================= -->
<section class="story-body">
  <div class="container">

    <?php if(!empty($story['video_link'])): ?>
    <div class="card video-card">
      <?php
      $video = $story['video_link'];
      if(strpos($video,'youtu')!==false){
        preg_match('/(youtu\.be\/|v=)([^&]+)/',$video,$m);
        $vid = $m[2] ?? '';
      ?>
        <iframe src="https://www.youtube.com/embed/<?= $vid ?>" allowfullscreen></iframe>
      <?php } else { ?>
        <video controls>
          <source src="<?= htmlspecialchars($video) ?>" type="video/mp4">
        </video>
      <?php } ?>
    </div>
    <?php endif; ?>

    <div class="card content-card">
      <?= nl2br(htmlspecialchars($story['content'])) ?>
    </div>

  </div>
</section>

<!-- ================= STYLES ================= -->
<style>
*{box-sizing:border-box}
body{background:#f3f4f6;font-family:'Segoe UI',sans-serif}

.container{max-width:900px;margin:auto;padding:20px}

/* HERO */
.story-hero{
  height:420px;
  background-size:cover;
  background-position:center;
  position:relative;
  overflow:hidden;
}

/* FLOATING ANIMALS */
.animal{
  position:absolute;
  bottom:40px;
  font-size:5.5rem;
  z-index:5;
  animation:float 4s ease-in-out infinite;
  filter:drop-shadow(0 6px 10px rgba(0,0,0,.4));
}

.left-animal{ left:25px; }
.right-animal{ right:25px; animation-delay:1s; }

/* OVERLAY */
.hero-overlay{
  position:relative;
  z-index:3;
  height:100%;
  padding:30px;
  color:#fff;
  background:linear-gradient(to top,rgba(0,0,0,.7),rgba(0,0,0,.2));
  display:flex;
  flex-direction:column;
  justify-content:flex-end;
}

.hero-overlay h1{
  font-size:3.2rem;
  font-weight:800;
  text-align:center;
}

.back-link{
  background:rgba(255,255,255,.25);
  padding:8px 18px;
  border-radius:30px;
  color:#fff;
  text-decoration:none;
  width:max-content;
  margin-bottom:auto;
}

.fav-float{
  position:absolute;
  top:20px;
  right:20px;
  font-size:1.8rem;
  background:#fff;
  border:none;
  border-radius:50%;
  width:55px;
  height:55px;
  cursor:pointer;
}
.fav-float.active{color:#ff3b3b}

/* CONTENT */
.card{
  background:#fff;
  border-radius:18px;
  padding:25px;
  margin-bottom:25px;
  box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.video-card iframe,
.video-card video{
  width:100%;
  height:420px;
  border-radius:12px;
}

.content-card{
  line-height:1.8;
  font-size:1.05rem;
}

/* FLOAT ANIMATION */
@keyframes float{
  0%,100%{ transform:translateY(0); }
  50%{ transform:translateY(-20px); }
}

/* MOBILE */
@media(max-width:600px){
  .story-hero{height:300px}
  .hero-overlay h1{font-size:1.6rem}
  .animal{display:none}
  .video-card iframe,.video-card video{height:220px}
}
</style>

<!-- ================= AJAX ================= -->
<script>
document.querySelectorAll('.fav-float').forEach(btn=>{
  btn.onclick=()=>{
    fetch("favourite.php",{
      method:"POST",
      headers:{"Content-Type":"application/x-www-form-urlencoded"},
      body:"story_id="+btn.dataset.id
    })
    .then(r=>r.json())
    .then(d=>{
      if(d.status=="added"){btn.classList.add("active");btn.innerHTML="❤️";}
      if(d.status=="removed"){btn.classList.remove("active");btn.innerHTML="♡";}
    });
  }
});
</script>

<?php include "includes/footer.php"; ?>
