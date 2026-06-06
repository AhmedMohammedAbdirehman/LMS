<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
$student_id = $_GET['student_id'];
$course_id = $_GET['course_id'];

// 1. Fetch Student and Course Details
$info_query = $conn->query("SELECT u.name as sname, c.title as ctitle 
                            FROM users u, courses c 
                            WHERE u.id = $student_id AND c.id = $course_id");
$info = $info_query->fetch_assoc();

// 2. Fetch All Grades
$grades_result = $conn->query("SELECT assessment_name, weight, score_out_of_100 
                               FROM grades 
                               WHERE student_id = $student_id AND course_id = $course_id");

$total_score_earned = 0; 
$total_possible_score = 0; 

// Letter Grade Logic
function getLetterGrade($percent) {
    if ($percent >= 90) return "A+";
    if ($percent >= 85) return "A";
    if ($percent >= 80) return "A-";
    if ($percent >= 75) return "B+";
    if ($percent >= 70) return "B";
    if ($percent >= 65) return "C+";
    if ($percent >= 60) return "C";
    if ($percent >= 50) return "D";
    return "F";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Report Card - TechIftiin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Restoring the premium Look and Feel */
        body { background: #f0f2f5; font-family: 'Georgia', 'Times New Roman', serif; padding: 40px; margin: 0; }
        
        .report-card { 
            max-width: 850px; 
            margin: auto; 
            background: white; 
            padding: 60px;
            border: 10px double #1a0b45; /* The classic institute border */
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
        }

        .header { text-align: center; border-bottom: 3px double #1a0b45; padding-bottom: 20px; margin-bottom: 30px; }
        
        .logo-text { 
            font-size: 28px; 
            font-weight: bold; 
            color: #1a0b45; 
            letter-spacing: 2px; 
            text-transform: uppercase;
        }

        .student-info { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 40px; 
            font-size: 18px; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 15px; 
        }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        
        th { 
            background: #1a0b45; 
            color: white; 
            padding: 15px; 
            border: 1px solid #ddd; 
            text-transform: uppercase; 
            font-size: 13px; 
        }
        
        td { padding: 15px; border: 1px solid #ddd; text-align: center; font-size: 16px; }
        
        .total-row { background: #f8fafc; font-weight: bold; color: #1a0b45; }

        .final-result-container { 
            display: flex; 
            justify-content: flex-end; 
            margin-top: 20px; 
        }

        .grade-box { 
            border: 3px solid #1a0b45; 
            padding: 25px; 
            width: 280px; 
            text-align: center; 
            background: #fff;
            border-radius: 4px;
        }

        .big-grade { 
            font-size: 60px; 
            font-weight: bold; 
            color: #1a0b45; 
            margin: 5px 0;
            line-height: 1;
        }

        /* The "Official Stamp" look */
        .stamp {
            position: absolute;
            bottom: 100px;
            right: 50px;
            width: 120px;
            height: 120px;
            border: 4px solid rgba(231, 76, 60, 0.4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: rgba(231, 76, 60, 0.4);
            font-weight: bold;
            font-size: 14px;
            transform: rotate(-25deg);
            pointer-events: none;
            text-transform: uppercase;
        }

        .footer-sig { margin-top: 100px; display: flex; justify-content: space-between; }
        
        .sig-line { 
            border-top: 2px solid #000; 
            width: 220px; 
            text-align: center; 
            padding-top: 8px; 
            font-weight: bold;
            font-family: sans-serif;
            font-size: 14px;
        }

/* 1. Tell the printer to use standard A4 and remove default margins */
@page {
    size: A4 portrait;
    margin: 0;
}

/* 2. Optimized Print Styles */
@media print {
    body { 
        background: white !important; 
        padding: 0 !important; 
        margin: 0 !important;
    }

    .no-print { 
        display: none !important; 
    }

    .report-card { 
        border: 10px double #1a0b45 !important; 
        box-shadow: none !important; 
        width: 100% !important; 
        max-width: 100% !important;
        margin: 0 !important;
        padding: 40px !important; /* Slightly reduced padding for print safety */
        box-sizing: border-box !important;
        height: 100vh; /* Ensures it fills the page height */
    }

    /* Ensure colors and backgrounds actually print */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Prevent the table or footer from breaking across pages if it gets too long */
    table, .grade-box, .footer-sig {
        page-break-inside: avoid;
    }
}
    </style>
</head>
<body>


<div class="no-print" style="text-align:center; margin-bottom: 30px;">
    <button onclick="window.print()" class="btn-print">
        <i class="fas fa-print"></i>
        <span data-lang="btn_print_report">PRINT OFFICIAL REPORT CARD</span>
    </button>
</div>

<div class="report-card">
    <!-- Official Stamp -->
    <!-- <div class="stamp">TECHIFTIIN<br>OFFICIAL<br>GRADED</div> -->

    <div class="header">
        <div class="logo-text">TECHIFTIIN INSTITUTE OF AI AND TECHNOLOGY</div>
        <p style="margin: 10px 0 0 0; color: #555; font-style: italic;">Empowering the Future through Innovation</p>
    </div>

    <div class="student-info">
        <div><strong data-lang="label_student_caps">STUDENT:</strong> <?php echo strtoupper($info['sname']); ?></div>
        <div><strong data-lang="label_course_caps">COURSE:</strong> <?php echo strtoupper($info['ctitle']); ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th data-lang="th_desc">Assessment Description</th>
                <th style="width: 150px;" data-lang="th_weight">Weight (%)</th>
                <th style="width: 150px;" data-lang="th_score">Score Obtained</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $grades_result->fetch_assoc()): 
                $total_score_earned += $row['score_out_of_100'];
                $total_possible_score += $row['weight'];
            ?>
            <tr>
                <td style="text-align:left; font-weight: bold;"><?php echo $row['assessment_name']; ?></td>
                <td><?php echo $row['weight']; ?></td>
                <td><?php echo $row['score_out_of_100']; ?></td>
            </tr>
            <?php endwhile; ?>
            
            <tr class="total-row">
                <td style="text-align:right; padding-right: 20px;" data-lang="cumulative_total">CUMULATIVE TOTAL</td>
                <td><?php echo $total_possible_score; ?></td>
                <td><?php echo $total_score_earned; ?></td>
            </tr>
        </tbody>
    </table>

    <div class="final-result-container">



        <div class="grade-box">
            <?php 
                if($total_possible_score > 0) {
                    $percentage = ($total_score_earned / $total_possible_score) * 100;
                    $letterGrade = getLetterGrade($percentage);
            ?>
                <div style="font-size: 12px; color: #7f8c8d; text-transform: uppercase; font-weight: bold;" data-lang="final_percentage">Final Percentage</div>
                <div style="font-size: 24px; font-weight: bold; margin-bottom: 10px; color: #333;">
                    <?php echo number_format($percentage, 1); ?>%
                </div>
                
                <div style="border-top: 1px solid #ddd; margin: 10px 0; padding-top: 10px;">
                    <div style="font-size: 12px; color: #7f8c8d; text-transform: uppercase; font-weight: bold;" data-lang="overall_grade">Overall Grade</div>
                    <div class="big-grade"><?php echo $letterGrade; ?></div>
                </div>
            <?php } else { ?>
                <span data-lang="no_grades_recorded">No Grades Recorded</span>
            <?php } ?>
        </div>
    </div>

    <div class="footer-sig">
        <div class="sig-line">
            <span style="font-size: 12px;" data-lang="sig_instructor">INSTRUCTOR SIGNATURE</span>
        </div>
        <div class="sig-line">
            <span style="font-size: 12px;" data-lang="sig_registrar">ACADEMIC REGISTRAR</span>
        </div>
    </div>

    <div style="text-align: center; margin-top: 60px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 10px;">
       <span data-lang="disclaimer_start">This document is an official academic record of TechIftiin Institute. Issued on</span> <?php echo date('d-m-Y'); ?>.
    </div>
</div>



      <script src="http://localhost/lms_tech/lang.js"></script>


</body>
</html>