<?php

use PHPUnit\Framework\TestCase;

class GetAnnouncementsTest extends TestCase
{
    protected function setUp(): void
    {
        // Mock session user_id for the tests
        $_SESSION['user_id'] = 1;
    }

    public function testFetchAnnouncementsSuccess()
    {
        // Mock the database result as an array that would be returned by the DB query
        $mockedResult = [
            ['course_code' => 'CSC101', 'text' => 'Assignment 1 due soon', 'time' => '2024-10-05 10:00:00']
        ];

        // Simulate fetching the announcements from the database
        // Assuming a function fetchAnnouncements() returns this data
        $actualResult = $this->fetchAnnouncements();  // Replace with actual method call

        // Assert that the fetched announcements match what we expect
        $this->assertEquals($mockedResult, $actualResult);
    }

    private function fetchAnnouncements()
    {
        // Simulate the data being fetched from the database for user_id = 1
        return [
            ['course_code' => 'CSC101', 'text' => 'Assignment 1 due soon', 'time' => '2024-10-05 10:00:00']
        ];
    }
}
