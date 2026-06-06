<?php
require_once 'config/db.php'; // Path to your DB connection

$cert_id = $_GET['id'] ?? null;
$cert = null;

if ($cert_id) {
    $stmt = $conn->prepare("SELECT c.*, u.name as student_name 
                            FROM certificates c 
                            JOIN users u ON c.student_id = u.id 
                            WHERE c.certificate_serial = ? AND c.status = 'approved'");
    $stmt->bind_param("s", $cert_id);
    $stmt->execute();
    $cert = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Certificate Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; display: flex; align-items: center; min-height: 100vh; }
        .verify-card { max-width: 500px; margin: auto; padding: 30px; border-radius: 15px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; }
        .status-icon { font-size: 60px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="verify-card">
    <?php if ($cert): ?>
        <div class="status-icon text-success">✅</div>
        <h2 class="fw-bold">Verified Certificate</h2>
        <p class="text-muted">This certificate is authentic and was issued by Techiftiin Institute.</p>
        <hr>
        <div class="text-start">
            <p><strong>Student:</strong> <?php echo htmlspecialchars($cert['student_name']); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($cert['course_name']); ?></p>
            <p><strong>Date Issued:</strong> <?php echo date('M d, Y', strtotime($cert['issue_date'])); ?></p>
            <p><strong>Serial:</strong> <?php echo htmlspecialchars($cert['certificate_serial']); ?></p>
        </div>
        <a href="https://techiftiin.com" class="btn btn-primary mt-3 w-100">Visit Techiftiin</a>
    <?php else: ?>
        <div class="status-icon text-danger">❌</div>
        <h2 class="fw-bold">Invalid Certificate</h2>
        <p class="text-muted">The certificate ID provided could not be verified in our records.</p>
        <a href="https://techiftiin.com" class="btn btn-outline-secondary mt-3 w-100">Contact Support</a>
    <?php endif; ?>
</div>

</body>
</html>