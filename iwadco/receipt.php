<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 'user';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Water Bill Receipt - IWADCO</title>
<style>
* {margin:0; padding:0; box-sizing:border-box;}
body {font-family: 'Segoe UI', Arial, sans-serif; background:#f4f7fb; color:#1a1a1a; min-height:100vh; display:flex; flex-direction:column;}
a {text-decoration:none; color:inherit;}

/* ===== HEADER ===== */
.site-header {background:#ffffff; border-bottom:1px solid #e5e7eb; padding:15px 0; box-shadow:0 2px 6px rgba(0,0,0,0.05);}
.header-inner {width:90%; max-width:1200px; margin:auto; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;}
.logo {font-size:22px; font-weight:700; color:#0077cc; cursor:pointer;}
.main-nav {list-style:none; display:flex; justify-content:center; flex-wrap:wrap; gap:22px; position:relative;}
.main-nav li {position:relative;}
.main-nav a {font-size:15px; font-weight:500; color:#1a1a1a; padding:6px 10px; border-bottom:2px solid transparent; transition:0.3s;}
.main-nav a:hover, .main-nav a.active {color:#0077cc; border-bottom:2px solid #0077cc;}
.main-nav .login {color:#0077cc; font-weight:bold;}
.dropdown {position:relative;}
.dropdown-content {display:none; position:absolute; top:32px; left:0; background:#fff; min-width:180px; box-shadow:0 4px 8px rgba(0,0,0,0.15); border-radius:6px; z-index:10;}
.dropdown-content a {display:block; padding:10px 14px; color:#1a1a1a; border-bottom:1px solid #f0f0f0; transition:0.3s;}
.dropdown-content a:hover {background:#f5f8fb; color:#0077cc;}
.dropdown:hover .dropdown-content {display:block;}

/* ===== MAIN CONTENT ===== */
.container {width:90%; max-width:1000px; margin:30px auto; flex:1;}
.page-title {font-size:28px; font-weight:700; margin-bottom:10px;}
.subtitle {color:#555; margin-bottom:20px;}
.card {background:white; border-radius:10px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.06); margin-bottom:20px;}

/* BILL INFO */
.bill-info {display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px;}
.info-box {background:#f0f4f8; border-radius:10px; padding:12px; text-align:center;}
.info-box h3 {margin:6px 0 4px; font-size:20px; color:#0077cc;}
.info-box p {margin:0; font-size:13px; color:#555;}
.status-paid {color:#10b981; font-weight:bold;}
.status-unpaid {color:#ef4444; font-weight:bold;}

/* TABLE */
.table-container {overflow-x:auto;}
table {width:100%; border-collapse:collapse; margin-top:12px;}
th, td {padding:12px 10px; text-align:left; border-bottom:1px solid #e5e7eb; font-size:14px;}
th {color:#555; font-weight:600; font-size:12px; text-transform:uppercase;}
.center{text-align:center;}

/* FOOTER - STICKY */
.site-footer {background:#0e3b66; color:#fff; padding:40px 20px 10px; margin-top:auto;}
.footer-inner {display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px;}
.footer-inner h4 {margin-bottom:8px;}
.footer-inner ul {list-style:none;}
.footer-inner a {color:#aee1ff;}
.logos img {height:30px; margin-right:6px;}
.copyright {text-align:center; display:block; padding-top:15px; opacity:0.8;}

@media(max-width:600px){.main-nav {gap:12px; font-size:14px;}}
</style>
</head>
<body>

<header class="site-header">
<div class="header-inner">
  <div class="logo" onclick="window.location.href='home.php'">IWADCO</div>
  <ul class="main-nav">
    <li><a href="home.php">Home</a></li>
    <li><a href="apply.php">Apply for Connection</a></li>
    <li class="dropdown">
      <a href="payment.php">Pay Bills ▾</a>
      <div class="dropdown-content">
        <a href="balance.php">Balance</a>
        <a href="billing_reminder.php">Billing Reminder</a>
      </div>
    </li>
    <li><a href="history.php">Billing History</a></li>
    <li><a href="about.php">About Us</a></li>
    <li><a href="logout.php" class="login">Logout</a></li>
  </ul>
</div>
</header>

<main class="container">
  <h1 class="page-title">Your Water Bill Receipt</h1>
  <p class="subtitle">View your current bill and past payment records.</p>

  <section class="card">
    <h2>Current Bill</h2>
    <div class="bill-info" id="currentBill">
      <div class="info-box">
        <p>Billing Month</p>
        <h3 id="billMonth">October 2025</h3>
      </div>
      <div class="info-box">
        <p>Amount Due</p>
        <h3 id="amountDue">₱0.00</h3>
      </div>
      <div class="info-box">
        <p>Due Date</p>
        <h3 id="dueDate">--</h3>
      </div>
      <div class="info-box">
        <p>Status</p>
        <h3 id="billStatus" class="status-unpaid">UNPAID</h3>
      </div>
    </div>
  </section>

  <section class="card">
    <h2>Past Bills</h2>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Month</th>
            <th>Amount</th>
            <th>Due Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="historyBody">
          <!-- populated by JS -->
        </tbody>
      </table>
    </div>
  </section>
</main>

<footer class="site-footer">
<div class="footer-inner">
  <div>
    <h4>IWADCO</h4>
    <p>Providing clean water and excellent service.</p>
  </div>
  <div>
    <h4>Quick Links</h4>
    <ul>
      <li><a href="payment.php">Online Payments</a></li>
      <li><a href="history.php">Billing History</a></li>
      <li><a href="apply.php">Apply for Connection</a></li>
    </ul>
  </div>
  <div>
    <h4>Payment Partners</h4>
    <div class="logos">
      <img src="assets/logos/gcash.png" alt="GCash">
      <img src="assets/logos/paymaya.png" alt="PayMaya">
    </div>
  </div>
</div>
<div class="copyright">© IWADCO — All rights reserved.</div>
</footer>

<script>
const userBillData = {
  name: "<?php echo $username; ?>",
  currentBill: {month:"October 2025", amount:520.75, due:"2025-11-10", paid:false},
  history:[
    {month:"September 2025", amount:495.25, due:"2025-10-10", paid:true},
    {month:"August 2025", amount:510.00, due:"2025-09-10", paid:true},
    {month:"July 2025", amount:480.50, due:"2025-08-10", paid:true}
  ]
};

// Display current bill
document.getElementById("billMonth").textContent = userBillData.currentBill.month;
document.getElementById("amountDue").textContent = "₱" + userBillData.currentBill.amount.toFixed(2);
document.getElementById("dueDate").textContent = userBillData.currentBill.due;
const status = document.getElementById("billStatus");
if(userBillData.currentBill.paid){
  status.textContent="PAID";
  status.classList.replace("status-unpaid","status-paid");
} else {
  status.textContent="UNPAID";
  status.classList.replace("status-paid","status-unpaid");
}

// Fill history
const tbody = document.getElementById("historyBody");
userBillData.history.forEach(b=>{
  const tr = document.createElement("tr");
  tr.innerHTML = `<td>${b.month}</td>
                  <td>₱${b.amount.toFixed(2)}</td>
                  <td>${b.due}</td>
                  <td class="${b.paid?'status-paid':'status-unpaid'}">${b.paid?'PAID':'UNPAID'}</td>`;
  tbody.appendChild(tr);
});
</script>
</body>
</html>
