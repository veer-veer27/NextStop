<?php
include '../login page/connect.php';

if (isset($_POST['submit'])) {
    $bus_name = $_POST['bus_name'];
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];

    // Image upload handling
    $image_name = $_FILES['bus_image']['name'];
    $image_tmp = $_FILES['bus_image']['tmp_name'];
    $upload_dir = "uploads/";

    // Ensure the uploads directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Move file to the upload directory
    $image_path = $upload_dir . basename($image_name);
    if (move_uploaded_file($image_tmp, $image_path)) {
        // Insert into database
        $sql = "INSERT INTO buses (bus_name, departure, destination, image) VALUES ('$bus_name', '$departure', '$destination', '$image_name')";
        if (mysqli_query($conn, $sql)) {
            echo "Bus added successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload image.";
    }
}
?>
