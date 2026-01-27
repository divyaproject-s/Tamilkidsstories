// Hamburger toggle
const toggler = document.querySelector('.navbar-toggler');
const navLinks = document.querySelector('.nav-links');

toggler.addEventListener('click', () => {
  toggler.classList.toggle('open');
  navLinks.classList.toggle('show');
});


document.querySelectorAll('.fav-btn').forEach(btn=>{
  btn.addEventListener('click', function(){
    let storyId = this.dataset.id;
    let button = this;

    fetch("favourite.php", {
      method: "POST",
      headers: {"Content-Type":"application/x-www-form-urlencoded"},
      body: "story_id=" + storyId
    })
    .then(res => res.json())
    .then(data => {
      if(data.status === "added"){
        button.classList.add("active");
        button.innerHTML = "❤️";
      } else if(data.status === "removed"){
        button.classList.remove("active");
        button.innerHTML = "♡";
      }
    });
  });
});

