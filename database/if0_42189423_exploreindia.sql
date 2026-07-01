-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql307.infinityfree.com
-- Generation Time: Jul 01, 2026 at 04:31 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42189423_exploreindia`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_login`
--

CREATE TABLE `admin_login` (
  `a_id` int(5) NOT NULL,
  `a_uname` varchar(15) NOT NULL,
  `a_password` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_login`
--

INSERT INTO `admin_login` (`a_id`, `a_uname`, `a_password`) VALUES
(1, 'Vasudev', '7cf92cf0f2b5c8bafa7bd7e58f5d6495');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `c_id` int(5) NOT NULL,
  `s_id` int(5) NOT NULL,
  `c_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`c_id`, `s_id`, `c_name`) VALUES
(1, 14, 'Jammu'),
(2, 14, 'Srinagar'),
(3, 14, 'Gulmarg'),
(4, 14, 'Sonamarg'),
(5, 14, 'Pahalgam'),
(6, 14, 'Patnitop'),
(7, 7, 'Amritsar'),
(8, 7, 'Jalandhar'),
(9, 7, 'Ludhiana'),
(10, 7, 'Patiala'),
(11, 3, 'Dharamshala'),
(12, 3, 'Shimla'),
(13, 3, 'Mandi'),
(14, 3, 'Manali'),
(15, 3, 'kullu'),
(16, 8, 'Jaipur'),
(17, 8, 'Jodhpur'),
(18, 8, 'Ajmer'),
(19, 8, 'Udaipur'),
(20, 8, 'Kumbhalgarh'),
(21, 1, 'Bicholim'),
(22, 1, 'Mapusa'),
(23, 1, 'Margao'),
(24, 1, 'Panaji'),
(25, 1, 'Ponda'),
(26, 2, 'Ahmedabad'),
(27, 2, 'Rajkot'),
(28, 2, 'Vadodara'),
(29, 2, 'Surat'),
(30, 2, 'Bhavnagar'),
(31, 4, 'Kochi'),
(32, 4, 'Kovalam'),
(33, 4, 'Kunnur'),
(34, 4, 'Munnar'),
(35, 4, 'Thekkady'),
(36, 4, 'Thiruvananthapuram'),
(37, 6, 'Amarkantaka'),
(38, 6, 'Bhopal'),
(39, 6, 'Indore'),
(40, 6, 'Jabalpur'),
(41, 6, 'Mandu'),
(42, 6, 'Omkareshwar'),
(43, 5, 'Mumbai'),
(44, 5, 'Nashik'),
(45, 5, 'Pune'),
(46, 5, 'Aurangabad'),
(47, 5, 'Kolhapur'),
(48, 9, 'Gangtok'),
(49, 9, 'Lachung'),
(50, 9, 'Namchi'),
(51, 9, 'Pelling'),
(52, 9, 'Yuksom'),
(53, 10, 'Chennai'),
(54, 10, 'Kanchipuram'),
(55, 10, 'Ooty'),
(56, 10, 'Rameshwaram'),
(57, 10, 'Triupur'),
(58, 11, 'Agra'),
(59, 11, 'Kanpur'),
(60, 11, 'Lucknow'),
(61, 11, 'Mathura'),
(62, 11, 'Varanasi'),
(63, 12, 'Badrinath'),
(64, 12, 'Dehradun'),
(65, 12, 'Haridwar'),
(66, 12, 'Nainital'),
(67, 12, 'Rishikesh'),
(68, 13, 'Asansol'),
(69, 13, 'Cooch Behar'),
(70, 13, 'Howrah'),
(71, 13, 'Kolkata'),
(72, 13, 'Siliguri'),
(73, 14, 'Amarnath'),
(74, 8, 'Jaisalmer');

-- --------------------------------------------------------

--
-- Table structure for table `customer_details`
--

CREATE TABLE `customer_details` (
  `cust_id` int(5) NOT NULL,
  `cust_fname` varchar(10) NOT NULL,
  `cust_lname` varchar(10) NOT NULL,
  `cust_gender` varchar(10) NOT NULL,
  `cust_email` varchar(30) NOT NULL,
  `cust_password` varchar(255) NOT NULL,
  `cust_mobile` bigint(10) NOT NULL,
  `cust_address` varchar(100) NOT NULL,
  `cust_birthdate` date NOT NULL,
  `cust_state` varchar(10) NOT NULL,
  `cust_city` varchar(10) NOT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_blocked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer_details`
--

INSERT INTO `customer_details` (`cust_id`, `cust_fname`, `cust_lname`, `cust_gender`, `cust_email`, `cust_password`, `cust_mobile`, `cust_address`, `cust_birthdate`, `cust_state`, `cust_city`, `otp_code`, `otp_expiry`, `is_verified`, `is_blocked`) VALUES
(10, 'Vasudev', 'Parmar', 'Male', 'parmarvasudev63@gmail.com', '$2y$10$E.hBKNcNNODgpdrlemtbZuXQji0KmWW/b5eQLM1ZVwMZPqqGYJh1u', 8980629376, 'E15 Pramukh Park', '2001-09-10', '2', '26', NULL, NULL, 1, 0),
(11, 'Ridhhi', 'Patel', 'Female', 'parmarvasudev8980@gmail.com', '$2y$10$o3D0OihcFbj8j2FV.xGkYOux.QX9gn7WamJmM6fZ9/WuNzgqoz/8q', 8980629376, 'E15 Pramukh Park 1 Opp. Ambalal Park 2 Gorwa Vadodara', '2026-07-01', '8', '17', NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customize_booking`
--

CREATE TABLE `customize_booking` (
  `id` int(11) NOT NULL,
  `cust_id` int(11) DEFAULT NULL,
  `h_id` varchar(200) DEFAULT NULL,
  `localg_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `day` varchar(5) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Confirmed',
  `booking_ref` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customize_booking`
--

INSERT INTO `customize_booking` (`id`, `cust_id`, `h_id`, `localg_id`, `date`, `day`, `amount`, `payment_id`, `status`, `booking_ref`, `created_at`) VALUES
(9, 10, '35,34,37,38', 1, '2026-07-17', '4', '108.00', 'pay_T7uu07w1rSmgtZ', 'Confirmed', 'CUS260630535', '2026-06-30 16:58:20'),
(8, 10, '7,5,6,9,8', 3, '2026-07-27', '5', '135.00', 'pay_T6BKnlaNv5u1o2', 'Confirmed', 'CUS260626216', '2026-06-26 07:44:44'),
(7, 10, '12,16,14', 2, '2026-07-07', '3', '81.00', 'pay_T6BFvf0rBCWOID', 'Confirmed', 'CUS260626449', '2026-06-26 07:40:22');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `f_id` int(5) NOT NULL,
  `cust_id` int(5) NOT NULL,
  `message` varchar(200) NOT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `type` varchar(50) DEFAULT 'website'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`f_id`, `cust_id`, `message`, `rating`, `type`) VALUES
(4, 10, 'Flight booking was super smooth and seat selection is amazing!', 5, 'transport'),
(5, 10, 'Train search gave real results, very impressed!', 4, 'transport'),
(6, 10, 'Cab fare calculation was accurate and booking was instant.', 5, 'transport'),
(7, 10, 'Customized my own package in minutes, loved the hotel selection!', 5, 'customized_package'),
(8, 10, 'The customize feature is very flexible and easy to use.', 4, 'customized_package'),
(9, 10, 'Got exactly what I wanted with the custom package builder.', 5, 'customized_package'),
(10, 10, 'Char Dham package was perfectly planned with all details.', 5, 'special_package'),
(11, 10, 'Kerala backwaters package exceeded my expectations!', 5, 'special_package'),
(12, 10, 'Rajasthan special package was great value for money.', 4, 'special_package'),
(13, 10, 'Local guide was very knowledgeable and helpful throughout.', 5, 'local_guide'),
(14, 10, 'Guide knew every corner of the city, amazing experience!', 5, 'local_guide'),
(15, 10, 'The website is very easy to navigate and beautifully designed.', 5, 'website'),
(16, 10, 'App is smooth and all features work perfectly.', 4, 'website'),
(17, 10, 'Local Guide was too good', 5, 'local_guide');

-- --------------------------------------------------------

--
-- Table structure for table `hotel`
--

CREATE TABLE `hotel` (
  `h_id` int(5) NOT NULL,
  `h_name` varchar(30) NOT NULL,
  `h_price` int(10) NOT NULL,
  `s_id` int(11) NOT NULL,
  `c_id` varchar(100) NOT NULL,
  `h_phone` bigint(10) NOT NULL,
  `h_rate` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hotel`
--

INSERT INTO `hotel` (`h_id`, `h_name`, `h_price`, `s_id`, `c_id`, `h_phone`, `h_rate`) VALUES
(1, 'Comesum Hotel', 27, 12, '63', 9834587621, 4),
(2, 'Hotel Patnitop Heights', 27, 14, '6', 8658685834, 4),
(3, 'Hotel Jammu Palace', 27, 14, '1', 9124774848, 3),
(4, 'Pine View', 27, 14, '2', 8234645649, 3),
(5, 'Hotel Ramada', 27, 8, '16', 9385757467, 4),
(6, 'Hotel Shikha', 27, 8, '17', 8762391567, 2),
(7, 'Plaza Inn', 27, 8, '18', 8345286432, 3),
(8, 'Hotel The Fnn', 27, 8, '19', 9456382346, 4),
(9, 'Hotel Tiger Vilas Ranhambore', 27, 8, '20', 8765412345, 3),
(10, 'Grand Hyatt Goa', 27, 1, '21', 9564285747, 3),
(11, 'Goa Marriott Resort', 27, 1, '24', 9564285747, 4),
(12, 'Radisson Blu Hotel', 27, 2, '26', 9564285747, 3),
(13, 'Welcome Hotel', 27, 2, '28', 9564285747, 5),
(14, 'Hotel Bhavani Palace', 27, 2, '27', 9564285747, 3),
(15, 'Hotel Cambay Grand', 27, 2, '29', 9564285747, 4),
(16, 'The Fern Residency', 27, 2, '30', 9564285747, 4),
(17, 'Sun Park Resort Manali', 27, 3, '14', 9564285747, 3),
(18, 'Summit Le Royale Hotel', 27, 3, '13', 9564285747, 3),
(19, 'Sterling Kufri', 27, 3, '12', 9564285747, 3),
(20, 'Amritara The Zion', 27, 3, '15', 9564285747, 3),
(21, 'The Himalayan', 27, 3, '11', 9564285747, 4),
(22, 'Trident Hotel ', 27, 4, '31', 9564285747, 3),
(23, 'Travancore Island Resort', 27, 4, '32', 9564285747, 4),
(24, 'Hotel Aramana', 27, 4, '33', 9564285747, 3),
(25, 'River Plaza Retreat', 27, 4, '35', 9564285747, 3),
(26, 'Hotel KR Grand Residency', 27, 4, '36', 9564285747, 3),
(27, 'Manyaa', 27, 11, '58', 9564285747, 4),
(28, 'The Fern Residency Bhopal', 27, 6, '38', 9564285747, 4),
(29, 'WOW Hotel', 27, 6, '39', 9564285747, 4),
(30, 'Pride By Samrat', 27, 6, '40', 9564285747, 3),
(31, 'Tiger Inn', 27, 6, '41', 9564285747, 3),
(32, 'MPT Rock End Manor', 27, 6, '42', 9564285747, 4),
(33, 'Ahilya Fort', 27, 6, '37', 9564285747, 4),
(34, 'Zikme Hotel', 27, 9, '50', 9564285747, 3),
(35, 'Mayfair Hotel', 27, 9, '48', 9564285747, 4),
(36, 'Delight Lachen Heritage', 27, 9, '49', 9564285747, 4),
(37, 'BlueBen Alpine', 27, 9, '51', 9564285747, 4),
(38, 'Lemon Tree Hotel', 27, 9, '52', 9564285747, 4),
(39, 'Hyatt Regency', 27, 7, '7', 9564285747, 3),
(40, 'Park Plaza', 27, 7, '9', 9564285747, 4),
(41, 'Taj Swarna', 27, 7, '8', 9564285747, 3),
(42, 'JW Marriott Hotel', 27, 7, '10', 9564285747, 4),
(43, 'Hotel Mounth Malik', 27, 14, '73', 9564285747, 4),
(44, 'Lemon Tree Hotel', 27, 14, '3', 9564285747, 4),
(45, 'Bhagirathi Sadan', 27, 12, '64', 9564285747, 4),
(46, 'Hotel Him Darshan', 27, 12, '67', 9564285747, 4),
(47, 'Shivalik Valley Resort', 27, 12, '65', 9564285747, 4),
(48, 'Hotel Kalyan', 27, 8, '74', 9564285747, 4),
(49, 'Hotel Regneta In', 27, 10, '53', 9564285747, 4),
(50, 'Hotel Suba Elite', 28, 10, '54', 9564285747, 3),
(51, 'Hotel Aashirwad', 27, 10, '55', 9564285747, 4);

-- --------------------------------------------------------

--
-- Table structure for table `local_guide`
--

CREATE TABLE `local_guide` (
  `localg_id` int(10) NOT NULL,
  `localg_name` varchar(15) NOT NULL,
  `localg_mobile` bigint(12) NOT NULL,
  `localg_email` varchar(30) NOT NULL,
  `localg_password` varchar(15) NOT NULL,
  `localg_language` varchar(50) NOT NULL,
  `s_id` int(15) NOT NULL,
  `c_id` varchar(20) NOT NULL,
  `h_id` int(5) NOT NULL,
  `localg_approve` int(5) NOT NULL,
  `localg_emailverify` int(5) NOT NULL,
  `status` int(1) NOT NULL,
  `activation_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `local_guide`
--

INSERT INTO `local_guide` (`localg_id`, `localg_name`, `localg_mobile`, `localg_email`, `localg_password`, `localg_language`, `s_id`, `c_id`, `h_id`, `localg_approve`, `localg_emailverify`, `status`, `activation_token`) VALUES
(1, 'Vasudev', 8401333436, 'parmarvasudev8980@gmail.com', 'Vasudev63@@', 'Hindi', 9, '', 0, 1, 1, 0, NULL),
(2, 'rajuParmar', 7046453962, 'parmarvasudev63@gmail.com', 'Rajesh@gujarat', 'English,Hindi,Gujarati', 2, '', 0, 1, 1, 0, NULL),
(3, 'Abhijeet', 9428131465, 'Abhijeet@gmail.com', 'Abhijeet@rajast', 'Hindi,English', 8, '', 0, 1, 1, 0, NULL),
(4, 'Arjun Rampal', 8512367425, 'Arjun@gmail.com', 'Arjun@madhyapra', 'Hindi,English,Bagheli,Awadhi', 6, '', 0, 1, 1, 0, NULL),
(5, 'Parvez Ahmad', 7401239552, 'Parvez@gmail.com', 'Parvez@jammukas', 'English,Hindi,Kashmiri,Urdu', 14, '', 0, 1, 1, 0, NULL),
(6, 'Suraj Tal', 9485123655, 'Suraj@gmail.com', 'Suraj@himachalp', 'Pahari,Hindi,English', 3, '', 0, 1, 1, 0, NULL),
(8, 'Mukul', 8980693275, 'Mukul@gmail.com', 'Mukul@kerala', 'Malayalam,Tamil,Hindi', 4, '', 0, 1, 1, 0, NULL),
(9, 'Anbarasi', 8745123688, 'Anbarasi@gmail.com', 'Anbarasi@tamiln', 'Tamil,Hindi,English', 10, '', 0, 1, 1, 0, NULL),
(10, 'Shushant', 8987253435, 'Shushant@gmail.com', 'Shushant@sikkim', 'Nepali,Hindi,English', 5, '', 0, 1, 1, 0, NULL),
(11, 'Karan Rawat', 8980629376, 'parmarvasudev2001@gmail.com', 'wekfnff32323#@#', 'Hindi English', 12, '', 0, 1, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `local_guide_request`
--

CREATE TABLE `local_guide_request` (
  `lgr_id` int(10) NOT NULL,
  `localg_id` int(10) NOT NULL,
  `cust_id` int(10) NOT NULL,
  `s_id` int(15) NOT NULL,
  `h_id` varchar(500) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `local_guide_request`
--

INSERT INTO `local_guide_request` (`lgr_id`, `localg_id`, `cust_id`, `s_id`, `h_id`, `date`) VALUES
(11, 11, 10, 12, '45,1,46,47', '2026-07-10'),
(12, 1, 10, 9, '35,34,37,38', '2026-07-17');

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `pa_id` int(5) NOT NULL,
  `pa_name` varchar(30) NOT NULL,
  `s_id` int(5) NOT NULL,
  `h_id` varchar(100) NOT NULL,
  `price` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `package`
--

INSERT INTO `package` (`pa_id`, `pa_name`, `s_id`, `h_id`, `price`) VALUES
(1, 'Jammu and Kashmir', 14, '3,43,2,44,4', 25),
(2, 'Himachal Pradesh', 3, '20,19,18,17,21', 30),
(3, 'Char Dham ', 12, '45,1,46,47', 27),
(4, 'Madhya Pradesh', 6, '33,32,30,28,31,29', 30),
(5, 'Gujarat', 2, '14,15,12,16,13', 28),
(6, 'Rajasthan', 8, '48,5,6,8,9,7', 29),
(7, 'Kerela', 4, '24,26,25,23,22', 25),
(8, 'Tamil Nadu', 10, '51,49,50', 30),
(9, 'Sikkim', 9, '37,36,38,35,34', 25);

-- --------------------------------------------------------

--
-- Table structure for table `predefine_booking`
--

CREATE TABLE `predefine_booking` (
  `booking_id` int(11) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `pa_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'Confirmed',
  `booking_ref` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `predefine_booking`
--

INSERT INTO `predefine_booking` (`booking_id`, `cust_id`, `pa_id`, `date`, `payment_id`, `amount`, `status`, `booking_ref`, `created_at`) VALUES
(1, 10, 4, '2026-06-30', 'pay_T6BcUJgCUJ7K5n', '30.00', 'Confirmed', 'PRE260626697', '2026-06-26 08:01:31'),
(3, 10, 3, '2026-07-10', 'pay_T7uqorKnLJPDWI', '27.00', 'Confirmed', 'PRE260630702', '2026-06-30 16:55:17');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `s_id` int(5) NOT NULL,
  `s_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`s_id`, `s_name`) VALUES
(1, 'Goa'),
(2, 'Gujarat'),
(3, 'Himachal Pradesh'),
(4, 'Kerela'),
(5, 'Maharashtra'),
(6, 'Madhya Pradesh'),
(7, 'Punjab'),
(8, 'Rajasthan'),
(9, 'Sikkim'),
(10, 'Tamil Nadu'),
(11, 'Uttar Pradesh'),
(12, 'Uttarakhand'),
(13, 'West Bengal'),
(14, 'Jammu and Kashmir');

-- --------------------------------------------------------

--
-- Table structure for table `transport_bookings`
--

CREATE TABLE `transport_bookings` (
  `booking_id` int(11) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `from_city` varchar(100) DEFAULT NULL,
  `to_city` varchar(100) DEFAULT NULL,
  `travel_date` date DEFAULT NULL,
  `details` text DEFAULT NULL,
  `fare` decimal(10,2) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `booking_ref` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Confirmed',
  `booked_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transport_bookings`
--

INSERT INTO `transport_bookings` (`booking_id`, `cust_id`, `type`, `from_city`, `to_city`, `travel_date`, `details`, `fare`, `payment_id`, `booking_ref`, `status`, `booked_at`) VALUES
(17, 10, 'flight', 'DEL', 'DED', '2026-06-30', '{\"type\":\"flight\",\"flight_no\":\"6E6582\",\"airline\":\"IndiGo\",\"from\":\"DEL\",\"to\":\"DED\",\"dep_time\":\"14:25\",\"arr_time\":\"15:15\",\"duration\":\"0h 50m\",\"date\":\"2026-06-30\",\"passengers\":\"1\",\"class\":\"economy\",\"price\":\"2499\",\"seats\":\"10A\",\"razorpay_payment_id\":\"pay_T6H4YUC2OJnugo\",\"pay_type\":\"flight\"}', '2499.00', 'pay_T6H4YUC2OJnugo', 'FLI260626672', 'Confirmed', '2026-06-26 13:21:32'),
(15, 10, 'train', 'Delhi', 'Dehradun', '2026-06-30', '{\"train_no\":\"12301\",\"train_name\":\"Rajdhani Express\",\"from\":\"Delhi\",\"to\":\"Dehradun\",\"dep_time\":\"16:55\",\"arr_time\":\"10:00\",\"duration\":\"17h 05m\",\"date\":\"2026-06-30\",\"type\":\"train\",\"passengers\":\"1\",\"class\":\"SL\",\"price\":\"450\",\"razorpay_payment_id\":\"pay_T6GyNQI2ZHKe6z\",\"pay_type\":\"train\"}', '450.00', 'pay_T6GyNQI2ZHKe6z', 'TRA260626168', 'Confirmed', '2026-06-26 13:15:45'),
(14, 10, 'cab', 'Delhi', 'Dehradun', '2026-06-27', '{\"type\":\"cab\",\"cab_type\":\"mini\",\"cab_name\":\"Mini\",\"from\":\"Delhi\",\"to\":\"Dehradun\",\"date\":\"2026-06-27\",\"time\":\"10:00\",\"distance\":\"203\",\"duration\":\"3h 24m\",\"price\":\"2436\",\"razorpay_payment_id\":\"pay_T6GoNg0eGAcjzB\",\"pay_type\":\"cab\"}', '2436.00', 'pay_T6GoNg0eGAcjzB', 'CAB260626974', 'Confirmed', '2026-06-26 13:06:12');

-- --------------------------------------------------------

--
-- Table structure for table `transport_cities`
--

CREATE TABLE `transport_cities` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `city_code` varchar(10) DEFAULT NULL,
  `airport_code` varchar(5) DEFAULT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transport_cities`
--

INSERT INTO `transport_cities` (`city_id`, `city_name`, `city_code`, `airport_code`, `lat`, `lng`) VALUES
(1, 'Mumbai', 'MUM', 'BOM', '19.07609000', '72.87723000'),
(2, 'Delhi', 'DEL', 'DEL', '28.63576000', '77.22445000'),
(3, 'Bengaluru', 'BLR', 'BLR', '12.97194000', '77.59369000'),
(4, 'Chennai', 'MAA', 'MAA', '13.08268000', '80.27071000'),
(5, 'Kolkata', 'CCU', 'CCU', '22.57264000', '88.36389000'),
(6, 'Hyderabad', 'HYD', 'HYD', '17.38405000', '78.45636000'),
(7, 'Ahmedabad', 'AMD', 'AMD', '23.02579000', '72.58727000'),
(8, 'Pune', 'PNQ', 'PNQ', '18.51957000', '73.85535000'),
(9, 'Jaipur', 'JAI', 'JAI', '26.91245000', '75.78727000'),
(10, 'Surat', 'STV', 'STV', '21.19594000', '72.83020000'),
(11, 'Lucknow', 'LKO', 'LKO', '26.84670000', '80.94620000'),
(12, 'Kanpur', 'KNU', NULL, '26.46523000', '80.34986000'),
(13, 'Nagpur', 'NAG', 'NAG', '21.14631000', '79.08491000'),
(14, 'Indore', 'IDR', 'IDR', '22.71792000', '75.85772000'),
(15, 'Bhopal', 'BPL', 'BPL', '23.25969000', '77.41261000'),
(16, 'Vadodara', 'BRC', 'BDQ', '22.30720000', '73.18176000'),
(17, 'Patna', 'PAT', 'PAT', '25.59408000', '85.13563000'),
(18, 'Varanasi', 'VNS', 'VNS', '25.31668000', '82.97390000'),
(19, 'Agra', 'AGR', 'AGR', '27.17667000', '78.00807000'),
(20, 'Guwahati', 'GAU', 'GAU', '26.14447000', '91.73616000'),
(21, 'Kochi', 'COK', 'COK', '9.93170000', '76.26730000'),
(22, 'Thiruvananthapuram', 'TRV', 'TRV', '8.52410000', '76.93580000'),
(23, 'Coimbatore', 'CBE', 'CJB', '11.00168000', '76.96612000'),
(24, 'Visakhapatnam', 'VTZ', 'VTZ', '17.68009000', '83.21938000'),
(25, 'Amritsar', 'ATQ', 'ATQ', '31.63400000', '74.87230000'),
(26, 'Chandigarh', 'IXC', 'IXC', '30.73310000', '76.77910000'),
(27, 'Jodhpur', 'JDH', 'JDH', '26.28473000', '73.02430000'),
(28, 'Udaipur', 'UDR', 'UDR', '24.57130000', '73.69143000'),
(29, 'Goa', 'GOI', 'GOI', '15.49820000', '73.82810000'),
(30, 'Bhubaneswar', 'BBI', 'BBI', '20.29600000', '85.82450000'),
(31, 'Ranchi', 'IXR', 'IXR', '23.34360000', '85.30960000'),
(32, 'Raipur', 'RPR', 'RPR', '21.25190000', '81.62960000'),
(33, 'Dehradun', 'DED', 'DED', '30.31650000', '78.03220000'),
(34, 'Jammu', 'IXJ', 'IXJ', '32.73500000', '74.87000000'),
(35, 'Srinagar', 'SXR', 'SXR', '34.08360000', '74.79730000'),
(36, 'Leh', 'IXL', 'IXL', '34.15440000', '77.57720000'),
(37, 'Shimla', 'SLV', NULL, '31.10480000', '77.17340000'),
(38, 'Manali', 'MNL', NULL, '32.23960000', '77.18890000'),
(39, 'Darjeeling', 'DAR', NULL, '27.03600000', '88.26300000'),
(40, 'Mysuru', 'MYQ', 'MYQ', '12.29580000', '76.63940000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_login`
--
ALTER TABLE `admin_login`
  ADD PRIMARY KEY (`a_uname`),
  ADD UNIQUE KEY `a_id` (`a_id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `s_id` (`s_id`);

--
-- Indexes for table `customer_details`
--
ALTER TABLE `customer_details`
  ADD PRIMARY KEY (`cust_id`);

--
-- Indexes for table `customize_booking`
--
ALTER TABLE `customize_booking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`f_id`);

--
-- Indexes for table `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`h_id`);

--
-- Indexes for table `local_guide`
--
ALTER TABLE `local_guide`
  ADD PRIMARY KEY (`localg_id`);

--
-- Indexes for table `local_guide_request`
--
ALTER TABLE `local_guide_request`
  ADD PRIMARY KEY (`lgr_id`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`pa_id`);

--
-- Indexes for table `predefine_booking`
--
ALTER TABLE `predefine_booking`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`s_id`);

--
-- Indexes for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `transport_cities`
--
ALTER TABLE `transport_cities`
  ADD PRIMARY KEY (`city_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `c_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `customer_details`
--
ALTER TABLE `customer_details`
  MODIFY `cust_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `customize_booking`
--
ALTER TABLE `customize_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `f_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `hotel`
--
ALTER TABLE `hotel`
  MODIFY `h_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `local_guide`
--
ALTER TABLE `local_guide`
  MODIFY `localg_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `local_guide_request`
--
ALTER TABLE `local_guide_request`
  MODIFY `lgr_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `pa_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `predefine_booking`
--
ALTER TABLE `predefine_booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `s_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transport_cities`
--
ALTER TABLE `transport_cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
