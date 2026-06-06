<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$course_id = $_GET['course_id'];
$msg = "";
$today = date('Y-m-d');

// Check if attendance was already taken today
$check_today = $conn->query("SELECT id FROM attendance WHERE course_id = '$course_id' AND attendance_date = '$today' LIMIT 1");
$is_already_taken = ($check_today->num_rows > 0);

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // STRICT CHECK: If already taken, do not process the loop
    if ($is_already_taken) {
        $msg = "<div style='color:#721c24; padding:10px; background:#f8d7da; margin-bottom:20px; border-radius:5px; border:1px solid #f5c6cb;'>
                    <i class='fas fa-lock'></i> Action Denied: Attendance for today is already locked and cannot be changed.
                </div>";
    } else {
        $date = $_POST['attendance_date'];
        $attendance_data = $_POST['attendance']; 

        foreach ($attendance_data as $student_id => $status) {
            $conn->query("INSERT INTO attendance (course_id, student_id, status, attendance_date) VALUES ('$course_id', '$student_id', '$status', '$date')");
        }


        // --- ADD THIS LOGGING CODE AFTER THE FOREACH LOOP ---

// 1. Fetch course title for the log message
$c_data = $conn->query("SELECT title FROM courses WHERE id = '$course_id'")->fetch_assoc();
$course_name = $c_data['title'] ?? 'Unknown Course';

// 2. Identify the teacher
$teacher_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;

// 3. Create the detail message
$details = "Teacher submitted attendance for course ($course_name) on date: $date";

// 4. Call the tracking function
if (function_exists('logActivity')) {
    logActivity($conn, $teacher_id, 'INSERT', $details);
}

        
        $is_already_taken = true; // Lock the UI immediately after save
        $msg = "<div style='color:green; padding:10px; background:#d4edda; margin-bottom:20px; border-radius:5px;'>Attendance successfully locked for $date!</div>";
    }
} elseif ($is_already_taken) {
    // Show a read-only notice if they just opened the page
    $msg = "<div style='color:#0c5460; padding:15px; background:#d1ecf1; border:1px solid #bee5eb; margin-bottom:20px; border-radius:8px;'>
                <i class='fas fa-check-circle'></i> Attendance for today has been completed and submitted.
            </div>";
}

// Fetch Students
$students = $conn->query("SELECT id, name, gender FROM users 
                          WHERE role = 'student' 
                          AND FIND_IN_SET('$course_id', course_id) 
                          AND status = 1");



// --- Pagination & Search Logic ---
$search_date = $_GET['search_date'] ?? '';
$limit = 5; // Rows per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base query for history
$where_clause = "WHERE course_id = '$course_id'";
if (!empty($search_date)) {
    $where_clause .= " AND attendance_date = '$search_date'";
}

// Count total for pagination
$total_res = $conn->query("SELECT COUNT(DISTINCT attendance_date) as total FROM attendance $where_clause");
$total_rows = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch the actual history rows
$history = $conn->query("SELECT DISTINCT attendance_date FROM attendance 
                         $where_clause 
                         ORDER BY attendance_date DESC 
                         LIMIT $limit OFFSET $offset");




// --- NEW: Attendance Analytics (Absenteeism Rate) ---
$absent_query = "SELECT u.name, 
                COUNT(CASE WHEN a.status = 'Absent' THEN 1 END) as absent_count,
                COUNT(a.id) as total_sessions,
                (COUNT(CASE WHEN a.status = 'Absent' THEN 1 END) / COUNT(a.id)) * 100 as absent_rate
                FROM attendance a
                JOIN users u ON a.student_id = u.id
                WHERE a.course_id = '$course_id'
                GROUP BY a.student_id
                HAVING absent_count > 0
                ORDER BY absent_rate DESC
                LIMIT 5";

$absent_stats = $conn->query($absent_query);
$absent_names = []; $absent_rates = [];
while($row = $absent_stats->fetch_assoc()) {
    $absent_names[] = $row['name'];
    $absent_rates[] = round($row['absent_rate'], 1);
}


?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 40px; }
        .attendance-container { background: white; padding: 30px; border-radius: 12px; max-width: 800px; margin: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .status-radio { margin-right: 15px; cursor: pointer; }
        .btn-save { background: #f39c12; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 20px; }
    
    table tr:hover {
    background-color: #f1f5f9; /* Subtle highlight when hovering over a student */
}

select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

td {
    vertical-align: middle;
}

/* Responsive Table Fix */
@media (max-width: 600px) {
    body { padding: 10px; }
    .attendance-container { padding: 15px; }
    
    table thead { display: none; } /* Hide headers on mobile */
    table, table tbody, table tr, table td { display: block; width: 100%; }
    table tr { margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; padding: 10px; background: #fff; }
    table td { border: none; padding: 5px 0; display: flex; justify-content: space-between; align-items: center; }
    table td::before { content: attr(data-label); font-weight: bold; color: #7f8c8d; }
}

.chart-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    max-width: 800px;
    margin: 20px auto;
}
    </style>
</head>
<body>


    <div class="attendance-container">
        <a href="dashboard.php" style="text-decoration:none; color:#3498db;">
            ← <span data-lang="back_to_dashboard">Back to Dashboard</span>
        </a>
        <h2 data-lang="take_attendance_title">Take Attendance</h2>
        <?php echo $msg; ?>

<form method="POST">
    <label><strong data-lang="label_select_date">Select Date:</strong></label>
    <input type="date" name="attendance_date" value="<?php echo $today; ?>" required 
           style="padding:8px; border-radius:5px; border:1px solid #ddd; margin-bottom:20px;"
           <?php echo $is_already_taken ? 'readonly' : ''; ?>>

  <table>
    <thead>
        <tr style="background:#f8f9fa;">
          <th data-lang="th_student_name">Student Name</th>
            <th data-lang="th_gender">Gender</th> 
            <th data-lang="th_status">Status</th>
        </tr>
    </thead>
    <tbody>
    <?php while($s = $students->fetch_assoc()): ?>
    <tr>
        <td data-label="Student Name"><strong><?php echo $s['name']; ?></strong></td>
        <td data-label="Gender"><?php echo $s['gender'] ?? 'N/A'; ?></td>
        <td data-label="Status">
            <select name="attendance[<?php echo $s['id']; ?>]" required 
                    style="padding: 8px; border-radius: 5px; border: 1px solid #cbd5e1; background: #fff; width: 100%;"
                    <?php echo $is_already_taken ? 'disabled' : ''; ?>>
                <option value="" disabled selected data-lang="opt_take_attendance">-- take attendance --</option>
                <option value="Present" data-lang="opt_present">✅ Present</option>
                <option value="Absent" data-lang="opt_absent">❌ Absent</option>
                <option value="Late" data-lang="opt_late">🕒 Late</option>
            </select>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
            </table>

<?php if (!$is_already_taken): ?>
    <button type="submit" class="btn-save">
        <i class="fas fa-save"></i> 
        <span data-lang="btn_submit_attendance">Submit Attendance</span>
    </button>
<?php else: ?>
    <div style="margin-top:20px; color:#e74c3c; font-weight:bold;">
        <i class="fas fa-ban"></i> Submission Closed for Today
    </div>
<?php endif; ?>
</form>

    </div>





    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="chart-container">
    <h3><i class="fas fa-chart-pie"></i> 
    <span data-lang="chart_absenteeism_title">High Absenteeism Rate (%)</span>
</h3>
    <div style="height: 300px;">
        <canvas id="absentChart"></canvas>
    </div>
</div>





    <hr style="margin: 40px 0;">
<div class="attendance-container" style="margin-top: 30px;">
    <h3><i class="fas fa-history"></i> <span data-lang="attendance_history_title">Attendance History</span></h3>
    
    <form method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
        <input type="date" name="search_date" value="<?php echo $search_date; ?>" 
               style="padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
        <button type="submit" style="background:#3498db; color:white; border:none; padding:8px 15px; border-radius:5px; cursor:pointer;">
            <i class="fas fa-search"></i> <span data-lang="btn_filter">Filter</span>
        </button>
        <?php if(!empty($search_date)): ?>
            <a href="take_attendance.php?course_id=<?php echo $course_id; ?>" style="padding:8px; color:#e74c3c; text-decoration:none;">Clear</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr style="background:#f8f9fa;">
            <th data-lang="th_date">Date</th>
                <th data-lang="th_action">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($history->num_rows > 0): ?>
                <?php while($h = $history->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('F d, Y', strtotime($h['attendance_date'])); ?></td>
                    <td>
                        <a href="generate_attendance_pdf.php?course_id=<?php echo $course_id; ?>&date=<?php echo $h['attendance_date']; ?>" 
                           target="_blank" style="color: #e74c3c; font-weight: bold; text-decoration:none;">
                            <i class="fas fa-file-pdf"></i> <span data-lang="btn_download_pdf">Download PDF</span>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="2" style="text-align:center;" data-lang="no_records_found">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px; display: flex; justify-content: center; gap: 10px;">
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="take_attendance.php?course_id=<?php echo $course_id; ?>&page=<?php echo $i; ?>&search_date=<?php echo $search_date; ?>" 
               style="padding: 5px 10px; border-radius: 4px; text-decoration: none; 
                      <?php echo ($i == $page) ? 'background:#3498db; color:white;' : 'background:#ddd; color:#333;'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</div>





<script>
const ctx = document.getElementById('absentChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($absent_names); ?>,
        datasets: [{
            label: 'Absence Rate %',
            data: <?php echo json_encode($absent_rates); ?>,
            backgroundColor: 'rgba(231, 76, 60, 0.7)',
            borderColor: '#e74c3c',
            borderWidth: 1,
            borderRadius: 5
        }]
    },
    options: {
        maintainAspectRatio: false,
        indexAxis: 'y', // Horizontal Bar Chart
        scales: {
            x: { beginAtZero: true, max: 100 }
        }
    }
});
</script>




      <script src="http://localhost/lms_tech/lang.js"></script>


</body>
</html>