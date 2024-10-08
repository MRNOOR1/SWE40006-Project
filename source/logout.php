<?php
// Start the session
session_start();

// Destroy all session variables
session_unset();
session_destroy();

// Redirect to the login page (or another page of your choice)
header("Location: index.html");
exit();
?>