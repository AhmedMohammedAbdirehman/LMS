<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['role'] == 'teacher') {
    $teacher_id = $_SESSION['user_id'];
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_pass = $_POST['new_password'];

    // 1. Update Email
    $sql = "UPDATE users SET email = '$new_email' WHERE id = '$teacher_id'";
    
    if ($conn->query($sql)) {
        // Update session email so the form shows the new one
        $_SESSION['user_email'] = $new_email;

        // 2. If password is provided, update it
        if (!empty($new_pass)) {
            // NOTE: On your actual server, use password_hash() 
            // For now, using direct update to match your existing style
            $conn->query("UPDATE users SET password = '$new_pass' WHERE id = '$teacher_id'");
        }

        header("Location: dashboard.php?msg=updated#settings");
    } else {
        header("Location: dashboard.php?msg=error#settings");
    }
} else {
    header("Location: dashboard.php");
}
exit();