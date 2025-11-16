<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullname = trim($_POST['fullname']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $dob = trim($_POST['dob']);
    $id_number = trim($_POST['id_number']); // optional
    $connection_type = trim($_POST['connection_type']);
    $preferred_date = trim($_POST['preferred_date']);
    $notes = trim($_POST['notes']);
    $username = $_SESSION['username'];

    // Handle file upload for ID
    $id_file_path = null;
    if(isset($_FILES['id_file']) && $_FILES['id_file']['error'] == 0){
        $upload_dir = 'uploads/ids/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $file_ext = pathinfo($_FILES['id_file']['name'], PATHINFO_EXTENSION);
        $file_name = $username.'_'.time().'.'.$file_ext;
        $file_path = $upload_dir.$file_name;
        if(move_uploaded_file($_FILES['id_file']['tmp_name'], $file_path)){
            $id_file_path = $file_path;
        } else {
            $error = "❌ Failed to upload ID file.";
        }
    }

    if(empty($fullname) || empty($address) || empty($contact) || empty($email) || empty($dob)){
        $error = "❌ Please fill in all required fields.";
    }

    if(empty($error)){
        $stmt = $conn->prepare("INSERT INTO applications 
            (username, fullname, address, contact, email, dob, id_number, id_file, connection_type, preferred_date, notes, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
        $stmt->bind_param("sssssssssss", $username, $fullname, $address, $contact, $email, $dob, $id_number, $id_file_path, $connection_type, $preferred_date, $notes);
        if($stmt->execute()){
            $success = "✅ Your application has been submitted successfully! Please wait for approval.";
        } else {
            $error = "❌ Failed to submit application. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apply for Water Connection - IWADCO</title>
<style>
body {font-family:'Segoe UI', Arial, sans-serif; margin:0; background:#f4f7fb; color:#1a1a1a; min-height:100vh; display:flex; flex-direction:column;}
.site-header {background:#fff; padding:15px 0; box-shadow:0 2px 6px rgba(0,0,0,0.05);}
.header-inner {width:90%; max-width:1200px; margin:auto; display:flex; justify-content:space-between; align-items:center;}
.logo {font-size:22px; font-weight:700; color:#0077cc; cursor:pointer;}
.main-nav {list-style:none; display:flex; gap:20px;}
.main-nav a {text-decoration:none; color:#1a1a1a; font-weight:500; transition:0.3s;}
.main-nav a:hover {color:#0077cc;}
.container {flex:1; width:90%; max-width:700px; margin:30px auto;}
.page-title {font-size:28px; font-weight:700; margin-bottom:10px;}
.subtitle {color:#555; margin-bottom:20px;}
.card {background:white; border-radius:10px; padding:25px; box-shadow:0 4px 12px rgba(0,0,0,0.06);}
.input-group {margin-bottom:15px; display:flex; flex-direction:column;}
.input-group label {font-size:14px; margin-bottom:5px;}
.input-group input, .input-group textarea, .input-group select {padding:10px; border-radius:6px; border:1px solid #ccc; font-size:14px;}
textarea {resize:none;}
.btn-submit {padding:12px 20px; border:none; border-radius:8px; background:#0077cc; color:white; font-weight:bold; cursor:pointer; transition:0.3s;}
.btn-submit:hover {opacity:0.9;}

/* Notification Styles */
.notification {
    position: fixed;
    top:20px;
    right:20px;
    padding:15px 20px;
    border-radius:8px;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
    font-weight:bold;
    color:white;
    z-index:1000;
    display:flex;
    align-items:center;
    gap:10px;
    animation: slideIn 0.5s ease forwards;
}
.notification.success {background:#28a745;}
.notification.error {background:#dc3545;}
@keyframes slideIn {from {opacity:0; transform:translateX(100%);} to {opacity:1; transform:translateX(0);}}

.site-footer {background:#0e3b66; color:#fff; padding:30px 20px; margin-top:auto; text-align:center;}
</style>
</head>
<body>

<header class="site-header">
<div class="header-inner">
  <div class="logo" onclick="window.location.href='home.php'">IWADCO</div>
  <ul class="main-nav">
    <li><a href="home.php">Home</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>
</header>

<main class="container">
<h1 class="page-title">Apply for Water Connection</h1>
<p class="subtitle">Complete the form below to request a new water connection.</p>

<div class="card">
<form method="POST" action="" enctype="multipart/form-data">
  <div class="input-group">
    <label for="fullname">Full Name *</label>
    <input type="text" id="fullname" name="fullname" required>
  </div>

  <div class="input-group">
    <label for="address">Address *</label>
    <input type="text" id="address" name="address" required>
  </div>

  <div class="input-group">
    <label for="contact">Contact Number *</label>
    <input type="text" id="contact" name="contact" required>
  </div>

  <div class="input-group">
    <label for="email">Email Address *</label>
    <input type="email" id="email" name="email" required>
  </div>

  <div class="input-group">
    <label for="dob">Date of Birth *</label>
    <input type="date" id="dob" name="dob" required>
  </div>

  <div class="input-group">
    <label for="id_file">Upload ID *</label>
    <input type="file" id="id_file" name="id_file" accept=".jpg,.jpeg,.png,.pdf" required>
  </div>

  <div class="input-group">
    <label for="id_number">ID Number (Optional)</label>
    <input type="text" id="id_number" name="id_number">
  </div>

  <div class="input-group">
    <label for="connection_type">Preferred Connection Type</label>
    <select id="connection_type" name="connection_type">
      <option value="Residential">Residential</option>
      <option value="Commercial">Commercial</option>
      <option value="Industrial">Industrial</option>
    </select>
  </div>

  <div class="input-group">
    <label for="preferred_date">Preferred Installation Date</label>
    <input type="date" id="preferred_date" name="preferred_date">
  </div>

  <div class="input-group">
    <label for="notes">Additional Notes</label>
    <textarea id="notes" name="notes" rows="4" placeholder="Meter reading info, special requirements, etc."></textarea>
  </div>

  <button type="submit" class="btn-submit">Submit Application</button>
</form>
</div>
</main>

<?php if($success): ?>
<div class="notification success" id="notification">
    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
</div>
<?php endif; ?>
<?php if($error): ?>
<div class="notification error" id="notification">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<script>
// Auto hide notification after 4 seconds
setTimeout(() => {
    const notif = document.getElementById('notification');
    if(notif) notif.style.display = 'none';
}, 4000);
</script>

<footer class="site-footer">
  © IWADCO 2025 — Water Connection Application Portal
</footer>

</body>
</html>
