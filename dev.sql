-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 29 Wrz 2020, 21:04
-- Wersja serwera: 10.4.8-MariaDB
-- Wersja PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `dev`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `acceses`
--

CREATE TABLE `acceses` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `acces_edit` tinyint(1) NOT NULL DEFAULT 0,
  `buildings_view` tinyint(1) NOT NULL DEFAULT 1,
  `buildings_edit` tinyint(1) NOT NULL DEFAULT 0,
  `logs_view` tinyint(1) NOT NULL DEFAULT 0,
  `logs_edit` tinyint(1) NOT NULL DEFAULT 0,
  `rooms_view` tinyint(1) NOT NULL DEFAULT 1,
  `rooms_edit` tinyint(1) NOT NULL DEFAULT 0,
  `reservations_acces` tinyint(1) NOT NULL DEFAULT 0,
  `reservations_confirm` tinyint(1) NOT NULL DEFAULT 0,
  `reservations_edit` tinyint(1) NOT NULL DEFAULT 0,
  `users_edit` tinyint(1) NOT NULL DEFAULT 0,
  `statistics_view` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `acceses`
--

INSERT INTO `acceses` (`id`, `name`, `acces_edit`, `buildings_view`, `buildings_edit`, `logs_view`, `logs_edit`, `rooms_view`, `rooms_edit`, `reservations_acces`, `reservations_confirm`, `reservations_edit`, `users_edit`, `statistics_view`) VALUES
(1, 'admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(3, 'demo', 1, 1, 0, 0, 0, 1, 0, 1, 0, 1, 0, 0),
(4, 'test', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `country` tinytext NOT NULL,
  `town` tinytext NOT NULL,
  `postal_code` tinytext NOT NULL,
  `street` tinytext NOT NULL,
  `number` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `addresses`
--

INSERT INTO `addresses` (`id`, `country`, `town`, `postal_code`, `street`, `number`) VALUES
(1, 'Poland', 'Bydgoszcz', '85-791', 'Kaliskiego', '41'),
(2, 'Poland', 'Kowal', '87-820', 'Grabkowska', '7');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `buildings`
--

CREATE TABLE `buildings` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `rooms_count` int(11) NOT NULL,
  `address_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `buildings`
--

INSERT INTO `buildings` (`id`, `name`, `rooms_count`, `address_id`) VALUES
(2, 'Budynek A', 10, 1),
(3, 'Budynek B', 35, 2),
(4, 'Budynek C', 36, 1),
(6, 'Budynek D', 36, 1),
(7, 'Budynek E', 37, 1),
(8, 'Budynek F', 38, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `building_id` int(1) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `logs`
--

INSERT INTO `logs` (`id`, `message`, `user_id`, `reservation_id`, `building_id`, `room_id`, `created_at`) VALUES
(6, 'testowy log', 4, NULL, NULL, NULL, '2020-08-12 12:10:24'),
(7, 'testowy log tworzenie pokoju', 4, NULL, NULL, 2, '2020-08-12 12:14:28'),
(8, 'testowy log tworzenie pokoju', 4, NULL, NULL, 2, '2020-08-12 12:39:31'),
(9, 'User weronika1212@gmail.com has been registered', 8, NULL, NULL, NULL, '2020-08-14 20:40:11'),
(10, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-14 20:53:06'),
(11, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 16:52:52'),
(12, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 17:35:02'),
(13, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 17:38:24'),
(14, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 21:00:00'),
(15, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 21:01:33'),
(16, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:11:33'),
(17, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:12:33'),
(18, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:13:18'),
(19, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:13:31'),
(20, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:13:35'),
(21, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:13:36'),
(22, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:13:38'),
(23, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:15:11'),
(24, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:15:26'),
(25, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:15:35'),
(26, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:15:48'),
(27, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:16:02'),
(28, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:17:46'),
(29, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:25:29'),
(30, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:35:36'),
(31, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-16 22:37:14'),
(32, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 17:56:37'),
(33, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 18:05:57'),
(34, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 18:06:14'),
(35, 'Account user szymonrykalaDemo@gmail.com was activated', 6, NULL, NULL, NULL, '2020-08-22 18:35:54'),
(36, 'Account user szymonrykalaDemo@gmail.com was activated', 6, NULL, NULL, NULL, '2020-08-22 18:52:59'),
(37, 'Account user szymonrykalaDemo@gmail.com was activated', 6, NULL, NULL, NULL, '2020-08-22 18:55:18'),
(38, 'Account user szymonrykalaDemo@gmail.com was activated', 6, NULL, NULL, NULL, '2020-08-22 18:58:23'),
(39, 'Account user szymonrykalaDemo@gmail.com was activated', 6, NULL, NULL, NULL, '2020-08-22 19:00:49'),
(40, 'Account user szymonrykalaDemo@gmail.com was activated', 6, NULL, NULL, NULL, '2020-08-22 19:01:42'),
(41, 'Account user weronika1212@gmail.com was activated', 8, NULL, NULL, NULL, '2020-08-22 19:09:40'),
(42, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 19:10:19'),
(43, 'Account user weronika1212@gmail.com was activated', 8, NULL, NULL, NULL, '2020-08-22 19:10:41'),
(44, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 19:10:46'),
(45, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 20:08:44'),
(46, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 20:59:28'),
(47, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:02:16'),
(48, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:03:47'),
(49, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:06:18'),
(50, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:06:23'),
(51, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:06:24'),
(52, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 21:11:18'),
(53, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:15:12'),
(54, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:15:34'),
(55, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:15:51'),
(56, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:16:53'),
(57, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:19:22'),
(58, 'User weronika1212@gmail.com updated name', 8, NULL, NULL, NULL, '2020-08-22 21:25:51'),
(59, 'User weronika1212@gmail.com updated name,password', 8, NULL, NULL, NULL, '2020-08-22 21:27:18'),
(60, 'User weronika1212@gmail.com veryfing failed', 8, NULL, NULL, NULL, '2020-08-22 21:27:25'),
(61, 'User weronika1212@gmail.com veryfing failed', 8, NULL, NULL, NULL, '2020-08-22 21:27:32'),
(62, 'User weronika1212@gmail.com veryfing failed', 8, NULL, NULL, NULL, '2020-08-22 21:28:20'),
(63, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 21:28:28'),
(64, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 21:28:36'),
(65, 'User weronika1212@gmail.com veryfing failed', 8, NULL, NULL, NULL, '2020-08-22 21:30:31'),
(66, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 21:33:32'),
(67, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-24 21:08:39'),
(68, 'User weronika1212@gmail.com updated name', 8, NULL, NULL, NULL, '2020-08-24 21:12:05'),
(69, 'User weronika1212@gmail.com updated name', 8, NULL, NULL, NULL, '2020-08-24 21:26:28'),
(70, 'User weronika1212@gmail.com updated name', 8, NULL, NULL, NULL, '2020-08-24 21:26:49'),
(71, 'User weronika1212@gmail.com (id=8) updated user (id=4) data: name', 8, NULL, NULL, NULL, '2020-08-24 21:33:57'),
(72, 'User weronika1212@gmail.com deleted weronika1212@gmail.com', 8, NULL, NULL, NULL, '2020-08-24 21:50:56'),
(73, 'User weronika1212@gmail.com deleted szymonrykalaAdmin@gmail.com', 4, NULL, NULL, NULL, '2020-08-24 21:52:44'),
(74, 'User weronika1212@gmail.com deleted reservation with id=7', 8, 7, NULL, NULL, '2020-08-25 20:33:22'),
(75, 'User weronika1212@gmail.com created reservation', 8, 12, 2, 4, '2020-08-25 21:18:03'),
(79, 'User weronika1212@gmail.com deleted reservation id=9', 8, 9, NULL, NULL, '2020-08-25 21:44:06'),
(80, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-27 21:46:08'),
(88, 'User weronika1212@gmail.com deleted reservation', 8, 14, NULL, NULL, '2020-08-28 00:36:44'),
(89, 'User weronika1212@gmail.com hard deleted reservation', 8, 14, NULL, NULL, '2020-08-28 00:37:07'),
(90, 'User weronika1212@gmail.com veryfing failed', 8, NULL, NULL, NULL, '2020-08-30 18:19:54'),
(91, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-30 18:20:11'),
(245, 'User weronika1212@gmail.com veryfing failed', 8, NULL, NULL, NULL, '2020-09-09 16:48:14'),
(246, 'User weronika1212@gmail.com (id=8) updated user (id=8) data: surname', 8, NULL, NULL, NULL, '2020-09-09 16:48:17'),
(247, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-09-09 17:08:15'),
(248, 'User weronika1212@gmail.com deleted acces id=3', 8, NULL, NULL, NULL, '2020-09-09 19:18:33'),
(249, 'User weronika1212@gmail.com created new acces class &#39;test&#39; ', 8, NULL, NULL, NULL, '2020-09-09 19:30:13'),
(250, 'User weronika1212@gmail.com updated', 8, NULL, NULL, NULL, '2020-09-09 20:15:39'),
(256, 'teseting building C', 8, NULL, 4, NULL, '2020-09-09 20:16:46');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `title` tinytext NOT NULL,
  `subtitle` text NOT NULL,
  `room_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `confirming_user_id` int(11) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `reservations`
--

INSERT INTO `reservations` (`id`, `title`, `subtitle`, `room_id`, `building_id`, `user_id`, `start_time`, `end_time`, `date`, `created_at`, `updated_at`, `confirmed`, `confirming_user_id`, `confirmed_at`, `deleted`) VALUES
(3, 'rezerwacja próbna', 'test - próbna rezerwacja', 2, 2, 4, '12:00:00', '13:30:00', '2020-09-12', '2020-08-12 16:24:44', '2020-08-12 16:24:44', 0, NULL, NULL, 0),
(4, 'rezerwacja próbna', 'test - próbna rezerwacja', 2, 2, 8, '12:00:00', '13:30:00', '2020-10-12', '2020-08-24 22:11:19', '2020-08-24 22:11:19', 0, NULL, NULL, 0),
(6, 'rezerwacja próbna', 'test - próbna rezerwacja', 2, 2, 6, '12:00:00', '13:30:00', '2020-11-12', '2020-08-12 16:33:03', '2020-08-12 16:33:03', 0, NULL, NULL, 0),
(7, 'rezerwacja próbna', 'test - próbna rezerwacja', 3, 3, 8, '12:00:00', '13:30:00', '2020-08-12', '2020-08-24 22:11:30', '2020-08-24 22:11:30', 0, NULL, NULL, 0),
(9, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 3, 3, 6, '11:00:00', '11:20:00', '2020-08-28', '2020-08-25 21:44:06', '2020-08-25 21:44:06', 0, NULL, NULL, 0),
(10, 'rezerwacja próbna', 'test - próbna rezerwacja', 3, 3, 8, '12:00:00', '13:30:00', '2020-08-14', '2020-08-24 22:11:26', '2020-08-24 22:11:26', 0, NULL, NULL, 0),
(11, 'rezerwacja próbna', 'update - próbna rezerwacja', 3, 3, 6, '12:00:00', '13:30:00', '2020-08-15', '2020-08-12 16:40:20', '2020-08-12 16:40:20', 0, NULL, NULL, 0),
(12, 'rezerwacja v1', 'podtytuł rezerwacji, opis', 4, 2, 8, '10:00:00', '11:15:00', '2020-08-11', '2020-08-25 21:27:20', '2020-08-25 21:27:20', 0, NULL, NULL, 0),
(13, 'rezerwacja v1', 'podtytuł rezerwacji, opis', 4, 2, 8, '10:00:00', '11:15:00', '2020-08-28', '2020-08-25 21:28:04', '2020-08-25 21:28:04', 0, NULL, NULL, 0),
(14, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 4, 2, 8, '11:00:00', '11:20:00', '2020-08-27', '2020-08-28 00:37:49', '2020-08-28 00:37:49', 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `building_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `seats_count` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `equipment` text NOT NULL,
  `blockade` tinyint(1) NOT NULL DEFAULT 1,
  `state` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `building_id`, `room_type_id`, `seats_count`, `floor`, `equipment`, `blockade`, `state`) VALUES
(2, 'B001', 3, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(3, 'B201', 3, 5, 60, 2, 'rzutnik,kreda,tablica', 1, 0),
(4, 'B101', 3, 2, 60, 1, 'rzutnik,kreda,tablica', 0, 0),
(5, 'A001', 2, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(6, 'A201', 2, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0),
(7, 'A101', 2, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0),
(8, 'C001', 4, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(9, 'C201', 2, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0),
(10, 'C101', 2, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0),
(11, 'D001', 6, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(12, 'D101', 6, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0),
(13, 'D201', 6, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0),
(14, 'E001', 7, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(15, 'E101', 7, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0),
(16, 'E201', 7, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `room_types`
--

INSERT INTO `room_types` (`id`, `name`) VALUES
(1, 'laboratory'),
(2, 'sala wykładowa'),
(5, 'aula');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `acces_id` int(11) NOT NULL DEFAULT 1,
  `name` tinytext NOT NULL,
  `surname` tinytext NOT NULL,
  `password` text NOT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` tinytext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `img_url` tinytext NOT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT 0,
  `login_fails` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `action_key` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `acces_id`, `name`, `surname`, `password`, `last_login`, `email`, `updated_at`, `img_url`, `activated`, `login_fails`, `created_at`, `action_key`) VALUES
(4, 1, 'Szymon', 'Rykała', '$2y$12$53Gv4712tmvJTTuyPniUeuSipJarTnb5Ck9tTQ4uVo/j2Jn5FeNqK', '2020-08-24 21:33:57', 'szymonrykalaAdmin@gmail.com', '2020-08-24 21:33:57', 'http://localhost:8080/img/users/default.jpg', 0, 0, '2020-08-24 21:33:57', 'xxxyyy111222'),
(6, 1, 'Szymon', 'Rykała', '$2y$12$4mBLGUlJa6qqtUZZR6wpwu6sMXW1aPBuVPXQvNBf8zjACrd3MQlyq', '2020-08-22 19:01:42', 'szymonrykalaDemo@gmail.com', '2020-08-22 19:01:42', 'http://localhost:8080/img/users/default.jpg', 1, 0, '2020-08-22 19:01:42', '1'),
(8, 3, 'Weronika', 'Urbańska|T', '$2y$12$mLMGKbiLWDMoOxkeyxnX6OEguoUFS.WyAAFOxA1GL14ESMp.MCxWi', '2020-09-09 17:08:15', 'weronika1212@gmail.com', '2020-09-09 17:08:15', 'http://localhost:8080/img/users/default.jpg', 1, 0, '2020-09-09 17:08:15', '1'),
(9, 1, 'imięTest', 'nazwizkoTest', '$2y$12$7cR1aElaT5pNyMxldZzW5.ogA77Uira62w4DWnJac3FkcK30Seyum', '2020-09-08 22:57:16', 'test@gmail.com', '2020-09-08 22:57:16', 'http://localhost:8080/img/users/default.jpg', 1, 0, '2020-09-08 22:57:16', 'lHnWEnB2Vg8waORt6exxGUXxqmdRbsMZ0IMnc1OuJx/vBRSxr8isege5xgPV16T/BF02lD4dNqJUo7Zu');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `acceses`
--
ALTER TABLE `acceses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`) USING HASH;

--
-- Indeksy dla tabeli `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buildings_to_addresses` (`address_id`);

--
-- Indeksy dla tabeli `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_to_buildings` (`building_id`),
  ADD KEY `reservations_to_rooms` (`room_id`),
  ADD KEY `reservations_to_users` (`user_id`),
  ADD KEY `reservations_to_users_confirm` (`confirming_user_id`);

--
-- Indeksy dla tabeli `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rooms_to_buildings` (`building_id`),
  ADD KEY `rooms_to_room_types` (`room_type_id`);

--
-- Indeksy dla tabeli `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`) USING HASH;

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`) USING HASH,
  ADD KEY `users_to_acceses` (`acces_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `acceses`
--
ALTER TABLE `acceses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT dla tabeli `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT dla tabeli `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT dla tabeli `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT dla tabeli `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `buildings`
--
ALTER TABLE `buildings`
  ADD CONSTRAINT `buildings_to_addresses` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_to_buildings` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_to_rooms` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_to_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_to_users_confirm` FOREIGN KEY (`confirming_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_to_buildings` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rooms_to_room_types` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_to_acceses` FOREIGN KEY (`acces_id`) REFERENCES `acceses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
