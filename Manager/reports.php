<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

// --- 1. Overall Attendance Percentage per Course ---
$course_stats_query = "SELECT c.title, 
    COUNT(a.id) as total_records,
    COUNT(CASE WHEN a.status = 'Present' THEN 1 END) as present_count,
    (COUNT(CASE WHEN a.status = 'Present' THEN 1 END) / COUNT(a.id)) * 100 as attendance_rate
    FROM attendance a
    JOIN courses c ON a.course_id = c.id
    GROUP BY c.id
    ORDER BY attendance_rate DESC";
$course_stats = $conn->query($course_stats_query);

// --- 2. Top 10 Most Absent Students (Global) ---
$absent_students_query = "SELECT u.name, COUNT(a.id) as total_absent
    FROM attendance a
    JOIN users u ON a.student_id = u.id
    WHERE a.status = 'Absent'
    GROUP BY u.id
    ORDER BY total_absent DESC
    LIMIT 10";
$absent_students = $conn->query($absent_students_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Manager Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #1a0b45; --accent: #2ecc71; --bg-light: #f4f7f6; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg-light); display: flex; min-height: 100vh; }
        
        /* Sidebar Styling (Same as Dashboard) */
        .sidebar { width: 260px; background: var(--primary); color: white; padding: 25px 0; flex-shrink: 0; }
        .sidebar a { display: flex; align-items: center; color: #bdc3c7; text-decoration: none; padding: 15px 25px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: var(--accent); background: rgba(255,255,255,0.05); border-left: 4px solid var(--accent); }
        .sidebar a i { margin-right: 12px; }

        .main-content { flex-grow: 1; overflow-x: hidden; }
        .top-bar { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }

        /* Report Layout */
        .report-container { padding: 30px; max-width: 1100px; margin: auto; }
        .report-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        
        .btn-print { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-print:hover { background: #2a1563; transform: translateY(-2px); }

        .progress-bar { background: #edf2f7; border-radius: 10px; height: 8px; width: 100%; margin-top: 5px; position: relative; }
        .progress-fill { height: 100%; border-radius: 10px; transition: 1s ease-in-out; }
        
        @media (max-width: 900px) { .report-grid { grid-template-columns: 1fr; } }
        @media print { .sidebar, .top-bar, .btn-print { display: none; } .main-content { width: 100%; } }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div style="padding: 0 25px; margin-bottom: 30px;">
            <h2 style="color: var(--accent); margin:0;">Tech Iftiin</h2>
            <small data-lang="portal_manager_title">Manager Portal</small>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-th-large"></i> <span data-lang="nav_dashboard">Dashboard</span></a>
            <a href="users_list.php"><i class="fas fa-users"></i> <span data-lang="nav_users">Users List</span></a>
            <a href="courses.php"><i class="fas fa-graduation-cap"></i> <span data-lang="nav_manage_courses">Manage Courses</span></a>
            <a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> <span data-lang="nav_reports">System Reports</span></a>
            <hr style="opacity:0.1;">
            <a href="../auth/logout.php" style="color: #ff7675;"><i class="fas fa-sign-out-alt"></i> <span data-lang="nav_logout">Logout</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="top-bar">
            <div>
                <a href="dashboard.php" style="text-decoration:none; color: var(--primary); font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> <span data-lang="btn_back_dashboard">Back to Dashboard</span>
                </a>
            </div>
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-file-pdf"></i> <span data-lang="btn_generate_pdf">Generate PDF Report</span>
            </button>
        </header>

        <div class="report-container">
            <h1 style="color: var(--primary); margin-bottom: 25px;" data-lang="report_main_title">System Analytics Report</h1>

            <div class="report-grid">
                <!-- Course Performance -->
                <div class="card">
                    <h3 style="margin-top:0;"><i class="fas fa-graduation-cap" style="color: var(--accent);"></i> <span data-lang="report_attendance_title">Course Attendance Rates</span></h3>
                    <table>
                        <thead>
                            <tr style="color: #7f8c8d; font-size: 0.85rem;">
                             <th data-lang="th_course_title">COURSE TITLE</th>
                            <th data-lang="th_engagement">ENGAGEMENT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $course_stats->fetch_assoc()): 
                                $rate = round($row['attendance_rate'], 1);
                                $color = ($rate > 80) ? '#2ecc71' : (($rate > 50) ? '#f1c40f' : '#e74c3c');
                            ?>
                            <tr>
                                <td><strong><?php echo $row['title']; ?></strong></td>
                                <td>
                                    <span style="font-size: 0.9rem; font-weight: bold;"><?php echo $rate; ?>%</span>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $rate; ?>%; background: <?php echo $color; ?>;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- At-Risk Students -->
                <div class="card">
                    <h3 style="margin-top:0;"><i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i> <span data-lang="report_at_risk_title">At-Risk Students</span></h3>
                    <p style="font-size: 0.8rem; color: #7f8c8d;" data-lang="report_at_risk_sub">Top 10 students with highest absences.</p>
                    <table>
                        <tbody>
                            <?php while($row = $absent_students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td style="text-align:right;">
                                    <span style="background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold;">
                                        <?php echo $row['total_absent']; ?> <span data-lang="label_absences">Absences</span>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




<script src="/lms_tech/lang.js"></script>

</body>
</html>