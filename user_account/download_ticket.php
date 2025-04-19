<?php
session_start();
require_once('../book/TCPDF-main/tcpdf.php'); // Include TCPDF library
include 'db_connect.php'; // Include database connection

if (!isset($_SESSION['user_email']) || !isset($_GET['id'])) {
    header("Location: available_buses.php");
    exit();
}

$email = $_SESSION['user_email'];
$booking_id = intval($_GET['id']);

// Fetch user details
$sql_user = "SELECT firstName, lastName, email, age FROM users WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Fetch booking details
$sql_booking = "SELECT * FROM booking WHERE id = ? AND passenger_email = ?";
$stmt_booking = $conn->prepare($sql_booking);
$stmt_booking->bind_param("is", $booking_id, $email);
$stmt_booking->execute();
$result_booking = $stmt_booking->get_result();
$booking = $result_booking->fetch_assoc();

if (!$user || !$booking) {
    header("Location: available_buses.php");
    exit();
}

// Create PDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('NextStop');
$pdf->SetTitle('Bus Ticket');
$pdf->SetHeaderData('', 0, 'NextStop - Bus Ticket', "Explore Gujarat by Bus");
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// Title
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Bus Ticket', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Ln(3);

// Ticket layout
$html = '
<table border="1" cellpadding="4">
    <tr><td colspan="2" style="background-color:#f2f2f2;"><strong>Passenger Details</strong></td></tr>
    <tr><td><strong>Name:</strong></td><td>' . htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) . '</td></tr>
    <tr><td><strong>Email:</strong></td><td>' . htmlspecialchars($user['email']) . '</td></tr>
    <tr><td><strong>Age:</strong></td><td>' . htmlspecialchars($user['age']) . '</td></tr>

    <tr><td colspan="2" style="background-color:#f2f2f2;"><strong>Bus Details</strong></td></tr>
    <tr><td><strong>Bus Name:</strong></td><td>' . htmlspecialchars($booking['bus_name']) . '</td></tr>
    <tr><td><strong>Bus No:</strong></td><td>' . htmlspecialchars($booking['bus_no']) . '</td></tr>
    <tr><td><strong>Bus Type:</strong></td><td>' . htmlspecialchars($booking['bus_type']) . '</td></tr>
    <tr><td><strong>Departure:</strong></td><td>' . htmlspecialchars($booking['departure']) . '</td></tr>
    <tr><td><strong>Destination:</strong></td><td>' . htmlspecialchars($booking['destination']) . '</td></tr>
    <tr><td><strong>Departure Time:</strong></td><td>' . htmlspecialchars($booking['departure_time']) . '</td></tr>
    <tr><td><strong>Arrival Time:</strong></td><td>' . htmlspecialchars($booking['arrival_time']) . '</td></tr>
    <tr><td><strong>Selected Seats:</strong></td><td>' . htmlspecialchars($booking['selected_seats']) . '</td></tr>
    <tr><td><strong>Total Price:</strong></td><td>₹' . number_format($booking['total_price'], 2) . '</td></tr>
</table>
<p style="text-align: center; margin-top: 10px;"><strong>Thank you for choosing NextStop!</strong></p>
';

$pdf->writeHTML($html, true, false, true, false, '');

// QR Code Data
$qrData = "Booking Details:\n" .
          "Name: {$user['firstName']} {$user['lastName']}\n" .
          "Email: {$user['email']}\n" .
          "Bus Name: {$booking['bus_name']}\n" .
          "Bus No: {$booking['bus_no']}\n" .
          "Departure: {$booking['departure']}\n" .
          "Destination: {$booking['destination']}\n" .
          "Departure Time: {$booking['departure_time']}\n" .
          "Arrival Time: {$booking['arrival_time']}\n" .
          "Seats: {$booking['selected_seats']}\n" .
          "Price: ₹" . number_format($booking['total_price'], 2);

$pdf->Ln(5);
$pdf->write2DBarcode($qrData, 'QRCODE,M', 80, 220, 40, 40, null, 'N');
$pdf->SetXY(80, 265);
$pdf->Cell(40, 10, 'Scan for Ticket Details', 0, 1, 'C');

$pdf->Output('Bus_Ticket_' . $booking_id . '.pdf', 'D');
exit();
?>
