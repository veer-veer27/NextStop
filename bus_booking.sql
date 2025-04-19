-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2025 at 08:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bus_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(2, 'admin', 'admin@example.com', '$2y$10$VNmWCIZEZyJPAGh3Y5vFbeS0G8Zwr0Zvt7PaVe5sPENgRg08G3jOu', '2025-03-28 04:16:47');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bus_name` varchar(100) NOT NULL,
  `bus_no` varchar(50) NOT NULL,
  `bus_type` varchar(50) NOT NULL,
  `departure` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `selected_seats` varchar(50) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `passenger_name` varchar(100) NOT NULL,
  `passenger_email` varchar(100) NOT NULL,
  `passenger_age` int(11) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `seat_reservations` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `bus_no` varchar(20) NOT NULL,
  `bus_name` varchar(100) NOT NULL DEFAULT 'NextStop',
  `departure` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `bus_type` varchar(50) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `travel_date` date NOT NULL,
  `state` varchar(50) NOT NULL DEFAULT 'Gujarat',
  `available_seats` int(11) NOT NULL DEFAULT 55,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `bus_no`, `bus_name`, `departure`, `destination`, `departure_time`, `arrival_time`, `bus_type`, `ticket_price`, `travel_date`, `state`, `available_seats`, `image`) VALUES
(47, 'GJ 06 Z 1002', 'NextStop', 'VADODARA - BRC (VADODARA)', 'BANASKANTHA - BSK (BANASKANTHA)', '14:39:00', '18:43:00', 'sleeper ', 500.00, '2025-04-18', 'Gujarat', 48, '1744877308_19498d607a841df9565bc028e458169d.png'),
(48, 'GJ 05 Z 5205', 'NextStop', 'ANAND - ANND (ANAND)', 'DEVBHOOMI DWARKA - DBW (DEVBHOOMI DWARKA)', '15:40:00', '19:44:00', 'AC', 5000.00, '2025-04-18', 'Gujarat', 55, '1744877349_coach-3206326_1920.png'),
(50, 'GJ 06 Z 2341', 'NextStop', 'BHAVNAGAR - BVC (BHAVNAGAR)', 'VADODARA - BRC (VADODARA)', '13:40:00', '19:46:00', 'AC', 4500.00, '2025-04-18', 'Gujarat', 55, '1744877455_pngtree-d-rendering-of-a-white-isolated-background-featuring-a-medium-sized-image_3893569 - Copy.jpg'),
(51, 'GJ 06 Z 2542', 'NextStop', 'BANASKANTHA - BSK (BANASKANTHA)', 'MAHISAGAR - MAH (MAHISAGAR)', '14:42:00', '19:47:00', 'sleeper ', 4500.00, '2025-04-18', 'Gujarat', 55, '1744877497_bus-driver-iguazu-falls-coach-volvo-buses-bus.jpg'),
(52, 'GJ 05 Z 5001', 'NextStop', 'SURAT - ST (SURAT)', 'DWARKA - DWK (DWARKA)', '14:43:00', '19:48:00', 'sleeper ', 5300.00, '2025-04-18', 'Gujarat', 55, '1744877576_Volvo-Bus-PNG-Picture.png'),
(54, 'GJ 06 Z 1045', 'NextStop', 'VADODARA - BRC (VADODARA)', 'BANASKANTHA - BSK (BANASKANTHA)', '14:50:00', '19:55:00', 'AC', 4500.00, '2025-04-18', 'Gujarat', 55, '1744877993_Volvo-Bus-PNG-Transparent.png');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `user_id`) VALUES
(1, 'VIR', 'veer@gmail.com', 'sa', 'sedfv', '2025-04-01 10:50:16', NULL),
(2, 'VIR', 'jon@gmail.com', 'asa', 'dfghj', '2025-04-01 10:51:52', NULL),
(3, 'Veer chauhan', 'jon@gmail.com', 'asdfghj', 'zxcvbnm', '2025-04-01 10:52:23', NULL),
(4, 'VIR', 'jon@gmail.com', 'fghjk', 'fghjk', '2025-04-01 10:53:20', NULL),
(5, 'Veer chauhan', 'jon@gmail.com', 'asdf', 'asdf', '2025-04-06 11:40:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `seat_reservations`
--

CREATE TABLE `seat_reservations` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `bus_id` int(11) DEFAULT NULL,
  `seat_number` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `age` int(11) DEFAULT NULL CHECK (`age` >= 18),
  `phone` varchar(15) NOT NULL CHECK (`phone` regexp '^[0-9]{10,15}$'),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `email`, `password`, `age`, `phone`, `created_at`) VALUES
(1, 'jon', 'snow', 'jon@gmail.com', '$2y$10$aBautXTKabkpU72lUgmg6eUjl4SMCuYO5vM23XPXABEMILj4K1MUa', 20, '2526325630', '2025-03-28 03:59:54'),
(2, 'veer', 'chauhan', 'veer@gmail.com', '$2y$10$Tuy68WhlYoDtkwAiz/EzseU.bcxVz8KfAjb79CQGdCdIXhgecvq42', 20, '6353209834', '2025-04-01 07:25:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seat_reservations`
--
ALTER TABLE `seat_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bus_id` (`bus_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `seat_reservations`
--
ALTER TABLE `seat_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `seat_reservations`
--
ALTER TABLE `seat_reservations`
  ADD CONSTRAINT `seat_reservations_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`),
  ADD CONSTRAINT `seat_reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
