<?php
session_start();
require_once '../login page/connect.php'; // Ensure correct database connection

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_booking'])) {
    if (!isset($_SESSION['booking_details']) || !isset($_SESSION['user_id'])) {
        echo "<script>alert('No booking details found!'); window.location.href='available_buses.php';</script>";
        exit();
    }

    $booking = $_SESSION['booking_details'];
    $user_id = $_SESSION['user_id'];

    // Fetch user details
    $user_query = "SELECT firstName, lastName, email, age FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($user_query);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $passenger_name = $user['firstName'] . " " . $user['lastName'];
        $passenger_email = $user['email'];
        $passenger_age = $user['age'];
    } else {
        $passenger_name = "Unknown";
        $passenger_email = "unknown@example.com";
        $passenger_age = 0;
    }
    $stmt_user->close();

    // Extract booking details
    $bus_name = $booking['bus_name'];
    $bus_no = $booking['bus_no'];
    $bus_type = $booking['bus_type'];
    $departure = $booking['departure'];
    $destination = $booking['destination'];
    $departure_time = $booking['departure_time'];
    $arrival_time = $booking['arrival_time'];
    $selected_seats = $booking['selected_seats'];
    $total_price = $booking['total_price'];

    // Ensure payment method is stored
    if (!isset($_POST['payment_method']) || empty($_POST['payment_method'])) {
        echo "<script>alert('Error: Payment method not selected.'); window.location.href='confirm_booking.php';</script>";
        exit();
    }

    $payment_method = $_POST['payment_method'];

    // Fetch already booked seats for this bus
    $fetch_seats_query = "SELECT selected_seats FROM booking WHERE bus_no = ?";
    $stmt_fetch_seats = $conn->prepare($fetch_seats_query);
    $stmt_fetch_seats->bind_param("s", $bus_no);
    $stmt_fetch_seats->execute();
    $result_seats = $stmt_fetch_seats->get_result();

    $booked_seats = [];
    while ($row = $result_seats->fetch_assoc()) {
        $booked_seats = array_merge($booked_seats, explode(',', $row['selected_seats']));
    }
    $stmt_fetch_seats->close();

    // Convert selected seats to an array
    $user_selected_seats = explode(',', $selected_seats);

    // Check if any selected seat is already booked
    $conflict = array_intersect($user_selected_seats, $booked_seats);

    if (!empty($conflict)) {
        echo "<script>alert('Error: Seats " . implode(', ', $conflict) . " are already booked. Please select different seats.'); window.location.href='available_buses.php';</script>";
        exit();
    }

    // Merge booked seats
    if (!empty($booked_seats)) {
        $updated_seats = implode(',', array_merge($booked_seats, $user_selected_seats));
    } else {
        $updated_seats = $selected_seats;
    }

    // Insert booking into database
    $sql = "INSERT INTO booking (user_id, bus_name, bus_no, bus_type, departure, destination, departure_time, arrival_time, selected_seats, total_price, payment_method, passenger_name, passenger_email, passenger_age, seat_reservations) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssdsssss", $user_id, $bus_name, $bus_no, $bus_type, $departure, $destination, $departure_time, $arrival_time, $selected_seats, $total_price, $payment_method, $passenger_name, $passenger_email, $passenger_age, $updated_seats);
    if ($stmt->execute()) {
        // ✅ Step 1: Get the inserted booking ID
        $booking_id = $stmt->insert_id;
    
        // ✅ Step 2: Fetch the booking_date for that ID
        $query = "SELECT booking_date FROM booking WHERE id = ?";
        $stmt_date = $conn->prepare($query);
        $stmt_date->bind_param("i", $booking_id);
        $stmt_date->execute();
        $result = $stmt_date->get_result();
        $row = $result->fetch_assoc();
        $booking_date = $row['booking_date'];
        $stmt_date->close();
    
        // ✅ Step 3: Save ID and booking date to session
        $_SESSION['booking_details']['booking_id'] = $booking_id;
        $_SESSION['booking_details']['booking_date'] = $booking_date;
    
        // ✅ Step 4: Redirect
        echo "<script>alert('Booking Confirmed with $payment_method!'); window.location.href='payment_success.php';</script>";
    }
    else {
        echo "<script>alert('Error: Could not confirm booking.'); window.location.href='confirm_booking.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
