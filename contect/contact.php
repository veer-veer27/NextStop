<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "bus_booking"; // Updated database name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if (isset($_POST['submit'])) {
    $name = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $subject = trim(htmlspecialchars($_POST['subject']));
    $message = trim(htmlspecialchars($_POST['message']));

    // Validate email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address. Please enter a valid email.'); window.location.href = 'contact.php';</script>";
        exit();
    }

    // Insert data into the database
    $sql = "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)"; // Updated table name
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Message sent successfully!'); window.location.href = '../contect/contect.html';</script>"; // Fixed folder name
    } else {
        echo "<script>alert('Error sending message. Please try again.'); window.location.href = 'contact.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
