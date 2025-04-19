<?php
session_start();
include 'db_connect.php'; // Include database connection

// Ensure session security
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login page/index.php");
    exit();
}

$email = $_SESSION['user_email'];

// Fetch user details
$sql_user = "SELECT firstName, lastName, email, phone FROM users WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Get user details
$full_name = isset($user['firstName'], $user['lastName']) ? htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) : "N/A";
$phone = isset($user['phone']) ? htmlspecialchars($user['phone']) : "N/A";

// Check if the user searched for a specific ticket
$search_id = "";
$search_result = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_id'])) {
    $search_id = trim($_POST['search_id']);
    
    if (!empty($search_id)) {
        $sql_search = "SELECT id, bus_name, bus_no, bus_type, departure, destination, departure_time, arrival_time, selected_seats, total_price, payment_method, passenger_name, booking_date 
                       FROM booking 
                       WHERE id = ? AND passenger_email = ?";
        $stmt_search = $conn->prepare($sql_search);
        $stmt_search->bind_param("is", $search_id, $email);
        $stmt_search->execute();
        $search_result = $stmt_search->get_result();
    }
}

// Fetch all user bookings if no search query
if (empty($search_id) || $search_result->num_rows === 0) {
    $sql_bookings = "SELECT id, bus_name, bus_no, bus_type, departure, destination, departure_time, arrival_time, selected_seats, total_price, payment_method, passenger_name, booking_date 
                     FROM booking 
                     WHERE passenger_email = ?";
    $stmt_bookings = $conn->prepare($sql_bookings);
    $stmt_bookings->bind_param("s", $email);
    $stmt_bookings->execute();
    $result_bookings = $stmt_bookings->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Bus Booking</title>
    <link rel="stylesheet" href="account.css">
</head>
<style>.find-ticket-form {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.input-field {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    width: 250px;
}

.find-ticket-btn {
    padding: 8px 15px;
    background-color: #27ae60;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.find-ticket-btn:hover {
    background-color:rgb(17, 157, 75);
}
</style>
<body>

    <div class="container">
        <h2 class="heading">Welcome</h2>

        <div class="user-details">
            <p><strong>Name:</strong> <?php echo $full_name; ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Phone:</strong> <?php echo $phone; ?></p>
        </div>

        <h3 class="sub-heading">Find Ticket</h3>
        <form method="POST" action="" class="find-ticket-form">
            <input type="text" name="search_id" class="input-field" placeholder="Enter Booking ID" required>
            <button type="submit" class="find-ticket-btn">Find Ticket</button>
        </form>

        <?php if (isset($_SESSION['message'])) { ?>
            <p class="success-message"><?php echo $_SESSION['message'];
            unset($_SESSION['message']); ?></p>
        <?php } ?>

        <?php if (isset($_SESSION['error'])) { ?>
            <p class="error-message"><?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?></p>
        <?php } ?>

        <h3 class="sub-heading">My Bookings</h3>

        <?php if (!empty($search_id) && $search_result->num_rows === 0) { ?>
            <p class="error-message">No booking found for ID: <?php echo htmlspecialchars($search_id); ?></p>
        <?php } ?>

        <?php 
        // Display results based on search or all bookings
        $bookings_data = !empty($search_id) && $search_result->num_rows > 0 ? $search_result : $result_bookings;

        if ($bookings_data->num_rows > 0) { ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Bus Name</th>
                            <th>Bus No</th>
                            <th>Bus Type</th>
                            <th>Departure</th>
                            <th>Destination</th>
                            <th>Departure Time</th>
                            <th>Arrival Time</th>
                            <th>Selected Seats</th>
                            <th>Total Price</th>
                            <th>Payment Method</th>
                            <th>Booking Date</th>
                            <th class="actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings_data->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['bus_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['bus_no']); ?></td>
                                <td><?php echo htmlspecialchars($booking['bus_type']); ?></td>
                                <td><?php echo htmlspecialchars($booking['departure']); ?></td>
                                <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                                <td><?php echo htmlspecialchars($booking['departure_time']); ?></td>
                                <td><?php echo htmlspecialchars($booking['arrival_time']); ?></td>
                                <td><?php echo htmlspecialchars($booking['selected_seats']); ?></td>
                                <td>&#8377; <?php echo number_format($booking['total_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($booking['payment_method']); ?></td>
                                <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                <td style="display: flex; gap: 10px;">
                                    <a href="download_ticket.php?id=<?= urlencode($booking['id']); ?>" class="download-btn">
                                        Download Ticket
                                    </a>
                                    <a href="cancel_booking.php?id=<?= urlencode($booking['id']); ?>" class="download-btn"
                                       onclick="return confirm('Are you sure you want to cancel this booking?');">
                                        Cancel Ticket
                                    </a>
                                    <a href="../tracking/bus_tracking.php?bus_no=<?= urlencode($booking['bus_no']); ?>" class="download-btn">
                                        Track Bus
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="no-bookings">No bookings found.</p>
        <?php } ?>

        <a href="logout.php" class="logout-btn" onclick="return confirmLogout();">Logout</a>
    </div>

    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }
    </script>

</body>

</html>
