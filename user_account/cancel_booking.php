<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_email']) || !isset($_GET['id'])) {
    header("Location: account.php");
    exit();
}

$email = $_SESSION['user_email'];
$booking_id = intval($_GET['id']); // Ensure it's an integer

// Check if the booking exists and belongs to the user
$sql_check = "SELECT id FROM booking WHERE id = ? AND passenger_email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("is", $booking_id, $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Delete the booking
    $sql_delete = "DELETE FROM booking WHERE id = ? AND passenger_email = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("is", $booking_id, $email);

    if ($stmt_delete->execute()) {
        $_SESSION['message'] = "✅ Booking deleted successfully.";
    } else {
        $_SESSION['error'] = "❌ Failed to delete booking.";
    }
} else {
    $_SESSION['error'] = "⚠️ Booking not found or already deleted.";
}

header("Location: account.php");
exit();
