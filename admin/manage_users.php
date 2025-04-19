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

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!');</script>";
    }
    $stmt->close();
}

// Fetch all users
$sql = "SELECT id, firstName, lastName, email FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #2c3e50, #4ca1af);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .manage-users-container {
            width: 1200px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .manage-users-title {
            font-size: 22px;
            font-weight: bold;
            color: #2980b9;
            margin-bottom: 20px;
        }

        .users-table {
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

        .table-data {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .delete-btn, .know-more-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .delete-btn {
            background: red;
            color: white;
        }

        .delete-btn:hover {
            background: darkred;
        }

        .know-more-btn {
            background: #3498db;
            color: white;
            margin-left: 5px;
        }

        .know-more-btn:hover {
            background: #1c5985;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            background:rgb(64, 147, 202);
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
        .know-more-btn{
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <div class="manage-users-container">
        <h2 class="manage-users-title">Manage Users</h2>
        <table class="users-table">
            <tr>
                <th class="table-header">ID</th>
                <th class="table-header">First Name</th>
                <th class="table-header">Last Name</th>
                <th class="table-header">Email</th>
                <th class="table-header">Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td class="table-data"><?php echo $row['id']; ?></td>
                    <td class="table-data"><?php echo $row['firstName']; ?></td>
                    <td class="table-data"><?php echo $row['lastName']; ?></td>
                    <td class="table-data"><?php echo $row['email']; ?></td>
                    <td class="table-data">
                        <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this user?');">
                            <button class="delete-btn">Delete</button>
                        </a>
                        <a href="user_details.php?user_id=<?php echo $row['id']; ?>">
                            <button class="know-more-btn">Know More</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <br>
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
