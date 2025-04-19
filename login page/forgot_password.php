<?php
session_start();
include 'connect.php'; // Ensure this file connects to the correct database

if(isset($_POST['send_otp'])){
    $email = trim($_POST['email']);

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp; // Store OTP in session
        $_SESSION['email'] = $email; // Store email in session

        // Store OTP in the database (hashed for security)
        $hashed_otp = password_hash($otp, PASSWORD_DEFAULT);
        $updateOTP = $conn->prepare("UPDATE users SET otp=? WHERE email=?");
        $updateOTP->bind_param("ss", $hashed_otp, $email);
        
        if($updateOTP->execute()){
            echo "<script>alert('Your OTP is: $otp'); window.location.href='verify_otp.php';</script>";
        } else {
            echo "<script>alert('Error storing OTP. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgot.css">
</head>
<body>
    <div class="container">
        <h2 class="form-title">Forgot Password</h2>
        <form method="post">
            <div class="input-group">
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <button type="submit" name="send_otp" class="btn">Send OTP</button>
        </form>
    </div>
</body>
</html>
