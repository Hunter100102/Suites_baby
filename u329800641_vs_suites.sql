-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 20, 2025 at 08:18 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u329800641_vs_suites`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `name` varchar(190) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `business_name` varchar(190) NOT NULL,
  `room_number` int(11) NOT NULL,
  `preferred_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `followup_sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `customer_name` varchar(190) NOT NULL,
  `customer_email` varchar(190) DEFAULT NULL,
  `customer_phone` varchar(60) DEFAULT NULL,
  `requested_date` date DEFAULT NULL,
  `requested_time` time DEFAULT NULL,
  `service_note` varchar(190) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('new','pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `tenant_id`, `customer_name`, `customer_email`, `customer_phone`, `requested_date`, `requested_time`, `service_note`, `message`, `status`, `created_at`) VALUES
(1, 1, 'William Cody Hunter', 'william@automatingsolutions.com', '7708964394', '2025-11-16', NULL, 'Mans cut', '', 'new', '2025-11-17 04:03:28'),
(2, 1, 'William Cody Hunter', 'william@automatingsolutions.com', '7708964394', '2025-11-16', '23:03:00', 'Mans cut', '', 'new', '2025-11-17 04:03:46'),
(3, 1, 'William Cody Hunter', 'william@automatingsolutions.com', '7708964394', '2025-11-16', '23:03:00', 'Mans cut', '', 'new', '2025-11-17 04:10:14'),
(4, 1, 'William Cody Hunter', 'william@automatingsolutions.com', '7708964394', '2025-11-16', '14:27:00', 'Mans cut', '', 'new', '2025-11-17 18:27:34'),
(5, 1, 'William Cody Hunter', 'william@automatingsolutions.com', '7708964394', '2025-11-16', '17:06:00', 'Mans cut', 'test', 'new', '2025-11-17 22:06:24'),
(6, 1, 'William Cody Hunter', 'william@automatingsolutions.com', '7708964394', '2025-11-16', '17:06:00', 'Mans cut', 'test', 'new', '2025-11-17 22:10:11'),
(7, 1, 'William Cody Hunter', 'william@automatingsolutions.com', '7708964394', '2025-11-17', '17:42:00', 'test', 'test', 'new', '2025-11-17 22:42:49'),
(8, 1, 'William Cody Hunter', 'william@automatingsolutions.com', '7708964394', '2025-11-17', '18:03:00', 'test', 'test', 'new', '2025-11-17 23:04:05'),
(9, 1, 'William Cody Hunter', 'william@automatingsolutions.com', '7708964394', '2025-11-17', '20:09:00', 'test', NULL, 'new', '2025-11-18 01:09:23');

-- --------------------------------------------------------

--
-- Table structure for table `onboarding_tokens`
--

CREATE TABLE `onboarding_tokens` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `score` tinyint(4) NOT NULL CHECK (`score` between 1 and 5),
  `author_name` varchar(120) DEFAULT NULL,
  `author_email` varchar(190) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `tenant_id`, `score`, `author_name`, `author_email`, `comment`, `created_at`) VALUES
(1, 1, 2, 'William Hunter', NULL, 'I mean this guy does an okay job, but he messed up my nails and my fade baby', '2025-11-17 22:15:46'),
(2, 1, 2, 'William Hunter', NULL, 'trest', '2025-11-17 22:41:46'),
(3, 1, 2, 'William Hunter', NULL, NULL, '2025-11-17 23:04:27'),
(4, 1, 2, 'William Hunter', NULL, NULL, '2025-11-17 23:04:53'),
(5, 1, 2, 'William Hunter', 'testing@gmail.com', 'test', '2025-11-17 23:05:19'),
(6, 1, 2, 'William Hunter', 'testing@gmail.com', 'new test', '2025-11-18 00:56:40'),
(7, 1, 2, 'William Hunter', 'testing@gmail.com', 'new test again', '2025-11-18 00:57:55'),
(8, 1, 3, 'William Hunter', 'testing@gmail.com', 'again', '2025-11-18 01:01:37'),
(9, 1, 5, 'William Hunter', 'testing@gmail.com', NULL, '2025-11-18 01:08:54');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_number` int(11) NOT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `tenant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `status`, `tenant_id`) VALUES
(1, 1, 'available', NULL),
(2, 2, 'unavailable', NULL),
(3, 3, 'available', NULL),
(4, 4, 'available', NULL),
(5, 5, 'available', NULL),
(6, 6, 'available', NULL),
(7, 7, 'available', NULL),
(8, 8, 'available', NULL),
(9, 9, 'available', NULL),
(10, 10, 'available', NULL),
(11, 11, 'available', NULL),
(12, 12, 'available', NULL),
(13, 13, 'available', NULL),
(14, 14, 'available', NULL),
(15, 15, 'available', NULL),
(16, 16, 'available', NULL),
(17, 17, 'available', NULL),
(18, 18, 'available', NULL),
(19, 19, 'available', NULL),
(20, 20, 'available', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(190) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(190) NOT NULL,
  `services` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 5.0,
  `avatar` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating_avg` decimal(3,2) DEFAULT NULL,
  `rating_count` int(11) DEFAULT 0,
  `room_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `user_id`, `business_name`, `services`, `bio`, `rating`, `avatar`, `updated_at`, `rating_avg`, `rating_count`, `room_number`) VALUES
(1, 3, 'AutomateIT', 'test', 'test $7', 5.0, '/uploads/avatars/1_1762315307.jpg', '2025-11-18 01:08:54', 2.44, 9, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tenant_photos`
--

CREATE TABLE `tenant_photos` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tenant_photos`
--

INSERT INTO `tenant_photos` (`id`, `tenant_id`, `file_path`, `caption`, `created_at`) VALUES
(1, 1, '/uploads/tenant_photos/1_1762315327.jpg', '', '2025-11-05 04:02:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','tenant') NOT NULL DEFAULT 'tenant',
  `name` varchar(190) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `role`, `name`, `phone`, `created_at`) VALUES
(2, 'Moustachehookah1@gmail.com', '$2y$10$y2BfFgQxqK8GTxy3Wmp8p.gk9o02Olaot5XsbCSvbbakcv/6zeUcm', 'admin', 'Administrator', NULL, '2025-11-05 03:46:07'),
(3, 'william@automatingsolutions.com', '$2y$10$Ha7Vy2J5tpNYxBwGPdi18uUnOYrpCzZcszFkAi3wmOLj6Lpf4z0QS', 'tenant', 'William Cody Hunter', NULL, '2025-11-05 03:56:07'),
(4, 'studio.aurora@example.com', '$2y$10$examplehashhashhashhashhashhashhash', 'tenant', '', '555-1001', '2025-11-10 05:07:52'),
(5, 'refinedglam@example.com', '$2y$10$examplehashhashhashhashhashhashhash', 'tenant', '', '555-1002', '2025-11-10 05:07:52'),
(6, 'precisioncuts@example.com', '$2y$10$examplehashhashhashhashhashhashhash', 'tenant', '', '555-1003', '2025-11-10 05:07:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tenant` (`tenant_id`);

--
-- Indexes for table `onboarding_tokens`
--
ALTER TABLE `onboarding_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tenant` (`tenant_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tenant_photos`
--
ALTER TABLE `tenant_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

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
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `onboarding_tokens`
--
ALTER TABLE `onboarding_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tenant_photos`
--
ALTER TABLE `tenant_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_ratings_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tenant_photos`
--
ALTER TABLE `tenant_photos`
  ADD CONSTRAINT `tenant_photos_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
