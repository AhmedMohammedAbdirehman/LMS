<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php"); exit();
}

$lesson_id = $_GET['lesson_id'] ?? 0;
$title = $_GET['title'] ?? "Assignment";
$student_id = $_SESSION['user_id'];
$msg = "";

if (isset($_POST['upload'])) {
    $target_dir = "../uploads/submissions/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_name = time() . "_" . basename($_FILES["file_to_upload"]["name"]);
    $target_file = $target_dir . $file_name;
    $db_path = "uploads/submissions/" . $file_name;

    if (move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO submissions (lesson_id, student_id, file_path) VALUES ('$lesson_id', '$student_id', '$db_path')";
        if ($conn->query($sql)) {

        // --- LOGGING START ---
// 1. Fetch the course name related to this lesson
$course_query = $conn->query("SELECT c.title FROM courses c JOIN lessons l ON c.id = l.course_id WHERE l.id = '$lesson_id'");
$c_data = $course_query->fetch_assoc();
$course_name = $c_data['title'] ?? 'Unknown Course';

// 2. Identify the student (already defined as $student_id in your code)
$student_name = $_SESSION['name'] ?? 'A Student';

// 3. Create the detail message
$details = "Student ($student_name) submitted assignment: $title for course ($course_name)";

// 4. Call the tracking function
if (function_exists('logActivity')) {
    logActivity($conn, $student_id, 'INSERT', $details);
}
// --- LOGGING END ---
            $msg = "<p style='color:green;'>Assignment submitted successfully!</p>";
        }
    } else {
        $msg = "<p style='color:red;'>Error uploading file.</p>";
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Assignment</title>

    <style>

        <style>
    :root {
        --primary: #1a0b45;
        --success: #27ae60;
        --bg: #f4f7f6;
        --text: #334155;
    }

    body {
        background-color: var(--bg) !important;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        color: var(--text);
    }

    /* Main Container Card */
    div[style*="max-width:500px"] {
        max-width: 450px !important;
        width: 90% !important; /* Full width on small devices */
        background: white !important;
        padding: 40px !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
        border: 1px solid #e2e8f0 !important;
    }

    h2 {
        color: var(--primary);
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    a[href="dashboard.php"] {
        text-decoration: none;
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 600;
        transition: color 0.2s;
    }

    a[href="dashboard.php"]:hover {
        color: var(--primary);
    }

    /* File Input Styling */
    input[type="file"] {
        border: 2px dashed #cbd5e1 !important; /* Makes it look like a dropzone */
        background: #f8fafc;
        padding: 30px 15px !important;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    input[type="file"]:hover {
        border-color: #3498db !important;
        background: #f1f5f9;
    }

    /* Modern Success Button */
    button[name="upload"] {
        background: var(--success) !important;
        font-size: 1rem !important;
        letter-spacing: 0.5px;
        transition: transform 0.2s, background 0.2s !important;
        box-shadow: 0 4px 6px rgba(39, 174, 96, 0.2) !important;
        margin-top: 10px;
    }

    button[name="upload"]:hover {
        background: #219150 !important;
        transform: translateY(-2px);
    }

    /* Feedback Messages */
    p {
        padding: 12px;
        border-radius: 6px;
        font-weight: 500;
        text-align: center;
        font-size: 0.9rem;
    }
</style>
    </style>
    
</head>
<body style="background:#f4f7f6; padding:40px; font-family:sans-serif;">
    <div style="max-width:500px; margin:auto; background:white; padding:30px; border-radius:8px; shadow:0 2px 10px rgba(0,0,0,0.1);">
        <h2><span data-lang="submit_title">Submit:</span> <?php echo htmlspecialchars($title); ?></h2>
        <a href="dashboard.php">← <span data-lang="btn_back_dashboard">Back to Dashboard</span></a>
        <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">
        
        <?php echo $msg; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <label style="display:block; margin-bottom:10px; font-weight:bold;" data-lang="label_upload_solution">
        Upload your Solution (PDF Only)
    </label>
    
    <input type="file" name="file_to_upload" accept=".pdf" required 
           style="margin-bottom:20px; border:1px solid #ddd; padding:10px; width:100%; border-radius:4px;">
           
    <button type="submit" name="upload" style=" color:black; border:none; padding:12px; border-radius:4px; cursor:pointer; width:100%; font-weight:bold;" data-lang="btn_upload_submit">
        Upload & Submit PDF
    </button>
</form>
    </div>



<script src="/lms_tech/lang.js"></script>

</body>
</html>