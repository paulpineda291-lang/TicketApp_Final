-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2026 at 11:04 AM
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
-- Database: `hau_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `event_date` date NOT NULL,
  `venue` varchar(200) NOT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `event_date`, `venue`, `banner_image`, `description`, `is_active`, `created_at`) VALUES
(1, 'HAU University Days 2026', '2026-03-25', 'HAU Main Campus', 'university_days.jpg', 'The biggest celebration of the year at Holy Angel University!', 1, '2026-03-21 07:53:23'),
(2, 'Sunset Soiree', '2026-03-28', 'Century Hotel, Angeles City', NULL, NULL, 1, '2026-03-21 10:03:09'),
(3, 'Regional Cybersecurity Conference', '2026-10-03', 'Holy Angel University Theater', NULL, NULL, 1, '2026-03-21 10:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `ticket_type_id` tinyint(3) UNSIGNED NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `status` enum('pending','payment_uploaded','verified','failed') NOT NULL DEFAULT 'pending',
  `reference_code` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `event_id`, `ticket_type_id`, `amount_paid`, `status`, `reference_code`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 299.00, 'verified', 'HAU-1958D74E37', '2026-02-17 11:53:10', '2026-03-21 07:53:24'),
(2, 2, 1, 1, 299.00, 'verified', 'HAU-A5B6299A8E', '2026-02-17 12:07:02', '2026-03-21 07:53:24'),
(3, 3, 1, 2, 199.00, 'verified', 'HAU-C7B482C0B5', '2026-02-17 12:11:45', '2026-03-21 07:53:24'),
(4, 4, 1, 2, 199.00, 'verified', 'HAU-D685131DB1', '2026-02-17 12:32:45', '2026-03-21 07:53:24'),
(5, 1, 1, 1, 299.00, 'verified', 'HAU-06F75E0D8E', '2026-02-17 12:43:01', '2026-03-21 07:53:24'),
(6, 6, 1, 2, 199.00, 'verified', 'HAU-3E6108CE6A', '2026-02-17 12:44:41', '2026-03-21 07:53:24'),
(7, 1, 1, 1, 299.00, 'verified', 'HAU-C5458CE0C2', '2026-02-18 15:46:00', '2026-03-21 07:53:24'),
(8, 1, 1, 1, 299.00, 'verified', 'HAU-7B6306A585', '2026-02-18 15:49:54', '2026-03-21 07:53:24');

-- --------------------------------------------------------

--
-- Table structure for table `payment_proofs`
--

CREATE TABLE `payment_proofs` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_proofs`
--

INSERT INTO `payment_proofs` (`id`, `order_id`, `file_name`, `uploaded_at`) VALUES
(1, 1, '1771329190_1.png', '2026-02-17 11:53:10'),
(2, 2, '1771330022_2.png', '2026-02-17 12:07:02'),
(3, 3, '1771330305_3.png', '2026-02-17 12:11:45'),
(4, 4, '1771331565_4.png', '2026-02-17 12:32:45'),
(5, 5, '1771332181_5.jpg', '2026-02-17 12:43:01'),
(6, 6, '1771332281_6.png', '2026-02-17 12:44:41'),
(7, 7, '1771429560_7.png', '2026-02-18 15:46:00'),
(8, 8, '1771429794_8.png', '2026-02-18 15:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `sent_tickets`
--

CREATE TABLE `sent_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sent_tickets`
--

INSERT INTO `sent_tickets` (`id`, `order_id`, `sent_at`) VALUES
(1, 2, '2026-02-17 12:07:07'),
(2, 3, '2026-02-17 12:11:51'),
(3, 4, '2026-02-17 12:32:51'),
(4, 5, '2026-02-17 12:43:08'),
(5, 6, '2026-02-17 12:44:48'),
(6, 7, '2026-02-18 15:46:06'),
(7, 8, '2026-02-18 15:50:01');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_types`
--

CREATE TABLE `ticket_types` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `allowed_for` enum('guest','student','both') NOT NULL DEFAULT 'both',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_types`
--

INSERT INTO `ticket_types` (`id`, `event_id`, `name`, `price`, `allowed_for`, `is_active`) VALUES
(1, 1, 'Guest Ticket', 299.00, 'guest', 1),
(2, 1, 'Student Ticket', 199.00, 'student', 1),
(7, 2, 'Guest Ticket', 1799.00, 'guest', 1),
(8, 2, 'Student Ticket', 1499.00, 'student', 1),
(9, 3, 'Guest Ticket', 0.00, 'guest', 1),
(10, 3, 'Student Ticket', 0.00, 'student', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(191) NOT NULL,
  `role` enum('guest','student') NOT NULL,
  `student_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `role`, `student_number`, `created_at`) VALUES
(1, 'paopaogracia1031@gmail.com', 'guest', NULL, '2026-02-17 11:51:20'),
(2, 'paulpineda291@gmail.com', 'guest', NULL, '2026-02-17 12:06:52'),
(3, 'paolo@student.hau.edu.ph', 'student', '123456', '2026-02-17 12:11:24'),
(4, 'wesly@student.hau.edu.ph', 'student', '20632321', '2026-02-17 12:32:25'),
(6, 'psgarcia@student.hau.edu.ph', 'student', '123456', '2026-02-17 12:44:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_code` (`reference_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ticket_type_id` (`ticket_type_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `orders_ibfk_event` (`event_id`);

--
-- Indexes for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `sent_tickets`
--
ALTER TABLE `sent_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `ticket_types`
--
ALTER TABLE `ticket_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ticket_event` (`event_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sent_tickets`
--
ALTER TABLE `sent_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ticket_types`
--
ALTER TABLE `ticket_types`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`),
  ADD CONSTRAINT `orders_ibfk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);

--
-- Constraints for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD CONSTRAINT `payment_proofs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sent_tickets`
--
ALTER TABLE `sent_tickets`
  ADD CONSTRAINT `sent_tickets_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_types`
--
ALTER TABLE `ticket_types`
  ADD CONSTRAINT `fk_ticket_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
