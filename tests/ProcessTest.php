<?php

use PHPUnit\Framework\TestCase;

class ProcessTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset the $_POST and $_SESSION superglobals before each test
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
            'password' => 'password123',  // In practice, this should be hashed
            'type' => 'S', // Indicates student type
        ];

        // Simulate the login function and set the session
        $loginSuccess = $this->simulateLogin($_POST['username'], $_POST['password'], $mockedUser);

        // Check if login was successful
        $this->assertTrue($loginSuccess);
        // Assert that the session contains the correct user information
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('S', $_SESSION['user_type']);  // Correct user type
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

    // Test login failure due to invalid password
    public function testLoginFailureInvalidPassword()
    {
        // Mock POST data for a failed login attempt
        $_POST['username'] = 'student1';
        $_POST['password'] = 'wrongpassword';

        // Simulate expected user details fetched from the database
        $mockedUser = [
            'id' => 1,
            'username' => 'student1',
            'password' => 'password123',  // Correct password is different
            'type' => 'S',
        ];

        // Simulate the login function
        $loginSuccess = $this->simulateLogin($_POST['username'], $_POST['password'], $mockedUser);

        // Assert that login fails
        $this->assertFalse($loginSuccess);
    }

    // Test login failure due to non-existent user
    public function testLoginFailureNoUser()
    {
        // Mock POST data for a failed login attempt
        $_POST['username'] = 'nonexistentuser';
        $_POST['password'] = 'password123';

        // Simulate no user found in the database
        $mockedUser = null;

        // Simulate the login function
        $loginSuccess = $this->simulateLogin($_POST['username'], $_POST['password'], $mockedUser);

        // Assert that login fails
        $this->assertFalse($loginSuccess);
    }

    // Simulate the login function
    private function simulateLogin($username, $password, $userFromDB)
    {
        // Check if the user exists and the password is correct
        if ($userFromDB && $username === $userFromDB['username'] && $password === $userFromDB['password']) {
            // Set session for logged-in user
            $_SESSION['user_id'] = $userFromDB['id'];
            $_SESSION['user_type'] = $userFromDB['type']; // 'S' for student, 'T' for teacher
            return true;
        }
        return false;
    }

    // Simulate the announcement creation function
    private function simulateAnnouncementCreation($course, $announcementText)
    {
        // Simulate a successful announcement creation
        if (!empty($course) && !empty($announcementText)) {
            return true; // Simulate successful insertion
        }
        return false;
    }
}
