-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 06, 2019 at 08:31 PM
-- Server version: 5.7.26-0ubuntu0.18.04.1
-- PHP Version: 7.2.17-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quemer`
--

-- --------------------------------------------------------

--
-- Table structure for table `emailActCodes`
--

CREATE TABLE `emailActCodes` (
  `userID` int(11) NOT NULL,
  `code` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groupmems`
--

CREATE TABLE `groupmems` (
  `userID` int(11) NOT NULL,
  `groupID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `groupmems`
--

INSERT INTO `groupmems` (`userID`, `groupID`) VALUES
(1, 2),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `groupID` int(11) NOT NULL,
  `groupName` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `adminID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`groupID`, `groupName`, `adminID`) VALUES
(2, 'Bulviakasiai', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `noteId` int(11) NOT NULL,
  `individ` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `title` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`noteId`, `individ`, `type`, `title`, `content`, `owner_id`, `date`) VALUES
(7, 3, 1, 'Reklaminei kompanijai', '', 1, '2019-05-02 01:02:49'),
(9, 1, 0, 'My new note', 'Hey, I just create my first note. :D Easily edit it.', 2, '2019-05-03 14:52:53'),
(10, 2, 1, 'My first task list', '', 2, '2019-05-03 14:53:23'),
(11, 3, 3, 'My first group only tasklist', '', 2, '2019-05-03 14:54:28');

-- --------------------------------------------------------

--
-- Table structure for table `peoplerelation`
--

CREATE TABLE `peoplerelation` (
  `id` int(11) NOT NULL,
  `areFriends` int(11) NOT NULL,
  `userid1` int(11) NOT NULL,
  `userid2` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `peoplerelation`
--

INSERT INTO `peoplerelation` (`id`, `areFriends`, `userid1`, `userid2`, `date`) VALUES
(3, 1, 2, 1, '2019-05-03');

-- --------------------------------------------------------

--
-- Table structure for table `sharedcontent`
--

CREATE TABLE `sharedcontent` (
  `contentID` int(11) NOT NULL,
  `tbName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `individ` int(11) NOT NULL,
  `groupID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sharedcontent`
--

INSERT INTO `sharedcontent` (`contentID`, `tbName`, `individ`, `groupID`) VALUES
(10, 'notes', 1, 2),
(11, 'notes', 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `todoId` int(11) NOT NULL,
  `noteAssignId` int(11) NOT NULL,
  `task` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `done` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`todoId`, `noteAssignId`, `task`, `done`) VALUES
(9, 6, 'Sukuriau', 1),
(10, 6, 'shared', 1),
(11, 6, 'taskus', 0),
(12, 6, 'pamaÄiau I.Å ', 1),
(13, 7, 'Padaryti screenshotus', 1),
(14, 7, 'Ä®kelti postÄ… apie paleidimÄ…', 1),
(15, 7, 'Padaryti video, demonstruojanti programos funkcijas', 1),
(16, 7, 'Akcentuoti cross platformÄ…, nemokamumÄ…', 1),
(18, 7, 'Parodyti, kad galima prisegti prie darbalaukio', 1),
(19, 10, 'This', 0),
(20, 10, 'is', 0),
(21, 10, 'first', 0),
(22, 10, 'tasks', 0),
(23, 10, 'Easy editing', 0),
(24, 11, 'my', 0),
(25, 11, 'tasks', 0),
(26, 10, 'Im his group member and can edit his tasks', 0),
(27, 7, 'Perdaryti reklaminÄ¯ video, kad nebÅ«tÅ³ juodÅ³ linijÅ³ iÅ¡ Å¡onÅ³', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No surname',
  `password` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `activated` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `password`, `salt`, `email`, `activated`) VALUES
(1, 'Ibragim', 'Å abanoviÄ', '09805000690b1e41fc76d0ffa06760be1760cb79815da71e5d8fea4119894641', 'd8db72c6480658517bc6d0b60081b495607b27d0b873f75063344239968fdabf', 'ibragimsabanovic91@gmail.com', 1),
(2, 'Dadan', 'Dadanski', '705588cbc1b3d09cd003e18eebd4e9046b22063bd11f1d2e9b4cc0b5eef7730c', '9bdf1e2bdfc0527d8d32497a56c6ed2b3c03d143fa78d7be1223106543fac75f', 'ibragimsabanovic@yahoo.com', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`groupID`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`noteId`);

--
-- Indexes for table `peoplerelation`
--
ALTER TABLE `peoplerelation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`todoId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `groupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `noteId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `peoplerelation`
--
ALTER TABLE `peoplerelation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `todoId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
