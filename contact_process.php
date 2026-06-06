<?php


require_once 'config/db.php'; 


// Check if the specific button was clicked
if (isset($_POST['submit_contact'])) {
    
    // Sanitize inputs
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $msg     = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert Query
    $sql = "INSERT INTO contact_messages (name, email, subject, message) 
            VALUES ('$name', '$email', '$subject', '$msg')";

if (mysqli_query($conn, $sql)) {
    // Redirect to 'index' instead of 'index.php'
    header("Location: index.php?status=success#contact");
    exit();
} else {
        // ERROR: Redirect with error flag for better UX
        header("Location: index.php?status=error#contact");
        exit();
    }
} else {
    // If someone tries to access this file directly, send them back
    header("Location: index.php");
    exit();
}
?>