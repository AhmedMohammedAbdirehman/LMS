<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['role'] == 'student') {
    $user_id = $_SESSION['user_id'];
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_pass = $_POST['new_password'];

    // Update Email
    $sql = "UPDATE users SET email = '$new_email' WHERE id = '$user_id'";
    
    if ($conn->query($sql)) {
        $_SESSION['user_email'] = $new_email; // Update session data

    // --- LOGGING START ---
        $action_details = "Student updated their profile email to ($new_email)";
        
        // If a new password was typed
        if (!empty($new_pass)) {
            $conn->query("UPDATE users SET password = '$new_pass' WHERE id = '$user_id'");
            $action_details .= " and changed their password";
        }

        // Identify the student
        $student_name = $_SESSION['name'] ?? 'A Student';
        $log_message = "Student ($student_name): " . $action_details;

        // Call the tracking function
        if (function_exists('logActivity')) {
            logActivity($conn, $user_id, 'UPDATE', $log_message);
        }
        // --- LOGGING END ---
        header("Location: dashboard.php?msg=updated");
    } else {
        header("Location: dashboard.php?msg=error");
    }
}
exit();