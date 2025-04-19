<?php
session_start();
include 'config.php';

// Clear any old session data
unset($_SESSION['booking_error']);
unset($_SESSION['booking_details']);  // Clear previous booking data

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    // Verify CSRF token if you implement it
    // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    //     die("Invalid CSRF token");
    // }

    if (!isset($_SESSION['user_email'])) {
        die("User not logged in!");
    }

    // Validate required fields
    $required_fields = [
        'bus_name',
        'bus_no',
        'bus_type',
        'departure',
        'destination',
        'departure_time',
        'arrival_time',
        'selected_seats',
        'total_price',
        'payment_method'
    ];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['booking_error'] = "Missing required field: $field";
            header("Location: schedule.php");
            exit();
        }
    }

    $user_email = $_SESSION['user_email'];
    $selected_seats = trim($_POST['selected_seats']);
    $bus_no = $_POST['bus_no'];
    $seats_array = explode(',', $selected_seats);

    // Fetch user details
    $user_query = "SELECT id, firstName, lastName, email, age FROM users WHERE email = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows !== 1) {
        die("User details not found!");
    }
    $user = $user_result->fetch_assoc();

    // Check seat availability (NEW IMPORTANT PART)
    $check_query = "SELECT selected_seats FROM booking WHERE bus_no = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $bus_no);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    $booked_seats = [];
    while ($row = $check_result->fetch_assoc()) {
        $booked_seats = array_merge($booked_seats, explode(',', $row['selected_seats']));
    }

    // Verify selected seats are available
    foreach ($seats_array as $seat) {
        $seat = trim($seat);
        if (in_array($seat, $booked_seats)) {
            $_SESSION['booking_error'] = "Seat $seat is already booked! Please select different seats.";
            header("Location: ../schedule/scchedule.php");
            exit();
        }
    }

    // Prepare booking data
    $booking_data = [
        'user_id' => $user['id'],
        'bus_name' => $_POST['bus_name'],
        'bus_no' => $bus_no,
        'bus_type' => $_POST['bus_type'],
        'departure' => $_POST['departure'],
        'destination' => $_POST['destination'],
        'departure_time' => $_POST['departure_time'],
        'arrival_time' => $_POST['arrival_time'],
        'selected_seats' => $selected_seats,
        'total_price' => $_POST['total_price'],
        'payment_method' => $_POST['payment_method'],
        'passenger_name' => $user['firstName'] . " " . $user['lastName'],
        'passenger_email' => $user['email'],
        'passenger_age' => $user['age']
    ];

    // Insert booking (using transaction for safety)
    $conn->begin_transaction();

    try {
        $insert_query = "INSERT INTO booking (
            user_id, bus_name, bus_no, bus_type, 
            departure, destination, departure_time, 
            arrival_time, selected_seats, total_price, 
            payment_method, passenger_name, passenger_email, passenger_age
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param(
            "isssssssssssss",
            $booking_data['user_id'],
            $booking_data['bus_name'],
            $booking_data['bus_no'],
            $booking_data['bus_type'],
            $booking_data['departure'],
            $booking_data['destination'],
            $booking_data['departure_time'],
            $booking_data['arrival_time'],
            $booking_data['selected_seats'],
            $booking_data['total_price'],
            $booking_data['payment_method'],
            $booking_data['passenger_name'],
            $booking_data['passenger_email'],
            $booking_data['passenger_age']
        );

        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }

        // Update bus available seats count
        $update_query = "UPDATE buses SET available_seats = available_seats - ? WHERE bus_no = ?";
        $update_stmt = $conn->prepare($update_query);
        $seats_count = count($seats_array);
        $update_stmt->bind_param("is", $seats_count, $bus_no);

        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update seat availability");
        }

        $conn->commit();

        // Store minimal booking data in session
        $_SESSION['booking_id'] = $stmt->insert_id;
        // Replace your current session storage with this:
        $_SESSION['booking_details'] = [
            'bus_name' => $booking_data['bus_name'],
            'bus_no' => $booking_data['bus_no'],
            'departure' => $booking_data['departure'],
            'destination' => $booking_data['destination'],
            'departure_time' => $booking_data['departure_time'],
            'arrival_time' => $booking_data['arrival_time'],
            'selected_seats' => $booking_data['selected_seats'],
            'total_price' => $booking_data['total_price'],
            'booking_date' => date('Y-m-d'), // Add current date as booking date
            'booking_id' => $stmt->insert_id // Store the actual booking ID
        ];

        session_regenerate_id(true);
        header("Location: payment_success.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['booking_error'] = $e->getMessage();
        header("Location: ../schedule/scchedule.php");
        exit();
    }
} else {
    header("Location: ../schedule/scchedule.php");
    exit();
}
?>