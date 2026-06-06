<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
$course_id = $_GET['course_id'];
$course_res = $conn->query("SELECT title FROM courses WHERE id = '$course_id'");
$course = $course_res->fetch_assoc();

$students = $conn->query("SELECT id, name, phone, email FROM users WHERE role = 'student' AND course_id = '$course_id'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Class Report - <?php echo $course['title']; ?></title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()"> <div class="no-print">
        <button onclick="window.print()">Print / Save PDF</button>
        <hr>
    </div>

    <div class="header">
        <h1 data-lang="class_grade_report">Class Grade Report</h1>
        <h3><span data-lang="label_course">Course:</span> <?php echo $course['title']; ?></h3>
        <p> <span data-lang="generated_on">Generated on:</span> <?php echo date('M d, Y'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
            <th data-lang="th_student_name">Student Name</th>
            <th data-lang="th_email">Email</th>
            <th data-lang="th_phone">Phone</th>
            <th data-lang="th_total_score">Total Score</th>
            <th data-lang="th_percentage">Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php while($s = $students->fetch_assoc()): 
                $st_id = $s['id'];
                $grades = $conn->query("SELECT score_out_of_100, weight FROM grades WHERE student_id = '$st_id' AND course_id = '$course_id'");
                $earned = 0; $total = 0;
                while($g = $grades->fetch_assoc()){ $earned += $g['score_out_of_100']; $total += $g['weight']; }
                $pct = ($total > 0) ? ($earned / $total) * 100 : 0;
            ?>
            <tr>
                <td><?php echo $s['name']; ?></td>
                <td><?php echo $s['email']; ?></td>
                <td><?php echo $s['phone']; ?></td>
                <td><?php echo $earned; ?> / <?php echo $total; ?></td>
                <td><?php echo round($pct, 1); ?>%</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>







      <script src="http://localhost/lms_tech/lang.js"></script>

</body>
</html>