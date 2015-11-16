-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas generowania: 16 Lis 2015, 18:43
-- Wersja serwera: 5.6.26
-- Wersja PHP: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `drop`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `assets`
--

CREATE TABLE IF NOT EXISTS `assets` (
  `idAsset` int(11) NOT NULL,
  `idItem` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(511) NOT NULL,
  `imagePath` varchar(255) NOT NULL,
  `orderAsset` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `assets`
--

INSERT INTO `assets` (`idAsset`, `idItem`, `name`, `description`, `imagePath`, `orderAsset`) VALUES
(1, 1, 'Test', 'Lol', 'uploads/564a1574e5897.png', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `idClient` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `urlName` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `clients`
--

INSERT INTO `clients` (`idClient`, `name`, `urlName`) VALUES
(1, 'Client 1', 'client1');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `idItem` int(11) NOT NULL,
  `idProject` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `items`
--

INSERT INTO `items` (`idItem`, `idProject`, `name`, `date`) VALUES
(1, 1, 'Test', '2015-11-16');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `idProject` int(11) NOT NULL,
  `idClient` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `urlName` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `projects`
--

INSERT INTO `projects` (`idProject`, `idClient`, `name`, `urlName`) VALUES
(1, 1, 'Project 1', 'project1');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` enum('admin','client','','') NOT NULL DEFAULT 'client',
  `idClient` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `type`, `idClient`) VALUES
(1, 'admin', '$2y$10$M.d7dEZ9uNkdMIZRhZbOveoGldGpfXPUC.3Dl3gj5rEBa25a0aeY.', 'admin', NULL),
(2, 'client', '$2y$10$veDkd77lYmbXV4xXJ0eKZukqBubNNSGAAS3bpa.fIpIwTVLy/eFYu', 'client', 1);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`idAsset`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`idClient`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`idItem`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`idProject`),
  ADD UNIQUE KEY `idClient_2` (`idClient`),
  ADD KEY `idClient` (`idClient`),
  ADD KEY `idClient_3` (`idClient`),
  ADD KEY `idClient_4` (`idClient`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `assets`
--
ALTER TABLE `assets`
  MODIFY `idAsset` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `clients`
--
ALTER TABLE `clients`
  MODIFY `idClient` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `items`
--
ALTER TABLE `items`
  MODIFY `idItem` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `projects`
--
ALTER TABLE `projects`
  MODIFY `idProject` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
