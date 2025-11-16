<?php
/**
 * Entry point for IWADCO application
 * Redirects to login page or home page based on session
 */
session_start();

// Check if user is logged in
if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    header("Location: home.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>

