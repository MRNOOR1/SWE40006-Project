<?php

use PHPUnit\Framework\TestCase;

class LogoutTest extends TestCase
{
    protected function setUp(): void
    {
        // Ensure that any active session is destroyed before starting a new one
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy(); // End any active session before starting a new one
        }
        //session_start(); // Start a new session for the test
    }

    public function testLogoutSuccess()
    {
        // Mock session data
        $_SESSION['user_id'] = 1; 

        // Include the logout script
        ob_start(); // Start output buffering to catch any output
        include 'source/logout.php'; 
        ob_end_clean(); // Clean the buffer

        // Assert that session was successfully destroyed
        $this->assertEquals(PHP_SESSION_NONE, session_status(), 'Session should be destroyed after logout.');
    }

    public function testSessionUnset()
    {
        // Mock session data
        $_SESSION['user_id'] = 1;

        // Include the logout script
        ob_start(); // Start output buffering
        include 'source/logout.php';
        ob_end_clean(); // Clean the buffer

        // Assert that all session variables are unset
        $this->assertEmpty($_SESSION, 'Session variables should be unset after logout.');
    }

    public function testRedirectionAfterLogout()
    {
        // Start the session and mock session data
        $_SESSION['user_id'] = 1;

        // Mock the header() function using PHPUnit's built-in method to prevent actual redirection
        $this->expectOutputRegex('/Location: index\.html/');

        // Start output buffering to prevent actual header call
        ob_start();
        include 'source/logout.php';
        ob_end_clean();

        // No need to assert anything here as the expectation is set by expectOutputRegex()
    }

    public function testLogoutWithoutSession()
    {
        // Simulate no active session
        session_destroy();

        // Include the logout script
        ob_start(); // Start output buffering
        include 'source/logout.php';
        ob_end_clean(); // Clean the buffer

        // Assert that session is not active
        $this->assertEquals(PHP_SESSION_NONE, session_status(), 'Session should not be active after logout without a session.');
    }
}

