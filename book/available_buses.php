<?php
session_start();
include "db_connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo "<script>
                alert('You need to log in first to book a ticket!');
                window.location.href = '../login page/index.php'; // Redirect to login page
            </script>";
    exit();
}

// Initialize variables
$departure = $destination = $travel_date = "";
$result = null;
$booked_seats = []; // Array to store booked seats

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departure = $_POST["departure"] ?? null;
    $destination = $_POST["destination"] ?? null;
    $travel_date = $_POST["travel_date"] ?? null;

    if (!$departure || !$destination || !$travel_date) {
        die("Error: All fields are required!");
    }

    // Query buses based on user input
    $query = "SELECT bus_name, bus_no, bus_type, departure, destination, departure_time, arrival_time, ticket_price, travel_date, image 
          FROM buses WHERE departure = ? AND destination = ? AND travel_date = ?";

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("sss", $departure, $destination, $travel_date);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        die("Query error: " . $conn->error);
    }
}

// Fetch booked seats for selected buses
if (isset($_GET['bus_no'])) {
    $bus_no = $_GET['bus_no'];
    $fetch_seats_query = "SELECT selected_seats FROM booking WHERE bus_no = ?";
    $stmt_fetch_seats = $conn->prepare($fetch_seats_query);
    $stmt_fetch_seats->bind_param("s", $bus_no);
    $stmt_fetch_seats->execute();
    $result_seats = $stmt_fetch_seats->get_result();

    while ($row = $result_seats->fetch_assoc()) {
        $booked_seats = array_merge($booked_seats, explode(',', $row['selected_seats']));
    }
    $stmt_fetch_seats->close();

    // Convert booked seats to an array of integers
    $booked_seats = array_map('intval', $booked_seats);
}

// Get user details
$userFirstName = $_SESSION['first_name'] ?? 'Guest';
$userLastName = $_SESSION['last_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? 'Not Available';
$userAge = $_SESSION['user_age'] ?? 'N/A';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Availability</title>
    <link rel="stylesheet" type="text/css" href="style10.css">

    <style>
        .modal {
            /* Seat selection modal styling */
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
            width: 600px;
        }

        .modal-content {
            text-align: center;
        }

        .driver-seat {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .bus-layout {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 15px;
        }

        #seatsContainer {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .row {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .seat {
            width: 45px;
            height: 45px;
            background: green;
            text-align: center;
            line-height: 45px;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        .seat:hover {
            background: blue;
        }

        .seat.selected {
            background: red;
        }

        .aisle {
            width: 20px;
        }
    </style>
</head>

<body>

    <h2>Available Buses from <?php echo htmlspecialchars($departure); ?> to
        <?php echo htmlspecialchars($destination); ?> on <?php echo htmlspecialchars($travel_date); ?></h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1" class="table">
            <tr>
                <th>Image</th> <!-- Add this new column -->
                <th>Bus Name</th>
                <th>Bus No</th>
                <th>Bus Type</th>
                <th>Departure</th>
                <th>Destination</th>
                <th>Departure Time</th>
                <th>Arrival Time</th>
                <th>Base Price (₹)</th>
                
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="../admin/uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Bus Image" width="180"
                            height="100" style="border-radius: 5px;">
                    </td>
                    <td><?php echo htmlspecialchars($row['bus_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['bus_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['bus_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure']); ?></td>
                    <td><?php echo htmlspecialchars($row['destination']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['arrival_time']); ?></td>
                    <td>₹ <?php echo number_format($row['ticket_price'], 2); ?></td>
                    
                    <td>
                        <button onclick="openSeatSelectionModal('<?php echo $row['bus_name']; ?>', 
                                                 '<?php echo $row['bus_no']; ?>', 
                                                 '<?php echo $row['ticket_price']; ?>',
                                                 '<?php echo $row['departure_time']; ?>',
                                                 '<?php echo $row['arrival_time']; ?>',
                                                 '<?php echo $row['bus_type']; ?>')">Book Now</button>
                    </td>
                </tr>

            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div style="text-align: center; margin-top: 20px; font-size: 18px; color: red;">
    <strong>🚌 No buses available for today. Please check back later.</strong>
</div>

    <?php endif; ?>

    <!-- Seat Selection Modal -->
    <div id="seatSelectionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeSeatSelectionModal()">&times;</span>
            <h3>Select Your Seats</h3>
            <div class="bus-layout">
                <div class="driver-seat">🚌 Driver</div>
                <div id="seatsContainer"></div>
            </div>
            <p>Selected Seats: <span id="selectedSeats">None</span></p>
            <p>Total Price: ₹<span id="totalPrice">0</span></p>

            <form id="bookingForm" action="store_booking.php" method="POST">
                <input type="hidden" name="bus_name" id="busNameInput">
                <input type="hidden" name="bus_no" id="busNoInput">
                <input type="hidden" name="ticket_price" id="ticketPriceInput">
                <input type="hidden" name="departure_time" id="departureTimeInput">
                <input type="hidden" name="arrival_time" id="arrivalTimeInput">
                <input type="hidden" name="bus_type" id="busTypeInput">
                <input type="hidden" name="selected_seats" id="selectedSeatsInput">
                <input type="hidden" name="total_price" id="totalPriceInput">

                <!-- ✅ Fix: Send departure & destination -->
                <input type="hidden" name="departure" value="<?php echo htmlspecialchars($departure); ?>">
                <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">

                <input type="hidden" name="passenger_name"
                    value="<?php echo isset($_SESSION['passenger_name']) ? htmlspecialchars($_SESSION['passenger_name']) : ''; ?>">
                <input type="hidden" name="passenger_email"
                    value="<?php echo isset($_SESSION['passenger_email']) ? htmlspecialchars($_SESSION['passenger_email']) : ''; ?>">
                <input type="hidden" name="passenger_age"
                    value="<?php echo isset($_SESSION['passenger_age']) ? htmlspecialchars($_SESSION['passenger_age']) : ''; ?>">


                <button type="submit">Confirm Booking</button>
            </form>


        </div>
    </div>

    <script>
        let selectedSeats = [];
        let seatPrice = 0;
        function openSeatSelectionModal(busName, busNo, price, departureTime, arrivalTime, busType) {
            seatPrice = parseFloat(price);
            selectedSeats = [];

            document.getElementById("selectedSeats").innerText = "None";
            document.getElementById("totalPrice").innerText = "0";
            document.getElementById("selectedSeatsInput").value = "";
            document.getElementById("totalPriceInput").value = "";

            // Fill hidden input fields with bus details
            document.getElementById("busNameInput").value = busName;
            document.getElementById("busNoInput").value = busNo;
            document.getElementById("ticketPriceInput").value = price;
            document.getElementById("departureTimeInput").value = departureTime; // ✅ Fix added
            document.getElementById("arrivalTimeInput").value = arrivalTime;     // ✅ Fix added
            document.getElementById("busTypeInput").value = busType;             // ✅ Fix added

            fetchBookedSeats(busNo).then((bookedSeats) => {
    generateSeatLayout(bookedSeats);
});

            document.getElementById("seatSelectionModal").style.display = "block";
        }

        function closeSeatSelectionModal() {
            document.getElementById("seatSelectionModal").style.display = "none";
        }

        function generateSeatLayout(bookedSeats = []) {
    let seatsContainer = document.getElementById("seatsContainer");
    seatsContainer.innerHTML = "";

    let seatNumber = 1;
    for (let row = 0; row < 11; row++) {
        let rowDiv = document.createElement("div");
        rowDiv.className = "row";

        for (let col = 0; col < 5; col++) {
            if (col === 2) {
                let aisle = document.createElement("div");
                aisle.className = "aisle";
                rowDiv.appendChild(aisle);
            }

            let seat = document.createElement("div");
            seat.className = "seat";
            seat.innerText = seatNumber;

            // Check if seat is already booked
            if (bookedSeats.includes(seatNumber.toString())) {
                seat.style.background = "gray"; // Mark as unavailable
                seat.style.cursor = "not-allowed";
                seat.onclick = null; // Disable clicking
            } else {
                seat.onclick = function () {
                    selectSeat(seat);
                };
            }

            rowDiv.appendChild(seat);
            seatNumber++;
        }
        seatsContainer.appendChild(rowDiv);
    }
}

        function selectSeat(seat) {
            const seatNumber = seat.innerText;
            if (selectedSeats.includes(seatNumber)) {
                selectedSeats.splice(selectedSeats.indexOf(seatNumber), 1);
            } else {
                selectedSeats.push(seatNumber);
            }

            seat.classList.toggle("selected");

            document.getElementById("selectedSeats").innerText = selectedSeats.length ? selectedSeats.join(", ") : "None";
            document.getElementById("totalPrice").innerText = (selectedSeats.length * seatPrice).toFixed(2);
            document.getElementById("selectedSeatsInput").value = selectedSeats.join(",");
            document.getElementById("totalPriceInput").value = (selectedSeats.length * seatPrice).toFixed(2);
        }

        // Store booking details in session before submitting the form
        document.getElementById("bookingForm").addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent default form submission

            if (selectedSeats.length === 0) {
                alert("❌ Please select at least one seat before proceeding!");
                return;
            }

            let formData = new FormData(this);

            fetch("store_booking.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "success") {
                        window.location.href = "confirm_booking.php"; // Redirect to confirmation page
                    } else {
                        alert("⚠️ Error storing booking details! Please try again.");
                    }
                })
                .catch(error => console.error("Error:", error));

        });
        function fetchBookedSeats(busNo) {
    return fetch(`fetch_booked_seats.php?bus_no=${busNo}`)
        .then(response => response.json())
        .then(data => data.booked_seats || [])
        .catch(error => {
            console.error("Error fetching booked seats:", error);
            return [];
        });
}

    </script>

</body>

</html>