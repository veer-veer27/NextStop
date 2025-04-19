<?php
session_start();
include 'db_connect.php'; // Ensure this file exists and connects to your database

// Fetch bookings data
$query = "SELECT id, user_id, bus_name, bus_no, bus_type, departure, destination, 
                 departure_time, arrival_time, selected_seats, total_price, payment_method, 
                 passenger_name, passenger_email, passenger_age, booking_date
          FROM booking
          ORDER BY booking_date DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bus Bookings</title>
    <style>
        /* General Body Styling */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #2c3e50, #4ca1af);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Container for Manage Bookings */
        .manage-bookings-container {
            width: 90%;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            overflow-x: auto;
        }

        /* Page Title */
        .manage-bookings-title {
            font-size: 22px;
            font-weight: bold;
            color: #2980b9;
            margin-bottom: 20px;
        }

        /* Table Styling */
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table-header {
            background: #f4f4f4;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }

        .table-row:nth-child(even) {
            background: #f9f9f9;
        }

        .table-data {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        /* Status Text */
        .status-text {
            font-style: italic;
            font-weight: bold;
            color: #555;
        }

        /* Back Button */
        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            background: #2980b9;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #1c5985;
        }

        /* Delete Button */
        .delete-btn {
            display: inline-block;
            padding: 6px 12px;
            text-decoration: none;
            background: #e74c3c;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: 0.3s;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        /* Responsive Design */
        @media (max-width: 750px) {
            .manage-bookings-container {
                width: 100%;
            }

            .bookings-table {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="manage-bookings-container">
        <h2 class="manage-bookings-title">Manage Bus Bookings</h2>
        <table class="bookings-table">
            <tr class="table-header">
                <th>Booking ID</th>
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
                <th>Passenger Name</th>
                <th>Passenger Email</th>
                <th>Passenger Age</th>
                <th>Booking Date</th>
                <th>Action</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="table-row">
                        <td class="table-data"><?php echo $row['id']; ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['bus_name']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['bus_no']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['bus_type']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['departure']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['destination']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['departure_time']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['arrival_time']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['selected_seats']); ?></td>
                        <td class="table-data"><?php echo "₹" . number_format($row['total_price'], 2); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['passenger_name']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['passenger_email']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['passenger_age']); ?></td>
                        <td class="table-data"><?php echo htmlspecialchars($row['booking_date']); ?></td>
                        <td class="table-data">
                            <a href="delete_booking.php?id=<?php echo urlencode($row['id']); ?>"
                               onclick="return confirm('Are you sure you want to delete this booking?');" 
                               class="delete-btn">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="16" style="text-align: center; font-weight: bold; padding: 10px;">
                        No bookings found.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
