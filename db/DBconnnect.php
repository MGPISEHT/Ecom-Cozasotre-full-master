<?php
$host = 'localhost'; 
$dbname = 'cozastore'; 
$username = 'root'; 
$password = ''; 
try {
    // Create a PDO instance (database connection)
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!"; 
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage()); 
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>