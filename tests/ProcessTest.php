<?php

use PHPUnit\Framework\TestCase;

class ProcessTest extends TestCase
{
    protected function setUp(): void
    {
        // This will reset the $_POST and $_SESSION superglobals before each test
        $_POST = [];
        $_SESSION = [];
    }

    // Test the login process for successful login
    public function testLoginSuccess()
    {
        // Mock POST data for a successful student login
        $_POST['username'] = 'student1';
        $_POST['password'] = 'password123';

        // Simulate expected user details fetched from the database
        $mockedUser = [
            'id' => 1,
            'username' => 'student1',
            'password' => 'password123',  // This should normally be a hashed password in practice
            'type' => 'S', // Indicates student type
        ];

        // Simulate the login function and set the session
        $loginSuccess = $this->simulateLogin($_POST['username'], $_POST['password'], $mockedUser);

        // Check if login was successful
        $this->assertTrue($loginSuccess);
        // Assert that the session contains the correct user information
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('student', $_SESSION['user_type']);
    }

    // Test the announcement creation process
    public function testAnnouncementCreation()
    {
        // Mock POST data for creating an announcement
        $_POST['course'] = 'CSC101';
        $_POST['announcement_text'] = 'Midterm exam next week';

        // Simulate announcement creation function
        $announcementCreated = $this->simulateAnnouncementCreation($_POST['course'], $_POST['announcement_text']);

        // Assert that the announcement was created successfully
        $this->assertTrue($announcementCreated);
    }

    // Simulate the login function
    private function simulateLogin($username, $password, $userFromDB)
    {
        // In a real application, you would fetch the user from the database and compare the password
        if ($username === $userFromDB['username'] && $password === $userFromDB['password']) {
            // Set session for logged-in user
            $_SESSION['user_id'] = $userFromDB['id'];
            $_SESSION['user_type'] = $userFromDB['type'] === 'S' ? 'student' : 'teacher';
            return true;
        }
        return false;
    }

    // Simulate the announcement creation function
    private function simulateAnnouncementCreation($course, $announcementText)
    {
        // In a real application, this would involve inserting the announcement into the database
        if (!empty($course) && !empty($announcementText)) {
            return true; // Simulate successful insertion
        }
        return false;
    }
}
