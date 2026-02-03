<?php include "includes/header.php"; ?>

<style>
/* ===== BASE ===== */
.about-section{
  background: linear-gradient(270deg,#fff7e6,#e0f2fe,#fde68a);
  background-size: 600% 600%;
  animation: bgMove 15s ease infinite;
  padding: 60px 20px;
  font-family: 'Comic Sans MS','Segoe UI',sans-serif;
}

@keyframes bgMove{
  0%{background-position:0% 50%}
  50%{background-position:100% 50%}
  100%{background-position:0% 50%}
}

.about-container{
  max-width:1100px;
  margin:auto;
  text-align:center;
}

/* ===== TITLE ===== */
.about-container h1{
  font-size:40px;
  color:#ff6f00;
  margin-bottom:20px;
  animation: bounceTitle 2s infinite;
}

@keyframes bounceTitle{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-10px)}
}

/* ===== TEXT ===== */
.about-container p{
  font-size:18px;
  color:#444;
  line-height:1.8;
}

/* ===== ANIMALS ===== */
.animal-row{
  display:flex;
  justify-content:center;
  gap:25px;
  flex-wrap:wrap;
  margin:45px 0;
}

.animal-row img{
  width:120px;
  animation: floatAnimal 3s ease-in-out infinite;
  cursor:pointer;
}

.animal-row img:nth-child(2){ animation-delay:.5s }
.animal-row img:nth-child(3){ animation-delay:1s }
.animal-row img:nth-child(4){ animation-delay:1.5s }

@keyframes floatAnimal{
  0%,100%{ transform:translateY(0) rotate(0deg); }
  50%{ transform:translateY(-20px) rotate(4deg); }
}

.animal-row img:hover{
  animation: wiggle .6s infinite;
}

@keyframes wiggle{
  0%{transform:rotate(0)}
  25%{transform:rotate(6deg)}
  50%{transform:rotate(-6deg)}
  75%{transform:rotate(6deg)}
  100%{transform:rotate(0)}
}

/* ===== CARDS ===== */
.about-cards{
  display:grid;
  grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
  gap:30px;
  margin:40px 0;
}

.about-card{
  background:#fff;
  padding:25px;
  border-radius:22px;
  box-shadow:0 15px 30px rgba(0,0,0,.12);
  animation: popUp 1s ease forwards;
  transition:.3s;
}

.about-card:hover{
  transform: scale(1.07) rotate(1deg);
}

@keyframes popUp{
  0%{opacity:0; transform:scale(.8)}
  100%{opacity:1; transform:scale(1)}
}

.about-card h3{
  color:#2563eb;
  margin-bottom:10px;
  font-size:22px;
}

/* ===== CTA ===== */
.about-cta{
  font-size:20px;
  background:#ffedd5;
  display:inline-block;
  padding:18px 36px;
  border-radius:50px;
  animation: pulse 2s infinite;
}

@keyframes pulse{
  0%{transform:scale(1)}
  50%{transform:scale(1.08)}
  100%{transform:scale(1)}
}

.about-cta a{
  color:#d97706;
  font-weight:bold;
  text-decoration:none;
}

.about-cta a:hover{
  text-decoration:underline;
}
</style>



<section class="about-section">
  <div class="about-container">

    <h1>👋 குட்டி குட்டி கதைகள் பற்றி</h1>

    <p>
      <strong>குட்டி குட்டி கதைகள்</strong> – குழந்தைகளின் கற்பனை உலகத்தை உயிர்ப்பிக்கும்
      ஒரு மந்திரமான தளம்!  
      மகிழ்ச்சி, நீதி மற்றும் கற்பனை நிறைந்த கதைகளின் அழகான உலகம்.
    </p>

    <!-- ANIMAL IMAGES -->
    <div class="animal-row">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" alt="Lion">
      <img src="https://cdn-icons-png.flaticon.com/512/1998/1998610.png" alt="Elephant">
      <img src="https://cdn-icons-png.flaticon.com/512/3069/3069172.png" alt="Rabbit">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616554.png" alt="Monkey">
    </div>

    <!-- FEATURE CARDS -->
    <div class="about-cards">
      <div class="about-card">
        <h3>🎨 மகிழ்ச்சி & படைப்பாற்றல்</h3>
        <p>
          குழந்தைகளின் படைப்பாற்றலை தூண்டும் விதமாக,
          வாசிப்பை ஒரு சந்தோஷமான அனுபவமாக மாற்றும் கதைகள்.
        </p>
      </div>

      <div class="about-card">
        <h3>💡 வாழ்க்கை பாடங்கள்</h3>
        <p>
          ஒவ்வொரு கதையும் ஒரு நீதியுடன் அமைந்துள்ளது.
          அன்பு, நேர்மை, துணிச்சல் போன்ற நல்ல பண்புகளை கற்றுத் தருகிறது.
        </p>
      </div>

      <div class="about-card">
        <h3>🌈 கற்பனை உலகம்</h3>
        <p>
          பேசும் விலங்குகள், மந்திரக் காட்டுகள்,
          குழந்தைகளை ஆச்சரியமூட்டும் கற்பனை பயணம்.
        </p>
      </div>
    </div>

    <p class="about-cta">
      🌟 கற்றலும் மகிழ்ச்சியும் நிறைந்த பயணத்தில் எங்களுடன் சேருங்கள்!  
      இன்று எங்கள் <a href="stories.php">கதைகளை</a> ஆராயுங்கள் 🐰📖
    </p>

  </div>
</section>

<?php include "includes/footer.php"; ?>
