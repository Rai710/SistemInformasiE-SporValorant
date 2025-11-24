-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 05:00 PM
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
-- Database: `db_valo`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_date` varchar(100) DEFAULT NULL,
  `status_event` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `event_date`, `status_event`) VALUES
(1, 'VCT APAC 2025 Stage 1', '2025-03-10', 'finished'),
(2, 'VCT APAC 2025 Stage 2', '2025-06-15', 'finished'),
(3, 'VCT APAC 2026 Stage 1', '2026-03-10', 'upcoming');

-- --------------------------------------------------------

--
-- Table structure for table `match_esports`
--

CREATE TABLE `match_esports` (
  `match_id` int(11) NOT NULL,
  `team1_id` int(11) DEFAULT NULL,
  `team2_id` int(11) DEFAULT NULL,
  `team1_score` int(11) DEFAULT 0,
  `team2_score` int(11) DEFAULT 0,
  `match_date` date DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `stage` enum('Group Stage','Playoffs','Grand Final') DEFAULT 'Group Stage',
  `group_name` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `match_esports`
--

INSERT INTO `match_esports` (`match_id`, `team1_id`, `team2_id`, `team1_score`, `team2_score`, `match_date`, `event_id`, `stage`, `group_name`) VALUES
(1, 12, 11, 0, 2, '2025-03-22', 1, 'Group Stage', NULL),
(2, 6, 9, 2, 1, '2025-03-22', 1, 'Group Stage', NULL),
(3, 2, 1, 2, 1, '2025-03-23', 1, 'Group Stage', NULL),
(4, 5, 10, 2, 1, '2025-03-23', 1, 'Group Stage', NULL),
(5, 4, 8, 0, 2, '2025-03-24', 1, 'Group Stage', NULL),
(6, 7, 3, 2, 1, '2025-03-24', 1, 'Group Stage', NULL),
(7, 10, 9, 0, 2, '2025-03-29', 1, 'Group Stage', NULL),
(8, 6, 7, 2, 0, '2025-03-29', 1, 'Group Stage', NULL),
(9, 4, 1, 2, 1, '2025-03-30', 1, 'Group Stage', NULL),
(10, 2, 12, 2, 0, '2025-03-30', 1, 'Group Stage', NULL),
(11, 5, 3, 0, 2, '2025-03-31', 1, 'Group Stage', NULL),
(12, 8, 11, 2, 0, '2025-03-31', 1, 'Group Stage', NULL),
(13, 4, 12, 2, 0, '2025-04-05', 1, 'Group Stage', NULL),
(14, 3, 9, 2, 0, '2025-04-05', 1, 'Group Stage', NULL),
(15, 1, 8, 1, 2, '2025-04-06', 1, 'Group Stage', NULL),
(16, 5, 7, 2, 1, '2025-04-06', 1, 'Group Stage', NULL),
(17, 2, 11, 2, 0, '2025-04-07', 1, 'Group Stage', NULL),
(18, 6, 10, 2, 1, '2025-04-07', 1, 'Group Stage', NULL),
(19, 7, 10, 2, 0, '2025-04-12', 1, 'Group Stage', NULL),
(20, 2, 8, 1, 2, '2025-04-12', 1, 'Group Stage', NULL),
(21, 6, 3, 1, 2, '2025-04-13', 1, 'Group Stage', NULL),
(22, 4, 11, 2, 0, '2025-04-13', 1, 'Group Stage', NULL),
(23, 5, 9, 0, 2, '2025-04-14', 1, 'Group Stage', NULL),
(24, 12, 1, 1, 2, '2025-04-14', 1, 'Group Stage', NULL),
(25, 1, 11, 2, 0, '2025-04-19', 1, 'Group Stage', NULL),
(26, 6, 5, 0, 2, '2025-04-19', 1, 'Group Stage', NULL),
(27, 2, 4, 2, 1, '2025-04-20', 1, 'Group Stage', NULL),
(28, 7, 9, 2, 1, '2025-04-20', 1, 'Group Stage', NULL),
(29, 12, 8, 0, 2, '2025-04-21', 1, 'Group Stage', NULL),
(30, 3, 10, 1, 2, '2025-04-21', 1, 'Group Stage', NULL),
(31, 5, 4, 1, 2, '2025-04-26', 1, 'Playoffs', NULL),
(32, 2, 6, 2, 0, '2025-04-26', 1, 'Playoffs', NULL),
(33, 8, 4, 0, 2, '2025-04-27', 1, 'Playoffs', NULL),
(34, 3, 2, 2, 1, '2025-04-27', 1, 'Playoffs', NULL),
(35, 7, 5, 1, 2, '2025-04-28', 1, 'Playoffs', NULL),
(36, 1, 6, 2, 1, '2025-04-28', 1, 'Playoffs', NULL),
(37, 5, 2, 0, 2, '2025-04-29', 1, 'Playoffs', NULL),
(38, 1, 8, 2, 0, '2025-04-29', 1, 'Playoffs', NULL),
(39, 4, 3, 2, 0, '2025-05-02', 1, 'Playoffs', NULL),
(40, 2, 1, 0, 2, '2025-05-03', 1, 'Playoffs', NULL),
(41, 3, 1, 3, 2, '2025-05-04', 1, 'Playoffs', NULL),
(42, 4, 3, 1, 3, '2025-05-10', 1, 'Grand Final', NULL),
(43, 8, 5, 0, 2, '2025-07-15', 2, 'Group Stage', NULL),
(44, 7, 10, 2, 1, '2025-07-15', 2, 'Group Stage', NULL),
(45, 6, 1, 0, 2, '2025-07-16', 2, 'Group Stage', NULL),
(46, 2, 4, 0, 2, '2025-07-16', 2, 'Group Stage', NULL),
(47, 3, 11, 0, 2, '2025-07-17', 2, 'Group Stage', NULL),
(48, 9, 12, 1, 2, '2025-07-17', 2, 'Group Stage', NULL),
(49, 2, 7, 2, 1, '2025-07-18', 2, 'Group Stage', NULL),
(50, 8, 6, 0, 2, '2025-07-18', 2, 'Group Stage', NULL),
(51, 11, 10, 1, 2, '2025-07-19', 2, 'Group Stage', NULL),
(52, 1, 12, 2, 0, '2025-07-19', 2, 'Group Stage', NULL),
(53, 5, 9, 2, 0, '2025-07-20', 2, 'Group Stage', NULL),
(54, 3, 4, 2, 1, '2025-07-20', 2, 'Group Stage', NULL),
(55, 4, 11, 2, 1, '2025-07-25', 2, 'Group Stage', NULL),
(56, 5, 12, 1, 2, '2025-07-25', 2, 'Group Stage', NULL),
(57, 6, 9, 2, 0, '2025-07-26', 2, 'Group Stage', NULL),
(58, 3, 7, 2, 1, '2025-07-26', 2, 'Group Stage', NULL),
(59, 2, 10, 2, 0, '2025-07-27', 2, 'Group Stage', NULL),
(60, 8, 1, 0, 2, '2025-07-27', 2, 'Group Stage', NULL),
(61, 8, 12, 0, 2, '2025-08-02', 2, 'Group Stage', NULL),
(62, 3, 2, 0, 2, '2025-08-02', 2, 'Group Stage', NULL),
(63, 7, 11, 2, 0, '2025-08-03', 2, 'Group Stage', NULL),
(64, 1, 9, 0, 2, '2025-08-03', 2, 'Group Stage', NULL),
(65, 4, 7, 0, 2, '2025-08-08', 2, 'Group Stage', NULL),
(66, 5, 1, 0, 2, '2025-08-08', 2, 'Group Stage', NULL),
(67, 2, 11, 2, 0, '2025-08-09', 2, 'Group Stage', NULL),
(68, 8, 9, 1, 2, '2025-08-09', 2, 'Group Stage', NULL),
(69, 3, 10, 2, 1, '2025-08-10', 2, 'Group Stage', NULL),
(70, 6, 12, 2, 0, '2025-08-10', 2, 'Group Stage', NULL),
(71, 5, 7, 2, 0, '2025-08-13', 2, 'Playoffs', NULL),
(72, 3, 6, 1, 2, '2025-08-13', 2, 'Playoffs', NULL),
(73, 2, 5, 1, 2, '2025-08-14', 2, 'Playoffs', NULL),
(74, 1, 6, 2, 0, '2025-08-14', 2, 'Playoffs', NULL),
(75, 7, 12, 2, 0, '2025-08-15', 2, 'Playoffs', NULL),
(76, 3, 4, 2, 1, '2025-08-15', 2, 'Playoffs', NULL),
(77, 6, 7, 2, 0, '2025-08-16', 2, 'Playoffs', NULL),
(78, 2, 3, 0, 2, '2025-08-16', 2, 'Playoffs', NULL),
(79, 5, 1, 0, 2, '2025-08-17', 2, 'Playoffs', NULL),
(80, 6, 3, 1, 2, '2025-08-17', 2, 'Playoffs', NULL),
(81, 5, 3, 2, 3, '2025-08-30', 2, 'Playoffs', NULL),
(82, 1, 3, 3, 1, '2025-08-31', 2, 'Grand Final', NULL),
(83, 4, 10, 2, 0, '2025-08-01', 2, 'Group Stage', NULL),
(84, 5, 6, 2, 0, '2025-08-01', 2, 'Group Stage', NULL);

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
(1, 1, 'Jing Jie Wang', 'Jinggg', 'Singapore', 'Duelist', '2022-01-10', NULL, 'https://owcdn.net/img/67c701558ae34.png'),
(2, 1, 'Jason Susanto', 'f0rsakeN', 'Indonesia', 'Flex', '2021-09-01', NULL, 'https://owcdn.net/img/67c7016159bd7.png'),
(3, 1, 'Khalish Sudin', 'd4v41', 'Malaysia', 'Initiator', '2020-05-15', NULL, 'https://owcdn.net/img/67c7016bb1f6a.png'),
(4, 1, 'Ilya Petrov', 'something', 'Russia', 'Duelist', '2023-03-22', NULL, 'https://owcdn.net/img/67c701797a51d.png'),
(5, 1, 'Patrick Mendoza', 'PatMen', 'Philippines', 'Initiator', '2025-03-04', NULL, 'https://owcdn.net/img/67c6d1461c5cf.png'),
(6, 2, 'Kim Myeong-gwan', 'MaKo', 'Korea', 'Controller', '2022-01-07', NULL, 'https://owcdn.net/img/676b9d064081b.png'),
(7, 2, 'Cho Min-hyuk', 'Flashback', 'Korea', 'Duelist', '2025-07-07', NULL, 'https://owcdn.net/img/676b9cfda9d77.png'),
(8, 2, 'No Ha-jun', 'free1ng', 'Korea', 'Flex', '2024-10-11', NULL, 'https://owcdn.net/img/676b9d177fab9.png'),
(9, 2, 'Song Hyun-min', 'HYUNMIN', 'Korea', 'Duelist', '2024-10-11', NULL, 'https://owcdn.net/img/676b9d1f6a2cd.png'),
(10, 2, 'Kang Ha-bin', 'BeYN', 'Korea', 'Flex', '2025-01-05', NULL, 'https://owcdn.net/img/6360e2fa70458.png'),
(11, 3, 'Ngô Công Anh', 'crazyguy', 'Vietnam', 'Flex', '2025-03-21', NULL, 'https://owcdn.net/img/6821f7746590a.png'),
(12, 3, 'Cahya Nugraha', 'Monyet', 'Indonesia', 'Controller', '2024-05-20', NULL, 'https://owcdn.net/img/6821f747b910a.png'),
(13, 3, 'David Monangin', 'xffero', 'Indonesia', 'Flex', '2022-10-09', NULL, 'https://owcdn.net/img/6821f77c54827.png'),
(14, 3, 'Maksim Batorov', 'Jemkin', 'Russia', 'Duelist', '2023-10-10', NULL, 'https://owcdn.net/img/6821f75962425.png'),
(15, 3, 'Bryan Carlos Setiawan', 'Kushy', 'Indonesia', 'Initiator', '2024-10-10', NULL, 'https://owcdn.net/img/6821f764bd20a.png'),
(16, 4, 'Kim Won-tae', 'Karon', 'Korea', 'Controller', '2025-10-24', NULL, 'https://owcdn.net/img/676ba061b7251.png'),
(17, 4, 'Kim Na-ra', 't3xture', 'Korea', 'Duelist', '2025-10-25', NULL, 'https://owcdn.net/img/676ba04aee5f9.png'),
(18, 4, 'Jung Jae-sung', 'Foxy9', 'Korea', 'Flex', '2024-10-24', NULL, 'https://owcdn.net/img/676ba0170dbab.png'),
(19, 4, 'Byeon Sang-beom', 'Munchkin', 'Korea', 'Flex', '2024-10-15', NULL, 'https://owcdn.net/img/676ba055c7c74.png'),
(20, 4, 'Kim Jong-min', 'Lakia', 'Korea', 'Initiator', '2025-08-04', NULL, 'https://owcdn.net/img/65f66e9636e73.png'),
(21, 5, 'Thanamethk Mahatthananuyut', 'Crws', 'Thailand', 'Controller', '2022-10-25', NULL, 'https://owcdn.net/img/67e2e72005d28.png'),
(22, 5, 'Tanate Teerasawad', 'Killua', 'Thailand', 'Initiator', '2025-02-28', NULL, 'https://owcdn.net/img/67e2e7068098a.png'),
(23, 5, 'Anupong Preamsak', 'thyy', 'Thailand', 'Duelist', '2025-02-24', NULL, 'https://owcdn.net/img/67e2e710e8dba.png'),
(24, 5, 'Jittana Nokngam', 'JitBoyS', 'Thailand', 'Sentinel', '2022-10-25', NULL, 'https://owcdn.net/img/67e2e72ea4fbf.png'),
(25, 5, 'Papaphat Sriprapha', 'primmie', 'Thailand', 'Flex', '2024-07-10', NULL, 'https://owcdn.net/img/67e2e73b188c0.png'),
(26, 6, 'Kim Gu-taek', 'stax', 'Korea', 'Controller', '2024-06-07', NULL, 'https://owcdn.net/img/6757d36ad3001.png'),
(27, 6, 'Kim Tae-oh', 'Meteor', 'Korea', 'Flex', '2024-10-12', NULL, 'https://owcdn.net/img/6757d37596b6f.png'),
(28, 6, 'Yu Byeong-cheol', 'BuZz', 'Korea', 'Duelist', '2024-10-15', NULL, 'https://owcdn.net/img/6757d39881445.png'),
(29, 6, 'Kang Dong-ho', 'DH', 'Korea', 'Flex', '2025-05-15', NULL, 'https://owcdn.net/img/67a4c0f02c6a5.png'),
(30, 6, 'Ham Woo-ju', 'iZu', 'Korea', 'Sentinel', '2023-09-12', NULL, 'https://owcdn.net/img/65cc6f174c154.png'),
(36, 8, 'Jonathan Adiputra', 'natz', 'Indonesia', 'Controller', '2025-09-29', NULL, 'https://owcdn.net/img/6821f7ca302fc.png'),
(37, 8, 'Hildegard Arnaldo', 'Shiro', 'Indonesia', 'Flex', '2022-10-27', NULL, 'https://owcdn.net/img/67fe1380672b6.png'),
(38, 8, 'Sheldon Andersen Chandra', 'NcSlasher', 'Indonesia', 'Initiator', '2023-09-13', NULL, 'https://owcdn.net/img/67fe1388348ff.png'),
(39, 8, 'Rizkie Adla Kusuma', 'BerserX', 'Indonesia', 'Flex', '2021-10-06', NULL, 'https://owcdn.net/img/67fe138f478b7.png'),
(40, 8, 'Fikri Zaki Hamdani', 'famouz', 'Indonesia', 'Duelist', '2022-10-27', NULL, 'https://owcdn.net/img/67fe1378b7193.png'),
(41, 9, 'Shota Watanabe', 'SugarZ3ro', 'Japan', 'Controller', '2021-12-01', NULL, 'https://owcdn.net/img/678a91caf34dd.png'),
(42, 9, 'Yuto Mizomori', 'Xdll', 'Japan', 'Initiator', '2024-10-16', NULL, 'https://owcdn.net/img/678a91e7bd2a2.png'),
(43, 9, 'Hikaru Mizutani', 'CLZ', 'Japan', 'Flex', '2025-09-17', NULL, 'https://owcdn.net/img/678a91d34f18a.png'),
(44, 9, 'Yuma Hashimoto', 'Dep', 'Japan', 'Duelist', '2025-04-13', NULL, 'https://owcdn.net/img/678a91bae6ff9.png'),
(45, 9, 'Shota Aoki', 'SyouTa', 'Japan', 'Controller', '2025-05-13', NULL, 'https://owcdn.net/img/678a91dbae974.png'),
(46, 10, 'Adrian Jiggs Reyes', 'invy', 'Philippines', 'Initiator', '2022-12-18', NULL, 'https://owcdn.net/img/65a8f723b2bdb.png'),
(47, 10, 'Jessie Cuyco', 'JessieVash', 'Philippines', 'Initiator', '2021-09-09', NULL, 'https://owcdn.net/img/65a8f7396035a.png'),
(48, 10, 'Brheyanne Christ Reyes', 'Wild0reoo', 'Philippines', 'Initiator', '2024-05-23', NULL, 'https://owcdn.net/img/677c0f384ed3c.png'),
(49, 10, 'Jeremy Cabrera', 'Jremy', 'Philippines', 'Duelist', '2022-01-22', NULL, 'https://owcdn.net/img/65a8f7423ad9f.png'),
(50, 10, 'James Goopio', '2GE', 'Philippines', 'Controller', '2024-05-23', NULL, 'https://owcdn.net/img/677c0f26abcb5.png'),
(51, 11, 'Go Kyung-won', 'UdoTan', 'Korea', 'Duelist', '2024-10-31', NULL, 'https://owcdn.net/img/677901e39cef9.png'),
(52, 11, 'Savva Fedorov', 'Kr1stal', 'Russia', 'Initiator', '2024-10-31', NULL, 'https://owcdn.net/img/677901eba6d1f.png'),
(53, 11, 'Federico Evangelista', 'PapiChulo', 'Philippines', 'Controller', '2024-10-31', NULL, 'https://owcdn.net/img/677901d0bac2c.png\r\n'),
(54, 11, 'Derrick Yee', 'Deryeon', 'Singapore', 'Duelist', '2024-12-30', NULL, 'https://owcdn.net/img/67f6a409d3bdc.png'),
(55, 11, 'Mark Musni', 'yokam', 'Philippines', 'Duelist', '2024-10-31', NULL, 'https://owcdn.net/img/677901da6202a.png'),
(56, 12, 'Ibuki Seki', 'Meiy', 'Japan', 'Duelist', '2023-11-28', NULL, 'https://owcdn.net/img/674037871e878.png'),
(57, 12, 'Tomonori Okimura', 'SSeeS', 'Japan', 'Controller', '2025-07-16', NULL, 'https://owcdn.net/img/674037e9c4e3c.png'),
(58, 12, 'Yu Gwang-hui', 'Akame', 'Korea', 'Flex', '2024-10-15', NULL, 'https://owcdn.net/img/674037c47365d.png'),
(59, 12, 'Kim Jin-won', 'Jinboong', 'Korea', 'Sentinel', '2024-10-15', NULL, 'https://owcdn.net/img/674037baea139.png'),
(60, 12, 'Koki Kagami', 'Art', 'Japan', 'Controller', '2024-10-15', NULL, 'https://owcdn.net/img/674037af91130.png'),
(61, 7, 'Kim Mu-bin', 'Francis', 'Korea', 'Flex', '2024-11-16', NULL, 'https://owcdn.net/img/679066fc2dea5.png'),
(62, 7, 'Goo Sang-min', 'Rb', 'Korea', 'Initiator', '2025-06-17', NULL, 'https://owcdn.net/img/678247d54091c.png'),
(63, 7, 'Lee Hyuk-kyu', 'Dambi', 'Korea', 'Flex', '2024-11-26', NULL, 'https://owcdn.net/img/679063171f64c.png'),
(64, 7, 'Park Sung-hyeon', 'Ivy', 'Korea', 'Sentinel', '2024-11-26', NULL, 'https://owcdn.net/img/6790670e30a6c.png'),
(65, 7, 'Jeonghwan', 'Xross', 'Korea', 'Controller', '2024-02-20', NULL, 'https://prosettings.net/cdn-cgi/image/dpr=1%2Cf=auto%2Cfit=contain%2Cg=top%2Ch=300%2Cq=99%2Csharpen=1%2Cw=300/wp-content/uploads/xross.png');

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `group_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`team_id`, `team_name`, `country`, `description`, `logo`, `group_name`) VALUES
(1, 'Paper Rex', 'Singapore', 'Paper Rex adalah salah satu tim Valorant paling ikonik di APAC, terkenal dengan gaya bermain yang agresif dan kreatif. Mereka sering tampil memukau di turnamen regional maupun internasional, membawa nama Singapore ke level tinggi dan selalu menjadi favorit fans di setiap kompetisi.', 'https://i.imgur.com/HdiTzhg.webp', 'Group A'),
(2, 'DRX', 'Korea', 'DRX merupakan tim Korea yang dikenal dengan kedisiplinan luar biasa dan strategi matang. Meskipun menghadapi tekanan dari tim-tim APAC lain, DRX selalu mampu menunjukkan permainan solid yang membuat mereka menjadi salah satu tim terkuat di regional, serta memiliki beberapa pemain bintang yang diakui secara internasional.', 'https://owcdn.net/img/63b17abd77fc0.png', 'Group B'),
(3, 'RRQ', 'Indonesia', 'RRQ Valorant adalah wakil Indonesia yang sudah membuktikan diri di berbagai turnamen APAC. Dengan kombinasi pemain muda berbakat dan veteran berpengalaman, mereka mampu menghadirkan permainan yang cepat, taktis, dan sering memberikan momen-momen menegangkan bagi fans. RRQ terus membangun reputasi sebagai tim yang sulit dikalahkan.', 'https://owcdn.net/img/629f17f51e7a3.png', 'Group B'),
(4, 'Gen.G', 'Malaysia', 'Gen.G adalah tim Malaysia yang fokus membina pemain muda berbakat untuk bersaing di kancah APAC. Mereka dikenal dengan gaya bermain agresif, kerja sama tim yang solid, dan sering menampilkan kejutan-kejutan menarik di setiap turnamen, menjadikan mereka tim yang selalu diperhitungkan oleh lawan.', 'https://liquipedia.net/commons/images/thumb/3/34/Gen.G_Esports_2019_full_darkmode.png/600px-Gen.G_Esports_2019_full_darkmode.png', NULL),
(5, 'Talon Esports', 'Thailand', 'Talon Esports berasal dari Thailand dan telah lama berkompetisi di berbagai turnamen APAC. Tim ini terkenal dengan roster yang seimbang antara pengalaman dan pemain muda, serta strategi yang fleksibel. Talon sering menjadi sorotan karena kemampuan mereka mengubah alur pertandingan dengan taktik brilian.', 'https://owcdn.net/img/6226f3d764e03.png', 'Group B'),
(6, 'T1', 'Korea', 'T1 adalah tim Korea yang memiliki reputasi kuat dalam hal strategi dan disiplin. Mereka selalu mempersiapkan diri secara matang sebelum setiap turnamen, sehingga mampu mengontrol permainan lawan dan menunjukkan performa stabil. T1 juga sering menjadi pelopor inovasi strategi baru di kancah APAC.', 'https://owcdn.net/img/62fe0b8f6b084.png', 'Group A'),
(7, 'Nongshim RedForce', 'Korea', 'Nongshim RedForce (NS RedForce) adalah tim Valorant Korea yang agresif dan memiliki pemain muda berbakat. Mereka tampil konsisten di kancah APAC dan selalu menjadi lawan tangguh di setiap turnamen.', 'https://owcdn.net/img/6399bb707aacb.png', 'Group B'),
(8, 'BOOM Esports', 'Indonesia', 'BOOM Esports adalah salah satu tim Indonesia yang dikenal konsisten dan kompetitif di APAC. Mereka menekankan komunikasi tim yang kuat dan strategi matang, sering menunjukkan performa mengejutkan dalam turnamen regional dan selalu memberikan pertandingan seru bagi para penonton.', 'https://owcdn.net/img/629f1bdae82ab.png', 'Group B'),
(9, 'ZETA Division', 'Japan', 'ZETA Division adalah tim Jepang yang memiliki reputasi tinggi berkat pemain muda berbakat dan kerja sama tim yang solid. Mereka sering tampil di turnamen APAC dengan performa menawan, serta dikenal karena ketekunan dalam latihan dan inovasi strategi yang membuat lawan kesulitan.', 'https://liquipedia.net/commons/images/thumb/9/95/ZETA_DIVISION_darkmode.png/600px-ZETA_DIVISION_darkmode.png', 'Group A'),
(10, 'Team Secret', 'Thailand', 'Team Secret dari Thailand dikenal fleksibilitas pemainnya dan gaya bermain kreatif. Tim ini mampu menyesuaikan strategi di tengah pertandingan dan sering menciptakan momen mengejutkan yang membuat mereka menjadi lawan yang tangguh di setiap turnamen APAC.', 'https://liquipedia.net/commons/images/thumb/d/d2/Team_Secret_darkmode.png/600px-Team_Secret_darkmode.png', 'Group A'),
(11, 'Global Esports', 'Malaysia', 'Global Esports adalah tim Malaysia yang fokus mengembangkan pemain muda berbakat. Mereka dikenal mampu menghadirkan permainan cepat dan agresif, dengan kerja sama tim yang baik, serta sering memberikan kejutan pada turnamen-turnamen besar di APAC.', 'https://owcdn.net/img/629f316ddd4dd.png', 'Group A'),
(12, 'Detonation FocusMe', 'Japan', 'Detonation FocusMe (DFM) adalah tim Jepang yang konsisten menunjukkan performa tinggi di kancah APAC. Mereka terkenal dengan strategi matang, komunikasi yang efektif, dan kemampuan adaptasi yang baik, menjadikan DFM selalu diperhitungkan dalam setiap pertandingan.', 'https://liquipedia.net/commons/images/thumb/0/08/DetonatioN_FocusMe_2022_full_darkmode.png/600px-DetonatioN_FocusMe_2022_full_darkmode.png', 'Group B');

-- --------------------------------------------------------

--
-- Table structure for table `team_stats`
--

CREATE TABLE `team_stats` (
  `stat_id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `wins` int(11) DEFAULT 0,
  `losses` int(11) DEFAULT 0,
  `last_updates` timestamp NOT NULL DEFAULT current_timestamp(),
  `event` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@val.id', 'admin', '2025-11-24 03:36:29'),
(2, 'raihan', 'ad@gmail.com', 'admin', '2025-11-24 15:17:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `match_esports`
--
ALTER TABLE `match_esports`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `fk_match_team1` (`team1_id`),
  ADD KEY `fk_match_team2` (`team2_id`),
  ADD KEY `fk_match_event` (`event_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `fk_players_team` (`team_id`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `team_stats`
--
ALTER TABLE `team_stats`
  ADD PRIMARY KEY (`stat_id`),
  ADD KEY `fk_teamstats_team` (`team_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `match_esports`
--
ALTER TABLE `match_esports`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `team_stats`
--
ALTER TABLE `team_stats`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `match_esports`
--
ALTER TABLE `match_esports`
  ADD CONSTRAINT `fk_match_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_match_team1` FOREIGN KEY (`team1_id`) REFERENCES `team` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_match_team2` FOREIGN KEY (`team2_id`) REFERENCES `team` (`team_id`) ON DELETE CASCADE;

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `fk_players_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`) ON DELETE SET NULL;

--
-- Constraints for table `team_stats`
--
ALTER TABLE `team_stats`
  ADD CONSTRAINT `fk_teamstats_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
