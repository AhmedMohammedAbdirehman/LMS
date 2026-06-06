<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$course_id = $_GET['course_id'];
$msg = "";

if (isset($_POST['add_lesson'])) {
    $part = mysqli_real_escape_string($conn, $_POST['part_number']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = $_POST['content_type'];
    $final_path = "";
    $category = $_POST['category'];

// 1. Handle File Upload (With Restrictions)
    if (!empty($_FILES['file_upload']['name'])) {
        $file = $_FILES['file_upload'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['pdf', 'zip'];
        $max_size = 10 * 1024 * 1024; // 10MB in bytes

        // Validation Checks
        if (!in_array($file_ext, $allowed_exts)) {
            $msg = "<p style='color:red;'>Error: Only PDF and ZIP files are allowed.</p>";
        } elseif ($file['size'] > $max_size) {
            $msg = "<p style='color:red;'>Error: File size exceeds 10MB limit.</p>";
        } else {
            // Proceed with upload if checks pass
            $file_name = time() . '_' . basename($file['name']);
            $target_dir = "../uploads/";
            
            if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $final_path = "uploads/" . $file_name; 
            }
        }
    }
    // 2. Handle Link Input (If Browse was empty)
    elseif (!empty($_POST['link_input'])) {
        $final_path = mysqli_real_escape_string($conn, $_POST['link_input']);
    }

    // 3. Insert into Database
    if ($final_path != "") {
       $sql = "INSERT INTO lessons (course_id, part_number, title, content_type, category, file_path_or_link) 
        VALUES ('$course_id', '$part', '$title', '$type', '$category', '$final_path')";
        
        if ($conn->query($sql)) {
            // --- LOGGING START ---
            $c_data = $conn->query("SELECT title FROM courses WHERE id = '$course_id'")->fetch_assoc();
            $course_name = $c_data['title'] ?? 'Unknown Course';
            $teacher_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;

            $details = "Teacher added $category: $title (Part $part) to $course_name";

            if (function_exists('logActivity')) {
                logActivity($conn, $teacher_id, 'INSERT', $details);
            }
// --- LOGGING END ---
            $msg = "<p style='color:green;'>Material added successfully!</p>";
        } else {
            $msg = "<p style='color:red;'>Database Error: " . $conn->error . "</p>";
        }
    } else {
        $msg = "<p style='color:red;'>Please upload a file or provide a link.</p>";
    }
}



// Handle Deletion of a lesson
if (isset($_GET['delete_lesson_id'])) {
    $lesson_id = $_GET['delete_lesson_id'];
    
    // Optional: Delete the physical file from the /uploads folder if it exists
    // --- LOGGING START ---
$l_data = $conn->query("SELECT title, category FROM lessons WHERE id = '$lesson_id'")->fetch_assoc();
$c_data = $conn->query("SELECT title FROM courses WHERE id = '$course_id'")->fetch_assoc();

$lesson_title = $l_data['title'] ?? 'Unknown Material';
$course_name = $c_data['title'] ?? 'Unknown Course';
$teacher_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;

$details = "Teacher deleted " . ($l_data['category'] ?? 'material') . ": $lesson_title from $course_name";

if (function_exists('logActivity')) {
    logActivity($conn, $teacher_id, 'DELETE', $details);
}
// --- LOGGING END ---
    $file_res = $conn->query("SELECT file_path_or_link FROM lessons WHERE id = '$lesson_id'");
    $file_data = $file_res->fetch_assoc();
    
    if (file_exists("../" . $file_data['file_path_or_link'])) {
        unlink("../" . $file_data['file_path_or_link']); // Deletes the actual file
    }

    $conn->query("DELETE FROM lessons WHERE id = '$lesson_id'");
    header("Location: manage_lessons.php?course_id=$course_id&msg=Material Deleted");
    exit();
}


// Fetch only the teaching materials
$materials = $conn->query("SELECT * FROM lessons WHERE course_id = '$course_id' AND category = 'material' ORDER BY part_number ASC");

// Fetch everything else (Assignments, Quizzes, Exams)
$assessments = $conn->query("SELECT * FROM lessons WHERE course_id = '$course_id' AND category != 'material' ORDER BY category ASC");
?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Manage Content</title>
<style>
    /* SaaS-style Variables */
:root {
    --primary: #1a0b45;
    --secondary: #3b82f6;
    --accent: #f59e0b;
    --danger: #ef4444;
    --bg: #f1f5f9;
    --glass: rgba(255, 255, 255, 0.9);
}

   body { 
    font-family: 'Plus Jakarta Sans', sans-serif; 
    background-color: var(--bg);
}

    /* The Responsive Wrapper */
    .sections-wrapper {
        display: grid;
        grid-template-columns: 1fr; /* Default: Stacked for mobile */
        gap: 30px;
        margin-top: 30px;
        width: 100%;
    }

    /* Large Device Logic: Side-by-Side */
    @media (min-width: 992px) {
        .sections-wrapper {
            grid-template-columns: 1fr 1fr; /* Two equal columns on Desktop */
        }
    }




    .login-container { 
        max-width: 800px; 
        background: white; 
        padding: 25px; 
        border-radius: 12px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
    }

.content-badge { 
    font-size: 0.7rem; 
    padding: 4px 10px; 
    border-radius: 20px; /* Pill shape */
    font-weight: 600; 
    letter-spacing: 0.3px;
}
    .pdf { background: #fee2e2; color: #b91c1c; }
    .video { background: #fef9c3; color: #854d0e; }
    .text { background: #dcfce7; color: #15803d; }

    /* Container for the form rows */
.form-grid-row {
    display: grid;
    grid-template-columns: 1fr; /* Default: Stacked for mobile */
    gap: 20px;
    margin-bottom: 15px;
}

/* 1. Make the container stretch full width but keep a clean margin */

/* The Form Container */
.login-container {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 16px !important;
    padding: 32px !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
}

/* Create a 2-column grid for the form */
.form-body-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* Make certain items span full width (like Title or Save button) */
.full-width {
    grid-column: span 2;
}

/* Refined Labels & Inputs */
.input-field-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.login-container input, 
.login-container select {
    padding: 12px 16px !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    margin-bottom: 0 !important; /* Managed by grid gap now */
}

/* Mobile Fallback */
@media (max-width: 768px) {
    .form-body-grid {
        grid-template-columns: 1fr;
    }
    .full-width {
        grid-column: span 1;
    }
}

    .login-container input:focus, 
    .login-container select:focus {
        border-color: #3498db !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1) !important;
        outline: none !important;
    }

    /* 3. Button Styling */
    .btn-login {
        width: 100% !important;
        background: #1a0b45 !important; /* Matching your portal color */
        color: white !important;
        padding: 14px !important;
        border-radius: 8px !important;
        border: none !important;
        font-weight: 600 !important;
        font-size: 1rem !important;
        cursor: pointer !important;
        transition: 0.3s !important;
    }

    .btn-login:hover {
        background: #2c166b !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(26, 11, 69, 0.2) !important;
    }

    /* 4. Labels */
    label {
        font-size: 0.9rem !important;
        color: #475569 !important;
        margin-bottom: 8px !important;
        font-weight: 600 !important;
    }

    /* 5. Desktop Optimization: Prevent the form from looking TOO stretched on 4K monitors */
    @media (min-width: 1400px) {
        .login-container {
            padding: 40px 60px !important;
        }
        
        /* Optional: If you want the form narrower on huge screens but full on laptop/tablet */
        /* .login-container { max-width: 1200px !important; margin: 20px auto !important; } */
    }

    /* Mobile adjustments */
    @media (max-width: 600px) {
        .login-container {
            padding: 20px 15px !important;
        }
    }
    input[type="file"] {
    background: #f8fafc;
    border: 2px dashed #e2e8f0 !important;
    padding: 15px !important;
    cursor: pointer;
}

h2 { font-weight: 700; color: var(--primary); letter-spacing: -0.5px; }

/* Enhanced Section Headers */
h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 12px;
}

/* Container for the cards */
.section-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.part-section {
    background: #ffffff;
    border: 1px solid #e2e8f0; /* Soft border instead of heavy box shadow */
    border-left: 4px solid var(--secondary);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.part-section:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: var(--secondary);
    transform: translateY(-4px);
}

/* Typography refinement */
.part-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
    line-height: 1.4;
}

.part-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
}
.swal2-popup {
    border-radius: 16px !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

.swal2-styled.swal2-confirm {
    border-radius: 8px !important;
    padding: 10px 25px !important;
}

.swal2-styled.swal2-cancel {
    border-radius: 8px !important;
    background-color: #f1f5f9 !important;
    color: #475569 !important;
    padding: 10px 25px !important;
}
</style>


</style>
</head>
<body style="padding: 20px; background: #f4f7f6; font-family: sans-serif;">

    <h2 data-lang="manage_content_title">Manage Course Content</h2>
    <a href="dashboard.php" style="text-decoration: none; color: #3498db;">
        ← <span data-lang="back_to_dashboard">Back to Dashboard</span>
    </a>

    <div class="login-container">
        <div style="margin-bottom: 15px;">
        <?php echo $msg; ?>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-body-grid">
            
            <!-- Row 1 -->
            <div class="input-field-group">
                <label data-lang="label_category">Category</label>
                <select name="category">
                    <option value="material" data-lang="label_category">Lesson Material</option>
                    <option value="assignment" data-lang="opt_assignment">Assignment</option>
                    <option value="quiz" data-lang="opt_quiz">Quiz</option>
                    <option value="exam" data-lang="opt_exam">Mid/Final Exam</option>
                </select>
            </div>

            <div class="input-field-group">
                <label data-lang="label_content_type">Content Type</label>
                <select name="content_type" id="typeSelect" onchange="updateUI()">
                    <option value="pdf" data-lang="opt_pdf">PDF Document</option>
                    <option value="video" data-lang="opt_video">Video (Link)</option>
                    <option value="text" data-lang="opt_text">Text Instructions</option>
                </select>
            </div>

            <!-- Row 2 -->
            <div class="input-field-group" style="grid-column: span 0.5;">
                <label data-lang="label_part_number">Part Number</label>
                <input type="number" name="part_number" placeholder="e.g. 1" data-lang-placeholder="placeholder_part" required>
            </div>

            <div class="input-field-group" style="flex-grow: 1;">
                <label data-lang="label_material_title">Material Title</label>
                <input type="text" name="title" placeholder="e.g. Introduction to AI" data-lang-placeholder="placeholder_title" required>
            </div>

            <!-- Row 3: Conditional Uploads (Full Width) -->
            <div class="input-field-group full-width" id="fileDiv">
                <label data-lang="label_upload_file">Upload File (PDF/ZIP)& Max: 10MB</label>
                <input type="file" name="file_upload" accept=".pdf,.zip">
            </div>

            <div class="input-field-group full-width" id="linkDiv" style="display:none;">
                <label id="linkLabel" data-lang="label_paste_link">Paste Link (URL)</label>
                <input type="text" name="link_input" placeholder="https://..." data-lang-placeholder="placeholder_url">
            </div>

            <!-- Row 4: Submit Button -->
            <div class="full-width" style="margin-top: 10px;">
                <button type="submit" name="add_lesson" class="btn-login">
                 🚀 <span data-lang="btn_save_content">Save Content</span>
                </button>
            </div>

        </div>
    </form>
</div>
  <hr>

  <div class="sections-wrapper">
    
    <!-- Section 1: Learning Materials -->
    <div>
        <h3 style="border-bottom: 2px solid #3498db; padding-bottom: 10px;">
            📚 <span data-lang="section_1_title">Section 1: Learning Materials</span>
        </h3>
        <?php if($materials && $materials->num_rows > 0): ?>
            <?php while($row = $materials->fetch_assoc()): ?>
                <div class="part-section">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong><span data-lang="part_label">Part</span> <?php echo $row['part_number']; ?>: <?php echo $row['title']; ?></strong> 
                            <span class="content-badge <?php echo $row['content_type']; ?>"><?php echo $row['content_type']; ?></span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 15px;">
                            <?php if($row['content_type'] == 'text'): ?>
                                <button onclick="alert('Content: <?php echo htmlspecialchars($row['file_path_or_link'], ENT_QUOTES); ?>')" 
                                        style="background: #eff6ff; color: #2563eb; padding: 6px 12px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer;" data-lang="btn_view_text">
                                    View Text
                                </button>
                            <?php else: ?>
                                <a href="../<?php echo $row['file_path_or_link']; ?>" target="_blank" 
                                   style="text-decoration: none; color: #2563eb; font-weight: 600; font-size: 0.9rem;" data-lang="btn_view_file">
                                    View File
                                </a>
                            <?php endif; ?>
                        <a href="javascript:void(0);" 
                        style="color: #fca5a5; text-decoration: none; transition: 0.2s; display: flex; align-items: center;" 
                        onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#fca5a5'"
                        onclick="confirmDelete('<?php echo $course_id; ?>', '<?php echo $row['id']; ?>')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                        </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #7f8c8d; padding: 20px; text-align: center;" data-lang="no_materials">No materials added yet.</p>
        <?php endif; ?>
    </div>

    <!-- Section 2: Assignments & Exams -->
    <div>
        <h3 style="border-bottom: 2px solid #e67e22; padding-bottom: 10px;">
            📝 <span data-lang="section_2_title">Section 2: Assignments & Exams</span>
        </h3>
        <?php if($assessments && $assessments->num_rows > 0): ?>
            <?php while($row = $assessments->fetch_assoc()): ?>
                <div class="part-section" style="border-left: 5px solid #e67e22;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="text-transform: uppercase; color: #e67e22; font-size: 0.75rem;">[<?php echo $row['category']; ?>]</strong><br>
                            <strong><?php echo $row['title']; ?></strong>
                        </div>

                        <div style="display: flex; align-items: center; gap: 15px;">
                            <?php if($row['content_type'] == 'text'): ?>
                                <button onclick="alert('Instructions: <?php echo htmlspecialchars($row['file_path_or_link'], ENT_QUOTES); ?>')" 
                                        style="background: #fff7ed; color: #ea580c; padding: 6px 12px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer;">
                                    View
                                </button>
                            <?php else: ?>
                                <a href="../<?php echo $row['file_path_or_link']; ?>" target="_blank" 
                                   style="text-decoration: none; color: #ea580c; font-weight: 600; font-size: 0.9rem;">
                                    View File
                                </a>
                            <?php endif; ?>

                            <!-- Using the same Graceful Trash Icon for Section 2 -->
                  <a href="javascript:void(0);" 
                    style="color: #fca5a5; text-decoration: none; transition: 0.2s; display: flex; align-items: center;" 
                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#fca5a5'"
                    onclick="confirmDelete('<?php echo $course_id; ?>', '<?php echo $row['id']; ?>')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                        </svg>
                    </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #7f8c8d; padding: 20px; text-align: center;" data-lang="no_assessments">No assignments or exams added yet.</p>
        <?php endif; ?>
    </div>

</div>

    <script>
        function updateUI() {
            var type = document.getElementById('typeSelect').value;
            var fileDiv = document.getElementById('fileDiv');
            var linkLabel = document.getElementById('linkLabel');

            if(type === 'video') {
                fileDiv.style.display = 'none';
                linkLabel.innerHTML = "Paste Video Link";
            } else if(type === 'text') {
                fileDiv.style.display = 'none';
                linkLabel.innerHTML = "Type Instructions or Quiz text here";
            } else {
                fileDiv.style.display = 'block';
                linkLabel.innerHTML = "Or Paste Link (URL)";
            }
        }
        // Run on load
        updateUI();




       
function confirmDelete(courseId, lessonId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This material and its associated files will be permanently removed.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a0b45', // Matches your primary theme color
        cancelButtonColor: '#cbd5e1',  // Neutral grey
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        reverseButtons: true, // Puts 'Confirm' on the right side
        backdrop: `rgba(26, 11, 69, 0.2)`, // Soft tinted background
        padding: '2em',
        customClass: {
            popup: 'swal2-border-radius-16' // We can add custom CSS for this
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Trigger a loading state so the user knows it's working
            Swal.fire({
                title: 'Deleting...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // Redirect to PHP deletion logic
            window.location.href = `manage_lessons.php?course_id=${courseId}&delete_lesson_id=${lessonId}`;
        }
    });
}


    </script>









      <script src="http://localhost/lms_tech/lang.js"></script>

</body>
</html>