<?php
/**
 * Database Verification Script
 * 
 * Run this script after importing your database to verify everything is set up correctly.
 * Access it via: http://yourdomain.com/iwadco/verify_database.php
 * 
 * IMPORTANT: Delete this file after verification for security!
 */

include('db_connect.php');

$errors = [];
$warnings = [];
$success = [];

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
$success[] = "✅ Database connection successful";

// Check if required tables exist
$required_tables = ['users', 'billing', 'application'];
foreach ($required_tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        $success[] = "✅ Table '$table' exists";
    } else {
        $errors[] = "❌ Table '$table' is missing";
    }
}

// Check users table structure
if (in_array('users', $required_tables)) {
    $result = $conn->query("SHOW COLUMNS FROM users");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    $required_columns = ['id', 'account_number', 'fullname', 'username', 'role', 'status', 'email', 'password'];
    foreach ($required_columns as $col) {
        if (in_array($col, $columns)) {
            $success[] = "✅ Column 'users.$col' exists";
        } else {
            $errors[] = "❌ Column 'users.$col' is missing";
        }
    }
}

// Check if admin user exists
$result = $conn->query("SELECT * FROM users WHERE username='admin' AND role='admin'");
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $success[] = "✅ Admin user exists";
    
    // Check for email typo (double @gmail.com)
    if (strpos($admin['email'], '@gmail.com@gmail.com') !== false) {
        $warnings[] = "⚠️ Admin email has typo: " . $admin['email'] . " (has double @gmail.com)";
    }
} else {
    $warnings[] = "⚠️ Admin user not found. You may need to create one.";
}

// Check billing table structure
if (in_array('billing', $required_tables)) {
    $result = $conn->query("SHOW COLUMNS FROM billing");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    $required_columns = ['id', 'billing_number', 'account_number', 'user_id', 'fullname', 'billing_month', 'billing_date', 'due_date', 'amount_due', 'status'];
    foreach ($required_columns as $col) {
        if (in_array($col, $columns)) {
            $success[] = "✅ Column 'billing.$col' exists";
        } else {
            $errors[] = "❌ Column 'billing.$col' is missing";
        }
    }
}

// Check application table structure
if (in_array('application', $required_tables)) {
    $result = $conn->query("SHOW COLUMNS FROM application");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    $required_columns = ['id', 'fullname', 'Address', 'Contact', 'Email', 'status'];
    foreach ($required_columns as $col) {
        if (in_array($col, $columns)) {
            $success[] = "✅ Column 'application.$col' exists";
        } else {
            $errors[] = "❌ Column 'application.$col' is missing";
        }
    }
}

// Check foreign key constraints
$result = $conn->query("SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND REFERENCED_TABLE_NAME IS NOT NULL");
if ($result->num_rows > 0) {
    $success[] = "✅ Foreign key constraints are set up";
    while ($row = $result->fetch_assoc()) {
        $success[] = "   → " . $row['TABLE_NAME'] . "." . $row['COLUMN_NAME'] . " → " . $row['REFERENCED_TABLE_NAME'];
    }
} else {
    $warnings[] = "⚠️ No foreign key constraints found";
}

// Get table row counts
foreach ($required_tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        $success[] = "📊 Table '$table' has {$row['count']} row(s)";
    }
}

// Display results
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Verification</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #0056a6; }
        .success { color: green; margin: 5px 0; }
        .error { color: red; margin: 5px 0; font-weight: bold; }
        .warning { color: orange; margin: 5px 0; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Verification Report</h1>
        
        <?php if (!empty($success)): ?>
        <div class="section">
            <h2>✅ Success</h2>
            <?php foreach ($success as $msg): ?>
                <div class="success"><?= htmlspecialchars($msg) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($warnings)): ?>
        <div class="section">
            <h2>⚠️ Warnings</h2>
            <?php foreach ($warnings as $msg): ?>
                <div class="warning"><?= htmlspecialchars($msg) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
        <div class="section">
            <h2>❌ Errors</h2>
            <?php foreach ($errors as $msg): ?>
                <div class="error"><?= htmlspecialchars($msg) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (empty($errors)): ?>
            <div class="section" style="background: #d4edda; border: 1px solid #c3e6cb;">
                <h2>🎉 Database is ready!</h2>
                <p>Your database has been imported successfully. You can now use your application.</p>
                <p><strong>Important:</strong> Delete this verification file for security!</p>
            </div>
        <?php else: ?>
            <div class="section" style="background: #f8d7da; border: 1px solid #f5c6cb;">
                <h2>⚠️ Issues Found</h2>
                <p>Please fix the errors above before using your application.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

