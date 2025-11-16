<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Disconnection Notice - IWADCO</title>
  <link rel="stylesheet" href="disco.css">
</head>
<body>
  <!-- HEADER -->
  <header class="site-header">
    <div class="header-inner">
      <div class="logo">IWADCO</div>

      <div class="hamburger" onclick="document.body.classList.toggle('nav-open')">☰</div>

      <nav class="main-nav">
        <a href="home.php">Home</a>
        <div class="nav-dropdown">
          <a href="payment.php">Online Payments</a>
          <div class="dropdown-menu">
            <a href="payment.php">My Bills</a>
          </div>
        </div>

        <div class="nav-dropdown">
          <a href="#">Contact</a>
          <div class="dropdown-menu">
            <a href="about.php">About Us</a>
          </div>
        </div>

        <a href="login.php" class="login">Login</a>
      </nav>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="container">
    <h1 class="page-title">Disconnection Notice</h1>
    <p class="subtitle">Please review your account status and settle your outstanding bills to avoid service interruption.</p>

    <div class="notice-card">
      <h2>Account: IW-000123</h2>
      <p><strong>Notice Date:</strong> October 26, 2025</p>
      <p><strong>Due Date for Payment:</strong> November 10, 2025</p>
      <p><strong>Outstanding Balance:</strong> ₱1,000.00</p>
      <p><strong>Status:</strong> Pending Payment</p>
      <p>To prevent service disconnection, please pay your outstanding balance before the due date.</p>

      <a href="payments.html" class="btn-pay">Pay Now</a>
    </div>

    <section class="info-section">
      <h3>Important Information</h3>
      <ul>
        <li>Disconnection will occur if the outstanding balance is not settled by the due date.</li>
        <li>You may contact our support team for assistance at any time.</li>
        <li>Regular payments ensure uninterrupted water supply and service reliability.</li>
      </ul>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="site-footer">
    <div class="footer-inner">
      <div class="col">
        <h4>IWADCO</h4>
        <p>Providing clean water and excellent service.</p>
      </div>
      <div class="col">
        <h4>Quick links</h4>
        <ul>
          <li><a href="payment.html">Online Payments</a></li>
          <li><a href="bills.html">My Bills</a></li>
          <li><a href="apply.html">Apply for Connection</a></li>
        </ul>
      </div>
      <div class="col">
        <h4>Payment partners</h4>
        <div class="logos">
          <img src="assets/logos/gcash.svg" alt="GCash">
          <img src="assets/logos/paymaya.svg" alt="PayMaya">
        </div>
      </div>
    </div>
    <div class="copyright">© IWADCO — All rights reserved.</div>
  </footer>

  <!-- MOBILE DROPDOWN SCRIPT -->
  <script>
    document.querySelectorAll('.nav-dropdown > a').forEach(drop => {
      drop.addEventListener('click', function(e){
        if(window.innerWidth <= 700){
          e.preventDefault();
          this.parentElement.classList.toggle('active');
        }
      });
    });
  </script>
</body>
</html>
