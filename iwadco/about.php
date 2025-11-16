<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IWADCO | About Us</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
/* GLOBAL */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}
body{background:#f5f8fa;color:#333;display:flex;flex-direction:column;min-height:100vh;scroll-behavior:smooth;}
section{padding:60px 20px;opacity:0;transform:translateY(50px);transition:all 1s ease;}
section.visible{opacity:1;transform:translateY(0);}
h2{color:#0056a6;margin-bottom:20px;font-size:2rem;}
p{line-height:1.6;font-size:1rem;margin-bottom:15px;}

/* NAVBAR */
header{background:#fff;box-shadow:0 2px 6px rgba(0,0,0,0.1);position:fixed;top:0;width:100%;z-index:999;}
.navbar{display:flex;justify-content:center;align-items:center;padding:15px 60px;gap:50px;flex-wrap:wrap;}
.logo{font-size:1.6rem;font-weight:bold;color:#0056a6;cursor:pointer;}
nav{display:flex;align-items:center;gap:25px;flex-wrap:wrap;}
nav a{text-decoration:none;color:#0056a6;font-weight:500;transition:color 0.3s;padding:6px 10px;}
nav a:hover{color:#0077d1;}
.dropdown{position:relative;}
.dropdown-menu{display:none;position:absolute;top:120%;left:0;background: rgba(255,255,255,0.15);backdrop-filter:blur(6px);border-radius:6px;list-style:none;padding:10px 0;min-width:180px;box-shadow:0 4px 10px rgba(0,0,0,0.15);}
.dropdown-menu li a{padding:10px 16px;color:#fff;display:block;text-decoration:none;transition:background 0.3s;}
.dropdown-menu li a:hover{background: rgba(255,255,255,0.25); color:#0056a6;}

/* HERO SLIDESHOW */
.hero{margin-top:80px;position:relative;height:60vh;overflow:hidden;display:flex;align-items:center;justify-content:center;text-align:center;color:white;}
.slide{position:absolute;width:100%;height:100%;background-size:cover;background-position:center;opacity:0;transition:opacity 1s ease-in-out;filter:brightness(0.95);}
.slide.active{opacity:1;}
.hero-overlay{position:absolute;top:0;left:0;width:100%;height:100%;z-index:2;display:flex;align-items:center;justify-content:center;flex-direction:column;padding:20px;
background:linear-gradient(to bottom, rgba(0,0,0,0.15), rgba(0,0,0,0.15));}
.hero-overlay h1{font-size:2.5rem;margin-bottom:15px;animation:fadeInDown 1s ease forwards;}
.hero-overlay p{font-size:1.1rem;max-width:700px;animation:fadeInUp 1s ease forwards;}
@keyframes fadeInDown{from{opacity:0;transform:translateY(-30px);}to{opacity:1;transform:translateY(0);}}
@keyframes fadeInUp{from{opacity:0;transform:translateY(30px);}to{opacity:1;transform:translateY(0);}}
.dots{position:absolute;bottom:20px;display:flex;justify-content:center;width:100%;gap:10px;z-index:3;}
.dot{width:12px;height:12px;border-radius:50%;background: rgba(255,255,255,0.5);cursor:pointer;}
.dot.active{background:#fff;}
.arrow{position:absolute;top:50%;transform:translateY(-50%);font-size:2rem;color: rgba(255,255,255,0.7);background: rgba(0,0,0,0.25);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;cursor:pointer;}
.arrow:hover{background: rgba(0,0,0,0.45); color:#fff;}
.arrow-left{left:20px;}
.arrow-right{right:20px;}

/* COMPANY SECTION - ADVANCED */
.company{display:flex;align-items:center;flex-wrap:wrap;gap:40px;margin-top:50px;}
.company-text{flex:1 1 500px;}
.company-text p{margin-bottom:20px;}
.company-stats{display:flex;gap:20px;flex-wrap:wrap;}
.stat{flex:1 1 120px;background:linear-gradient(135deg,#0077d1,#0056a6);color:#fff;padding:20px;border-radius:12px;text-align:center;transition:transform 0.3s, background 1.5s; cursor:pointer; position:relative; overflow:hidden;}
.stat:hover{transform:translateY(-8px);}
.stat h3{font-size:1.8rem;margin-bottom:5px;}
.stat p{font-size:0.95rem;}
.stat::before{content:"";position:absolute;top:0;left:-75%;width:50%;height:100%;background:rgba(255,255,255,0.2);transform:skewX(-20deg);transition:0.8s;}
.stat:hover::before{left:125%;}
.company-image{flex:1 1 500px;text-align:center;}
.company-image img{width:100%;max-width:500px;border-radius:15px;box-shadow:0 10px 25px rgba(0,0,0,0.15);transition: transform 0.3s;}
.company-image img:hover{transform: scale(1.05);}

/* MISSION & VISION CAROUSEL */
.mv-carousel{position:relative;overflow:hidden;margin:50px auto;max-width:900px;border-radius:12px;}
.mv-track{display:flex;transition:transform 0.6s ease;}
.mv-slide{min-width:100%;box-sizing:border-box;background:#f5f8fa;padding:40px;position:relative;}
.mv-slide img{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;opacity:0.2;border-radius:12px;}
.mv-slide-content{position:relative;z-index:2;max-width:700px;margin:0 auto;text-align:center;}
.mv-slide-content h3{font-size:1.8rem;margin-bottom:15px;color:#0056a6;}
.mv-slide-content p{font-size:1.1rem;line-height:1.6;color:#333;}
.mv-controls{display:flex;justify-content:center;gap:10px;margin-top:15px;}
.mv-dot{width:12px;height:12px;border-radius:50%;background:#ccc;cursor:pointer;}
.mv-dot.active{background:#0077d1;}
.mv-arrow{position:absolute;top:50%;transform:translateY(-50%);font-size:2rem;color:#0077d1;background:rgba(255,255,255,0.7);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:3;}
.mv-arrow:hover{background:rgba(0,0,0,0.45);color:#fff;}
.mv-arrow-left{left:15px;}
.mv-arrow-right{right:15px;}

/* VALUES - GLASSMORPHISM FLIP CARDS */
.values{display:flex;flex-wrap:wrap;gap:30px;justify-content:center;perspective:1200px;padding-bottom:20px;}
.value-card{width:250px;height:200px;position:relative;cursor:pointer;transform-style:preserve-3d;transition:transform 0.6s ease, box-shadow 0.3s;}
.value-card-front,.value-card-back{position:absolute;width:100%;height:100%;border-radius:15px;backface-visibility:hidden;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;transition:background 0.3s, box-shadow 0.3s;}
.value-card-front{
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  color:#0056a6;
  box-shadow:0 8px 30px rgba(0,0,0,0.1);
}
.value-card-front h3{margin-bottom:10px;}
.value-card-back{
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(12px);
  color:#0056a6;
  transform:rotateY(180deg);
  text-align:center;
  box-shadow:0 8px 30px rgba(0,0,0,0.15);
}
.value-card-back p{font-size:0.95rem;line-height:1.6;}
.value-card:hover{box-shadow:0 20px 40px rgba(0,0,0,0.2);}

/* MOBILE RESPONSIVE */
@media(max-width:768px){.values{flex-direction:column;align-items:center;}.value-card{width:80%;margin-bottom:20px;}}

/* FOOTER */
footer{background:#0056a6;color:#fff;text-align:center;padding:15px;margin-top:auto;}
</style>
</head>
<body>

<header>
<div class="navbar">
    <div class="logo" onclick="window.location.href='home.php'">IWADCO</div>
    <nav>
        <a href="home.php">Home</a>
        <a href="about.php">About Us</a>
        <div class="dropdown">
            <a href="services.php">Services ▼</a>
            <ul class="dropdown-menu">
                <li><a href="customer_service.php">Customer Service</a></li>
                <li><a href="installation.php">Create a Ticket</a></li>
            </ul>
        </div>
        <a href="apply.php">Apply Now</a>
        <a href="logout.php" style="color:#0056a6; font-weight:bold;">Logout</a>
    </nav>
</div>
</header>

<!-- HERO SLIDESHOW -->
<section class="hero">
  <div class="slide active" style="background-image: url('assets/slideshow/slide1.jpg');"></div>
  <div class="slide" style="background-image: url('assets/slideshow/slide2.jpg');"></div>
  <div class="slide" style="background-image: url('assets/slideshow/slide3.jpg');"></div>
  <div class="slide" style="background-image: url('assets/slideshow/slide4.jpg');"></div>
  <div class="slide" style="background-image: url('assets/slideshow/slide5.jpg');"></div>

  <div class="hero-overlay">
    <h1 id="slide-title">Reliable Water Solutions</h1>
    <p id="slide-text">Delivering safe and sustainable water for communities.</p>
  </div>

  <div class="dots">
    <div class="dot active" onclick="currentSlide(0)"></div>
    <div class="dot" onclick="currentSlide(1)"></div>
    <div class="dot" onclick="currentSlide(2)"></div>
    <div class="dot" onclick="currentSlide(3)"></div>
    <div class="dot" onclick="currentSlide(4)"></div>
  </div>

  <div class="arrow arrow-left" onclick="prevSlide()">&#10094;</div>
  <div class="arrow arrow-right" onclick="nextSlide()">&#10095;</div>
</section>

<!-- OUR COMPANY -->
<section class="section company">
    <div class="company-text">
        <h2>Our Company</h2>
        <p>IWADCO is the premier water and wastewater services provider for several cities in Metro Manila and Cavite. We operate under strict national standards to ensure safe and sustainable water services for households and businesses.</p>
        <div class="company-stats">
            <div class="stat" data-target="1000"><h3>0</h3><p>Customers Served</p></div>
            <div class="stat" data-target="50"><h3>0</h3><p>Years of Experience</p></div>
            <div class="stat" data-target="99"><h3>0</h3><p>Service Satisfaction</p></div>
        </div>
    </div>
    <div class="company-image">
        <img src="assets/slideshow/slide2.jpg" alt="IWADCO Company">
    </div>
</section>

<!-- MISSION & VISION CAROUSEL -->
<section class="section">
    <h2>Mission & Vision</h2>
    <div class="mv-carousel" id="mv-carousel">
        <div class="mv-track">
            <div class="mv-slide">
                <img src="assets/mv/slide1.jpg" alt="">
                <div class="mv-slide-content">
                    <h3>Vision</h3>
                    <p>To be the leading water solutions company in the Philippines with a strong presence across Asia.</p>
                </div>
            </div>
            <div class="mv-slide">
                <img src="assets/mv/slide2.jpg" alt="">
                <div class="mv-slide-content">
                    <h3>Mission</h3>
                    <p>To provide safe, affordable, and sustainable water solutions that enable our communities to lead healthier, more comfortable lives.</p>
                </div>
            </div>
            <div class="mv-slide">
                <img src="assets/mv/slide3.jpg" alt="">
                <div class="mv-slide-content">
                    <h3>Commitment</h3>
                    <p>We innovate and maintain efficient water management systems for our communities.</p>
                </div>
            </div>
            <div class="mv-slide">
                <img src="assets/mv/slide4.jpg" alt="">
                <div class="mv-slide-content">
                    <h3>Sustainability</h3>
                    <p>Eco-friendly practices guide all our water services and community programs.</p>
                </div>
            </div>
            <div class="mv-slide">
                <img src="assets/mv/slide5.jpg" alt="">
                <div class="mv-slide-content">
                    <h3>Community Focus</h3>
                    <p>We ensure accessible clean water for households and businesses alike.</p>
                </div>
            </div>
        </div>
        <div class="mv-controls">
            <div class="mv-dot active"></div>
            <div class="mv-dot"></div>
            <div class="mv-dot"></div>
            <div class="mv-dot"></div>
            <div class="mv-dot"></div>
        </div>
        <div class="mv-arrow mv-arrow-left" onclick="mvPrev()">&#10094;</div>
        <div class="mv-arrow mv-arrow-right" onclick="mvNext()">&#10095;</div>
    </div>
</section>

<!-- VALUES -->
<section class="section">
    <h2>Our Values</h2>
    <div class="values">
        <div class="value-card">
            <div class="value-card-front"><h3>Honesty & Integrity</h3></div>
            <div class="value-card-back"><p>We uphold transparency and fairness in all dealings, ensuring trust in our community.</p></div>
        </div>
        <div class="value-card">
            <div class="value-card-front"><h3>Customer Service</h3></div>
            <div class="value-card-back"><p>We value our customers as partners and aim to provide exceptional service every time.</p></div>
        </div>
        <div class="value-card">
            <div class="value-card-front"><h3>Commitment to Excellence</h3></div>
            <div class="value-card-back"><p>Operational efficiency and innovation are at the core of everything we do.</p></div>
        </div>
        <div class="value-card">
            <div class="value-card-front"><h3>Teamwork</h3></div>
            <div class="value-card-back"><p>Collaboration drives our success and strengthens our organization.</p></div>
        </div>
        <div class="value-card">
            <div class="value-card-front"><h3>Love for Country</h3></div>
            <div class="value-card-back"><p>We actively contribute to national development and community progress.</p></div>
        </div>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> IWADCO. All Rights Reserved.
</footer>

<script>
// SCROLL REVEAL
const sections=document.querySelectorAll('section');
const revealOnScroll=()=>{const h=window.innerHeight;sections.forEach(s=>{const t=s.getBoundingClientRect().top;if(t<h-50){s.classList.add('visible');}});};
window.addEventListener('scroll',revealOnScroll); revealOnScroll();

// HERO SLIDESHOW
let slideIndex=0;
const slides=document.querySelectorAll('.slide');
const dots=document.querySelectorAll('.dot');
const slideTitle=document.getElementById('slide-title');
const slideText=document.getElementById('slide-text');
const slideContents=[
  {title:"Our Infrastructure", text:"State‑of‑the‑art water treatment and delivery systems."},
  {title:"Serving Communities", text:"Ensuring access to safe and clean water for all."},
  {title:"Dedicated Engineers", text:"Our team works tirelessly to maintain and improve service."},
  {title:"Sustainability in Action", text:"Commitment to eco‑friendly and efficient operations."},
  {title:"Clean Water Delivered", text:"Reliable water supply for households and businesses."}
];
function showSlide(n){slides.forEach((s,i)=>{s.classList.remove('active');dots[i].classList.remove('active');});slides[n].classList.add('active');dots[n].classList.add('active');slideTitle.textContent = slideContents[n].title;slideText.textContent = slideContents[n].text;}
function nextSlide(){slideIndex=(slideIndex+1)%slides.length;showSlide(slideIndex);}
function prevSlide(){slideIndex=(slideIndex-1+slides.length)%slides.length;showSlide(slideIndex);}
function currentSlide(n){slideIndex=n;showSlide(slideIndex);}
setInterval(nextSlide,5000);

// MISSION & VISION CAROUSEL
let mvIndex=0;
const mvTrack=document.querySelector('.mv-track');
const mvSlides=document.querySelectorAll('.mv-slide');
const mvDots=document.querySelectorAll('.mv-dot');
function updateMv(){ mvTrack.style.transform=`translateX(-${mvIndex*100}%)`; mvDots.forEach(d=>d.classList.remove('active')); mvDots[mvIndex].classList.add('active'); }
function mvNext(){ mvIndex=(mvIndex+1)%mvSlides.length; updateMv(); }
function mvPrev(){ mvIndex=(mvIndex-1+mvSlides.length)%mvSlides.length; updateMv(); }
mvDots.forEach((dot,i)=>{ dot.addEventListener('click',()=>{ mvIndex=i; updateMv(); }); });
let mvInterval = setInterval(mvNext, 5000);
const mvCarousel=document.getElementById('mv-carousel');
mvCarousel.addEventListener('mouseenter',()=>clearInterval(mvInterval));
mvCarousel.addEventListener('mouseleave',()=>mvInterval=setInterval(mvNext,5000));

// VALUES CARD FLIP EFFECT
const valueCards=document.querySelectorAll('.value-card');
valueCards.forEach(card=>{
  card.addEventListener('mouseenter',()=>card.style.transform='rotateY(180deg) rotateX(5deg)');
  card.addEventListener('mouseleave',()=>card.style.transform='rotateY(0deg) rotateX(0deg)');
});

// COMPANY STATS COUNT-UP
const stats=document.querySelectorAll('.stat');
stats.forEach(stat=>{
  const target=parseInt(stat.dataset.target);
  const h3=stat.querySelector('h3');
  let count=0;
  const increment=Math.ceil(target/100);
  const updateCount=()=>{
    if(count<target){
      count+=increment;
      if(count>target) count=target;
      h3.textContent=count;
      requestAnimationFrame(updateCount);
    } else { h3.textContent=target; }
  }
  // Trigger count when in view
  const observer=new IntersectionObserver(entries=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){ updateCount(); observer.disconnect(); }
    });
  }, {threshold:0.6});
  observer.observe(stat);
});
</script>

</body>
</html>
