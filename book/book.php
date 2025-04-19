<?php
session_start();
include "db_connection.php"; // Ensure database connection

// ✅ Redirect if the user is not logged in
if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Please log in first to book a bus!'); window.location.href='../index.php';</script>";
    exit();
}

// ✅ Ensure the request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "<script>alert('Invalid request!'); window.location.href='book.html';</script>";
    exit();
}

// Fetch values safely from the form
$bus_id = $_POST["bus_id"] ?? null;
$departure = $_POST["departure"] ?? null;
$destination = $_POST["destination"] ?? null;
$travel_date = $_POST["travel_date"] ?? null;
$passenger_count = $_POST["passenger_count"] ?? 1; // Default 1 if not provided

// Validate required fields
if (!$bus_id || !$departure || !$destination || !$travel_date || $passenger_count < 1) {
    echo "<script>alert('All fields are required!'); window.location.href='book.html';</script>";
    exit();
}

// Fetch user ID from session email
$user_email = $_SESSION['user_email'];
$query = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('User not found!'); window.location.href='book.html';</script>";
    exit();
}

$user_id = $user["id"];

// Fetch available seats for the selected bus
$query = "SELECT available_seats FROM buses WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();
$bus = $result->fetch_assoc();

if (!$bus) {
    echo "<script>alert('Bus not found!'); window.location.href='book.html';</script>";
    exit();
}

$available_seats = $bus["available_seats"];

// Check if enough seats are available
if ($available_seats < $passenger_count) {
    echo "<script>alert('Not enough seats available! Only $available_seats left.'); window.location.href='book.html';</script>";
    exit();
}

// Insert booking into bus_bookings
$query = "INSERT INTO bus_bookings (user_id, bus_id, departure, destination, travel_date, passenger_count, booking_status) 
        VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
$stmt = $conn->prepare($query);
$stmt->bind_param("iisssi", $user_id, $bus_id, $departure, $destination, $travel_date, $passenger_count);

if ($stmt->execute()) {
    // Reduce the available seats in the buses table
    $new_seats = $available_seats - $passenger_count;
    $update_query = "UPDATE buses SET available_seats = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ii", $new_seats, $bus_id);
    $update_stmt->execute();

    echo "<script>alert('Booking successful!'); window.location.href='confirmation_page.php?success=1';</script>";
    exit();
} else {
    echo "<script>alert('Booking failed! Error: " . $stmt->error . "'); window.location.href='book.html';</script>";
}
?>
