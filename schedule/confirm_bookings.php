<?php
session_start();
include 'config.php'; // Include database connection

// Clear any old session error data
unset($_SESSION['booking_error']);

// Ensure user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login page/index.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Fetch user details
$query = "SELECT firstName, lastName, email, age FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $passenger_name = $user['firstName'] . " " . $user['lastName'];
    $passenger_email = $user['email'];
    $passenger_age = $user['age'];
} else {
    die("User details not found!");
}

// Get booking data from POST (fallback to session if needed)
$booking_data = [
    'bus_name' => $_POST['bus_name'] ?? $_SESSION['booking_details']['bus_name'] ?? '',
    'bus_no' => $_POST['bus_no'] ?? $_SESSION['booking_details']['bus_no'] ?? '',
    'bus_type' => $_POST['bus_type'] ?? $_SESSION['booking_details']['bus_type'] ?? '',
    'departure' => $_POST['departure'] ?? $_SESSION['booking_details']['departure'] ?? '',
    'destination' => $_POST['destination'] ?? $_SESSION['booking_details']['destination'] ?? '',
    'departure_time' => $_POST['departure_time'] ?? $_SESSION['booking_details']['departure_time'] ?? '',
    'arrival_time' => $_POST['arrival_time'] ?? $_SESSION['booking_details']['arrival_time'] ?? '',
    'selected_seats' => $_POST['selected_seats'] ?? $_SESSION['booking_details']['selected_seats'] ?? '',
    'total_price' => $_POST['total_price'] ?? $_SESSION['booking_details']['total_price'] ?? ''
];

// Store current booking data in session
$_SESSION['current_booking'] = array_merge($booking_data, [
    'passenger_name' => $passenger_name,
    'passenger_email' => $passenger_email,
    'passenger_age' => $passenger_age
]);

function generateHiddenFields($booking_data, $name, $email, $age) {
    return '
    <input type="hidden" name="passenger_name" value="' . htmlspecialchars($name) . '">
    <input type="hidden" name="email" value="' . htmlspecialchars($email) . '">
    <input type="hidden" name="passenger_age" value="' . htmlspecialchars($age) . '">
    <input type="hidden" name="bus_name" value="' . htmlspecialchars($booking_data['bus_name']) . '">
    <input type="hidden" name="bus_no" value="' . htmlspecialchars($booking_data['bus_no']) . '">
    <input type="hidden" name="bus_type" value="' . htmlspecialchars($booking_data['bus_type']) . '">
    <input type="hidden" name="departure" value="' . htmlspecialchars($booking_data['departure']) . '">
    <input type="hidden" name="destination" value="' . htmlspecialchars($booking_data['destination']) . '">
    <input type="hidden" name="departure_time" value="' . htmlspecialchars($booking_data['departure_time']) . '">
    <input type="hidden" name="arrival_time" value="' . htmlspecialchars($booking_data['arrival_time']) . '">
    <input type="hidden" name="selected_seats" value="' . htmlspecialchars($booking_data['selected_seats']) . '">
    <input type="hidden" name="total_price" value="' . htmlspecialchars($booking_data['total_price']) . '">
    <input type="hidden" id="payment_method" name="payment_method" value="">';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="popup-style.css">
</head>
<style>
       /* General Page Styling */
body {
    background: linear-gradient(to bottom, #ddebf8, #83b8db);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    overflow: hidden; /* Prevent scrollbars due to fixed elements */
}

/* Video Background */
.video-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: -1;
}

.video-container video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Wrapper to center content properly */
.page-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100vh;
}

/* Booking Details Container */
.booking-container {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    width: 550px;
    padding: 20px;
    text-align: center;
    border-top: 8px solid #2c9c66;
    position: relative;
    z-index: 1;
}

/* Heading */
.booking-header {
    font-size: 20px;
    font-weight: bold;
    background: linear-gradient(to right, #2c9c66, #29b984);
    color: white;
    padding: 10px;
    border-radius: 8px 8px 0 0;
}

/* Details Section */
.details-container {
    padding: 15px;
    background: white;
    border-radius: 0 0 8px 8px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

/* Each Detail Row */
.details-item {
    margin-bottom: 10px;
    font-size: 14px;
    padding: 5px 10px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.details-item:last-child {
    border-bottom: none;
}
.btn {
            display: inline-block;
            margin-top: 20px;
            width: 440px;
            padding: 10px 20px;
            background: green;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: darkgreen;
        }

/* Buttons */
.pay-btn {
    width: 100%;
    padding: 12px;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
    background: #29b984;
    transition: all 0.3s ease-in-out;
}

.pay-btn:hover {
    background: darkgreen;
}

/* Payment Popup */
.payment-options {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    max-width: 450px;
    width: 90%;
    text-align: center;
    transition: all 0.3s ease-in-out;
}

.payment-options h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
}

.payment-options button {
    display: block;
    margin: 10px 0;
    padding: 14px;
    width: 100%;
    font-size: 16px;
    font-weight: 500;
    color: white;
    background: #29b984;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}

.payment-options button:hover {
    transform: scale(1.03);
}
.payment-options .close-btn {
            background: red;
            color: white;
        }
.close-btn {
    background-color: red;
    padding: 14px;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
}

/* Payment Form */
.payment-form {
    display: none;
    margin-left: 600px;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 500px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    padding: 20px;
    border-radius: 8px;
    z-index: 1000;
    text-align: left; /* Align text to the left */
}

/* Adjust input alignment */
.payment-form label {
    display: block;
    margin: 5px 0;
    text-align: left; /* Ensure labels are aligned left */
    font-weight: bold; /* Make labels stand out */
}

.payment-form input {
    width: 95%;
    padding: 8px;
    margin: 5px 0;
    border: 1px solid #ccc;
    text-align: left; /* Align text inside input fields */
}

.payment-form h3 {
    text-align: left; /* Align heading to the left */
    margin-bottom: 10px;
}

    </style>
<body>

<!-- Booking Details Container -->
<div class="page-container">
    <div class="booking-container">
        <div class="booking-header">Booking Details</div>
        <div class="details-container">
            <div class="details-item"><strong>Passenger:</strong> <span><?php echo htmlspecialchars($passenger_name); ?></span></div>
            <div class="details-item"><strong>Email:</strong> <span><?php echo htmlspecialchars($passenger_email); ?></span></div>
            <div class="details-item"><strong>Age:</strong> <span><?php echo htmlspecialchars($passenger_age); ?></span></div>
            <div class="details-item"><strong>Bus Name:</strong> <span><?php echo htmlspecialchars($booking_data['bus_name']); ?></span></div>
            <div class="details-item"><strong>Bus No:</strong> <span><?php echo htmlspecialchars($booking_data['bus_no']); ?></span></div>
            <div class="details-item"><strong>Bus Type:</strong> <span><?php echo htmlspecialchars($booking_data['bus_type']); ?></span></div>
            <div class="details-item"><strong>Departure:</strong> <span><?php echo htmlspecialchars($booking_data['departure']); ?></span></div>
            <div class="details-item"><strong>Destination:</strong> <span><?php echo htmlspecialchars($booking_data['destination']); ?></span></div>
            <div class="details-item"><strong>Departure Time:</strong> <span><?php echo htmlspecialchars($booking_data['departure_time']); ?></span></div>
            <div class="details-item"><strong>Arrival Time:</strong> <span><?php echo htmlspecialchars($booking_data['arrival_time']); ?></span></div>
            <div class="details-item"><strong>Selected Seats:</strong> <span><?php echo htmlspecialchars($booking_data['selected_seats']); ?></span></div>
            <div class="details-item"><strong>Total Price:</strong> <span>₹<?php echo htmlspecialchars($booking_data['total_price']); ?></span></div>
        </div>

        <!-- Payment Button -->
        <button type="button" class="pay-btn" onclick="openPaymentOptions()">Pay Now</button>
    </div>
</div>

<!-- Payment Options Popup -->
<div class="payment-options" id="paymentOptions" style="display: none;">
    <h3>Select Payment Method</h3>
    <button onclick="showPaymentForm('card')">Card Payment</button>
    <button onclick="showPaymentForm('gpay')">GPay</button>
    <button onclick="showPaymentForm('phonepe')">PhonePe</button>
    <button class="close-btn" onclick="closePaymentOptions()">Close</button>
</div>

<!-- Card Payment Form -->
<div class="payment-form" id="cardForm" style="display: none;">
    <h3>Enter Card Details</h3>
    <form method="POST" action="../schedule/process_booking.php" onsubmit="return validatePayment('card')">
        <?php echo generateHiddenFields($booking_data, $passenger_name, $passenger_email, $passenger_age); ?>
        <input type="text" name="card_name" placeholder="Cardholder Name" required>
        <input type="text" name="card_number" placeholder="Card Number" required>
        <input type="month" name="card_expiry" required>
        <input type="text" name="card_cvv" placeholder="CVV" required>
        <input type="hidden" name="payment_method" value="Card">
        <button type="submit" class="pay-btn" name="confirm_booking">Confirm Booking</button>
    </form>
</div>

<!-- Google Pay Form -->
<div class="payment-form" id="gpayForm" style="display: none;">
    <h3>Google Pay</h3>
    <form method="POST" action="process_booking.php" onsubmit="return validatePayment('gpay')">
        <?php echo generateHiddenFields($booking_data, $passenger_name, $passenger_email, $passenger_age); ?>
        <input type="text" name="gpay_upi" placeholder="yourupi@bank" required>
        <input type="hidden" name="payment_method" value="Google Pay">
        <button type="submit" class="pay-btn" name="confirm_booking">Confirm Booking</button>
    </form>
</div>

<!-- PhonePe Form -->
<div class="payment-form" id="phonepeForm" style="display: none;">
    <h3>PhonePe Payment</h3>
    <form method="POST" action="process_booking.php" onsubmit="return validatePayment('phonepe')">
        <?php echo generateHiddenFields($booking_data, $passenger_name, $passenger_email, $passenger_age); ?>
        <input type="text" name="phonepe_upi" placeholder="yourupi@bank" required>
        <input type="hidden" name="payment_method" value="PhonePe">
        <button type="submit" class="pay-btn" name="confirm_booking">Confirm Booking</button>
    </form>
</div>

<script>
function openPaymentOptions() {
    document.getElementById("paymentOptions").style.display = "block";
}

function closePaymentOptions() {
    document.getElementById("paymentOptions").style.display = "none";
}

function showPaymentForm(method) {
    document.getElementById("cardForm").style.display = "none";
    document.getElementById("gpayForm").style.display = "none";
    document.getElementById("phonepeForm").style.display = "none";
    document.getElementById(method + "Form").style.display = "block";
    closePaymentOptions();
}

function validatePayment(method) {
    document.getElementById("payment_method").value = method;
    return true;
}
</script>

</body>
</html>