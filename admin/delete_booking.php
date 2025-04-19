<?php
session_start();
include 'db_connect.php'; // Ensure this file connects to your database

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $booking_id = intval($_GET['id']); // Convert to integer for security

    // Prepare the delete query
    $query = "DELETE FROM booking WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to manage_bookings.php with success message
        $_SESSION['message'] = "Booking deleted successfully!";
    } else {
        // Redirect with an error message
        $_SESSION['error'] = "Error deleting booking.";
    }

    // Close statement
    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid request.";
}

// Redirect to manage_bookings.php
header("Location: manage_bookings.php");
exit();
?>
