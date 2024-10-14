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
            fetchCourses($conn, $teacher_id);
            break;
        case 'get_announcements':
            fetchAnnouncements($conn, $teacher_id);
            break;
        case 'create_announcement':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                createAnnouncement($conn, $teacher_id);
            } else {
                echo "Invalid request method for creating announcement.";
            }
            break;
        case 'delete_announcement':
            if (isset($_GET['id'])) {
                deleteAnnouncement($conn);
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
