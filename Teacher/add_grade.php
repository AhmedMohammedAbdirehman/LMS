<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';

// Ensure we have a course_id from the URL
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : 0; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $assessment_name = $_POST['assessment_name'];
    $weight = $_POST['weight']; 
    $score = $_POST['score_out_of_100'];

    // 1. Insert the Grade
    $stmt = $conn->prepare("INSERT INTO grades (course_id, student_id, assessment_name, weight, score_out_of_100) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissd", $course_id, $student_id, $assessment_name, $weight, $score);
    
    if($stmt->execute()){
        
        // 2. Fetch descriptive data for the log
        $s_query = $conn->query("SELECT name FROM users WHERE id = '$student_id'");
        $s_data = $s_query->fetch_assoc();
        $student_name = $s_data['name'] ?? 'Unknown Student';

        $c_query = $conn->query("SELECT title FROM courses WHERE id = '$course_id'");
        $c_data = $c_query->fetch_assoc();
        $course_name = $c_data['title'] ?? 'Unknown Course';

        // 3. Capture the correct Session ID
        // Often 'user_id' is used for students and 'id' for staff, let's check both
        $current_user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;
        $role = ucfirst($_SESSION['role'] ?? 'Staff');
        
        $details = "$role added grade ($score%) for $student_name in $course_name ($assessment_name)";

        // 4. Log the Activity
        if (function_exists('logActivity')) {
            logActivity($conn, $current_user_id, 'INSERT', $details);
        }
        
        // 5. Redirect to the CORRECT file you mentioned
        header("Location: manage_students.php?course_id=$course_id&msg=success");
        exit();
    } else {
        // If it fails, show the error instead of redirecting
        die("Database Error: " . $conn->error);
    }
}
?>