<?php
session_start();
include 'connect.php';

// Ensure user comes from a verified session
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Unauthorized access! Please verify your email again.'); window.location.href='forgot_password.php';</script>";
    exit;
}

if (isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['email'];

    // Password validation rules
    if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password) || !preg_match('/[\W]/', $new_password)) {
        echo "<script>alert('Password must be at least 8 characters long and include an uppercase letter, a number, and a special character.');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in database and reset OTP
        $stmt = $conn->prepare("UPDATE users SET password=?, otp=NULL WHERE email=?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            unset($_SESSION['email']); // Clear only email session
            echo "<script>alert('Password updated successfully! Please login with your new password.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Error updating password. Please try again later.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="forgot.css">
</head>

<body>
    <div class="container">
        <h2 class="form-title">Reset Password</h2>
        <form method="post">
            <div class="input-group">
                <input type="password" name="new_password" required placeholder="Enter new password">
            </div>
            <div class="input-group">
                <input type="password" name="confirm_password" required placeholder="Confirm new password">
            </div>
            <button type="submit" name="reset_password" class="btn">Reset Password</button>
        </form>
    </div>
</body>

</html>
