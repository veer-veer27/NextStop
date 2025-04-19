<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bus_booking"; // Updated to bus booking database

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total users
$user_query = "SELECT COUNT(*) AS total_users FROM users";
$user_result = $conn->query($user_query);
$user_data = $user_result->fetch_assoc();
$total_users = $user_data['total_users'];

// Fetch total bookings
$booking_query = "SELECT COUNT(*) AS total_bookings FROM booking";
$booking_result = $conn->query($booking_query);
$booking_data = $booking_result->fetch_assoc();
$total_bookings = $booking_data['total_bookings'];

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        /* ====== General Styles ====== */
        body {
            font-family: Arial, sans-serif;
            background-image: url('../login page/image/login page 6.png');
            background-size: cover;
            /* Ensure the background image covers the entire page */
            background-position: center;
            /* Center the background image */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* ====== Dashboard Container ====== */
        .dashboard-container {
            width: 520px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        /* ====== Dashboard Title ====== */
        .dashboard-title {
            font-size: 22px;
            font-weight: bold;
            color:rgb(55, 147, 208);
            margin-bottom: 20px;
        }

        /* ====== Dashboard Cards ====== */
        .dashboard-cards {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            width: 48%;
            text-align: center;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .card-value {
            font-size: 18px;
            color: #555;
        }

        /* ====== Dashboard Links ====== */
        .dashboard-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .dashboard-btn {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: white;
            background:rgb(71, 147, 198);
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }

        .dashboard-btn:hover {
            background: #1c5985;
        }

        .logout-btn {
            background: red;
        }

        .logout-btn:hover {
            background: darkred;
        }

        /* ====== Responsive Design ====== */
        @media (max-width: 500px) {
            .dashboard-container {
                width: 90%;
            }
            
            .dashboard-cards {
                flex-direction: column;
            }

            .card {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2 class="dashboard-title">Welcome, <?php echo $_SESSION["admin_username"]; ?>!</h2>
        <div class="dashboard-cards">
            <div class="card">
                <h3 class="card-title">Total Users</h3>
                <p class="card-value"><?php echo $total_users; ?></p>
            </div>
            <div class="card">
                <h3 class="card-title">Total Bookings</h3>
                <p class="card-value"><?php echo $total_bookings; ?></p>
            </div>
        </div>
        <div class="dashboard-links">
            <a href="manage_users.php" class="dashboard-btn">Manage Users</a>
            <a href="manage_bookings.php" class="dashboard-btn">Manage Bookings</a>
            <a href="manage_buses.php" class="dashboard-btn">Manage Buses</a>
            <a href="admin_logout.php" class="dashboard-btn logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>
