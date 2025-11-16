<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us | IWADCO</title>
<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: #f5f8fa;
  color: #333;
  margin: 0;
  padding: 0;
}
.container {
  max-width: 800px;
  background: #fff;
  margin: 120px auto;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
h2 {
  color: #0056a6;
  margin-bottom: 15px;
}
p {
  margin-bottom: 15px;
}
.contact-item {
  margin-bottom: 12px;
  font-size: 16px;
}
.contact-item strong {
  color: #0056a6;
}
form input, form textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
}
button {
  background: #0056a6;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}
button:hover {
  background: #0077d1;
}
a.back {
  display: inline-block;
  margin-top: 15px;
  text-decoration: none;
  color: #0056a6;
}
a.back:hover {
  text-decoration: underline;
}
</style>
</head>
<body>
<div class="container">
  <h2>Office Hours & Contact Us</h2>
  <p>If you have any questions, feel free to contact us through the details below or send us a direct message using the form.</p>

  <div class="contact-item"><strong>🏢 Office Address:</strong> 123 Main St, City, Country</div>
  <div class="contact-item"><strong>📞 Hotline:</strong> 0917-123-4567</div>
  <div class="contact-item"><strong>📠 Telephone:</strong> (02) 888-5555</div>
  <div class="contact-item"><strong>✉ Email:</strong> support@iwadco.com</div>
  <div class="contact-item"><strong>🕓 Office Hours:</strong> Monday – Saturday, 8:00 AM – 8:00 PM</div>

  <h3 style="color:#0056a6; margin-top:25px;">Send a Message</h3>
  <form>
    <input type="text" placeholder="Your Name" required>
    <input type="email" placeholder="Your Email" required>
    <textarea rows="4" placeholder="Your Message..." required></textarea>
    <button type="submit">Send Message</button>
  </form>

  <a href="customer_service.php" class="back">← Back to Customer Service</a>
</div>
</body>
</html>
