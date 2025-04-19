<?php
include "db_connection.php";

if (isset($_GET['bus_no'])) {
    $bus_no = $_GET['bus_no'];
    $booked_seats = [];

    // Fetch all booked seats for the given bus
    $query = "SELECT selected_seats FROM booking WHERE bus_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $bus_no);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $seats = explode(',', $row['selected_seats']);
        $booked_seats = array_merge($booked_seats, $seats);
    }

    echo json_encode(["booked_seats" => $booked_seats]);
}
?>
