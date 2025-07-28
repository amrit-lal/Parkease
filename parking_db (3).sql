-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2025 at 09:09 AM
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
-- Database: `parking_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `slot_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `category` enum('2W','4W') NOT NULL,
  `vehicle_match` tinyint(1) NOT NULL,
  `scanned_vehicle_no` varchar(20) NOT NULL,
  `vehicle_no` varchar(20) NOT NULL,
  `status` enum('Pending','Booked','Occupied','Completed','Cancelled','Rejected') NOT NULL DEFAULT 'Pending',
  `booking_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `checkout_time` datetime DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `slot_id`, `slot_date`, `start_time`, `end_time`, `category`, `vehicle_match`, `scanned_vehicle_no`, `vehicle_no`, `status`, `booking_code`, `created_at`, `updated_at`, `checkout_time`, `cancelled_at`) VALUES
(29, 9, 5, '2025-07-14', '18:26:00', '19:26:00', '2W', 0, '', '', 'Completed', 'PASBCA23E08', '2025-07-14 07:27:54', '2025-07-14 07:34:50', '2025-07-14 09:34:50', NULL),
(30, 9, 5, '2025-07-14', '18:26:00', '19:26:00', '2W', 0, '', '', 'Completed', 'PAS63BB2DBF', '2025-07-14 07:35:38', '2025-07-14 07:36:29', '2025-07-14 09:36:29', NULL),
(31, 9, 5, '2025-07-15', '18:26:00', '19:26:00', '2W', 0, '', '', 'Rejected', NULL, '2025-07-15 08:33:50', '2025-07-15 08:34:18', NULL, NULL),
(32, 9, 5, '2025-07-18', '18:26:00', '19:26:00', '2W', 0, '', 'PB02123342', 'Completed', 'PASBBFB473C', '2025-07-18 07:01:12', '2025-07-18 07:25:37', '2025-07-18 09:25:37', NULL),
(33, 9, 2, '2025-07-18', '20:30:00', '21:31:00', '4W', 0, '', '', 'Completed', 'PAS692951E6', '2025-07-18 08:09:27', '2025-07-18 08:24:42', '2025-07-18 10:24:42', NULL),
(34, 9, 2, '2025-07-23', '20:30:00', '21:31:00', '4W', 0, '', '', '', 'PAS8A62E8FD', '2025-07-23 06:27:35', '2025-07-23 06:29:52', NULL, NULL),
(35, 9, 2, '2025-07-23', '20:30:00', '21:31:00', '4W', 0, '', '', '', 'PAS1D47ED41', '2025-07-23 06:37:04', '2025-07-23 06:37:43', NULL, NULL),
(36, 9, 2, '2025-07-23', '20:30:00', '21:31:00', '4W', 0, '', '', '', 'PASB3C2BEB0', '2025-07-23 06:52:12', '2025-07-23 06:53:29', NULL, '2025-07-23 06:53:29'),
(37, 9, 2, '2025-07-23', '20:30:00', '21:31:00', '4W', 0, '', '', 'Cancelled', 'PAS1B68CF86', '2025-07-23 07:07:04', '2025-07-23 07:07:36', NULL, '2025-07-23 07:07:36'),
(38, 9, 2, '2025-07-23', '20:30:00', '21:31:00', '4W', 0, '', '', 'Pending', NULL, '2025-07-23 07:08:19', '2025-07-23 07:08:19', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_form`
--

CREATE TABLE `contact_form` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guards`
--

CREATE TABLE `guards` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guards`
--

INSERT INTO `guards` (`id`, `unique_id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'GRD6867A78F8954D', 'ram', 'a@g.com', '$2y$10$Ospv24frEKrFwEzGb4GfK.Xx12ZwkYkLXGCPc./U9Yq0bHX9A2xym', '2025-07-04 10:06:07'),
(2, 'GRD686B6EB613EA2', 'sham', 's@g.com', '$2y$10$iEUWKwub1vCKCyWuWsS/AOvUse1Azv0UhmyqgVNx8713NjHmj3GnW', '2025-07-07 06:52:38');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Online',
  `transaction_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE `pricing` (
  `id` int(11) NOT NULL,
  `category` varchar(10) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`id`, `category`, `price`, `updated_at`) VALUES
(1, '2W', 100.00, '2025-07-15 08:32:45'),
(2, '4W', 150.00, '2025-07-15 08:32:45');

-- --------------------------------------------------------

--
-- Table structure for table `slots`
--

CREATE TABLE `slots` (
  `id` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `slot_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `category` enum('2W','4W') NOT NULL,
  `status` enum('Available','Pending','Booked','Occupied') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slots`
--

INSERT INTO `slots` (`id`, `location`, `slot_date`, `start_time`, `end_time`, `category`, `status`) VALUES
(2, '2', '2025-07-23', '20:30:00', '21:31:00', '4W', 'Pending'),
(4, '1', '2025-07-18', '10:25:00', '12:25:00', '4W', 'Available'),
(5, '3', '2025-07-18', '18:26:00', '19:26:00', '2W', 'Available'),
(7, '5', '2025-07-18', '09:04:00', '10:05:00', '2W', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `testimonial_text` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `testimonial_text`, `status`, `created_at`, `updated_at`) VALUES
(4, 9, 'very good servuice . every one should try there services .', 'Approved', '2025-07-16 09:36:24', '2025-07-18 07:25:24'),
(5, 9, 'very good services                                          ', 'Approved', '2025-07-18 08:03:39', '2025-07-18 08:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile_img` varchar(100) DEFAULT NULL,
  `license_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','blocked') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `profile_img`, `license_file`, `created_at`, `last_login`, `status`) VALUES
(9, 'a', 'a3@g.com', '$2y$10$wub8dwhiUOn1VukRCxlxguQN4cMJ1s/SYDACWG0gT98exnlj1gnN2', 'Screenshot (1).png', NULL, '2025-07-02 07:59:30', NULL, 'active'),
(11, 'a', 'a1@g.com', '$2y$10$zpZteSM3CSFBDCHB0EnY7upeQEcdiohoNq7o6B.Cs2VFcMoZMw9wu', 'Screenshot (1).png', NULL, '2025-07-02 07:59:30', NULL, 'active'),
(20, 'a', 'a22@g.com', '$2y$10$hsQBXu67Hdmp/kIJPU2oaOl8YxUVq2uDQM/uY3CY1es.sraSmqaTy', 'Screenshot (5).png', NULL, '2025-07-02 07:59:30', NULL, 'active'),
(29, 'a', 'ab@g.com', '$2y$10$7YPAOt1B8z3PC192EEnRT./kT16JWnMN6F6E6qFrde6czuEpOXmI.', 'profile_702396_1752562165.png', NULL, '2025-07-15 06:49:25', NULL, 'active'),
(32, 'ram', 'r@g.com', '$2y$10$p60Mv4sqnnMB7fKvfhNcI.vzrSKgG984faHROlTYD87Q7Oqhyv65O', 'profile_285521_1752824869.jpg', 'license_713465_1752824869.pdf', '2025-07-18 07:47:49', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `verifications`
--

CREATE TABLE `verifications` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `guard_id` int(11) NOT NULL,
  `verification_time` datetime NOT NULL,
  `action` enum('check-in','check-out') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verifications`
--

INSERT INTO `verifications` (`id`, `booking_id`, `guard_id`, `verification_time`, `action`) VALUES
(16, 29, 1, '2025-07-14 09:33:43', 'check-in'),
(17, 29, 1, '2025-07-14 09:34:50', 'check-out'),
(18, 30, 1, '2025-07-14 09:36:15', 'check-in'),
(19, 30, 1, '2025-07-14 09:36:29', 'check-out'),
(20, 32, 1, '2025-07-18 09:19:22', 'check-in'),
(21, 32, 1, '2025-07-18 09:25:37', 'check-out'),
(22, 33, 1, '2025-07-18 10:24:28', 'check-in'),
(23, 33, 1, '2025-07-18 10:24:42', 'check-out');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indexes for table `contact_form`
--
ALTER TABLE `contact_form`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guards`
--
ALTER TABLE `guards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slots`
--
ALTER TABLE `slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `verifications`
--
ALTER TABLE `verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `guard_id` (`guard_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `contact_form`
--
ALTER TABLE `contact_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `guards`
--
ALTER TABLE `guards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `slots`
--
ALTER TABLE `slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `verifications`
--
ALTER TABLE `verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verifications`
--
ALTER TABLE `verifications`
  ADD CONSTRAINT `verifications_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `verifications_ibfk_2` FOREIGN KEY (`guard_id`) REFERENCES `guards` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
