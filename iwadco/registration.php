<?php
include('db_connect.php');
session_start();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email_prefix = trim($_POST['email']); 
    $email = $email_prefix . "@gmail.com"; 

    // SERVER-SIDE VALIDATION
    if (empty($fullname) || empty($username) || empty($password) || empty($confirm_password) || empty($email_prefix)) {
        $error = "❌ All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "❌ Passwords do not match.";
    } else {
        // Check if username exists
        $query = "SELECT * FROM users WHERE Username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "❌ Username already taken.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $status = 'connected'; // ✅ match sa enum('connected','disconnected')

            // Insert user — match exact case ng columns sa DB
            $insert = "INSERT INTO users (Fullname, Username, role, status, email, password) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("ssssss", $fullname, $username, $role, $status, $email, $hashed_password);

            if ($stmt->execute()) {
                // ✅ AUTO-LOGIN, consistent with home.php
                $_SESSION['username'] = $username;
                $_SESSION['fullname'] = $fullname;
                $_SESSION['role'] = $role;
                $_SESSION['status'] = $status;
                $_SESSION['email'] = $email;

                $success = "✅ Registration successful! Redirecting to home...";
                header("refresh:2;url=home.php");
                exit;
            } else {
                $error = "❌ Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration - IWADCO</title>
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #e0e0e0;
}
.login-container { width: 100%; max-width: 400px; padding: 20px; }
.login-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 35px 25px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    text-align: center;
}
.logo { font-size: 26px; font-weight: bold; color: #0072ff; margin-bottom: 14px; }
h2 { margin-bottom: 6px; color: #333; }
.subtitle { font-size: 13px; margin-bottom: 20px; color: #555; }
.input-group { position: relative; margin-bottom: 16px; }
.input-group input {
    width: 100%;
    padding: 12px 40px 12px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
}
.input-group input:focus {
    border-color: #0072ff;
    box-shadow: 0 0 5px rgba(0,114,255,0.3);
}
.input-group label {
    position: absolute;
    top: 50%;
    left: 12px;
    transform: translateY(-50%);
    color: #aaa;
    pointer-events: none;
    transition: 0.3s;
}
.input-group input:focus + label,
.input-group input:not(:placeholder-shown) + label {
    top: -8px;
    left: 10px;
    font-size: 12px;
    color: #0072ff;
    background: #fff;
    padding: 0 4px;
}
.show-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    width: 20px;
    height: 20px;
    fill: #0072ff;
    transition: transform 0.2s;
}
.show-password:hover { transform: translateY(-50%) scale(1.2); }
.btn-login {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #00c6ff, #0072ff);
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
.btn-login:hover { opacity: 0.9; }
.signup-text { margin-top: 14px; font-size: 13px; color: #555; }
.signup-text a { color: #0072ff; text-decoration: none; font-weight: bold; }
.signup-text a:hover { text-decoration: underline; }
.error { color: red; font-size: 13px; margin-bottom: 10px; }
.success { color: green; font-size: 13px; margin-bottom: 10px; }
footer { margin-top: 20px; text-align: center; color: #555; font-size: 13px; }
</style>
</head>
<body>
<div class="login-container">
<div class="login-card">
<div class="logo">IWADCO</div>
<h2>Create Account</h2>
<p class="subtitle">Register to manage your water account</p>
<?php
if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
if (!empty($success)) echo "<p class='success'>" . htmlspecialchars($success) . "</p>";
?>
<form method="post" action="">
    <div class="input-group">
        <input type="text" id="fullname" name="fullname" required placeholder=" ">
        <label for="fullname">Full Name</label>
    </div>
    <div class="input-group">
        <input type="text" id="username" name="username" required placeholder=" ">
        <label for="username">Username</label>
    </div>
    <div class="input-group">
        <input type="text" id="email" name="email" required placeholder=" ">
        <label for="email">Email (prefix only, e.g. username)</label>
    </div>
    <div class="input-group">
        <input type="password" id="password" name="password" required placeholder=" ">
        <label for="password">Password</label>
        <svg class="show-password" onclick="togglePassword('password', this)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/>
            <circle cx="12" cy="12" r="2.5"/>
        </svg>
    </div>
    <div class="input-group">
        <input type="password" id="confirm_password" name="confirm_password" required placeholder=" ">
        <label for="confirm_password">Confirm Password</label>
        <svg class="show-password" onclick="togglePassword('confirm_password', this)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/>
            <circle cx="12" cy="12" r="2.5"/>
        </svg>
    </div>
    <button type="submit" class="btn-login">Register</button>
    <p class="signup-text">Already have an account? <a href="login.php">Login</a></p>
</form>
</div>
<footer>&copy; 2025 IWADCO. All rights reserved.</footer>
</div>
<script>
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        icon.innerHTML = '<path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/><line x1="1" y1="1" x2="23" y2="23" stroke="red" stroke-width="2"/>';
    } else {
        field.type = "password";
        icon.innerHTML = '<path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/><circle cx="12" cy="12" r="2.5"/>';
    }
}
</script>
</body>
</html>
