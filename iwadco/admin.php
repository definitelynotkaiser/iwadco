<?php
session_start();
include("db_connect.php");

// Redirect if not logged in or not admin
if(!isset($_SESSION['username']) || $_SESSION['role']!='admin'){
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? $_SESSION['username'];

// TOTAL USERS
$totalUsers = $conn->query("SELECT COUNT(id) AS total FROM users")->fetch_assoc()['total'];

// PENDING BILLS
$pendingBills = $conn->query("SELECT COUNT(*) AS total FROM bills")->fetch_assoc()['total'];

// PAYMENTS TODAY
$paymentsToday = $conn->query("SELECT SUM(amount) AS total FROM bills")->fetch_assoc()['total'];

// DISCONNECTED ACCOUNTS
$disconnectedAccounts = 0;
if($conn->query("SHOW TABLES LIKE 'users'")->num_rows == 1) {
    $disconnectedAccounts = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
}

// SERVICE REQUESTS
$totalRequests = $pendingRequests = $resolvedRequests = 0;
if ($conn->query("SHOW TABLES LIKE 'service_requests'")->num_rows == 1) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM service_requests");
    $data = $result->fetch_assoc();
    $totalRequests = $data['total'] ?? 0;
    $pendingRequests = 0;  // Status tracking removed
    $resolvedRequests = 0; // Status tracking removed
}

// WATER CONNECTION APPLICATIONS
$totalApplications = $pendingApplications = $approvedApplications = 0;
if ($conn->query("SHOW TABLES LIKE 'applications'")->num_rows == 1) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM applications");
    $data = $result->fetch_assoc();
    $totalApplications = $data['total'] ?? 0;
    $pendingApplications = 0;  // Status tracking removed
    $approvedApplications = 0; // Status tracking removed

    // Fetch all applications for table
    $applicationsResult = $conn->query("SELECT * FROM applications ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - IWADCO</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
html, body { height:100%; }
body { display:flex; flex-direction:column; background-color:#f4f6f9; color:#333; }
header { background-color:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); position:fixed; top:0; width:100%; z-index:1000; }
.navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 50px; flex-wrap:wrap; }
.navbar .logo { font-size:1.6rem; font-weight:bold; color:#0056a6; cursor:default; }
nav { display:flex; align-items:center; gap:25px; flex-wrap:wrap; }
nav a { text-decoration:none; color:#0056a6; font-weight:500; font-size:14px; padding:6px 10px; border-radius:5px; transition:0.3s; }
nav a:hover { background:#e1efff; color:#0077d1; }
nav a.active { background:#e1efff; color:#0077d1; }

.main-content { flex:1; padding:130px 40px 40px 40px; }
.card-container { display:flex; flex-wrap:wrap; gap:25px; margin-top:20px; }
.card { flex:1 1 220px; background:linear-gradient(135deg,#0077d1,#0056a6); color:#fff; border-radius:12px; padding:25px 20px; box-shadow:0 6px 18px rgba(0,0,0,0.15); position:relative; overflow:hidden; transition:0.3s; cursor:pointer; text-decoration:none; }
.card:hover { transform:translateY(-4px); }
.card i { font-size:1.8rem; margin-bottom:10px; }
.card h3 { margin-bottom:10px; font-size:1.1rem; user-select:none; }
.card p { font-size:0.9rem; user-select:none; }
.card small { display:block; font-size:0.8rem; opacity:0.9; margin-top:5px; }

table { width:100%; border-collapse:collapse; margin-top:40px; }
table th, table td { padding:10px; border:1px solid #ccc; text-align:left; font-size:14px; }
table th { background:#0077d1; color:#fff; }
table tr:nth-child(even) { background:#f2f2f2; }
table tr:hover { background:#dbe7f5; }

footer { background:#0056a6; color:#fff; text-align:center; padding:15px; font-size:0.9rem; margin-top:auto; }

@media(max-width:992px){ .card-container{justify-content:center;} nav{gap:15px;} }
@media(max-width:768px){ .main-content{padding:160px 20px 20px 20px;} .navbar{padding:15px 20px;} }
</style>
</head>
<body>

<header>
    <div class="navbar">
        <div class="logo">IWADCO Admin</div>
        <nav>
            <a href="admin.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin.php'?'active':''; ?>">Dashboard</a>
            <a href="user.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='user.php'?'active':''; ?>">Users</a>
            <a href="bill.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='bill.php'?'active':''; ?>">Bills</a>
            <a href="payments.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='payments.php'?'active':''; ?>">Payments</a>
            <a href="admin_applications.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_applications.php'?'active':''; ?>">Water Applications</a>
            <a href="admin_service_requests.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_service_requests.php'?'active':''; ?>">Service Requests</a>
            <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='settings.php'?'active':''; ?>">Settings</a>
            <a href="logout.php" style="font-weight:bold;">Logout</a>
        </nav>
    </div>
</header>

<div class="main-content">
    <h1 style="color:#0056a6; user-select:none;">Welcome, <?php echo htmlspecialchars($fullname); ?>!</h1>

    <div class="card-container">
        <div class="card">
            <i class="fas fa-users"></i>
            <h3>Total Users</h3>
            <p><?php echo $totalUsers; ?> Users registered</p>
        </div>

        <div class="card">
            <i class="fas fa-file-invoice"></i>
            <h3>Pending Bills</h3>
            <p><?php echo $pendingBills; ?> bills are pending</p>
        </div>

        <div class="card">
            <i class="fas fa-money-bill-wave"></i>
            <h3>Payments Today</h3>
            <p>₱<?php echo number_format($paymentsToday,2); ?> collected</p>
        </div>

        <div class="card">
            <i class="fas fa-user-slash"></i>
            <h3>Disconnected Accounts</h3>
            <p><?php echo $disconnectedAccounts; ?> accounts disconnected</p>
        </div>

        <a href="admin_service_requests.php" class="card">
            <i class="fas fa-headset"></i>
            <h3>Service Requests</h3>
            <p><?php echo $totalRequests; ?> total requests</p>
            <small>🕒 <?php echo $pendingRequests; ?> Pending | ✅ <?php echo $resolvedRequests; ?> Resolved</small>
        </a>

        <a href="admin_applications.php" class="card">
            <i class="fas fa-water"></i>
            <h3>Water Applications</h3>
            <p><?php echo $totalApplications; ?> total applications</p>
            <small>🕒 <?php echo $pendingApplications; ?> Pending | ✅ <?php echo $approvedApplications; ?> Approved</small>
        </a>
    </div>
</div>

<footer>
    &copy; 2025 IWADCO. All rights reserved.
</footer>

</body>
</html>
