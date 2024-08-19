<?php
// Database configuration
$host = 'localhost'; // Database host, typically 'localhost' for local development
$dbname = ''; // Replace with your actual database name
$username = 'freddy'; // Replace with your MySQL username
$password = 'freddy'; // Replace with your MySQL password

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If connected successfully
echo "Connected successfully";
?>
