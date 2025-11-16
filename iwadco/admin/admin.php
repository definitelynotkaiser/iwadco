<?php
session_start();
include('db_connect.php'); // your database connection

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? $_SESSION['username'];

// Determine current page from query string
$page = $_GET['page'] ?? 'dashboard';

// --- DASHBOARD DATA ---
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$pendingBills = $conn->query("SELECT COUNT(*) AS count FROM bills WHERE status='Pending'")->fetch_assoc()['count'];
$todayPayments = $conn->query("SELECT SUM(amount) AS total FROM payments WHERE DATE(date)=CURDATE()")->fetch_assoc()['total'] ?? 0;
$disconnectedAccounts = $conn->query("SELECT COUNT(*) AS count FROM users WHERE status='Disconnected'")->fetch_assoc()['count'];

// --- USERS DATA ---
$usersResult = $conn->query("SELECT * FROM users ORDER BY id ASC");

// --- BILLS DATA ---
$billsResult = $conn->query("SELECT b.id, u.fullname, b.amount, b.due_date, b.status 
                             FROM bills b 
                             JOIN users u ON b.user_id = u.id 
                             ORDER BY b.id ASC");

// --- PAYMENTS DATA ---
$paymentsResult = $conn->query("SELECT p.id, u.fullname, p.amount, p.date 
                                FROM payments p 
                                JOIN users u ON p.user_id = u.id 
                                ORDER BY p.date DESC");

// --- SETTINGS DATA ---
$error = $success = "";
if(isset($_POST['change_password'])){
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Verify current password
    $userId = $_SESSION['id'];
    $row = $conn->query("SELECT password FROM users WHERE id='$userId'")->fetch_assoc();
    if(password_verify($current, $row['password'])){
        if($new === $confirm){
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$hashed' WHERE id='$userId'");
            $success = "Password changed successfully!";
        } else { $error = "New password and confirm password do not match."; }
    } else { $error = "Current password is incorrect."; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IWADCO Admin Panel</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
html, body { height:100%; }
body { display:flex; flex-direction:column; background-color:#f4f6f9; color:#333; }
header { background-color:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); position:fixed; top:0; width:100%; z-index:1000; }
.navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 50px; flex-wrap:wrap; }
.navbar .logo { font-size:1.6rem; font-weight:bold; color:#0056a6; cursor:pointer; }
nav { display:flex; align-items:center; gap:25px; flex-wrap:wrap; }
nav a { text-decoration:none; color:#0056a6; font-weight:500; font-size:14px; padding:6px 10px; transition:all 0.3s; border-radius:5px; }
nav a:hover, nav a.active { background:#e1efff; color:#0077d1; }
.main-content { flex:1; padding:130px 40px 40px 40px; }
.card-container { display:flex; flex-wrap:wrap; gap:25px; margin-top:20px; }
.card { flex:1 1 200px; background:linear-gradient(135deg,#0077d1,#0056a6); color:#fff; border-radius:12px; padding:25px 20px; box-shadow:0 6px 18px rgba(0,0,0,0.15); transition:transform 0.3s, box-shadow 0.3s; position:relative; overflow:hidden; cursor:pointer; }
.card i { font-size:1.8rem; margin-bottom:10px; }
.card h3 { margin-bottom:10px; font-size:1.1rem; }
.card p { font-size:0.9rem; }
.card:hover { transform:translateY(-5px); box-shadow:0 12px 25px rgba(0,0,0,0.25); }
table { width:100%; border-collapse:collapse; margin-top:25px; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 6px 15px rgba(0,0,0,0.05); }
table th, table td { padding:12px 15px; text-align:left; }
table th { background:#0056a6; color:#fff; font-weight:500; position:sticky; top:0; }
table tbody tr { border-bottom:1px solid #eee; transition:background 0.3s; }
table tbody tr:hover { background:#f1f8ff; }
table td .btn { margin-right:5px; }
.btn, button { padding:6px 12px; background:#0056a6; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:13px; transition:background 0.3s; text-decoration:none; user-select:none; -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; }
.btn:hover, button:hover { background:#0077d1; }
button:focus { outline:none; }
footer { background:#0056a6; color:#fff; text-align:center; padding:15px; font-size:0.9rem; margin-top:auto; }
.alert { padding:10px; border-radius:5px; margin:10px 0; }
.alert-success { background:#d4edda; color:#155724; }
.alert-error { background:#f8d7da; color:#721c24; }
@media(max-width:992px){ .card-container{justify-content:center;} nav{gap:15px;} }
@media(max-width:768px){ .main-content{padding:160px 20px 20px 20px;} .navbar{padding:15px 20px;} }
</style>
</head>
<body>

<header>
    <div class="navbar">
        <div class="logo" onclick="window.location.href='admin.php?page=dashboard'">IWADCO Admin</div>
        <nav>
            <a href="admin.php?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>">Dashboard</a>
            <a href="admin.php?page=users" class="<?= $page=='users'?'active':'' ?>">Users</a>
            <a href="admin.php?page=bills" class="<?= $page=='bills'?'active':'' ?>">Bills</a>
            <a href="admin.php?page=payments" class="<?= $page=='payments'?'active':'' ?>">Payments</a>
            <a href="admin.php?page=settings" class="<?= $page=='settings'?'active':'' ?>">Settings</a>
            <a href="logout.php" style="font-weight:bold;">Logout</a>
        </nav>
    </div>
</header>

<div class="main-content">

<?php if($page=='dashboard'): ?>
<h1 style="color:#0056a6;">Welcome, <?= htmlspecialchars($fullname) ?>!</h1>
<div class="card-container">
    <div class="card" onclick="window.location.href='admin.php?page=users'">
        <i class="fas fa-users"></i>
        <h3>Total Users</h3>
        <p><?= $totalUsers ?> Users registered</p>
    </div>
    <div class="card" onclick="window.location.href='admin.php?page=bills'">
        <i class="fas fa-file-invoice"></i>
        <h3>Pending Bills</h3>
        <p><?= $pendingBills ?> bills are pending</p>
    </div>
    <div class="card" onclick="window.location.href='admin.php?page=payments'">
        <i class="fas fa-money-bill-wave"></i>
        <h3>Payments Today</h3>
        <p>₱<?= number_format($todayPayments,2) ?> collected</p>
    </div>
    <div class="card" onclick="window.location.href='admin.php?page=users&filter=disconnected'">
        <i class="fas fa-user-slash"></i>
        <h3>Disconnected Accounts</h3>
        <p><?= $disconnectedAccounts ?> accounts disconnected</p>
    </div>
</div>

<?php elseif($page=='users'): ?>
<h2>Users Management</h2>
<a href="add_user.php" class="btn">Add New User</a>
<table>
<thead><tr><th>ID</th><th>Fullname</th><th>Username</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
<tbody>
<?php while($user=$usersResult->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($user['id']) ?></td>
<td><?= htmlspecialchars($user['fullname']) ?></td>
<td><?= htmlspecialchars($user['username']) ?></td>
<td><?= htmlspecialchars($user['role']) ?></td>
<td><?= htmlspecialchars($user['status']) ?></td>
<td>
<a href="edit_user.php?id=<?= $user['id'] ?>" class="btn">Edit</a>
<?php if($user['status']=='Active'): ?>
<a href="disconnect_user.php?id=<?= $user['id'] ?>" class="btn">Disconnect</a>
<?php else: ?>
<a href="reconnect_user.php?id=<?= $user['id'] ?>" class="btn">Reconnect</a>
<?php endif; ?>
<a href="delete_user.php?id=<?= $user['id'] ?>" class="btn" onclick="return confirm('Delete this user?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php elseif($page=='bills'): ?>
<h2>Bills Management</h2>
<a href="add_bill.php" class="btn">Add New Bill</a>
<table>
<thead><tr><th>ID</th><th>User</th><th>Amount</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead>
<tbody>
<?php while($bill=$billsResult->fetch_assoc()): ?>
<tr style="<?= ($bill['status']=='Pending' && $bill['due_date']<date('Y-m-d'))?'background:#f8d7da;':'' ?>">
<td><?= htmlspecialchars($bill['id']) ?></td>
<td><?= htmlspecialchars($bill['fullname']) ?></td>
<td>₱<?= number_format($bill['amount'],2) ?></td>
<td><?= htmlspecialchars($bill['due_date']) ?></td>
<td><?= htmlspecialchars($bill['status']) ?></td>
<td>
<?php if($bill['status']=='Pending'): ?>
<a href="mark_paid.php?id=<?= $bill['id'] ?>" class="btn">Mark Paid</a>
<?php endif; ?>
<a href="edit_bill.php?id=<?= $bill['id'] ?>" class="btn">Edit</a>
<a href="delete_bill.php?id=<?= $bill['id'] ?>" class="btn" onclick="return confirm('Delete this bill?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php elseif($page=='payments'): ?>
<h2>Payments</h2>
<table>
<thead><tr><th>ID</th><th>User</th><th>Amount</th><th>Date</th></tr></thead>
<tbody>
<?php while($payment=$paymentsResult->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($payment['id']) ?></td>
<td><?= htmlspecialchars($payment['fullname']) ?></td>
<td>₱<?= number_format($payment['amount'],2) ?></td>
<td><?= htmlspecialchars($payment['date']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php elseif($page=='settings'): ?>
<h2>Settings - Change Password</h2>
<?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
<form method="post">
<label>Current Password</label><br>
<input type="password" name="current_password" required><br><br>
<label>New Password</label><br>
<input type="password" name="new_password" required><br><br>
<label>Confirm Password</label><br>
<input type="password" name="confirm_password" required><br><br>
<button type="submit" name="change_password" class="btn">Change Password</button>
</form>
<?php endif; ?>

</div>

<footer>
&copy; 2025 IWADCO. All rights reserved.
</footer>

</body>
</html>
