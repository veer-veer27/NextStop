<?php
session_start();
include "db_connection.php";

// Check if form data exists
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $bus_name = $_POST["bus_name"];
    $bus_no = $_POST["bus_no"];
    $ticket_price = $_POST["ticket_price"];
    $departure_time = $_POST["departure_time"];
    $arrival_time = $_POST["arrival_time"];
    $bus_type = $_POST["bus_type"];
    $departure = $_POST["departure"];
    $destination = $_POST["destination"];
    $selected_seats = $_POST["selected_seats"];
    $total_price = $_POST["total_price"];
    
    $passenger_name = $_POST["passenger_name"];
    $passenger_email = $_POST["passenger_email"];
    $passenger_age = $_POST["passenger_age"];

    // Store booking details in session
    $_SESSION['booking_details'] = [
        "bus_name" => $bus_name,
        "bus_no" => $bus_no,
        "ticket_price" => $ticket_price,
        "departure_time" => $departure_time,
        "arrival_time" => $arrival_time,
        "bus_type" => $bus_type,
        "departure" => $departure,
        "destination" => $destination,
        "selected_seats" => $selected_seats,
        "total_price" => $total_price,
        "passenger_name" => $passenger_name,
        "passenger_email" => $passenger_email,
        "passenger_age" => $passenger_age
    ];

    echo "success"; // JavaScript will handle redirection
} else {
    echo "error";
}
?>
