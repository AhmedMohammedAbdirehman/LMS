<?php
session_start();
require_once '../config/db.php';

// Security: Only logged-in users should see this
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$cert_id = $_GET['id'] ?? null;

if (!$cert_id) {
    die("Certificate ID is missing.");
}

// CORRECTED QUERY: Ensure there is only ONE '?' if you are only binding the ID
$query = "SELECT c.*, u_std.name as student_name, u_tea.name as teacher_name 
          FROM certificates c
          JOIN users u_std ON c.student_id = u_std.id
          JOIN users u_tea ON c.teacher_id = u_tea.id
          WHERE c.id = ?"; // Only one placeholder here

$stmt = $conn->prepare($query);

// 'i' stands for integer, $cert_id is the single variable being bound
$stmt->bind_param("i", $cert_id); 

$stmt->execute();
$result = $stmt->get_result();
$cert = $result->fetch_assoc();

if (!$cert) {
    die("Certificate not found.");
}

// Handle "Pending" certificates for previewing
$display_serial = !empty($cert['certificate_serial']) 
                  ? $cert['certificate_serial'] 
                  : "techiftiin-XXXX-" . date("Y"); // Placeholder for preview

$display_date = ($cert['status'] === 'approved') 
                ? date('F jS, Y', strtotime($cert['issue_date'])) 
                : date('F jS, Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?php echo $cert['certificate_serial']; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&family=Cinzel:wght@400;700&family=Great+Vibes&family=Montserrat:wght@300;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&family=Cinzel:wght@400;700&family=Pinyon+Script&family=Montserrat:wght@300;600&display=swap" rel="stylesheet">
<style>
    :root {
        --tech-navy: #211566;   /* Deep Navy from logo background */
        --tech-orange: #f49d1a; /* Vibrant Orange from logo accents */
        --tech-white: #ffffff;
    }

    @page { size: landscape; margin: 0; }

    body { 
        background: #f0f0f0; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        min-height: 100vh; 
        margin: 0; 
        font-family: 'Montserrat', sans-serif;
    }

    .cert-card {
        width: 1100px; 
        height: 770px; 
        background: var(--tech-navy); 
        position: relative;
        overflow: hidden;
        color: var(--tech-white);
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        border: 15px solid #0a0430;
        box-sizing: border-box;
    }

    /* Abstract Graceful Shapes (Replacing Borders) */
    .cert-card::before {
        content: "";
        position: absolute;
        top: -100px;
        right: -100px;
        width: 400px;
        height: 400px;
        background: var(--tech-orange);
        border-radius: 50%;
        opacity: 0.1;
        z-index: 1;
    }

    .inner-container {
        position: relative;
        z-index: 10;
        height: 100%;
        padding: 60px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-align: center;
        margin: 15px;
        border: 2px solid #f49d1a;
        position: relative;
    }

    .header h1 { 
        font-family: 'Cinzel', serif; 
        font-size: 4rem; 
        margin: 0; 
        color: var(--tech-white);
        letter-spacing: 4px;
    }

    .header p { 
        color: var(--tech-orange); 
        letter-spacing: 8px; 
        text-transform: uppercase; 
        font-weight: 600;
        margin-top: 10px;
    }

    .student-name { 
        font-family: 'Pinyon Script', cursive; 
        font-size: 4rem; 
        color: var(--tech-orange); 
        margin: 20px 0;
        text-shadow: 2px 2px 0px rgba(0,0,0,0.2);
        text-transform: capitalize;
    }


    .signature-line {
        border-top: 2px solid var(--tech-orange);
        margin: 0 40px;
        padding-top: 10px;
    }
    /* Visible Stamp Area */
    .stamp-spot {
        width: 120px;
        height: 120px;
        border: 2px dashed rgba(244, 157, 26, 0.4); /* Subtle orange dash */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.03);
    }

    .stamp-text {
        font-family: 'Cinzel', serif;
        font-size: 0.6rem;
        color: var(--tech-orange);
        opacity: 0.6;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

@media print {
    .no-print { display: none !important; }
    
    .cert-card { 
        width: 100%; 
        height: 99vh; 
        box-shadow: none !important; 
        /* CRITICAL: Force background colors to print */
        -webkit-print-color-adjust: exact !important; 
        print-color-adjust: exact !important; 
    }
    
    body { 
        background: var(--tech-navy) !important; 
        -webkit-print-color-adjust: exact !important; 
    }
}
/* Styling for the QR Code container */
.qr-container {
    background: #ffffff; /* White background makes it scannable */
    padding: 8px;
    border-radius: 5px;
    display: inline-block;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

@media print {
    /* Ensures the QR code prints clearly */
    img {
        -webkit-print-color-adjust: exact !important;
    }
}

.serial-text {
    color: var(--tech-orange); /* Or #f49d1a */
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    letter-spacing: 1px;
    text-transform: uppercase;
    display: block;
    margin-top: 10px;
    font-size: 0.4rem;
}
</style>
</head>

<body>
    <div class="cert-card">
        <div class="inner-container">
            <!-- Top Section -->
            <div class="header">
                <h1>TECH IFTIIN</h1>
                <p>Institute of AI & Technology</p>
                <div style="width: 80px; height: 4px; background: var(--tech-orange); margin: 30px auto;"></div>
            </div>

            <!-- Main Content -->
            <div>
                <p style="font-size: 1.5rem; font-weight: 300; opacity: 0.9;">This Certificate is Proudly Presented to</p>
                <div class="student-name"><?php echo htmlspecialchars($cert['student_name']); ?></div>
                
                <p style="font-size: 1.3rem; line-height: 1.6; max-width: 800px; margin: 0 auto;">
                    For successfully completing the professional requirements for the course<br>
                    <span style="color: var(--tech-orange); font-size: 1.8rem; font-weight: 600;"><?php echo htmlspecialchars($cert['course_name']); ?></span>
                </p>
                <p style="margin-top: 20px; opacity: 0.8;">Issued on: <?php echo $display_date; ?></p>
            </div>

            <!-- Footer Section -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; padding-bottom: 20px;">
    
<!-- QR Code Section - No White Padding -->
<!-- Replace your current QR Code Section with this improved version -->
<div style="text-align: left; width: 35%;">
    <?php 
        $cert_serial = trim($cert['certificate_serial']); 
        if(empty($cert_serial)) {
            $cert_serial = "techiftiin-XXXX-" . date("Y");
        }

        // 1. DATA TO BE VISIBLE ON SCAN
        // We include the Name and Course so the scanner shows them immediately
        $student_name = htmlspecialchars($cert['student_name']);
        $course_name = htmlspecialchars($cert['course_name']);
        
        $qr_content = "Student: $student_name\n"
                    . "Course: $course_name\n"
                    . "Serial: $cert_serial\n"
                    . "Verify: https://techiftiin.com/verify?id=" . $cert_serial;

        // 2. ENHANCED QR URL (White background for better scanning)
        $qr_url = "https://quickchart.io/chart?chs=300x300&cht=qr&chl=" . urlencode($qr_content) . "&choe=UTF-8&margin=1";
    ?>
    
    <!-- Using a white background container ensures high contrast for phone cameras -->
    <div style="display: inline-block; background: #fff; padding: 5px; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
        <img src="<?php echo $qr_url; ?>" 
             alt="QR Verification" 
             style="display: block; width: 110px; height: 110px; border: none;">
    </div>

    <div class="serial-text">
        VERIFICATION ID: <span style="color: #fff;"><?php echo htmlspecialchars($cert_serial); ?></span>
    </div>
</div>


    <!-- Visible Stamp Area -->
    <div style="width: 30%;">
        <div class="stamp-spot">
            <div class="stamp-text">Affix Official<br>Institute Stamp<br>Here</div>
        </div>
    </div>

    <!-- Signature Section -->
    <div style="width: 35%; text-align: center;">
        <div style="font-family: 'Pinyon Script'; font-size: 2.3rem; color: var(--tech-white); margin-bottom: -5px; text-transform: capitalize;">
            Ahmed Bache
        </div>
        
        <div style="border-top: 2px solid var(--tech-orange); margin: 0 40px; padding-top: 8px;">
            <span style="text-transform: uppercase; font-size: 0.75rem; letter-spacing: 2px; font-weight: 600; color: var(--tech-orange);">Academic Director</span>
        </div>
    </div>

</div>
        </div>
    </div>

  <button class="no-print" onclick="triggerPrint()" style="position:fixed; bottom:30px; right:30px; z-index:999; padding:15px 30px; background:#f49d1a; color:#0e0541; border:none; border-radius:50px; cursor:pointer; font-weight:bold; box-shadow: 0 10px 20px rgba(0,0,0,0.3);">
    PRINT CERTIFICATE
</button>





<script>
function triggerPrint() {
    // Small delay ensures the browser has rendered all styles before opening the dialog
    setTimeout(function() {
        window.print();
    }, 500);
}
</script>


</body>

</html>