<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['username']) || $_SESSION['role']!='admin'){
    echo json_encode(['new'=>0]);
    exit();
}

// Check new applications
$newApplications = 0;
if($conn->query("SHOW COLUMNS FROM applications LIKE 'notified'")->num_rows == 1){
    $result = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE status='Pending' AND notified=0");
    $data = $result->fetch_assoc();
    $newApplications = $data['total'] ?? 0;

    // Mark them as notified
    if($newApplications>0){
        $conn->query("UPDATE applications SET notified=1 WHERE status='Pending' AND notified=0");
    }
}

echo json_encode(['new'=>$newApplications]);
