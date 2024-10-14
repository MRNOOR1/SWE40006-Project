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
        require_once './source/get_teacher.php';

        // Create a mock database connection
        $this->conn = $this->createMock(mysqli::class);
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
        // Simulate a scenario where the session does not contain a user_id
        session_destroy(); // Clear session data

        // Capture the output if the user is unauthorized
        ob_start();
        fetchAnnouncements($this->conn, 1);
        $output = ob_get_clean();
        ob_end_clean();
        // Assert unauthorized message is displayed
        $this->assertEquals("Unauthorized access. Please login.", $output);
    }
}
