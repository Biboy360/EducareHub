-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2025 at 03:00 PM
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
-- Database: `educarehub`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `admin_id`, `action`, `details`, `created_at`) VALUES
(1, NULL, 'Login', 'Admin logged in', '2025-06-20 12:55:52'),
(2, NULL, 'Login', 'Admin logged in', '2025-06-20 13:48:58'),
(3, NULL, 'Login', 'Admin logged in', '2025-06-20 13:51:02'),
(4, 2, 'Login', 'Admin logged in', '2025-06-20 15:09:11'),
(5, 2, 'Login', 'Admin logged in', '2025-06-20 22:53:45'),
(6, 2, 'Login', 'Admin logged in', '2025-06-20 23:59:23'),
(7, NULL, 'Login', 'Admin logged in', '2025-06-21 01:53:34'),
(8, 2, 'Login', 'Admin logged in', '2025-06-21 01:56:26'),
(9, 2, 'Login', 'Admin logged in', '2025-06-21 02:19:21'),
(10, NULL, 'Login', 'Admin logged in', '2025-06-21 02:27:00'),
(11, 2, 'Login', 'Admin logged in', '2025-06-21 02:29:13'),
(12, 2, 'Login', 'Admin logged in', '2025-06-21 02:48:49'),
(13, NULL, 'Login', 'Admin logged in', '2025-06-21 04:26:42'),
(14, 2, 'Login', 'Admin logged in', '2025-06-21 04:27:09'),
(15, 2, 'Login', 'Admin logged in', '2025-06-21 05:01:29'),
(16, 2, 'Login', 'Admin logged in', '2025-06-21 10:02:23'),
(17, 2, 'Login', 'Admin logged in', '2025-06-22 00:43:17'),
(18, NULL, 'Login', 'Admin logged in', '2025-06-22 07:37:42'),
(19, 2, 'Login', 'Admin logged in', '2025-06-22 07:38:08'),
(20, NULL, 'Login', 'Admin logged in', '2025-06-22 07:39:14'),
(21, NULL, 'Login', 'Admin logged in', '2025-06-22 07:42:15'),
(22, NULL, 'Login', 'Admin logged in', '2025-06-22 07:42:49'),
(23, 2, 'Login', 'Admin logged in', '2025-06-22 07:45:57'),
(24, NULL, 'Login', 'Admin logged in', '2025-06-22 07:47:12'),
(25, 2, 'Login', 'Admin logged in', '2025-06-22 07:47:42'),
(26, NULL, 'Login', 'Admin logged in', '2025-06-22 07:48:57'),
(27, 2, 'Login', 'Admin logged in', '2025-06-22 07:49:43'),
(28, NULL, 'Login', 'Admin logged in', '2025-06-22 07:50:53'),
(29, 2, 'Login', 'Admin logged in', '2025-06-22 07:51:18'),
(30, NULL, 'Login', 'Admin logged in', '2025-06-22 07:52:02'),
(31, NULL, 'Login', 'Admin logged in', '2025-06-22 07:58:16'),
(32, NULL, 'Login', 'Admin logged in', '2025-06-22 07:59:27'),
(33, 2, 'Login', 'Admin logged in', '2025-06-22 07:59:59'),
(34, NULL, 'Login', 'Admin logged in', '2025-06-22 08:03:01'),
(35, NULL, 'Login', 'Admin logged in', '2025-06-22 08:03:41'),
(36, NULL, 'Login', 'Admin logged in', '2025-06-22 08:04:08'),
(37, NULL, 'Login', 'Admin logged in', '2025-06-22 08:04:55'),
(38, NULL, 'Login', 'Admin logged in', '2025-06-22 08:05:40'),
(39, 2, 'Login', 'Admin logged in', '2025-06-22 08:14:23'),
(40, NULL, 'Login', 'Admin logged in', '2025-06-22 08:15:41'),
(41, 2, 'Login', 'Admin logged in', '2025-06-22 08:16:37'),
(42, NULL, 'Login', 'Admin logged in', '2025-06-22 10:17:23'),
(43, 2, 'Login', 'Admin logged in', '2025-06-22 10:17:43'),
(44, 2, 'Login', 'Admin logged in', '2025-06-22 10:18:53'),
(45, 2, 'Login', 'Admin logged in', '2025-06-22 11:08:29'),
(46, NULL, 'Login', 'Admin logged in', '2025-06-22 11:43:48'),
(47, NULL, 'Login', 'Admin logged in', '2025-06-22 11:44:51'),
(48, 2, 'Login', 'Admin logged in', '2025-06-22 11:45:30'),
(49, 2, 'Login', 'Admin logged in', '2025-06-22 12:31:13'),
(50, 25, 'Login', 'Admin logged in', '2025-06-22 12:33:59'),
(51, 2, 'Login', 'Admin logged in', '2025-06-22 12:34:46'),
(52, 26, 'Login', 'Admin logged in', '2025-06-22 12:35:10'),
(53, 2, 'Login', 'Admin logged in', '2025-06-22 12:35:49'),
(54, 27, 'Login', 'Admin logged in', '2025-06-22 12:36:14'),
(55, 28, 'Login', 'Admin logged in', '2025-06-22 12:36:40'),
(56, 24, 'Login', 'Admin logged in', '2025-06-22 12:37:18'),
(57, 2, 'Login', 'Admin logged in', '2025-06-22 12:38:32'),
(58, 27, 'Login', 'Admin logged in', '2025-06-22 13:26:09'),
(59, 29, 'Login', 'Admin logged in', '2025-06-22 13:29:01'),
(60, 33, 'Login', 'Admin logged in', '2025-06-22 13:40:00'),
(61, 29, 'Login', 'Admin logged in', '2025-06-22 14:24:03'),
(62, 33, 'Login', 'Admin logged in', '2025-06-22 14:35:08'),
(63, 33, 'Login', 'Admin logged in', '2025-06-22 15:13:25'),
(64, 29, 'Login', 'Admin logged in', '2025-06-22 15:13:38'),
(65, 33, 'Login', 'Admin logged in', '2025-06-22 15:35:33'),
(66, 29, 'Login', 'Admin logged in', '2025-06-22 15:43:17'),
(67, 33, 'Login', 'Admin logged in', '2025-06-22 15:45:08'),
(68, 24, 'Login', 'Admin logged in', '2025-06-23 06:16:26'),
(69, 26, 'Login', 'Admin logged in', '2025-06-23 06:20:37'),
(70, 25, 'Login', 'Admin logged in', '2025-06-23 06:25:40'),
(71, 26, 'Login', 'Admin logged in', '2025-06-23 06:27:15'),
(72, 25, 'Login', 'Admin logged in', '2025-06-23 06:27:57'),
(73, 26, 'Login', 'Admin logged in', '2025-06-23 06:38:30'),
(74, 25, 'Login', 'Admin logged in', '2025-06-23 06:50:57'),
(75, 2, 'Login', 'Admin logged in', '2025-06-23 07:10:29'),
(76, 25, 'Login', 'Admin logged in', '2025-06-23 07:20:23'),
(77, 26, 'Login', 'Admin logged in', '2025-06-23 07:34:03'),
(78, 24, 'Login', 'Admin logged in', '2025-06-23 07:39:36'),
(79, 25, 'Login', 'Admin logged in', '2025-06-23 09:07:38'),
(80, 24, 'Login', 'Admin logged in', '2025-06-23 09:27:09'),
(81, 33, 'Login', 'Admin logged in', '2025-06-23 09:27:48'),
(82, 29, 'Login', 'Admin logged in', '2025-06-23 09:28:10'),
(83, 27, 'Login', 'Admin logged in', '2025-06-23 09:28:28'),
(84, 2, 'Login', 'Admin logged in', '2025-06-23 09:32:49'),
(85, 24, 'Login', 'Admin logged in', '2025-06-23 09:34:11'),
(86, 2, 'Login', 'Admin logged in', '2025-06-23 09:34:43'),
(87, 38, 'Login', 'Admin logged in', '2025-06-23 09:37:11'),
(88, 33, 'Login', 'Admin logged in', '2025-06-23 09:37:37'),
(89, 29, 'Login', 'Admin logged in', '2025-06-23 09:37:50'),
(90, 27, 'Login', 'Admin logged in', '2025-06-23 09:38:07'),
(91, 38, 'Login', 'Admin logged in', '2025-06-23 09:39:14'),
(92, 27, 'Login', 'Admin logged in', '2025-06-23 09:44:24'),
(93, 38, 'Login', 'Admin logged in', '2025-06-23 09:44:57'),
(94, 33, 'Login', 'Admin logged in', '2025-06-23 09:45:11'),
(95, 38, 'Login', 'Admin logged in', '2025-06-23 09:48:03'),
(96, 38, 'Login', 'Admin logged in', '2025-06-23 09:52:35'),
(97, 33, 'Login', 'Admin logged in', '2025-06-23 10:02:24'),
(98, 24, 'Login', 'Admin logged in', '2025-06-23 10:02:45'),
(99, 38, 'Login', 'Admin logged in', '2025-06-23 10:03:18'),
(100, 27, 'Login', 'Admin logged in', '2025-06-23 10:03:37'),
(101, 33, 'Login', 'Admin logged in', '2025-06-23 10:03:51'),
(102, 33, 'Login', 'Admin logged in', '2025-06-23 10:05:36'),
(103, 29, 'Login', 'Admin logged in', '2025-06-23 10:07:39'),
(104, 38, 'Login', 'Admin logged in', '2025-06-23 10:10:40'),
(105, 38, 'Login', 'Admin logged in', '2025-06-23 10:31:08'),
(106, 38, 'Login', 'Admin logged in', '2025-06-23 10:37:16'),
(107, 38, 'Login', 'Admin logged in', '2025-06-23 10:38:58'),
(108, 24, 'Login', 'Admin logged in', '2025-06-23 10:56:16'),
(109, 33, 'Login', 'Admin logged in', '2025-06-23 10:57:05'),
(110, 29, 'Login', 'Admin logged in', '2025-06-23 10:57:23'),
(111, 27, 'Login', 'Admin logged in', '2025-06-23 10:57:38'),
(112, 2, 'Login', 'Admin logged in', '2025-06-23 10:57:54'),
(113, 38, 'Login', 'Admin logged in', '2025-06-23 11:05:18'),
(114, 38, 'Login', 'Admin logged in', '2025-06-23 11:07:19'),
(115, 38, 'Login', 'Admin logged in', '2025-06-23 11:07:39'),
(116, 38, 'Login', 'Admin logged in', '2025-06-23 11:08:25'),
(117, 38, 'Login', 'Admin logged in', '2025-06-23 11:17:55'),
(118, 24, 'Login', 'Admin logged in', '2025-06-23 11:19:53'),
(119, 29, 'Login', 'Admin logged in', '2025-06-23 11:20:14'),
(120, 33, 'Login', 'Admin logged in', '2025-06-23 11:20:25'),
(121, 27, 'Login', 'Admin logged in', '2025-06-23 11:20:39'),
(122, 2, 'Login', 'Admin logged in', '2025-06-23 11:20:59'),
(123, 38, 'Login', 'Admin logged in', '2025-06-24 05:08:42'),
(124, 38, 'Login', 'Admin logged in', '2025-06-24 05:09:42'),
(125, 38, 'Login', 'Admin logged in', '2025-06-24 05:13:51'),
(126, 38, 'Login', 'Admin logged in', '2025-06-24 05:15:17'),
(127, 38, 'Login', 'Admin logged in', '2025-06-24 05:38:18'),
(128, 24, 'Login', 'Admin logged in', '2025-06-24 05:44:28'),
(129, 29, 'Login', 'Admin logged in', '2025-06-24 05:44:46'),
(130, 33, 'Login', 'Admin logged in', '2025-06-24 05:44:58'),
(131, 27, 'Login', 'Admin logged in', '2025-06-24 05:45:10'),
(132, 38, 'Login', 'Admin logged in', '2025-06-24 05:45:31'),
(133, 38, 'Login', 'Admin logged in', '2025-06-24 07:14:56');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `fullname` varchar(225) NOT NULL,
  `username` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `Salary` decimal(10,2) NOT NULL,
  `Role` enum('super_admin','admin','stock_encoder','purchasing_officer','rewards_manager','viewer') NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `fullname`, `username`, `password`, `email`, `Salary`, `Role`, `photo`, `last_login`) VALUES
(2, 'rolly', 'hahaha', 'hahaha', 'hahaha@hhahah', 99999999.99, 'super_admin', NULL, '2025-06-23 13:20:59'),
(24, 'Gerry Tapuyo', 'admin gerry', '$2y$10$OlByr5j8kopipikp2b7amO.TTYsFSwvZt65BV6qN5vD8ap3BF/1l2', 'gerry.educarehub@gmail.com', 100000.00, 'admin', NULL, '2025-06-24 07:44:28'),
(25, 'Rolly Ruiz', 'S.E rolly', '$2y$10$fpRFR..9elCKIPv5g/dhNu1.lz7chd4feQcnOr/jltlxIMr1.H8XG', 'ruizrollyc.educarehub@gmail.com', 1000000.00, 'stock_encoder', NULL, '2025-06-23 11:07:38'),
(26, 'Quency Sosa', 'P.O quency', '$2y$10$Pd7h2qpuikfbpbkd/jC1PejEWowSJYBstyUeylS3wNM9awpwlQ4bS', 'quency.educarehub@gmail.com', 10000.00, 'purchasing_officer', NULL, '2025-06-23 09:34:03'),
(27, 'Earol John', 'R.M Earol', '$2y$10$AD5QG0vtzenuQCjccyC/HeFlvuIDFZ0S8aZpDyoi8xjC5IbArK9W.', 'earol.educarehub@gmail.com', 32000.00, 'rewards_manager', NULL, '2025-06-24 07:45:10'),
(28, 'Son Junel', 'V.R son', '$2y$10$wOU5.19Z208DJp878.Q8BeZvtHSt1RbBuuTT3gDU.V.XmXCHh4DQm', 'son.educarehub@gmail.com', 45000.00, 'viewer', NULL, '2025-06-22 14:36:40'),
(29, 'Carel', 'S.E carel', '$2y$10$uEVdnJwJ2aC2VLKTFGoY3eBmjq.7k6.h9U1WOg1Z2HnKQYNWhctle', 'carel.educarehub@gmail.com', 1000000.00, 'stock_encoder', NULL, '2025-06-24 07:44:46'),
(30, 'Michael', 'S.E michael', '$2y$10$Q6ATAmpnd0EJZJaeS4Xae.S4nZu5bRX0j.T1M7tojSc7zdDFf6VeG', 'michael.educarehub@gmail.com', 1000000.00, 'stock_encoder', NULL, NULL),
(31, 'Joey', 'S.E joey', '$2y$10$gsO3R8hjcdUTJEZzPYyUneX8jQdKkKJy/Sy0lJt4tYyNe//RByTf2', 'joey.educarehub@gmail.com', 1000000.00, 'stock_encoder', NULL, NULL),
(32, 'Mark Noga', 'P.O noga', '$2y$10$IyiH1in01ZMzX1t8gupHueO/JbKxhs3m2QJk5gpXQqa.l8paBs6Sm', 'marknoga.educarehub@gmail.com', 999999.98, 'purchasing_officer', NULL, NULL),
(33, 'iverson', 'P.O iver', '$2y$10$Gu9DdVpVX2hHrJ5Z/CZhhO2IVV9FfyimZPRlb8VdPY.li3mGpD6WG', 'iver.educarehub@gmail.com', 10000000.00, 'purchasing_officer', NULL, '2025-06-24 07:44:58'),
(34, 'dindo', 'P.O dindo', '$2y$10$t9Xg5ZidBRdEgjJgOieL7eqKcjWInKbAV0JpAQ31wLXdNiYM6ulKC', 'dindo.educarehub@gmail.com', 10000000.00, 'purchasing_officer', NULL, NULL),
(35, 'Brent Joseph', 'R.M brent', '$2y$10$K70NGpD/mstIuhxFpaTEY.aBhgiP4P0cw/SHEcwCcGDMlP7NtnN9G', 'brent.educarehub@gmail.com', 10000000.00, 'rewards_manager', NULL, NULL),
(36, 'Coloma', 'R.M coloma', '$2y$10$Z45qsrxPRyBy74l8lqormOX0hmSX4DAb6AstWTLgUa/RgyIdvJlOW', 'coloma.educarehub@gmail.com', 10000000.00, 'rewards_manager', NULL, NULL),
(37, 'jollyann', 'R.M jollyann', '$2y$10$Iq9MTutH8apjqNU8XZ9cGO1X3TTS63uSwsZlRwvt1cIi3nTR4uFIW', 'jollyann.educarehub@gmail.com', 99999999.99, 'rewards_manager', NULL, NULL),
(38, 'EducareHub', 'educarehubsp', 'educare123', 'educarehub@gmail.com', 0.00, 'super_admin', NULL, '2025-06-24 09:14:56');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(225) NOT NULL,
  `description` text NOT NULL,
  `img` varchar(225) NOT NULL,
  `supplier` varchar(225) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `points` int(11) DEFAULT 0,
  `category` varchar(100) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `img`, `supplier`, `price`, `created_by`, `created_at`, `updated_at`, `points`, `category`, `sku`) VALUES
(1, 'Pilot BP-S Ballpoint Pen', 'Black or Blue, 0.7mm.', 'img/product1.png', 'National Book Store', 26.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2600, 'Writing Tools', 'PILOT-BPS-07'),
(2, 'Mongol No.2 Pencil', 'No. 2, Classic hexagonal barrel.', 'img/product2.png', 'National Book Store', 12.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1200, 'Writing Tools', 'MONG-NO2'),
(3, 'Faber-Castell Dust-Free Eraser', 'Small size, clean erasing, minimal residue.', 'img/product3.png', 'National Book Store', 22.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2200, 'Eraser & Correction Tools', 'FC-ERASE-S'),
(4, 'Paperlink Jumbo Spiral Notebook', '80 leaves, ruled pages, simple design.', 'img/product4.png', 'National Book Store', 22.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2200, 'Notebook & Paper', 'PLINK-JUMBO-NB'),
(5, 'Dong-A My-Gel Pen', '0.5mm, 3-color pack (Black, Blue, Red).', 'img/product5.png', 'National Book Store', 20.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2000, 'Writing Tools', 'DONGA-MYGEL-05'),
(6, 'Stabilo BOSS ORIGINAL Highlighter', '1 pc (Yellow or Pink), chisel tip, anti-dry-out.', 'img/product6.png', 'National Book Store', 134.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 13400, 'Writing Tools', 'STABILO-BOSS-HL'),
(7, 'HBW Sticky Notes', '(3x3 inch, 100 sheets) Standard square sticky notes, common colors.', 'img/product7.png', 'National Book Store', 37.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 3700, 'Notebook & Paper', 'HBW-STICKY-NOTE'),
(8, 'Limelight Composition Notebook', '80 leaves, 2-pack, stitched binding, durable covers.', 'img/product8.png', 'National Book Store', 60.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 6000, 'Notebook & Paper', 'LIME-COMP-NB'),
(9, 'Pilot G-Tec C4 Gel Pen Set', '0.4mm, 4-color pack, ultra-fine point for precise writing.', 'img/product9.png', 'National Book Store', 60.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 6000, 'Writing Tools', 'PILOT-GTC-C4'),
(10, 'Faber-Castell Classic Colour Pencils', '12-color set, break-resistant leads, vibrant pigments.', 'img/product10.png', 'National Book Store', 120.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 12000, 'Writing Tools', 'FC-CLSSC-CP'),
(11, 'Limelight Undated Planner', 'Small Weekly/Monthly, simple layouts for planning tasks and goals.', 'img/product11.png', 'National Book Store', 349.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 34900, 'Filing & Organization', 'LIME-UNDTD-PL'),
(12, 'Orion Plastic Ruler & Protractor Set', '12 inch/30 cm ruler + protractor, clear plastic, accurate measurements.', 'img/product12.png', 'National Book Store', 80.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 8000, 'Scaler', 'ORION-RULER-SET'),
(13, 'Simple Plastic Pen Holder / Desk Organizer', 'Basic caddy with 3-5 compartments.', 'img/product13.png', 'National Book Store', 25.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2500, 'Organizer', 'SIMPLE-PENHOLDER'),
(14, 'Sterling Premium Spiral Notebook', '100 leaves, 3-pack, high-quality paper, durable covers.', 'img/product14.png', 'National Book Store', 93.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 9300, 'Notebook & Paper', 'STERLING-SPIRAL-NB'),
(15, 'Generic 1-inch 3-Ring Binder', '1-inch 3-ring binder for organizing papers and documents.', 'img/product15.png', 'National Book Store', 130.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 13000, 'Filing & Organization', 'GENERIC-BINDER-1IN'),
(16, 'HBW Mini Portable Whiteboard', 'A4 size, with Markers & Eraser, reusable surface.', 'img/product16.png', 'National Book Store', 90.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 9000, 'Classroom Tools', 'HBW-WHITEBOARD-MINI'),
(17, 'Hard Copy A4 Bond Paper (10 pcs)', '10 pcs, A4 size, 70gsm, white, suitable for printers and writing.', 'img/product17.png', 'National Book Store', 4.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 400, 'Notebook & Paper', 'HCOPY-A4BP-10'),
(18, 'Hard Copy A4 Bond Paper (20 pcs)', '20 pcs, A4 size, 70gsm, white, suitable for printers and writing.', 'img/product18.png', 'National Book Store', 8.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 800, 'Notebook & Paper', 'HCOPY-A4BP-20'),
(19, 'Hard Copy A4 Bond Paper (50 pcs)', '50 pcs, A4 size, 70gsm, white, suitable for printers and writing.', 'img/product19.png', 'National Book Store', 20.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2000, 'Notebook & Paper', 'HCOPY-A4BP-50'),
(20, 'Hard Copy A4 Bond Paper (100 pcs)', '100 pcs, A4 size, 70gsm, white, suitable for printers and writing.', 'img/product20.png', 'National Book Store', 40.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 4000, 'Notebook & Paper', 'HCOPY-A4BP-100'),
(21, 'Hard Copy A4 Bond Paper (250 pcs)', '250 pcs (Half Ream), A4 size, 70gsm, white, suitable for printers and writing.', 'img/product21.png', 'National Book Store', 100.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 10000, 'Notebook & Paper', 'HCOPY-A4BP-250'),
(22, 'HBW 2000 Ballpen', '0.5mm, Blue', 'img/product22.png', 'National Book Store', 6.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 600, 'Writing Tools', 'HBW-BP-2000'),
(23, 'Faber-Castell Grip Ballpen', '1 pc – Smooth ballpen with soft grip for daily school use.', 'img/product23.png', 'National Book Store', 18.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1800, 'Writing Tools', 'FC-GRIP-BP'),
(24, 'Stabilo Point 88 Fineliner (single)', '1 pc – 0.4mm fine-tip pen for precise writing and underlining notes.', 'img/product24.png', 'National Book Store', 28.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2800, 'Writing Tools', 'STABILO-P88-FINE'),
(25, 'Zebra Zensations Mechanical Pencil', '1 pc – Durable 0.5mm mechanical pencil with textured grip for better control.', 'img/product25.png', 'National Book Store', 40.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 4000, 'Writing Tools', 'ZEBRA-MECH-ZEN'),
(26, 'Dong-A Whiteboard Marker (Black)', '1 pc – Black whiteboard marker with quick-dry and bold ink.', 'img/product26.png', 'National Book Store', 25.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2500, 'Writing Tools', 'DONGA-WBMARKER-BLK'),
(27, 'HBW Correction Pen', '1 pc – Handy correction pen with fine tip for accurate corrections.', 'img/product27.png', 'National Book Store', 18.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1800, 'Writing Tools', 'HBW-CORPEN'),
(28, 'Dong-A Correction Tape', '1 pc – Easy-glide tape applicator for clean and instant corrections.', 'img/product28.png', 'National Book Store', 25.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2500, 'Writing Tools', 'DONGA-CORTAPE'),
(29, 'Maped Eraser + Sharpener Combo', '1 set – Compact and efficient combo of soft eraser and sharpener.', 'img/product29.png', 'National Book Store', 40.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 4000, 'Writing Tools', 'MAPED-ERASHARP'),
(30, 'Corona Intermediate Pad', '1 pad – Classic college-ruled pad with 50 sheets, intermediate size.', 'img/product30.png', 'National Book Store', 15.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1500, 'Notebook & Paper', 'CORONA-INTERPAD'),
(31, 'Graphing Paper (A4)', '20 sheets of white graph paper, 8.5\" x 11\", ideal for Math and Science.', 'img/product31.png', 'National Book Store', 25.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2500, 'Notebook & Paper', 'CORONA-GRAPH-A4'),
(32, 'Viva Softcover Notebook (A5)', '1 pc – Small A5 notebook with soft cover and lined pages, easy to carry.', 'img/product32.png', 'National Book Store', 30.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 3000, 'Notebook & Paper', 'VIVA-NB-A5'),
(33, 'Colored Bond Paper (20 sheets)', '1 pack – 20 sheets, A4 size, assorted colors for creative projects.', 'img/product33.png', 'National Book Store', 20.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2000, 'Notebook & Paper', 'COLORED-BP-20'),
(34, 'Maped Basic Geometry Set', '1 set – Includes compass, triangle, ruler, and protractor for geometry tasks.', 'img/product34.png', 'National Book Store', 60.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 6000, 'Scaler', 'MAPED-GEO-SET'),
(35, 'Deli Flexible Ruler (30cm)', '1 pc – Durable and flexible 30cm ruler; bendable without breaking.', 'img/product35.png', 'National Book Store', 20.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2000, 'Scaler', 'DELI-RULER-30'),
(36, 'Plastic Pencil Case with Divider', '1 pc – Plastic pencil case with divider to keep pens, pencils, and erasers organized.', 'img/product36.png', 'National Book Store', 40.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 4000, 'Filing & Organization', 'GENERIC-PCASE-DIV'),
(37, 'Mesh Zipper File Organizer (A4)', '1 pc – A4-sized mesh zipper bag to secure documents and files.', 'img/product37.png', 'National Book Store', 50.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 5000, 'Filing & Organization', 'GENERIC-MESH-ZIP-A4'),
(38, 'Magnetic Whiteboard Eraser', '1 pc – Compact magnetic eraser for whiteboards; easy to attach and grab.', 'img/product38.png', 'National Book Store', 25.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2500, 'Classroom Tools', 'GENERIC-MAGWB-ERASE'),
(39, 'Plastic Clip Folder (with cover)', '1 pc – A4 plastic folder with slide clip for reports and loose sheets.', 'img/product39.png', 'National Book Store', 24.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2400, 'Filing & Organization', 'GENERIC-CLIPFOLDER'),
(40, 'Puncher', 'Used to create holes in paper for binding or filing.', 'img/product40.png', 'National Book Store', 200.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 20000, 'Filing & Organization', 'JOY-PUNCHER'),
(41, 'Fastener', 'A tool used to bind paper together using fasteners or prongs.', 'img/product41.png', 'National Book Store', 45.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 4500, 'Filing & Organization', 'DELI-PASTENER'),
(42, 'Paper Clip', 'A small bent wire used to hold sheets of paper together.', 'img/product42.png', 'National Book Store', 50.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 5000, 'Filing & Organization', 'STERLING-CLIP'),
(43, 'Oil Pastel', 'Soft, oil-based coloring sticks with vibrant colors.', 'img/product43.png', 'National Book Store', 75.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 7500, 'Art Supplies', 'DONGA-OILPASTEL'),
(44, 'Water Color', 'Pigment-based paints activated with water for painting.', 'img/product44.png', 'National Book Store', 85.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 8500, 'Art Supplies', 'DONGA-WATERCOLOR'),
(45, 'Scissor', 'A cutting tool used for paper, fabric, and other materials.', 'img/product45.png', 'National Book Store', 27.50, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2750, 'General Tools', 'DELI-SCISSOR'),
(46, 'Oslo Paper', 'Medium-weight white paper, usually used for sketches or assignments.', 'img/product46.png', 'National Book Store', 100.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 10000, 'Paper Products', 'ADVANCE-OSLO'),
(47, 'Color Paper', 'Assorted colored sheets used for crafts and projects.', 'img/product47.png', 'National Book Store', 75.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 7500, 'Paper Products', 'ADVANCE-COLOR'),
(48, 'Art Paper', 'Thick, smooth paper used for drawing, painting, and collage.', 'img/product48.png', 'National Book Store', 80.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 8000, 'Art Supplies', 'ADVANCE-ART'),
(49, 'Manila Paper', 'Light brown paper used for charts and visual aids.', 'img/product49.png', 'National Book Store', 75.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 7500, 'Paper Products', 'STERLING-MANILA'),
(50, 'Cartolina', 'Large, colored sheet used for projects, charts, and posters.', 'img/product50.png', 'National Book Store', 50.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 5000, 'Paper Products', 'STERLING-CARTOLINA'),
(51, 'Permanent Marker', '6 pcs - Ink marker that writes permanently on most surfaces.', 'img/product51.png', 'National Book Store', 75.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 7500, 'Writing Tools', 'FC-PERM-MARKER'),
(52, 'Dry Erase Markers', '6 pcs - Marker used on whiteboards that can be erased easily.', 'img/product52.png', 'National Book Store', 75.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 7500, 'Writing Tools', 'HBW-WBMARKER-SET'),
(53, 'Crayons (8 pcs)', '8 pcs - Wax-based coloring sticks for kids and students.', 'img/product53.png', 'National Book Store', 40.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 4000, 'Art Supplies', 'CRAYOLA-CRAY-08'),
(54, 'Crayons (16 pcs)', '16 pcs - Wax-based coloring sticks for kids and students.', 'img/product54.png', 'National Book Store', 60.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 6000, 'Art Supplies', 'CRAYOLA-CRAY-16'),
(55, 'Crayons (24 pcs)', '24 pcs - Wax-based coloring sticks for kids and students.', 'img/product55.png', 'National Book Store', 80.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 8000, 'Art Supplies', 'CRAYOLA-CRAYONS-24'),
(56, 'Composition notebook(8 pcs)', 'Bound notebook with lined pages, ideal for essays, journaling, or note-taking.', 'img/product56.png', 'National Book Store', 17.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1700, 'Notebooks', 'VECO-COMP-NB-8'),
(57, 'Fine liner (6pcs)', 'Pen with a fine tip (0.4mm) for precise writing, outlining, or drawing.', 'img/product57.png', 'National Book Store', 60.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 6000, 'Writing Tools', 'ARTLINE-FINELINER-6'),
(58, 'Index card', 'Small lined cards used for studying, summarizing notes, or organizing thoughts.', 'img/product58.png', 'National Book Store', 30.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 3000, 'Paper Products', 'BB-INDEXCARD'),
(59, 'Dictionary', 'A reference book or digital tool for looking up word definitions and meanings.', 'img/product59.png', 'National Book Store', 250.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 25000, 'Books', 'MW-DICTIONARY'),
(60, 'Zip Pouches', 'Transparent or mesh storage bags with a zipper, used for organizing supplies.', 'img/product60.png', 'National Book Store', 60.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 6000, 'Filing & Organization', 'ALPAKA-ZIP-POUCH'),
(61, 'External hard drive (1TB)', 'Portable data storage device with 1 terabyte capacity for files, videos, and backups.', 'img/product61.png', 'National Book Store', 1000.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 100000, 'Technology', 'SEAGATE-HDD-1TB'),
(62, 'External hard drive (2TB)', 'Large-capacity portable drive for storing large data files, media, and backups.', 'img/product62.png', 'National Book Store', 2000.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 200000, 'Technology', 'SEAGATE-HDD-2TB'),
(63, 'Mouse', 'A handheld input device used to control a computer cursor or pointer.', 'img/product63.png', 'National Book Store', 130.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 13000, 'Technology', 'OCTAGON-MOUSE'),
(64, 'Mouse Pad', 'A smooth surface placed under a computer mouse to improve tracking and comfort.', 'img/product64.png', 'National Book Store', 70.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 7000, 'Technology', 'GENERIC-MOUSEPAD'),
(65, 'Keyboard', 'Input device with keys used for typing text and commands on a computer.', 'img/product65.png', 'National Book Store', 400.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 40000, 'Technology', 'ASUS-KEYBOARD'),
(66, 'Tablet', 'A touchscreen device used for educational apps, drawing, or browsing the internet.', 'img/product66.png', 'National Book Store', 10000.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1000000, 'Technology', 'SAMSUNG-TABLET'),
(67, 'Printer', 'A machine that produces physical copies of digital documents or images.', 'img/product67.png', 'National Book Store', 5000.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 500000, 'Technology', 'EPSON-PRINTER'),
(68, 'Lunch Box', 'A container used to store and carry food for school or work.', 'img/product68.png', 'National Book Store', 120.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 12000, 'Personal Care', 'GENERIC-LUNCHBOX'),
(69, 'Backpack', 'A bag with shoulder straps used to carry books, gadgets, and supplies.', 'img/product69.png', 'National Book Store', 250.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 25000, 'Bags', 'JANSPORT-BACKPACK'),
(70, 'Glue', 'Is an adhesive substance used to bond materials together.', 'img/product70.png', 'National Book Store', 10.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1000, 'Art Supplies', 'HBW-GLUE'),
(71, 'Sketch pad', 'A pad of blank pages used for drawing, sketching, or visual note-taking.', 'img/product71.png', 'National Book Store', 50.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 5000, 'Art Supplies', 'BB-SKETCHPAD'),
(72, 'Fountain pen', 'A pen with a metal nib and refillable ink system, often used for elegant writing.', 'img/product72.png', 'National Book Store', 80.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 8000, 'Writing Tools', 'FC-FOUNTAIN-PEN'),
(73, 'Correction fluid', 'A liquid used to cover up writing errors on paper', 'img/product73.png', 'National Book Store', 25.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 2500, 'Writing Tools', 'BB-CORRECTION-FLUID'),
(74, 'Frixion Pen', 'It allows you to write smoothly and erase cleanly using the built-in rubber eraser tip.', 'img/product74.png', 'National Book Store', 40.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 4000, 'Writing Tools', 'PILOT-FRIXION-PEN'),
(75, 'Eraser', 'A rubber or vinyl tool used to remove pencil marks from paper.', 'img/product75.png', 'National Book Store', 3.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 300, 'Writing Tools', 'HBW-ERASER'),
(76, 'Pencil Sharpener', 'A tool used to restore a pencil tip by shaving away the wood casing.', 'img/product76.png', 'National Book Store', 4.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 400, 'Writing Tools', 'JOY-SHARPENER'),
(77, 'Reference Notebook', 'A durable notebook for long-term academic use, often with labeled sections.', 'img/product77.png', 'National Book Store', 10.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1000, 'Notebooks', 'CORONA-REF-NB'),
(78, 'Envelopes', 'Paper sleeves used to hold and send documents, assignments, or letters.', 'img/product78.png', 'National Book Store', 4.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 400, 'Filing & Organization', 'GENERIC-ENVELOPES'),
(79, 'Scientific Calculator', 'A calculator capable of solving algebraic, trigonometric, and scientific problems.', 'img/product79.png', 'National Book Store', 400.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 40000, 'Math Tools', 'CASIO-SCI-CALC'),
(80, 'Basic Calculator', 'A simple calculator for addition, subtraction, multiplication, and division.', 'img/product80.png', 'National Book Store', 200.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 20000, 'Math Tools', 'CASIO-BASIC-CALC'),
(81, 'Stapler', 'A handheld tool used to fasten sheets of paper together using metal staples.', 'img/product81.png', 'National Book Store', 130.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 13000, 'Filing & Organization', 'JOY-STAPLER'),
(82, 'Graphing Calculator', 'Advanced calculator capable of plotting graphs and solving complex equations.', 'img/product82.png', 'National Book Store', 280.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 28000, 'Math Tools', 'CASIO-GRAPH-CALC'),
(83, 'USB flash drive (16 GB)', 'A portable device for saving and transferring digital files.', 'img/product83.png', 'National Book Store', 250.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 25000, 'Technology', 'SANDISK-USB-16GB'),
(84, 'Headphones', 'Audio device worn on the ears for listening to sound privately.', 'img/product84.png', 'National Book Store', 200.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 20000, 'Technology', 'SONY-HEADPHONES'),
(85, 'Tissues (box)', 'Disposable paper sheets used for hygiene or cleaning.', 'img/product85.png', 'National Book Store', 8.50, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 850, 'Personal Care', 'CLEENE-TISSUE'),
(86, 'Wet wipes (pack)', 'Pre-moistened disposable wipes for cleaning hands or surfaces.', 'img/product86.png', 'National Book Store', 10.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1000, 'Personal Care', 'SANICARE-WETWIPES'),
(87, 'Face mask (10 pcs)', 'A pack of protective masks used to prevent the spread of germs or viruses.', 'img/product87.png', 'National Book Store', 5.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 500, 'Personal Care', 'GENERIC-FACEMASK-10'),
(88, 'Toothbrush + toothpaste', 'Basic oral hygiene set for cleaning and maintaining healthy teeth.', 'img/product88.png', 'National Book Store', 10.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1000, 'Personal Care', 'COLGATE-TOOTHSET'),
(89, 'Popsiccle Stick', 'Wooden sticks used in crafts, science experiments, or classroom activities.', 'img/product89.png', 'National Book Store', 5.50, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 550, 'Art Supplies', 'GENERIC-POPSICLE-STICK'),
(90, 'Glue Gun', 'A heating tool that melts glue sticks for crafting and bonding materials.', 'img/product90.png', 'National Book Store', 210.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 21000, 'Art Supplies', 'INGCO-GLUE-GUN'),
(91, 'Glue Stick', 'A solid glue in stick form used for clean and mess-free paper adhesion.', 'img/product91.png', 'National Book Store', 45.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 4500, 'Art Supplies', 'GENERIC-GLUE-STICK'),
(92, 'Staple remover', 'A small tool used to easily remove staples from paper without damaging the sheets', 'img/product92.png', 'National Book Store', 30.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 3000, 'Filing & Organization', 'JOY-STAPLE-REMOVER'),
(93, 'Thumbtacks (box)', 'Small, sharp pins with flat heads used to attach papers or decorations to corkboards or walls; usually sold in boxes of 50–100 pcs.', 'img/product93.png', 'National Book Store', 30.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 3000, 'Filing & Organization', 'MITSUYA-THUMBTACKS'),
(94, 'Projector', 'A digital device that displays images, videos, or presentations onto a screen or wall; ideal for classroom and group learning.', 'img/product94.png', 'National Book Store', 5000.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 500000, 'Technology', 'ACER-PROJECTOR'),
(95, 'Messenger Bag', 'A bag with a long strap worn over the shoulder, used for carrying school items like notebooks, gadgets, and documents.', 'img/product95.png', 'National Book Store', 120.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 12000, 'Bags', 'HAAPAR-SHOULDER-BAG'),
(96, 'Mechanical pencil', 'A pencil with a refillable lead mechanism; allows consistent writing or sketching without sharpening.', 'img/product96.png', 'National Book Store', 50.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 5000, 'Writing Tools', 'MONGOL-MECH-PENCIL'),
(97, 'Calligraphy pen', 'A special pen with a flat or flexible nib, used for decorative writing and artistic lettering.', 'img/product97.png', 'National Book Store', 40.00, 25, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 4000, 'Art Supplies', 'ARTLINE-CALLIGRAPHY'),
(98, 'Drawing pen', 'A precision pen used for technical drawing or detailed line work in art; often waterproof or archival quality.', 'img/product98.png', 'National Book Store', 15.00, 29, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1500, 'Art Supplies', 'ARTLINE-DRAWING-PEN'),
(99, 'Charcoal pencils (set)', 'A set of pencils made from compressed charcoal; used for sketching, shading, and expressive artwork.', 'img/product99.png', 'National Book Store', 15.00, 30, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 1500, 'Art Supplies', 'FC-CHARCOAL-SET'),
(100, 'Chalk', 'a soft, white, porous, sedimentary rock primarily composed of calcium carbonate.', 'img/product100.png', 'National Book Store', 5.00, 31, '2025-06-23 15:33:26', '2025-06-23 15:33:26', 500, 'Classroom Tools', 'BB-CHALK'),
(102, 'test', 'test', 'img/1750675722_borgir.png', 'Test', 1000.00, 38, '2025-06-23 18:48:42', '2025-06-23 18:49:10', 100000, 'Writing Tools', 'test'),
(103, 'test1', 'test1', 'img/1750677185_muffin final.png', 'test1', 122.00, 38, '2025-06-23 19:13:05', '2025-06-23 19:13:05', 122, 'test', 'testt'),
(105, 'Pilot BP-S Ballpoint Pen', 'Black or Blue, 0.7mm.', 'img/1750743158_pilot ballpoint.jpeg', 'SM Stationery', 2600.00, 38, '2025-06-24 13:32:38', '2025-06-24 13:32:38', 26, 'Writing Tools', 'PILOT-BPS-07');

-- --------------------------------------------------------

--
-- Table structure for table `products_supplier`
--

CREATE TABLE `products_supplier` (
  `p_supplier_id` int(11) NOT NULL,
  `supplier` int(11) NOT NULL,
  `supplier_name` varchar(225) NOT NULL,
  `product` int(11) NOT NULL,
  `quantity_ordered` int(11) NOT NULL,
  `quantity_recieved` int(11) NOT NULL,
  `quantity_remaining` int(11) NOT NULL,
  `status` varchar(225) NOT NULL,
  `batch` varchar(225) DEFAULT 'Pending',
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products_supplier`
--

INSERT INTO `products_supplier` (`p_supplier_id`, `supplier`, `supplier_name`, `product`, `quantity_ordered`, `quantity_recieved`, `quantity_remaining`, `status`, `batch`, `created_by`, `created_at`, `updated_at`, `category`, `sku`, `points`) VALUES
(26, 22, 'National Book Store', 26, 50, 25, 0, 'Incomplete', 'BATCH026', 29, '2025-06-23 15:44:55', '2025-06-23 18:19:32', 'Writing Tools', 'DONGA-WBMARKER-BLK', 2500),
(27, 22, 'National Book Store', 27, 50, 5, 0, 'Incomplete', 'BATCH027', 30, '2025-06-23 15:44:55', '2025-06-23 18:19:37', 'Writing Tools', 'HBW-CORPEN', 1800),
(28, 22, 'National Book Store', 28, 50, 10, 0, 'Incomplete', 'BATCH028', 31, '2025-06-23 15:44:55', '2025-06-23 18:19:41', 'Writing Tools', 'DONGA-CORTAPE', 2500),
(29, 22, 'National Book Store', 29, 50, 24, 0, 'Incomplete', 'BATCH029', 25, '2025-06-23 15:44:55', '2025-06-23 18:19:44', 'Writing Tools', 'MAPED-ERASHARP', 4000),
(30, 22, 'National Book Store', 30, 50, 45, 0, 'Incomplete', 'BATCH030', 29, '2025-06-23 15:44:55', '2025-06-23 18:19:47', 'Notebook & Paper', 'CORONA-INTERPAD', 1500),
(31, 22, 'National Book Store', 31, 50, 1, 0, 'Incomplete', 'BATCH031', 30, '2025-06-23 15:44:55', '2025-06-23 18:19:49', 'Notebook & Paper', 'CORONA-GRAPH-A4', 2500),
(32, 22, 'National Book Store', 32, 50, 10, 0, 'Incomplete', 'BATCH032', 31, '2025-06-23 15:44:55', '2025-06-23 18:19:53', 'Notebook & Paper', 'VIVA-NB-A5', 3000),
(33, 22, 'National Book Store', 33, 50, 32, 0, 'Incomplete', 'BATCH033', 25, '2025-06-23 15:44:55', '2025-06-23 18:19:56', 'Notebook & Paper', 'COLORED-BP-20', 2000),
(34, 22, 'National Book Store', 34, 50, 10, 0, 'Incomplete', 'BATCH034', 29, '2025-06-23 15:44:55', '2025-06-23 18:19:58', 'Scaler', 'MAPED-GEO-SET', 6000),
(35, 22, 'National Book Store', 35, 50, 14, 0, 'Incomplete', 'BATCH035', 30, '2025-06-23 15:44:55', '2025-06-23 18:20:00', 'Scaler', 'DELI-RULER-30', 2000),
(36, 22, 'National Book Store', 36, 50, 9, 0, 'Incomplete', 'BATCH036', 31, '2025-06-23 15:44:55', '2025-06-23 18:20:06', 'Filing & Organization', 'GENERIC-PCASE-DIV', 4000),
(37, 22, 'National Book Store', 37, 50, 45, 0, 'Incomplete', 'BATCH037', 25, '2025-06-23 15:44:55', '2025-06-23 18:20:08', 'Filing & Organization', 'GENERIC-MESH-ZIP-A4', 5000),
(38, 22, 'National Book Store', 38, 50, 40, 0, 'Incomplete', 'BATCH038', 29, '2025-06-23 15:44:55', '2025-06-23 18:20:25', 'Classroom Tools', 'GENERIC-MAGWB-ERASE', 2500),
(39, 22, 'National Book Store', 39, 50, 30, 0, 'Incomplete', 'BATCH039', 30, '2025-06-23 15:44:55', '2025-06-23 18:20:28', 'Filing & Organization', 'GENERIC-CLIPFOLDER', 2400),
(40, 22, 'National Book Store', 40, 50, 0, 0, 'Pending', 'BATCH040', 31, '2025-06-23 15:44:55', '2025-06-23 18:21:09', 'Filing & Organization', 'JOY-PUNCHER', 20000),
(41, 22, 'National Book Store', 41, 50, 0, 0, 'Pending', 'BATCH041', 25, '2025-06-23 15:44:55', '2025-06-23 18:21:14', 'Filing & Organization', 'DELI-PASTENER', 4500),
(42, 22, 'National Book Store', 42, 50, 0, 0, 'Pending', 'BATCH042', 29, '2025-06-23 15:44:55', '2025-06-23 18:21:17', 'Filing & Organization', 'STERLING-CLIP', 5000),
(43, 22, 'National Book Store', 43, 50, 0, 0, 'Pending', 'BATCH043', 30, '2025-06-23 15:44:55', '2025-06-23 18:21:20', 'Art Supplies', 'DONGA-OILPASTEL', 7500),
(44, 22, 'National Book Store', 44, 50, 0, 0, 'Pending', 'BATCH044', 31, '2025-06-23 15:44:55', '2025-06-23 18:21:22', 'Art Supplies', 'DONGA-WATERCOLOR', 8500),
(45, 22, 'National Book Store', 45, 50, 0, 0, 'Pending', 'BATCH045', 25, '2025-06-23 15:44:55', '2025-06-23 18:21:24', 'General Tools', 'DELI-SCISSOR', 2750),
(46, 22, 'National Book Store', 46, 50, 50, 0, 'Completed', 'BATCH046', 29, '2025-06-23 15:44:55', '2025-06-23 15:44:55', 'Paper Products', 'ADVANCE-OSLO', 10000),
(47, 22, 'National Book Store', 47, 50, 50, 0, 'Completed', 'BATCH047', 30, '2025-06-23 15:44:55', '2025-06-23 15:44:55', 'Paper Products', 'ADVANCE-COLOR', 7500),
(48, 22, 'National Book Store', 48, 50, 50, 0, 'Completed', 'BATCH048', 31, '2025-06-23 15:44:55', '2025-06-23 15:44:55', 'Art Supplies', 'ADVANCE-ART', 8000),
(49, 22, 'National Book Store', 49, 50, 50, 0, 'Completed', 'BATCH049', 25, '2025-06-23 15:44:55', '2025-06-23 15:44:55', 'Paper Products', 'STERLING-MANILA', 7500),
(50, 22, 'National Book Store', 50, 50, 50, 0, 'Completed', 'BATCH050', 29, '2025-06-23 15:44:55', '2025-06-23 15:44:55', 'Paper Products', 'STERLING-CARTOLINA', 5000),
(51, 22, 'National Book Store', 51, 50, 50, 0, 'Completed', 'BATCH051', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Writing Tools', 'FC-PERM-MARKER', 7500),
(52, 22, 'National Book Store', 52, 50, 50, 0, 'Completed', 'BATCH052', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Writing Tools', 'HBW-WBMARKER-SET', 7500),
(53, 22, 'National Book Store', 53, 50, 50, 0, 'Completed', 'BATCH053', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'CRAYOLA-CRAY-08', 4000),
(54, 22, 'National Book Store', 54, 50, 50, 0, 'Completed', 'BATCH054', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'CRAYOLA-CRAY-16', 6000),
(55, 22, 'National Book Store', 55, 50, 50, 0, 'Completed', 'BATCH055', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'CRAYOLA-CRAYONS-24', 8000),
(56, 22, 'National Book Store', 56, 50, 50, 0, 'Completed', 'BATCH056', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Notebooks', 'VECO-COMP-NB-8', 1700),
(57, 22, 'National Book Store', 57, 50, 50, 0, 'Completed', 'BATCH057', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Writing Tools', 'ARTLINE-FINELINER-6', 6000),
(58, 22, 'National Book Store', 58, 50, 50, 0, 'Completed', 'BATCH058', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Paper Products', 'BB-INDEXCARD', 3000),
(59, 22, 'National Book Store', 59, 50, 50, 0, 'Completed', 'BATCH059', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Books', 'MW-DICTIONARY', 25000),
(60, 22, 'National Book Store', 60, 50, 50, 0, 'Completed', 'BATCH060', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Filing & Organization', 'ALPAKA-ZIP-POUCH', 6000),
(61, 22, 'National Book Store', 61, 50, 50, 0, 'Completed', 'BATCH061', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'SEAGATE-HDD-1TB', 100000),
(62, 22, 'National Book Store', 62, 50, 50, 0, 'Completed', 'BATCH062', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'SEAGATE-HDD-2TB', 200000),
(63, 22, 'National Book Store', 63, 50, 50, 0, 'Completed', 'BATCH063', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'OCTAGON-MOUSE', 13000),
(64, 22, 'National Book Store', 64, 50, 50, 0, 'Completed', 'BATCH064', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'GENERIC-MOUSEPAD', 7000),
(65, 22, 'National Book Store', 65, 50, 50, 0, 'Completed', 'BATCH065', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'ASUS-KEYBOARD', 40000),
(66, 22, 'National Book Store', 66, 50, 50, 0, 'Completed', 'BATCH066', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'SAMSUNG-TABLET', 1000000),
(67, 22, 'National Book Store', 67, 50, 50, 0, 'Completed', 'BATCH067', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'EPSON-PRINTER', 500000),
(68, 22, 'National Book Store', 68, 50, 50, 0, 'Completed', 'BATCH068', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Personal Care', 'GENERIC-LUNCHBOX', 12000),
(69, 22, 'National Book Store', 69, 50, 50, 0, 'Completed', 'BATCH069', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Bags', 'JANSPORT-BACKPACK', 25000),
(70, 22, 'National Book Store', 70, 50, 50, 0, 'Completed', 'BATCH070', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'HBW-GLUE', 1000),
(71, 22, 'National Book Store', 71, 50, 50, 0, 'Completed', 'BATCH071', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'BB-SKETCHPAD', 5000),
(72, 22, 'National Book Store', 72, 50, 50, 0, 'Completed', 'BATCH072', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Writing Tools', 'FC-FOUNTAIN-PEN', 8000),
(73, 22, 'National Book Store', 73, 50, 50, 0, 'Completed', 'BATCH073', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Writing Tools', 'BB-CORRECTION-FLUID', 2500),
(74, 22, 'National Book Store', 74, 50, 50, 0, 'Completed', 'BATCH074', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Writing Tools', 'PILOT-FRIXION-PEN', 4000),
(75, 22, 'National Book Store', 75, 50, 50, 0, 'Completed', 'BATCH075', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Writing Tools', 'HBW-ERASER', 300),
(76, 22, 'National Book Store', 76, 50, 50, 0, 'Completed', 'BATCH076', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Writing Tools', 'JOY-SHARPENER', 400),
(77, 22, 'National Book Store', 77, 50, 50, 0, 'Completed', 'BATCH077', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Notebooks', 'CORONA-REF-NB', 1000),
(78, 22, 'National Book Store', 78, 50, 50, 0, 'Completed', 'BATCH078', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Filing & Organization', 'GENERIC-ENVELOPES', 400),
(79, 22, 'National Book Store', 79, 50, 50, 0, 'Completed', 'BATCH079', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Math Tools', 'CASIO-SCI-CALC', 40000),
(80, 22, 'National Book Store', 80, 50, 50, 0, 'Completed', 'BATCH080', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Math Tools', 'CASIO-BASIC-CALC', 20000),
(81, 22, 'National Book Store', 81, 50, 50, 0, 'Completed', 'BATCH081', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Filing & Organization', 'JOY-STAPLER', 13000),
(82, 22, 'National Book Store', 82, 50, 50, 0, 'Completed', 'BATCH082', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Math Tools', 'CASIO-GRAPH-CALC', 28000),
(83, 22, 'National Book Store', 83, 50, 50, 0, 'Completed', 'BATCH083', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'SANDISK-USB-16GB', 25000),
(84, 22, 'National Book Store', 84, 50, 50, 0, 'Completed', 'BATCH084', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'SONY-HEADPHONES', 20000),
(85, 22, 'National Book Store', 85, 50, 50, 0, 'Completed', 'BATCH085', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Personal Care', 'CLEENE-TISSUE', 850),
(86, 22, 'National Book Store', 86, 50, 50, 0, 'Completed', 'BATCH086', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Personal Care', 'SANICARE-WETWIPES', 1000),
(87, 22, 'National Book Store', 87, 50, 50, 0, 'Completed', 'BATCH087', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Personal Care', 'GENERIC-FACEMASK-10', 500),
(88, 22, 'National Book Store', 88, 50, 50, 0, 'Completed', 'BATCH088', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Personal Care', 'COLGATE-TOOTHSET', 1000),
(89, 22, 'National Book Store', 89, 50, 50, 0, 'Completed', 'BATCH089', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'GENERIC-POPSICLE-STICK', 550),
(90, 22, 'National Book Store', 90, 50, 50, 0, 'Completed', 'BATCH090', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'INGCO-GLUE-GUN', 21000),
(91, 22, 'National Book Store', 91, 50, 50, 0, 'Completed', 'BATCH091', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'GENERIC-GLUE-STICK', 4500),
(92, 22, 'National Book Store', 92, 50, 50, 0, 'Completed', 'BATCH092', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Filing & Organization', 'JOY-STAPLE-REMOVER', 3000),
(93, 22, 'National Book Store', 93, 50, 50, 0, 'Completed', 'BATCH093', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Filing & Organization', 'MITSUYA-THUMBTACKS', 3000),
(94, 22, 'National Book Store', 94, 50, 50, 0, 'Completed', 'BATCH094', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Technology', 'ACER-PROJECTOR', 500000),
(95, 22, 'National Book Store', 95, 50, 50, 0, 'Completed', 'BATCH095', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Bags', 'HAAPAR-SHOULDER-BAG', 12000),
(96, 22, 'National Book Store', 96, 50, 50, 0, 'Completed', 'BATCH096', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Writing Tools', 'MONGOL-MECH-PENCIL', 5000),
(97, 22, 'National Book Store', 97, 50, 50, 0, 'Completed', 'BATCH097', 25, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'ARTLINE-CALLIGRAPHY', 4000),
(98, 22, 'National Book Store', 98, 50, 50, 0, 'Completed', 'BATCH098', 29, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'ARTLINE-DRAWING-PEN', 1500),
(99, 22, 'National Book Store', 99, 50, 50, 0, 'Completed', 'BATCH099', 30, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Art Supplies', 'FC-CHARCOAL-SET', 1500),
(100, 22, 'National Book Store', 100, 50, 50, 0, 'Completed', 'BATCH100', 31, '2025-06-23 15:47:18', '2025-06-23 15:47:18', 'Classroom Tools', 'BB-CHALK', 500),
(102, 22, 'National Book Store', 55, 50, 0, 50, 'Pending', '919DB8F8', 33, '2025-06-23 18:06:15', '2025-06-23 18:06:15', 'Art Supplies', 'CRAYOLA-CRAYONS-24', 8000),
(103, 22, 'National Book Store', 16, 100, 0, 100, 'Pending', '919DB8F8', 33, '2025-06-23 18:06:15', '2025-06-23 18:06:15', 'Classroom Tools', 'HBW-WHITEBOARD-MINI', 9000),
(104, 22, 'National Book Store', 18, 200, 100, 100, 'Incomplete', '919DB8F8', 33, '2025-06-23 18:06:15', '2025-06-23 18:06:30', 'Notebook & Paper', 'HCOPY-A4BP-20', 800),
(105, 22, 'National Book Store', 62, 20, 0, 20, 'Pending', '919DB8F8', 33, '2025-06-23 18:06:15', '2025-06-23 18:06:15', 'Technology', 'SEAGATE-HDD-2TB', 200000);

-- --------------------------------------------------------

--
-- Table structure for table `reward_orders`
--

CREATE TABLE `reward_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','on delivery','delivered','unsuccessful','declined') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reward_orders`
--

INSERT INTO `reward_orders` (`id`, `user_id`, `product_id`, `address`, `contact`, `order_date`, `status`) VALUES
(1, 910, 69, 'xdad', 'asdsad', '2025-06-23 15:57:09', 'on delivery'),
(2, 910, 48, 'asdad', 'adada', '2025-06-23 16:59:34', 'on delivery'),
(3, 910, 100, 'dqweq', 'qweqwe', '2025-06-23 17:02:36', 'on delivery'),
(4, 910, 100, 'adad', 'adSAd', '2025-06-23 17:03:17', 'on delivery'),
(5, 910, 100, 'asdad', 'adasd', '2025-06-23 17:05:36', 'on delivery'),
(6, 910, 97, 'adad', 'adasd', '2025-06-23 17:06:46', 'declined'),
(7, 913, 48, '1188 Blk 19 Brgy. Pulido', '09922687847', '2025-06-23 18:53:08', 'pending'),
(8, 913, 48, 'dwfdq', '334324', '2025-06-23 19:18:11', 'pending'),
(9, 913, 48, '1188 Blk 19 Brgy. Pulido', '09922687847', '2025-06-24 13:38:08', 'pending'),
(10, 913, 48, '1188 Blk 19 Brgy. Pulido', '09922687847', '2025-06-24 13:42:43', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `stocks_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`stocks_id`, `product_id`, `created_by`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 25, 12, '2025-06-23 15:45:28', '2025-06-23 18:08:39'),
(2, 2, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(3, 3, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(4, 4, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(5, 5, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(6, 6, 29, 3, '2025-06-23 15:45:28', '2025-06-23 18:08:47'),
(7, 7, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(8, 8, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(9, 9, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(10, 10, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(11, 11, 30, 5, '2025-06-23 15:45:28', '2025-06-23 18:08:55'),
(12, 12, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(13, 13, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(14, 14, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(15, 15, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(16, 16, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(17, 17, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(18, 18, 29, 150, '2025-06-23 15:45:28', '2025-06-23 18:06:30'),
(19, 19, 30, 0, '2025-06-23 15:45:28', '2025-06-23 18:22:52'),
(20, 20, 31, 0, '2025-06-23 15:45:28', '2025-06-23 18:22:48'),
(21, 21, 25, 0, '2025-06-23 15:45:28', '2025-06-23 18:22:57'),
(22, 22, 29, 1, '2025-06-23 15:45:28', '2025-06-23 18:09:05'),
(23, 23, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(24, 24, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(25, 25, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(26, 26, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(27, 27, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(28, 28, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(29, 29, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(30, 30, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(31, 31, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(32, 32, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(33, 33, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(34, 34, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(35, 35, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(36, 36, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(37, 37, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(38, 38, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(39, 39, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(40, 40, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(41, 41, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(42, 42, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(43, 43, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(44, 44, 31, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(45, 45, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(46, 46, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(47, 47, 30, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(48, 48, 31, 47, '2025-06-23 15:45:28', '2025-06-24 13:42:43'),
(49, 49, 25, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(50, 50, 29, 50, '2025-06-23 15:45:28', '2025-06-23 15:45:28'),
(51, 51, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(52, 52, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(53, 53, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(54, 54, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(55, 55, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(56, 56, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(57, 57, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(58, 58, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(59, 59, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(60, 60, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(61, 61, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(62, 62, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(63, 63, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(64, 64, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(65, 65, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(66, 66, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(67, 67, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(68, 68, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(69, 69, 25, 49, '2025-06-23 15:47:34', '2025-06-23 15:57:09'),
(70, 70, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(71, 71, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(72, 72, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(73, 73, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(74, 74, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(75, 75, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(76, 76, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(77, 77, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(78, 78, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(79, 79, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(80, 80, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(81, 81, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(82, 82, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(83, 83, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(84, 84, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(85, 85, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(86, 86, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(87, 87, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(88, 88, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(89, 89, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(90, 90, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(91, 91, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(92, 92, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(93, 93, 25, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(94, 94, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(95, 95, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(96, 96, 31, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(97, 97, 25, 49, '2025-06-23 15:47:34', '2025-06-23 17:06:46'),
(98, 98, 29, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(99, 99, 30, 50, '2025-06-23 15:47:34', '2025-06-23 15:47:34'),
(100, 100, 31, 47, '2025-06-23 15:47:34', '2025-06-23 17:05:36'),
(101, 101, 24, 100, '2025-06-23 16:04:21', '2025-06-23 16:04:21');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(225) NOT NULL,
  `supplier_location` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `supplier_name`, `supplier_location`, `email`, `contact_no`, `created_by`, `created_at`, `updated_at`) VALUES
(18, 'PC Express', 'SM City Dasmariñas', 'pcxsmdasmarinas@pcx.com.ph', '0919 075 0614', 32, '2025-04-05 10:30:00', '2025-04-05 10:30:00'),
(19, 'Shoppe Depot', '438 Emilio Aguinaldo Highway, corner Daang Hari, Brgy. Anabu II‑D, Imus, Cavite 4103', 'info@cwhomedepot.com', '0917 875 7831', 26, '2025-03-28 10:30:00', '2025-03-28 10:30:00'),
(20, 'Office Warehouse', '2nd Level, SM City Sta. Rosa, Manila South Highway (Old National Highway), Brgy. Tagapo, Santa Rosa City, Laguna', 'sml@officewarehouse.com.ph', '0917 862 7805', 33, '2025-02-15 10:30:00', '2025-02-15 10:30:00'),
(21, 'Octagon', 'SM City Dasmariñas, Sampaloc I, Dasmariñas City, Cavite', 'ocsdasmarinas@octagon.com.ph', '(8) 526‑7152', 34, '2025-04-22 10:30:00', '2025-04-22 10:30:00'),
(22, 'National Book Store', 'Macaria Business Centre, Governor\'s Drive, Brgy. Mabuhay, Carmona, Cavite', 'onlineorders@nationalbookstore.com.ph', '0998 888 8627', 32, '2025-05-02 10:30:00', '2025-05-02 10:30:00'),
(23, 'Watsons', 'Macaria Business Centre, Governor\'s Drive, Brgy. Mabuhay, Carmona, Cavite', 'watsons.customercare@watsons.com.ph', '(+63)(2) 7919‑999', 26, '2025-03-11 10:30:00', '2025-03-11 10:30:00'),
(24, 'Ace Hardware', 'SM City Dasmariñas', 'acehardware2008@gmail.com', '(+63) 2 8832‑7600', 33, '2025-01-27 10:30:00', '2025-01-27 10:30:00'),
(25, 'Test', 'test', 'test@gmail.com', '123', 38, '2025-06-23 18:47:43', '2025-06-23 18:47:43'),
(28, 'SM Stationery', 'SM Stationery – SM Calamba', 'sm@smstationery.com.ph', '09173244943', 38, '2025-06-24 13:29:27', '2025-06-24 13:29:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(225) NOT NULL,
  `lastname` varchar(225) NOT NULL,
  `birthdate` date NOT NULL,
  `username` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `tier` varchar(225) DEFAULT 'Fresh Ink',
  `points` decimal(10,0) DEFAULT 100,
  `coins` int(10) DEFAULT 0,
  `Role` enum('user','admin') DEFAULT 'user',
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `birthdate`, `username`, `password`, `email`, `tier`, `points`, `coins`, `Role`, `reset_token`, `reset_expires`, `created_at`, `updated_at`) VALUES
(910, 'Gerry', 'Tapuyo', '2003-07-11', 'gerryTaps', '$2y$10$ZWPU.eqNCk9WpwpTanFMsujTrkPrbDonzvRubQycwlvjNjY7tmcrC', 'galler@gmail.com', 'Fresh Ink', 100, 10000, 'user', NULL, '0000-00-00 00:00:00', '2025-06-23 15:54:55', '2025-06-23 17:06:57'),
(911, 'rolly', 'ruiz', '2025-06-03', 'brenty', 'hahaha', 'ruizrollyc@gmail.com', 'Fresh Ink', 999999, 0, 'user', NULL, '2025-06-23 11:53:42', '2025-06-23 17:55:10', '2025-06-23 18:36:07'),
(912, 'rolly', 'ruiz', '2025-06-03', 'rollyruiz', 'hahaha', 'ruizrollyc@gmail.com', 'Fresh Ink', 9999, 0, 'user', NULL, '2025-06-23 11:53:42', '2025-06-23 17:55:19', '2025-06-23 18:35:36'),
(913, 'quency', 'sosa', '2004-06-23', 'quencysosa', '$2y$10$itexpcSAbUvxLCU8vzCiiedsXf.VPfWsqVTwjLuAxCpxDCKcdjpxW', 'quencysosa@gmail.com', 'Fresh Ink', 9999999999, 967999, 'user', NULL, '0000-00-00 00:00:00', '2025-06-23 18:33:51', '2025-06-24 13:42:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_log_ibfk_1` (`admin_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_admins` (`created_by`);

--
-- Indexes for table `products_supplier`
--
ALTER TABLE `products_supplier`
  ADD PRIMARY KEY (`p_supplier_id`),
  ADD KEY `fk_supplies_supplier` (`supplier`),
  ADD KEY `fk_admins_prdct_suplrs` (`created_by`),
  ADD KEY `fk_product` (`product`);

--
-- Indexes for table `reward_orders`
--
ALTER TABLE `reward_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`stocks_id`),
  ADD KEY `fk_admins_stocks` (`created_by`),
  ADD KEY `fk_products_stocks` (`product_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `admin_fk_supplier` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `reset_token` (`reset_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `products_supplier`
--
ALTER TABLE `products_supplier`
  MODIFY `p_supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `reward_orders`
--
ALTER TABLE `reward_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `stocks_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=914;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_admins` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reward_orders`
--
ALTER TABLE `reward_orders`
  ADD CONSTRAINT `reward_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `admin_fk_supplier` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
