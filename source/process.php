<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Database credentials
require 'credentials.inc';

// Create MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user login (students/teachers)
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["role"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"];  // Get the role value from the form (teacher or student)

    // Validate inputs
    if (empty($username)) {
        die("Username is required");
    }
    if (strlen($password) < 8) {
        die("Password must be at least 8 characters");
    }
    if (!preg_match("/[a-z]/i", $password)) {
        die("Password must contain at least 1 letter");
    }

    // Fetch user from the database based on the role
    $sql = "SELECT * FROM users WHERE username = ? AND type = ?";
    $stmt = $conn->prepare($sql);
    
    // Determine user type based on the role selected
    $user_type = ($role === 'teacher') ? 'T' : 'S';
    $stmt->bind_param("ss", $username, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $password == $user['password']) {
        // Start session and store user info
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['type'];

        // Redirect based on user type
        if ($user['type'] == 'S') {
            header("Location: student_dashboard.html");
            exit();
        } elseif ($user['type'] == 'T') {
            header("Location: teacher_dashboard.html");
            exit();
        }
    } else {
        echo "Invalid username or password";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
