<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in!'); window.location.href='../login page/index.php';</script>";
    exit();
}

// Store booking details in session if coming from schedule.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_now'])) {
    $_SESSION['booking_details'] = [
        'bus_name' => $_POST['bus_name'],
        'bus_no' => $_POST['bus_no'],
        'bus_type' => $_POST['bus_type'],
        'departure' => $_POST['departure'],
        'destination' => $_POST['destination'],
        'departure_time' => $_POST['departure_time'],
        'arrival_time' => $_POST['arrival_time'],
        'selected_seats' => "1", // Change this to actual seat selection logic
        'total_price' => $_POST['fare'],
    ];
}

// Redirect if no booking details exist
if (!isset($_SESSION['booking_details'])) {
    echo "<script>alert('No booking details found!'); window.location.href='schedule.php';</script>";
    exit();
}

$booking = $_SESSION['booking_details'];
$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT firstName, lastName, email, age FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<script>alert('User details not found!'); window.location.href='schedule.php';</script>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="confirm_booking.css">
</head>
<style>
        /* Video Background */
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

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

        /* Booking Details Container */
        .container {
            max-width: 600px;
            width: 90%;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            z-index: 2;
            margin-bottom: 400px;
            top: 50%;
            transform: translateY(-50%);
        }

        h2 {
            color: white;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            width: 500px;
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

        /* Payment Popup */
        .payment-options {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 10;
            text-align: center;
            width: 300px;
            border-radius: 10px;
        }

        .payment-options h3 {
            margin-bottom: 10px;
        }

        .payment-options button {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: none;
            cursor: pointer;
        }

        .payment-options .close-btn {
            background: red;
            color: white;
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

    <div class="container">
        <h2>Booking Details</h2>

        <table>
    <tr><th>Passenger</th><td><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></td></tr>
    <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
    <tr><th>Age</th><td><?php echo htmlspecialchars($user['age']); ?></td></tr>
    <tr><th>Bus Name</th><td><?php echo htmlspecialchars($booking['bus_name']); ?></td></tr>
    <tr><th>Bus No</th><td><?php echo htmlspecialchars($booking['bus_no']); ?></td></tr>
    <tr><th>Bus Type</th><td><?php echo htmlspecialchars($booking['bus_type']); ?></td></tr>
    <tr><th>Departure</th><td><?php echo htmlspecialchars($booking['departure']); ?></td></tr>
    <tr><th>Destination</th><td><?php echo htmlspecialchars($booking['destination']); ?></td></tr>
    <tr><th>Departure Time</th><td><?php echo htmlspecialchars($booking['departure_time']); ?></td></tr>
    <tr><th>Arrival Time</th><td><?php echo htmlspecialchars($booking['arrival_time']); ?></td></tr>
    <tr><th>Selected Seats</th><td><?php echo htmlspecialchars($booking['selected_seats']); ?></td></tr>
    <tr><th>Total Price</th><td>₹ <?php echo number_format($booking['total_price'], 2); ?></td></tr>
</table>


        <button class="btn" onclick="openPaymentOptions()">Pay Now</button>
    </div>

        <div class="payment-options" id="paymentOptions">
            <h3>Select Payment Method</h3>
            <button onclick="showPaymentForm('card')">Card Payment</button>
            <button onclick="showPaymentForm('gpay')">GPay</button>
            <button onclick="showPaymentForm('phonepe')">PhonePe</button>
            <button class="close-btn" onclick="closePaymentOptions()">Close</button>
        </div>

        <!-- Card Payment Form -->
    <!-- Card Payment Form -->
    <div class="payment-form" id="cardForm" style="display: none;">
        <h3>Enter Card Details</h3>
        <form method="POST" action="process_booking.php" onsubmit="return processPayment('card')">
            <label>Name on Card</label>
            <input type="text" id="cardName" name="card_name" placeholder="Cardholder Name" required>

            <label>Card Number</label>
            <input type="text" id="cardNumber" name="card_number" placeholder="XXXX XXXX XXXX XXXX" required>

            <label>Expiry Date</label>
            <input type="month" id="cardExpiry" name="card_expiry" required>

            <label>CVV</label>
            <input type="text" id="cardCVV" name="card_cvv" placeholder="CVV" required>

            <input type="hidden" name="payment_method" value="Card">
            <button type="submit" name="confirm_booking" class="btn">Confirm Booking</button>
        </form>
    </div>

    <!-- Google Pay Form -->
    <div class="payment-form" id="gpayForm" style="display: none;">
        <h3>Google Pay</h3>
        <form method="POST" action="process_booking.php" onsubmit="return processPayment('gpay')">
            <label>UPI ID</label>
            <input type="text" id="gpayUpi" name="gpay_upi" placeholder="yourupi@bank" required>

            <input type="hidden" name="payment_method" value="Google Pay">
            <button type="submit" name="confirm_booking" class="btn">Confirm Booking</button>
        </form>
    </div>

    <!-- PhonePe Form -->
    <div class="payment-form" id="phonepeForm" style="display: none;">
        <h3>PhonePe Payment</h3>
        <form method="POST" action="process_booking.php" onsubmit="return processPayment('phonepe')">
            <label>UPI ID</label>
            <input type="text" id="phonepeUpi" name="phonepe_upi" placeholder="yourupi@bank" required>

            <input type="hidden" name="payment_method" value="PhonePe">
            <button type="submit" name="confirm_booking" class="btn">Confirm Booking</button>
        </form>
    </div>

        <script>
    function processPayment(method) {
        if (method === "card") {
            let name = document.getElementById("cardName").value.trim();
            let number = document.getElementById("cardNumber").value.trim();
            let expiry = document.getElementById("cardExpiry").value.trim();
            let cvv = document.getElementById("cardCVV").value.trim();

            if (name === "" || number === "" || expiry === "" || cvv === "") {
                alert("Please fill in all card details.");
                return false;
            }

            if (!/^\d{16}$/.test(number)) {
                alert("Invalid card number! Must be 16 digits.");
                return false;
            }

            if (!/^\d{3}$/.test(cvv)) {
                alert("Invalid CVV! Must be 3 digits.");
                return false;
            }
        } 

        else if (method === "gpay") {
            let upi = document.getElementById("gpayUpi").value.trim();
            if (!upi.includes("@")) {
                alert("Invalid UPI ID for Google Pay!");
                return false;
            }
        } 

        else if (method === "phonepe") {
            let upi = document.getElementById("phonepeUpi").value.trim();
            if (!upi.includes("@")) {
                alert("Invalid UPI ID for PhonePe!");
                return false;
            }
        }

        return true;
    }
    function openPaymentOptions() {
        document.getElementById("paymentOptions").style.display = "block";
    }

    function closePaymentOptions() {
        document.getElementById("paymentOptions").style.display = "none";
    }

    function showPaymentForm(method) {
        // Hide all payment forms first
        document.getElementById("cardForm").style.display = "none";
        document.getElementById("gpayForm").style.display = "none";
        document.getElementById("phonepeForm").style.display = "none";

        // Show the selected payment form
        document.getElementById(method + "Form").style.display = "block";

        // Close the payment options popup
        closePaymentOptions();
    }

        </script>
</body>
</html>
