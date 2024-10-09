<?php

use PHPUnit\Framework\TestCase;

class GetAnnouncementsTest extends TestCase
{
    private $conn;
    private $stmt;

    protected function setUp(): void
    {
        // Mock the session user_id for the tests
        $_SESSION['user_id'] = 1;

        // Create mock for the MySQLi connection and prepared statement
        $this->conn = $this->createMock(mysqli::class);
        $this->stmt = $this->createMock(mysqli_stmt::class);
    }

    public function testFetchAnnouncementsSuccess()
    {
        // Mock the prepared statement and result
        $result = $this->createMock(mysqli_result::class);
        $result->method('fetch_all')->willReturn([
            ['course_code' => 'CSC101', 'text' => 'Assignment 1 due soon', 'time' => '2024-10-05 10:00:00']
        ]);

        // Mock the connection and prepare method
        $this->stmt->method('bind_param')->with("i", $_SESSION['user_id']);
        $this->stmt->method('execute')->willReturn(true);
        $this->stmt->method('get_result')->willReturn($result);

        $this->conn->method('prepare')->willReturn($this->stmt);

        // Capture output (since the script echoes JSON data)
        ob_start();
        $this->fetchAnnouncements($this->conn, $_SESSION['user_id']);  // This is the method being tested
        $output = ob_get_clean();

        // Expected result
        $expectedOutput = json_encode([
            ['course_code' => 'CSC101', 'text' => 'Assignment 1 due soon', 'time' => '2024-10-05 10:00:00']
        ]);

        // Assert that the output matches the expected JSON
        $this->assertJsonStringEqualsJsonString($expectedOutput, $output);
    }

    private function fetchAnnouncements($conn, $user_id)
    {
        // Simulate the SQL query and prepared statement execution (mocked in the test)
        $sql = "SELECT courses.course_code, announcements.text, announcements.time 
                FROM user_courses
                JOIN courses ON user_courses.course_code = courses.course_code
                JOIN announcements ON announcements.course_code = courses.course_code
                WHERE user_courses.user_id = ?";

        // Prepare and bind (mocked)
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all announcements as an associative array
        $announcements = $result->fetch_all(MYSQLI_ASSOC);

        // Send the data as JSON
        header('Content-Type: application/json');
        echo json_encode($announcements);

        // Close statement and connection
        $stmt->close();
    }
}
