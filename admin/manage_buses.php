<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bus_booking"; // Updated database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add a new bus
if (isset($_POST['add_bus'])) {
    $bus_no = $_POST['bus_no'];
    $bus_name = 'NextStop';
    $travel_date = $_POST['travel_date'];
    $state = 'Gujarat'; // Default state as Gujarat
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $bus_type = $_POST['bus_type'];
    $ticket_price = $_POST['ticket_price'];
    
    // Image upload handling
    $image_name = "";
    if (!empty($_FILES["bus_image"]["name"])) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES["bus_image"]["name"]); // Rename file to prevent duplicates
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png"];

        if (in_array($imageFileType, $allowed_types) && $_FILES["bus_image"]["size"] <= 5000000) { // Limit size to 5MB
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Create directory if not exists
            }
            move_uploaded_file($_FILES["bus_image"]["tmp_name"], $target_file);
        } else {
            echo "<script>alert('Invalid image file. Please upload JPG, JPEG, or PNG under 5MB.');</script>";
            $image_name = "";
        }
    }

    $stmt = $conn->prepare("INSERT INTO buses (
        bus_no, bus_name, departure, destination,
        departure_time, arrival_time, bus_type,
        ticket_price, travel_date, state, image
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "sssssssssss",
        $bus_no,
        $bus_name,
        $departure,
        $destination,
        $departure_time,
        $arrival_time,
        $bus_type,
        $ticket_price,
        $travel_date,
        $state,
        $image_name
    );

    if ($stmt->execute()) {
        echo "<script>alert('Bus added successfully!'); window.location.href='manage_buses.php';</script>";
    } else {
        echo "<script>alert('Error adding bus!');</script>";
    }
    $stmt->close();
}

// Handle Bus Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Get the image file name before deleting the bus
    $stmt = $conn->prepare("SELECT image FROM buses WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($image_name);
    $stmt->fetch();
    $stmt->close();

    // Delete the image file if exists
    if ($image_name && file_exists("uploads/" . $image_name)) {
        unlink("uploads/" . $image_name);
    }

    $stmt = $conn->prepare("DELETE FROM buses WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Bus deleted successfully!'); window.location.href='manage_buses.php';</script>";
}

// Fetch all buses
$sql = "SELECT * FROM buses";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buses</title>
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #2c3e50, #4ca1af);
            margin: 0;
            padding: 20px;
        }

        .manage-buses-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .manage-buses-title {
            font-size: 22px;
            font-weight: bold;
            color: #2980b9;
            margin-bottom: 20px;
        }

        .bus-form {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .input-field {
            width: 400px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
            text-align: center;
        }

        .add-btn {
            width: 70.5%;
            padding: 10px;
            background: green;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .add-btn:hover {
            background: darkgreen;
        }

        .buses-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        .table-header {
            background: #f4f4f4;
            padding: 10px;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
            text-align: center;
        }

        .table-data {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .delete-btn {
            padding: 6px 10px;
            color: white;
            background: red;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .delete-btn:hover {
            background: darkred;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            background: #2980b9;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 15px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #1c5985;
        }
        .departure{
            width: 420px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
            text-align: center;
        }
    </style>
<body>
    <div class="manage-buses-container">
        <h2 class="manage-buses-title">Manage Buses</h2>

        <form method="POST" enctype="multipart/form-data" class="bus-form">
            <input type="text" name="bus_no" placeholder="Bus No" required class="input-field">
            <input type="text" name="bus_name" value="NextStop" class="input-field" readonly>
            <input type="date" name="travel_date" required class="input-field">
            <input type="text" name="bus_type" placeholder="Bus Type" required class="input-field">


            <!-- Departure Dropdown -->
            <select name="departure" required class="departure">
                <option value="">Select Departure</option>
                <option value="AHMEDABAD JN - ADI (AHMEDABAD)">AHMEDABAD JN - ADI (AHMEDABAD)</option>
                <option value="AMRELI - AML (AMRELI)">AMRELI - AML (AMRELI)</option>
                <option value="ANAND - ANND (ANAND)">ANAND - ANND (ANAND)</option>
                <option value="ARAVALLI - ARV (ARAVALLI)">ARAVALLI - ARV (ARAVALLI)</option>
                <option value="BANASKANTHA - BSK (BANASKANTHA)">BANASKANTHA - BSK (BANASKANTHA)</option>
                <option value="BHARUCH - BH (BHARUCH)">BHARUCH - BH (BHARUCH)</option>
                <option value="BHAVNAGAR - BVC (BHAVNAGAR)">BHAVNAGAR - BVC (BHAVNAGAR)</option>
                <option value="BOTAD - BOT (BOTAD)">BOTAD - BOT (BOTAD)</option>
                <option value="CHHOTA UDEPUR - CUD (CHHOTA UDEPUR)">CHHOTA UDEPUR - CUD (CHHOTA UDEPUR)</option>
                <option value="DANG - DANG (DANG)">DANG - DANG (DANG)</option>
                <option value="DEVBHOOMI DWARKA - DBW (DEVBHOOMI DWARKA)">DEVBHOOMI DWARKA - DBW (DEVBHOOMI DWARKA)</option>
                <option value="DOHAD - DHD (DAHOD)">DOHAD - DHD (DAHOD)</option>
                <option value="GANDHINAGAR - GNC (GANDHINAGAR)">GANDHINAGAR - GNC (GANDHINAGAR)</option>
                <option value="GIR SOMNATH - GIR (GIR SOMNATH)">GIR SOMNATH - GIR (GIR SOMNATH)</option>
                <option value="JAMNAGAR - JAM (JAMNAGAR)">JAMNAGAR - JAM (JAMNAGAR)</option>
                <option value="JUNAGADH - JND (JUNAGADH)">JUNAGADH - JND (JUNAGADH)</option>
                <option value="KACHCHH - KAC (KACHCHH)">KACHCHH - KAC (KACHCHH)</option>
                <option value="KHEDA - KHD (KHEDA)">KHEDA - KHD (KHEDA)</option>
                <option value="MAHISAGAR - MAH (MAHISAGAR)">MAHISAGAR - MAH (MAHISAGAR)</option>
                <option value="MEHSANA - MSH (MEHSANA)">MEHSANA - MSH (MEHSANA)</option>
                <option value="MORBI - MBI (MORBI)">MORBI - MBI (MORBI)</option>
                <option value="NARMADA - NRD (NARMADA)">NARMADA - NRD (NARMADA)</option>
                <option value="NAVSARI - NVS (NAVSARI)">NAVSARI - NVS (NAVSARI)</option>
                <option value="PANCHMAHAL - PAN (PANCHMAHAL)">PANCHMAHAL - PAN (PANCHMAHAL)</option>
                <option value="PATAN - PTN (PATAN)">PATAN - PTN (PATAN)</option>
                <option value="PORBANDAR - PBR (PORBANDAR)">PORBANDAR - PBR (PORBANDAR)</option>
                <option value="RAJKOT - RJT (RAJKOT)">RAJKOT - RJT (RAJKOT)</option>
                <option value="SABARKANTHA - SBT (SABARKANTHA)">SABARKANTHA - SBT (SABARKANTHA)</option>
                <option value="SURAT - ST (SURAT)">SURAT - ST (SURAT)</option>
                <option value="SURENDRANAGAR - SRN (SURENDRANAGAR)">SURENDRANAGAR - SRN (SURENDRANAGAR)</option>
                <option value="TAPI - TPI (TAPI)">TAPI - TPI (TAPI)</option>
                <option value="VADODARA - BRC (VADODARA)">VADODARA - BRC (VADODARA)</option>
                <option value="VALSAD - VLD (VALSAD)">VALSAD - VLD (VALSAD)</option>
                <option value="SOMNATH - SMN (SOMNATH)">SOMNATH - SMN (SOMNATH)</option>
                <option value="STATUE OF UNITY - SUT (STATUE OF UNITY">STATUE OF UNITY - SUT (STATUE OF UNITY)</option>
                <option value="BHUJ - BHUJ (RANN OF KUTCH)  ">BHUJ - BHUJ (RANN OF KUTCH)</option>
                <option value="SASAN GIR - SG (GIR NATIONAL PARK)">SASAN GIR - SG (GIR NATIONAL PARK)</option>
                <option value="DWARKA - DWK (DWARKA)">DWARKA - DWK (DWARKA)</option>
            </select>

            <!-- Destination Dropdown -->
            <select name="destination" required class="departure">
            <option value="">Select Destination</option>
                <option value="AHMEDABAD JN - ADI (AHMEDABAD)">AHMEDABAD JN - ADI (AHMEDABAD)</option>
                <option value="AMRELI - AML (AMRELI)">AMRELI - AML (AMRELI)</option>
                <option value="ANAND - ANND (ANAND)">ANAND - ANND (ANAND)</option>
                <option value="ARAVALLI - ARV (ARAVALLI)">ARAVALLI - ARV (ARAVALLI)</option>
                <option value="BANASKANTHA - BSK (BANASKANTHA)">BANASKANTHA - BSK (BANASKANTHA)</option>
                <option value="BHARUCH - BH (BHARUCH)">BHARUCH - BH (BHARUCH)</option>
                <option value="BHAVNAGAR - BVC (BHAVNAGAR)">BHAVNAGAR - BVC (BHAVNAGAR)</option>
                <option value="BOTAD - BOT (BOTAD)">BOTAD - BOT (BOTAD)</option>
                <option value="CHHOTA UDEPUR - CUD (CHHOTA UDEPUR)">CHHOTA UDEPUR - CUD (CHHOTA UDEPUR)</option>
                <option value="DANG - DANG (DANG)">DANG - DANG (DANG)</option>
                <option value="DEVBHOOMI DWARKA - DBW (DEVBHOOMI DWARKA)">DEVBHOOMI DWARKA - DBW (DEVBHOOMI DWARKA)</option>
                <option value="DOHAD - DHD (DAHOD)">DOHAD - DHD (DAHOD)</option>
                <option value="GANDHINAGAR - GNC (GANDHINAGAR)">GANDHINAGAR - GNC (GANDHINAGAR)</option>
                <option value="GIR SOMNATH - GIR (GIR SOMNATH)">GIR SOMNATH - GIR (GIR SOMNATH)</option>
                <option value="JAMNAGAR - JAM (JAMNAGAR)">JAMNAGAR - JAM (JAMNAGAR)</option>
                <option value="JUNAGADH - JND (JUNAGADH)">JUNAGADH - JND (JUNAGADH)</option>
                <option value="KACHCHH - KAC (KACHCHH)">KACHCHH - KAC (KACHCHH)</option>
                <option value="KHEDA - KHD (KHEDA)">KHEDA - KHD (KHEDA)</option>
                <option value="MAHISAGAR - MAH (MAHISAGAR)">MAHISAGAR - MAH (MAHISAGAR)</option>
                <option value="MEHSANA - MSH (MEHSANA)">MEHSANA - MSH (MEHSANA)</option>
                <option value="MORBI - MBI (MORBI)">MORBI - MBI (MORBI)</option>
                <option value="NARMADA - NRD (NARMADA)">NARMADA - NRD (NARMADA)</option>
                <option value="NAVSARI - NVS (NAVSARI)">NAVSARI - NVS (NAVSARI)</option>
                <option value="PANCHMAHAL - PAN (PANCHMAHAL)">PANCHMAHAL - PAN (PANCHMAHAL)</option>
                <option value="PATAN - PTN (PATAN)">PATAN - PTN (PATAN)</option>
                <option value="PORBANDAR - PBR (PORBANDAR)">PORBANDAR - PBR (PORBANDAR)</option>
                <option value="RAJKOT - RJT (RAJKOT)">RAJKOT - RJT (RAJKOT)</option>
                <option value="SABARKANTHA - SBT (SABARKANTHA)">SABARKANTHA - SBT (SABARKANTHA)</option>
                <option value="SURAT - ST (SURAT)">SURAT - ST (SURAT)</option>
                <option value="SURENDRANAGAR - SRN (SURENDRANAGAR)">SURENDRANAGAR - SRN (SURENDRANAGAR)</option>
                <option value="TAPI - TPI (TAPI)">TAPI - TPI (TAPI)</option>
                <option value="VADODARA - BRC (VADODARA)">VADODARA - BRC (VADODARA)</option>
                <option value="VALSAD - VLD (VALSAD)">VALSAD - VLD (VALSAD)</option>
                <option value="SOMNATH - SMN (SOMNATH)">SOMNATH - SMN (SOMNATH)</option>
                <option value="STATUE OF UNITY - SUT (STATUE OF UNITY">STATUE OF UNITY - SUT (STATUE OF UNITY)</option>
                <option value="BHUJ - BHUJ (RANN OF KUTCH)  ">BHUJ - BHUJ (RANN OF KUTCH)</option>
                <option value="SASAN GIR - SG (GIR NATIONAL PARK)">SASAN GIR - SG (GIR NATIONAL PARK)</option>
                <option value="DWARKA - DWK (DWARKA)">DWARKA - DWK (DWARKA)</option>
            </select>

            <input type="time" name="departure_time" required class="input-field">
            <input type="time" name="arrival_time" required class="input-field">
            <input type="number" name="ticket_price" placeholder="Ticket Price" required class="input-field">

            <!-- Image Upload -->
            <input type="file" name="bus_image" accept="image/*" class="input-field">

            <button type="submit" name="add_bus" class="add-btn">Add Bus</button>
        </form>

        <table class="buses-table">
            <tr>
                <th class="table-header">Bus No</th>
                <th class="table-header">Bus Name</th>
                <th class="table-header">Departure</th>
                <th class="table-header">Destination</th>
                <th class="table-header">Departure Time</th>
                <th class="table-header">Arrival Time</th>
                <th class="table-header">Bus Type</th>
                <th class="table-header">Ticket Price</th>
                <th class="table-header">Travel Date</th>
                <th class="table-header">Image</th>
                <th class="table-header">Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td class="table-data"><?php echo $row['bus_no']; ?></td>
                    <td class="table-data"><?php echo $row['bus_name']; ?></td>
                    <td class="table-data"><?php echo $row['departure']; ?></td>
                    <td class="table-data"><?php echo $row['destination']; ?></td>
                    <td class="table-data"><?php echo $row['departure_time']; ?></td>
                    <td class="table-data"><?php echo $row['arrival_time']; ?></td>
                    <td class="table-data"><?php echo $row['bus_type']; ?></td>
                    <td class="table-data"><?php echo $row['ticket_price']; ?></td>
                    <td class="table-data"><?php echo $row['travel_date']; ?></td>
                    <td class="table-data">
                        <?php if ($row['image']) { ?>
                            <img src="uploads/<?php echo $row['image']; ?>" width="100" height="60" alt="Bus Image">
                        <?php } else { echo "No Image"; } ?>
                    </td>
                    <td class="table-data">
                        <a href="manage_buses.php?delete_id=<?php echo $row['id']; ?>" 
                           class="delete-btn" 
                           onclick="return confirm('Are you sure you want to delete this bus?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
