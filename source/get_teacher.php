<?php
session_start();
require 'credentials.inc'; // Database connection details

$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is a teacher
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please login.");
}

$teacher_id = $_SESSION['user_id'];

// Determine which action to perform
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Fetch courses for the logged-in teacher
function fetchCourses($conn, $teacher_id) {
    $sql = "SELECT course_code, course_name FROM courses WHERE course_code IN (SELECT course_code FROM user_courses WHERE user_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    header('Content-Type: application/json');
    echo json_encode($courses);
}

// Fetch announcements for the teacher's courses
function fetchAnnouncements($conn, $teacher_id) {
    $sql = "SELECT id, course_code, text, time FROM announcements WHERE teacher_id = ? ORDER BY time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $announcements = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    header('Content-Type: application/json');
    echo json_encode($announcements);
}

// Create a new announcement
function createAnnouncement($conn, $teacher_id) {
    $course_code = $_POST['course'];
    $announcement_text = $_POST['announcement_text'];

    $sql = "INSERT INTO announcements (course_code, text, time, teacher_id) VALUES (?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $course_code, $announcement_text, $teacher_id);
    
    if ($stmt->execute()) {
        echo "Announcement created successfully.";
    } else {
        echo "Error creating announcement.";
    }

    $stmt->close();
}

// Delete an announcement by ID
function deleteAnnouncement($conn) {
    $announcement_id = $_GET['id'];

    $sql = "DELETE FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $announcement_id);

    if ($stmt->execute()) {
        echo "Announcement deleted successfully.";
    } else {
        echo "Error deleting announcement.";
    }

    $stmt->close();
}

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
?>
