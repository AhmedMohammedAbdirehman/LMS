<?php
// Use ../ to go back to the root folder then into config
require_once '../config/db.php'; 

if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Mark as read automatically when opened
    mysqli_query($conn, "UPDATE contact_messages SET status='read' WHERE id='$id'");

    // Fetch the message
    $result = mysqli_query($conn, "SELECT * FROM contact_messages WHERE id='$id'");
    $msg = mysqli_fetch_assoc($result);
} else {
    header("Location: admin_messages.php");
    exit();
}
?>

<div class="message-content">
    <h3><?php echo htmlspecialchars($msg['subject']); ?></h3>
    <p><strong>From:</strong> <?php echo htmlspecialchars($msg['name']); ?> (<?php echo htmlspecialchars($msg['email']); ?>)</p>
    <div class="body">
        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
    </div>
</div>