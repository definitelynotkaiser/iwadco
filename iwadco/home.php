<?php
session_start();

// Check if user is logged in (either from login or successful registration)
$hasAccount = isset($_SESSION['username']) && !empty($_SESSION['username']);

if ($hasAccount) {
    $username = $_SESSION['username'];
    $fullname = isset($_SESSION['fullname']) && !empty($_SESSION['fullname']) ? $_SESSION['fullname'] : $username;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>IWADCO | Home</title>
<style>
/* ===== BASIC RESET ===== */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { background-color: #f5f8fa; color: #333; overflow-x: hidden; }

/* ===== NAVIGATION BAR ===== */
header { background-color: #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); position: fixed; top: 0; left: 0; width: 100%; z-index: 999; }
.navbar { display: flex; justify-content: center; align-items: center; padding: 15px 60px; gap: 50px; flex-wrap: wrap; }
.navbar .logo { font-size: 1.6rem; font-weight: bold; color: #0056a6; letter-spacing: 1px; cursor: pointer; margin-right: 50px; }
nav { display: flex; align-items: center; gap: 25px; position: relative; flex-wrap: wrap; }
nav a, nav span { text-decoration: none; color: #0056a6; font-weight: 500; transition: color 0.3s; font-size: 15px; padding: 6px 10px; cursor: pointer; }
nav a:hover, nav span:hover { color: #0077d1; }

/* ===== DROPDOWN ===== */
.dropdown { position: relative; display: inline-block; }
.dropdown-toggle { user-select: none; }
.dropdown-menu { display: none; position: absolute; top: 120%; left: 0; background: rgba(255,255,255,0.95); border-radius: 6px; list-style: none; padding: 10px 0; min-width: 180px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); z-index: 9999; }
.dropdown-menu li a { padding: 10px 16px; color: #0056a6; font-weight: 500; display: block; text-decoration: none; transition: background 0.3s; }
.dropdown-menu li a:hover { background: rgba(0,0,0,0.05); color: #0077d1; }

/* ===== MY WATER BILL / REGISTER BUTTON ===== */
.bill-btn { display: inline-flex; align-items: center; justify-content: center; background-color: #0056a6; color: #ffffff !important; padding: 10px 25px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 15px; letter-spacing: 0.5px; transition: background-color 0.3s, transform 0.2s; border: none; height: 36px; min-width: 150px; }
.bill-btn span { color: #ffffff; }
.bill-btn:hover { background-color: #0077d1; transform: scale(1.05); }

/* ===== GREETING ===== */
.greeting-top { position: fixed; top: 80px; left: 50%; transform: translateX(-50%); background-color: #0077d1; color: #fff; padding: 10px 25px; border-radius: 6px; font-weight: bold; box-shadow: 0 2px 8px rgba(0,0,0,0.2); z-index: 9999; opacity: 1; transition: opacity 0.5s ease; }

/* ===== SLIDESHOW ===== */
.slideshow { margin-top: 80px; position: relative; height: 75vh; overflow: hidden; }
.slide { position: absolute; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1s ease-in-out; }
.slide.active { opacity: 1; }
.slide-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.35); display: flex; align-items: center; justify-content: center; flex-direction: column; color: white; text-align: center; padding: 20px; }
.slide-overlay h1 { font-size: 2.8rem; margin-bottom: 15px; }
.slide-overlay p { font-size: 1.2rem; max-width: 600px; }

/* ===== NEWS SECTION ===== */
.news { padding: 70px 60px; background-color: #ffffff; text-align: center; }
.news h2 { color: #0056a6; margin-bottom: 40px; font-size: 2rem; }
.news-cards { display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; }
.news-card { background-color: #f0f8ff; border-radius: 8px; width: 300px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; text-align: left; transition: transform 0.3s; }
.news-card:hover { transform: translateY(-5px); }
.news-card img { width: 100%; height: 180px; object-fit: cover; }
.news-card h3 { font-size: 1.2rem; color: #0056a6; padding: 15px; }
.news-card p { padding: 0 15px 15px; color: #333; font-size: 0.95rem; }

/* ===== CONNECT SECTION ===== */
.connect { display: flex; justify-content: space-between; align-items: center; background-color: #e8f4fb; padding: 70px 60px; gap: 40px; flex-wrap: wrap; }
.connect-text { flex: 1; min-width: 260px; }
.connect-text h2 { font-size: 2rem; color: #0056a6; border-left: 4px solid #0056a6; padding-left: 10px; }
.connect-text p { margin-top: 15px; color: #333; }
.connect-imgs { display: flex; flex-wrap: wrap; gap: 10px; flex: 1; justify-content: flex-end; }
.connect-imgs img { width: 48%; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); }

/* ===== FOOTER ===== */
footer { background-color: #0056a6; color: white; text-align: center; padding: 15px; font-size: 0.9rem; }

@media (max-width: 768px) {
  .navbar { flex-direction: column; gap: 10px; padding: 15px; }
  nav { flex-wrap: wrap; justify-content: center; }
  .dropdown-menu { position: static; min-width: auto; box-shadow: none; background: none; }
}
</style>
</head>
<body>

<!-- ===== GREETING (TEMPORARY) ===== -->
<?php if ($hasAccount): ?>
<div id="greeting-top" class="greeting-top">
  Welcome back, <?php echo htmlspecialchars($fullname); ?>!
</div>
<?php endif; ?>

<!-- ===== NAVIGATION BAR ===== -->
<header>
  <div class="navbar">
    <div class="logo" onclick="window.location.href='home.php'">IWADCO</div>
    <nav>
      <a href="home.php">Home</a>
      <a href="about.php">About Us</a>

      <div class="dropdown">
        <span class="dropdown-toggle">Services ▼</span>
        <ul class="dropdown-menu">
          <li><a href="customer_service.php">Customer Service</a></li>
        </ul>
      </div>

      <a href="apply.php">Apply Now</a>

      <!-- CONDITIONAL BUTTONS -->
      <?php if ($hasAccount): ?>
        <a href="my_water_bills.php" class="bill-btn"><span>MY WATER BILL</span></a>
        <a href="logout.php" style="color:#0056a6; font-weight:bold;">Logout</a>
      <?php else: ?>
        <a href="registration.php" class="bill-btn"><span>REGISTER NOW</span></a>
        <a href="login.php" style="color:#0056a6; font-weight:bold;">Log in</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- ===== SLIDESHOW ===== -->
<div class="slideshow">
  <div class="slide active" style="background-image: url('assets/slideshow/water.jpg');">
    <div class="slide-overlay">
      <h1>Clean. Reliable. Local.</h1>
      <p>Providing safe and dependable water services to our community.</p>
    </div>
  </div>
  <div class="slide" style="background-image: url('assets/slideshow/water2.jpg');">
    <div class="slide-overlay">
      <h1>Committed to Service</h1>
      <p>Ensuring continuous water supply through innovation and care.</p>
    </div>
  </div>
  <div class="slide" style="background-image: url('assets/slideshow/waterr.jpg');">
    <div class="slide-overlay">
      <h1>Together for a Better Tomorrow</h1>
      <p>Working hand in hand for sustainable water development.</p>
    </div>
  </div>
</div>

<!-- ===== NEWS SECTION ===== -->
<section class="news">
  <h2>Latest News & Advisories</h2>
  <div class="news-cards">
    <div class="news-card">
      <img src="assets/slideshow/water2.jpg" alt="">
      <h3>Pipeline Fix in Zambales</h3>
      <p>IWADCO continues improving service reliability through mainline rehabilitation.</p>
    </div>
    <div class="news-card">
      <img src="assets/slideshow/water.jpg" alt="">
      <h3>Water Quality Assurance</h3>
      <p>Our laboratory teams ensure every drop meets national safety standards.</p>
    </div>
    <div class="news-card">
      <img src="assets/slideshow/water4.jpg" alt="">
      <h3>Community Outreach</h3>
      <p>Providing access to clean water for remote communities.</p>
    </div>
  </div>
</section>

<!-- ===== CONNECT SECTION ===== -->
<section class="connect">
  <div class="connect-text">
    <h2>CONNECT WITH US</h2>
    <p>Stay informed by following us on our official social channels.</p>
  </div>
  <div class="connect-imgs">
    <img src="assets/slideshow/water7.jpg" alt="">
    <img src="assets/slideshow/water8.jpg" alt="">
    <img src="assets/slideshow/water9.jpg" alt="">
    <img src="assets/slideshow/water10.jpg" alt="">
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer>
  &copy; 2025 IWADCO. All rights reserved.
</footer>

<!-- ===== JS ===== -->
<script>
// Slideshow
let slides = document.querySelectorAll('.slide');
let index = 0;
function nextSlide() {
  slides[index].classList.remove('active');
  index = (index + 1) % slides.length;
  slides[index].classList.add('active');
}
setInterval(nextSlide, 3000);

// Dropdown hover
const dropdown = document.querySelector('.dropdown');
const menu = dropdown.querySelector('.dropdown-menu');
dropdown.addEventListener('mouseenter', () => { menu.style.display = 'block'; });
dropdown.addEventListener('mouseleave', () => { menu.style.display = 'none'; });

// Auto-hide greeting after 5 seconds
const greeting = document.getElementById('greeting-top');
if (greeting) { setTimeout(() => { greeting.style.opacity = '0'; }, 5000); }
</script>

</body>
</html>
