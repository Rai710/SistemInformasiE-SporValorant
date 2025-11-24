-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 06:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_valo`
--

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `player_name` varchar(255) DEFAULT NULL,
  `in_game_name` varchar(255) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `role` enum('Duelist','Controller','Sentinel','Initiator','Flex') DEFAULT NULL,
  `joined_date` date DEFAULT NULL,
  `left_date` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `team_id`, `player_name`, `in_game_name`, `nationality`, `role`, `joined_date`, `left_date`, `photo`) VALUES
(1, 1, 'Jing Jie Wang', 'Jinggg', 'Singapore', 'Duelist', '2022-01-10', NULL, NULL),
(2, 1, 'Jason Susanto', 'f0rsakeN', 'Indonesia', 'Flex', '2021-09-01', NULL, NULL),
(3, 1, 'Khalish Sudin', 'd4v41', 'Malaysia', 'Initiator', '2020-05-15', NULL, NULL),
(4, 1, 'Ilya Petrov', 'something', 'Russia', 'Duelist', '2023-03-22', NULL, NULL),
(5, 1, 'Wee Jie', 'mindfreak', 'Singapore', 'Sentinel', '2023-01-05', NULL, NULL),
(6, 2, 'Kim Myeong-gwan', 'MaKo', 'Korea', 'Controller', '2022-01-07', NULL, NULL),
(7, 2, 'Cho Min-hyuk', 'Flashback', 'Korea', 'Duelist', '2025-07-07', NULL, NULL),
(8, 2, 'No Ha-jun', 'free1ng', 'Korea', 'Flex', '2024-10-11', NULL, NULL),
(9, 2, 'Song Hyun-min', 'HYUNMIN', 'Korea', 'Duelist', '2024-10-11', NULL, NULL),
(10, 2, 'Kang Ha-bin', 'BeYN', 'Korea', 'Flex', '2025-01-05', NULL, NULL),
(11, 3, 'Ngô Công Anh', 'crazyguy', 'Vietnam', 'Flex', '2025-03-21', NULL, NULL),
(12, 3, 'Cahya Nugraha', 'Monyet', 'Indonesia', 'Controller', '2024-05-20', NULL, NULL),
(13, 3, 'David Monangin', 'xffero', 'Indonesia', 'Flex', '2022-10-09', NULL, NULL),
(14, 3, 'Maksim Batorov', 'Jemkin', 'Russia', 'Duelist', '2023-10-10', NULL, NULL),
(15, 3, 'Bryan Carlos Setiawan', 'Kushy', 'Indonesia', 'Initiator', '2024-10-10', NULL, NULL),
(16, 4, 'Kim Won-tae', 'Karon', 'Korea', 'Controller', '2025-10-24', NULL, NULL),
(17, 4, 'Kim Na-ra', 't3xture', 'Korea', 'Duelist', '2025-10-25', NULL, NULL),
(18, 4, 'Jung Jae-sung', 'Foxy9', 'Korea', 'Flex', '2024-10-24', NULL, NULL),
(19, 4, 'Ha Hyun-cheol', 'Ash', 'Korea', 'Duelist', '2025-03-17', NULL, NULL),
(20, 4, 'Kim Won-tae', 'Karon', 'Korea', 'Controller', '2023-11-23', NULL, NULL),
(21, 5, 'Thanamethk Mahatthananuyut', 'Crws', 'Thailand', 'Controller', '2022-10-25', NULL, NULL),
(22, 5, 'Tanate Teerasawad', 'Killua', 'Thailand', 'Initiator', '2025-02-28', NULL, NULL),
(23, 5, 'Anupong Preamsak', 'thyy', 'Thailand', 'Duelist', '2025-02-24', NULL, NULL),
(24, 5, 'Jittana Nokngam', 'JitBoyS', 'Thailand', 'Sentinel', '2022-10-25', NULL, NULL),
(25, 5, 'Papaphat Sriprapha', 'primmie', 'Thailand', 'Flex', '2024-07-10', NULL, NULL),
(26, 6, 'Kim Gu-taek', 'stax', 'Korea', 'Controller', '2024-06-07', NULL, NULL),
(27, 6, 'Kim Tae-oh', 'Meteor', 'Korea', 'Flex', '2024-10-12', NULL, NULL),
(28, 6, 'Yu Byeong-cheol', 'BuZz', 'Korea', 'Duelist', '2024-10-15', NULL, NULL),
(29, 6, 'Kang Dong-ho', 'DH', 'Korea', 'Flex', '2025-05-15', NULL, NULL),
(30, 6, 'Ham Woo-ju', 'iZu', 'Korea', 'Sentinel', '2023-09-12', NULL, NULL),
(36, 8, 'Jonathan Adiputra', 'natz', 'Indonesia', 'Controller', '2025-09-29', NULL, NULL),
(37, 8, 'Hildegard Arnaldo', 'Shiro', 'Indonesia', 'Flex', '2022-10-27', NULL, NULL),
(38, 8, 'Sheldon Andersen Chandra', 'NcSlasher', 'Indonesia', 'Initiator', '2023-09-13', NULL, NULL),
(39, 8, 'Rizkie Adla Kusuma', 'BerserX', 'Indonesia', 'Flex', '2021-10-06', NULL, NULL),
(40, 8, 'Fikri Zaki Hamdani', 'famouz', 'Indonesia', 'Duelist', '2022-10-27', NULL, NULL),
(41, 9, 'Shota Watanabe', 'SugarZ3ro', 'Japan', 'Controller', '2021-12-01', NULL, NULL),
(42, 9, 'Yuto Mizomori', 'Xdll', 'Japan', 'Initiator', '2024-10-16', NULL, NULL),
(43, 9, 'Hikaru Mizutani', 'CLZ', 'Japan', 'Flex', '2025-09-17', NULL, NULL),
(44, 9, 'Yuma Hashimoto', 'Dep', 'Japan', 'Duelist', '2025-04-13', NULL, NULL),
(45, 9, 'Shota Aoki', 'SyouTa', 'Japan', 'Controller', '2025-05-13', NULL, NULL),
(46, 10, 'Adrian Jiggs Reyes', 'invy', 'Philippines', 'Initiator', '2022-12-18', NULL, NULL),
(47, 10, 'Jessie Cuyco', 'JessieVash', 'Philippines', 'Initiator', '2021-09-09', NULL, NULL),
(48, 10, 'Brheyanne Christ Reyes', 'Wild0reoo', 'Philippines', 'Initiator', '2024-05-23', NULL, NULL),
(49, 10, 'Jeremy Cabrera', 'Jremy', 'Philippines', 'Duelist', '2022-01-22', NULL, NULL),
(50, 10, 'James Goopio', '2GE', 'Philippines', 'Controller', '2024-05-23', NULL, NULL),
(51, 11, 'Go Kyung-won', 'UdoTan', 'Korea', 'Duelist', '2024-10-31', NULL, NULL),
(52, 11, 'Savva Fedorov', 'Kr1stal', 'Russia', 'Initiator', '2024-10-31', NULL, NULL),
(53, 11, 'Federico Evangelista', 'PapiChulo', 'Philippines', 'Controller', '2024-10-31', NULL, NULL),
(54, 11, 'Derrick Yee', 'Deryeon', 'Singapore', 'Duelist', '2024-12-30', NULL, NULL),
(55, 11, 'Mark Musni', 'yokam', 'Philippines', 'Duelist', '2024-10-31', NULL, NULL),
(56, 12, 'Ibuki Seki', 'Meiy', 'Japan', 'Duelist', '2023-11-28', NULL, NULL),
(57, 12, 'Tomonori Okimura', 'SSeeS', 'Japan', 'Controller', '2025-07-16', NULL, NULL),
(58, 12, 'Yu Gwang-hui', 'Akame', 'Korea', 'Flex', '2024-10-15', NULL, NULL),
(59, 12, 'Kim Jin-won', 'Jinboong', 'Korea', 'Sentinel', '2024-10-15', NULL, NULL),
(60, 12, 'Koki Kagami', 'Art', 'Japan', 'Controller', '2024-10-15', NULL, NULL),
(61, 7, 'Kim Mu-bin', 'Francis', 'Korea', 'Flex', '2024-11-16', NULL, NULL),
(62, 7, 'Goo Sang-min', 'Rb', 'Korea', 'Initiator', '2025-06-17', NULL, NULL),
(63, 7, 'Lee Hyuk-kyu', 'Dambi', 'Korea', 'Flex', '2024-11-26', NULL, NULL),
(64, 7, 'Park Sung-hyeon', 'Ivy', 'Korea', 'Sentinel', '2024-11-26', NULL, NULL),
(65, 7, 'Jeonghwan', 'Xross', 'Korea', 'Controller', '2024-02-20', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `fk_players_team` (`team_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `fk_players_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
