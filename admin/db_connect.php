<?php
$servername = "localhost"; // Change if using a different server
$username = "root"; // Change if using a different database user
$password = ""; // Change if you have a database password
$dbname = "bus_booking"; // Updated to match the bus booking database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
