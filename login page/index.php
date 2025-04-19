<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Booking - Register & Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<style>
  /* General Styles */
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }

  /* Sidebar Styling */
  .sidebar {
    height: 100%;
    width: 250px;
    position: fixed;
    top: 0;
    left: -250px;
    background-color: #4CAF50;
    transition: left 0.6s cubic-bezier(0.25, 1, 0.30, 1);
    padding-top: 60px;
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
  }

  .sidebar a {
    padding: 12px 20px;
    display: block;
    font-size: 20px;
    color: white;
    text-decoration: none;
  }

  .sidebar a:hover {
    background-color: #367c39;
    transform: scale(1.05);
  }

  .sidebar .closebtn {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 30px;
    cursor: pointer;
  }

  /* Sidebar Toggle */
  .open-sidebar {
    left: 0 !important;
  }

  /* Hamburger Icon */
  .hamburger {
    position: fixed;
    top: 15px;
    left: 15px;
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1600;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease-in-out, background 0.3s;
  }

  .hamburger:hover {
    transform: scale(1.1);
    background: #f0f0f0;
  }

  .hamburger i {
    font-size: 26px;
    color: #4CAF50;
  }

  /* Form Containers */
  .form-container {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    width: 96%;
  }

  .container {
    width: 440px;
    padding: 30px;
    background: #fff;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    text-align: center;
  }


  h1 {
    color: #333;
  }

  /* Error Message */
  .error-message {
    color: red;
    margin-bottom: 10px;
  }

  .input-group {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    border: 2px solid #ccc;
    border-radius: 8px;
    padding: 10px;
    background: #fff;
    height: 50px;
    position: relative;
  }

  .input-group i {
    margin-right: 10px;
    color: rgb(99, 191, 50);
    font-size: 20px;
  }

  .input-group input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 16px;
    padding-left: 25px;
    height: 100%;
    background: transparent;
  }

  input::placeholder {
    color: #888;
    font-size: 16px;
  }

  /* Buttons */
  .btn {
    width: 100%;
    padding: 12px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: background 0.3s;
  }

  .btn:hover {
    background: #367c39;
  }

  /* MENU Button */
  .btn1 {
    width: 150px;
    height: 50px;
    border-radius: 5px;
    border: none;
    transition: all 0.5s;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    background: #45a049;
    color: #f5f5f5;
    position: fixed;
    top: 10px;
    left: 10px;
    z-index: 1000;
  }

  .btn1:hover {
    box-shadow: 0 0 20px 0px #2e2e2e3a;
  }

  .btn1 .icon {
    position: absolute;
    height: 40px;
    width: 70px;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: all 0.5s;
  }

  .btn1 .text {
    transform: translateX(55px);
  }

  .btn1:hover .icon {
    width: 175px;
  }

  .btn1:hover .text {
    opacity: 0;
  }

  /* MENU Links (Initially Hidden) */
  .menu-links {
    display: none;
    position: fixed;
    top: 70px;
    left: 10px;
    background: white;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
  }

  .menu-links a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: black;
    font-size: 18px;
    transition: 0.3s;
  }

  .menu-links a:hover {
    background: #ddd;
    border-radius: 3px;
  }

  .links {
    margin-top: 15px;
    display: flex;
    justify-content: center;
    /* Centers content horizontally */
    align-items: center;
    /* Centers content vertically */
  }

  .links p {
    margin-right: 5px;
    /* Adjust the space between the text and the button */
    font-size: 16px;
  }

  .links button {
    background: none;
    border: none;
    color: #4CAF50;
    cursor: pointer;
    font-size: 16px;
  }

  .links button:hover {
    text-decoration: underline;
  }



  /* Hide Signup Form Initially */
  #signup {
    display: none;
  }

  .toggle-password {
    cursor: pointer;
    position: absolute;
    right: 15px;
    font-size: 1px;
    color: blue;
  }
</style>

<body>

  <!-- MENU Button -->
  <button class="btn1" id="menuButton">
    <span class="icon">
      <svg viewBox="0 0 175 80" width="40" height="40">
        <rect width="80" height="15" fill="#f0f0f0" rx="10"></rect>
        <rect y="30" width="80" height="15" fill="#f0f0f0" rx="10"></rect>
        <rect y="60" width="80" height="15" fill="#f0f0f0" rx="10"></rect>
      </svg>
    </span>
    <span class="text">MENU</span>
  </button>

  <!-- Menu Links -->
  <div id="menuLinks" class="menu-links">
    <a href="../home.php">Home</a>
    <a href="../contact/contact.html">Contact Us</a>
    <a href="../about_us/about.html">About Us</a>
    <a href="../schedule/schedule.php">View Bus Schedule</a>
  </div>

  <div class="form-container">
    <!-- Sign In -->
    <div class="container" id="signIn">
      <h1>Sign In</h1>

      <?php
      if (isset($_SESSION['message'])) {
        echo "<p class='error-message'>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']);
      }
      ?>

      <form method="post" action="register.php">
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" required placeholder="Enter your Email">
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" required placeholder="Create a Password" id="registerPassword"
            autocomplete="new-password">
          <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
        </div>
        <p class="recover">
          <a href="forgot_password.php">Forgot Password?</a>
        </p>
        <input type="submit" class="btn" value="Sign In" name="signIn">
      </form>
      <div class="links">
        <p>Don't have an account?</p>
        <button id="signUpButton">Sign Up</button>
      </div>
    </div>

    <!-- Register -->
    <div class="container" id="signup" style="display: none;">
      <h1>Register</h1>
      <form method="post" action="register.php">
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="fName" required placeholder="First Name">
        </div>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="lName" required placeholder="Last Name">
        </div>
        <div class="input-group">
          <i class="fas fa-user-clock"></i>
          <input type="number" name="age" id="age" required placeholder="Enter your Age" min="18">
        </div>
        <div class="input-group">
          <i class="fas fa-phone"></i>
          <input type="tel" name="phone" required placeholder="Enter your Phone Number" pattern="[0-9]{10,15}"
            title="Enter a valid phone number (10-15 digits)">
        </div>
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" required placeholder="Enter your Email">
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" required placeholder="Create a Password" id="registerPassword"
            autocomplete="new-password">
          <i class="fas fa-eye toggle-password" onclick="togglePassword('registerPassword')"></i>
        </div>
        <input type="submit" class="btn" value="Sign Up" name="signUp">
      </form>
      <div class="links">
        <p>Already have an account?</p>
        <button id="signInButton">Sign In</button>
      </div>
    </div>
  </div>

  <script>
    // Toggle Sign In & Sign Up Forms
    document.getElementById("signUpButton").addEventListener("click", () => {
      document.getElementById("signIn").style.display = "none";
      document.getElementById("signup").style.display = "block";
    });

    document.getElementById("signInButton").addEventListener("click", () => {
      document.getElementById("signup").style.display = "none";
      document.getElementById("signIn").style.display = "block";
    });

    // Menu Hover Functionality
    let menuButton = document.getElementById("menuButton");
    let menuLinks = document.getElementById("menuLinks");

    menuButton.addEventListener("mouseenter", function () {
      menuLinks.style.display = "block";
    });

    menuLinks.addEventListener("mouseenter", function () {
      menuLinks.style.display = "block";
    });

    menuButton.addEventListener("mouseleave", function () {
      setTimeout(() => {
        if (!menuLinks.matches(":hover")) {
          menuLinks.style.display = "none";
        }
      }, 200);
    });

    menuLinks.addEventListener("mouseleave", function () {
      menuLinks.style.display = "none";
    });

    // Toggle Password Visibility
    function togglePassword(inputId) {
      let passwordInput = document.getElementById(inputId);
      let icon = passwordInput.nextElementSibling;

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }

    // Age Validation
    document.addEventListener("DOMContentLoaded", function () {
      document.querySelector("form").addEventListener("submit", function (event) {
        let ageInput = document.getElementById("age");
        if (parseInt(ageInput.value) < 18) {
          alert("You must be at least 18 years old to register.");
          event.preventDefault();
        }
      });
    });
  </script>

</body>

</html>