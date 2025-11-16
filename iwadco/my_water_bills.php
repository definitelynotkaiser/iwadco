<?php
session_start();
include("db_connect.php");

// Redirect guest to registration page
if (!isset($_SESSION['username'])) {
    header("Location: registration.php");
    exit();
}

$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'] ?? '';

// 🔹 Get user ID from the users table
$user_query = $conn->prepare("SELECT id, fullname FROM users WHERE username = ?");
$user_query->bind_param("s", $username);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user = $user_result->fetch_assoc();
$user_id = $user['id'];
$fullname = $user['fullname'];

// 🔹 Fetch all bills for the logged-in user
$bill_query = $conn->prepare("
    SELECT * FROM billing 
    WHERE user_id = ? 
    ORDER BY billing_date DESC
");
$bill_query->bind_param("i", $user_id);
$bill_query->execute();
$bill_result = $bill_query->get_result();

$bills = [];
while ($row = $bill_result->fetch_assoc()) {
    $bills[] = $row;
}

// 🔹 Find next unpaid bill
$next_bill = null;
foreach ($bills as $b) {
    if (isset($b['status']) && strtolower($b['status']) === 'unpaid') {
        $next_bill = $b;
        break;
    }
}

// 🔹 Check if success flag is set from process_payment.php
$payment_success = $_SESSION['payment_success'] ?? false;
$payment_error = $_SESSION['payment_error'] ?? false;
// clear flags after reading
unset($_SESSION['payment_success'], $_SESSION['payment_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IWADCO | My Water Bill</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
<?php
// retain your entire CSS
if(file_exists(__DIR__ . "/my_water_bills_style.css")) {
    readfile(__DIR__ . "/my_water_bills_style.css");
}
?>
/* Global Button Fix */
button, .btn, .btn-pay {
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    cursor: pointer !important;
}
button:focus, .btn:focus, .btn-pay:focus {
    outline: none !important;
}
</style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
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
        </ul>
      </div>
      <a href="apply.php">Apply Now</a>
      <a href="my_water_bills.php" class="bill-btn"><span>MY WATER BILL</span></a>
      <a href="logout.php" style="color:#0056a6; font-weight:bold;">Logout</a>
    </nav>
  </div>
</header>

<!-- ===== MAIN CONTENT ===== -->
<div class="main">

  <!-- REAL-TIME BALANCE CARD -->
  <div class="balance-box">
    <h3>Your Current Balance</h3>
    <p id="balance-amount">₱0.00</p>
    <p id="balance-status" class="text-success">Fetching balance...</p>
  </div>

  <!-- PAYMENT FORM -->
  <div class="pay-card">
    <?php if ($next_bill): ?>
      <h2>Amount Due</h2>
      <?php
        $amount = isset($next_bill['amount_due']) ? (float)$next_bill['amount_due'] : 0.00;
        $period = isset($next_bill['billing_month']) ? htmlspecialchars($next_bill['billing_month']) : 'N/A';
        $duedate = isset($next_bill['due_date']) ? htmlspecialchars($next_bill['due_date']) : 'N/A';
      ?>
      <div class="amount">₱ <?= number_format($amount, 2) ?></div>
      <p>Bill Period: <strong><?= $period ?></strong></p>
      <p>Due Date: <strong><?= $duedate ?></strong></p>

      <form method="post" action="process_payment.php">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">
        <input type="hidden" name="billing_id" value="<?= $next_bill['id'] ?>">
        <div class="form-group">
          <label for="paymentMethod">Payment Method</label>
          <select class="form-control" id="paymentMethod" name="method" required>
            <option value="">Choose...</option>
            <option value="gcash">GCash</option>
            <option value="paymaya">PayMaya</option>
          </select>
        </div>

        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="tel" class="form-control" id="phone" name="phone" placeholder="09XXXXXXXXX" required>
        </div>

        <div class="form-group">
          <label for="email">Email (Receipt)</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com">
        </div>

        <button type="submit" class="btn-pay">Proceed to Pay</button>
      </form>
    <?php else: ?>
      <h2>No Outstanding Bills</h2>
      <p>You’re all caught up! ✅</p>
    <?php endif; ?>
  </div>
</div>

<div class="footer">&copy; <?= date('Y') ?> IWADCO. All rights reserved.</div>

<!-- ===== POPUP NOTIFICATIONS ===== -->
<div class="toast-popup success" id="successToast">
  <img src="iwadco/assets/icons/check.png" alt="Success">
  <p>Payment Successful! Your bill has been updated.</p>
</div>

<div class="toast-popup error" id="errorToast">
  <img src="iwadco/assets/icons/error.png" alt="Error">
  <p>Payment failed. Please try again later.</p>
</div>

<script>
// Dropdown hover
const dropdown = document.querySelector('.dropdown');
const menu = document.querySelector('.dropdown-menu');
let hideTimeout;
dropdown.addEventListener('mouseenter', () => { clearTimeout(hideTimeout); menu.style.display='block'; });
dropdown.addEventListener('mouseleave', () => { hideTimeout=setTimeout(()=>{menu.style.display='none';},300); });

// === Show Toast Notifications ===
function showToast(id) {
  const toast = document.getElementById(id);
  if (!toast) return;
  toast.classList.add('show');
  setTimeout(()=>{ toast.classList.remove('show'); }, 4000);
}
<?php if ($payment_success): ?> showToast('successToast'); <?php endif; ?>
<?php if ($payment_error): ?> showToast('errorToast'); <?php endif; ?>

// === Real-time Balance Auto-Refresh ===
function fetchBalance() {
  fetch('fetch_balance.php')
    .then(res => res.json())
    .then(data => {
      const amount = document.getElementById('balance-amount');
      const status = document.getElementById('balance-status');
      const bal = (data && typeof data.balance === 'number') ? data.balance : 0;
      amount.textContent = `₱${bal.toFixed(2)}`;
      if (bal > 0) {
        status.textContent = "You still have unpaid balance.";
        status.className = "text-danger";
      } else {
        status.textContent = "No Outstanding Balance";
        status.className = "text-success";
      }
    })
    .catch(err => console.error('Error fetching balance:', err));
}
fetchBalance();
setInterval(fetchBalance, 5000);
</script>
</body>
</html>
