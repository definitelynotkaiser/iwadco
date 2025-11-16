<?php
/**
 * Fix Admin Email Typo Script
 * 
 * This script fixes the double @gmail.com typo in the admin email.
 * Run once after importing your database.
 * 
 * Access via: http://yourdomain.com/iwadco/fix_admin_email.php
 * 
 * IMPORTANT: Delete this file after use for security!
 */

include('db_connect.php');

$success = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_email'])) {
    // Fix the email typo
    $correct_email = 'nishcruz8@gmail.com';
    $result = $conn->query("UPDATE users SET email = '$correct_email' WHERE username = 'admin'");
    
    if ($result) {
        $success = true;
        $message = "✅ Admin email has been fixed to: $correct_email";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}

// Check current admin email
$result = $conn->query("SELECT email FROM users WHERE username = 'admin'");
$current_email = '';
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_email = $row['email'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Admin Email</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #0056a6; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; color: green; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; color: red; }
        button { background: #0056a6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0077d1; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Fix Admin Email Typo</h1>
        
        <div class="info">
            <strong>Current Admin Email:</strong> <?= htmlspecialchars($current_email) ?><br>
            <strong>Should be:</strong> nishcruz8@gmail.com
        </div>
        
        <?php if ($message): ?>
            <div class="<?= $success ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (strpos($current_email, '@gmail.com@gmail.com') !== false): ?>
            <form method="POST">
                <p>Click the button below to fix the email typo:</p>
                <button type="submit" name="fix_email">Fix Email</button>
            </form>
        <?php else: ?>
            <div class="success">
                ✅ Admin email is already correct!
            </div>
        <?php endif; ?>
        
        <p style="margin-top: 20px; color: #666; font-size: 12px;">
            <strong>Note:</strong> Delete this file after use for security.
        </p>
    </div>
</body>
</html>

