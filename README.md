# NextStop – Explore Gujarat by Bus.

**NextStop** is a web-based bus ticket booking system designed to simplify travel across Gujarat. Built using PHP, MySQL, HTML, CSS, and JavaScript.

## Features

- User registration and login
- Search buses by route and date
- Seat selection and booking
- Payment summary and ticket download (PDF)
- Send ticket to WhatsApp (UltraMsg API)
- Admin panel to add, edit, or delete buses

## Tech Stack

- PHP & MySQL (XAMPP)
- HTML, CSS, JavaScript
- UltraMsg API for WhatsApp ticket delivery

## Project Structure

- `home.php`: Landing page with search and "Find Ticket"
- `schedule.php`: Displays available buses and seat maps
- `process_booking.php`: Handles booking logic and DB insert
- `download_ticket.php`: Generates PDF ticket
- `send_ticket_whatsapp.php`: Sends ticket via WhatsApp
- `manage_buses.php`: Admin dashboard

## Database Setup

To set up the database for NextStop, run the following SQL script:

1. Download the `bus_booking.sql` file from the GitHub repo.
2. Import it into your MySQL database (using phpMyAdmin or MySQL Workbench).

## Team

Developed by Viren chauhan , 2025
