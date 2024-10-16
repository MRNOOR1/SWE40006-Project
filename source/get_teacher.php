<?php
session_start();
require 'credentials.inc'; // Database connection details
require_once "teacher_functions.inc";
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is a teacher
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access. Please login.";
} else {

    $teacher_id = $_SESSION['user_id'];

    // Determine which action to perform
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    // Route the request based on the action parameter
    switch ($action) {
        case 'get_courses':
            echo fetchCourses($conn, $teacher_id);
            break;
        case 'get_announcements':
            echo fetchAnnouncements($conn, $teacher_id);
            break;
        case 'create_announcement':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $course_code = $_POST['course'];
                $announcement_text = $_POST['announcement_text'];            
                echo createAnnouncement($conn, $teacher_id, $course_code, $announcement_text);
            } else {
                echo "Invalid request method for creating announcement.";
            }
            break;
        case 'delete_announcement':
            if (isset($_GET['id'])) {
                echo deleteAnnouncement($conn, $_GET['id']);
            } else {
                echo "No announcement ID provided.";
            }
            break;
        default:
            echo "Invalid action.";
    }

    // Close the database connection
    $conn->close();
}