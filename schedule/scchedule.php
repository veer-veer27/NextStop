<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

// Clear previous booking data when page loads
unset($_SESSION['booking_details']);
unset($_SESSION['selected_seats']);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login page/index.php");
    exit();
}

include '../login page/connect.php';

// NEW: Function to get booked seats
function getBookedSeats($conn, $bus_no)
{
    $query = "SELECT GROUP_CONCAT(selected_seats) AS all_booked_seats 
              FROM booking 
              WHERE bus_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $bus_no);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['all_booked_seats'] ?? '';
}

$date_today = date('Y-m-d');
$current_time = date('h:i A');

$query = "SELECT * FROM buses WHERE travel_date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $date_today);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Bus Schedule</title>
    <link rel="stylesheet" href="popup-style.css">
    <style>
        .header-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background-color: rgb(63, 204, 103);
            color: white;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
        }

        .header-date-time {
            font-size: 22px;
            font-weight: bold;
        }

        .subtext {
            margin-top: 10px;
            color: aliceblue;
            font-size: 30px;
        }

        .bus-container {
            margin-top: 20px;
        }

        .bus-card {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .bus-card img {
            width: 150px;
            height: 100px;
            border-radius: 5px;
            margin-right: 15px;
            object-fit: cover;
        }

        .bus-info-item {
            margin-bottom: 5px;
        }

        /* NEW: Added styles for booked seats */
        .seat.booked {
            background-color: #cccccc !important;
            cursor: not-allowed !important;
            pointer-events: none;
        }
    </style>
</head>

<body>

    <!-- Your existing HTML remains unchanged -->
    <div class="header-container">
        <div class="header-date-time">
            Today's Bus Schedule - <?php echo date('d M Y'); ?> | <?php echo $current_time; ?>
        </div>
        <p class="subtext">This page displays only the buses available for today.</p>
    </div>
    <div class="bus-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="bus-card">
                    <img src="<?php echo !empty($row['image']) ? '../admin/uploads/' . htmlspecialchars($row['image']) : '../login page/image/default-bus.jpg'; ?>"
                        alt="Bus Image">

                    <div class="bus-info">
                        <div class="bus-info-item">
                            <strong>Bus Name:</strong> <?php echo htmlspecialchars($row['bus_name']); ?>
                        </div>
                        <div class="bus-info-item">
                            <strong>From:</strong> <?php echo htmlspecialchars($row['departure']); ?>
                        </div>
                        <div class="bus-info-item">
                            <strong>To:</strong> <?php echo htmlspecialchars($row['destination']); ?>
                        </div>
                        <div class="bus-info-item">
                            <strong>Departure:</strong> <?php echo $row['departure_time']; ?>
                        </div>
                        <div class="bus-info-item">
                            <strong>Arrival:</strong> <?php echo $row['arrival_time']; ?>
                        </div>
                        <div class="bus-info-item">
                            <strong>Seats Available:</strong> <?php echo $row['available_seats']; ?>
                        </div>
                        <div class="bus-info-item">
                            <strong>Bus Type:</strong> <?php echo htmlspecialchars($row['bus_type']); ?>
                        </div>

                        <div class="bus-info-item">
                            <strong>Price:</strong> ₹<?php echo $row['ticket_price']; ?>
                        </div>
                        <button class="book-btn" onclick="openSeatSelectionModal(
                            '<?= htmlspecialchars($row['bus_name']) ?>',
                            '<?= htmlspecialchars($row['bus_no']) ?>',
                            '<?= $row['ticket_price'] ?>',
                            '<?= $row['departure_time'] ?>',
                            '<?= $row['arrival_time'] ?>',
                            '<?= htmlspecialchars($row['bus_type']) ?>',
                            '<?= $row['available_seats'] ?>',
                            '<?= htmlspecialchars($row['departure']) ?>',
                            '<?= htmlspecialchars($row['destination']) ?>',
                            '<?= getBookedSeats($conn, $row['bus_no']) ?>' // NEW: Added booked seats parameter
                        )">Book Now</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No buses available for today.</p>
        <?php endif; ?>
    </div>

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
            <form id="bookingForm" action="confirm_bookings.php" method="post">
                <input type="hidden" name="bus_name" id="busNameInput">
                <input type="hidden" name="bus_no" id="busNoInput">
                <input type="hidden" name="ticket_price" id="ticketPriceInput">
                <input type="hidden" name="departure_time" id="departureTimeInput">
                <input type="hidden" name="arrival_time" id="arrivalTimeInput">
                <input type="hidden" name="bus_type" id="busTypeInput">
                <input type="hidden" name="selected_seats" id="selectedSeatsInput">
                <input type="hidden" name="total_price" id="totalPriceInput">
                <input type="hidden" name="departure" id="departureInput">
                <input type="hidden" name="destination" id="destinationInput">

                <!-- Passenger Details (from session) -->
                <input type="hidden" name="passenger_name"
                    value="<?php echo isset($_SESSION['passenger_name']) ? htmlspecialchars($_SESSION['passenger_name']) : ''; ?>">
                <input type="hidden" name="passenger_email"
                    value="<?php echo isset($_SESSION['passenger_email']) ? htmlspecialchars($_SESSION['passenger_email']) : ''; ?>">
                <input type="hidden" name="passenger_age"
                    value="<?php echo isset($_SESSION['passenger_age']) ? htmlspecialchars($_SESSION['passenger_age']) : ''; ?>">

                <button type="submit" class="book-btn">Confirm Booking</button>
            </form>
        </div>
    </div>

    <script>
        function closeSeatSelectionModal() {
            document.getElementById("seatSelectionModal").style.display = "none";
        }

        // to fetch booked seats via AJAX
        function fetchBookedSeats(busNo, totalSeats) {
            fetch('get_booked_seats.php?bus_no=' + busNo)
                .then(response => response.text())
                .then(bookedSeats => {
                    generateSeats(totalSeats, bookedSeats);
                });
        }

        function openSeatSelectionModal(busName, busNo, ticketPrice, departureTime, arrivalTime, busType, availableSeats, departure, destination, bookedSeats) {
            // Reset all selections when modal opens
            document.getElementById("selectedSeats").textContent = "None";
            document.getElementById("totalPrice").textContent = "0";

            // Update form fields with CURRENT bus data
            document.getElementById("busNameInput").value = busName;
            document.getElementById("busNoInput").value = busNo;
            document.getElementById("ticketPriceInput").value = ticketPrice;
            document.getElementById("departureTimeInput").value = departureTime;
            document.getElementById("arrivalTimeInput").value = arrivalTime;
            document.getElementById("busTypeInput").value = busType;
            document.getElementById("departureInput").value = departure;
            document.getElementById("destinationInput").value = destination;

            // Modified to use booked seats
            generateSeats(55, bookedSeats);

            document.getElementById("seatSelectionModal").style.display = "block";
        }

        // Modified to handle booked seats
        function generateSeats(totalSeats, bookedSeats) {
            const seatsContainer = document.getElementById("seatsContainer");
            seatsContainer.innerHTML = "";

            // NEW: Convert booked seats string to array
            const bookedSeatsArray = bookedSeats ? bookedSeats.split(',').map(Number) : [];

            let seatNumber = 1;
            let rows = Math.ceil(totalSeats / 5); // Each row will have 5 seats (2+3 format)

            for (let i = 0; i < rows; i++) {
                let rowDiv = document.createElement("div");
                rowDiv.classList.add("row");
                rowDiv.style.display = "flex";
                rowDiv.style.justifyContent = "center";
                rowDiv.style.marginBottom = "5px";

                for (let j = 0; j < 5 && seatNumber <= totalSeats; j++) {
                    let seatDiv = document.createElement("div");
                    seatDiv.classList.add("seat");
                    seatDiv.textContent = seatNumber;
                    seatDiv.dataset.seatNumber = seatNumber;

                    // NEW: Mark as booked if seat is in bookedSeatsArray
                    if (bookedSeatsArray.includes(seatNumber)) {
                        seatDiv.classList.add("booked");
                    } else {
                        seatDiv.addEventListener("click", function () {
                            selectSeat(seatDiv);
                        });
                    }

                    if (j === 2) {
                        let spaceDiv = document.createElement("div");
                        spaceDiv.style.width = "20px";
                        rowDiv.appendChild(spaceDiv);
                    }

                    rowDiv.appendChild(seatDiv);
                    seatNumber++;
                }

                seatsContainer.appendChild(rowDiv);
            }
        }

        // Your existing selectSeat function remains unchanged
        function selectSeat(seat) {
            seat.classList.toggle("selected");

            let selectedSeats = document.querySelectorAll(".seat.selected");
            let selectedSeatsArray = Array.from(selectedSeats).map(seat => seat.dataset.seatNumber);

            document.getElementById("selectedSeats").textContent = selectedSeatsArray.length > 0 ? selectedSeatsArray.join(", ") : "None";

            let ticketPrice = parseFloat(document.getElementById("ticketPriceInput").value) || 0;
            let totalPrice = selectedSeatsArray.length * ticketPrice;
            document.getElementById("totalPrice").textContent = totalPrice;

            document.getElementById("selectedSeatsInput").value = selectedSeatsArray.join(", ");
            document.getElementById("totalPriceInput").value = totalPrice;
        }
    </script>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>