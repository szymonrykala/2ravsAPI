-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 22 Paź 2020, 23:13
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
  `statistics_view` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `accesses`
--

INSERT INTO `accesses` (`id`, `name`, `rfid_action`, `access_edit`, `buildings_view`, `buildings_edit`, `logs_view`, `logs_edit`, `rooms_view`, `rooms_edit`, `reservations_access`, `reservations_confirm`, `reservations_edit`, `users_edit`, `statistics_view`, `created`, `updated`) VALUES
(1, 'admin', 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2020-10-22 22:07:22', '2020-10-22 22:07:22'),
(3, 'demo', 1, 0, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0, '2020-10-22 22:07:22', '2020-10-22 22:07:22'),
(4, 'test', 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2020-10-22 22:07:22', '2020-10-22 22:07:22');

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
  `number` tinytext NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `addresses`
--

INSERT INTO `addresses` (`id`, `country`, `town`, `postal_code`, `street`, `number`, `created`, `updated`) VALUES
(1, 'Poland', 'Bydgoszcz', '85-791', 'Kaliskiego', '41', '2020-10-22 21:40:10', '2020-10-22 21:40:10'),
(2, 'Poland', 'Kowal', '87-820', 'Grabkowska', '7', '2020-10-22 21:40:10', '2020-10-22 21:40:10');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `buildings`
--

CREATE TABLE `buildings` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `rooms_count` int(11) NOT NULL DEFAULT 0,
  `address_id` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `buildings`
--

INSERT INTO `buildings` (`id`, `name`, `rooms_count`, `address_id`, `created`, `updated`) VALUES
(2, 'Budynek A', 5, 1, '2020-10-22 19:44:39', '2020-10-22 19:44:39'),
(3, 'Budynek B', 3, 2, '2020-10-22 19:44:39', '2020-10-22 19:44:39'),
(4, 'Budynek C', 1, 1, '2020-10-22 19:44:39', '2020-10-22 19:44:39'),
(6, 'Budynek D', 3, 2, '2020-10-22 19:44:39', '2020-10-22 19:44:39'),
(7, 'Budynek E', 4, 1, '2020-10-22 19:44:39', '2020-10-22 19:44:39'),
(8, 'Budynek F', 1, 2, '2020-10-22 19:44:39', '2020-10-22 19:44:39'),
(11, 'Budynek testowy', 0, 1, '2020-10-22 19:44:39', '2020-10-22 19:44:39');

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
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `logs`
--

INSERT INTO `logs` (`id`, `message`, `user_id`, `reservation_id`, `building_id`, `room_id`, `created`) VALUES
(1, 'User szymonrykala1214@gmail.com created new room in building id=9; data:{\"name\":\"super pok\\u00f3j testowy\",\"room_type_id\":5,\"seats_count\":350,\"floor\":2,\"rfid\":\"ksjdfhi73t48i3\",\"equipment\":\"umywalka,kreda,tablica\",\"blockade\":true,\"building_id\":9}', 10, NULL, 9, 0, '2020-10-19 22:50:53'),
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
(419, 'User szymonrykala1214@gmail.com updated room data:{\"rfid\":\"erkjn495gnsdhyn345\"}', 10, NULL, NULL, 2, '2020-10-19 15:50:15'),
(420, 'User szymonrykala1214@gmail.com created new room in building id=2; data:{\"name\":\"super budynek testowy\",\"room_type_id\":5,\"seats_count\":350,\"floor\":2,\"rfid\":\"ksjdfhi73t48i3\",\"equipment\":\"umywalka,kreda,tablica\",\"building_id\":2}', 10, NULL, 2, 18, '2020-10-19 16:15:12'),
(421, 'User szymonrykala1214@gmail.com updated room data:{\"blockade\":true}', 10, NULL, NULL, 18, '2020-10-19 16:24:47'),
(422, 'User szymonrykala1214@gmail.com updated room data:{\"blockade\":false}', 10, NULL, NULL, 18, '2020-10-19 16:31:54'),
(423, 'User szymonrykala1214@gmail.com created reservation data:{\"title\":\"rezerwacja testowa\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"15:00:00\",\"end_time\":\"15:16:00\",\"date\":\"2020-10-21\",\"room_id\":18,\"building_id\":2,\"user_id\":10}', 10, 39, 2, 18, '2020-10-19 16:32:31'),
(424, 'User szymonrykala1214@gmail.com deleted room', 10, NULL, NULL, 18, '2020-10-19 16:36:53'),
(425, 'User szymonrykala1214@gmail.com created new room in building id=9; data:{\"name\":\"super pok\\u00f3j testowy\",\"room_type_id\":5,\"seats_count\":350,\"floor\":2,\"rfid\":\"ksjdfhi73t48i3\",\"equipment\":\"umywalka,kreda,tablica\",\"blockade\":true,\"building_id\":9}', 10, NULL, 9, 20, '2020-10-19 16:40:01'),
(426, 'User szymonrykala1214@gmail.com updated room data:{\"blockade\":false}', 10, NULL, NULL, 20, '2020-10-19 16:40:52'),
(427, 'User szymonrykala1214@gmail.com created reservation data:{\"title\":\"rezerwacja testowa\",\"subtitle\":\"super fajny tytu\\u0142 i opis rezerwacji kt&oacute;ry powinien zawiera\\u0107 kropki, przecinki.:a czasem nawet &quot;cytat&quot;\",\"start_time\":\"15:00:00\",\"end_time\":\"15:16:00\",\"date\":\"2020-10-21\",\"room_id\":20,\"building_id\":9,\"user_id\":10}', 10, 40, 9, 20, '2020-10-19 16:40:59'),
(428, 'User szymonrykala1214@gmail.com deleted Building id=9', 10, NULL, 9, NULL, '2020-10-19 16:42:53'),
(429, 'User szymonrykala1214@gmail.com created new room in building id=8; data:{\"name\":\"test integralno\\u015bci 2\",\"room_type_id\":5,\"seats_count\":350,\"floor\":1,\"rfid\":\"ksjdfhi7ifhfe3\",\"equipment\":\"umywalka,kreda,tablica\",\"blockade\":true,\"building_id\":8}', 10, NULL, 8, 41, '2020-10-19 23:09:12'),
(430, 'User szymonrykala1214@gmail.com updated room data:{\"blockade\":false}', 10, NULL, NULL, 6, '2020-10-19 23:13:11'),
(431, 'User szymonrykala1214@gmail.com updated room data:{\"rfid\":\"ksjdfhi7ifhfje3\"}', 10, NULL, NULL, 6, '2020-10-19 23:13:20'),
(432, 'User szymonrykala1214@gmail.com updated room data:{\"rfid\":\"ksjdfhi7ifhfje3\"}', 10, NULL, NULL, 6, '2020-10-19 23:13:25'),
(433, 'User szymonrykala1214@gmail.com updated room data:{\"rfid\":\"ksjdfhi7ifhfje3\"}', 10, NULL, NULL, 6, '2020-10-19 23:13:33'),
(434, 'USER szymonrykala1214@gmail.com CREATE room DATA {\"name\":\"test integralno\\u015bci 2\",\"room_type_id\":5,\"seats_count\":350,\"floor\":1,\"rfid\":\"ksjdfhi7ifhfj53\",\"equipment\":\"umywalka,kreda,tablica\",\"blockade\":true,\"building_id\":7}', 10, NULL, 7, 49, '2020-10-19 23:25:00'),
(435, 'USER szymonrykala1214@gmail.com CREATE building DATA {\"name\":\"Budynek testowy\",\"address_id\":18,\"id\":9}', 10, NULL, 9, NULL, '2020-10-20 00:00:58'),
(436, 'USER szymonrykala1214@gmail.com CREATE building DATA {\"name\":\"Budynek testowy\",\"address_id\":1,\"id\":11}', 10, NULL, 11, NULL, '2020-10-20 00:06:05'),
(437, 'USER szymonrykala1214@gmail.com UPDATE user testupdate@gmail.com DATA {\"name\":\"test name \"}', 10, NULL, NULL, NULL, '2020-10-20 00:35:48'),
(438, 'USER szymonrykala1214@gmail.com UPDATE user testupdate@gmail.com DATA {\"access_id\":3}', 10, NULL, NULL, NULL, '2020-10-20 00:36:38'),
(439, 'USER szymonrykala1214@gmail.com UPDATE user testupdate@gmail.com DATA {\"access_id\":3}', 10, NULL, NULL, NULL, '2020-10-20 00:36:47'),
(440, 'USER szymonrykala1214@gmail.com UPDATE user testupdate@gmail.com DATA {\"access_id\":4}', 10, NULL, NULL, NULL, '2020-10-20 00:37:19'),
(441, 'USER szymonrykala1214@gmail.com UPDATE user testupdate@gmail.com DATA {\"access_id\":4}', 10, NULL, NULL, NULL, '2020-10-20 00:37:21'),
(442, 'USER szymonrykala1214@gmail.com UPDATE user testupdate@gmail.com DATA {\"access_id\":1}', 10, NULL, NULL, NULL, '2020-10-20 00:37:51'),
(443, 'USER szymonrykala1214@gmail.com UPDATE user testupdate@gmail.com DATA {\"access_id\":1}', 10, NULL, NULL, NULL, '2020-10-20 00:37:59'),
(444, 'USER szymonrykala1214@gmail.com VERYFIED', 10, NULL, NULL, NULL, '2020-10-21 00:48:50'),
(445, 'USER szymonrykala1214@gmail.com UPDATE user testupdate@gmail.com DATA {\"name\":\"updated test\"}', 10, NULL, NULL, NULL, '2020-10-21 01:02:18');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `title` tinytext NOT NULL,
  `describtion` text NOT NULL,
  `room_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `date` date NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `confirming_user_id` int(11) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `reservations`
--

INSERT INTO `reservations` (`id`, `title`, `describtion`, `room_id`, `building_id`, `user_id`, `start_time`, `end_time`, `date`, `created`, `updated`, `confirmed`, `confirming_user_id`, `confirmed_at`) VALUES
(4, 'rezerwacja próbna', 'test - próbna rezerwacja', 2, 2, 10, '12:00:00', '21:30:00', '2020-10-19', '2020-08-24 22:11:19', '2020-10-19 20:09:51', 1, NULL, NULL),
(7, 'rezerwacja próbna', 'test - próbna rezerwacja', 3, 3, 8, '12:00:00', '13:30:00', '2020-08-12', '2020-08-24 22:11:30', '2020-08-24 22:11:30', 0, NULL, NULL),
(10, 'rezerwacja próbna', 'test - próbna rezerwacja', 3, 3, 8, '12:00:00', '13:30:00', '2020-08-14', '2020-08-24 22:11:26', '2020-08-24 22:11:26', 0, NULL, NULL),
(12, 'rezerwacja v1', 'podtytuł rezerwacji, opis', 4, 2, 8, '10:00:00', '11:15:00', '2020-08-11', '2020-08-25 21:27:20', '2020-08-25 21:27:20', 0, NULL, NULL),
(13, 'rezerwacja v1', 'podtytuł rezerwacji, opis', 4, 2, 8, '10:00:00', '11:15:00', '2020-08-28', '2020-08-25 21:28:04', '2020-08-25 21:28:04', 0, NULL, NULL),
(14, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 4, 2, 8, '11:00:00', '11:20:00', '2020-08-27', '2020-08-28 00:37:49', '2020-08-28 00:37:49', 0, NULL, NULL),
(16, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 12, 6, 8, '11:00:00', '11:45:00', '2020-10-02', '2020-10-01 17:44:14', '2020-10-01 17:44:14', 0, NULL, NULL),
(17, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 12, 6, 8, '12:00:00', '12:45:00', '2020-10-02', '2020-10-01 17:47:05', '2020-10-01 17:47:05', 0, NULL, NULL),
(18, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 11, 6, 8, '12:00:00', '12:45:00', '2020-10-02', '2020-10-01 17:47:18', '2020-10-01 17:47:18', 0, NULL, NULL),
(19, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 12, 6, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:47:50', '2020-10-01 17:47:50', 0, NULL, NULL),
(20, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 11, 6, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:47:55', '2020-10-01 17:47:55', 0, NULL, NULL),
(21, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 14, 7, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:48:41', '2020-10-01 17:48:41', 0, NULL, NULL),
(22, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 15, 7, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:48:45', '2020-10-01 17:48:45', 0, NULL, NULL),
(24, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:03:37', '2020-10-08 15:03:37', 0, NULL, NULL),
(25, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:04:08', '2020-10-08 15:04:08', 0, NULL, NULL),
(26, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:05:02', '2020-10-08 15:05:02', 0, NULL, NULL),
(27, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:05:06', '2020-10-08 15:05:06', 0, NULL, NULL),
(28, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:05:07', '2020-10-08 15:05:07', 0, NULL, NULL),
(29, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:05:08', '2020-10-08 15:05:08', 0, NULL, NULL),
(30, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:09:18', '2020-10-08 15:09:18', 0, NULL, NULL),
(31, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:10:43', '2020-10-08 15:10:43', 0, NULL, NULL),
(32, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:11:17', '2020-10-08 15:11:17', 0, NULL, NULL),
(33, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:12:26', '2020-10-08 15:12:26', 0, NULL, NULL),
(34, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:12:45', '2020-10-08 15:12:45', 0, NULL, NULL),
(35, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:14:04', '2020-10-08 15:14:04', 0, NULL, NULL),
(36, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:14:14', '2020-10-08 15:14:14', 0, NULL, NULL),
(37, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '14:00:00', '2020-10-21', '2020-10-08 15:14:20', '2020-10-08 15:14:20', 0, NULL, NULL),
(38, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '15:00:00', '15:16:00', '2020-10-21', '2020-10-08 15:50:14', '2020-10-08 15:50:14', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `rfid` text NOT NULL,
  `building_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `seats_count` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `equipment` text NOT NULL,
  `blockade` tinyint(1) NOT NULL DEFAULT 1,
  `occupied` tinyint(1) NOT NULL DEFAULT 0,
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `rfid`, `building_id`, `room_type_id`, `seats_count`, `floor`, `equipment`, `blockade`, `occupied`, `updated`, `created`) VALUES
(2, 'B001', 'erkjn495gnsdhyn345', 3, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(3, 'B201', 'r', 3, 5, 60, 2, 'rzutnik,kreda,tablica', 1, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(4, 'B101', 't', 3, 2, 60, 1, 'rzutnik,kreda,tablica', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(5, 'A001', 'y', 2, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(6, 'A201', 'ksjdfhi7ifhfje3', 2, 2, 60, 2, 'rzutnik,kreda,tablica', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(7, 'A101', 'i', 2, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(8, 'C001', 'erkjn495gn', 4, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(9, 'C201', 'p', 2, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(10, 'C101', 'a', 2, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(11, 'D001', 's', 6, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(12, 'D101', 'erkjn495gnddd', 6, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 1, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(13, 'D201', 'f', 6, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(14, 'E001', 'g', 7, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(15, 'E101', 'h', 7, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(16, 'E201', 'j', 7, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(41, 'test integralności 2', 'ksjdfhi7ifhfe3', 8, 5, 350, 1, 'umywalka,kreda,tablica', 1, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10'),
(49, 'test integralności 2', 'ksjdfhi7ifhfj53', 7, 5, 350, 1, 'umywalka,kreda,tablica', 1, 0, '2020-10-22 21:35:10', '2020-10-22 21:35:10');

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
  `name` text NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `created`, `updated`) VALUES
(1, 'laboratory', '2020-10-22 21:38:05', '2020-10-22 21:38:05'),
(2, 'sala wykładowa', '2020-10-22 21:38:05', '2020-10-22 21:38:05'),
(5, 'aula', '2020-10-22 21:38:05', '2020-10-22 21:38:05');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `access_id` int(11) DEFAULT 1,
  `name` tinytext NOT NULL,
  `surname` tinytext NOT NULL,
  `password` text NOT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` text NOT NULL,
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  `activated` tinyint(1) NOT NULL DEFAULT 0,
  `login_fails` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `action_key` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `access_id`, `name`, `surname`, `password`, `last_login`, `email`, `updated`, `activated`, `login_fails`, `created`, `action_key`) VALUES
(8, 3, 'Weronika', 'Urbańska|T', '$2y$12$mLMGKbiLWDMoOxkeyxnX6OEguoUFS.WyAAFOxA1GL14ESMp.MCxWi', '2020-10-07 17:43:42', 'weronika1212@gmail.com', '2020-10-07 17:43:42', 1, 0, '2020-10-07 17:43:42', '1'),
(10, 1, 'Szymon', 'Rykała', '$2y$12$efkNjWJHZwkgSnWs4ExVdON47kma2OAw0q/2E7ivTf9qIVvNAN.HO', '2020-10-14 22:02:20', 'szymonrykala1214@gmail.com', '2020-10-14 22:02:20', 1, 0, '2020-10-14 22:02:20', '1'),
(11, 1, 'updated test', 'testSurname', '$2y$12$FXd5UMeI5hpVTUyTUjqRyOCYUNYRdgdMxzw1tC/dNwB.2ecaKgP.K', '2020-10-21 01:02:18', 'testupdate@gmail.com', '2020-10-21 01:02:18', 0, 1, '2020-10-18 13:15:53', 'NONE_NONE'),
(12, NULL, 'testName', 'testSurname', '$2y$12$bXhBq9HzVmwWXd76dLee4uddfCQWnqbwm3zDMvOxrtopQjDDCWasW', '2020-10-19 19:39:15', 'test@gmail.com', '2020-10-19 19:39:15', 0, 0, '2020-10-18 22:56:46', '1ePjDzo9');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `accesses`
--
ALTER TABLE `accesses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`(80)) USING BTREE COMMENT 'Gwarantuje unikalność nazwy klasy dostępu';

--
-- Indeksy dla tabeli `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_address` (`country`(80),`town`(80),`postal_code`(80),`street`(80),`number`(80)) USING BTREE COMMENT 'Gwarantuje unikalność adresu';

--
-- Indeksy dla tabeli `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_building` (`name`(80),`address_id`) USING BTREE COMMENT 'Gwarantuje, że pod danym adresem jest jeden budynek o danej nazwie',
  ADD KEY `buildings_to_addresses` (`address_id`) USING BTREE COMMENT 'adres budynku';

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
  ADD KEY `reservations_to_buildings` (`building_id`) USING BTREE COMMENT 'budynek w którym został zarezerwowany pokój',
  ADD KEY `reservations_to_rooms` (`room_id`) USING BTREE COMMENT 'zarezerwowany pokój',
  ADD KEY `reservations_to_users` (`user_id`) USING BTREE COMMENT 'rezerwujący użytkownik',
  ADD KEY `reservations_to_users_confirm` (`confirming_user_id`) USING BTREE COMMENT 'użytkownik potwierdzający rezerwację';

--
-- Indeksy dla tabeli `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rfid` (`rfid`(300)) USING BTREE COMMENT 'Gwarantuje unikalne RFID dla każdego pokoju',
  ADD UNIQUE KEY `unique_room` (`name`(80),`building_id`,`floor`) USING BTREE COMMENT 'Gwarantuje, że w danym budynku, na danym piętrze nie znajdą się pokoje z identycznymi nazwami',
  ADD KEY `rooms_to_buildings` (`building_id`) USING BTREE COMMENT 'budynek w którym znajduje się pokój',
  ADD KEY `rooms_to_room_types` (`room_type_id`) USING BTREE COMMENT 'typ pokoju';

--
-- Indeksy dla tabeli `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`(300)) USING BTREE COMMENT 'unikalna nazwa typu pokoji';

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`(100)) USING BTREE COMMENT 'Gwarantuje, że każdy użytkownik ma inny email',
  ADD KEY `users_to_accesses` (`access_id`) USING BTREE COMMENT 'klasa dostępu użytkownika';

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT dla tabeli `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT dla tabeli `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=446;

--
-- AUTO_INCREMENT dla tabeli `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT dla tabeli `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT dla tabeli `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  ADD CONSTRAINT `buildings_to_addresses` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_to_buildings` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_to_rooms` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_to_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_to_users_confirm` FOREIGN KEY (`confirming_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_to_buildings` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rooms_to_room_types` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_to_acceses` FOREIGN KEY (`access_id`) REFERENCES `accesses` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
