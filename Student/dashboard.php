<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];


// 2. Get student's list of IDs
$user_query = "SELECT course_id FROM users WHERE id = '$user_id'";
$user_result = $conn->query($user_query);
$user_data = $user_result->fetch_assoc();
$course_id_list = !empty($user_data['course_id']) ? $user_data['course_id'] : "0";

// 3. Get all courses for the Sidebar/List
if ($course_id_list !== "0" && !empty($course_id_list)) {
    $courses_res = $conn->query("SELECT * FROM courses WHERE id IN ($course_id_list)");
} else {
    // Prevent SQL error if list is empty
    $courses_res = $conn->query("SELECT * FROM courses WHERE id = 0");
}
// 4. Handle "Specific Course" selection
// If a user clicks a course, we get that ID from the URL. If not, we pick the first one.
$selected_course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// If no course is selected yet, default to the first course in their list
if ($selected_course_id == 0 && $course_id_list != "0") {
    $first_course = $conn->query("SELECT id FROM courses WHERE id IN ($course_id_list) LIMIT 1");
    if($f = $first_course->fetch_assoc()) $selected_course_id = $f['id'];
}

// 5. Fetch Materials & Grades ONLY for the selected course
$materials_res = $conn->query("SELECT * FROM lessons WHERE course_id = '$selected_course_id' AND category = 'material' ORDER BY part_number ASC");
$assessments_res = $conn->query("SELECT * FROM lessons WHERE course_id = '$selected_course_id' AND category != 'material'");

// 6. Calculate Grade for selected course
$total_score = 0;
$grades_list = []; // Initialize this array
$grade_res = $conn->query("SELECT * FROM grades WHERE student_id = '$user_id' AND course_id = '$selected_course_id'");

if ($grade_res) {
    while($g = $grade_res->fetch_assoc()) {
        $total_score += ($g['score_out_of_100'] * $g['weight']) / 100;
        $grades_list[] = $g; // Add this line so the table below can see the data
    }
}

// 6. Get list of IDs for assignments this student has already submitted
$submitted_ids = [];
$check_submissions = $conn->query("SELECT lesson_id FROM submissions WHERE student_id = '$user_id'");
while($sub = $check_submissions->fetch_assoc()) {
    $submitted_ids[] = $sub['lesson_id'];
}


// --- 7. Fetch Attendance with Search Logic ---
$search_date = $_GET['attn_date'] ?? ''; // Check if a date was searched
$attendance_list = [];
$total_present = 0;
$total_days = 0;

// Base query
$attn_sql = "SELECT * FROM attendance WHERE student_id = '$user_id' AND course_id = '$selected_course_id'";

// If a date is searched, filter by it
if (!empty($search_date)) {
    $attn_sql .= " AND attendance_date = '$search_date'";
}

$attn_sql .= " ORDER BY attendance_date DESC LIMIT 3";
$attendance_res = $conn->query($attn_sql);

if ($attendance_res) {
    while($a = $attendance_res->fetch_assoc()) {
        $attendance_list[] = $a;
        $total_days++;
        if($a['status'] == 'Present') $total_present++;
    }
}
$attendance_percentage = ($total_days > 0) ? ($total_present / $total_days) * 100 : 0;





// --- 8. FETCH CERTIFICATE (UPDATED WITH CORRECT COLUMN NAMES) ---
$certificate = false; 
$current_course_title = "Selected Course"; 
$current_course_name = "Selected Course"; // Add this line as a fallback for your HTML

if (isset($selected_course_id) && $selected_course_id > 0) {
    
    // 1. Get the official course title from the 'courses' table
    $course_sql = "SELECT title FROM courses WHERE id = ? LIMIT 1";
    $course_stmt = $conn->prepare($course_sql);

    if (!$course_stmt) {
        die("SQL Prepare Error (Courses): " . $conn->error);
    }

    $course_stmt->bind_param("i", $selected_course_id);
    $course_stmt->execute();
    $course_res = $course_stmt->get_result();

    if ($course_res && $course_res->num_rows > 0) {
        $course_data = $course_res->fetch_assoc();
        $current_course_title = trim($course_data['title']);

        // 2. Search for the certificate in the 'certificates' table
        // We use LOWER and REPLACE to match 'title' with 'course_name' regardless of spaces/casing
        $cert_sql = "SELECT id, status, certificate_serial FROM certificates 
                     WHERE student_id = ? 
                     AND LOWER(REPLACE(course_name, ' ', '')) = LOWER(REPLACE(?, ' ', '')) 
                     AND status = 'approved'
                     LIMIT 1";
        
        $cert_stmt = $conn->prepare($cert_sql);

        if (!$cert_stmt) {
            die("SQL Prepare Error (Certificates): " . $conn->error);
        }

        $cert_stmt->bind_param("is", $user_id, $current_course_title);
        $cert_stmt->execute();
        $cert_res = $cert_stmt->get_result();

        if ($cert_res && $cert_res->num_rows > 0) {
            $certificate = $cert_res->fetch_assoc();
        }
    }
}
echo "<!-- Debug: Student ID: $user_id | Looking for Course: $current_course_name -->";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Student Dashboard</title>
    
<style>
    /* Keep your existing styles above this */
    .grade-card { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #2ecc71; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .lesson-item { background: white; padding: 15px; margin-bottom: 10px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ddd; }
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
    .pdf-badge { background: #e74c3c; color: white; }
    .video-badge { background: #f1c40f; color: black; }
    .text-badge { background: #3498db; color: white; }

    /* --- RESPONSIVE FULL-WIDTH FIX --- */
    
    /* This targets the <div> with the inline grid style and forces it to 1 column on mobile */
    div[style*="display: grid"] {
        grid-template-columns: 1fr !important; /* Forces full width (1 column) */
        gap: 20px !important;
    }

    /* This restores the 2-column layout only for screens larger than 768px (Tablets/Desktops) */
    @media (min-width: 768px) {
        div[style*="display: grid"] {
            grid-template-columns: 1fr 1fr !important;
        }
    }

    /* Ensures the grade table doesn't break the layout on small phones */
    .grade-card {
        overflow-x: auto;
    }

    table {
        min-width: 100%;
    }

    /* Adjusts header flexbox for mobile */
    header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }

    @media (min-width: 768px) {
        header {
            flex-direction: row;
        }
    }

    .user-icon {
        width: 35px;
        height: 35px;
        background: #3498db;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        text-transform: uppercase;
    }
    .fa-cog {
    transition: transform 0.3s ease;
}
.fa-cog:hover {
    transform: rotate(90deg); /* Makes the gear spin when you hover! */
    color: #1a0b45;
}

.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
.status-present { background: #eafaf1; color: #27ae60; }
.status-absent { background: #fdedec; color: #e74c3c; }
.status-late { background: #fef9e7; color: #f39c12; }

.cert-delivery-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-top: 5px solid #1a0b45; /* Techiftiin Navy */
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.btn-cert {
    background: #f49d1a; /* Techiftiin Orange */
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
}
.btn-cert:hover { background: #d48612; }

</style>
</head>
<body style="background: #f4f7f6; padding: 20px; font-family: 'Segoe UI', sans-serif;">




<div style="margin-bottom: 25px;">
    <h3 style="color: #1a0b45; font-size: 0.9rem; text-transform: uppercase; margin-bottom: 10px;" data-lang="label_registered_courses">Your Registered Courses:</h3>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <?php 
        $courses_res->data_seek(0); // Reset pointer
        while($c = $courses_res->fetch_assoc()): 
            $active = ($c['id'] == $selected_course_id) ? 'border: 2px solid #2ecc71; background: #eafaf1;' : 'background: white;';
        ?>
            <a href="dashboard.php?course_id=<?php echo $c['id']; ?>" 
               style="text-decoration: none; color: #2c3e50; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-weight: bold; <?php echo $active; ?>">
               <i class="fas fa-book-reader" style="color: #2ecc71; margin-right: 8px;"></i>
               <?php echo $c['title']; ?>
            </a>
        <?php endwhile; ?>
    </div>
</div>




    <div style="max-width: 1000px; margin: 0 auto;">
<header style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #edf2f7; margin-bottom: 30px;">
    <div class="user-profile" style="display: flex; align-items: center; gap: 12px;">
        <div class="user-icon" style="width: 40px; height: 40px; background: #1a0b45; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; text-transform: uppercase; box-shadow: 0 4px 10px rgba(26, 11, 69, 0.2);">
            <?php echo substr($_SESSION['user_name'] ?? 'U', 0, 1); ?>
        </div>
        <div>
            <h2 style="margin:0; font-size: 1.1rem; color: #1a0b45;" data-lang="portal_title">My Learning Portal</h2>
            <p style="margin:0; font-size: 0.8rem; color: #7f8c8d;"><span data-lang="welcome_back">Welcome back,</span> <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>!</p>
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 15px;">
        <button onclick="openSettingsModal()" style="background: none; border: none; color: #3498db; cursor: pointer; font-size: 1.1rem;" title="Account Settings">
            <i class="fas fa-cog"></i>
        </button>

        <button onclick="toggleAI()" style="background: #8b5cf6; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);">
        <i class="fas fa-robot"></i> <span data-lang="btn_student_ai">Student AI</span>
    </button>
    
        <a href="../auth/logout.php" style="color: #e74c3c; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
            <i class="fas fa-sign-out-alt"></i> <span data-lang="nav_logout">Logout</span>
        </a>
    </div>
</header>





<!-- Permanent Certificate Status Card -->
<div class="cert-delivery-card" style="background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 25px; border-left: 6px solid #f49d1a; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
    
    <div style="display: flex; align-items: center; gap: 20px;">
        <div style="background: rgba(244, 157, 26, 0.1); padding: 15px; border-radius: 50%;">
            <i class="fas fa-certificate" style="color: #f49d1a; font-size: 1.5rem;"></i>
        </div>
        <div>
            <h4 style="margin: 0; color: #1a0b45; font-size: 1.1rem; font-weight: 700;">
                <?php echo htmlspecialchars($current_course_name); ?> <span data-lang="cert_label">Certificate</span>
            </h4>
            <p style="margin: 4px 0 0 0; font-size: 0.9rem; color: #6c757d;">
                <span data-lang="status_label">Status:</span> 
                <span style="font-weight: 600; color: <?php echo (!$certificate) ? '#e74c3c' : (($certificate['status'] == 'approved') ? '#27ae60' : '#f39c12'); ?>;">
                <?php 
                    if(!$certificate) echo '<span data-lang="cert_none">No Certificate Found</span>';
                    else echo ucfirst($certificate['status']); 
                ?>
                </span>
            </p>
        </div>
    </div>

    <div>
        <?php if ($certificate && $certificate['status'] === 'approved'): ?>
            <!-- Approved: Show Print Button -->
            <a href="view_certificate.php?id=<?php echo $certificate['id']; ?>" target="_blank" style="background: #f49d1a; color: #fff; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(244, 157, 26, 0.3);">
                <i class="fas fa-print"></i> <span data-lang="btn_download_cert">Download Certificate</span>
            </a>
        <?php elseif ($certificate): ?>
            <!-- Pending: Show Waiting Message -->
            <span style="font-size: 0.85rem; color: #d35400; background: #fff3e0; padding: 8px 16px; border-radius: 30px; border: 1px solid #ffe0b2; font-weight: 600;">
                <i class="fas fa-clock"></i><span data-lang="cert_pending"> Pending Approval </span>
            </span>
        <?php else: ?>
            <!-- No Record: Show Instruction -->
            <span style="font-size: 0.8rem; color: #95a5a6; font-style: italic;" data-lang="cert_instruction">
                Certificate will appear here after course completion
            </span>
        <?php endif; ?>
    </div>
</div>




<?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
    <div style="background: #27ae60; color: white; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 0.9rem;">
        ✅ Profile updated successfully!
    </div>
<?php endif; ?>

<div class="grade-card">
    <?php 
    // Initialize totals for the calculation
    $total_earned = 0;
    $total_possible = 0;

    if (!empty($grades_list)) {
        foreach($grades_list as $gl) {
            $total_earned += $gl['score_out_of_100'];
            $total_possible += $gl['weight'];
        }
        // Calculate the percentage based on the ratio (Earned / Possible) * 100
        $final_percentage = ($total_possible > 0) ? ($total_earned / $total_possible) * 100 : 0;
    } else {
        $final_percentage = 0;
    }
    ?>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin: 0;" data-lang="academic_progress_title">Academic Progress</h3>
            <p style="color: #7f8c8d; margin: 5px 0 0 0;" data-lang="cumulative_grade_label">Current Cumulative Grade</p>
        </div>
        <div style="font-size: 2.5rem; font-weight: bold; color: #27ae60;">
            <!-- Displaying the calculated 100% scale percentage -->
            <?php echo number_format($final_percentage, 1); ?>%
        </div>
    </div>
    
    <?php if (!empty($grades_list)): ?>
        <table style="width: 100%; margin-top: 15px; border-collapse: collapse; font-size: 0.9rem;">
            <tr style="border-bottom: 1px solid #eee; color: #7f8c8d;">
               <th align="left" data-lang="th_assessment">Assessment</th>
                <th align="center" data-lang="th_weight">Weight %</th>
                <th align="right" data-lang="th_score_obtained">Score Obtained</th>
            </tr>
            <?php foreach($grades_list as $gl): ?>
            <tr style="border-bottom: 1px solid #f9f9f9;">
                <td style="padding: 8px 0;"><?php echo $gl['assessment_name']; ?></td>
                <td align="center"><?php echo $gl['weight']; ?></td>
                <td align="right" style="font-weight: bold;"><?php echo $gl['score_out_of_100']; ?></td>
            </tr>
            <?php endforeach; ?>
            <!-- Summary Row -->
            <tr style="color: #1a0b45; font-weight: bold; background: #f8fafc;">
                <td style="padding: 8px;" data-lang="total_raw_score">TOTAL RAW SCORE</td>
                <td align="center"><?php echo $total_possible; ?></td>
                <td align="right"><?php echo $total_earned; ?></td>
            </tr>
        </table>
    <?php else: ?>
        <p style="font-style: italic; color: #95a5a6; margin-top: 10px;" data-lang="no_grades_posted">No grades posted yet.</p>
    <?php endif; ?>
</div>




     <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
    
    <div class="grade-card" style="border-left-color: #3498db; margin-bottom: 0;">
        <h3 style="margin-top: 0; font-size: 1.1rem;"><i class="fas fa-calendar-check" style="color:#3498db;"></i> <span data-lang="attendance_summary_title">Attendance Summary</span></h3>
        <div style="display: flex; gap: 15px; margin-top: 15px;">
            <div style="flex: 1; text-align: center; background: #f8fafc; padding: 15px; border-radius: 10px;">
                <span style="display: block; font-size: 1.5rem; font-weight: bold; color: #3498db;"><?php echo number_format($attendance_percentage, 0); ?>%</span>
                <small style="color: #7f8c8d;" data-lang="label_total_rate">Total Rate</small>
            </div>
            <div style="flex: 1; text-align: center; background: #f8fafc; padding: 15px; border-radius: 10px;">
                <span style="display: block; font-size: 1.5rem; font-weight: bold; color: #27ae60;"><?php echo $total_present; ?></span>
                <small style="color: #7f8c8d;" data-lang="label_days_present">Days Present</small>
            </div>
        </div>
    </div>

<div class="grade-card" style="border-left-color: #f39c12; margin-bottom: 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <h3 style="margin: 0; font-size: 1.1rem;"><i class="fas fa-history"></i> <span data-lang="logs_title">Logs</span></h3>
        
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="hidden" name="course_id" value="<?php echo $selected_course_id; ?>">
            <input type="date" name="attn_date" value="<?php echo $search_date; ?>" 
                   style="font-size: 0.75rem; padding: 3px; border: 1px solid #ddd; border-radius: 4px;">
            <button type="submit" style="background: #f39c12; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">
                <i class="fas fa-search"></i>
            </button>
            <?php if(!empty($search_date)): ?>
                <a href="dashboard.php?course_id=<?php echo $selected_course_id; ?>" style="color: #e74c3c; font-size: 0.75rem; text-decoration: none; margin-top: 5px;" data-lang="btn_clear">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div style="max-height: 150px; overflow-y: auto;">
        <?php if (!empty($attendance_list)): ?>
            <table style="width: 100%; font-size: 0.85rem; border-collapse: collapse;">
                <?php foreach($attendance_list as $at): ?>
                <tr style="border-bottom: 1px solid #f1f1f1;">
                    <td style="padding: 8px 0;"><?php echo date('M d, Y', strtotime($at['attendance_date'])); ?></td>
                    <td align="right" style="padding: 8px 0;">
                        <span class="status-badge status-<?php echo strtolower($at['status']); ?>">
                            <?php echo $at['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p style="font-style: italic; color: #95a5a6; font-size: 0.8rem; text-align: center; margin-top: 20px;">
                No records found for this date.
            </p>
        <?php endif; ?>
    </div>
</div>
</div>





<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
    
  <div>
    <h3 style="border-bottom: 2px solid #3498db; padding-bottom: 10px;">📚 <span data-lang="course_materials_title">Course Materials</span></h3>
    <?php 
    $current_part = 0;
    if($materials_res && $materials_res->num_rows > 0):
        while($row = $materials_res->fetch_assoc()): 
            if($current_part != $row['part_number']):
                $current_part = $row['part_number'];
                echo "<h4 style='margin: 20px 0 10px 0; color: #2c3e50;'>Part $current_part</h4>";
            endif;
    ?>
        <div class="lesson-item">
            <div>
                <!-- <small style="color:#7f8c8d; display:block;">Course ID: <?php echo $row['course_id']; ?></small> -->
                <span class="badge <?php echo $row['content_type']; ?>-badge"><?php echo $row['content_type']; ?></span>
                <strong style="margin-left: 10px;"><?php echo $row['title']; ?></strong>
            </div>
            <div class="actions">
                <?php if($row['content_type'] == 'text'): ?>
                    <button class="btn-manage" 
                            style="padding: 5px 10px; font-size: 0.8rem; background: #3498db; border: none; cursor: pointer; color: white; border-radius: 4px;" 
                            onclick="showText('<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['file_path_or_link']); ?>')">
                        Read Text
                    </button>
                <?php else: ?>
                    <a href="../<?php echo $row['file_path_or_link']; ?>" target="_blank" class="btn-manage" style="padding: 5px 10px; font-size: 0.8rem; text-decoration:none; background:#3498db; color:white; border-radius:4px;" data-lang="btn_open_pdf">Open PDF </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
    <?php else: ?>
        <p style="color: #95a5a6;">No study materials uploaded yet.</p>
    <?php endif; ?>
</div>

<div>
    <h3 style="border-bottom: 2px solid #e67e22; padding-bottom: 10px;">📝 Tasks & Exams</h3>
    <?php if($assessments_res && $assessments_res->num_rows > 0): ?>
        <?php while($row = $assessments_res->fetch_assoc()): ?>
            <div class="lesson-item" style="border-left: 5px solid #e67e22;">
                <div>
                    <span style="font-size: 0.7rem; font-weight: bold; color: #e67e22; text-transform: uppercase; display: block;">
                        <?php echo $row['category']; ?>
                    </span>
                    <strong><?php echo $row['title']; ?></strong>
                </div>

                <div class="actions" style="display: flex; align-items: center; gap: 10px;">
                    <?php if($row['content_type'] == 'text'): ?>
                        <button class="btn-manage" style="background:#2c3e50; color:white; border:none; padding:5px; border-radius:4px; cursor:pointer;" 
                                onclick="showText('<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['file_path_or_link']); ?>')">
                            View Info
                        </button>
                    <?php else: ?>
                        <a href="../<?php echo $row['file_path_or_link']; ?>" target="_blank" style="color:#2980b9; font-size:0.8rem; text-decoration:none; font-weight:bold;">Download PDF</a>
                    <?php endif; ?>

                    <?php if(in_array($row['id'], $submitted_ids)): ?>
                        <span style="color: #27ae60; font-weight: bold; font-size: 0.8rem;">
                            <i class="checkmark">✓</i> <span data-lang="status_submitted">Submitted</span>
                        </span>
                    <?php else: ?>
                        <a href="submit_assignment.php?lesson_id=<?php echo $row['id']; ?>&title=<?php urlencode($row['title']); ?>" 
                           style="background: #27ae60; color: white; padding: 5px 12px; border-radius: 4px; text-decoration: none; font-size: 0.8rem;" data-lang="btn_submit_work">
                           Submit Work
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color: #95a5a6;">No tasks listed.</p>
    <?php endif; ?>
</div>


</div>




<div id="textModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6);">
    <div style="background:white; margin:10% auto; padding:25px; border-radius:8px; width:80%; max-width:600px; box-shadow:0 5px 15px rgba(0,0,0,0.3); position:relative;">
        <span onclick="closeModal()" style="position:absolute; right:15px; top:10px; font-size:24px; cursor:pointer; color:#7f8c8d;">&times;</span>
        <h3 id="modalTitle" style="margin-top:0; color:#2c3e50; border-bottom:1px solid #eee; padding-bottom:10px;">Content Details</h3>
        <div id="modalBody" style="margin-top:15px; line-height:1.6; color:#34495e; white-space: pre-wrap;">
            </div>
        <button onclick="closeModal()" style="margin-top:20px; padding:8px 20px; background:#3498db; color:white; border:none; border-radius:4px; cursor:pointer;">Close</button>
    </div>
</div>




<div id="settingsModal" style="display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter: blur(3px);">
    <div style="background:white; margin:5% auto; padding:25px; border-radius:12px; width:90%; max-width:400px; box-shadow:0 10px 25px rgba(0,0,0,0.2); position:relative;">
        <span onclick="closeSettingsModal()" style="position:absolute; right:15px; top:10px; font-size:24px; cursor:pointer; color:#7f8c8d;">&times;</span>
        
        <h3 style="margin-top:0; color:#1a0b45;" data-lang="settings_title">Account Settings</h3>
        <p style="color: #7f8c8d; font-size: 0.85rem; margin-bottom: 20px;" data-lang="settings_subtitle">Update your email or set a secure new password.</p>

        <form action="update_profile.php" method="POST" onsubmit="return validatePassword()">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 5px;" data-lang="label_email">Email Address</label>
                <input type="email" name="email" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
            </div>

            <!-- New Password -->
            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 5px;" data-lang="label_new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current" onkeyup="checkStrength(this.value)"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
            </div>

            <!-- Security Criteria Display -->
            <div id="password-criteria" style="font-size: 0.45rem; color: #95a5a6; margin-bottom: 8px; padding: 5px; background: #f9f9f9; border-radius: 6px;">
                <div id="crit-upper" data-lang="crit_upper">✖ Uppercase & Lowercase</div>
                <div id="crit-number" data-lang="crit_number">✖ At least one number</div>
                <div id="crit-special" data-lang="crit_special">✖ Special character (@$!%*#?&)</div>
            </div>

            <!-- Repeat Password -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 5px;" data-lang="label_confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat new password" data-lang-placeholder="placeholder_repeat_password"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
            </div>

            <button type="submit" id="saveBtn" style="width: 100%; padding: 12px; background: #1a0b45; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s;" data-lang="btn_save_changes">
                Save Changes
            </button>
        </form>
    </div>
</div>










<!-- The AI Panel -->
<div id="aiSidebar" style="position: fixed; right: -450px; top: 0; width: 400px; height: 100%; background: #ffffff; z-index: 3000; transition: 0.4s ease; box-shadow: -10px 0 30px rgba(0,0,0,0.15); display: flex; flex-direction: column;">
    
    <div style="padding: 20px; background: #1a0b45; color: white; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #f49d1a;">
        <h3 style="margin: 0; font-size: 1.1rem;"><i class="fas fa-robot"></i> Techiftiin AI</h3>
        <button onclick="toggleAI()" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.5rem;">&times;</button>
    </div>
    
    <div id="aiResponse" style="padding: 20px; flex: 1; overflow-y: auto; background-color: #f8fafc; display: flex; flex-direction: column; gap: 12px;">
        <!-- Initial Greeting -->
        <div style="background: white; border: 1px solid #e2e8f0; padding: 12px; border-radius: 10px; font-size: 0.9rem;">
            Ask me anything about your current lesson or the MERN Stack!
        </div>
    </div>

    <!-- Input Area -->
    <div style="padding: 20px; border-top: 1px solid #e2e8f0;">
        <div style="display: flex; gap: 8px;">
            <input type="text" id="aiInput" 
                   onkeypress="if(event.key === 'Enter') askAI()"
                   placeholder="Type your custom question here..." 
                   style="flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
            
            <button onclick="askAI()" 
                    style="background: #8b5cf6; color: white; border: none; padding: 0 15px; border-radius: 8px; cursor: pointer;">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<!-- Background Dimmer (Overlay) -->
<div id="aiOverlay" onclick="toggleAI()" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); z-index: 2999; backdrop-filter: blur(2px);"></div>



<script>
    function openSettingsModal() {
    document.getElementById('settingsModal').style.display = 'block';
}

function closeSettingsModal() {
    document.getElementById('settingsModal').style.display = 'none';
}

// Update your window.onclick to handle both modals
window.onclick = function(event) {
    let textModal = document.getElementById('textModal');
    let settingsModal = document.getElementById('settingsModal');
    
    if (event.target == textModal) { closeModal(); }
    if (event.target == settingsModal) { closeSettingsModal(); }
}
    function showText(title, content) {
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalBody').innerText = content;
        document.getElementById('textModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('textModal').style.display = 'none';
    }

    // Close modal if user clicks outside of the white box
    window.onclick = function(event) {
        let modal = document.getElementById('textModal');
        if (event.target == modal) { closeModal(); }
    }




    function checkStrength(password) {
    if (password === "") {
        document.getElementById('password-criteria').style.display = "none";
        return;
    }
    document.getElementById('password-criteria').style.display = "block";

    const hasUpperLower = /[a-z]/.test(password) && /[A-Z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[@$!%*#?&]/.test(password);

    updateCriteria('crit-upper', hasUpperLower);
    updateCriteria('crit-number', hasNumber);
    updateCriteria('crit-special', hasSpecial);
}

function updateCriteria(id, isValid) {
    const el = document.getElementById(id);
    if (isValid) {
        el.style.color = "#27ae60";
        el.innerHTML = "✔ " + el.innerHTML.substring(2);
    } else {
        el.style.color = "#e74c3c";
        el.innerHTML = "✖ " + el.innerHTML.substring(2);
    }
}

function validatePassword() {
    const pass = document.getElementById('new_password').value;
    const confirm = document.getElementById('confirm_password').value;

    if (pass === "") return true; // Allow update if password is left blank

    // Check if match
    if (pass !== confirm) {
        alert("Passwords do not match!");
        return false;
    }

    // Check criteria
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;
    if (!regex.test(pass)) {
        alert("Password does not meet security criteria!");
        return false;
    }

    return true;
}








// 1. Define the toggle function in the global scope
function toggleAI() {
    const sidebar = document.getElementById('aiSidebar');
    const overlay = document.getElementById('aiOverlay');
    
    if (!sidebar) return;

    // Direct style check handles the initial "empty string" state
    if (sidebar.style.right === "0px") {
        sidebar.style.right = "-450px";
        if (overlay) overlay.style.display = "none";
    } else {
        sidebar.style.right = "0px";
        if (overlay) overlay.style.display = "block";
    }
}

// 2. Define the ask function using Fetch API
async function askAI() {
    const inputField = document.getElementById('aiInput');
    const responseArea = document.getElementById('aiResponse');
    
    if (!inputField || !responseArea) return;
    
    const query = inputField.value.trim();
    if (query === "") return;

    // Display User Message
    const userDiv = document.createElement('div');
    userDiv.style.cssText = "align-self: flex-end; background: #8b5cf6; color: white; padding: 10px; border-radius: 10px; margin-bottom: 10px; max-width: 80%;";
    userDiv.textContent = query;
    responseArea.appendChild(userDiv);
    
    // Clear input and scroll
    inputField.value = "";
    responseArea.scrollTop = responseArea.scrollHeight;

    // Prepare Form Data for PHP
    const formData = new FormData();
    formData.append('query', query);
    formData.append('course_id', "<?php echo isset($course_id) ? $course_id : 0; ?>");

    try {
        const response = await fetch('process_ai.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.text();

        // Display AI Response
        const aiDiv = document.createElement('div');
        aiDiv.style.cssText = "align-self: flex-start; background: white; border: 1px solid #e2e8f0; padding: 10px; border-radius: 10px; margin-bottom: 10px; max-width: 85%;";
        aiDiv.innerHTML = data; // Use innerHTML because PHP returns formatted HTML
        responseArea.appendChild(aiDiv);
        
        responseArea.scrollTop = responseArea.scrollHeight;

    } catch (error) {
        console.error('Error:', error);
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = "align-self: flex-start; background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 10px; margin-bottom: 10px;";
        errorDiv.textContent = "Sorry, I couldn't connect to the AI. Please try again.";
        responseArea.appendChild(errorDiv);
    }
}
</script>



<script src="/lms_tech/lang.js"></script>


</body>
</html>