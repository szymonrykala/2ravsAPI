-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 19 Paź 2020, 16:01
-- Wersja serwera: 10.4.14-MariaDB
-- Wersja PHP: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `ravs_dev`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `accesses`
--

CREATE TABLE `accesses` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `rfid_action` tinyint(1) NOT NULL DEFAULT 0,
  `access_edit` tinyint(1) NOT NULL DEFAULT 0,
  `buildings_view` tinyint(1) NOT NULL DEFAULT 1,
  `buildings_edit` tinyint(1) NOT NULL DEFAULT 0,
  `logs_view` tinyint(1) NOT NULL DEFAULT 0,
  `logs_edit` tinyint(1) NOT NULL DEFAULT 0,
  `rooms_view` tinyint(1) NOT NULL DEFAULT 1,
  `rooms_edit` tinyint(1) NOT NULL DEFAULT 0,
  `reservations_access` tinyint(1) NOT NULL DEFAULT 0,
  `reservations_confirm` tinyint(1) NOT NULL DEFAULT 0,
  `reservations_edit` tinyint(1) NOT NULL DEFAULT 0,
  `users_edit` tinyint(1) NOT NULL DEFAULT 0,
  `statistics_view` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `accesses`
--

INSERT INTO `accesses` (`id`, `name`, `rfid_action`, `access_edit`, `buildings_view`, `buildings_edit`, `logs_view`, `logs_edit`, `rooms_view`, `rooms_edit`, `reservations_access`, `reservations_confirm`, `reservations_edit`, `users_edit`, `statistics_view`) VALUES
(1, 'admin', 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(3, 'demo', 1, 0, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0),
(4, 'test', 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);

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
  `rooms_count` int(11) NOT NULL DEFAULT 0,
  `address_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `buildings`
--

INSERT INTO `buildings` (`id`, `name`, `rooms_count`, `address_id`) VALUES
(2, 'Budynek A', 5, 1),
(3, 'Budynek B', 3, 2),
(4, 'Budynek C', 1, 1),
(6, 'Budynek D', 3, 2),
(7, 'Budynek E', 3, 1),
(8, 'Budynek F', 0, 2),
(9, 'Budynek testowy', 0, 1);

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
(34, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 18:06:14'),
(35, 'Account user szymonrykalaDemo@gmail.com was activated', 6, NULL, NULL, NULL, '2020-08-22 18:35:54'),
(39, 'Account user szymonrykalaDemo@gmail.com was activated', 6, NULL, NULL, NULL, '2020-08-22 19:00:49'),
(40, 'Account user szymonrykalaDemo@gmail.com was activated', 6, NULL, NULL, NULL, '2020-08-22 19:01:42'),
(41, 'Account user weronika1212@gmail.com was activated', 8, NULL, NULL, NULL, '2020-08-22 19:09:40'),
(42, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 19:10:19'),
(43, 'Account user weronika1212@gmail.com was activated', 8, NULL, NULL, NULL, '2020-08-22 19:10:41'),
(44, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 19:10:46'),
(45, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 20:08:44'),
(46, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 20:59:28'),
(47, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:02:16'),
(52, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-08-22 21:11:18'),
(53, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:15:12'),
(54, 'User weronika1212@gmail.com updated name', 4, NULL, NULL, NULL, '2020-08-22 21:15:34'),
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
(248, 'User weronika1212@gmail.com deleted access id=3', 8, NULL, NULL, NULL, '2020-09-09 19:18:33'),
(249, 'User weronika1212@gmail.com created new access class &#39;test&#39; ', 8, NULL, NULL, NULL, '2020-09-09 19:30:13'),
(250, 'User weronika1212@gmail.com updated', 8, NULL, NULL, NULL, '2020-09-09 20:15:39'),
(256, 'teseting building C', 8, NULL, 4, NULL, '2020-09-09 20:16:46'),
(257, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-10-01 17:17:47'),
(258, 'User weronika1212@gmail.com created reservation data:{&#34;title&#34;:&#34;rezerwacja v3&#34;,&#34;subtitle&#34;:&#34;podtytu\\u0142 rezerwacji, update rezerwacji&#34;,&#34;start_time&#34;:&#34;11:00&#34;,&#34;end_time&#34;:&#34;11:45&#34;,&#34;date&#34;:&#34;2020-10-02&#34;,&#34;room_id&#34;:12,&#34;building_id&#34;:6,&#34;user_id&#34;:8}', 8, 16, 6, 12, '2020-10-01 17:44:14'),
(259, 'User weronika1212@gmail.com created reservation data:{&#34;title&#34;:&#34;rezerwacja v3&#34;,&#34;subtitle&#34;:&#34;podtytu\\u0142 rezerwacji, update rezerwacji&#34;,&#34;start_time&#34;:&#34;12:00&#34;,&#34;end_time&#34;:&#34;12:45&#34;,&#34;date&#34;:&#34;2020-10-02&#34;,&#34;room_id&#34;:12,&#34;building_id&#34;:6,&#34;user_id&#34;:8}', 8, 17, 6, 12, '2020-10-01 17:47:05'),
(260, 'User weronika1212@gmail.com created reservation data:{&#34;title&#34;:&#34;rezerwacja v3&#34;,&#34;subtitle&#34;:&#34;podtytu\\u0142 rezerwacji, update rezerwacji&#34;,&#34;start_time&#34;:&#34;12:00&#34;,&#34;end_time&#34;:&#34;12:45&#34;,&#34;date&#34;:&#34;2020-10-02&#34;,&#34;room_id&#34;:11,&#34;building_id&#34;:6,&#34;user_id&#34;:8}', 8, 18, 6, 11, '2020-10-01 17:47:18'),
(261, 'User weronika1212@gmail.com created reservation data:{&#34;title&#34;:&#34;rezerwacja v3&#34;,&#34;subtitle&#34;:&#34;podtytu\\u0142 rezerwacji, update rezerwacji&#34;,&#34;start_time&#34;:&#34;14:00&#34;,&#34;end_time&#34;:&#34;14:45&#34;,&#34;date&#34;:&#34;2020-10-02&#34;,&#34;room_id&#34;:12,&#34;building_id&#34;:6,&#34;user_id&#34;:8}', 8, 19, 6, 12, '2020-10-01 17:47:50'),
(262, 'User weronika1212@gmail.com created reservation data:{&#34;title&#34;:&#34;rezerwacja v3&#34;,&#34;subtitle&#34;:&#34;podtytu\\u0142 rezerwacji, update rezerwacji&#34;,&#34;start_time&#34;:&#34;14:00&#34;,&#34;end_time&#34;:&#34;14:45&#34;,&#34;date&#34;:&#34;2020-10-02&#34;,&#34;room_id&#34;:11,&#34;building_id&#34;:6,&#34;user_id&#34;:8}', 8, 20, 6, 11, '2020-10-01 17:47:55'),
(263, 'User weronika1212@gmail.com created reservation data:{&#34;title&#34;:&#34;rezerwacja v3&#34;,&#34;subtitle&#34;:&#34;podtytu\\u0142 rezerwacji, update rezerwacji&#34;,&#34;start_time&#34;:&#34;14:00&#34;,&#34;end_time&#34;:&#34;14:45&#34;,&#34;date&#34;:&#34;2020-10-02&#34;,&#34;room_id&#34;:14,&#34;building_id&#34;:7,&#34;user_id&#34;:8}', 8, 21, 7, 14, '2020-10-01 17:48:41'),
(264, 'User weronika1212@gmail.com created reservation data:{&#34;title&#34;:&#34;rezerwacja v3&#34;,&#34;subtitle&#34;:&#34;podtytu\\u0142 rezerwacji, update rezerwacji&#34;,&#34;start_time&#34;:&#34;14:00&#34;,&#34;end_time&#34;:&#34;14:45&#34;,&#34;date&#34;:&#34;2020-10-02&#34;,&#34;room_id&#34;:15,&#34;building_id&#34;:7,&#34;user_id&#34;:8}', 8, 22, 7, 15, '2020-10-01 17:48:45'),
(265, 'User weronika1212@gmail.com moved reservation to trash', 8, 6, NULL, NULL, '2020-10-01 18:04:32'),
(266, 'User weronika1212@gmail.com moved reservation to trash', 8, 6, NULL, NULL, '2020-10-01 18:05:34'),
(267, 'User weronika1212@gmail.com moved reservation to trash', 8, 6, NULL, NULL, '2020-10-01 18:05:47'),
(268, 'User weronika1212@gmail.com moved reservation to trash', 8, 6, NULL, NULL, '2020-10-01 18:05:53'),
(269, 'User weronika1212@gmail.com moved reservation to trash', 8, 6, NULL, NULL, '2020-10-01 18:07:17'),
(270, 'User weronika1212@gmail.com moved reservation to trash', 8, 6, NULL, NULL, '2020-10-01 18:08:09'),
(271, 'User weronika1212@gmail.com hard deleted reservation', 8, 6, NULL, NULL, '2020-10-01 18:09:22'),
(272, 'User weronika1212@gmail.com updated data:{&#34;name&#34;:&#34;test&#34;,&#34;access_edit&#34;:true,&#34;buildings_view&#34;:true,&#34;buildings_edit&#34;:true}', 8, NULL, NULL, NULL, '2020-10-05 16:33:03'),
(273, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:40:05'),
(274, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:41:01'),
(275, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:42:22'),
(276, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:42:24'),
(277, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;probna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:42:30'),
(278, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;probna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:42:45'),
(279, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;probna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:43:02'),
(280, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;probna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:43:11'),
(281, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;probna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:43:15'),
(282, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;probna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:43:16'),
(283, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;probna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:43:37'),
(284, 'User weronika1212@gmail.com updated reservation data:{&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:43:49'),
(285, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;aktualizacja rezerwacji&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:45:05'),
(286, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;aktualizacja rezerwacji&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:45:30'),
(287, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;aktualizacja rezerwacji&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:45:32'),
(288, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;aktualizacja rezerwacji&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:45:47'),
(289, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;aktualizacja rezerwacji&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:46:26'),
(290, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;aktualizacja rezerwacji&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:47:29'),
(291, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;aktualizacja rezerwacji 2&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:48:51'),
(292, 'User weronika1212@gmail.com updated data:{&#34;name&#34;:&#34;test&#34;,&#34;access_edit&#34;:true,&#34;buildings_view&#34;:true,&#34;buildings_edit&#34;:true}', 8, NULL, NULL, NULL, '2020-10-05 18:53:58'),
(293, 'User weronika1212@gmail.com updated data:{&#34;name&#34;:&#34;test&#34;,&#34;access_edit&#34;:true,&#34;buildings_view&#34;:true,&#34;buildings_edit&#34;:true}', 8, NULL, NULL, NULL, '2020-10-05 18:54:34'),
(294, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:55:48'),
(295, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00d3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 18:56:02'),
(296, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr&oacute;bna rezerwacja&lt;&gt;\\/?&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt&oacute;ry zach\\u0119ca niechc\\u0105cych student&oacute;w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:19:07'),
(297, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr&oacute;bna rezerwacja&lt;&gt;\\/?&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt&oacute;ry zach\\u0119ca niechc\\u0105cych student&oacute;w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:20:06'),
(298, 'User weronika1212@gmail.com created Building id=9; data:{&#34;name&#34;:&#34;Budynek testowy&#34;,&#34;address_id&#34;:1,&#34;rooms_count&#34;:45}', 8, NULL, 9, NULL, '2020-10-05 19:23:49'),
(299, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:24:21'),
(300, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:35:54'),
(301, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja\\/\\/\\/&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:36:02'),
(302, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:36:16'),
(303, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:20'),
(304, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:22'),
(305, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:23'),
(306, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:25'),
(307, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:26'),
(308, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:26'),
(309, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:27'),
(310, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:29'),
(311, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:30'),
(312, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:30'),
(313, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:31'),
(314, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:37'),
(315, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:38'),
(316, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:38'),
(317, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:39'),
(318, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:39'),
(319, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:40'),
(320, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:40'),
(321, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:41'),
(322, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:45'),
(323, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:46'),
(324, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:47'),
(325, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:48'),
(326, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:38:55'),
(327, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:00'),
(328, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:01'),
(329, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:06'),
(330, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:37'),
(331, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:39'),
(332, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:41'),
(333, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:47'),
(334, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:49'),
(335, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:50'),
(336, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:39:52'),
(337, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja@#$&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:40:00'),
(338, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:40:10'),
(339, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:40:37'),
(340, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:40:42'),
(341, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:41:36'),
(342, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:41:37'),
(343, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:41:48'),
(344, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:41:49'),
(345, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:46:28'),
(346, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 19:59:17'),
(347, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 20:05:26'),
(348, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 20:05:33'),
(349, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;}', 8, 3, NULL, NULL, '2020-10-05 21:06:04'),
(350, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;pr\\u00f3bna rezerwacja&#34;,&#34;subtitle&#34;:&#34;bardzo fajny podtytu\\u0142 kt\\u00f3ry zach\\u0119ca niechc\\u0105cych student\\u00f3w&#34;,&#34;start_time&#34;:&#34;12:00:00&#34;,&#34;end_time&#34;:&#34;13:30:00&#34;,&#34;date&#34;:&#34;2020-09-12&#34;}', 8, 3, NULL, NULL, '2020-10-05 21:25:02'),
(351, 'User weronika1212@gmail.com (id=8) updated user (id=8) data:{&#34;name&#34;:&#34;Weronika&#34;}', 8, NULL, NULL, NULL, '2020-10-05 21:34:38'),
(352, 'User weronika1212@gmail.com (id=8) updated user (id=8) data:{&#34;name&#34;:&#34;Weronika&#34;}', 8, NULL, NULL, NULL, '2020-10-05 21:34:52'),
(353, 'User weronika1212@gmail.com (id=8) updated user (id=8) data:{&#34;name&#34;:&#34;Weronika&#34;}', 8, NULL, NULL, NULL, '2020-10-05 21:36:12'),
(354, 'User weronika1212@gmail.com (id=8) updated user (id=8) data:{&#34;name&#34;:&#34;WeronikaX&#34;}', 8, NULL, NULL, NULL, '2020-10-05 21:36:45'),
(355, 'Updated User weronika1212@gmail.com (id=8) with data:{&#34;name&#34;:&#34;WeronikaX&#34;,&#34;access_id&#34;:1}', 8, NULL, NULL, NULL, '2020-10-07 16:41:02'),
(356, 'Updated User weronika1212@gmail.com (id=8) with data:{&#34;name&#34;:&#34;Weronika12&#34;}', 8, NULL, NULL, NULL, '2020-10-07 17:43:38'),
(357, 'Updated User weronika1212@gmail.com (id=8) with data:{&#34;name&#34;:&#34;Weronika&#34;}', 8, NULL, NULL, NULL, '2020-10-07 17:43:42'),
(358, 'User szymonrykala1214@gmail.com has been registered data:{&#34;name&#34;:&#34;Szymon&#34;,&#34;surname&#34;:&#34;Ryka\\u0142a&#34;,&#34;email&#34;:&#34;szymonrykala1214@gmail.com&#34;,&#34;action_key&#34;:&#34;EWvWRyhD2bszfhd8P8+tkz+Kedb1bLj8cKEAxq25PUGY2KTLQhPk8xvxAhWlchoTHQAAb8HcB8r3LqOk&#34;}', 10, NULL, NULL, NULL, '2020-10-07 17:54:52'),
(359, 'User weronika1212@gmail.com created new room type id=7; data:{&#34;name&#34;:&#34;sala bardzo du\\u017ca og\\u00f3lnie&#34;}', 8, NULL, NULL, NULL, '2020-10-07 18:03:10'),
(360, 'User weronika1212@gmail.com updated room type id=7 data:{&#34;name&#34;:&#34;testowej update&#34;}', 8, NULL, NULL, NULL, '2020-10-07 18:03:35'),
(361, 'User weronika1212@gmail.com updated room type id=7', 8, NULL, NULL, NULL, '2020-10-07 22:13:13'),
(362, 'User weronika1212@gmail.com created new room in building id=2; data:{&#34;name&#34;:&#34;ABCDsuper budynek&#34;,&#34;room_type_id&#34;:5,&#34;seats_count&#34;:350,&#34;floor&#34;:2,&#34;equipment&#34;:&#34;umywalka,kreda,tablica&#34;,&#34;building_id&#34;:2}', 8, NULL, 2, 17, '2020-10-07 22:34:02'),
(363, 'User weronika1212@gmail.com updated room data:{&#34;floor&#34;:3,&#34;name&#34;:&#34;pok\\u00f3j update itd&#34;}', 8, NULL, 2, 17, '2020-10-07 22:37:50'),
(364, 'User weronika1212@gmail.com deleted room', 8, NULL, 2, 17, '2020-10-07 22:39:55'),
(365, 'User weronika1212@gmail.com created reservation data:{&#34;title&#34;:&#34;rezerwacja v4&#34;,&#34;subtitle&#34;:&#34;super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;&#34;,&#34;start_time&#34;:&#34;14:00&#34;,&#34;end_time&#34;:&#34;14:45&#34;,&#34;date&#34;:&#34;2020-10-21&#34;,&#34;room_id&#34;:15,&#34;building_id&#34;:7,&#34;user_id&#34;:8}', 8, 23, 7, 15, '2020-10-07 23:31:11'),
(366, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;aktualizacja&#34;,&#34;start_time&#34;:&#34;12:00:00&#34;,&#34;end_time&#34;:&#34;13:30:00&#34;,&#34;date&#34;:&#34;2020-09-12&#34;}', 8, 23, NULL, NULL, '2020-10-07 23:35:34'),
(367, 'User weronika1212@gmail.com moved reservation to trash', 8, 23, NULL, NULL, '2020-10-07 23:37:50'),
(368, 'User weronika1212@gmail.com updated reservation data:{&#34;title&#34;:&#34;aktualizacja&#34;,&#34;start_time&#34;:&#34;12:00:00&#34;,&#34;end_time&#34;:&#34;13:30:00&#34;,&#34;date&#34;:&#34;2020-09-12&#34;}', 8, 23, NULL, NULL, '2020-10-07 23:37:56'),
(369, 'User weronika1212@gmail.com hard deleted reservation', 8, 23, NULL, NULL, '2020-10-07 23:38:07'),
(370, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 24, 7, 15, '2020-10-08 15:03:37'),
(371, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 25, 7, 15, '2020-10-08 15:04:08'),
(372, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 26, 7, 15, '2020-10-08 15:05:02'),
(373, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 27, 7, 15, '2020-10-08 15:05:06'),
(374, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 28, 7, 15, '2020-10-08 15:05:07'),
(375, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 29, 7, 15, '2020-10-08 15:05:08'),
(376, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 30, 7, 15, '2020-10-08 15:09:18'),
(377, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 31, 7, 15, '2020-10-08 15:10:43'),
(378, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 32, 7, 15, '2020-10-08 15:11:17'),
(379, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 33, 7, 15, '2020-10-08 15:12:26'),
(380, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 34, 7, 15, '2020-10-08 15:12:45'),
(381, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 35, 7, 15, '2020-10-08 15:14:04'),
(382, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 36, 7, 15, '2020-10-08 15:14:14'),
(383, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"14:45\",\"end_time\":\"14:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 37, 7, 15, '2020-10-08 15:14:20'),
(384, 'User weronika1212@gmail.com created reservation data:{\"title\":\"rezerwacja v4\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"15:00:00\",\"end_time\":\"15:16:00\",\"date\":\"2020-10-21\",\"room_id\":15,\"building_id\":7,\"user_id\":8}', 8, 38, 7, 15, '2020-10-08 15:50:14'),
(385, 'Account user szymonrykala1214@gmail.comwas activated', 10, NULL, NULL, NULL, '2020-10-14 22:02:20'),
(386, 'User weronika1212@gmail.com toggled to trueroom with rfid: d', 8, NULL, NULL, 12, '2020-10-16 17:48:11'),
(387, 'User weronika1212@gmail.com toggled to falseroom with rfid: d', 8, NULL, NULL, 12, '2020-10-16 17:48:39'),
(388, 'User weronika1212@gmail.com toggled to true state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:06:49'),
(389, 'User weronika1212@gmail.com toggled to false state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:06:53'),
(390, 'User weronika1212@gmail.com toggled to true state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:24:12'),
(391, 'User weronika1212@gmail.com toggled to false state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:24:31'),
(392, 'User weronika1212@gmail.com toggled to true state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:24:34'),
(393, 'User weronika1212@gmail.com toggled to false state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:24:38'),
(394, 'User weronika1212@gmail.com toggled to true state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:27:37'),
(395, 'User weronika1212@gmail.com toggled to false state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:27:39'),
(396, 'User weronika1212@gmail.com toggled to true state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:28:43'),
(397, 'User weronika1212@gmail.com toggled to false state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:29:17'),
(398, 'User weronika1212@gmail.com toggled to true state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:29:36'),
(399, 'User weronika1212@gmail.com toggled to false state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:29:50'),
(400, 'User weronika1212@gmail.com toggled to true state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:31:07'),
(401, 'User weronika1212@gmail.com toggled to false state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:31:30'),
(402, 'User weronika1212@gmail.com toggled to true state of room with rfid: d', 8, NULL, NULL, 12, '2020-10-16 18:31:42'),
(403, 'User weronika1212@gmail.com succesfully veryfied', 8, NULL, NULL, NULL, '2020-10-17 21:12:06'),
(404, 'User test@gmail.com succesfully veryfied', 11, NULL, NULL, NULL, '2020-10-18 13:26:56'),
(405, 'User test@gmail.com veryfing failed count:1', 11, NULL, NULL, NULL, '2020-10-18 13:27:23'),
(406, 'User test@gmail.comchanged his email to testupdate@gmail.com', 11, NULL, NULL, NULL, '2020-10-18 14:06:20'),
(407, 'User weronika1212@gmail.com confirmed reservation', 8, 4, NULL, NULL, '2020-10-18 15:45:40'),
(408, 'User test@gmail.com has been registered data:{\"name\":\"testName\",\"surname\":\"testSurname\",\"email\":\"test@gmail.com\",\"access_id\":1,\"action_key\":\"1ePjDzo9\"}', 12, NULL, NULL, NULL, '2020-10-18 22:56:46'),
(409, 'User szymonrykala1214@gmail.com succesfully veryfied', 10, NULL, NULL, NULL, '2020-10-19 12:52:28'),
(410, 'User szymonrykala1214@gmail.com succesfully veryfied', 10, NULL, NULL, NULL, '2020-10-19 13:18:56'),
(411, 'User szymonrykala1214@gmail.com succesfully veryfied', 10, NULL, NULL, NULL, '2020-10-19 13:23:59'),
(412, 'User szymonrykala1214@gmail.com succesfully veryfied', 10, NULL, NULL, NULL, '2020-10-19 13:24:02'),
(413, 'User szymonrykala1214@gmail.com succesfully veryfied', 10, NULL, NULL, NULL, '2020-10-19 13:53:19'),
(414, 'User szymonrykala1214@gmail.com succesfully veryfied', 10, NULL, NULL, NULL, '2020-10-19 13:53:39'),
(415, 'User szymonrykala1214@gmail.com updated room data:{\"rfid\":\"0x450x45ksouigrnoo8gdfg\"}', 10, NULL, 2, 8, '2020-10-19 15:03:20'),
(416, 'User szymonrykala1214@gmail.com updated room data:{\"rfid\":\"erkjn495gn\"}', 10, NULL, 2, 8, '2020-10-19 15:09:11'),
(417, 'User szymonrykala1214@gmail.com updated room data:{\"rfid\":\"erkjn495gnddd\"}', 10, NULL, 2, 12, '2020-10-19 15:26:05'),
(418, 'User szymonrykala1214@gmail.com updated room data:{\"rfid\":\"erkjn495gnsdhyn345h\"}', 10, NULL, NULL, 2, '2020-10-19 15:30:06'),
(419, 'User szymonrykala1214@gmail.com updated room data:{\"rfid\":\"erkjn495gnsdhyn345\"}', 10, NULL, NULL, 2, '2020-10-19 15:50:15');

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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
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
(4, 'rezerwacja próbna', 'test - próbna rezerwacja', 2, 2, 10, '12:00:00', '13:30:00', '2020-10-12', '2020-08-24 22:11:19', '2020-10-18 15:45:40', 1, NULL, NULL, 0),
(7, 'rezerwacja próbna', 'test - próbna rezerwacja', 3, 3, 8, '12:00:00', '13:30:00', '2020-08-12', '2020-08-24 22:11:30', '2020-08-24 22:11:30', 0, NULL, NULL, 0),
(10, 'rezerwacja próbna', 'test - próbna rezerwacja', 3, 3, 8, '12:00:00', '13:30:00', '2020-08-14', '2020-08-24 22:11:26', '2020-08-24 22:11:26', 0, NULL, NULL, 0),
(12, 'rezerwacja v1', 'podtytuł rezerwacji, opis', 4, 2, 8, '10:00:00', '11:15:00', '2020-08-11', '2020-08-25 21:27:20', '2020-08-25 21:27:20', 0, NULL, NULL, 0),
(13, 'rezerwacja v1', 'podtytuł rezerwacji, opis', 4, 2, 8, '10:00:00', '11:15:00', '2020-08-28', '2020-08-25 21:28:04', '2020-08-25 21:28:04', 0, NULL, NULL, 0),
(14, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 4, 2, 8, '11:00:00', '11:20:00', '2020-08-27', '2020-08-28 00:37:49', '2020-08-28 00:37:49', 0, NULL, NULL, 0),
(16, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 12, 6, 8, '11:00:00', '11:45:00', '2020-10-02', '2020-10-01 17:44:14', '2020-10-01 17:44:14', 0, NULL, NULL, 0),
(17, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 12, 6, 8, '12:00:00', '12:45:00', '2020-10-02', '2020-10-01 17:47:05', '2020-10-01 17:47:05', 0, NULL, NULL, 0),
(18, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 11, 6, 8, '12:00:00', '12:45:00', '2020-10-02', '2020-10-01 17:47:18', '2020-10-01 17:47:18', 0, NULL, NULL, 0),
(19, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 12, 6, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:47:50', '2020-10-01 17:47:50', 0, NULL, NULL, 0),
(20, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 11, 6, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:47:55', '2020-10-01 17:47:55', 0, NULL, NULL, 0),
(21, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 14, 7, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:48:41', '2020-10-01 17:48:41', 0, NULL, NULL, 0),
(22, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 15, 7, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:48:45', '2020-10-01 17:48:45', 0, NULL, NULL, 0),
(24, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:03:37', '2020-10-08 15:03:37', 0, NULL, NULL, 0),
(25, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:04:08', '2020-10-08 15:04:08', 0, NULL, NULL, 0),
(26, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:05:02', '2020-10-08 15:05:02', 0, NULL, NULL, 0),
(27, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:05:06', '2020-10-08 15:05:06', 0, NULL, NULL, 0),
(28, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:05:07', '2020-10-08 15:05:07', 0, NULL, NULL, 0),
(29, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:05:08', '2020-10-08 15:05:08', 0, NULL, NULL, 0),
(30, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:09:18', '2020-10-08 15:09:18', 0, NULL, NULL, 0),
(31, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:10:43', '2020-10-08 15:10:43', 0, NULL, NULL, 0),
(32, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:11:17', '2020-10-08 15:11:17', 0, NULL, NULL, 0),
(33, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:12:26', '2020-10-08 15:12:26', 0, NULL, NULL, 0),
(34, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:12:45', '2020-10-08 15:12:45', 0, NULL, NULL, 0),
(35, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:14:04', '2020-10-08 15:14:04', 0, NULL, NULL, 0),
(36, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:14:14', '2020-10-08 15:14:14', 0, NULL, NULL, 0),
(37, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:14:20', '2020-10-08 15:14:20', 0, NULL, NULL, 0),
(38, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '15:00:00', '15:16:00', '2020-10-21', '2020-10-08 15:50:14', '2020-10-08 15:50:14', 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `rfid` tinytext NOT NULL DEFAULT '',
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

INSERT INTO `rooms` (`id`, `name`, `rfid`, `building_id`, `room_type_id`, `seats_count`, `floor`, `equipment`, `blockade`, `state`) VALUES
(2, 'B001', 'erkjn495gnsdhyn345', 3, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(3, 'B201', 'r', 3, 5, 60, 2, 'rzutnik,kreda,tablica', 1, 0),
(4, 'B101', 't', 3, 2, 60, 1, 'rzutnik,kreda,tablica', 0, 0),
(5, 'A001', 'y', 2, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(6, 'A201', 'u', 2, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0),
(7, 'A101', 'i', 2, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0),
(8, 'C001', 'erkjn495gn', 4, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(9, 'C201', 'p', 2, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0),
(10, 'C101', 'a', 2, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0),
(11, 'D001', 's', 6, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(12, 'D101', 'erkjn495gnddd', 6, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 1),
(13, 'D201', 'f', 6, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0),
(14, 'E001', 'g', 7, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0),
(15, 'E101', 'h', 7, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0),
(16, 'E201', 'j', 7, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0);

--
-- Wyzwalacze `rooms`
--
DELIMITER $$
CREATE TRIGGER `Update_rooms_count_after_delete` AFTER DELETE ON `rooms` FOR EACH ROW UPDATE buildings set buildings.rooms_count=(SELECT COUNT(rooms.id) FROM rooms WHERE rooms.building_id=buildings.id)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Update_rooms_count_after_insert` AFTER INSERT ON `rooms` FOR EACH ROW UPDATE buildings set buildings.rooms_count=(SELECT COUNT(rooms.id) FROM rooms WHERE rooms.building_id=buildings.id)
$$
DELIMITER ;

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
  `access_id` int(11) NOT NULL DEFAULT 1,
  `name` tinytext NOT NULL,
  `surname` tinytext NOT NULL,
  `password` text NOT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` tinytext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activated` tinyint(1) NOT NULL DEFAULT 0,
  `login_fails` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `action_key` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `access_id`, `name`, `surname`, `password`, `last_login`, `email`, `updated_at`, `activated`, `login_fails`, `created_at`, `action_key`) VALUES
(8, 3, 'Weronika', 'Urbańska|T', '$2y$12$mLMGKbiLWDMoOxkeyxnX6OEguoUFS.WyAAFOxA1GL14ESMp.MCxWi', '2020-10-07 17:43:42', 'weronika1212@gmail.com', '2020-10-07 17:43:42', 1, 0, '2020-10-07 17:43:42', '1'),
(10, 1, 'Szymon', 'Rykała', '$2y$12$efkNjWJHZwkgSnWs4ExVdON47kma2OAw0q/2E7ivTf9qIVvNAN.HO', '2020-10-14 22:02:20', 'szymonrykala1214@gmail.com', '2020-10-14 22:02:20', 1, 0, '2020-10-14 22:02:20', '1'),
(11, 1, 'testName', 'testSurname', '$2y$12$FXd5UMeI5hpVTUyTUjqRyOCYUNYRdgdMxzw1tC/dNwB.2ecaKgP.K', '2020-10-18 14:06:20', 'testupdate@gmail.com', '2020-10-18 14:06:20', 0, 1, '2020-10-18 13:15:53', 'NONE_NONE'),
(12, 1, 'testName', 'testSurname', '$2y$12$bXhBq9HzVmwWXd76dLee4uddfCQWnqbwm3zDMvOxrtopQjDDCWasW', '2020-10-18 22:56:46', 'test@gmail.com', '2020-10-18 22:56:46', 0, 0, '2020-10-18 22:56:46', '1ePjDzo9');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `accesses`
--
ALTER TABLE `accesses`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `unique_rfid` (`rfid`(255)) USING BTREE,
  ADD KEY `rooms_to_buildings` (`building_id`),
  ADD KEY `rooms_to_room_types` (`room_type_id`);

--
-- Indeksy dla tabeli `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`) USING HASH,
  ADD KEY `users_to_accesses` (`access_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `accesses`
--
ALTER TABLE `accesses`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=420;

--
-- AUTO_INCREMENT dla tabeli `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT dla tabeli `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT dla tabeli `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  ADD CONSTRAINT `users_to_accesses` FOREIGN KEY (`access_id`) REFERENCES `accesses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
