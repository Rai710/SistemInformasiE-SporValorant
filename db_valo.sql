-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2025 at 09:28 AM
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
  `event_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(4, 1, 'Shang Yi', 'XcN', 'Singapore', 'Controller', '2022-03-20', NULL, NULL),
(5, 1, 'Wee Jie', 'mindfreak', 'Singapore', 'Sentinel', '2023-01-05', NULL, NULL),
(6, 2, 'Kim Dong-hyeon', 'stax', 'Korea', 'Duelist', '2021-02-10', NULL, NULL),
(7, 2, 'Lee Hyun', 'Zest', 'Korea', 'Controller', '2021-06-15', NULL, NULL),
(8, 2, 'Park Sung-ho', 'Beast', 'Korea', 'Initiator', '2020-12-01', NULL, NULL),
(9, 2, 'Choi Min', 'Nightfall', 'Korea', 'Flex', '2022-01-20', NULL, NULL),
(10, 2, 'Han Jae', 'Shield', 'Korea', 'Sentinel', '2022-05-05', NULL, NULL),
(11, 3, 'Muhammad Fadli', 'Zykoo', 'Indonesia', 'Duelist', '2021-03-12', NULL, NULL),
(12, 3, 'Raka Pratama', 'V1per', 'Indonesia', 'Controller', '2022-02-01', NULL, NULL),
(13, 3, 'Aditya Hendra', 'Phoenix', 'Indonesia', 'Initiator', '2020-11-20', NULL, NULL),
(14, 3, 'Gilang Ramadhan', 'Frost', 'Indonesia', 'Flex', '2021-05-05', NULL, NULL),
(15, 3, 'Andika Prasetyo', 'Guardian', 'Indonesia', 'Sentinel', '2022-07-15', NULL, NULL),
(16, 4, 'Ahmad Faiz', 'Blaze', 'Malaysia', 'Duelist', '2021-04-12', NULL, NULL),
(17, 4, 'Hafiz Rahman', 'Fang', 'Malaysia', 'Controller', '2022-03-01', NULL, NULL),
(18, 4, 'Daniel Lim', 'Ice', 'Malaysia', 'Initiator', '2020-08-20', NULL, NULL),
(19, 4, 'Syafiq Amir', 'Shade', 'Malaysia', 'Flex', '2021-06-10', NULL, NULL),
(20, 4, 'Rizal Hakim', 'Bulwark', 'Malaysia', 'Sentinel', '2022-05-25', NULL, NULL),
(21, 5, 'Chatchai', 'Jade', 'Thailand', 'Duelist', '2021-08-01', NULL, NULL),
(22, 5, 'Kittiphong', 'Shadow', 'Thailand', 'Controller', '2020-12-20', NULL, NULL),
(23, 5, 'Thanapong', 'Bolt', 'Thailand', 'Initiator', '2021-03-15', NULL, NULL),
(24, 5, 'Nattapong', 'Viper', 'Thailand', 'Flex', '2022-01-10', NULL, NULL),
(25, 5, 'Pornsak', 'Aegis', 'Thailand', 'Sentinel', '2022-05-25', NULL, NULL),
(26, 6, 'Kim Jae-hyun', 'Flare', 'Korea', 'Duelist', '2021-02-05', NULL, NULL),
(27, 6, 'Lee Sang', 'Orbit', 'Korea', 'Controller', '2020-09-10', NULL, NULL),
(28, 6, 'Park Min', 'Nova', 'Korea', 'Initiator', '2021-06-12', NULL, NULL),
(29, 6, 'Choi Hyun', 'Pulse', 'Korea', 'Flex', '2022-01-15', NULL, NULL),
(30, 6, 'Han Sung', 'Shield', 'Korea', 'Sentinel', '2022-03-22', NULL, NULL),
(36, 8, 'Dimas', 'Rogue', 'Indonesia', 'Duelist', '2021-01-15', NULL, NULL),
(37, 8, 'Rendy', 'Cinder', 'Indonesia', 'Controller', '2020-10-20', NULL, NULL),
(38, 8, 'Agus', 'Volt', 'Indonesia', 'Initiator', '2021-06-25', NULL, NULL),
(39, 8, 'Fajar', 'Ghost', 'Indonesia', 'Flex', '2022-03-12', NULL, NULL),
(40, 8, 'Bayu', 'Aegis', 'Indonesia', 'Sentinel', '2022-05-30', NULL, NULL),
(41, 9, 'Takumi', 'Ace', 'Japan', 'Duelist', '2021-04-01', NULL, NULL),
(42, 9, 'Hiroshi', 'Zen', 'Japan', 'Controller', '2020-11-10', NULL, NULL),
(43, 9, 'Kenta', 'Blitz', 'Japan', 'Initiator', '2021-02-15', NULL, NULL),
(44, 9, 'Shota', 'Flux', 'Japan', 'Flex', '2022-01-05', NULL, NULL),
(45, 9, 'Ryota', 'Guardian', 'Japan', 'Sentinel', '2022-05-18', NULL, NULL),
(46, 10, 'Pong', 'Shadow', 'Thailand', 'Duelist', '2021-03-01', NULL, NULL),
(47, 10, 'Somchai', 'Vortex', 'Thailand', 'Controller', '2020-12-05', NULL, NULL),
(48, 10, 'Krit', 'Blaze', 'Thailand', 'Initiator', '2021-06-10', NULL, NULL),
(49, 10, 'Anan', 'Pulse', 'Thailand', 'Flex', '2022-02-20', NULL, NULL),
(50, 10, 'Chaiwat', 'Aegis', 'Thailand', 'Sentinel', '2022-06-01', NULL, NULL),
(51, 11, 'Ahmad', 'Volt', 'Malaysia', 'Duelist', '2021-05-01', NULL, NULL),
(52, 11, 'Hafiz', 'Cinder', 'Malaysia', 'Controller', '2020-10-20', NULL, NULL),
(53, 11, 'Daniel', 'Spark', 'Malaysia', 'Initiator', '2021-07-15', NULL, NULL),
(54, 11, 'Syafiq', 'Shade', 'Malaysia', 'Flex', '2022-03-10', NULL, NULL),
(55, 11, 'Rizal', 'Bulwark', 'Malaysia', 'Sentinel', '2022-05-15', NULL, NULL),
(56, 12, 'Takumi', 'Ace', 'Japan', 'Duelist', '2021-02-10', NULL, NULL),
(57, 12, 'Hiroshi', 'Zen', 'Japan', 'Controller', '2020-11-05', NULL, NULL),
(58, 12, 'Kenta', 'Blitz', 'Japan', 'Initiator', '2021-03-12', NULL, NULL),
(59, 12, 'Shota', 'Flux', 'Japan', 'Flex', '2022-01-20', NULL, NULL),
(60, 12, 'Ryota', 'Guardian', 'Japan', 'Sentinel', '2022-05-05', NULL, NULL),
(61, 7, 'Kim Min-jae', 'Phoenix', 'Korea', 'Duelist', '2021-02-15', NULL, NULL),
(62, 7, 'Lee Sang-ho', 'Vortex', 'Korea', 'Controller', '2020-11-10', NULL, NULL),
(63, 7, 'Park Ji-hoon', 'Blaze', 'Korea', 'Initiator', '2021-05-01', NULL, NULL),
(64, 7, 'Choi Sung', 'Pulse', 'Korea', 'Flex', '2022-01-05', NULL, NULL),
(65, 7, 'Han Jae', 'Guardian', 'Korea', 'Sentinel', '2022-03-10', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`team_id`, `team_name`, `country`, `description`, `logo`) VALUES
(1, 'Paper Rex', 'Singapore', 'Paper Rex adalah salah satu tim Valorant paling ikonik di APAC, terkenal dengan gaya bermain yang agresif dan kreatif. Mereka sering tampil memukau di turnamen regional maupun internasional, membawa nama Singapore ke level tinggi dan selalu menjadi favorit fans di setiap kompetisi.', 'https://link.logo/prx.png'),
(2, 'DRX', 'Korea', 'DRX merupakan tim Korea yang dikenal dengan kedisiplinan luar biasa dan strategi matang. Meskipun menghadapi tekanan dari tim-tim APAC lain, DRX selalu mampu menunjukkan permainan solid yang membuat mereka menjadi salah satu tim terkuat di regional, serta memiliki beberapa pemain bintang yang diakui secara internasional.', 'https://link.logo/drx.png'),
(3, 'RRQ', 'Indonesia', 'RRQ Valorant adalah wakil Indonesia yang sudah membuktikan diri di berbagai turnamen APAC. Dengan kombinasi pemain muda berbakat dan veteran berpengalaman, mereka mampu menghadirkan permainan yang cepat, taktis, dan sering memberikan momen-momen menegangkan bagi fans. RRQ terus membangun reputasi sebagai tim yang sulit dikalahkan.', 'https://link.logo/rrq.png'),
(4, 'GENG', 'Malaysia', 'GENG adalah tim Malaysia yang fokus membina pemain muda berbakat untuk bersaing di kancah APAC. Mereka dikenal dengan gaya bermain agresif, kerja sama tim yang solid, dan sering menampilkan kejutan-kejutan menarik di setiap turnamen, menjadikan mereka tim yang selalu diperhitungkan oleh lawan.', 'https://link.logo/geng.png'),
(5, 'Talon Esports', 'Thailand', 'Talon Esports berasal dari Thailand dan telah lama berkompetisi di berbagai turnamen APAC. Tim ini terkenal dengan roster yang seimbang antara pengalaman dan pemain muda, serta strategi yang fleksibel. Talon sering menjadi sorotan karena kemampuan mereka mengubah alur pertandingan dengan taktik brilian.', 'https://link.logo/talon.png'),
(6, 'T1', 'Korea', 'T1 adalah tim Korea yang memiliki reputasi kuat dalam hal strategi dan disiplin. Mereka selalu mempersiapkan diri secara matang sebelum setiap turnamen, sehingga mampu mengontrol permainan lawan dan menunjukkan performa stabil. T1 juga sering menjadi pelopor inovasi strategi baru di kancah APAC.', 'https://link.logo/t1.png'),
(7, 'Nongshim RedForce', 'Korea', 'Nongshim RedForce (NS RedForce) adalah tim Valorant Korea yang agresif dan memiliki pemain muda berbakat. Mereka tampil konsisten di kancah APAC dan selalu menjadi lawan tangguh di setiap turnamen.', NULL),
(8, 'BOOM Esports', 'Indonesia', 'BOOM Esports adalah salah satu tim Indonesia yang dikenal konsisten dan kompetitif di APAC. Mereka menekankan komunikasi tim yang kuat dan strategi matang, sering menunjukkan performa mengejutkan dalam turnamen regional dan selalu memberikan pertandingan seru bagi para penonton.', 'https://link.logo/boom.png'),
(9, 'ZETA Division', 'Japan', 'ZETA Division adalah tim Jepang yang memiliki reputasi tinggi berkat pemain muda berbakat dan kerja sama tim yang solid. Mereka sering tampil di turnamen APAC dengan performa menawan, serta dikenal karena ketekunan dalam latihan dan inovasi strategi yang membuat lawan kesulitan.', 'https://link.logo/zeta.png'),
(10, 'Team Secret', 'Thailand', 'Team Secret dari Thailand dikenal fleksibilitas pemainnya dan gaya bermain kreatif. Tim ini mampu menyesuaikan strategi di tengah pertandingan dan sering menciptakan momen mengejutkan yang membuat mereka menjadi lawan yang tangguh di setiap turnamen APAC.', 'https://link.logo/secret.png'),
(11, 'Global Esports', 'Malaysia', 'Global Esports adalah tim Malaysia yang fokus mengembangkan pemain muda berbakat. Mereka dikenal mampu menghadirkan permainan cepat dan agresif, dengan kerja sama tim yang baik, serta sering memberikan kejutan pada turnamen-turnamen besar di APAC.', 'https://link.logo/global.png'),
(12, 'Detonation FocusMe', 'Japan', 'Detonation FocusMe (DFM) adalah tim Jepang yang konsisten menunjukkan performa tinggi di kancah APAC. Mereka terkenal dengan strategi matang, komunikasi yang efektif, dan kemampuan adaptasi yang baik, menjadikan DFM selalu diperhitungkan dalam setiap pertandingan.', 'https://link.logo/dfm.png');

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
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT;

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
