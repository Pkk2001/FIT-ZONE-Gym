-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2025 at 09:17 PM
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
-- Database: `fitzone`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `trainer_email` varchar(100) DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `customer_email`, `trainer_email`, `appointment_date`, `purpose`, `status`, `created_at`) VALUES
(2, 'prabath7689@gmail.com', NULL, '2025-04-17 13:32:00', 'yoga', 'pending', '2025-04-16 09:12:50'),
(3, 'prabath7689@gmail.com', NULL, '2025-04-18 23:06:00', 'bulk', 'pending', '2025-04-16 14:15:20'),
(4, 'pkk@gmail.com', NULL, '2025-04-17 00:34:00', 'yoga', 'pending', '2025-04-17 18:27:03'),
(5, 'prabath7689@gmail.com', NULL, '2025-04-26 00:32:00', 'bulk', 'pending', '2025-04-22 17:43:27');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `type` enum('schedule','appointment','timetable') NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `time` time NOT NULL,
  `duration` int(11) NOT NULL,
  `trainer` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `user_email`, `type`, `class_name`, `day`, `time`, `duration`, `trainer`, `created_at`) VALUES
(1, 'jone@gmail.com', 'appointment', 'yoga', 'Thursday', '15:10:00', 15, '', '2025-04-12 16:44:50'),
(2, 'jone@gmail.com', 'timetable', 'yoga', 'Thursday', '15:28:00', 60, '', '2025-04-12 16:45:49'),
(3, 'prabath7689@gmail.com', 'appointment', 'yoga', 'Monday', '10:21:00', 115, '', '2025-04-12 16:51:28'),
(4, 'prabath7689@gmail.com', 'appointment', 'yoga', 'Thursday', '14:01:00', 22, '', '2025-04-15 17:38:09'),
(5, 'prabath7689@gmail.com', 'appointment', 'yoga', 'Saturday', '00:23:00', 50, '', '2025-04-15 18:07:22'),
(6, 'prabath7689@gmail.com', 'appointment', 'yoga', 'Monday', '00:23:00', 30, '', '2025-04-15 18:14:37'),
(7, 'prabath7689@gmail.com', 'timetable', 'bulk', 'Tuesday', '00:23:00', 40, '', '2025-04-22 17:45:56');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `message_text` text NOT NULL,
  `staff_email` varchar(100) DEFAULT NULL,
  `reply_text` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `customer_email`, `message_text`, `staff_email`, `reply_text`, `replied_at`, `sent_at`) VALUES
(1, 'customer1@fitzone.com', 'Can you recommend a workout plan for weight loss?', 'staff@fitzone.com', 'Try cardio 3x/week and strength training.', '2025-04-15 16:47:41', '2025-04-15 16:47:41'),
(2, 'customer2@fitzone.com', 'What are the yoga class schedules?', 'yo@gmail.com', 'affafa', '2025-04-15 20:11:51', '2025-04-15 16:47:41'),
(3, 'prabath7689@gmail.com', 'fuck you', 'yo@gmail.com', 'oky\r\n', '2025-04-16 09:18:51', '2025-04-15 16:56:06'),
(4, 'prabath7689@gmail.com', 'dhfh', 'ra@gmail.com', 'ha', '2025-04-18 17:47:40', '2025-04-15 16:56:14'),
(5, 'prabath7689@gmail.com', 'adooooooooooo', 'pit@gmail.com', 'ei adooo', '2025-04-16 16:06:13', '2025-04-16 16:04:59'),
(6, 'pkk@gmail.com', 'hi', 'vahala@gmail.com', 'ei', '2025-04-17 18:28:37', '2025-04-17 18:26:34'),
(7, 'prabath7689@gmail.com', 'sadada', 'pit@gmail.com', 'sf', '2025-04-22 17:58:15', '2025-04-22 17:40:47'),
(8, 'prabath7689@gmail.com', 'asda', NULL, NULL, NULL, '2025-04-22 17:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `plan` enum('basic','standard','premium') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `fullname`, `email`, `phone`, `plan`, `created_at`) VALUES
(1, 'Prabhath Kaushalya Koralage', 'prabath7689@gmail.com', '+94754202061', 'standard', '2025-04-12 14:19:26'),
(2, 'Prabhath Kaushalya Koralage', 'prabath7689@gmail.com', '+94754202061', 'standard', '2025-04-12 14:20:57'),
(3, 'pky', 'prabath7689@gmail.com', '0754202061', 'standard', '2025-04-12 14:21:27'),
(4, 'Prabhath Kaushalya Koralage', 'prabath7689@gmail.com', '0754202061', 'basic', '2025-04-16 14:14:40'),
(5, 'Prabhath Kaushalya Koralage', 'prabath7689@gmail.com', '0754202061', 'basic', '2025-04-18 16:45:16'),
(6, 'Prabhath Kaushalya Koralage', 'prabath7689@gmail.com', '0754202061', 'standard', '2025-04-22 17:48:50');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`id`, `name`, `email`, `specialty`, `created_at`, `image`) VALUES
(6, 'John Marston', 'jone@gmail.com', 'Strength Training', '2025-04-14 20:11:10', NULL),
(7, 'Jack Marston', 'Jack@gmail.com', 'cardio', '2025-04-14 20:11:55', NULL),
(8, 'Bonnie MacFarlane', 'Bonnie@gmai.com', 'yoga', '2025-04-14 20:12:19', NULL),
(32, 'je', 'prabath7689@gmail.com', 'Strength Training', '2025-04-22 17:54:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `email` varchar(100) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','staff') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`email`, `fullname`, `password`, `role`, `created_at`) VALUES
('as@email.com', 'as', '$2y$10$pj9Padknf4uJ1q8RDBygKe77E2y..0pqNLxP2aBtwv6vaD/6o6GnK', 'customer', '2025-04-16 14:16:06'),
('customer1@fitzone.com', 'John Doe', '$2y$10$6bS7Y9j3Xz2Qz1W5V8K9eO7Xz1Y2W3E4R5T6Y7U8I9O0P1Q2W3E4R', 'customer', '2025-04-14 21:16:29'),
('customer2@fitzone.com', 'Jane Smith', '$2y$10$6bS7Y9j3Xz2Qz1W5V8K9eO7Xz1Y2W3E4R5T6Y7U8I9O0P1Q2W3E4R', 'customer', '2025-04-14 21:16:29'),
('f@gmail.com', 'p', '$2y$10$DWwXXD/IKXgjCCr6b4ca4uHQfcUxLMaM1CLMxe4B1wD5.Fe46O5TO', 'staff', '2025-04-22 18:23:14'),
('jo@gmail.com', 'jo', '$2y$10$VO0m.4v1wv1RCtz2csQtsuWYEahlt301T.tn1T1srBZV6ZvAqPqAi', 'customer', '2025-04-14 19:29:49'),
('KK@gmail.com', 'lla', '$2y$10$937lBSCUhkZKesdtxMBbyevA0om3gguaKcjQkuvaUBcQrCysZ9to.', 'staff', '2025-04-22 18:32:36'),
('pit@gmail.com', 'pit', '$2y$10$trdRZXphGaQ4lO34JmZeheDGz8oWEKYDp60vt1DpHEIgykUfdRnpu', 'staff', '2025-04-16 14:16:49'),
('pkk@gmail.com', 'pkk', '$2y$10$QgtOP5rlYTNOpteVQlJKZuyFG56qNFjgfmz8y3CxrYy/EypDi.Vxm', 'customer', '2025-04-17 18:25:51'),
('ppk@gmail.com', 'ppk', '$2y$10$kFyHUp6EwfI50xhkvlm4MuFkM6ZMchXoyOM/gNoxfTS3REgaVfI82', 'customer', '2025-04-22 17:33:32'),
('prabath7689@gmail.com', 'pny', '$2y$10$.2lVJ3kvk8zw0wpTip3SQuxBeKCYkyxXXizY.5uNIV9rpgLb9VAfy', 'customer', '2025-04-14 20:06:41'),
('ra@gmail.com', 'ramal', '$2y$10$O6aUtu.P8p.rpYSSVaIN2uU3.kCTTx1t9MzGiv6WIK86Fk/4QGI5G', 'staff', '2025-04-18 17:46:33'),
('staff@fitzone.com', 'Staff User', '$2y$10$5vX8Z9Y0A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W', 'staff', '2025-04-14 19:18:09'),
('vahala@gmail.com', 'vahala', '$2y$10$rYD5SZnJwoWNzT5PtGqqDO7RyhEc2kTGcFv0x/089qFUs2aW6tIhW', 'staff', '2025-04-17 18:28:03'),
('yo@gmail.com', 'yo', '$2y$10$GopfUgbmG9VB1wlqE8mJQuLSDM3r/7aWVRXEIH3HrajB8pNMCWubG', 'staff', '2025-04-14 21:21:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_email` (`customer_email`),
  ADD KEY `trainer_email` (`trainer_email`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_email` (`customer_email`),
  ADD KEY `staff_email` (`staff_email`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`customer_email`) REFERENCES `users` (`email`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`trainer_email`) REFERENCES `trainers` (`email`) ON DELETE SET NULL;

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `contact_messages_ibfk_1` FOREIGN KEY (`customer_email`) REFERENCES `users` (`email`) ON DELETE CASCADE,
  ADD CONSTRAINT `contact_messages_ibfk_2` FOREIGN KEY (`staff_email`) REFERENCES `users` (`email`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
