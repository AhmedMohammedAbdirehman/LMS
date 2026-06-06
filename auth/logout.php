<?php
session_start();

// 1. You MUST include the database connection first
require_once __DIR__ . '/../config/db.php'; 

// 2. Then include the global functions
require_once __DIR__ . '/../global_file.php';

// 3. Record the activity (Check if user_id exists to avoid errors)
if (isset($_SESSION['user_id'])) {
    logActivity($conn, $_SESSION['user_id'], 'LOGOUT', 'User logged out');
}

// 4. Destroy session and redirect
session_destroy();
header("Location: ../index.php");
exit();
?>