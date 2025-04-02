-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2025 at 04:39 PM
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
-- Database: `eyewear_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','manager','staff') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'Admin User', 'admin@example.com', 'hashed_password', '9876500000', 'admin', '2025-04-02 13:46:59'),
(2, 'Manager One', 'manager@example.com', 'hashed_password', '9765400000', 'manager', '2025-04-02 13:46:59'),
(3, 'Staff Member', 'staff@example.com', 'hashed_password', '9654300000', 'staff', '2025-04-02 13:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `frames`
--

CREATE TABLE `frames` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `material` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `shape` varchar(50) DEFAULT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `image5` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frames`
--

INSERT INTO `frames` (`id`, `name`, `price`, `material`, `description`, `color`, `shape`, `image1`, `image2`, `image3`, `image4`, `image5`, `created_at`) VALUES
(1, 'Classic Black Frame', 49.99, 'Metal', 'Premium eyewear for daily use.', 'Black', 'Rectangle', 'black1.jpg', 'black2.jpg', 'black3.jpg', 'black4.jpg', 'black5.jpg', '2025-04-02 13:46:59'),
(2, 'Round Silver Frame', 59.99, 'Metal', 'Premium eyewear for daily use.', 'Silver', 'Round', 'silver1.jpg', 'silver2.jpg', 'silver3.jpg', 'silver4.jpg', 'silver5.jpg', '2025-04-02 13:46:59'),
(3, 'Aviator Gold Frame', 79.99, 'Titanium', 'Premium eyewear for daily use.', 'Gold', 'Aviator', 'gold1.jpg', 'gold2.jpg', 'gold3.jpg', 'gold4.jpg', 'gold5.jpg', '2025-04-02 13:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `lenses`
--

CREATE TABLE `lenses` (
  `id` int(11) NOT NULL,
  `type` enum('single vision','bifocal','progressive') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lenses`
--

INSERT INTO `lenses` (`id`, `type`, `price`, `created_at`) VALUES
(1, 'single vision', 29.99, '2025-04-02 13:46:59'),
(2, 'bifocal', 49.99, '2025-04-02 13:46:59'),
(3, 'progressive', 69.99, '2025-04-02 13:46:59'),
(4, 'single vision', 29.99, '2025-04-02 13:46:59'),
(5, 'bifocal', 49.99, '2025-04-02 13:46:59'),
(6, 'progressive', 69.99, '2025-04-02 13:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 99.98, 'pending', '2025-04-02 13:46:59'),
(2, 2, 129.98, 'confirmed', '2025-04-02 13:46:59'),
(3, 3, 149.99, 'shipped', '2025-04-02 13:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `frame_id` int(11) DEFAULT NULL,
  `lens_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `frame_id`, `lens_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 1, 79.98),
(2, 2, 2, 2, 1, 99.98),
(3, 3, 3, 3, 1, 129.99);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit card','debit card','paypal','UPI','cash on delivery') NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `user_id`, `amount`, `payment_method`, `payment_status`, `transaction_id`, `created_at`) VALUES
(1, 1, 1, 99.98, 'credit card', 'completed', 'TXN123456', '2025-04-02 13:46:59'),
(2, 2, 2, 129.98, 'paypal', 'completed', 'TXN123457', '2025-04-02 13:46:59'),
(3, 3, 3, 149.99, 'UPI', 'pending', 'TXN123458', '2025-04-02 13:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `left_eye_sphere` decimal(5,2) NOT NULL,
  `left_eye_cylinder` decimal(5,2) DEFAULT NULL,
  `left_eye_axis` int(11) DEFAULT NULL,
  `right_eye_sphere` decimal(5,2) NOT NULL,
  `right_eye_cylinder` decimal(5,2) DEFAULT NULL,
  `right_eye_axis` int(11) DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `user_id`, `order_id`, `left_eye_sphere`, `left_eye_cylinder`, `left_eye_axis`, `right_eye_sphere`, `right_eye_cylinder`, `right_eye_axis`, `additional_notes`, `created_at`) VALUES
(1, 1, 1, -2.50, -0.75, 90, -2.00, -0.50, 85, 'Anti-glare coating required', '2025-04-02 13:46:59'),
(2, 2, 2, -1.25, -1.00, 100, -1.75, -0.50, 95, 'Blue light protection', '2025-04-02 13:46:59'),
(3, 3, 3, -3.00, -0.50, 85, -2.50, -0.75, 80, 'Photochromic lenses preferred', '2025-04-02 13:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `carrier` varchar(255) DEFAULT NULL,
  `estimated_delivery` date DEFAULT NULL,
  `status` enum('processing','shipped','out for delivery','delivered') DEFAULT 'processing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`id`, `order_id`, `user_id`, `tracking_number`, `carrier`, `estimated_delivery`, `status`, `created_at`) VALUES
(1, 1, 1, 'TRK123456', 'FedEx', '2025-04-05', 'processing', '2025-04-02 13:46:59'),
(2, 2, 2, 'TRK123457', 'UPS', '2025-04-03', 'shipped', '2025-04-02 13:46:59'),
(3, 3, 3, 'TRK123458', 'DHL', '2025-04-07', 'out for delivery', '2025-04-02 13:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `created_at`) VALUES
(1, 'John Doe', 'john@example.com', 'hashed_password', '9876543210', '123 Main St, NY', '2025-04-02 13:46:59'),
(2, 'Alice Smith', 'alice@example.com', 'hashed_password', '9765432109', '456 Oak St, CA', '2025-04-02 13:46:59'),
(3, 'Bob Johnson', 'bob@example.com', 'hashed_password', '9654321098', '789 Pine St, TX', '2025-04-02 13:46:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `frames`
--
ALTER TABLE `frames`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lenses`
--
ALTER TABLE `lenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `frame_id` (`frame_id`),
  ADD KEY `lens_id` (`lens_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`),
  ADD KEY `order_id` (`order_id`),
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
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `frames`
--
ALTER TABLE `frames`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lenses`
--
ALTER TABLE `lenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`frame_id`) REFERENCES `frames` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`lens_id`) REFERENCES `lenses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipping_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
