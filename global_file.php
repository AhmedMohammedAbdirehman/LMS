<?php
// global_file.php - Located in root

function logActivity($conn, $user_id, $action, $details) {
    // If user_id is missing, try to get it from the session automatically
    if (!$user_id && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } elseif (!$user_id && isset($_SESSION['id'])) {
        $user_id = $_SESSION['id'];
    }

    // Fallback to 0 if still no ID found (system action)
    $user_id = $user_id ? $user_id : 0;
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $action, $details, $ip);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}
?>