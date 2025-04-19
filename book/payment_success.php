<?php
session_start();

if (!isset($_SESSION['booking_details']) || !isset($_SESSION['user_id'])) {
    header("Location: available_buses.php");
    exit();
}

$booking = $_SESSION['booking_details'];
$user_id = $_SESSION['user_id'];
$payment_method = isset($_GET['method']) ? htmlspecialchars($_GET['method']) : "Unknown";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
       /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background: linear-gradient(135deg,rgb(132, 166, 217), #c3cfe2);
    color: #333;
    text-align: center;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow-x: hidden;
}

.container {
    background: #ffffff;
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1), 0 6px 20px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    width: 100%;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease-in-out;
}

.container:hover {
    transform: scale(1.02);
}

h2 {
    font-size: 28px;
    color: #28a745;
    margin-bottom: 20px;
    animation: fadeInDown 1s ease-in-out;
}

p {
    font-size: 18px;
    line-height: 1.6;
    margin-bottom: 20px;
    color: #555;
    animation: fadeInUp 1s ease-in-out;
}

.btn {
    display: inline-block;
    margin-top: 20px;
    width: 200px;
    padding: 10px 20px;
    background: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    font-size: 16px;
    border: none;
}

.btn:hover {
    background: darkgreen;
}

.download-btn {
    background: #28a745;
}

.download-btn:hover {
    background: #1e7e34;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .container {
        padding: 30px 20px;
    }

    h2 {
        font-size: 24px;
    }

    p {
        font-size: 16px;
    }

    .btn {
        padding: 10px 20px;
        font-size: 14px;
    }
}
    </style>
</head>
<body>

    <div class="container">
        <h2>Payment Successful!</h2>
        <p>Your payment has been processed successfully.</p>
        <p>Thank you for booking with us.</p>
        
        <a href="../home.php" class="btn">Go to Home</a>

        <!-- ✅ NEW download button that triggers JS -->
        <button class="btn download-btn" onclick="handleDownload()">Download Ticket</button>
    </div>

    <!-- ✅ JavaScript to handle download + WhatsApp flow -->
    <script>
        function handleDownload() {
            // Step 1: Download the ticket
            const link = document.createElement('a');
            link.href = 'download_ticket.php';
            link.download = ''; // Let the browser decide filename
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Step 2: Ask if user wants WhatsApp ticket
            setTimeout(() => {
                const confirmSend = confirm("Do you want to receive the ticket on WhatsApp?");
                if (confirmSend) {
                    const number = prompt("Enter your WhatsApp number (with country code):", "91");
                    if (number) {
                        fetch('send_ticket_whatsapp.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'phone=' + encodeURIComponent(number)
                        })
                        .then(response => response.text())
                        .then(result => {
                            alert("Ticket sent to WhatsApp successfully!");
                        })
                        .catch(error => {
                            console.error('Error sending WhatsApp:', error);
                            alert("Failed to send ticket via WhatsApp.");
                        });
                    }
                }
            }, 1000);
        }
    </script>

</body>
</html>
