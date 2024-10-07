<?php
// Database credentials
require 'credentials.inc';

// Create MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the user's courses and the corresponding announcements
session_start();
$user_id = $_SESSION['user_id'];

$sql = "SELECT courses.course_code, announcements.text, announcements.time 
        FROM user_courses
        JOIN courses ON user_courses.course_code = courses.course_code
        JOIN announcements ON announcements.course_code = courses.course_code
        WHERE user_courses.user_id = ?";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all announcements as an associative array
$announcements = $result->fetch_all(MYSQLI_ASSOC);

// Send the data as JSON
header('Content-Type: application/json');
echo json_encode($announcements);

// Close connections
$stmt->close();
$conn->close();
?>