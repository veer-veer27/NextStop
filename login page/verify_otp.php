<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Unauthorized access! Please request OTP again.'); window.location.href='forgot_password.php';</script>";
    exit;
}

if (isset($_POST['verify_otp'])) {
    $entered_otp = trim($_POST['otp']);

    // Validate OTP format (Only digits, 6 characters long)
    if (!preg_match('/^\d{6}$/', $entered_otp)) {
        echo "<script>alert('Invalid OTP format! Please enter a 6-digit code.');</script>";
    } else {
        $email = $_SESSION['email'];

        // Fetch OTP from the database
        $stmt = $conn->prepare("SELECT otp FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_otp = $row['otp'];

            // Uncomment this line if OTP is hashed in DB
            // if (password_verify($entered_otp, $stored_otp)) {
            if ($entered_otp == $stored_otp) { // Plaintext OTP check
                $_SESSION['verified'] = true; // Mark user as verified

                // Clear OTP from the database after successful verification
                $clearOtp = $conn->prepare("UPDATE users SET otp=NULL WHERE email=?");
                $clearOtp->bind_param("s", $email);
                $clearOtp->execute();

                echo "<script>alert('OTP Verified!'); window.location.href='reset_password.php';</script>";
                exit();
            } else {
                echo "<script>alert('Invalid OTP! Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Email not found! Please request a new OTP.'); window.location.href='forgot_password.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="forgot.css">
</head>
<body>
    <div class="container">
        <h2 class="form-title">Verify OTP</h2>
        <form method="post">
            <div class="input-group">
                <input type="text" name="otp" required placeholder="Enter OTP" maxlength="6" pattern="\d{6}">
            </div>
            <button type="submit" name="verify_otp" class="btn">Verify OTP</button>
        </form>
    </div>
</body>
</html>
