<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';

// 1. Security Check: Only allow logged-in admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

// 2. Validate the ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $course_id = $_GET['id'];

    // --- LOGGING PREPARATION ---
    // Fetch the course title before we delete it so the log is descriptive
    $course_query = $conn->query("SELECT title FROM courses WHERE id = $course_id");
    $course_data = $course_query->fetch_assoc();
    $course_name = $course_data['title'] ?? 'Unknown Course';

    // 3. Optional: Delete related lessons first if your database doesn't have CASCADE delete
    // $conn->query("DELETE FROM lessons WHERE course_id = $course_id");

    // 4. Delete the Course
    $sql = "DELETE FROM courses WHERE id = $course_id";

    if ($conn->query($sql)) {
        // --- LOG THE ACTIVITY ---
        $admin_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;
        $details = " deleted course: " . $course_name;
        
        if (function_exists('logActivity')) {
            logActivity($conn, $admin_id, 'DELETE', $details);
        }

        // Redirect back with a success flag
        header("Location: courses.php?msg=deleted");
    } else {
        // Redirect back with an error flag
        header("Location: courses.php?msg=error");
    }
} else {
    // If no ID is provided, just go back
    header("Location: courses.php");
}
exit();
?>