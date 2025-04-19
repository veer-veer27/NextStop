<?php
session_start();
require_once "../login page/connect.php"; // Ensure correct database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_username = trim($_POST["username"]);
    $admin_password = trim($_POST["password"]);

    // Use Prepared Statements to prevent SQL Injection
    $stmt = $conn->prepare("SELECT * FROM admin WHERE LOWER(username) = LOWER(?)");
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Debugging Output (Uncomment to check stored password)
        // echo "Stored Hash: " . $row["password"];

        if (password_verify($admin_password, $row["password"])) { // Secure password check
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_username"] = $admin_username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Admin not found!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../login page/image/login page 6.png');
            background-size: cover;
            /* Ensure the background image covers the entire page */
            background-position: center;
            /* Center the background image */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            width: 450px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .login-title {
            font-size: 22px;
            font-weight: bold;
            color: #2980b9;
            margin-bottom: 20px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
        }

        .input-label {
            font-size: 16px;
            text-align: left;
            margin-bottom: 5px;
            display: block;
        }

        .input-field {
            width: 94%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-button {
            width: 100%;
            padding: 10px;
            background: #2980b9;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
        }

        .login-button:hover {
            background: #1c5985;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        @media (max-width: 400px) {
            .login-container {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2 class="login-title">Admin Login</h2>
        <form method="post" class="login-form">
            <input type="text" name="fakeusernameremembered" style="display:none;">
            <input type="password" name="fakepasswordremembered" style="display:none;">

            <label class="input-label">Username:</label>
            <input type="text" name="username" class="input-field" autocomplete="off" required>

            <label class="input-label">Password:</label>
            <input type="password" name="password" class="input-field" autocomplete="new-password" required>

            <button type="submit" class="login-button">Login</button>
        </form>

        <br>
        <?php if (isset($error)) {
            echo "<p class='error-message'>$error</p>";
        } ?>
    </div>
</body>

</html>