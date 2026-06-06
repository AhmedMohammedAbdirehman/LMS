<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
$course_id = $_GET['course_id'];

// Get auto-fill data from URL (if coming from Review page)
$pre_student_id = $_GET['student_id'] ?? null;
$pre_as_name = $_GET['as_name'] ?? "";

// Handling Grade Submission
if(isset($_POST['submit_grade'])) {
    $s_id = $_POST['student_id'];
    $as_name = mysqli_real_escape_string($conn, $_POST['as_name']);
    $weight = (int)$_POST['weight'];
    $score = (float)$_POST['score'];

    $sql = "INSERT INTO grades (course_id, student_id, assessment_name, weight, score_out_of_100) 
            VALUES ('$course_id', '$s_id', '$as_name', '$weight', '$score') 
            ON DUPLICATE KEY UPDATE weight='$weight', score_out_of_100='$score'";
    
    if ($conn->query($sql)) {
        // --- LOGGING START ---
        
        // 1. Fetch Student and Course names for a clear log message
        $s_data = $conn->query("SELECT name FROM users WHERE id = '$s_id'")->fetch_assoc();
        $c_data = $conn->query("SELECT title FROM courses WHERE id = '$course_id'")->fetch_assoc();
        
        $student_name = $s_data['name'] ?? 'Unknown Student';
        $course_name = $c_data['title'] ?? 'Unknown Course';
        
        // 2. Identify who is logged in (Teacher/Manager)
        $logged_user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;
        $role = ucfirst($_SESSION['role'] ?? 'Staff');

        // 3. Create the detail string
        $details = "$role updated grade ($score%) for $student_name in $course_name ($as_name)";

        // 4. Call the tracking function
        if (function_exists('logActivity')) {
            logActivity($conn, $logged_user_id, 'UPDATE', $details);
        }
        
        // --- LOGGING END ---

       // Change the header line to this:
header("Location: manage_students.php?course_id=$course_id&msg=success");
        exit();
    }
}

$students = $conn->query("SELECT * FROM users WHERE role = 'student' AND FIND_IN_SET('$course_id', course_id)");
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weighted Grading</title>
    
<style>
    :root {
        --primary: #1a0b45;
        --secondary: #3498db;
        --success: #27ae60;
        --bg: #f4f7f6;
        --card-border: #e2e8f0;
    }

    body { 
        font-family: 'Inter', system-ui, sans-serif; 
        background-color: var(--bg); 
        margin: 0; 
        padding: 20px; 
        color: #1e293b;
    }

    .grading-container { max-width: 1300px; margin: auto; }

    /* Responsive Grid: 1 col on mobile, 2 on tablet, 3-4 on desktop */
    .student-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
        gap: 25px; 
        margin-top: 20px; 
    }

    .student-card { 
        background: white; 
        padding: 24px; 
        border-radius: 16px; 
        border: 1px solid var(--card-border); 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .student-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
    }

    /* Highlight style for the targeted student */
    .target-student {
        border: 2px solid var(--success) !important;
        background: #f0fff4 !important;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0px rgba(39, 174, 96, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(39, 174, 96, 0); }
        100% { box-shadow: 0 0 0 0px rgba(39, 174, 96, 0); }
    }

    input {
        width: 100%;
        padding: 12px;
        margin-bottom: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.9rem;
        box-sizing: border-box;
    }

    input:focus {
        border-color: var(--secondary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .btn-save {
        width: 100%;
        padding: 12px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-save:hover { background: #2c166b; }

    .nav-links a {
        text-decoration: none;
        color: var(--secondary);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .alert-success {
    background: #ecfdf5;
    color: #065f46;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #a7f3d0;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>

<body style="padding: 20px; background: #f4f7f6;">

   <div class="grading-container">

   <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
       <div class="alert-success">
            <span>✅</span> <strong><span data-lang="alert_success_bold">Success!</span></strong> 
            <span data-lang="alert_success_msg">The student's grade has been recorded and tracked successfully.</span>
        </div>
    <?php endif; ?>


    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h2 data-lang="grading_portal_title">Grading Portal </h2>
        <div class="nav-links">
            <a href="view_submissions.php">← <span data-lang="back_to_submissions">Back to Submissions</span></a> | 
            <a href="dashboard.php" data-lang="nav_dashboard">Dashboard</a>
        </div>
    </div>
    
    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

    <div class="student-grid">
        <?php while($s = $students->fetch_assoc()): 
            $is_target = ($s['id'] == $pre_student_id);
            $target_class = $is_target ? "target-student" : "";
        ?>
            <div class="student-card <?php echo $target_class; ?>">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($s['name']); ?>&background=random&size=45" style="border-radius:50%">
                    <div>
                        <h4 style="margin:0; font-size: 1.1rem;"><?php echo $s['name']; ?></h4>
                        <small style="color: #64748b;"><span data-lang="student_id_label">Student ID:</span> #<?php echo $s['id']; ?></small>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="student_id" value="<?php echo $s['id']; ?>">
                    
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569; display: block; margin-bottom: 5px;" data-lang="label_as_name">ASSESSMENT NAME</label>
                    <input type="text" name="as_name" 
                           placeholder="e.g. Mid Exam" data-lang-placeholder="placeholder_as_name"
                           value="<?php echo $is_target ? htmlspecialchars($pre_as_name) : ''; ?>" 
                           required>

                    <div style="display:flex; gap:12px;">
                        <div style="flex: 1;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569; display: block; margin-bottom: 5px;" data-lang="label_weight">TOTAL WEIGHT</label>
                            <input type="number" name="weight" data-lang-placeholder="placeholder_weight" placeholder="e.g. 20" required>
                        </div>
                        <div style="flex: 1;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569; display: block; margin-bottom: 5px;" data-lang="label_score">SCORE</label>
                            <input type="number" name="score" data-lang-placeholder="placeholder_score" placeholder="e.g. 18" required <?php echo $is_target ? 'autofocus' : ''; ?>>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit_grade" class="btn-save">
                       <span data-lang="btn_save_grade">Confirm & Save Grade</span>
                    </button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>







      <script src="http://localhost/lms_tech/lang.js"></script>

</body>

</html>