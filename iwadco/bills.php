<?php
session_start();
include('db_connect.php'); // connect to your database

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// --- HANDLE DELETE BILL ---
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM bills WHERE user_id=$delete_id");
    header("Location: bills.php");
    exit();
}

// --- HANDLE STATUS UPDATE ---
if(isset($_POST['update_status']) && isset($_POST['bill_id'])){
    $bill_id = intval($_POST['bill_id']);
    $new_status = $_POST['status'] === 'Paid' ? 'Paid' : 'Pending';
    $conn->query("UPDATE bills SET status='$new_status' WHERE user_id=$bill_id");
    header("Location: bills.php");
    exit();
}

// --- FETCH BILLS ---
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM bills WHERE 1";
if($search != '') {
    $sql .= " AND (FULL_NAME LIKE '%$search%' OR user_id LIKE '%$search%')";
}
$sql .= " ORDER BY user_id ASC";

$billsResult = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bills - IWADCO Admin</title>
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
table { width:100%; border-collapse:collapse; margin-top:25px; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 6px 15px rgba(0,0,0,0.05); }
table th, table td { padding:12px 15px; text-align:left; font-size:14px; }
table th { background:#0077d1; color:#fff; font-weight:500; position:sticky; top:0; }
table tbody tr { border-bottom:1px solid #eee; transition:background 0.3s; }
table tbody tr:hover { background:#dbe7f5; }
table td .btn { margin-right:5px; font-size:13px; }
.btn, button { padding:6px 12px; background:#0056a6; color:#fff; border:none; border-radius:6px; cursor:pointer; transition:background 0.3s; text-decoration:none; user-select:none; -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; }
.btn:hover, button:hover { background:#0077d1; }
button:focus { outline:none; }
input[type=text]{ padding:6px; width:200px; margin-right:10px; border-radius:5px; border:1px solid #ccc; }
select{ padding:6px; border-radius:5px; border:1px solid #ccc; }
.status-Pending { color:orange; font-weight:bold; }
.status-Paid { color:green; font-weight:bold; }
.no-record { text-align:center; padding:20px; color:#555; }
footer { background:#0056a6; color:#fff; text-align:center; padding:15px; font-size:0.9rem; margin-top:auto; }

@media(max-width:992px){ nav{ gap:15px; } }
@media(max-width:768px){ .main-content{ padding:160px 20px 20px 20px; } .navbar{ padding:15px 20px; } }
</style>
</head>
<body>

<header>
    <div class="navbar">
        <div class="logo">IWADCO Admin</div>
        <nav>
            <a href="admin.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin.php'?'active':''; ?>">Dashboard</a>
            <a href="user.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='user.php'?'active':''; ?>">Users</a>
            <a href="bills.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='bills.php'?'active':''; ?>">Bills</a>
            <a href="payments.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='payments.php'?'active':''; ?>">Payments</a>
            <a href="admin_applications.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_applications.php'?'active':''; ?>">Water Applications</a>
            <a href="admin_service_requests.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_service_requests.php'?'active':''; ?>">Service Requests</a>
            <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='settings.php'?'active':''; ?>">Settings</a>
            <a href="logout.php" style="font-weight:bold;">Logout</a>
        </nav>
    </div>
</header>

<div class="main-content">
    <h1 style="color:#0056a6; user-select:none;">Customer Bills</h1>
    
    <!-- SEARCH -->
    <form method="get" style="margin-bottom:15px;">
        <input type="text" name="search" placeholder="Search by Account ID or Name..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn">Search</button>
    </form>

    <form method="post">
    <table>
        <thead>
            <tr>
                <th>Account ID</th>
                <th>Full Name</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if($billsResult->num_rows > 0): ?>
            <?php while($bill = $billsResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($bill['user_id']) ?></td>
                <td><?= htmlspecialchars($bill['FULL_NAME']) ?></td>
                <td>₱<?= number_format($bill['amount'],2) ?></td>
                <td><?= htmlspecialchars($bill['method']) ?></td>
                <td><?= htmlspecialchars($bill['paid_at']) ?></td>
                <td class="status-<?= $bill['status'] ?>"><?= htmlspecialchars($bill['status']) ?></td>
                <td>
                    <a href="?delete_id=<?= $bill['user_id'] ?>" class="btn" onclick="return confirm('Delete this bill?')">Delete</a>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="bill_id" value="<?= $bill['user_id'] ?>">
                        <select name="status">
                            <option value="Pending" <?= $bill['status']=='Pending'?'selected':'' ?>>Pending</option>
                            <option value="Paid" <?= $bill['status']=='Paid'?'selected':'' ?>>Paid</option>
                        </select>
                        <button type="submit" name="update_status" class="btn">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="no-record">No records found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </form>
</div>

<footer>
    &copy; 2025 IWADCO. All rights reserved.
</footer>

</body>
</html>
