<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bus_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['user_id'])) {
    echo "<script>alert('No user selected!'); window.location.href='manage_users.php';</script>";
    exit();
}

$user_id = $_GET['user_id'];

// Fetch user details
$user_sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

// Fetch user bookings
$booking_sql = "SELECT * FROM booking WHERE user_id = ?";
$stmt = $conn->prepare($booking_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$booking_result = $stmt->get_result();
$stmt->close();
// ✅ Fetch contact messages by email match
$contact_sql = "SELECT name, email, subject, message, created_at FROM contacts WHERE email = ?";
$stmt = $conn->prepare($contact_sql);
$stmt->bind_param("s", $user['email']);

$stmt->execute();
$contact_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #2c3e50, #4ca1af);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .user-details-container {
            width: 90%;
            max-width: 1000px;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .user-details-title {
            font-size: 24px;
            color: #2980b9;
            margin-bottom: 20px;
            text-align: center;
        }

        .details-table, .bookings-table, .contacts-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: left;
        }

        .table-header {
            background-color: #2980b9;
            color: #fff;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }

        .table-data {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
            color: #333;
        }

        .table-data:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            background: #2980b9;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
        }

        .back-btn:hover {
            background: #1c5985;
        }
    </style>
</head>
<body>

<div class="user-details-container">
    <h2 class="user-details-title">User Details</h2>
    <table class="details-table">
        <tr><th class="table-header">ID</th><td class="table-data"><?php echo $user['id']; ?></td></tr>
        <tr><th class="table-header">First Name</th><td class="table-data"><?php echo $user['firstName']; ?></td></tr>
        <tr><th class="table-header">Last Name</th><td class="table-data"><?php echo $user['lastName']; ?></td></tr>
        <tr><th class="table-header">Email</th><td class="table-data"><?php echo $user['email']; ?></td></tr>
        <tr><th class="table-header">Phone</th><td class="table-data"><?php echo $user['phone']; ?></td></tr>
        <tr><th class="table-header">Created At</th><td class="table-data"><?php echo $user['created_at']; ?></td></tr>
        <tr><th class="table-header">Age</th><td class="table-data"><?php echo $user['age']; ?></td></tr>
    </table>

    <h2 class="user-details-title">Booking History</h2>
    <table class="bookings-table">
        <tr>
            <th class="table-header">Booking ID</th>
            <th class="table-header">Bus ID</th>
            <th class="table-header">Seats</th>
            <th class="table-header">Total Price</th>
            <th class="table-header">Booking Date</th>
        </tr>
        <?php while ($booking = $booking_result->fetch_assoc()) { ?>
            <tr>
                <td class="table-data"><?php echo $booking['id']; ?></td>
                <td class="table-data"><?php echo $booking['bus_no']; ?></td>
                <td class="table-data"><?php echo $booking['selected_seats']; ?></td>
                <td class="table-data"><?php echo $booking['total_price']; ?></td>
                <td class="table-data"><?php echo $booking['booking_date']; ?></td>
            </tr>
        <?php } ?>
    </table>

    <h2 class="user-details-title">Contact Messages</h2>
    <table class="contacts-table">
        <tr>
            <th class="table-header">Name</th>
            <th class="table-header">Email</th>
            <th class="table-header">Subject</th>
            <th class="table-header">Message</th>
            <th class="table-header">Date</th>
        </tr>
        <?php
        if ($contact_result->num_rows > 0) {
            while ($contact = $contact_result->fetch_assoc()) {
                echo "<tr>
                    <td class='table-data'>{$contact['name']}</td>
                    <td class='table-data'>{$contact['email']}</td>
                    <td class='table-data'>{$contact['subject']}</td>
                    <td class='table-data'>{$contact['message']}</td>
                    <td class='table-data'>{$contact['created_at']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td class='table-data' colspan='5'>No contact messages found.</td></tr>";
        }
        ?>
    </table>

    <a href="manage_users.php" class="back-btn">Back to Users</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
