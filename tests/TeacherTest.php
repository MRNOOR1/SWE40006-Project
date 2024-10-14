<?php

use PHPUnit\Framework\TestCase;



class TeacherTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        session_start();
        // Mock session for teacher login
        $_SESSION['user_id'] = 1;
        require_once './source/teacher_functions.inc';

        // Create a mock database connection
        $this->conn = $this->createMock(mysqli::class);
    }

    protected function tearDown(): void
    {
        // Clear session and superglobals after each test
        session_destroy();
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
    }


    public function testFetchTeacherCoursesSuccess()
    {
        // Mock the prepared statement and result
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->expects($this->once())->method('bind_param');
        $stmt->expects($this->once())->method('execute');

        $result = $this->createMock(mysqli_result::class);
        $result->method('fetch_all')->willReturn([
            ['course_code' => 'CSC101', 'course_name' => 'Introduction to Computer Science']
        ]);

        $stmt->method('get_result')->willReturn($result);
        $this->conn->method('prepare')->willReturn($stmt);

        // Call the fetchCourses function
        ob_start();  // Start output buffering to capture the output
        fetchCourses($this->conn, $_SESSION['user_id']);
        $output = ob_get_clean();  // Get the output and end buffering

        // Assert the JSON response matches expected result
        $expectedOutput = json_encode([['course_code' => 'CSC101', 'course_name' => 'Introduction to Computer Science']]);
        $this->assertJsonStringEqualsJsonString($expectedOutput, $output);
    }

    public function testFetchAnnouncementsSuccess()
    {
        // Mock the prepared statement and result
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->expects($this->once())->method('bind_param');
        $stmt->expects($this->once())->method('execute');

        $result = $this->createMock(mysqli_result::class);
        $result->method('fetch_all')->willReturn([
            ['id' => 1, 'course_code' => 'CSC101', 'text' => 'Exam next week', 'time' => '2024-10-01 10:00:00']
        ]);

        $stmt->method('get_result')->willReturn($result);
        $this->conn->method('prepare')->willReturn($stmt);

        // Call the fetchAnnouncements function
        ob_start();  // Start output buffering to capture the output
        fetchAnnouncements($this->conn, $_SESSION['user_id']);
        $output = ob_get_clean();  // Get the output and end buffering

        // Assert the JSON response matches expected result
        $expectedOutput = json_encode([['id' => 1, 'course_code' => 'CSC101', 'text' => 'Exam next week', 'time' => '2024-10-01 10:00:00']]);
        $this->assertJsonStringEqualsJsonString($expectedOutput, $output);
    }

    public function testCreateAnnouncementSuccess()
    {
        // Mock the POST request data
        $_POST['course'] = 'CSC101';
        $_POST['announcement_text'] = 'Final exam is scheduled for next week.';

        // Mock the prepared statement
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->expects($this->once())->method('bind_param');
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($stmt);

        // Call the createAnnouncement function
        ob_start();  // Start output buffering to capture the output
        createAnnouncement($this->conn, $_SESSION['user_id']);
        $output = ob_get_clean();  // Get the output and end buffering

        // Assert the output matches expected success message
        $this->assertEquals("Announcement created successfully.", $output);
    }

    public function testDeleteAnnouncementSuccess()
    {
        // Mock the GET request data
        $_GET['id'] = 1;

        // Mock the prepared statement
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->expects($this->once())->method('bind_param');
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($stmt);

        // Call the deleteAnnouncement function
        ob_start();  // Start output buffering to capture the output
        deleteAnnouncement($this->conn);
        $output = ob_get_clean();  // Get the output and end buffering

        // Assert the output matches expected success message
        $this->assertEquals("Announcement deleted successfully.", $output);
    }

    public function testUnauthorizedAccess()
    {
        // Simulate no user being logged in by destroying the session
        session_destroy();

        // Simulate the GET request for announcements
        $_GET['action'] = 'get_announcements';

        ob_start();
        require "./source/get_teacher.php";
        // Capture the output if the user is unauthorized
        $output = ob_get_clean();  // Capture the output and clean the buffer

        // Assert unauthorized message is displayed
        $this->assertEquals("Unauthorized access. Please login.", $output);

        // Ensure the output buffer is cleaned up
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
}
