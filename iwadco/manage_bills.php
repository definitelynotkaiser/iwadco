<?php
session_start();
include("db_connect.php");

// Redirect if not logged in or not admin
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? $_SESSION['username'];

// ------------------
// Handle actions: mark as paid or delete
// ------------------
if(isset($_GET['action'])){
    $bill_id = intval($_GET['id']);
    if($_GET['action'] == 'delete'){
        $conn->query("DELETE FROM bills WHERE id=$bill_id");
    } elseif($_GET['action'] == 'paid'){
        $conn->query("UPDATE bills SET status='Paid' WHERE id=$bill_id");
    }
}

// ------------------
// Fetch all bills
// ------------------
$result = $conn->query("
    SELECT bills.*, users.fullname 
    FROM bills 
    JOIN users ON bills.user_id = users.id
    ORDER BY bills.billing_month DESC
");

// Count totals for cards
$totalBills = $conn->query("SELECT COUNT(*) AS total FROM bills")->fetch_assoc()['total'];
$pendingBills = $conn->query("SELECT COUNT(*) AS total FROM bills WHERE status='Pending'")->fetch_assoc()['total'];
$paidBills = $conn->query("SELECT COUNT(*) AS total FROM bills WHERE status='Paid'")->fetch_assoc()['total'];
$overdueBills = $conn->query("SELECT COUNT(*) AS total FROM bills WHERE status='Overdue'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Customer Bills - IWADCO</title>
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
.card-container { display:flex; flex-wrap:wrap; gap:25px; margin-bottom:30px; }
.card { flex:1 1 220px; background:linear-gradient(135deg,#0077d1,#0056a6); color:#fff; border-radius:12px; padding:25px 20px; box-shadow:0 6px 18px rgba(0,0,0,0.15); position:relative; overflow:hidden; transition:0.3s; text-decoration:none; }
.card:hover { transform:translateY(-4px); }
.card i { font-size:1.8rem; margin-bottom:10px; }
.card h3 { margin-bottom:10px; font-size:1.1rem; user-select:none; }
.card p { font-size:0.9rem; user-select:none; }
.card small { display:block; font-size:0.8rem; opacity:0.9; margin-top:5px; }

table { width:100%; border-collapse:collapse; margin-top:20px; }
table th, table td { padding:10px; border:1px solid #ccc; text-align:left; font-size:14px; }
table th { background:#0077d1; color:#fff; }
table tr:nth-child(even){ background:#f2f2f2; }
table tr:hover { background:#dbe7f5; }

.status-Pending { color:#e67e22; font-weight:bold; }
.status-Paid { color:#27ae60; font-weight:bold; }
.status-Overdue { color:#c0392b; font-weight:bold; }

.action-btn, button { padding:5px 10px; margin-right:5px; border:none; border-radius:5px; cursor:pointer; color:#fff; text-decoration:none; user-select:none; -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; }
button:focus { outline:none; }
.action-paid { background:#27ae60; }
.action-delete { background:#c0392b; }

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
            <a href="admin.php">Dashboard</a>
            <a href="user.php">Users</a>
            <a href="manage_bills.php" class="active">Bills</a>
            <a href="payments.php">Payments</a>
            <a href="admin_applications.php">Water Applications</a>
            <a href="admin_service_requests.php">Service Requests</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php" style="font-weight:bold;">Logout</a>
        </nav>
    </div>
</header>

<div class="main-content">
    <h1 style="color:#0056a6; user-select:none;">Manage Customer Bills</h1>

    <div class="card-container">
        <div class="card">
            <i class="fas fa-file-invoice"></i>
            <h3>Total Bills</h3>
            <p><?php echo $totalBills; ?> bills</p>
        </div>
        <div class="card">
            <i class="fas fa-clock"></i>
            <h3>Pending Bills</h3>
            <p><?php echo $pendingBills; ?> bills</p>
        </div>
        <div class="card">
            <i class="fas fa-money-bill-wave"></i>
            <h3>Paid Bills</h3>
            <p><?php echo $paidBills; ?> bills</p>
        </div>
        <div class="card">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Overdue Bills</h3>
            <p><?php echo $overdueBills; ?> bills</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Billing Month</th>
                <th>Amount (₱)</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($bill = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $bill['id']; ?></td>
                    <td><?php echo htmlspecialchars($bill['fullname']); ?></td>
                    <td><?php echo date('F Y', strtotime($bill['billing_month'])); ?></td>
                    <td><?php echo number_format($bill['amount'],2); ?></td>
                    <td class="status-<?php echo $bill['status']; ?>"><?php echo $bill['status']; ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($bill['created_at'])); ?></td>
                    <td>
                        <?php if($bill['status'] != 'Paid'): ?>
                        <a href="?action=paid&id=<?php echo $bill['id']; ?>" class="action-btn action-paid">Mark Paid</a>
                        <?php endif; ?>
                        <a href="?action=delete&id=<?php echo $bill['id']; ?>" class="action-btn action-delete" onclick="return confirm('Are you sure to delete this bill?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<footer>
    &copy; 2025 IWADCO. All rights reserved.
</footer>

</body>
</html>
