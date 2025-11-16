<?php
session_start();
include("db_connect.php");

// Redirect if not logged in or not admin
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? $_SESSION['username'];
$success_msg = "";
$error_msg = "";

// -------------------------
// UPDATE PROFILE
// -------------------------
if(isset($_POST['update_profile'])){
    $new_fullname = $conn->real_escape_string($_POST['fullname']);
    $new_username = $conn->real_escape_string($_POST['username']);

    $conn->query("UPDATE users SET fullname='$new_fullname', username='$new_username' WHERE id=".$_SESSION['id']);
    $_SESSION['fullname'] = $new_fullname;
    $_SESSION['username'] = $new_username;
    $success_msg = "Profile updated successfully!";
}

// -------------------------
// CHANGE PASSWORD
// -------------------------
if(isset($_POST['change_password'])){
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    $user = $conn->query("SELECT password FROM users WHERE id=".$_SESSION['id'])->fetch_assoc();
    if(password_verify($current_pass, $user['password'])){
        if($new_pass === $confirm_pass){
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$hashed' WHERE id=".$_SESSION['id']);
            $success_msg = "Password changed successfully!";
        } else {
            $error_msg = "New password and confirm password do not match!";
        }
    } else {
        $error_msg = "Current password is incorrect!";
    }
}

// -------------------------
// UPDATE WATER RATE
// -------------------------
if(isset($_POST['update_rate'])){
    $water_rate = floatval($_POST['water_rate']);
    $success_msg = "Water rate updated to ₱".number_format($water_rate,2);
}

// -------------------------
// UPDATE NOTIFICATIONS
// -------------------------
if(isset($_POST['update_notifications'])){
    $email_notify = isset($_POST['email_notify']) ? 1 : 0;
    $sms_notify = isset($_POST['sms_notify']) ? 1 : 0;
    $success_msg = "Notification settings updated!";
}

// -------------------------
// UPDATE SECURITY
// -------------------------
if(isset($_POST['update_security'])){
    $session_timeout = intval($_POST['session_timeout']);
    $success_msg = "Security settings updated!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Settings - IWADCO</title>
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

form { background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); margin-bottom:30px; }
form h3 { color:#0056a6; margin-bottom:15px; }
input, select { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px; }
button { padding:10px 20px; background:#0056a6; color:#fff; border:none; border-radius:5px; cursor:pointer; }
button:hover { background:#0077d1; }
.alert { padding:10px; margin-bottom:15px; border-radius:5px; }
.success { background:#d4edda; color:#155724; }
.error { background:#f8d7da; color:#721c24; }
label { display:block; margin-bottom:5px; font-weight:bold; }
table { width:100%; border-collapse:collapse; margin-top:20px; }
table th, table td { padding:10px; border:1px solid #ccc; text-align:left; font-size:14px; }
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
    <h1 style="color:#0056a6; user-select:none;">Settings</h1>

    <?php if($success_msg): ?><div class="alert success"><?php echo $success_msg; ?></div><?php endif; ?>
    <?php if($error_msg): ?><div class="alert error"><?php echo $error_msg; ?></div><?php endif; ?>

    <!-- Profile Form -->
    <form method="POST">
        <h3>Update Profile</h3>
        <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" placeholder="Full Name" required>
        <input type="text" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" placeholder="Username" required>
        <button type="submit" name="update_profile">Save Profile</button>
    </form>

    <!-- Change Password Form -->
    <form method="POST">
        <h3>Change Password</h3>
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" name="change_password">Change Password</button>
    </form>

    <!-- Water Rate Form -->
    <form method="POST">
        <h3>Water Rate</h3>
        <input type="number" step="0.01" name="water_rate" placeholder="₱ per cubic meter" required>
        <button type="submit" name="update_rate">Update Rate</button>
    </form>

    <!-- Notifications Form -->
    <form method="POST">
        <h3>Notifications</h3>
        <label><input type="checkbox" name="email_notify"> Email Notifications</label>
        <label><input type="checkbox" name="sms_notify"> SMS Notifications</label>
        <button type="submit" name="update_notifications">Update Notifications</button>
    </form>

    <!-- Security Form -->
    <form method="POST">
        <h3>Security</h3>
        <input type="number" name="session_timeout" placeholder="Session Timeout (minutes)" required>
        <button type="submit" name="update_security">Update Security</button>
    </form>

</div>

<footer>
    &copy; 2025 IWADCO. All rights reserved.
</footer>

</body>
</html>
