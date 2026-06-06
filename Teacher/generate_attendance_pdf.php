<?php
session_start();
require_once '../config/db.php';
// Note: You may need a PDF library like Dompdf or FPDF. 
// For now, this version creates a clean, printable HTML layout.
require_once __DIR__ . '/../global_file.php';
$course_id = $_GET['course_id'];
$date = $_GET['date'];

// Fetch Course Name
$course_res = $conn->query("SELECT c.title, u.name as teacher_name 
                            FROM courses c 
                            JOIN users u ON c.teacher_id = u.id 
                            WHERE c.id = '$course_id'");
$course_data = $course_res->fetch_assoc();

// Fetch Attendance Data for this specific date
$sql = "SELECT u.name, u.gender, a.status 
        FROM attendance a 
        JOIN users u ON a.student_id = u.id 
        WHERE a.course_id = '$course_id' AND a.attendance_date = '$date'";
$results = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - <?php echo $date; ?></title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .Present { color: green; font-weight: bold; }
        .Absent { color: red; font-weight: bold; }
        .Late { color: orange; font-weight: bold; }
        /* Branding Header */
.branding {
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 3px solid #1a0b45;
    padding-bottom: 15px;
    margin-bottom: 20px;
}
.logo {
    width: 70px; 
    height: auto;
    margin-right: 15px;
}
.institute-name {
    font-size: 24px;
    font-weight: bold;
    color: #1a0b45;
    margin: 0;
}
    </style>
</head>
<body onload="window.print()"> 
    
<body onload="window.print()">

    <div class="branding">
        <img src="../images/logo.jpg" alt="Techiftiin Logo" class="logo">
        <div class="institute-details">
            <h1 class="institute-name">Techiftiin Institute of AI and Technology</h1>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <span><strong data-lang="label_course">Course:</strong> <?php echo $course_data['title']; ?></span>
        <span><strong data-lang="label_course">Instructor:</strong> <?php echo $course_data['teacher_name']; ?></span>
        <span><strong data-lang="label_date">Date:</strong> <?php echo date('d M, Y', strtotime($date)); ?></span>
    </div>

    <table>
        <thead>
            <tr>
                <th data-lang="th_student_name">Student Name</th>
                <th data-lang="th_gender">Gender</th>
                <th data-lang="th_status">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $results->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td>
                    <span data-lang="gender_<?php echo strtolower($row['gender']); ?>">
                    <?php echo ucfirst($row['gender']); ?>
                </span>
            </td>
            <td class="<?php echo $row['status']; ?>">
                <span data-lang="status_<?php echo strtolower($row['status']); ?>">
                    <?php echo $row['status']; ?>
                </span>
            </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <p style="margin-top: 30px; font-size: 0.8rem; color: #7f8c8d;">
       <span data-lang="report_generated_on">Report Generated on:</span>: <?php echo date('Y-m-d H:i:s'); ?>
    </p>



    <div style="margin-top: 60px; display: flex; justify-content: space-between; padding: 0 40px;">
    <div style="text-align: center; width: 200px; border-top: 1px solid #000; padding-top: 5px;">
        <p style="font-size: 12px;" data-lang="instructor_signature">Instructor Signature</p>
    </div>
    <div style="text-align: center; width: 200px; border-top: 1px solid #000; padding-top: 5px;">
        <p style="font-size: 12px;" data-lang="admin_stamp">Administration Stamp</p>
    </div>
</div>




  <script src="http://localhost/lms_tech/lang.js"></script>

</body>
</html>