<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'];

// Sample FAQs
$faqs = [
    ['question'=>'How can I read my water bill?','answer'=>'Your water bill shows your consumption in cubic meters (m³), rate per unit, total amount, and due date.'],
    ['question'=>'What payment methods are accepted?','answer'=>'We accept over-the-counter payments, GCash, PayMaya, and online payments via QR code.'],
    ['question'=>'How can I submit a service request?','answer'=>'Use the "Submit a Ticket" form below and provide details of your issue or request.'],
    ['question'=>'What are the office hours?','answer'=>'Our office is open from 8:00 AM to 8:00 PM, Monday to Saturday.'],
    ['question'=>'How can I report a leak?','answer'=>'Submit a ticket or call our hotline immediately to report water leaks or emergencies.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IWADCO | Customer Service</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', sans-serif; }
body { background:#f5f8fa; color:#333; overflow-x:hidden; }

header {
    background:#fff;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
    position:fixed;
    top:0;
    left:0;
    width:100%;
    z-index:999;
    height:70px;
}
.navbar {
    display:flex; justify-content:center; align-items:center;
    padding:15px 60px; gap:50px; flex-wrap:wrap;
}
.navbar .logo { font-size:1.6rem; font-weight:bold; color:#0056a6; cursor:pointer; margin-right:50px; }
nav { display:flex; align-items:center; gap:25px; flex-wrap:wrap; }
nav a { text-decoration:none; color:#0056a6; font-weight:500; font-size:15px; padding:6px 10px; border-radius:5px; transition:0.3s; }
nav a:hover { color:#0077d1; }
.dropdown { position:relative; display:inline-block; }
.dropdown-menu { display:none; position:absolute; top:120%; left:0; background:#0056a6; border-radius:6px; list-style:none; padding:10px 0; min-width:230px; box-shadow:0 4px 10px rgba(0,0,0,0.15); z-index:9999; }
.dropdown-menu li a { padding:10px 16px; color:#fff; display:block; text-decoration:none; font-size:14px; }
.dropdown-menu li a:hover { background:#0077d1; color:#fff; }
.dropdown:hover .dropdown-menu { display:block; }

.container {
    max-width:1200px;
    margin:0 auto;
    padding:120px 20px 20px 20px; /* top padding para sa fixed header */
}
.card { background:white; padding:25px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.08); margin-bottom:20px; }
h2,h3 { color:#0056a6; }
input, textarea, select {
    width:100%; padding:10px; margin:5px 0 15px 0;
    border:1px solid #ccc; border-radius:6px; font-size:14px;
    pointer-events:auto;
}
textarea { resize: vertical; }
button { padding:10px 20px; border:none; border-radius:6px; background:#0056a6; color:white; font-weight:600; cursor:pointer; transition:0.3s; }
button:hover { background:#0077d1; }

.faq-item { border-bottom:1px solid #ddd; margin-bottom:10px; }
.faq-question { cursor:pointer; padding:12px; background:#f0f4f8; border-radius:6px; font-weight:600; }
.faq-answer { display:none; padding:10px 12px; color:#333; }

.footer { background:#0056a6; color:white; text-align:center; padding:12px 0; margin-top:auto; }
.contact-info div { margin-bottom:10px; font-size:14px; }
.contact-info strong { color:#0056a6; }

@media screen and (max-width:768px){
  .navbar { flex-direction:column; align-items:center; gap:10px; }
}
</style>
</head>
<body>

<header>
  <div class="navbar">
    <div class="logo" onclick="window.location.href='home.php'">IWADCO</div>
    <nav>
      <a href="home.php">Home</a>
      <a href="about.php">About Us</a>
      <div class="dropdown">
        <a href="#">Services ▼</a>
        <ul class="dropdown-menu">
          <li><a href="customer_service.php">Customer Service</a></li>
        </ul>
      </div>
      <a href="apply.php">Apply Now</a>
      <a href="my_water_bills.php">My Water Bill</a>
      <a href="logout.php" style="color:#0056a6; font-weight:bold;">Logout</a>
    </nav>
  </div>
</header>

<div class="container">
    <div class="card contact-info">
        <h2>Contact Information</h2>
        <div><strong>📞 Hotline:</strong> 0917-123-4567</div>
        <div><strong>✉ Email:</strong> support@iwadco.com</div>
        <div><strong>🏢 Address:</strong> 123 Main St, City, Country</div>
        <div><strong>⏰ Office Hours:</strong> 8:00 AM – 8:00 PM</div>
        <div><strong>💬 Social Media:</strong> Facebook | Twitter | Instagram</div>
    </div>

    <div class="card">
        <h2>Submit a Service Request</h2>
        <form id="ticketForm" enctype="multipart/form-data">
            <input type="text" name="fullname" value="<?= htmlspecialchars($fullname) ?>" readonly>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" readonly>
            
            <select name="service_type" required>
                <option value="">Select Service Type</option>
                <option value="Report a Leak or Pipe Damage">Report a Leak or Pipe Damage</option>
                <option value="Water Quality Concern">Water Quality Concern</option>
                <option value="Complaint or Feedback">Complaint or Feedback</option>
                <option value="Emergency Hotline">Emergency Hotline Inquiry</option>
                <option value="Office Hours or Contact Info">Office Hours / Contact Info Inquiry</option>
                <option value="Meter Reading Request">Meter Reading Request</option>
                <option value="Customer Satisfaction Survey">Customer Satisfaction Survey</option>
            </select>
            
            <textarea name="details" rows="4" placeholder="Describe your issue..." required></textarea>
            <input type="file" name="attachment">
            <button type="submit">Submit Request</button>
        </form>
    </div>

    <div class="card">
        <h2>Frequently Asked Questions (FAQ)</h2>
        <?php foreach($faqs as $faq): ?>
        <div class="faq-item">
            <div class="faq-question"><?= htmlspecialchars($faq['question']) ?></div>
            <div class="faq-answer"><?= htmlspecialchars($faq['answer']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <h2>Useful Links</h2>
        <ul>
            <li><a href="apply.php">Apply for New Service</a></li>
            <li><a href="my_water_bills.php">View My Water Bill</a></li>
            <li><a href="payment_options.php">Payment Options</a></li>
            <li><a href="download_forms.php">Download Forms</a></li>
        </ul>
    </div>
</div>

<div class="footer">&copy; <?= date('Y') ?> IWADCO. All rights reserved.</div>

<script>
document.querySelectorAll('.faq-question').forEach(q=>{
    q.addEventListener('click', ()=>{
        const answer = q.nextElementSibling;
        answer.style.display = (answer.style.display==='block') ? 'none' : 'block';
    });
});

document.getElementById('ticketForm').addEventListener('submit', function(e){
    e.preventDefault();
    let formData = new FormData(this);

    fetch('submit_ticket.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            alert(data.message);
            document.getElementById('ticketForm').reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => alert('Request failed: ' + error));
});
</script>
</body>
</html>
