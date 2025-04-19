<?php
require_once('../book/TCPDF-main/tcpdf.php');
include 'config.php'; // Database connection

session_start();

// Ensure user is logged in and booking ID is set in session
if (!isset($_SESSION['booking_id'])) {
    die("Booking not found!");
}

$booking_id = $_SESSION['booking_id']; // Get the correct booking ID from session
$query = "SELECT * FROM booking WHERE id = ?"; // Fetch booking by ID
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No booking found!");
}

$booking = $result->fetch_assoc();

// Generate QR Code Data
$qr_data = "Passenger: " . $booking['passenger_name'] . "\n" .
           "Email: " . $booking['passenger_email'] . "\n" .
           "Bus: " . $booking['bus_name'] . " - " . $booking['bus_no'] . "\n" .
           "Departure: " . $booking['departure'] . " at " . $booking['departure_time'] . "\n" .
           "Destination: " . $booking['destination'] . "\n" .
           "Seats: " . $booking['selected_seats'] . "\n" .
           "Total Price: ₹" . $booking['total_price'];

// Create new PDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('NextStop');
$pdf->SetTitle('Bus Ticket');
$pdf->SetHeaderData('', 0, 'NextStop - Bus Ticket', "Explore Gujarat by Bus");

// Set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);

// Add a page
$pdf->AddPage();

// Ticket content
$html = '
    <h2 style="text-align: center;">Bus Ticket</h2>
    <table margin-top="10px" border="1" cellpadding="5" style="width: 100%;">
        <tr><td><strong>Passenger Name:</strong></td><td>' . htmlspecialchars($booking['passenger_name']) . '</td></tr>
        <tr><td><strong>Email:</strong></td><td>' . htmlspecialchars($booking['passenger_email']) . '</td></tr>
        <tr><td><strong>Bus Name:</strong></td><td>' . htmlspecialchars($booking['bus_name']) . '</td></tr>
        <tr><td><strong>Bus No:</strong></td><td>' . htmlspecialchars($booking['bus_no']) . '</td></tr>
        <tr><td><strong>Bus Type:</strong></td><td>' . htmlspecialchars($booking['bus_type']) . '</td></tr>
        <tr><td><strong>Departure:</strong></td><td>' . htmlspecialchars($booking['departure']) . '</td></tr>
        <tr><td><strong>Destination:</strong></td><td>' . htmlspecialchars($booking['destination']) . '</td></tr>
        <tr><td><strong>Departure Time:</strong></td><td>' . htmlspecialchars($booking['departure_time']) . '</td></tr>
        <tr><td><strong>Arrival Time:</strong></td><td>' . htmlspecialchars($booking['arrival_time']) . '</td></tr>
        <tr><td><strong>Selected Seats:</strong></td><td>' . htmlspecialchars($booking['selected_seats']) . '</td></tr>
        <tr><td><strong>Total Price:</strong></td><td>₹' . htmlspecialchars($booking['total_price']) . '</td></tr>
        <tr><td><strong>Payment Method:</strong></td><td>' . htmlspecialchars($booking['payment_method']) . '</td></tr>
    </table>
    <br><br>
    <p style="text-align: center;">Thank you for choosing NextStop! Have a safe journey.</p>
';

// Print content
$pdf->writeHTML($html, true, false, true, false, '');

// Add QR Code in center with proper bottom margin
$pdf->Ln(10);
$pdf->SetX((210 - 40) / 2); // Centering the QR Code
$pdf->write2DBarcode($qr_data, 'QRCODE,H', '', '', 40, 40, [], 'N');

// Align text below QR Code
$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 10, 'Scan for Ticket Details', 0, 1, 'C');

// Output the PDF as a download
$pdf->Output('Bus_Ticket.pdf', 'D');
exit();
?>
