<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "bus_booking"; // Changed database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Failed to connect to database: " . $conn->connect_error);
}

?>
