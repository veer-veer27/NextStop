<?php
$servername = "localhost"; // Database server (default is localhost)
$username = "root"; // MySQL username (default is root in XAMPP)
$password = ""; // MySQL password (default is empty in XAMPP)
$database = "bus_booking"; // Updated to the bus booking database

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Uncomment this to check if the connection works
// echo "Connected successfully"; 
?>
