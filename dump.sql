-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 14 Lis 2020, 19:07
-- Wersja serwera: 10.4.14-MariaDB
-- Wersja PHP: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `ravs_test`
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
  `buildings_view` tinyint(1) NOT NULL DEFAULT 0,
  `buildings_edit` tinyint(1) NOT NULL DEFAULT 0,
  `logs_view` tinyint(1) NOT NULL DEFAULT 0,
  `logs_edit` tinyint(1) NOT NULL DEFAULT 0,
  `rooms_view` tinyint(1) NOT NULL DEFAULT 0,
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
(1, 'admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2020-10-22 22:07:22', '2020-11-08 23:34:38'),
(3, 'demo', 0, 0, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0, '2020-10-22 22:07:22', '2020-11-03 15:20:52'),
(4, 'test', 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2020-10-22 22:07:22', '2020-10-22 22:07:22'),
(5, 'super dostęp', 0, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, '2020-11-03 14:19:26', '2020-11-03 16:02:17');

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
(4, 'Budynek C', 9, 1, '2020-10-22 19:44:39', '2020-11-05 20:28:01'),
(6, 'Budynek D', 3, 2, '2020-10-22 19:44:39', '2020-10-22 19:44:39'),
(7, 'Budynek E', 3, 1, '2020-10-22 19:44:39', '2020-11-01 21:08:32'),
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
(445, 'USER szymonrykala1214@gmail.com UPDATE user testupdate@gmail.com DATA {\"name\":\"updated test\"}', 10, NULL, NULL, NULL, '2020-10-21 01:02:18'),
(446, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:52:16'),
(447, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:52:45'),
(448, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:52:46'),
(449, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:52:47'),
(450, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:52:47'),
(451, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:53:21'),
(452, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:53:22'),
(453, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:54:27'),
(454, 'USER weronika1212@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 8, NULL, NULL, NULL, '2020-11-01 17:54:42'),
(455, 'USER weronika1212@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 8, NULL, NULL, NULL, '2020-11-01 17:54:44'),
(456, 'USER weronika1212@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 8, NULL, NULL, NULL, '2020-11-01 17:54:56'),
(457, 'USER weronika1212@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 8, NULL, NULL, NULL, '2020-11-01 17:54:57'),
(458, 'USER weronika1212@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 8, NULL, NULL, NULL, '2020-11-01 17:55:05'),
(459, 'USER weronika1212@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 8, NULL, NULL, NULL, '2020-11-01 17:55:07'),
(460, 'USER weronika1212@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 8, NULL, NULL, NULL, '2020-11-01 17:55:17'),
(461, 'USER weronika1212@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 8, NULL, NULL, NULL, '2020-11-01 17:55:22'),
(462, 'USER weronika1212@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 8, NULL, NULL, NULL, '2020-11-01 17:55:28'),
(463, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:55:40'),
(464, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:55:45'),
(465, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:56:38'),
(466, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:56:51'),
(467, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:56:53'),
(468, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:0). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:57:14'),
(469, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:1). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:58:11'),
(470, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:2). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 17:58:28'),
(471, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:3). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 18:01:45'),
(472, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:4). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 18:01:50'),
(473, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Can not login. Login failed to many times and Your account is locked. Please contact with Your administrator', 10, NULL, NULL, NULL, '2020-11-01 18:02:06'),
(474, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Can not login. Login failed to many times and Your account is locked. Please contact with Your administrator', 10, NULL, NULL, NULL, '2020-11-01 18:02:07'),
(475, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Can not login. Login failed to many times and Your account is locked. Please contact with Your administrator', 10, NULL, NULL, NULL, '2020-11-01 18:02:08'),
(476, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Can not login. Login failed to many times and Your account is locked. Please contact with Your administrator', 10, NULL, NULL, NULL, '2020-11-01 18:02:10'),
(477, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:1). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 18:02:50'),
(478, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:2). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 18:05:19'),
(479, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:3). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 18:05:27'),
(480, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:4). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 18:07:12'),
(481, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:5). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 18:24:05'),
(482, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Can not login. Login failed to many times and Your account is locked. Please contact with Your administrator', 10, NULL, NULL, NULL, '2020-11-01 18:24:10'),
(483, 'USER szymonrykala1214@gmail.com NOT VERYFIED DATA Authentication failed (count:1). Password is not correct.', 10, NULL, NULL, NULL, '2020-11-01 18:24:28'),
(484, 'USER szymonrykala@gmail.com NOT VERYFIED DATA Can not authenticate because user is not activated', 15, NULL, NULL, NULL, '2020-11-01 19:19:42'),
(485, 'USER szymonrykala@gmail.com ACTIVATED DATA {&quot;activated&quot;:true}', 15, NULL, NULL, NULL, '2020-11-01 19:44:42'),
(486, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-01 19:44:53'),
(487, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-01 19:49:13'),
(488, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;name&quot;:&quot;Szymon_update&quot;}', 15, NULL, NULL, NULL, '2020-11-01 20:11:02'),
(489, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;name&quot;:&quot;Szymon_update&quot;,&quot;dupsko&quot;:34}', 15, NULL, NULL, NULL, '2020-11-01 20:11:36'),
(490, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;name&quot;:&quot;Szymon_update&quot;,&quot;dupsko&quot;:34}', 15, NULL, NULL, NULL, '2020-11-01 20:11:37'),
(491, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;name&quot;:&quot;Szymon_update&quot;,&quot;dupsko&quot;:34}', 15, NULL, NULL, NULL, '2020-11-01 20:11:38'),
(492, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;name&quot;:&quot;\\u015azym\\u0107i\\u00f3o&quot;}', 15, NULL, NULL, NULL, '2020-11-01 20:29:41'),
(493, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;name&quot;:&quot;\\u015azym\\u0107i\\u00f3o&quot;}', 15, NULL, NULL, NULL, '2020-11-01 20:32:17'),
(494, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;name&quot;:&quot;\\u015azym\\u0107i\\u00f3o&quot;}', 15, NULL, NULL, NULL, '2020-11-01 20:32:33'),
(495, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;name&quot;:&quot;Szymon&quot;}', 15, NULL, NULL, NULL, '2020-11-01 20:32:53'),
(496, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;id&quot;:&quot;Szymon&quot;}', 15, NULL, NULL, NULL, '2020-11-01 20:35:32'),
(497, 'USER szymonrykala@gmail.com UPDATE user szymonrykala@gmail.com DATA {&quot;id&quot;:&quot;Szymon&quot;}', 15, NULL, NULL, NULL, '2020-11-01 20:35:35'),
(498, 'USER szymonrykala@gmail.com CREATE building DATA {&quot;name&quot;:&quot;example name&quot;,&quot;address_id&quot;:2,&quot;id&quot;:13}', 15, NULL, 13, NULL, '2020-11-01 20:40:46'),
(499, 'USER szymonrykala@gmail.com UPDATE building DATA {&quot;rooms_count&quot;:23}', 15, NULL, 13, NULL, '2020-11-01 20:41:56'),
(500, 'USER szymonrykala@gmail.com UPDATE building DATA {&quot;name&quot;:&quot;updated name&quot;}', 15, NULL, 13, NULL, '2020-11-01 20:42:40'),
(501, 'USER szymonrykala@gmail.com UPDATE building DATA {&quot;name&quot;:&quot;\\u015bci\\u00f3\\u0142ka B&quot;}', 15, NULL, 13, NULL, '2020-11-01 20:43:26'),
(502, 'USER szymonrykala@gmail.com UPDATE building DATA {&quot;name&quot;:&quot;\\u015aci\\u00f3\\u0142ka B&quot;}', 15, NULL, 13, NULL, '2020-11-01 20:43:33'),
(503, 'USER szymonrykala@gmail.com UPDATE building DATA {&quot;name&quot;:&quot;\\u015aci\\u00f3\\u0142ka B&quot;}', 15, NULL, 13, NULL, '2020-11-01 20:43:35'),
(504, 'USER szymonrykala@gmail.com UPDATE building DATA {&#34;name&#34;:&#34;\\u015aci\\u00f3\\u0142ka B&#34;}', 15, NULL, 13, NULL, '2020-11-01 20:45:24'),
(505, 'USER szymonrykala@gmail.com UPDATE building DATA {&#34;name&#34;:&#34;\\u015aci\\u00f3\\u0142ka.B&#34;}', 15, NULL, 13, NULL, '2020-11-01 20:45:37'),
(506, 'USER szymonrykala@gmail.com UPDATE building DATA {&#34;name&#34;:&#34;\\u015aci\\u00f3\\u0142ka.B.1&#34;}', 15, NULL, 13, NULL, '2020-11-01 20:46:14'),
(507, 'USER szymonrykala@gmail.com DELETE building DATA {&#34;id&#34;:13,&#34;name&#34;:&#34;\\u015aci\\u00f3\\u0142ka.B.1&#34;,&#34;rooms_count&#34;:0,&#34;address_id&#34;:2,&#34;created&#34;:&#34;2020-11-01 20:40:46&#34;,&#34;updated&#34;:&#34;2020-11-01 20:46:14&#34;}', 15, NULL, 13, NULL, '2020-11-01 20:47:21'),
(508, 'USER szymonrykala@gmail.com DELETE building DATA {&#34;id&#34;:13,&#34;name&#34;:&#34;\\u015aci\\u00f3\\u0142ka.B.1&#34;,&#34;rooms_count&#34;:0,&#34;address_id&#34;:2,&#34;created&#34;:&#34;2020-11-01 20:40:46&#34;,&#34;updated&#34;:&#34;2020-11-01 20:46:14&#34;}', 15, NULL, 13, NULL, '2020-11-01 20:47:30'),
(509, 'USER szymonrykala@gmail.com DELETE building DATA {&#34;id&#34;:13,&#34;name&#34;:&#34;\\u015aci\\u00f3\\u0142ka.B.1&#34;,&#34;rooms_count&#34;:0,&#34;address_id&#34;:2,&#34;created&#34;:&#34;2020-11-01 20:40:46&#34;,&#34;updated&#34;:&#34;2020-11-01 20:46:14&#34;}', 15, NULL, 13, NULL, '2020-11-01 20:48:18'),
(510, 'USER szymonrykala@gmail.com DELETE room DATA {&#34;id&#34;:49,&#34;name&#34;:&#34;test integralno\\u015bci 2&#34;,&#34;rfid&#34;:&#34;ksjdfhi7ifhfj53&#34;,&#34;building_id&#34;:7,&#34;room_type_id&#34;:5,&#34;seats_count&#34;:350,&#34;floor&#34;:1,&#34;equipment&#34;:[&#34;kreda&#34;,&#34;tablica&#34;],&#34;blockade&#34;:true,&#34;occupied&#34;:false,&#34;updated&#34;:&#34;2020-11-01 21:01:14&#34;,&#34;created&#34;:&#34;2020-10-22 21:35:10&#34;}', 15, NULL, NULL, 49, '2020-11-01 21:08:32'),
(511, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equimpent\":[\"umywalka\",\"zupa\",\"talerze\",\"sofa\"],\"name\":\"B.103 zalewnia\"}', 15, NULL, NULL, 41, '2020-11-01 22:23:57'),
(512, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equimpent\":[\"umywalka\",\"zupa\",\"talerze\",\"sofa\"],\"name\":\"B.103 zalewnia\"}', 15, NULL, NULL, 41, '2020-11-01 22:24:44'),
(513, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equimpent\":[\"umywalka\",\"zupa\",\"talerze\",\"sofa\"],\"name\":\"B.103 zalewnia\"}', 15, NULL, NULL, 41, '2020-11-01 22:33:04'),
(514, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equimpent\":[\"umywalka\",\"zupa\",\"talerze\",\"sofa\"],\"name\":\"B.103 zalewnia\"}', 15, NULL, NULL, 41, '2020-11-01 22:33:32'),
(515, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"sofa\"],\"name\":\"B.103 zalewnia\"}', 15, NULL, NULL, 41, '2020-11-01 22:33:49'),
(516, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"sofa\",\"kot\",\"pies\",\"\\u015bwinia\"],\"name\":\"B.103 chlew\"}', 15, NULL, NULL, 41, '2020-11-01 22:35:23'),
(522, 'USER 15 CREATE address DATA {\"country\":\"test\",\"town\":\"testtt\",\"postal_code\":\"85-791\",\"street\":\"Kaliskiego\",\"number\":\"47\",\"id\":3}', 15, NULL, NULL, NULL, '2020-11-03 13:08:36'),
(523, 'USER szymonrykala@gmail.com UPDATE address DATA {\"postal_code\":\"39-765\"}', 15, NULL, NULL, NULL, '2020-11-03 13:19:58'),
(524, 'USER szymonrykala@gmail.com DELETE address DATA {\"id\":3,\"country\":\"test\",\"town\":\"testtt\",\"postal_code\":\"39-765\",\"street\":\"Kaliskiego\",\"number\":\"47\",\"created\":\"2020-11-03 13:08:36\",\"updated\":\"2020-11-03 13:19:58\"}', 15, NULL, NULL, NULL, '2020-11-03 13:20:36'),
(525, 'USER 15 CREATE address DATA {\"country\":\"test\",\"town\":\"testtt\",\"postal_code\":\"85-791\",\"street\":\"Kaliskiego\",\"number\":\"47\",\"id\":4}', 15, NULL, NULL, NULL, '2020-11-03 13:21:15'),
(526, 'USER szymonrykala@gmail.com DELETE address DATA {\"id\":4,\"country\":\"test\",\"town\":\"testtt\",\"postal_code\":\"85-791\",\"street\":\"Kaliskiego\",\"number\":\"47\",\"created\":\"2020-11-03 13:21:15\",\"updated\":\"2020-11-03 13:21:15\"}', 15, NULL, NULL, NULL, '2020-11-03 13:21:33'),
(527, 'USER 15 CREATE address DATA {\"country\":\"test\",\"town\":\"testtt\",\"postal_code\":\"85-791\",\"street\":\"Kaliskiego\",\"number\":\"47\",\"id\":5}', 15, NULL, NULL, NULL, '2020-11-03 14:26:31'),
(528, 'USER szymonrykala@gmail.com DELETE address DATA {\"id\":5,\"country\":\"test\",\"town\":\"testtt\",\"postal_code\":\"85-791\",\"street\":\"Kaliskiego\",\"number\":\"47\",\"created\":\"2020-11-03 14:26:31\",\"updated\":\"2020-11-03 14:26:31\"}', 15, NULL, NULL, NULL, '2020-11-03 14:26:59'),
(529, 'USER szymonrykala@gmail.com UPDATE access DATA {\"name\":\"super dost\\u0119p\"}', 15, NULL, NULL, NULL, '2020-11-03 14:57:49'),
(530, 'USER szymonrykala@gmail.com UPDATE access DATA {\"name\":\"super dost\\u0119p\",\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false}', 15, NULL, NULL, NULL, '2020-11-03 15:25:06'),
(531, 'USER szymonrykala@gmail.com UPDATE access DATA {\"name\":\"super dost\\u0119p\",\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false}', 15, NULL, NULL, NULL, '2020-11-03 15:25:26'),
(532, 'USER szymonrykala@gmail.com UPDATE access DATA {\"name\":\"super dost\\u0119p\",\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false}', 15, NULL, NULL, NULL, '2020-11-03 15:33:55'),
(533, 'USER szymonrykala@gmail.com UPDATE access DATA {\"name\":\"super dost\\u0119p\",\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false}', 15, NULL, NULL, NULL, '2020-11-03 15:34:22'),
(534, 'USER szymonrykala@gmail.com CREATE access DATA {\"name\":\"test access\",\"access_edit\":\"false\",\"rfid_action\":true,\"buildings_view\":true,\"buildings_edit\":\"xd\",\"logs_view\":\"false\",\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"id\":7}', 15, NULL, NULL, NULL, '2020-11-03 15:34:36'),
(535, 'USER szymonrykala@gmail.com DELETE access DATA {\"id\":7,\"name\":\"test access\",\"rfid_action\":true,\"access_edit\":true,\"buildings_view\":true,\"buildings_edit\":true,\"logs_view\":true,\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"created\":\"2020-11-03 15:34:36\",\"updated\":\"2020-11-03 15:34:36\"}', 15, NULL, NULL, NULL, '2020-11-03 15:38:27'),
(536, 'USER szymonrykala@gmail.com CREATE access DATA {\"name\":\"test access\",\"access_edit\":\"false\",\"rfid_action\":true,\"buildings_view\":true,\"buildings_edit\":\"xd\",\"logs_view\":\"false\",\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"id\":9}', 15, NULL, NULL, NULL, '2020-11-03 15:39:16'),
(537, 'USER szymonrykala@gmail.com DELETE access DATA {\"id\":9,\"name\":\"test access\",\"rfid_action\":true,\"access_edit\":true,\"buildings_view\":true,\"buildings_edit\":true,\"logs_view\":true,\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"created\":\"2020-11-03 15:39:16\",\"updated\":\"2020-11-03 15:39:16\"}', 15, NULL, NULL, NULL, '2020-11-03 16:01:14'),
(538, 'USER szymonrykala@gmail.com CREATE access DATA {\"name\":\"test access\",\"access_edit\":\"false\",\"rfid_action\":true,\"buildings_view\":true,\"buildings_edit\":\"xd\",\"logs_view\":\"false\",\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"id\":11}', 15, NULL, NULL, NULL, '2020-11-03 16:01:23'),
(539, 'USER szymonrykala@gmail.com UPDATE access DATA {\"name\":\"super dost\\u0119p\",\"reservations_access\":true,\"reservations_confirm\":false,\"reservations_edit\":false}', 15, NULL, NULL, NULL, '2020-11-03 16:02:17'),
(540, 'USER szymonrykala@gmail.com UPDATE access DATA {\"reservations_access\":true,\"reservations_confirm\":false,\"reservations_edit\":false}', 15, NULL, NULL, NULL, '2020-11-03 16:03:05'),
(541, 'USER szymonrykala@gmail.com DELETE access DATA {\"id\":11,\"name\":\"test access\",\"rfid_action\":true,\"access_edit\":true,\"buildings_view\":true,\"buildings_edit\":true,\"logs_view\":true,\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":true,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"created\":\"2020-11-03 16:01:23\",\"updated\":\"2020-11-03 16:03:05\"}', 15, NULL, NULL, NULL, '2020-11-03 16:03:16'),
(542, 'USER szymonrykala@gmail.com CREATE room_type DATA {\"name\":\"testowa sala\",\"id\":6}', 15, NULL, NULL, NULL, '2020-11-03 20:51:20'),
(543, 'USER szymonrykala@gmail.com UPDATE room_type DATA {\"name\":\"testowa sala update\"}', 15, NULL, NULL, NULL, '2020-11-03 20:52:05'),
(544, 'USER szymonrykala@gmail.com DELETE room_type DATA {\"id\":6,\"name\":\"testowa sala update\",\"created\":\"2020-11-03 20:51:20\",\"updated\":\"2020-11-03 20:52:05\"}', 15, NULL, NULL, NULL, '2020-11-03 20:53:07'),
(545, 'USER szymonrykala@gmail.com CREATE room_type DATA {\"name\":\"testowa sala\",\"id\":7}', 15, NULL, NULL, NULL, '2020-11-03 20:53:26'),
(546, 'USER szymonrykala@gmail.com DELETE room_type DATA {\"id\":7,\"name\":\"testowa sala\",\"created\":\"2020-11-03 20:53:26\",\"updated\":\"2020-11-03 20:53:26\"}', 15, NULL, NULL, NULL, '2020-11-03 20:53:38'),
(547, 'USER szymonrykala@gmail.com CREATE reservation DATA {\"title\":\"tytu\\u0142 rezerwacji\",\"description\":\"podtytu\\u0142 rezerwacji, opis\",\"start_time\":\"10:00\",\"end_time\":\"11:15\",\"date\":\"2020-11-28\",\"room_id\":4,\"building_id\":3,\"user_id\":15,\"id\":39}', 15, 39, 3, 4, '2020-11-04 00:05:37'),
(548, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"start_time\":\"12:00\",\"end_time\":\"12:10\"}', 15, 39, NULL, NULL, '2020-11-04 00:22:53'),
(549, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"start_time\":\"12:00\",\"end_time\":\"12:10\"}', 15, 39, NULL, NULL, '2020-11-04 00:30:24'),
(550, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"start_time\":\"12:00\",\"end_time\":\"12:10\"}', 15, 39, NULL, NULL, '2020-11-04 00:30:59'),
(551, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"start_time\":\"12:00\",\"end_time\":\"12:10\"}', 15, 39, NULL, NULL, '2020-11-04 00:31:00'),
(552, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"start_time\":\"12:00\",\"end_time\":\"12:10\"}', 15, 39, NULL, NULL, '2020-11-04 00:31:00'),
(553, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"start_time\":\"12:00\",\"end_time\":\"12:10\"}', 15, 39, NULL, NULL, '2020-11-04 00:31:01'),
(554, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"start_time\":\"12:00\",\"end_time\":\"12:11\"}', 15, 39, NULL, NULL, '2020-11-04 00:31:07'),
(555, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"start_time\":\"12:00\",\"end_time\":\"12:11\"}', 15, 39, NULL, NULL, '2020-11-04 00:31:08'),
(556, 'USER szymonrykala@gmail.com DELETE reservation DATA {\"id\":39,\"title\":\"tytu\\u0142 rezerwacji\",\"description\":\"podtytu\\u0142 rezerwacji, opis\",\"room_id\":4,\"building_id\":3,\"user_id\":15,\"start_time\":\"12:00:00\",\"end_time\":\"12:10:00\",\"date\":\"2020-11-28\",\"created\":\"2020-11-04 00:05:37\",\"updated\":\"2020-11-04 00:05:37\",\"confirmed\":false,\"confirming_user_id\":null,\"confirmed_at\":null}', 15, 39, NULL, NULL, '2020-11-04 00:44:27'),
(557, 'USER szymonrykala@gmail.com CREATE room_type DATA {\"name\":\"testowa sala\",\"id\":9}', 15, NULL, NULL, NULL, '2020-11-04 23:25:56'),
(558, 'USER szymonrykala@gmail.com UPDATE room_type DATA {\"name\":\"testowa sala update\"}', 15, NULL, NULL, NULL, '2020-11-04 23:29:50'),
(559, 'USER szymonrykala@gmail.com DELETE room_type DATA {\"id\":9,\"name\":\"testowa sala update\",\"created\":\"2020-11-04 23:25:56\",\"updated\":\"2020-11-04 23:29:50\"}', 15, NULL, NULL, NULL, '2020-11-04 23:30:06'),
(560, 'USER szymonrykala@gmail.com CREATE access DATA {\"name\":\"test access\",\"access_edit\":true,\"rfid_action\":true,\"buildings_view\":true,\"buildings_edit\":false,\"logs_view\":false,\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"id\":12}', 15, NULL, NULL, NULL, '2020-11-04 23:43:05'),
(561, 'USER szymonrykala@gmail.com UPDATE access DATA {\"name\":\"test access update\",\"access_edit\":false,\"rfid_action\":true,\"buildings_view\":false}', 15, NULL, NULL, NULL, '2020-11-04 23:45:47'),
(562, 'USER szymonrykala@gmail.com UPDATE access DATA {\"name\":\"\\u015bci\\u0119ma\",\"access_edit\":false,\"rfid_action\":true,\"buildings_view\":false}', 15, NULL, NULL, NULL, '2020-11-04 23:46:34'),
(563, 'USER szymonrykala@gmail.com UPDATE access DATA {\"nme\":\"\\u015bci\\u0119ma\",\"access_dit\":false,\"rfid_acton\":true,\"building_view\":false}', 15, NULL, NULL, NULL, '2020-11-04 23:47:25'),
(564, 'USER szymonrykala@gmail.com CREATE access DATA {\"name\":\"test access\",\"access_edit\":true,\"rfid_action\":true,\"buildings_view\":true,\"buildings_edit\":false,\"logs_view\":false,\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"id\":13}', 15, NULL, NULL, NULL, '2020-11-04 23:47:28'),
(565, 'USER szymonrykala@gmail.com DELETE access DATA {\"id\":12,\"name\":\"\\u015bci\\u0119ma\",\"rfid_action\":true,\"access_edit\":false,\"buildings_view\":false,\"buildings_edit\":false,\"logs_view\":false,\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"created\":\"2020-11-04 23:43:05\",\"updated\":\"2020-11-04 23:46:34\"}', 15, NULL, NULL, NULL, '2020-11-04 23:47:47'),
(566, 'USER szymonrykala@gmail.com DELETE access DATA {\"id\":13,\"name\":\"test access\",\"rfid_action\":true,\"access_edit\":true,\"buildings_view\":true,\"buildings_edit\":false,\"logs_view\":false,\"logs_edit\":false,\"rooms_view\":true,\"rooms_edit\":false,\"reservations_access\":false,\"reservations_confirm\":false,\"reservations_edit\":false,\"users_edit\":false,\"statistics_view\":true,\"created\":\"2020-11-04 23:47:28\",\"updated\":\"2020-11-04 23:47:28\"}', 15, NULL, NULL, NULL, '2020-11-04 23:47:51'),
(567, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"pies\",\"\\u015bwinia\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:02:26'),
(568, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"pies\",\"\\u015bwinia\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:03:23'),
(569, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"pies\",\"\\u015bwinia\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:03:40'),
(570, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"pies\",\"\\u015bwinia\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:05:23'),
(571, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"pies\",\"\\u015bwinia\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:06:06'),
(572, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"pies\",\"\\u015bwinia\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:06:26'),
(573, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"pies\",\"\\u015bwinia\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:06:58'),
(574, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"pies\",\"\\u015bwinia\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:07:29'),
(575, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\",\"pies\",\"\\u015bwinia\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:10:57'),
(576, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"umywalka\",\"zupa\",\"talerze\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:14:48'),
(577, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"zupa\"],\"name\":\"pok\\u00f3j update v2\"}', 15, NULL, NULL, 58, '2020-11-05 00:54:22'),
(578, 'USER szymonrykala@gmail.com CREATE room DATA {\"name\":\"pozderkgicx v 2\",\"rfid\":\"sdafgw435tgwrw4drgdfgdffdtr\",\"room_type_id\":5,\"seats_count\":35,\"floor\":1,\"equipment\":[\"kreda\",\"tablica\"],\"blockade\":true,\"building_id\":4}', 15, NULL, 4, 63, '2020-11-05 13:29:09'),
(579, 'USER szymonrykala@gmail.com CREATE room DATA {\"name\":\"pozderkghicx v 2\",\"rfid\":\"sdafgw435tgwrw4drgdfhgdffdtr\",\"room_type_id\":5,\"seats_count\":35,\"floor\":1,\"equipment\":[\"kreda\",\"tablica\"],\"blockade\":true,\"building_id\":4}', 15, NULL, 4, 65, '2020-11-05 15:34:40'),
(580, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"zupa\",\"unblockedbitch\"],\"name\":\"<pok\\u00f3j update>\",\"blockade\":false}', 15, NULL, NULL, 63, '2020-11-05 17:57:13'),
(581, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"zupa\",\"unblockedbitch\"],\"name\":\"<pok\\u00f3j update>\",\"blockadegfg\":true}', 15, NULL, NULL, 63, '2020-11-05 17:59:36'),
(582, 'USER szymonrykala@gmail.com UPDATE room DATA {\"nom\":2}', 15, NULL, NULL, 63, '2020-11-05 18:10:04'),
(583, 'USER szymonrykala@gmail.com CREATE room DATA {\"name\":\"pozderkghicx v 3\",\"rfid\":\"sdafgw435tgwrw4drgdfhgdfftdtr\",\"room_type_id\":2,\"seats_count\":35,\"floor\":1,\"equipment\":[\"kreda\",\"tablica\"],\"blockade\":true,\"building_id\":4}', 15, NULL, 4, 66, '2020-11-05 18:57:35'),
(584, 'USER szymonrykala@gmail.com CREATE room DATA {\"name\":\"pozderkghicx v 4\",\"rfid\":\"sdafgw435tgwerw4drgdfhgdfftdtr\",\"room_type_id\":2,\"seats_count\":35,\"floor\":1,\"equipment\":[\"kreda\",\"tablica\"],\"blockade\":true,\"building_id\":4}', 15, NULL, 4, 71, '2020-11-05 20:28:01'),
(585, 'USER szymonrykala@gmail.com UPDATE room DATA {\"equipment\":[\"zupa\",\"unblockedbitch\"],\"name\":\"udpated <>?!@.,()*&\",\"blockade\":false}', 15, NULL, NULL, 71, '2020-11-05 20:30:36'),
(586, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 21:57:17'),
(587, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:26'),
(588, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:30'),
(589, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:31'),
(590, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:33'),
(591, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:35'),
(592, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:36'),
(593, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:38'),
(594, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:39'),
(595, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:42'),
(596, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:44'),
(597, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:45'),
(598, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:46'),
(599, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:01:47'),
(600, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-05 22:08:26'),
(601, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activatioi\"}', 15, NULL, NULL, NULL, '2020-11-07 16:14:07'),
(602, 'USER szymonrykala@gmail.com PERFORMED BAD ACTION DATAactivatioi', 15, NULL, NULL, NULL, '2020-11-07 16:14:07'),
(603, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:14:26'),
(604, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:14:31'),
(605, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:14:31'),
(606, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:18:16'),
(607, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"new_key\"}', 15, NULL, NULL, NULL, '2020-11-07 16:20:06'),
(608, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"new_key\"}', 15, NULL, NULL, NULL, '2020-11-07 16:20:32'),
(609, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"new_key\"}', 15, NULL, NULL, NULL, '2020-11-07 16:21:27'),
(610, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"new_key\"}', 15, NULL, NULL, NULL, '2020-11-07 16:24:51'),
(611, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"new_key\"}', 15, NULL, NULL, NULL, '2020-11-07 16:25:14'),
(612, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"new_key\"}', 15, NULL, NULL, NULL, '2020-11-07 16:32:35'),
(613, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:33:35'),
(614, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:34:45'),
(615, 'USER szymonrykala@gmail.com ACTIVATED DATA {\"activated\":true}', 15, NULL, NULL, NULL, '2020-11-07 16:34:45'),
(616, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:42:38'),
(617, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:44:39'),
(618, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"new_key\"}', 15, NULL, NULL, NULL, '2020-11-07 16:44:50'),
(619, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"new_key\"}', 15, NULL, NULL, NULL, '2020-11-07 16:45:32'),
(620, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:46:40'),
(621, 'USER szymonrykala@gmail.com ACTIVATED DATA {\"activated\":true}', 15, NULL, NULL, NULL, '2020-11-07 16:46:40'),
(622, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:50:34'),
(623, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"new_key\"}', 15, NULL, NULL, NULL, '2020-11-07 16:50:53'),
(624, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:51:22'),
(625, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:51:43'),
(626, 'USER szymonrykala@gmail.com ACTIVATED DATA {\"activated\":true}', 15, NULL, NULL, NULL, '2020-11-07 16:51:43'),
(627, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:57:41'),
(628, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:57:47'),
(629, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:57:48'),
(630, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:57:51'),
(631, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:57:52'),
(632, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"activation\"}', 15, NULL, NULL, NULL, '2020-11-07 16:57:53'),
(633, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"email\"}', 15, NULL, NULL, NULL, '2020-11-07 17:51:28'),
(634, 'USER szymonrykala@gmail.com VERIFIED IN actions DATA {\"action\":\"email\"}', 15, NULL, NULL, NULL, '2020-11-07 22:29:49'),
(635, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"confirmed\":true}', 15, 12, NULL, NULL, '2020-11-08 19:16:33'),
(636, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"confirmed\":true}', 15, 12, NULL, NULL, '2020-11-08 19:21:43'),
(637, 'USER szymonrykala@gmail.com UPDATE reservation DATA {\"confirmed\":true}', 15, 10, NULL, NULL, '2020-11-08 19:43:35'),
(638, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:31:33'),
(639, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:31:37'),
(640, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:31:38'),
(641, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:31:39'),
(642, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:31:40'),
(643, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:31:41'),
(644, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:31:42'),
(645, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:31:58'),
(646, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:32:08'),
(647, 'USER szymonrykala@gmail.com UPDATE room DATA {\"state\":false}', 15, NULL, NULL, 12, '2020-11-09 00:34:04'),
(648, 'USER szymonrykala@gmail.com UPDATE room DATA {\"occupied\":false}', 15, NULL, NULL, 12, '2020-11-09 00:36:23'),
(649, 'USER szymonrykala@gmail.com UPDATE room DATA {\"occupied\":true}', 15, NULL, NULL, 12, '2020-11-09 01:04:52'),
(650, 'USER szymonrykala@gmail.com UPDATE room DATA {\"occupied\":false}', 15, NULL, NULL, 12, '2020-11-09 01:05:06'),
(651, 'USER szymonrykala@gmail.com UPDATE room DATA {\"occupied\":true}', 15, NULL, NULL, 12, '2020-11-09 01:16:38'),
(652, 'USER szymonrykala@gmail.com UPDATE room DATA {\"occupied\":false}', 15, NULL, NULL, 12, '2020-11-09 01:16:52'),
(653, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-09 15:58:01'),
(654, 'USER szymonrykala@gmail.com VERIFIED', 15, NULL, NULL, NULL, '2020-11-09 15:59:58');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `title` tinytext NOT NULL,
  `description` text NOT NULL,
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

INSERT INTO `reservations` (`id`, `title`, `description`, `room_id`, `building_id`, `user_id`, `start_time`, `end_time`, `date`, `created`, `updated`, `confirmed`, `confirming_user_id`, `confirmed_at`) VALUES
(4, 'rezerwacja próbna', 'test - próbna rezerwacja', 2, 2, 10, '12:00:00', '18:30:00', '2020-10-19', '2020-08-24 22:11:19', '2020-10-19 20:09:51', 1, NULL, NULL),
(7, 'rezerwacja próbna', 'test - próbna rezerwacja', 3, 3, 8, '12:00:00', '13:30:00', '2020-08-12', '2020-08-24 22:11:30', '2020-08-24 22:11:30', 0, NULL, NULL),
(10, 'rezerwacja próbna', 'test - próbna rezerwacja', 3, 3, 8, '12:00:00', '13:30:00', '2020-08-14', '2020-08-24 22:11:26', '2020-08-24 22:11:26', 1, 15, NULL),
(12, 'rezerwacja v1', 'podtytuł rezerwacji, opis', 4, 2, 8, '10:00:00', '11:15:00', '2020-08-11', '2020-08-25 21:27:20', '2020-08-25 21:27:20', 1, 15, NULL),
(13, 'rezerwacja v1', 'podtytuł rezerwacji, opis', 4, 2, 8, '10:00:00', '11:15:00', '2020-08-28', '2020-08-25 21:28:04', '2020-08-25 21:28:04', 0, NULL, NULL),
(14, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 4, 2, 8, '11:00:00', '11:20:00', '2020-08-27', '2020-08-28 00:37:49', '2020-08-28 00:37:49', 0, NULL, NULL),
(16, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 12, 6, 8, '11:00:00', '11:45:00', '2020-10-02', '2020-10-01 17:44:14', '2020-10-01 17:44:14', 0, NULL, NULL),
(17, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 12, 6, 8, '12:00:00', '12:45:00', '2020-10-02', '2020-10-01 17:47:05', '2020-10-01 17:47:05', 0, NULL, NULL),
(18, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 11, 6, 8, '12:00:00', '12:45:00', '2020-10-02', '2020-10-01 17:47:18', '2020-10-01 17:47:18', 0, NULL, NULL),
(19, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 12, 6, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:47:50', '2020-10-01 17:47:50', 0, NULL, NULL),
(20, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 11, 6, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:47:55', '2020-10-01 17:47:55', 0, NULL, NULL),
(21, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 14, 7, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:48:41', '2020-10-01 17:48:41', 0, NULL, NULL),
(22, 'rezerwacja v3', 'podtytuł rezerwacji, update rezerwacji', 15, 7, 8, '14:00:00', '14:45:00', '2020-10-02', '2020-10-01 17:48:45', '2020-10-01 17:48:45', 0, NULL, NULL),
(24, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '15:00:00', '2020-10-21', '2020-10-08 15:03:37', '2020-10-08 15:03:37', 0, NULL, NULL),
(25, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '15:00:00', '2020-10-21', '2020-10-08 15:04:08', '2020-10-08 15:04:08', 0, NULL, NULL),
(26, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '15:00:00', '2020-10-21', '2020-10-08 15:05:02', '2020-10-08 15:05:02', 0, NULL, NULL),
(27, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 11, '14:45:00', '15:00:00', '2020-10-21', '2020-10-08 15:05:06', '2020-10-08 15:05:06', 0, NULL, NULL),
(28, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '15:00:00', '2020-10-21', '2020-10-08 15:05:07', '2020-10-08 15:05:07', 0, NULL, NULL),
(29, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '15:00:00', '2020-10-21', '2020-10-08 15:05:08', '2020-10-08 15:05:08', 0, NULL, NULL),
(30, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '15:00:00', '2020-10-21', '2020-10-08 15:09:18', '2020-10-08 15:09:18', 0, NULL, NULL),
(31, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '16:00:00', '2020-10-21', '2020-10-08 15:10:43', '2020-10-08 15:10:43', 0, NULL, NULL),
(32, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '16:00:00', '2020-10-21', '2020-10-08 15:11:17', '2020-10-08 15:11:17', 0, NULL, NULL),
(33, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '16:00:00', '2020-10-21', '2020-10-08 15:12:26', '2020-10-08 15:12:26', 0, NULL, NULL),
(34, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '16:00:00', '2020-10-21', '2020-10-08 15:12:45', '2020-10-08 15:12:45', 0, NULL, NULL),
(35, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '16:00:00', '2020-10-21', '2020-10-08 15:14:04', '2020-10-08 15:14:04', 0, NULL, NULL),
(36, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '15:30:00', '2020-10-21', '2020-10-08 15:14:14', '2020-10-08 15:14:14', 0, NULL, NULL),
(37, 'rezerwacja v4', 'super fajny tytuł i opis rezerwacji kt&oacute;ry powinien zawierać kropki, przecinki.:a czasem nawet &quot;cytat&quot;', 15, 7, 8, '14:45:00', '15:15:00', '2020-10-21', '2020-10-08 15:14:20', '2020-10-08 15:14:20', 0, NULL, NULL),
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
(2, 'B001', '2365467', 3, 1, 30, 0, 'tablica;rzutnik;kreda', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(3, 'B201', '2563', 3, 5, 60, 2, 'rzutnik,kreda,tablica', 1, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(4, 'B101', '134234', 3, 2, 60, 1, 'rzutnik,kreda,tablica', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(5, 'A001', '43523635', 2, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(6, 'A201', '5243657', 2, 2, 60, 2, 'rzutnik,kreda,tablica', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(7, 'A101', '58579', 2, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(8, 'C001', '7807863', 4, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(9, 'C201', '254634', 2, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(10, 'C101', '68567', 2, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(11, 'D001', '2111514', 6, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(12, 'D101', '2346546', 6, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0, '2020-11-09 01:16:52', '2020-10-22 21:35:10'),
(13, 'D201', '236345', 6, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(14, 'E001', '657586', 7, 1, 30, 0, 'tablica,rzutnik,kreda', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(15, 'E101', '12345236', 7, 5, 60, 1, 'rzutnik,kreda,tablica', 0, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(16, 'E201', '43674845', 7, 2, 60, 2, 'rzutnik,kreda,tablica', 1, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(41, 'B.103 chlew', '257568576', 8, 5, 350, 1, ';umywalka;zupa;talerze;sofa;kot;pies;świnia', 1, 0, '2020-11-08 23:52:18', '2020-10-22 21:35:10'),
(55, 'pozderki', '23653476', 4, 5, 60, 1, 'Array', 1, 0, '2020-11-08 23:52:18', '2020-11-01 22:57:49'),
(56, 'pokój update', '125236546 ', 4, 5, 35, 1, 'Array', 1, 0, '2020-11-08 23:52:18', '2020-11-01 22:59:47'),
(58, 'pokój update v2', '4575476', 4, 5, 35, 1, ';zupa', 1, 0, '2020-11-08 23:52:18', '2020-11-01 23:20:59'),
(60, 'pozderkicx v 2', '3246546', 4, 5, 35, 1, ';kreda;tablica', 1, 0, '2020-11-08 23:52:18', '2020-11-05 13:19:52'),
(63, '&lt;pok&oacute;j update&gt;', '56988', 4, 5, 35, 1, ';zupa;unblockedbitch', 0, 0, '2020-11-08 23:52:18', '2020-11-05 13:29:09'),
(65, 'pozderkghicx v 2', '578978', 4, 5, 35, 1, ';kreda;tablica', 1, 0, '2020-11-08 23:52:18', '2020-11-05 15:34:40'),
(66, 'pozderkghicx v 3', '6890789', 4, 2, 35, 1, ';kreda;tablica', 1, 0, '2020-11-08 23:52:18', '2020-11-05 18:57:35'),
(71, 'udpated &lt;&gt;?!@.,()*&amp;', '253465344', 4, 2, 35, 1, ';zupa;unblockedbitch', 0, 0, '2020-11-08 23:52:18', '2020-11-05 20:28:01');

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
  `access_id` int(11) NOT NULL DEFAULT 1,
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
(10, 1, 'Szymon', 'Rykała', '$2y$12$efkNjWJHZwkgSnWs4ExVdON47kma2OAw0q/2E7ivTf9qIVvNAN.HO', '2020-11-01 19:19:34', 'szymonrykala1214@gmail.com', '2020-10-14 22:02:20', 1, 5, '2020-10-14 22:02:20', '1'),
(11, 1, 'updated test', 'testSurname', '$2y$12$FXd5UMeI5hpVTUyTUjqRyOCYUNYRdgdMxzw1tC/dNwB.2ecaKgP.K', '2020-10-21 01:02:18', 'testupdate@gmail.com', '2020-10-21 01:02:18', 0, 1, '2020-10-18 13:15:53', 'NONE_NONE'),
(12, 1, 'testName', 'testSurname', '$2y$12$bXhBq9HzVmwWXd76dLee4uddfCQWnqbwm3zDMvOxrtopQjDDCWasW', '2020-11-05 11:30:06', 'test@gmail.com', '2020-10-19 19:39:15', 0, 0, '2020-10-18 22:56:46', '1ePjDzo9'),
(15, 1, 'Szymon', 'Rykała', '$2y$12$EltPMoXvEfzCXSs3i/YT6OwI9n0/XIFxhNMxUGJHFko4v31JfOAEe', '2020-11-07 16:51:43', 'szymonrykala@gmail.com', '2020-11-01 19:18:18', 1, 0, '2020-11-01 19:18:18', NULL);

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
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `accesses`
--
ALTER TABLE `accesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT dla tabeli `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT dla tabeli `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=655;

--
-- AUTO_INCREMENT dla tabeli `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT dla tabeli `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT dla tabeli `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
