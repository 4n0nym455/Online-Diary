<?php

// Database configuration
$host = '127.0.0.1'; // Use '127.0.0.1' instead of 'localhost' to avoid socket issues
$username = 'root';
$password = '';
$dbase = 'diary';

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $dbase);

// Check if the connection was successful
if ($conn->connect_error) {
    // Log the connection error
    error_log("Connection failed: " . $conn->connect_error);

    // Output a user-friendly message
    die("Unable to connect to the database. Please try again later.");
}

// Optional: Set the character set (to avoid encoding issues)
if (!$conn->set_charset("utf8")) {
    error_log("Error setting character set: " . $conn->error);
    die("Database connection encountered a problem.");
}

?>
