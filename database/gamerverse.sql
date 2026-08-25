-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2026 at 09:04 AM
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
-- Database: `gamerverse`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `developer` varchar(100) DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `name`, `developer`, `genre`, `description`, `cover_image`, `banner_image`, `release_date`, `is_active`, `created_at`) VALUES
(1, 'CyberQuest 2077', 'Neon Studios', 'Action RPG', 'A futuristic adventure in a cyberpunk world.', NULL, NULL, '2024-01-15', 1, '2026-08-17 13:36:30'),
(2, 'Fantasy Kingdom', 'Dragon Games', 'Fantasy', 'Build your kingdom and defeat mythical creatures.', NULL, NULL, '2023-11-01', 1, '2026-08-17 13:36:30'),
(3, 'Space Warriors', 'Galaxy Devs', 'Sci-Fi', 'Battle across the galaxy in epic space combat.', NULL, NULL, '2024-03-20', 1, '2026-08-17 13:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_equipped` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `user_id`, `item_id`, `purchase_date`, `is_equipped`) VALUES
(1, 2, 7, '2026-08-22 14:45:29', 0);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('weapon','costume','gem') NOT NULL,
  `price` int(11) NOT NULL CHECK (`price` > 0),
  `image_url` varchar(255) DEFAULT NULL,
  `skill_value` varchar(50) DEFAULT NULL,
  `attribute` varchar(50) DEFAULT NULL,
  `special_effect` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) DEFAULT 10,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `game_id`, `name`, `category`, `price`, `image_url`, `skill_value`, `attribute`, `special_effect`, `description`, `stock`, `is_featured`, `created_at`) VALUES
(1, 1, 'Cyber Blade', 'weapon', 500, NULL, '+15 Attack', 'Electric', '10% chance to stun enemies', 'A high-tech blade with electric charge.', 20, 0, '2026-08-17 13:36:30'),
(2, 1, 'Neon Armor', 'costume', 750, NULL, 'DEF +20', 'Neon', 'Glows in dark areas', 'Protective armor with neon lights.', 15, 0, '2026-08-17 13:36:30'),
(3, 1, 'Power Gem', 'gem', 300, NULL, '+10% Damage', 'Radiant', 'Increases weapon damage', 'A glowing gem that boosts power.', 30, 0, '2026-08-17 13:36:30'),
(4, 2, 'Dragon Slayer', 'weapon', 800, NULL, '+25 Attack', 'Fire', 'Deals extra damage to dragons', 'Legendary sword forged in dragon fire.', 10, 0, '2026-08-17 13:36:30'),
(5, 2, 'Royal Robe', 'costume', 600, NULL, 'DEF +15', 'Royal', 'Increases mana regeneration', 'Robes worn by ancient kings.', 25, 0, '2026-08-17 13:36:30'),
(6, 3, 'Laser Rifle', 'weapon', 450, NULL, '+12 Attack', 'Energy', 'Bounces between targets', 'Advanced rifle with energy pulses.', 18, 0, '2026-08-17 13:36:30'),
(7, 3, 'Space Suit', 'costume', 900, NULL, 'DEF +25', 'Space', 'Protects from radiation', 'High-tech suit for space survival.', 11, 0, '2026-08-17 13:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `type` enum('purchase','bonus','adjustment') DEFAULT 'purchase',
  `description` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `item_id`, `amount`, `type`, `description`, `status`, `created_at`) VALUES
(1, 2, 7, 900, 'purchase', 'Purchased: Space Suit (Space Warriors)', 'completed', '2026-08-22 14:45:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `coins` int(11) DEFAULT 1000,
  `avatar` varchar(255) DEFAULT 'default.png',
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `coins`, `avatar`, `bio`, `created_at`, `last_login`, `is_active`) VALUES
(2, 'Dhananjay Singh', 'abc@gmail.com', '$2y$10$PmViL1MsavluetbxLO2n0.meEa62X5/odkzzfIcFCTVb.vAsjhXB.', 'user', 100, 'default.png', NULL, '2026-08-17 14:00:14', '2026-08-25 06:29:26', 1),
(3, 'admin', 'admin@gamerVerse.com', '$2y$10$ZMI1.p.bXLHRM79DCEcr2.Au8YibWayuzOyBQayBkc3niE5ADinVy', 'admin', 1000, 'default.png', NULL, '2026-08-17 14:41:31', '2026-08-25 06:30:38', 1),
(4, 'testuser', 'test@test.gmail.com', '$2y$10$GQre8QdMSWghYlb4Mu9S9uX5LS.S3CJwMyQ/277lE9t8J/3r1Ok6y', 'user', 1000, 'default.png', NULL, '2026-08-25 05:53:41', '0000-00-00 00:00:00', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
