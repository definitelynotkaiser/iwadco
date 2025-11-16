<?php
// ✅ Always send JSON response type
header('Content-Type: application/json');

// ✅ Include database connection file (make sure the filename is correct)
include("db_connect.php"); 

// ✅ Handle only POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize inputs
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $service_type = trim($_POST['service_type'] ?? '');
    $details = trim($_POST['details'] ?? '');
    $created_at = date("Y-m-d H:i:s");

    // ✅ Check database connection
    if (!$conn || $conn->connect_error) {
        echo json_encode([
            "success" => false,
            "message" => "Database connection failed: " . ($conn ? $conn->connect_error : 'Unknown error')
        ]);
        exit;
    }

    // ✅ Prepare SQL with default status "Pending"
    $stmt = $conn->prepare("INSERT INTO service_requests (fullname, username, service_type, details, created_at, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Database prepare error: " . $conn->error
        ]);
        exit;
    }

    // ✅ Bind parameters
    $stmt->bind_param("sssss", $fullname, $username, $service_type, $details, $created_at);

    // ✅ Execute query and return JSON
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "✅ Your service request has been submitted successfully!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "❌ Failed to submit request: " . $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();

} else {
    // If not POST
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}
?>
