<?php
// Database credentials
require 'credentials.inc';

echo $host . $username . $password . $dbname;
// Create MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for announcement creation (Teacher Dashboard)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course']) && isset($_POST['announcement_text'])) {
    $course = $_POST["course"];
    $announcement_text = $_POST["announcement_text"];
    $teacher_id = 1; // You can modify this based on session/authentication

    $sql = "INSERT INTO announcements (text, time, teacher_id) VALUES (?, NOW(), ?)";
    
    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $announcement_text, $teacher_id);
    
    if ($stmt->execute()) {
        echo "Announcement created successfully.";
    } else {
        echo "Error creating announcement: " . $conn->error;
    }
    
    // Close the statement
    $stmt->close();
}

// Handle user login (students/teachers)
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

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

    // Fetch user from the database
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $password == $user['password']) {
      // Start session and store user info
        session_start();
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
