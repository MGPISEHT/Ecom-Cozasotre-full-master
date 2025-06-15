<?php
// Database credentials
$host = 'localhost'; // Database host
$dbname = 'cozastore'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

try {
    // Create a PDO instance (database connection)
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Set PDO to throw exceptions on errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected successfully!"; // Debugging: Remove this in production
} catch (PDOException $e) {
    // Handle connection errors
    die("Connection failed: " . $e->getMessage()); // Terminate script with error message
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>