<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextStop - Explore Gujarat by Bus</title>
    <link rel="stylesheet" href="home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Existing styles remain unchanged */

        /* Discount Routes Section */
        .discount-routes {
            padding: 50px 20px;
            background-color: #f9f9f9;
            text-align: center;
        }

        .discount-routes h2 {
            margin-bottom: 30px;
            font-size: 2rem;
            color: #333;
        }

        .routes-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .route-card {
            width: 420px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .route-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease-in-out;
        }

        .route-card h3 {
            margin: 15px 0 10px;
            font-size: 1.2rem;
            color: #4CAF50;
        }

        .route-card p {
            font-size: 0.9rem;
            color: #666;
        }

        .route-card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color:rgb(36, 141, 57);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .route-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .route-card:hover img {
            transform: scale(1.1);
        }
        
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="header">
        <div class="header1">
            <img src="login page/image/logoo-removebg-preview.png" alt="NextStop Logo" class="logo">
            <div style="font-size: 2rem; font-weight: bold;">NextStop</div>
        </div>

        <div class="options">
            <img src="login page/image/th.jpg" alt="Login Icon" class="login-logo">

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user_account/account.php" class="btn">My Account</a>
            <?php else: ?>
                <a href="login page/index.php" class="btn">Log in / Sign in</a>
            <?php endif; ?>

            <img src="login page/image/contact us.png" alt="Contact Icon" class="contact-logo">
            <a href="contect/contect.html" class="btn">Contact Us</a>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero-section" id="hero">
        <div class="overlaay"></div>
        <div class="content">
            <h1>Welcome to NextStop</h1>
            <p>Explore Gujarat by bus with our easy-to-use booking and timetable services.</p>
            <div class="buttons">
                <a href="book/book.html" class="btn">Book Tickets</a>
                <a href="schedule/scchedule.php" class="btn outline">View Timetable</a>
            </div>
        </div>
    </div>

    <!-- Background Image Changing Script -->
    <script>
        const images = [
            "login page/image/home3.png",
            "login page/image/home1.png",
            "login page/image/home2.png",
            "login page/image/bus 3.png"
        ];
        let index = 0;
        const heroSection = document.getElementById("hero");
        const overlaay = document.querySelector(".overlaay");

        function changeBackground() {
            overlaay.style.opacity = 0;
            setTimeout(() => {
                heroSection.style.backgroundImage = `url('${images[index]}')`;
                setTimeout(() => {
                    overlaay.style.opacity = 1;
                }, 100);
            }, 2000);
            index = (index + 1) % images.length;
        }

        setInterval(changeBackground, 5000);
        changeBackground();

        // Admin portal shortcut (Ctrl + A)
        document.addEventListener("keydown", function (event) {
            if (event.ctrlKey && event.key === "a") {
                let confirmLogin = confirm("Do you want to access the Admin Login?");
                if (confirmLogin) {
                    window.location.href = "admin/admin_login.php";
                }
            }
        });
    </script>

    <!-- About Section -->
    <section class="about">
        <h2>Why Choose NextStop?</h2>
        <p>At NextStop, we make your bus travel hassle-free. From easy bookings to real-time timetables, our services
            are designed to meet all your travel needs. Experience comfort, convenience, and reliability with us.</p>
    </section>



<!-- NEW SECTION: Discount Routes -->
<section class="discount-routes">
    <h2>Discount Routes</h2>
    <div class="routes-container">
        <?php
        // Example: Array of discounted routes (replace with database query if needed)
        $discountedRoutes = [
            [
                "image" => "login page/image/pngtree-d-rendering-of-a-white-isolated-background-featuring-a-medium-sized-image_3893569 - Copy.jpg",
                "title" => "Ahmedabad to Vadodara",
                "description" => "Flat 20% off on all tickets!",
                "date" => "20/04/2025",
                "link" => "book/book.html"
            ],
            [
                "image" => "login page/image/coach-3206326_1920.png",
                "title" => "Surat to Rajkot",
                "description" => "Limited time offer: 15% discount!",
                "date" => "24/04/2025",
                "link" => "book/book.html"
            ],
            [
                "image" => "login page/image/19498d607a841df9565bc028e458169d.png",
                "title" => "Bhavnagar to Junagadh",
                "description" => "Special discount: 25% off!",
                "date" => "18/04/2025",
                "link" => "book/book.html"
            ],
            [
                "image" => "login page/image/Volvo-Bus-Transparent-Background.png",
                "title" => "Gandhinagar to Bhuj",
                "description" => "Exclusive 18% discount on all tickets!",
                "date" => "22/04/2025",
                "link" => "book/book.html"
            ]
        ];

        // Loop through the discounted routes and display them
        foreach ($discountedRoutes as $route) {
            echo '<div class="route-card">';
            echo '<img src="' . $route["image"] . '" alt="' . $route["title"] . '">';
            echo '<h3>' . $route["title"] . '</h3>';
            echo '<p>' . $route["description"] . '</p>';
            echo '<p><strong>Travel Date:</strong> ' . $route["date"] . '</p>';
            echo '<a href="' . $route["link"] . '" class="button">Book Now</a>';
            echo '</div>';
        }
        ?>
    </div>
</section>

<!-- TOP DESTINATIONS - SLIDER VERSION -->
<section class="top-destinations">
    <h2>Explore Top Destinations in Gujarat</h2>
    <div class="slider-container">
        <div class="slider">
            <?php
            $destinations = [
                [
                    "image" => "login page/image/somnath2.jpg",
                    "name" => "Somnath",
                    "description" => "Historic temple town, home to the sacred Somnath Temple.",
                    "link" => ""  // Link to the book page
                ],
                [
                    "image" => "login page/image/Statue of Unity.jpg",
                    "name" => "Statue of Unity",
                    "description" => "World's tallest statue, dedicated to Sardar Patel.",
                    "link" => "#"  // Link to the book page
                ],
                [
                    "image" => "login page/image/Rann of Kutch.jpg",
                    "name" => "Rann of Kutch",
                    "description" => "Stunning white salt desert, famous for Rann Utsav.",
                    "link" => "#"  // Link to the book page
                ],
                [
                    "image" => "login page/image/Gir-National-Park-e1598571541189.jpeg",
                    "name" => "Gir National Park",
                    "description" => "The only home of Asiatic lions in the world.",
                    "link" => "#"  // Link to the book page
                ],
                [
                    "image" => "login page/image/Dwarka.jpg",
                    "name" => "Dwarka",
                    "description" => "A sacred city associated with Lord Krishna."
                    
                ]
            ];

            foreach ($destinations as $destination) {
                echo '<div class="slide">';
                // Wrap image with an anchor tag to make it clickable
                
                echo '<img src="' . $destination["image"] . '" alt="' . $destination["name"] . '">';
                echo '</a>';
                echo '<div class="overlay">';
                echo '<h3>' . $destination["name"] . '</h3>';
                echo '<p>' . $destination["description"] . '</p>';
                echo '</div>';
                echo '</div>'; 
            }
            ?>
        </div>
        <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
        <button class="next" onclick="moveSlide(1)">&#10095;</button>
    </div>
    <!-- CSS Styles -->
<style>
  
</style>
</section>

<!-- JavaScript for Slider -->
<script>
    let slideIndex = 0;

    function moveSlide(direction) {
        const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');

        slideIndex += direction;

        if (slideIndex < 0) {
            slideIndex = slides.length - 1;
        } else if (slideIndex >= slides.length) {
            slideIndex = 0;
        }

        slider.style.transform = `translateX(-${slideIndex * 100}%)`;
    }

    // Auto-slide every 5 seconds
    setInterval(() => {
        moveSlide(1);
    }, 5000);
</script>

<!-- Footer Section -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-logo">
            <img src="login page/image/logoo-removebg-preview.png" alt="NextStop Logo">
            <h3>NextStop</h3>
        </div>
        <div class="footer-links">
            <a href="about_us/about.html">About Us</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
            <a href="contect/contect.html">Contact Us</a>
        </div>
        
        <p>&copy; 2025 NextStop - Explore Gujarat by Bus. All rights reserved.</p>
    </div>
</footer>

</body>

</html>