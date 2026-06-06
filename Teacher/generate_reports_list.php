<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
$teacher_id = $_SESSION['user_id'];

// Get all students enrolled in any of this teacher's courses
$query = "SELECT u.id as student_id, u.name as student_name, c.title as course_name, c.id as course_id 
          FROM users u 
          JOIN courses c ON FIND_IN_SET(c.id, u.course_id)
          WHERE c.teacher_id = '$teacher_id' AND u.role = 'student'
          ORDER BY c.title, u.name";
// --- NEW: PAGINATION SETTINGS ---
$limit = 8; // Number of students per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total count for the pagination bar
$count_query = "SELECT COUNT(*) as total FROM users u 
                JOIN courses c ON FIND_IN_SET(c.id, u.course_id)
                WHERE c.teacher_id = '$teacher_id' AND u.role = 'student'";
$total_students = $conn->query($count_query)->fetch_assoc()['total'];
$total_pages = ceil($total_students / $limit);

// Updated query with LIMIT and OFFSET
$query = "SELECT u.id as student_id, u.name as student_name, c.title as course_name, c.id as course_id 
          FROM users u 
          JOIN courses c ON FIND_IN_SET(c.id, u.course_id)
          WHERE c.teacher_id = '$teacher_id' AND u.role = 'student'
          ORDER BY c.title, u.name
          LIMIT $limit OFFSET $offset";
$result = $conn->query($query);






// Query to count students with C and above (Average score >= 60)
$high_achievers_query = "SELECT COUNT(*) as high_count FROM (
    SELECT u.id
    FROM users u
    JOIN courses c ON FIND_IN_SET(c.id, u.course_id)
    JOIN grades g ON u.id = g.student_id AND c.id = g.course_id
    WHERE c.teacher_id = '$teacher_id' 
    GROUP BY u.id, c.id
    HAVING (SUM(g.score_out_of_100) / SUM(g.weight)) * 100 >= 60
) as achievers";
$high_achievers_count = $conn->query($high_achievers_query)->fetch_assoc()['high_count'];





// --- ARCHIVE PAGINATION SETTINGS ---
$archive_limit = 5; // Number of certified students per page
$archive_page = isset($_GET['arch_page']) ? (int)$_GET['arch_page'] : 1;
$archive_offset = ($archive_page - 1) * $archive_limit;

// Fetch total count for Archive
$arch_count_query = "SELECT COUNT(*) as total FROM certificates 
                     WHERE status = 'approved' AND teacher_id = '$teacher_id'";
$total_arch_students = $conn->query($arch_count_query)->fetch_assoc()['total'];
$total_arch_pages = ceil($total_arch_students / $archive_limit);



// --- 1. HANDLE POST ACTIONS (Keep this at the top) ---
$message = "";
$preview_result = null;
$target_id = $_POST['target_course_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $target_id) {
    
    // ACTION: Send to Admin
    if (isset($_POST['send_to_admin'])) {
        $send_query = "SELECT u.id as sid, u.name as sname, c.title as ctitle 
                       FROM users u 
                       JOIN courses c ON FIND_IN_SET(c.id, u.course_id)
                       JOIN grades g ON u.id = g.student_id AND c.id = g.course_id
                       WHERE c.id = '$target_id' AND c.teacher_id = '$teacher_id'
                       GROUP BY u.id 
                       HAVING (SUM(g.score_out_of_100) / SUM(g.weight)) * 100 >= 60";
        
        $to_certify = $conn->query($send_query);
        $count = 0;
        while($row = $to_certify->fetch_assoc()) {
            $sid = $row['sid'];
            $ctitle = $row['ctitle'];
            $check = $conn->query("SELECT id FROM certificates WHERE student_id = '$sid' AND course_name = '$ctitle'");
            if ($check->num_rows == 0) {
                $conn->query("INSERT INTO certificates (student_id, teacher_id, course_name, status) 
                              VALUES ('$sid', '$teacher_id', '$ctitle', 'pending')");
                $count++;
            }
        }
        $message = "Successfully sent $count students to Admin.";
    }

// ACTION: Always fetch preview if either button is clicked
// ACTION: Fetch only students who haven't been provided certificates yet
if (isset($_POST['show_preview']) || isset($_POST['send_to_admin'])) {
    $preview_query = "SELECT u.name as sname, u.id as sid, c.title as ctitle, 
                       cert.status as cert_status,
                      (SUM(g.score_out_of_100) / SUM(g.weight)) * 100 as final_grade
                      FROM users u 
                      JOIN courses c ON FIND_IN_SET(c.id, u.course_id)
                      JOIN grades g ON u.id = g.student_id AND c.id = g.course_id
                      LEFT JOIN certificates cert ON u.id = cert.student_id AND c.title = cert.course_name
                      WHERE c.teacher_id = '$teacher_id' AND c.id = '$target_id' 
                      AND (cert.status IS NULL OR cert.status != 'approved') 
                      GROUP BY u.id, c.id 
                      HAVING final_grade >= 60";
    $preview_result = $conn->query($preview_query);
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Reports | TechIftiin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap 5 JS Bundle (Includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #1a0b45;
            --accent-color: #3498db;
            --bg-color: #f4f7f6;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
        }

        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            background: var(--bg-color); 
            margin: 0; 
            padding: 20px;
            color: var(--text-dark);
        }

        .container { 
            max-width: 1000px; 
            margin: auto; 
        }

        .back-link {
            text-decoration: none;
            color: var(--accent-color);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .back-link:hover { transform: translateX(-5px); }

        .report-box { 
            background: white; 
            padding: 30px; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
        }

        .header-section {
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header-section h2 { 
            color: var(--primary-color); 
            margin: 0; 
            font-size: 1.8rem;
        }

        .header-section p { color: var(--text-light); margin: 5px 0 0 0; }

        /* Modern Table Styling */
        .table-responsive { overflow-x: auto; }

        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0 10px; 
            margin-top: 10px; 
        }

        th { 
            padding: 15px; 
            text-align: left; 
            color: var(--text-light); 
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        td { 
            padding: 20px 15px; 
            background: #fff;
            border-top: 1px solid #f0f2f5;
            border-bottom: 1px solid #f0f2f5;
        }

        tr td:first-child { border-left: 1px solid #f0f2f5; border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        tr td:last-child { border-right: 1px solid #f0f2f5; border-top-right-radius: 10px; border-bottom-right-radius: 10px; }

        .student-name { font-weight: 700; color: var(--primary-color); font-size: 1.05rem; }
        .course-tag { 
            background: #eef2ff; 
            color: #4338ca; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.85rem; 
            font-weight: 500;
        }

        .btn-view { 
            background: var(--primary-color); 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 8px; 
            font-size: 0.9rem; 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-view:hover { 
            background: #2a1563; 
            box-shadow: 0 4px 12px rgba(26, 11, 69, 0.2);
            transform: translateY(-2px);
        }

        /* Mobile Specific Card Layout */
@media screen and (max-width: 768px) {
    /* 1. Make the search bar and form buttons full width */
    #studentSearch, 
    .report-box form, 
    .report-box select, 
    .report-box button {
        width: 100% !important;
        box-sizing: border-box;
    }

    /* 2. Transform the High Achievers Table into cards */
/* Only hide headers for the main achievement table, NOT the modal */
table:not(.table-hover) thead {
    display: none;
}

    .report-table, .report-table tbody, .report-table tr, .report-table td {
        display: block;
        width: 100%;
    }

    .report-table tr {
        margin-bottom: 15px;
        border: 1px solid #eee;
        padding: 10px;
        background: #fff;
    }

    .report-table td {
        text-align: right;
        padding: 8px 10px;
        position: relative;
        border-bottom: 1px solid #f9f9f9;
    }

    /* Add labels manually for the preview table */
    .report-table td:nth-of-type(1):before { content: "Student:"; float: left; font-weight: bold; }
    .report-table td:nth-of-type(2):before { content: "Course:"; float: left; font-weight: bold; }
    .report-table td:nth-of-type(3):before { content: "Grade:"; float: left; font-weight: bold; }
    .report-table td:nth-of-type(4):before { content: "Status:"; float: left; font-weight: bold; }

    /* 3. Adjust Stat Cards */
    .stat-card {
        flex: 1 1 100% !important; /* Force cards to 100% width on mobile */
    }
}
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border-bottom: 4px solid #ddd;
    transition: transform 0.3s ease;
}

.stat-card:hover { transform: translateY(-5px); }

.stat-card i {
    font-size: 2rem;
    padding: 12px;
    border-radius: 10px;
}

/* Colors for specific cards */
.card-total { border-color: #3498db; }
.card-total i { background: #ebf5fb; color: #3498db; }

.card-high { border-color: #27ae60; }
.card-high i { background: #eafaf1; color: #27ae60; }

.stat-info h3 { margin: 0; font-size: 1.6rem; color: #1a0b45; }
.stat-info p { margin: 5px 0 0 0; color: #7f8c8d; font-size: 0.85rem; font-weight: 600; }
/* Update labels manually for the preview table in the mobile CSS section */
.report-table td:nth-of-type(4):before { content: "Status:"; float: left; font-weight: bold; }
    </style>
</head>
<body>


<div class="container">
    <a href="dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> 
        <span data-lang="back_to_teacher_dashboard">Back to Teacher Dashboard</span>
    </a>


    <!-- NEW: SEARCH INPUT -->
<div style="position: relative; margin-bottom: 20px;">
    <i class="fas fa-search" style="position: absolute; left: 15px; top: 18px; color: #aaa;"></i>
    <input type="text" id="studentSearch" placeholder="Search by name or course..." data-lang-placeholder="search_placeholder"
           style="width: 100%; padding: 15px 15px 15px 45px; border-radius: 12px; border: 2px solid #ddd; outline: none; box-sizing: border-box;">
</div>


<div class="stats-container" style="display: flex; flex-wrap: wrap; gap: 15px;">
    <!-- Card 1: Total Students Enrolled -->
    <div class="stat-card card-total">
        <i class="fas fa-users"></i>
        <div class="stat-info">
            <h3><?php echo $total_students; ?></h3>
            <p data-lang="total_students_label">TOTAL STUDENTS</p>
        </div>
    </div>

        <!-- Card 2: Students with C and Above -->
    <div class="stat-card card-high">
        <i class="fas fa-trophy"></i>
        <div class="stat-info">
            <h3><?php echo $high_achievers_count; ?></h3>
            <p data-lang="grade_c_above_label">GRADE C & ABOVE</p>
        </div>
    </div>


        <!-- Card 3: Courses You Teach -->
    <div class="stat-card" style="border-color: #9b59b6;">
        <i class="fas fa-book" style="background: #f5eef8; color: #9b59b6;"></i>
        <div class="stat-info">
            <h3><?php 
                $c_count = $conn->query("SELECT COUNT(*) as c FROM courses WHERE teacher_id = '$teacher_id'")->fetch_assoc()['c'];
                echo $c_count; 
            ?></h3>
            <p data-lang="active_courses_label">ACTIVE COURSES</p>
        </div>
    </div>

    
<!-- Display Success Message -->
<?php if($message): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #28a745;">
        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="report-box" style="margin-bottom: 30px; border-top: 5px solid #f1c40f;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="margin:0; color: #2c3e50; font-size:14px"><i class="fas fa-award"></i> <span data-lang="high_achievers_title">High Achievers (Grade C & Above)</span></h2>
            <p style="color: #7f8c8d; margin-top: 5px;" data-lang="high_achievers_desc">Review and send eligible students for certification.</p>
        </div>

        <!-- Batch Send Action -->
<form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap; width: auto;">
    <select name="target_course_id" required style="padding: 10px; border-radius: 8px; border: 1px solid #ddd; flex: 1; min-width: 200px;">
        <option value="" data-lang="select_course">Select Course</option>
        <?php 
        $course_list = $conn->query("SELECT id, title FROM courses WHERE teacher_id = '$teacher_id'");
        while($c = $course_list->fetch_assoc()): ?>
            <option value="<?php echo $c['id']; ?>" <?php echo ($target_id == $c['id']) ? 'selected' : ''; ?>>
                <?php echo $c['title']; ?>
            </option>
        <?php endwhile; ?>
    </select>
    
    <button type="submit" name="show_preview" class="btn-view" style="background: #3498db;">
        <i class="fas fa-eye"></i> <span data-lang="btn_preview_list">Preview List</span>
    </button>

    <button type="submit" name="send_to_admin" class="btn-view" style="background: #27ae60;">
        <i class="fas fa-paper-plane"></i> <span data-lang="btn_send_admin">Send to Admin</span>
    </button>
</form>

    </div>





</div>



</div>





    <div class="report-box">
        <div class="header-section">
            <h2><i class="fas fa-graduation-cap"></i> <span data-lang="achievement_cards_title">Student Achievement Cards</span></h2>
            <p data-lang="achievement_cards_desc">Generate and download official reports for students.</p>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th data-lang="th_student_details">Student Details</th>
                        <th data-lang="th_course_module">Course Module</th>
                        <th style="text-align: right;" data-lang="th_action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="report-row">
                            <td>
                                <div class="student-name"><?php echo $row['student_name']; ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-light);">ID: #STU-0<?php echo $row['student_id']; ?></div>
                            </td>
                            <td>
                                <span class="course-tag"><?php echo $row['course_name']; ?></span>
                            </td>
                        <!-- Inside the main table <tbody> -->
                    <td style="text-align: right;">
                        <a href="student_report.php?student_id=<?php echo $row['student_id']; ?>&course_id=<?php echo $row['course_id']; ?>" class="btn-view" target="_blank">
                            <i class="fas fa-file-pdf"></i> <span data-lang="btn_view_report">View Report</span>
                        </a>
                    </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--text-light); padding: 40px;">
                                <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                <span data-lang="no_students_found">No enrolled students found for your courses.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- NEW: PAGINATION NAV -->
<div style="display: flex; justify-content: center; gap: 8px; margin-top: 20px;">
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" 
           style="padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; border-radius: 5px; 
                  background: <?php echo ($page == $i) ? '#1a0b45' : 'white'; ?>; 
                  color: <?php echo ($page == $i) ? 'white' : '#1a0b45'; ?>;">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>








<!-- New Section for Certified Students -->
<div class="report-box" style="margin-top: 30px; border-top: 5px solid #27ae60;">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2><i class="fas fa-certificate text-success"></i> Certified Student Archive</h2>
                <p>Official records of students certified for your modules.</p>
            </div>
            <span class="badge bg-light text-dark border">Total: <?php echo $total_arch_students; ?></span>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Student Details</th>
                    <th>Course Name</th>
                    <th>Serial Number</th>
                    <th>Issue Date</th>
                    <th style="text-align: right;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $cert_query = "SELECT u.name, cert.course_name, cert.certificate_serial, cert.issue_date, u.id as sid 
                               FROM certificates cert 
                               JOIN users u ON cert.student_id = u.id 
                               WHERE cert.status = 'approved' 
                               AND cert.teacher_id = '$teacher_id'
                               ORDER BY cert.issue_date DESC
                               LIMIT $archive_limit OFFSET $archive_offset";
                               
                $cert_res = $conn->query($cert_query);
                
                if($cert_res && $cert_res->num_rows > 0):
                    while($cert = $cert_res->fetch_assoc()): ?>
                    <tr class="report-row">
                        <td>
                            <div class="student-name"><?php echo htmlspecialchars($cert['name']); ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-light);">ID: #STU-0<?php echo $cert['sid']; ?></div>
                        </td>
                        <td><span class="course-tag"><?php echo htmlspecialchars($cert['course_name']); ?></span></td>
                        <td><code class="fw-bold text-dark"><?php echo $cert['certificate_serial'] ?: 'N/A'; ?></code></td>
                        <td><?php echo date('M d, Y', strtotime($cert['issue_date'])); ?></td>
                        <td style="text-align: right;">
                            <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Verified</span>
                        </td>
                    </tr>
                    <?php endwhile; 
                else: ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ARCHIVE PAGINATION BUTTONS -->
    <?php if ($total_arch_pages > 1): ?>
    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <div class="text-muted small">
            Page <?php echo $archive_page; ?> of <?php echo $total_arch_pages; ?>
        </div>
        <nav aria-label="Archive pagination">
            <ul class="pagination pagination-sm mb-0">
                <!-- Previous Button -->
                <li class="page-item <?php echo ($archive_page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-none" href="?page=<?php echo $page; ?>&arch_page=<?php echo $archive_page - 1; ?>">
                        <i class="fas fa-chevron-left me-1"></i> Previous
                    </a>
                </li>
                
                <!-- Next Button -->
                <li class="page-item <?php echo ($archive_page >= $total_arch_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-none" href="?page=<?php echo $page; ?>&arch_page=<?php echo $archive_page + 1; ?>">
                        Next <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>





<!-- Modal Structure -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: #f1c40f; color: #000;">
                <h5 class="modal-title fw-bold" id="previewModalLabel">
                    <i class="fas fa-award me-2"></i> High Achievers Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Student Name</th>
                                <th>Course</th>
                                <th>Grade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($preview_result && $preview_result->num_rows > 0): ?>
                                <?php while($row = $preview_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-3"><strong><?php echo $row['sname']; ?></strong></td>
                                        <td><?php echo $row['ctitle']; ?></td>
                                        <td>
                                            <span style="background: #eafaf1; color: #27ae60; padding: 4px 8px; border-radius: 4px; font-weight: bold;">
                                                <?php echo number_format($row['final_grade'], 1); ?>%
                                            </span>
                                        </td>


                                        <!-- Inside the Modal Tbody -->
<td>
    <?php if ($row['cert_status'] === 'pending'): ?>
        <span class="badge bg-info text-dark">Awaiting Admin</span>
    <?php else: ?>
        <span class="badge bg-warning text-dark">Ready for Cert</span>
    <?php endif; ?>
</td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-4">No achievers found for this course.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
        <form method="POST" style="display:inline;">
                <input type="hidden" name="target_course_id" value="<?php echo htmlspecialchars($target_id); ?>">
                <button type="submit" name="send_to_admin" class="btn btn-success">
                    Confirm & Send to Admin
                </button>
            </form>
            </div>
        </div>
    </div>
</div>

<script>
// NEW: LIVE SEARCH SCRIPT
document.getElementById('studentSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('.report-row');

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});



    // If the PHP variable 'show_preview' was sent, open the modal automatically

document.addEventListener("DOMContentLoaded", function() {
    // Only trigger if PHP actually found a result or a request was made
    <?php if (isset($_POST['show_preview']) || isset($_POST['send_to_admin'])): ?>
        var myModal = new bootstrap.Modal(document.getElementById('previewModal'));
        myModal.show();
    <?php endif; ?>
});
</script>









  <script src="http://localhost/lms_tech/lang.js"></script>


</body>
</html>