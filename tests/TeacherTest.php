<?php
use PHPUnit\Framework\TestCase;

class TeacherTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        session_start();
        $_SESSION['user_id'] = 1;
        require_once "./source/teacher_functions.inc";
        // Mock the mysqli connection
        $this->conn = $this->createMock(mysqli::class);
    }

    protected function tearDown(): void
    {
        session_destroy();
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
    }

    public function testFetchTeacherCoursesSuccess()
    {
        // Create a mock statement object
        $stmt = $this->createMock(mysqli_stmt::class);
        $result = $this->createMock(mysqli_result::class);

        // Mock the database interaction
        $this->conn->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('SELECT course_code, course_name FROM courses WHERE course_code IN (SELECT course_code FROM user_courses WHERE user_id = ?)'))
            ->willReturn($stmt);

        $stmt->expects($this->once())->method('bind_param')->with('i', $_SESSION['user_id']);
        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('get_result')->willReturn($result);

        $result->expects($this->once())->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn([
            ['course_code' => 'CSC101', 'course_name' => 'Introduction to Computer Science']
        ]);


        $output = fetchCourses($this->conn, $_SESSION['user_id']);

        // Expected output
        $expectedOutput = json_encode([['course_code' => 'CSC101', 'course_name' => 'Introduction to Computer Science']]);

        $this->assertJsonStringEqualsJsonString($expectedOutput, $output);
    }

    public function testFetchAnnouncementsSuccess()
    {
        // Create a mock statement object
        $stmt = $this->createMock(mysqli_stmt::class);
        $result = $this->createMock(mysqli_result::class);

        // Mock the database interaction
        $this->conn->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('SELECT id, course_code, text, time FROM announcements WHERE teacher_id = ? ORDER BY time DESC'))
            ->willReturn($stmt);

        $stmt->expects($this->once())->method('bind_param')->with('i', $_SESSION['user_id']);
        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('get_result')->willReturn($result);

        $result->expects($this->once())->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn([
            ['id' => 1, 'course_code' => 'CSC101', 'text' => 'Exam next week', 'time' => '2024-10-01 10:00:00']
        ]);

        $output = fetchAnnouncements($this->conn, $_SESSION['user_id']);

        // Expected output
        $expectedOutput = json_encode([
            ['id' => 1, 'course_code' => 'CSC101', 'text' => 'Exam next week', 'time' => '2024-10-01 10:00:00']
        ]);

        $this->assertJsonStringEqualsJsonString($expectedOutput, $output);
    }

    public function testCreateAnnouncementSuccess()
    {
        // Mock POST request data
        $_POST['course'] = 'CSC101';
        $_POST['announcement_text'] = 'Final exam is scheduled for next week.';

        // Create a mock statement object
        $stmt = $this->createMock(mysqli_stmt::class);

        // Mock the database interaction
        $this->conn->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('INSERT INTO announcements (course_code, text, time, teacher_id) VALUES (?, ?, NOW(), ?)'))
            ->willReturn($stmt);

        $stmt->expects($this->once())->method('bind_param')->with('ssi', $_POST['course'], $_POST['announcement_text'], $_SESSION['user_id']);
        $stmt->expects($this->once())->method('execute')->willReturn(true);


        $output = createAnnouncement($this->conn, $_SESSION['user_id']);

        // Assert the output matches expected success message
        $this->assertEquals("Announcement created successfully.", $output);
    }

    public function testDeleteAnnouncementSuccess()
    {
        // Mock GET request data
        $_GET['id'] = 1;

        // Create a mock statement object
        $stmt = $this->createMock(mysqli_stmt::class);

        // Mock the database interaction
        $this->conn->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('DELETE FROM announcements WHERE id = ?'))
            ->willReturn($stmt);

        $stmt->expects($this->once())->method('bind_param')->with('i', $_GET['id']);
        $stmt->expects($this->once())->method('execute')->willReturn(true);

        $output = deleteAnnouncement($this->conn);

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
        $output = ob_get_clean();  // Capture the output and clean the buffer

        // Assert unauthorized message is displayed
        $this->assertEquals("Unauthorized access. Please login.", $output);
    }
}
