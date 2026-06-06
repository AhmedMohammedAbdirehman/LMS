<?php
require_once '../config/db.php';

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Update status to read
$conn->query("UPDATE contact_messages SET status='read' WHERE id='$id'");

// Fetch message
$res = $conn->query("SELECT * FROM contact_messages WHERE id='$id'");
echo json_encode($res->fetch_assoc());
?>