<?php
include("db_connect.php");
session_start();

$error = "";

// Redirect to home if already logged in
if (isset($_SESSION['username'])) { // ✅ lowercase (match with home.php)
    header("Location: home.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        $query = "SELECT * FROM users WHERE Username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $db_password = $row['password']; // lowercase in DB

            // ✅ verify hashed password
            if (password_verify($password, $db_password)) {
                // ✅ store sessions in lowercase (to match home.php)
                $_SESSION['username'] = $row['Username'];
                $_SESSION['fullname'] = $row['Fullname'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['status'] = $row['status'];
                $_SESSION['email'] = $row['email'];

                echo "<script>alert('✅ Login successful! Redirecting...');</script>";
                echo "<meta http-equiv='refresh' content='1;url=home.php'>";
                exit();
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ Account not found.";
        }
    } else {
        $error = "⚠️ Please fill in both username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - IWADCO</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #e0e0e0ff;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
.login-container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.login-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 35px 25px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    text-align: center;
    width: 100%;
    max-width: 360px;
}
.logo {
    font-size: 28px;
    font-weight: bold;
    color: #0056a6;
    margin-bottom: 10px;
}
h2 {
    margin-bottom: 6px;
    color: #0056a6;
}
.subtitle {
    font-size: 13px;
    margin-bottom: 18px;
    color: #333;
}
.input-group {
    position: relative;
    margin-bottom: 18px;
}
.input-group input {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
}
.input-group input:focus {
    border-color: #0056a6;
    box-shadow: 0 0 5px rgba(0,86,166,0.3);
}
.input-group label {
    position: absolute;
    top: 50%;
    left: 12px;
    transform: translateY(-50%);
    color: #aaa;
    pointer-events: none;
    transition: 0.3s;
    background: white;
    padding: 0 4px;
}
.input-group input:focus + label,
.input-group input:not(:placeholder-shown) + label {
    top: -10px;
    font-size: 12px;
    color: #0056a6;
}
.show-password {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 16px;
    font-size: 13px;
}
.show-password input {
    margin-right: 6px;
}
.btn-login {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 8px;
    background-color: #0056a6;
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}
.btn-login:hover {
    background-color: #0077d1;
    transform: translateY(-2px);
}
.signup-text, .forgot-text {
    margin-top: 12px;
    font-size: 13px;
    color: #555;
}
.signup-text a, .forgot-text a {
    color: #0056a6;
    text-decoration: none;
    font-weight: bold;
}
.signup-text a:hover, .forgot-text a:hover {
    text-decoration: underline;
}
.error {
    color: red;
    font-size: 13px;
    margin-bottom: 10px;
}
.footer {
    background-color: #f1f2f5;
    text-align: center;
    padding: 15px 10px;
    font-size: 12px;
    color: #65676b;
}
.footer a {
    color: #65676b;
    text-decoration: none;
    margin: 0 6px;
}
.footer a:hover {
    text-decoration: underline;
}
.language-selector {
    margin-bottom: 8px;
}
.language-selector select {
    border: none;
    background: transparent;
    font-size: 12px;
    color: #65676b;
    cursor: pointer;
}
</style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="logo">IWADCO</div>
        <h2>Stay Hydrated, Stay Informed!</h2>
        <p class="subtitle">Login to manage your water account</p>

        <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

        <form method="post" action="">
            <div class="input-group">
                <input type="text" id="username" name="username" required placeholder=" ">
                <label for="username">Username</label>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" required placeholder=" ">
                <label for="password">Password</label>
            </div>

            <div class="show-password">
                <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()">
                <label for="togglePassword">Show Password</label>
            </div>

            <button type="submit" class="btn-login">Login</button>

            <p class="forgot-text">
                <a href="#">Forgot Password?</a>
            </p>
            <p class="signup-text">
                Don’t have an account? <a href="registration.php">Sign Up</a>
            </p>
        </form>
    </div>
</div>

<div class="footer">
    <div class="language-selector">
        <select onchange="changeLanguage(this.value)">
            <option value="en" selected>English (US)</option>
            <option value="es">Español</option>
            <option value="fr">Français</option>
            <option value="de">Deutsch</option>
            <option value="zh">中文</option>
        </select>
    </div>
    <div class="footer-links">
        <a href="#">About</a>
        <a href="#">Help</a>
        <a href="#">Terms</a>
        <a href="#">Privacy</a>
        <a href="#">Cookies</a>
        <a href="#">Ads</a>
        <a href="#">More</a>
    </div>
</div>

<script>
function changeLanguage(lang) {
    alert("Language changed to: " + lang);
}
function togglePasswordVisibility() {
    const passwordField = document.getElementById("password");
    passwordField.type = passwordField.type === "password" ? "text" : "password";
}
</script>
</body>
</html>
