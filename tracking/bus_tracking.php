<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "bus_booking");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the booking details (Assuming user ID is available via session)
session_start();
$user_id = $_SESSION['user_id'];  // Ensure user is logged in
$sql = "SELECT departure, destination FROM booking WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$departure = "Vadodara"; // Default start location
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $departure = $row['departure']; // Get user's booked departure city
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Tracking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
    <style>
        html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }
        #map { height: 100vh; width: 100vw; position: absolute; top: 0; left: 0; }
        h2 {
            position: absolute; top: 10px; left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7); color: white;
            padding: 10px 20px; border-radius: 10px;
            font-size: 20px; z-index: 1000;
        }
        #status {
            position: absolute; bottom: 20px; left: 50%;
            transform: translateX(-50%);
            background: rgba(228, 34, 34, 0.8); color: white;
            padding: 10px 15px; border-radius: 10px;
            font-size: 18px; z-index: 1000;
        }
    </style>
</head>
<body>

    <h2>Bus Tracking</h2>
    <div id="map"></div>
    <div id="status">Bus is moving...</div>

    <script>
        var map = L.map('map').setView([23.0225, 72.5714], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        var busIcon = L.icon({ 
            iconUrl: '../tracking/track-bus-live.webp',
            iconSize: [50, 50], 
            iconAnchor: [25, 25] 
        });

        var busMarker = L.marker([23.0225, 72.5714], { icon: busIcon }).addTo(map);

        var departureCity = "<?php echo $departure; ?>"; // Fetch departure city from PHP
        var route = [];

        function loadRoute(city) {
            fetch('tracking/' + city.toLowerCase() + '_route.json') 
                .then(response => response.json())
                .then(data => {
                    route = data;
                    moveBus();
                })
                .catch(error => console.error("Error loading route:", error));
        }

        loadRoute(departureCity);

        var index = 0;
        function moveBus() {
            if (index < route.length) {
                busMarker.setLatLng([route[index].lat, route[index].lng]);
                map.setView([route[index].lat, route[index].lng], 13);
                index++;
                setTimeout(moveBus, 3000);
            }
        }
    </script>

</body>
</html>
