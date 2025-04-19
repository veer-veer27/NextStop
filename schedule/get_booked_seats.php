<?php
session_start();
include '../login page/connect.php';

if (!isset($_GET['bus_no'])) {
    die("Bus number not provided");
}

// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$bus_no = $_GET['bus_no'];
$query = "SELECT GROUP_CONCAT(selected_seats) AS all_booked_seats 
          FROM booking 
          WHERE bus_no = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $bus_no);
$stmt->execute();
$result = $stmt->get_result();

// Return as JSON for more reliable parsing
header('Content-Type: application/json');
echo json_encode(['booked_seats' => $result->fetch_assoc()['all_booked_seats'] ?? '']);
?>