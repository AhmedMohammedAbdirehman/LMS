<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// --- PAGINATION LOGIC ---
$limit = 10; // Submissions per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total count for pagination links
$count_sql = "SELECT COUNT(*) as total FROM submissions s 
              JOIN lessons l ON s.lesson_id = l.id 
              JOIN courses c ON l.course_id = c.id 
              WHERE c.teacher_id = '$teacher_id'";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Main Query with LIMIT and OFFSET
$sql = "SELECT 
            s.id as sub_id, s.file_path, s.submitted_at, s.student_id,
            l.course_id, u.name as student_name, l.title as assignment_title,
            c.title as course_name, g.score_out_of_100 as official_score, g.weight
        FROM submissions s
        JOIN users u ON s.student_id = u.id
        JOIN lessons l ON s.lesson_id = l.id
        JOIN courses c ON l.course_id = c.id
        LEFT JOIN grades g ON (g.student_id = s.student_id 
                               AND g.course_id = l.course_id 
                               AND g.assessment_name = l.title)
        WHERE c.teacher_id = '$teacher_id'
        ORDER BY s.submitted_at DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Submissions</title>
    
<style>
    :root {
        --primary: #1a0b45;
        --secondary: #3498db;
        --success: #27ae60;
        --warning: #e67e22;
        --bg: #f8fafc;
    }

    body { background: var(--bg); font-family: 'Inter', sans-serif; margin: 0; padding: 20px; }
    
    .sub-container { 
        max-width: 1200px; 
        margin: auto; 
        padding: 20px;
    }

    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    /* Table Styling */
    .table-responsive { width: 100%; overflow-x: auto; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    
    table { width: 100%; border-collapse: collapse; min-width: 600px; }
    
    th { background: var(--primary); color: white; padding: 18px 15px; text-align: left; font-size: 0.9rem; }
    
    td { padding: 15px; border-bottom: 1px solid #edf2f7; vertical-align: middle; }

    /* Action Buttons */
    .btn-view { 
        display: inline-block;
        padding: 8px 16px; 
        border-radius: 8px; 
        text-decoration: none; 
        font-size: 0.85rem; 
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-pdf { background: #fee2e2; color: #b91c1c; }
    .btn-grade { background: var(--secondary); color: white; }
    .btn-edit { color: #64748b; font-size: 0.75rem; text-decoration: underline; }

    .badge-graded { background: #dcfce7; color: #15803d; padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; }

    /* Pagination UI */
    .pagination {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .pagination a {
        padding: 10px 18px;
        background: white;
        border: 1px solid #e2e8f0;
        color: var(--primary);
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
    }

    .pagination a.active { background: var(--primary); color: white; border-color: var(--primary); }

    /* --- THE MOBILE STACKING TRICK --- */
    @media (max-width: 768px) {
        .header-flex { flex-direction: column; align-items: flex-start; gap: 10px; }
        
        table, thead, tbody, th, td, tr { display: block; }
        
        thead tr { position: absolute; top: -9999px; left: -9999px; }
        
        tr { background: white; margin-bottom: 15px; border-radius: 12px; border: 1px solid #e2e8f0; padding: 10px; }
        
        td { border: none; position: relative; padding-left: 50% !important; text-align: right; }
        
        td:before {
            content: attr(data-label);
            position: absolute; left: 15px; width: 45%; text-align: left;
            font-weight: bold; color: var(--primary);
        }
    }
</style>
</head>
<body style="background: #f4f7f6; font-family: sans-serif;">

<div class="sub-container">
    <div class="header-flex">
        <div>
            <h2 data-lang="review_submissions_title">Review Submissions</h2>
            <a href="dashboard.php" style="color: var(--secondary); text-decoration: none; font-weight: 600;">← <span data-lang="back_to_dashboard">Back to Dashboard</span></a>
        </div>
        <div style="color: #64748b; font-size: 0.9rem;">Total: <?php echo $total_rows; ?> Submissions</div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                 <th data-lang="th_student_name">Student Name</th>
                <th data-lang="th_assignment">Assignment</th>
                <th data-lang="th_submitted_date">Submitted Date</th>
                <th data-lang="th_action">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Student">
                            <strong style="color: var(--primary);"><?php echo $row['student_name']; ?></strong><br>
                            <small style="color: #94a3b8;"><?php echo $row['course_name']; ?></small>
                        </td>
                        <td data-label="Assignment"><?php echo $row['assignment_title']; ?></td>
                        <td data-label="Date"><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></td>
                        <td data-label="Action">
                            <div style="display: flex; align-items: center; gap: 10px; justify-content: flex-end;">
                                <a href="../<?php echo $row['file_path']; ?>" target="_blank" class="btn-view btn-pdf" data-lang="btn_view_pdf">View PDF</a>
                                
                                <?php if($row['official_score'] !== null): ?>
                                    <span class="badge-graded">✓ Graded (<?php echo $row['official_score']; ?>/<?php echo $row['weight']; ?>)</span>
                                    <a href="manage_students.php?course_id=<?php echo $row['course_id']; ?>&student_id=<?php echo $row['student_id']; ?>&as_name=<?php echo urlencode($row['assignment_title']); ?>" class="btn-edit" data-lang="btn_edit">>Edit</a>
                                <?php else: ?>
                                    <a href="manage_students.php?course_id=<?php echo $row['course_id']; ?>&student_id=<?php echo $row['student_id']; ?>&as_name=<?php echo urlencode($row['assignment_title']); ?>" 
                                       class="btn-view btn-grade" data-lang="btn_add_grade">Add Grade</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 50px; color: #94a3b8;" data-lang="no_submissions_found">No submissions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>










  <script src="http://localhost/lms_tech/lang.js"></script>


</body>
</html>