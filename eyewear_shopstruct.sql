-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 07:49 PM
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
-- Database: `testing`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Admin One', 'admin1@example.com', 'adminpass1', '2025-04-02 19:05:31'),
(2, 'Admin Two', 'admin2@example.com', 'adminpass2', '2025-04-02 19:05:31'),
(3, 'Het Shah', 'hetshah6315@gmail.com', '$2y$10$G2wTiU6tZYNuibK.6qCk3O07GtovnzjC/b9/wB26qN4d.rI36unXW', '2025-05-17 10:09:25');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `name`) VALUES
(3, 'Gucci'),
(2, 'Oakley'),
(1, 'Ray-Ban'),
(4, 'sad'),
(5, 'sd');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `created_at`) VALUES
(3, 5, '2025-04-02 19:06:43'),
(4, 3, '2025-04-02 19:06:43'),
(5, NULL, '2025-05-17 07:40:11'),
(10, 4, '2025-05-20 17:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `frame_id` int(11) DEFAULT NULL,
  `lens_id` int(11) DEFAULT NULL,
  `prescription_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `frame_id`, `lens_id`, `prescription_id`, `quantity`, `price`) VALUES
(7, 5, 1, 1, NULL, 1, 200.00),
(8, 5, 1, 2, 3, 1, 225.00),
(9, 5, 1, 2, 4, 1, 225.00),
(10, 5, 1, 1, 5, 1, 200.00),
(11, 5, 1, 1, NULL, 1, 200.00),
(23, 10, 1, 1, 13, 1, 200.00),
(24, 10, 1, 4, NULL, 1, 672.00),
(25, 10, 1, NULL, NULL, 1, 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employee_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'John Doe', 'employee1@example.com', 'emp123', 'Manager', '2025-04-02 19:05:31'),
(2, 'Jane Smith', 'employee2@example.com', 'emp456', 'Sales', '2025-04-02 19:05:31');

-- --------------------------------------------------------

--
-- Table structure for table `frames`
--

CREATE TABLE `frames` (
  `frame_id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `gender` enum('men','women','child') NOT NULL,
  `description` text DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `material` varchar(255) NOT NULL,
  `shape` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `tag` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frames`
--

INSERT INTO `frames` (`frame_id`, `brand_id`, `gender`, `description`, `name`, `material`, `shape`, `price`, `tag`) VALUES
(1, 1, 'men', 'Classic black frame', 'Ray-Ban Aviator', 'Metal', 'Round', 150.00, 'featured'),
(2, 2, 'women', 'Lightweight design', 'Oakley Sports', 'Plastic', 'Rectangle', 120.00, 'popular'),
(3, 1, 'men', 'DSDSFSDF', 'TEST', 'STEEL', 'ROUND', 499.00, 'DF');

-- --------------------------------------------------------

--
-- Table structure for table `frame_category`
--

CREATE TABLE `frame_category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frame_category`
--

INSERT INTO `frame_category` (`category_id`, `name`) VALUES
(1, 'Men'),
(2, 'Women'),
(3, 'Child');

-- --------------------------------------------------------

--
-- Table structure for table `frame_category_map`
--

CREATE TABLE `frame_category_map` (
  `map_id` int(11) NOT NULL,
  `frame_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frame_category_map`
--

INSERT INTO `frame_category_map` (`map_id`, `frame_id`, `category_id`) VALUES
(4, 1, 3),
(5, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `frame_details`
--

CREATE TABLE `frame_details` (
  `detail_id` int(11) NOT NULL,
  `frame_id` int(11) DEFAULT NULL,
  `size` varchar(50) NOT NULL,
  `color` varchar(100) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `hinge_type` varchar(100) DEFAULT NULL,
  `nose_pad` tinyint(1) DEFAULT 1,
  `uv_protection` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frame_details`
--

INSERT INTO `frame_details` (`detail_id`, `frame_id`, `size`, `color`, `weight`, `hinge_type`, `nose_pad`, `uv_protection`) VALUES
(1, 1, 'Medium', 'Black', 30.00, 'Spring Hinge', 1, 1),
(2, 2, 'Large', 'Blue', 28.50, 'Standard', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `frame_images`
--

CREATE TABLE `frame_images` (
  `image_id` int(11) NOT NULL,
  `frame_id` int(11) DEFAULT NULL,
  `image_url` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frame_images`
--

INSERT INTO `frame_images` (`image_id`, `frame_id`, `image_url`) VALUES
(1, 1, 'images/img1.png'),
(2, 1, 'images/img2.png'),
(3, 2, 'images/img3.png'),
(4, 3, 'uploads/1747669491_682b51f3442e3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `lens`
--

CREATE TABLE `lens` (
  `lens_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lens`
--

INSERT INTO `lens` (`lens_id`, `category_id`, `type`, `price`, `description`) VALUES
(1, 1, 'Basic Single Vision', 50.00, 'Affordable single vision lenses'),
(2, 2, 'Bifocal Standard', 75.00, 'Traditional bifocal lenses'),
(3, 3, 'Premium Progressive', 120.00, 'High-quality progressive lenses'),
(4, 2, 'sadas', 522.00, 'sdasda');

-- --------------------------------------------------------

--
-- Table structure for table `lens_category`
--

CREATE TABLE `lens_category` (
  `category_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lens_category`
--

INSERT INTO `lens_category` (`category_id`, `type`, `description`) VALUES
(1, 'Single Vision', 'Corrects for one field of vision'),
(2, 'Bifocal', 'Lenses with two focal points'),
(3, 'Progressives', 'Seamless transition between multiple focal points'),
(4, 'adads', 'assda');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `status`, `created_at`, `order_note`) VALUES
(1, 1, 450.00, 'pending', '2025-04-02 19:05:59', NULL),
(2, 2, 180.00, 'shipped', '2025-04-02 19:05:59', NULL),
(3, 1, 450.00, 'pending', '2025-04-02 19:06:49', NULL),
(4, 2, 180.00, 'shipped', '2025-04-02 19:06:49', NULL),
(6, 4, 236.00, 'pending', '2025-05-17 09:16:25', 'asda'),
(7, 6, 611.00, 'pending', '2025-05-17 09:49:37', 'asdas'),
(8, 4, 881.00, 'pending', '2025-05-19 06:30:39', 'dsfsd'),
(9, 4, 241.00, 'pending', '2025-05-19 15:21:31', ''),
(10, 4, 311.00, 'pending', '2025-05-19 15:41:38', ''),
(11, 4, 311.00, 'pending', '2025-05-19 15:52:43', 'sdfs'),
(12, 4, 241.00, 'pending', '2025-05-19 15:55:54', '');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `frame_id` int(11) DEFAULT NULL,
  `lens_id` int(11) DEFAULT NULL,
  `prescription_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `frame_id`, `lens_id`, `prescription_id`, `quantity`, `price`) VALUES
(4, 1, 1, 1, 1, 1, 200.00),
(5, 1, 2, 3, 1, 1, 250.00),
(6, 2, 2, 2, 2, 1, 180.00),
(7, 6, 2, 2, 2, 1, 195.00),
(8, 7, 1, 1, 6, 1, 200.00),
(9, 7, 1, 1, 7, 1, 200.00),
(10, 7, 2, 1, NULL, 1, 170.00),
(11, 8, 1, 1, 1, 1, 200.00),
(12, 8, 2, 3, 1, 1, 240.00),
(13, 8, 1, 1, NULL, 1, 200.00),
(14, 8, 1, 1, 8, 1, 200.00),
(15, 9, 1, 1, 9, 1, 200.00),
(16, 10, 1, 3, 10, 1, 270.00),
(17, 11, 1, 3, 11, 1, 270.00),
(18, 12, 1, 1, 12, 1, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','paypal','bank_transfer','cod') NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `amount`, `payment_method`, `status`, `created_at`) VALUES
(1, 1, 450.00, 'credit_card', 'completed', '2025-04-02 19:07:04'),
(2, 2, 180.00, 'paypal', 'completed', '2025-04-02 19:07:04'),
(3, 6, 236.00, '', 'completed', '2025-05-17 09:16:25'),
(4, 7, 611.00, '', 'completed', '2025-05-17 09:49:37'),
(5, 8, 881.00, '', 'completed', '2025-05-19 06:30:39'),
(6, 9, 241.00, '', 'completed', '2025-05-19 15:21:31'),
(7, 10, 311.00, '', 'completed', '2025-05-19 15:41:38'),
(8, 11, 311.00, '', 'completed', '2025-05-19 15:52:43'),
(9, 12, 241.00, '', 'completed', '2025-05-19 15:55:54');

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `prescription_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `left_eye_sph` varchar(10) DEFAULT NULL,
  `right_eye_sph` varchar(10) DEFAULT NULL,
  `left_eye_cyl` varchar(10) DEFAULT NULL,
  `right_eye_cyl` varchar(10) DEFAULT NULL,
  `axis` varchar(10) DEFAULT NULL,
  `addition` varchar(10) DEFAULT NULL,
  `prescription_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`prescription_id`, `user_id`, `left_eye_sph`, `right_eye_sph`, `left_eye_cyl`, `right_eye_cyl`, `axis`, `addition`, `prescription_image`) VALUES
(1, 1, '-2.50', '-2.75', '-1.00', '-1.25', '90', '2.00', NULL),
(2, 2, '-1.75', '-2.00', '-0.75', '-1.00', '85', '1.50', NULL),
(3, NULL, '', '', '', '', '', '', NULL),
(4, 3, '', '', '', '', '', '', NULL),
(5, 3, '-0.50', '+0.50', '-0.25', '-0.75', '90', '+1.00', NULL),
(6, 6, '+1.50', '+1.50', '-0.25', '-0.50', '45', '+1.50', NULL),
(7, 6, '+1.00', '-0.50', '-0.50', '-0.75', '45', '+1.25', NULL),
(8, 4, '-0.50', '-1.00', '-1.00', '-0.75', '45', '+1.25', NULL),
(9, 4, '0.00', '-1.00', '-1.00', '-1.00', '135', '+1.50', NULL),
(10, 4, '-1.00', '+0.50', '-0.75', '-0.75', '90', '+1.00', NULL),
(11, 4, '-0.50', '-0.50', '', '', '', '', NULL),
(12, 4, '-0.50', '-0.50', '', '', '', '', NULL),
(13, 4, '+1.50', '+1.00', '', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `status` enum('pending','in_transit','delivered') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'India'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`shipping_id`, `order_id`, `tracking_number`, `shipping_address`, `status`, `created_at`, `name`, `phone`, `email`, `city`, `state`, `pincode`, `country`) VALUES
(1, 1, 'TRACK12345', '123 Main St, NY', 'in_transit', '2025-04-02 19:07:10', NULL, NULL, NULL, NULL, NULL, NULL, 'India'),
(2, 2, 'TRACK67890', '456 Elm St, CA', 'delivered', '2025-04-02 19:07:10', NULL, NULL, NULL, NULL, NULL, NULL, 'India'),
(3, 6, NULL, 'A 403 prakruti appt suvidha sanjivani road, Ahmedabad, 380007, India', 'pending', '2025-05-17 09:16:25', 'Het Shah', '9427961426', 'hetshah6315@gmail.com', NULL, NULL, NULL, 'India'),
(4, 7, NULL, 'A 403 prakruti appt suvidha sanjivani road, Ahmedabad, 380007, India', 'pending', '2025-05-17 09:49:37', 'Het Shah', '9427961426', 'hetshah6315@gmail.com', 'ahmdabad', 'gujarat', '380007', 'India'),
(5, 8, NULL, 'A 403 prakruti appt suvidha sanjivani road, Ahmedabad, 380007, India', 'pending', '2025-05-19 06:30:39', 'Het Shah', '09427961426', 'hetshah6315@gmail.com', NULL, NULL, NULL, 'India'),
(6, 9, NULL, 'A 403 prakruti appt suvidha sanjivani road, Ahmedabad, 380007, India', 'pending', '2025-05-19 15:21:31', 'Het Shah', '09427961426', 'hetshah6315@gmail.com', NULL, NULL, NULL, 'India'),
(7, 10, NULL, 'A 403 prakruti appt suvidha sanjivani road, Ahmedabad, 380007, India', 'pending', '2025-05-19 15:41:38', 'Het Shah', '9427961426', 'hetshah6315@gmail.com', NULL, NULL, NULL, 'India'),
(8, 11, NULL, 'A 403 prakruti appt suvidha sanjivani road, Ahmedabad, 380007, India', 'pending', '2025-05-19 15:52:43', 'Het Shah', '9427961426', 'hetshah6315@gmail.com', NULL, NULL, NULL, 'India'),
(9, 12, NULL, 'A 403 prakruti appt suvidha sanjivani road, Ahmedabad, 380007, India', 'pending', '2025-05-19 15:55:54', 'Het Shah', '9427961426', 'hetshah6315@gmail.com', NULL, NULL, NULL, 'India');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'India'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone`, `address`, `zip_code`, `created_at`, `city`, `state`, `country`) VALUES
(1, 'Alice Brown', 'alice@example.com', 'alicepass', '9876543210', '123 Main St, NY', '10001', '2025-04-02 19:05:31', NULL, NULL, 'India'),
(2, 'Bob White', 'bob@example.com', 'bobpass', '8765432109', '456 Elm St, CA', '90001', '2025-04-02 19:05:31', NULL, NULL, 'India'),
(3, 'het shah', 'hetshah6312@gmail.com', '', '', '', '', '2025-04-28 05:51:11', NULL, NULL, 'India'),
(4, 'het shah', 'hetshah6315@gmail.com', '', '09427961426', 'A 403 prakruti appt suvidha sanjivani road', '380007', '2025-04-28 18:18:19', NULL, NULL, 'India'),
(5, 'het', 'hetshah6313@gmail.com', '$2y$10$4uw.Lp2IvFTH6xCcMHDN6uqjiZq34XmcV.vFrPDRlRRv4cX6tNX4O', '', '', '', '2025-04-28 19:43:37', NULL, NULL, 'India'),
(6, 'het lj', 'hetlj6315@gmail.com', '', '', '', '', '2025-05-17 09:48:16', NULL, NULL, 'India');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `frame_id` (`frame_id`),
  ADD KEY `lens_id` (`lens_id`),
  ADD KEY `prescription_id` (`prescription_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `frames`
--
ALTER TABLE `frames`
  ADD PRIMARY KEY (`frame_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `frame_category`
--
ALTER TABLE `frame_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `frame_category_map`
--
ALTER TABLE `frame_category_map`
  ADD PRIMARY KEY (`map_id`),
  ADD KEY `frame_id` (`frame_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `frame_details`
--
ALTER TABLE `frame_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `frame_id` (`frame_id`);

--
-- Indexes for table `frame_images`
--
ALTER TABLE `frame_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `frame_id` (`frame_id`);

--
-- Indexes for table `lens`
--
ALTER TABLE `lens`
  ADD PRIMARY KEY (`lens_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `lens_category`
--
ALTER TABLE `lens_category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `type` (`type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `frame_id` (`frame_id`),
  ADD KEY `lens_id` (`lens_id`),
  ADD KEY `prescription_id` (`prescription_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `frames`
--
ALTER TABLE `frames`
  MODIFY `frame_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `frame_category`
--
ALTER TABLE `frame_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `frame_category_map`
--
ALTER TABLE `frame_category_map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `frame_details`
--
ALTER TABLE `frame_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `frame_images`
--
ALTER TABLE `frame_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lens`
--
ALTER TABLE `lens`
  MODIFY `lens_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lens_category`
--
ALTER TABLE `lens_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`frame_id`) REFERENCES `frames` (`frame_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cart_items_ibfk_3` FOREIGN KEY (`lens_id`) REFERENCES `lens` (`lens_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cart_items_ibfk_4` FOREIGN KEY (`prescription_id`) REFERENCES `prescription` (`prescription_id`) ON DELETE SET NULL;

--
-- Constraints for table `frames`
--
ALTER TABLE `frames`
  ADD CONSTRAINT `frames_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`) ON DELETE SET NULL;

--
-- Constraints for table `frame_category_map`
--
ALTER TABLE `frame_category_map`
  ADD CONSTRAINT `frame_category_map_ibfk_1` FOREIGN KEY (`frame_id`) REFERENCES `frames` (`frame_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `frame_category_map_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `frame_category` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `frame_details`
--
ALTER TABLE `frame_details`
  ADD CONSTRAINT `frame_details_ibfk_1` FOREIGN KEY (`frame_id`) REFERENCES `frames` (`frame_id`) ON DELETE CASCADE;

--
-- Constraints for table `frame_images`
--
ALTER TABLE `frame_images`
  ADD CONSTRAINT `frame_images_ibfk_1` FOREIGN KEY (`frame_id`) REFERENCES `frames` (`frame_id`) ON DELETE CASCADE;

--
-- Constraints for table `lens`
--
ALTER TABLE `lens`
  ADD CONSTRAINT `lens_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `lens_category` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`frame_id`) REFERENCES `frames` (`frame_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`lens_id`) REFERENCES `lens` (`lens_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_ibfk_4` FOREIGN KEY (`prescription_id`) REFERENCES `prescription` (`prescription_id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `frames` (`frame_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
