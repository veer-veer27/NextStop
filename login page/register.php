<?php
include 'connect.php';
session_start();

// Registration Logic
if (isset($_POST['signUp'])) {
    $firstName = trim($_POST['fName']);
    $lastName = trim($_POST['lName']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Raw password (to hash once)
    $age = intval($_POST['age']); // Ensure age is an integer
    $phone = trim($_POST['phone']); // Get phone number

    // Ensure age is at least 18
    if ($age < 18) {
        echo "<script>alert('You must be at least 18 years old to register!'); window.location.href='index.php';</script>";
        exit();
    }

    // Validate phone number (only digits, length between 10-15)
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        echo "<script>alert('Enter a valid phone number (10-15 digits only)!'); window.location.href='index.php';</script>";
        exit();
    }

    // Hash password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email Address Already Exists!'); window.location.href='index.php';</script>";
        exit();
    } else {
        // Insert new user into the database (including phone number)
        $insertQuery = $conn->prepare("INSERT INTO users (firstName, lastName, email, password, age, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $insertQuery->bind_param("ssssss", $firstName, $lastName, $email, $hashedPassword, $age, $phone);

        if ($insertQuery->execute()) {
            echo "<script>alert('Registration Successful!'); window.location.href='index.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Login Logic
if (isset($_POST['signIn'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check user credentials
    $sql = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];

        // Verify password
        if (password_verify($password, $storedPassword)) {
            // Store user details in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['first_name'] = $row['firstName'];
            $_SESSION['last_name'] = $row['lastName'];
            $_SESSION['user_age'] = $row['age'];
            $_SESSION['user_phone'] = $row['phone']; // Store phone number in session

            header("Location: ../home.php"); // Redirect to bus booking page
            exit();
        } else {
            $_SESSION['message'] = "Incorrect Password!";
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Incorrect Email or Password!";
        header("Location: index.php");
        exit();
    }
}
?>
