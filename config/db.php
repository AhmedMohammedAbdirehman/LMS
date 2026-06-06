<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "lms_tech";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

define('GEMINI_API_KEY', 'AIzaSyByk2uWgwJvkdAqYfBTABNqicK8OZK_H9s');
?>