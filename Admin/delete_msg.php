<?php
require_once '../config/db.php';

if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "DELETE FROM contact_messages WHERE id = '$id'";
    
    if(mysqli_query($conn, $query)) {
        header("Location: admin_messages.php?msg=deleted");
        exit();
    }
}
?>