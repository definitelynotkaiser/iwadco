<?php
session_start();
include('db_connect.php'); // connect to your database

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// --- HANDLE DELETE USER ---
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM users WHERE id=$delete_id");
    header("Location: user.php");
    exit();
}

// --- HANDLE ROLE UPDATE ---
if(isset($_POST['update_role']) && isset($_POST['user_id'])){
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'] === 'admin' ? 'admin' : 'user';
    $conn->query("UPDATE users SET role='$new_role' WHERE id=$user_id");
    header("Location: user.php");
    exit();
}

// --- HANDLE DISCONNECT / RECONNECT ---
if(isset($_GET['disconnect_id'])){
    $id = intval($_GET['disconnect_id']);
    $conn->query("UPDATE users SET status='Disconnected' WHERE id=$id");
    header("Location: user.php");
    exit();
}

if(isset($_GET['reconnect_id'])){
    $id = intval($_GET['reconnect_id']);
    $conn->query("UPDATE users SET status='Active' WHERE id=$id");
    header("Location: user.php");
    exit();
}

// --- FETCH USERS ---
$filter = $_GET['filter'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM users WHERE 1";
if($filter=='active') $sql .= " AND status='Active'";
if($filter=='disconnected') $sql .= " AND status='Disconnected'";
if($search != '') $sql .= " AND (Fullname LIKE '%$search%' OR Username LIKE '%$search%' OR id LIKE '%$search%')";
$sql .= " ORDER BY id ASC";

$usersResult = $conn->query($sql);

// --- EDIT ID ---
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users - IWADCO Admin</title>
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
.btn { padding:6px 12px; background:#0056a6; color:#fff; border:none; border-radius:6px; cursor:pointer; transition:background 0.3s; text-decoration:none; }
.btn:hover { background:#0077d1; }
input[type=text]{ padding:6px; width:200px; margin-right:10px; border-radius:5px; border:1px solid #ccc; }
select{ padding:6px; border-radius:5px; border:1px solid #ccc; }
.status-active { color:green; font-weight:bold; }
.status-disconnected { color:red; font-weight:bold; }
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
            <a href="bills.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='bill.php'?'active':''; ?>">Bills</a>
            <a href="payments.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='payments.php'?'active':''; ?>">Payments</a>
            <a href="admin_applications.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_applications.php'?'active':''; ?>">Water Applications</a>
            <a href="admin_service_requests.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_service_requests.php'?'active':''; ?>">Service Requests</a>
            <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='settings.php'?'active':''; ?>">Settings</a>
            <a href="logout.php" style="font-weight:bold;">Logout</a>
        </nav>
    </div>
</header>

<div class="main-content">
    <h1 style="color:#0056a6; user-select:none;">Users Management</h1>

    <!-- SEARCH & FILTER -->
    <form method="get" style="margin-bottom:15px;">
        <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
        <select name="filter">
            <option value="">All</option>
            <option value="active" <?= $filter=='active'?'selected':'' ?>>Active</option>
            <option value="disconnected" <?= $filter=='disconnected'?'selected':'' ?>>Disconnected</option>
        </select>
        <button type="submit" class="btn">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fullname</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($usersResult->num_rows > 0): ?>
                <?php while($user = $usersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['Fullname']) ?></td>
                        <td><?= htmlspecialchars($user['Username']) ?></td>
                        <td>
                            <?php if($edit_id == $user['id']): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="role">
                                        <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
                                        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="update_role" class="btn">Save</button>
                                    <a href="user.php" class="btn">Cancel</a>
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($user['role']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="<?= $user['status']=='Active' ? 'status-active' : 'status-disconnected' ?>">
                            <?= htmlspecialchars($user['status']) ?>
                        </td>
                        <td>
                            <?php if($edit_id != $user['id']): ?>
                                <a href="?edit_id=<?= $user['id'] ?>" class="btn">Edit</a>
                            <?php endif; ?>
                            <?php if($user['status']=='Active'): ?>
                                <a href="?disconnect_id=<?= $user['id'] ?>" class="btn">Disconnect</a>
                            <?php else: ?>
                                <a href="?reconnect_id=<?= $user['id'] ?>" class="btn">Reconnect</a>
                            <?php endif; ?>
                            <a href="?delete_id=<?= $user['id'] ?>" class="btn" onclick="return confirm('Delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="no-record">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<footer>
    &copy; 2025 IWADCO. All rights reserved.
</footer>

</body>
</html>
