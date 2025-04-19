<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "bus_booking"); // Updated database name

if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Check if POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

// Validate POST parameters
if (empty($_POST['id']) || empty($_POST['status'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "ID and Status are required"]);
    exit;
}

$id = intval($_POST['id']);
$status = trim($_POST['status']);

error_log("Received ID: $id, Raw Status: $status"); // Debugging

// Normalize and validate status
$status_lower = strtolower($status);
$valid_statuses = ["approved", "rejected"];

if (!in_array($status_lower, $valid_statuses)) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Invalid status value"]);
    exit;
}

$status = ucfirst($status_lower); // Capitalize first letter

// Update booking status
$update_sql = "UPDATE bookings SET status = ? WHERE id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        http_response_code(200); // OK
        echo json_encode(["message" => "Booking status updated to " . $status]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Booking ID not found"]);
    }
} else {
    error_log("SQL Error: " . $stmt->error);
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Error updating booking"]);
}

$stmt->close();
$conn->close();
?>
