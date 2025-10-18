-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2025 at 06:22 PM
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
-- Database: `smart-life`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `email`, `password`, `name`) VALUES
(9, 'a2dmin@example.com', 'Admin123!', ''),
(10, 'a3dmin@example.com', 'Admin123!', 'zinix');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'ibna mahmud', 'Ebdsolution43@gmail.com', 'dfgberte', '2025-09-26 15:19:12');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orders_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orders_id`, `user_id`, `service_id`, `order_date`, `total_amount`, `status`) VALUES
(1254674, 7, 3, '2025-10-02 15:12:42', 5000, 'Cancelled'),
(649865298, 1, 4, '2025-10-02 15:12:42', 2000, 'Completed'),
(649865299, 7, 1, '2025-10-02 15:25:16', NULL, 'Processing'),
(649865300, 7, 1, '2025-10-02 15:25:34', NULL, 'Shipped'),
(649865301, 7, 1, '2025-10-02 16:09:38', NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`details`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `details`) VALUES
(1, 649865299, '{\"cleaning_type\":\"regular\",\"rooms\":\"8\",\"date\":\"2025-10-16\",\"time\":\"23:24\",\"address\":\"dhaka1212\"}'),
(2, 649865300, '{\"cleaning_type\":\"regular\",\"rooms\":\"8\",\"date\":\"2025-10-16\",\"time\":\"23:24\",\"address\":\"dhaka1212\"}'),
(3, 649865301, '{\"cleaning_type\":\"move_in_out\",\"rooms\":\"11\",\"date\":\"2025-10-16\",\"time\":\"23:24\",\"address\":\"dhaka1212\"}');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`) VALUES
(1, 'Home Cleaning', 'Professional cleaning services for your home.'),
(2, 'Plumber', 'Expert plumbing services at your doorstep.'),
(3, 'Electrician', 'Certified electricians for all your electrical needs.'),
(4, 'Gas Delivery', 'Fast and reliable gas cylinder delivery.'),
(5, 'Medicine Delivery', 'Get your medicines delivered to your home.'),
(6, 'Daily News', 'Stay updated with the latest news.'),
(7, 'House Shifting ', 'We offer a full range of packing, moving and storage services. This includes local moves and full packaging services. If you are packing yourself we can provide boxes and any other packing supplies for you to purchase.'),
(9, 'Painting Services', 'Looking for painting services near you? We have expert experienced painter in Bangladesh for painting services. Book now.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `address`, `phone`, `age`, `gender`, `token`, `created_at`) VALUES
(1, 'mahmud pranto', 'fbdsolution43@gmail.com', '$2y$10$k7BbeFIPm/8mXzTdymAQPe3Ig0Z2hjj9xaUBcETylr2n2lgHqDBPC', 'Badda, notun-bazar,100-feet, Sayednagar B-block\\', '1966613289', 28, 'male', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoxLCJlbWFpbCI6IkViZHNvbHV0aW9uNDNAZ21haWwuY29tIn0=.G226Sb1eTXRt9GWuy3viCl141c96iwITPkOcSRqJw2w=', '2025-09-26 13:48:44'),
(3, 'hira', 'E2bdsolution43@gmail.com', '$2y$10$k7BbeFIPm/8mXzTdymAQPe3Ig0Z2hjj9xaUBcETylr2n2lgHqDBPC', 'Badda, notun-bazar,100-feet, Sayednagar B-block\\', '1966613289', 28, 'male', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoxLCJlbWFpbCI6IkViZHNvbHV0aW9uNDNAZ21haWwuY29tIn0=.G226Sb1eTXRt9GWuy3viCl141c96iwITPkOcSRqJw2w=', '2025-09-26 13:48:44'),
(7, 'ibna mahmud', 'Ebdsolution97@gmail.com', '$2y$10$OSYCEMa0cfLOwkMB09BUv.c0tywiR/v2rNj5BXFexBaxuOC9vM5we', '', '01966613289', 0, '', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjo3LCJlbWFpbCI6IkViZHNvbHV0aW9uOTdAZ21haWwuY29tIn0=.U8mwt9Rh9PA/2T4yljH4fRCZE/4QQ4b9dSDLGjoe1JM=', '2025-10-02 14:36:21'),
(8, 'zini', 'Ebdsolution00@gmail.com', '$2y$10$mcsH1AdRAVc3Fyni3b2yKOlMLDD6OkJtIyNh6YZssDIueoK7hteOi', NULL, '123432434', 45, '0', NULL, '2025-10-14 13:31:24'),
(9, 'towsif', 'Ebdsolution5543@gmail.com', '$2y$10$c1jbp33gN53FFFj2CY3VPuj7E7U2ir9ssgOmAbR7fykH6Y9MOcXEO', NULL, '454567734545', 33, '0', NULL, '2025-10-14 13:32:00'),
(10, 'hatim', 'Ebdsolution1121@gmail.com', '$2y$10$d25ddV5wmA6bwzfhUfNv8esHBra7FXqRtjtc35J2DDQBJG8ecc6YC', 'badda, notun-bazar, sayednagar b-block, social islamic bank er opposite building', '0129343245', 34, 'male', NULL, '2025-10-14 13:41:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orders_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orders_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=649865302;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`orders_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
