<?php

use PHPUnit\Framework\TestCase;

class GetTeacherTest extends TestCase
{
    protected function setUp(): void
    {
        // Mock teacher session
        $_SESSION['teacher_id'] = 1;
    }

    public function testFetchTeacherCoursesSuccess()
    {
        // Mock the result returned by the database query
        $mockedCourses = [
            ['course_code' => 'CSC101', 'course_name' => 'Introduction to Computer Science']
        ];

        // Simulate fetching the teacher's courses
        $actualCourses = $this->fetchTeacherCourses();  // Replace with actual method

        // Assert that the fetched courses match the expected result
        $this->assertEquals($mockedCourses, $actualCourses);
    }

    private function fetchTeacherCourses()
    {
        // Simulate the data being fetched from the database for teacher_id = 1
        return [
            ['course_code' => 'CSC101', 'course_name' => 'Introduction to Computer Science']
        ];
    }

    public function testUnauthorizedAccess()
    {
        // Simulate a scenario where the session does not contain a teacher_id
        $_SESSION = []; // Clear session data

        // Capture the output if the user is unauthorized
        $this->expectOutputString('Unauthorized access. Please login.');

        // Simulate the script running without teacher login
        include 'get_teacher.php';  // Replace with actual script
    }
}
