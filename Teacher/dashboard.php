<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
// Security: Only Teachers allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Fetch only courses assigned to THIS teacher by the Admin
$sql = "SELECT * FROM courses WHERE teacher_id = '$teacher_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Teacher Dashboard - LMS</title>
    
<style>
    /* 1. Global Reset for better spacing */
    * { box-sizing: border-box; }
    
    body { 
        font-family: 'Segoe UI', Roboto, sans-serif; 
        background: #f4f7f6; 
        margin: 0; 
    }

    .teacher-container { 
        padding: 40px; 
        max-width: 1600px; /* Increased for large screens */
        margin: 0 auto; 
    }

    /* 2. Optimized Grid for Large Screens */
    .course-grid { 
        display: grid; 
        /* Changed 300px to 350px to prevent "cramping" on desktop */
        grid-template-columns: repeat(3, 1fr); 
        gap: 30px; /* More space between cards */
        margin-top: 30px; 
    }

    /* 3. Card UI */
    .course-card { 
        background: white; 
        padding: 30px; 
        border-radius: 12px; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
        border-top: 5px solid #3498db;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .course-card h3 { margin-top: 10px; font-size: 1.4rem; color: #1a0b45; }

    .btn-manage { 
        display: block;
        text-align: center;
        margin-top: 20px; 
        padding: 12px; 
        background: #3498db; 
        color: white; 
        text-decoration: none; 
        border-radius: 8px; 
        font-weight: 600;
    }

    /* 4. THE FIX FOR SMALL DEVICES */
    @media (max-width: 768px) {
        .teacher-container { padding: 20px 15px; }
        
        .course-grid { 
            /* This forces EXACTLY 1 column on mobile */
            grid-template-columns: 1fr !important; 
            gap: 20px;
        }

        header {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .course-card { padding: 20px; }
        
        /* Modal becomes full screen on mobile for better UI */
        .modal-content {
            width: 95% !important;
            margin: 5% auto !important;
            padding: 15px;
        }
    }

    /* Modal Styling */
    .modal {
        display: none; position: fixed; z-index: 1000; left: 0; top: 0;
        width: 100%; height: 100%; background-color: rgba(0,0,0,0.6);
        backdrop-filter: blur(3px);
    }
    .modal-content {
        background-color: white; margin: 50px auto; padding: 30px;
        width: 60%; border-radius: 15px; max-height: 80vh; overflow-y: auto;
    }

    /* Settings Section Styling */
.settings-box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    margin-top: 40px;
    border-top: 5px solid #2c3e50;
}

.settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.settings-group { margin-bottom: 15px; }

.settings-group label { display: block; font-weight: 600; margin-bottom: 5px; color: #34495e; }

.settings-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #dcdde1;
    border-radius: 6px;
    font-size: 1rem;
}

.btn-save {
    background: #2c3e50;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

.btn-save:hover { background: #1a0b45; }

@media (max-width: 768px) {
    .settings-grid { grid-template-columns: 1fr; }
}

.fa-cog {
    transition: transform 0.3s ease;
}
.fa-cog:hover {
    transform: rotate(90deg); /* Makes the gear spin when you hover! */
    color: #1a0b45;
}
</style>
</head>
<body>
   

<div class="teacher-container">
<header style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
    <h1 style="font-size: 1.5rem; color: #1a0b45;" data-lang="teacher_portal">Teacher Portal</h1>
    <div style="display: flex; align-items: center; gap: 20px;">
        <span> <span data-lang="welcome">Welcome</span>, <strong><?php echo $_SESSION['user_name']; ?></strong></span>
        <button onclick="openModal('settingsModal')" style="background: none; border: none; color: #3498db; cursor: pointer; font-size: 1.2rem;">
            <i class="fas fa-cog"></i>
        </button>
        <a href="../auth/logout.php" style="color: #e74c3c; text-decoration: none; font-weight: 600;" data-lang="logout">Logout</a>
    </div>
</header>



<?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
    <div style="background: #27ae60; color: white; padding: 12px; border-radius: 8px; margin: 20px 0; text-align: center; font-weight: bold;">
        ✅ <span data-lang="profile_success">Profile updated successfully!</span>
    </div>
<?php endif; ?>


<div style="margin-bottom: 30px;">
    <div class="course-card" style="border-top: 5px solid #e67e22; display: flex; justify-content: space-between; align-items: center; background: #fff;">
        <div>
            <h3 style="margin: 0; color: #e67e22;" data-lang="student_submissions_title">Student Submissions</h3>
            <p style="margin: 5px 0 0 0; color: #7f8c8d;" data-lang="student_submissions_desc">Review and download the latest assignment answers from your students.</p>
        </div>
        <div>
            <a href="view_submissions.php" class="btn-manage" style="background: #e67e22; margin-top: 0;" data-lang="btn_view_all_answers">
                View All Answers
            </a>
        </div>
    </div>
</div>



<!-- Add this section right below your "Student Submissions" card and above the <hr> -->

<div style="margin-bottom: 30px;">
    <div class="settings-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        
        <!-- Existing Submissions Card -->
        <div class="course-card" style="border-top: 5px solid #e67e22; flex-direction: row; align-items: center; justify-content: space-between;">
            <div>
                <h3 style="margin: 0; color: #e67e22;"><i class="fas fa-file-import"></i> <span data-lang="submissions">Submissions</span></h3>
                <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 0.9rem;" data-lang="submissions_desc">Review assignment answers.</p>
            </div>
            <a href="view_submissions.php" class="btn-manage" style="background: #e67e22; margin-top: 0; padding: 10px 15px;" data-lang="btn_view">View</a>
        </div>

        <!-- NEW: Student Report Cards Center -->
        <div class="course-card" style="border-top: 5px solid #1a0b45; flex-direction: row; align-items: center; justify-content: space-between;">
            <div>
                <h3 style="margin: 0; color: #1a0b45;"><i class="fas fa-id-card"></i> <span data-lang="report_cards">Report Cards</span></h3>
                <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 0.9rem;" data-lang="report_cards_desc">Generate official TechIftiin Student Cards.</p>
            </div>
            <a href="generate_reports_list.php" class="btn-manage" style="background: #1a0b45; margin-top: 0; padding: 10px 15px;" data-lang="btn_generate">Generate</a>
        </div>

    </div>
</div>

<hr>





        <hr>

        <h2 data-lang="my_assigned_courses">My Assigned Courses</h2>

<div class="course-grid">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            
            <div class="course-card" style="border-top: 5px solid #3498db;">
                <span style="font-size: 0.7rem; color: #7f8c8d; text-transform: uppercase;" data-lang="curriculum">Curriculum</span>
                <h3><?php echo $row['title']; ?></h3>
                <p><?php echo substr($row['description'], 0, 80); ?>...</p>
                <a href="manage_lessons.php?course_id=<?php echo $row['id']; ?>" class="btn-manage" data-lang="btn_manage_lessons">
                    Manage Lessons & Materials
                </a>
            </div>

            <div class="course-card" style="border-top: 5px solid #27ae60;">
                <span style="font-size: 0.7rem; color: #7f8c8d; text-transform: uppercase;">Assessment</span>
                <h3><?php echo $row['title']; ?></h3>
                <p data-lang="assessment_desc">View registered students, track attendance, and update final grades.</p>
                <a href="manage_students.php?course_id=<?php echo $row['id']; ?>" 
                   class="btn-manage" style="background: #27ae60;" data-lang="btn_grade_students">
                    Grade Students
                </a>
            </div>


<div class="course-card" style="border-top: 5px solid #9b59b6;">
    <span style="font-size: 0.7rem; color: #7f8c8d; text-transform: uppercase;" data-lang="class_list">Class List</span>
    <h3><?php echo $row['title']; ?></h3>
    <p data-lang="class_list_desc">View all enrolled students and their contact information in one list.</p>
    <button onclick="openModal('modal-<?php echo $row['id']; ?>')" class="btn-manage" style="background: #9b59b6; border:none; cursor:pointer; width:100%;" data-lang="btn_view_student_list">
        View Student List
    </button>
</div>



<div class="course-card" style="border-top: 5px solid #f39c12;">
    <span style="font-size: 0.7rem; color: #7f8c8d; text-transform: uppercase;">Daily Log</span>
    <h3><?php echo $row['title']; ?></h3>
    <p data-lang="daily_log_desc">Record daily student presence and track attendance history for this course.</p>
    <a href="take_attendance.php?course_id=<?php echo $row['id']; ?>" 
       class="btn-manage" style="background: #f39c12;">
       <i class="fas fa-calendar-check"></i> <span data-lang="btn_take_attendance">Take Attendance</span>
    </a>
</div>





<div id="modal-<?php echo $row['id']; ?>" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('modal-<?php echo $row['id']; ?>')">&times;</span>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 style="margin: 0; color: #2c3e50;"><?php echo $row['title']; ?> - Students</h2>
            <a href="generate_class_report.php?course_id=<?php echo $row['id']; ?>" target="_blank" 
               style="background: #e74c3c; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 0.8rem;" data-lang="btn_export_pdf">
               📄 Export PDF Report
            </a>
        </div>
        <hr>

        <div class="student-list">
            <?php 
            $c_id = $row['id'];
            $st_query = $conn->query("SELECT id, name, phone, email FROM users WHERE role = 'student' AND course_id = '$c_id'");
            
            if($st_query->num_rows > 0):
                while($s = $st_query->fetch_assoc()): 
                    $st_id = $s['id'];
                    
                    // Fetch all grades to sum up the Raw Points
                    $grades = $conn->query("SELECT score_out_of_100, weight FROM grades WHERE student_id = '$st_id' AND course_id = '$c_id'");
                    
                    $earned_points = 0;
                    $total_possible = 0;
                    
                    while($g = $grades->fetch_assoc()){
                        $earned_points += $g['score_out_of_100'];
                        $total_possible += $g['weight']; // Weight is our 'Total Points'
                    }
                    
                    // Calculate Percentage safely
                    $percentage = ($total_possible > 0) ? ($earned_points / $total_possible) * 100 : 0;
            ?>
                <div class="student-item">
                    <div>
                        <strong><?php echo $s['name']; ?></strong><br>
                        <small style="color:#7f8c8d;">📧 <?php echo $s['email']; ?></small><br>
                        <small style="color:#7f8c8d;">📞 <?php echo $s['phone']; ?></small>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-weight: bold; color: #2c3e50;">
                            <?php echo $earned_points; ?> / <?php echo $total_possible; ?>
                        </span>
                        <span class="grade-tag" style="margin-left: 8px;">
                            <?php echo round($percentage, 1); ?>%
                        </span>
                        <br>
                        <a href="manage_students.php?course_id=<?php echo $c_id; ?>&student_id=<?php echo $st_id; ?>" 
                           style="font-size: 0.7rem; color: #3498db; text-decoration: none;" data-lang="btn_manage_small">Manage</a>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <p style="padding:20px; text-align:center;" data-lang="no_students_registered">No students registered.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-courses" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
            <h3 data-lang="no_courses_assigned">No courses assigned yet.</h3>
            <p data-lang="contact_admin_desc">Please contact the Admin to get started.</p>
        </div>
    <?php endif; ?>
</div>






    </div>




    <div id="settingsModal" class="modal">
    <div class="modal-content" style="width: 400px;">
        <span class="close-btn" onclick="closeModal('settingsModal')" style="float: right; cursor: pointer; font-size: 1.5rem;">&times;</span>
        
        <h2 style="color: #1a0b45; margin-top: 0;" data-lang="account_settings">Account Settings</h2>
        <p style="color: #7f8c8d; font-size: 0.85rem; margin-bottom: 20px;" data-lang="settings_subtitle">Update your login credentials.</p>

        <form action="update_profile.php" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;" data-lang="email_address">Email Address</label>
                <input type="email" name="email" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;" data-lang="new_password">New Password</label>
                <input type="password" name="new_password" placeholder="Leave blank to keep current" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
            </div>

            <button type="submit" class="btn-manage" style="width: 100%; border: none; cursor: pointer;" data-lang="btn_save_changes">
                Save Changes
            </button>
        </form>
    </div>
</div>

    <script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = "block";
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Close the modal if the teacher clicks anywhere outside the white box
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = "none";
    }
}
</script>









  <script src="http://localhost/lms_tech/lang.js"></script>






</body>
</html>