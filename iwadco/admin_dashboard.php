<?php
session_start();
include("db_connect.php");

// Check admin login
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Handle Reconnection / Disconnection
if(isset($_GET['action']) && isset($_GET['user_id'])){
    $user_id = intval($_GET['user_id']);
    
    if($_GET['action'] === 'disconnect'){
        $conn->query("UPDATE bills SET status='Disconnected' WHERE id = (SELECT id FROM (SELECT id FROM bills WHERE user_id=$user_id ORDER BY id DESC LIMIT 1) AS t)");
    } elseif($_GET['action'] === 'reconnect'){
        $conn->query("UPDATE bills SET status='Active' WHERE id = (SELECT id FROM (SELECT id FROM bills WHERE user_id=$user_id ORDER BY id DESC LIMIT 1) AS t)");
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Handle Approve / Reject Application
if(isset($_GET['app_action']) && isset($_GET['app_id'])){
    $app_id = intval($_GET['app_id']);
    if($_GET['app_action'] === 'approve'){
        $conn->query("UPDATE application SET status='Approved' WHERE id=$app_id");
    } elseif($_GET['app_action'] === 'reject'){
        $conn->query("UPDATE application SET status='Rejected' WHERE id=$app_id");
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Determine which tab to show
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'users';

// Fetch users & latest bills (exclude admin)
$users = $conn->query("
    SELECT u.id AS user_id, u.fullname, b.amount, b.duedate, b.status AS payment_status, b.start
    FROM users u
    LEFT JOIN bills b 
    ON u.id = b.user_id
    AND b.id = (
        SELECT id FROM bills WHERE user_id=u.id ORDER BY id DESC LIMIT 1
    )
    WHERE u.role != 'admin'
");

// Fetch applications
$applications = $conn->query("SELECT * FROM application ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - IWADCO</title>
<style>
/* ===== BASE STYLES ===== */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f8fa;
    color: #333;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* ===== NAVBAR ===== */
.navbar {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 25px;
    flex-wrap: wrap;
    background-color: #ffffff;
    padding: 15px 40px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 999;
}

.navbar h1 {
    color: #0056a6;
    font-size: 1.6rem;
    font-weight: bold;
    cursor: pointer;
    margin-right: 40px;
}

.navbar a {
    text-decoration: none;
    color: #0056a6;
    font-weight: 500;
    font-size: 15px;
    padding: 6px 12px;
    border-radius: 6px;
    transition: 0.3s;
}

.navbar a:hover {
    background-color: #0077d1;
    color: white;
}

/* ===== CONTAINER ===== */
.container {
    flex: 1;
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
    padding: 100px 20px 20px 20px;
}

/* ===== CARD ===== */
.card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
    text-align: left;
    font-size: 14px;
}

th {
    background-color: #f8f8f8;
    font-weight: 600;
}

/* Buttons & Status */
button {
    padding: 6px 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
}

.btn-approve { background: #10b981; color: white; }
.btn-reject { background: #ef4444; color: white; }
.btn-action { background: #0077cc; color: white; margin-right: 5px; }
button:disabled { background: #ccc; cursor: not-allowed; }
.status-active { color: #10b981; font-weight: 600; }
.status-disconnected { color: #ef4444; font-weight: 600; }

/* Footer sticky */
.footer {
    background-color: #0056a6;
    color: white;
    text-align: center;
    padding: 15px 0;
    flex-shrink: 0;
}

/* Responsive */
@media screen and (max-width:768px){
    .navbar { flex-direction: column; align-items: center; gap: 10px; }
    .navbar h1 { margin-bottom: 10px; }
    .container { padding: 140px 15px 20px 15px; }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <h1 onclick="window.location.href='home.php'">IWADCO</h1>
    <a href="home.php">Home</a>
    <a href="?tab=users">Users & Bills</a>
    <a href="?tab=applications">Connection Applications</a>
    <a href="login.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="container">
    <?php if($tab === 'users'): ?>
    <div class="card">
        <h3>Users & Bills</h3>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Due Date</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Connection Start</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= $row['duedate'] ?? '-' ?></td>
                    <td><?= $row['amount'] ? '₱'.number_format($row['amount'],2) : '-' ?></td>
                    <td class="<?= $row['payment_status']==='Active'?'status-active':'status-disconnected' ?>"><?= $row['payment_status'] ?? '-' ?></td>
                    <td><?= $row['start'] ?? '-' ?></td>
                    <td>
                        <a href="?action=disconnect&user_id=<?= $row['user_id'] ?>">
                            <button class="btn-action" <?= $row['payment_status'] !== 'Active' ? 'disabled' : '' ?>>Disconnect</button>
                        </a>
                        <a href="?action=reconnect&user_id=<?= $row['user_id'] ?>">
                            <button class="btn-action" <?= $row['payment_status'] === 'Active' ? 'disabled' : '' ?>>Reconnect</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php elseif($tab === 'applications'): ?>
    <div class="card">
        <h3>Connection Applications</h3>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>DOB</th>
                    <th>Preferred Installation</th>
                    <th>Notes</th>
                    <th>ID File</th>
                    <th>ID Number</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($app = $applications->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($app['fullname']) ?></td>
                    <td><?= htmlspecialchars($app['Address']) ?></td>
                    <td><?= htmlspecialchars($app['Contact']) ?></td>
                    <td><?= htmlspecialchars($app['Email']) ?></td>
                    <td><?= htmlspecialchars($app['Birth']) ?></td>
                    <td><?= htmlspecialchars($app['prepdate']) ?></td>
                    <td><?= htmlspecialchars($app['notes']) ?></td>
                    <td><?php if($app['id_file']): ?><a href="<?= $app['id_file'] ?>" target="_blank">View ID</a><?php else: ?>-<?php endif; ?></td>
                    <td><?= htmlspecialchars($app['id_number'] ?? '-') ?></td>
                    <td class="<?= $app['status']==='Approved'?'status-active':($app['status']==='Rejected'?'status-disconnected':'') ?>"><?= $app['status'] ?></td>
                    <td>
                        <?php if($app['status'] === 'Pending'): ?>
                        <a href="?app_action=approve&app_id=<?= $app['id'] ?>"><button class="btn-approve">Approve</button></a>
                        <a href="?app_action=reject&app_id=<?= $app['id'] ?>"><button class="btn-reject">Reject</button></a>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- FOOTER -->
<div class="footer">
    &copy; <?= date('Y') ?> IWADCO. All rights reserved.
</div>

</body>
</html>
