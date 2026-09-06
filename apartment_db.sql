-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 06, 2026 at 10:21 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apartment_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cus_id` int(11) NOT NULL,
  `cus_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `id_card` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cus_id`, `cus_name`, `phone`, `address`, `id_card`) VALUES
(1, 'Somchai', '02056378375', 'ບ້ານວັງຊາຍ', '094044923094'),
(2, 'Anon Thepvongsa', '02052730773', 'xaysetthaa', '324993920'),
(3, 'Thanousone', '02098756378', 'ບ້ານຄຳສະຫວາດ', '32465465'),
(10, 'latsamy phim', '02056556317', 'Vientiane', '56745675646'),
(12, 'Sundy', '02098475684', 'ໜອງປິງ', '085048594958'),
(13, 'lida', '020767647657', 'phonpapao', '0947487674578'),
(14, 'Jo', '4890399493', 'xaysettha', '3283674876'),
(15, 'Mee', '02095578578', 'xaysettha', '0934908345'),
(17, 'Rj song', '030439893', 'xaysettha', '934834755');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `emp_id` int(11) NOT NULL,
  `emp_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`emp_id`, `emp_name`, `phone`, `address`, `position`, `username`, `password`) VALUES
(1, 'Anoudeth', '02099999999', 'Vientiane', 'ພະນັກງານຕ້ອນຮັບ', 'admin', '$2y$10$VhG1sGq1QhXKcP2wH6ZPse7d7rJ9p9R6hYb0M0M6gKX1yQkYz8a3y'),
(2, 'Somsaiy', '02055555555', 'xaysettha', 'ພະນັກງານຕ້ອນຮັບ', 'Somsaiy', '$2y$10$441ppQNAG5syZBniBccPQ.kGLZY473kDGVIFVGVSqwUSsqxOIBEMi'),
(3, 'Anon Thepvongsa', '02099441178', 'xaysettha', 'ເຈົ້າຂອງອາພາດເມັ້ນ', 'Anonn', '$2y$10$mu3jyHYjwFEi5PV6iQoV0.NOIZJsCiabmSidGw1Fz/EKyeqoOf/4a'),
(5, 'Phoutthasone', '02076485763', 'ບ້ານໂພນຕ້ອງ', 'ຜູ້ຈັດການ', 'Phouthasone', '$2y$10$O.lrnn4/3BJ0EEvy2Hs93uuac.NZdxuFAqwEXB2A9cKvNumDvu/sS'),
(7, 'Mina', '02078948965', 'ສະພານທອງ', 'ພະນັກງານຕ້ອນຮັບ', 'Mina', '$2y$10$nM3qiVD/Pi/SJhcIsgSgke/chtvOM3JCo9tQl3znlD/6nKdTjaeFG'),
(8, 'Khamkeo', '020998876568', 'Phonpapao', 'ເຈົ້າຂອງອາພາດເມັ້ນ', 'khamkeo', '$2y$10$VGXh47VGYrYFWqVK7rpEVesnjnCU.sUOwlqA23VwJrNVIT/beO5d2'),
(9, 'New', '02088764567', 'xaysettha', 'ພະນັກງານຕ້ອນຮັບ', 'Neww', '$2y$10$CCY2wDg/l7eSNu7rGYd1Mu52mj.BBJxXHNt3y/QWA4doYmmrRabA2'),
(10, 'Hutsadee', '0206746473', 'xaysettha', 'ພະນັກງານຕ້ອນຮັບ', 'Hutt', '$2y$10$aXWQ8o.erCZKPwNACIYY9u3x8P6U1GxvZPt8x4eizFehsgm55nVqK');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `pay_id` int(11) NOT NULL,
  `rent_id` int(11) NOT NULL,
  `pay_date` date NOT NULL,
  `pay_month` varchar(10) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `pay_method` enum('cash','transfer') DEFAULT 'cash',
  `pay_status` enum('paid','unpaid') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`pay_id`, `rent_id`, `pay_date`, `pay_month`, `amount`, `pay_method`, `pay_status`) VALUES
(15, 39, '2026-08-25', '2026-08', 4000000.00, 'cash', 'paid'),
(16, 40, '2026-08-25', '2026-08', 4000000.00, 'cash', 'paid'),
(17, 41, '2026-08-25', '2026-08', 1500000.00, 'cash', 'paid'),
(18, 42, '2026-08-25', '2026-08', 1500000.00, 'cash', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `rental`
--

CREATE TABLE `rental` (
  `rent_id` int(11) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `deposit` decimal(10,2) DEFAULT NULL,
  `rent_price` decimal(10,2) DEFAULT NULL,
  `status` enum('active','finished','cancel') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rental`
--

INSERT INTO `rental` (`rent_id`, `cus_id`, `emp_id`, `room_id`, `start_date`, `end_date`, `deposit`, `rent_price`, `status`) VALUES
(39, 17, 3, 1, '2026-08-25', '2026-09-25', 1500000.00, 4000000.00, 'finished'),
(40, 17, 3, 4, '2026-08-25', '2026-09-25', 1500000.00, 4000000.00, 'finished'),
(41, 15, 3, 5, '2026-08-25', '2026-09-25', 500000.00, 1500000.00, 'active'),
(42, 12, 3, 6, '2026-08-25', '2026-09-25', 500000.00, 1500000.00, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL,
  `room_no` varchar(10) NOT NULL,
  `floor` int(11) DEFAULT NULL,
  `status` enum('free','occupied','repair') DEFAULT 'free',
  `type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `room_no`, `floor`, `status`, `type_id`) VALUES
(1, 'A101', 2, 'free', 1),
(4, 'A102', 2, 'free', 4),
(5, 'A103', 2, 'occupied', 1),
(6, 'A104', 2, 'occupied', 1),
(8, 'A105', 2, 'free', 5),
(9, 'A201', 3, 'free', 5),
(10, 'A202', 3, 'free', 5),
(11, 'A203', 3, 'free', 4),
(12, 'A204', 3, 'free', 4),
(13, 'A205', 3, 'free', 1),
(14, 'A106', 2, 'free', 5),
(15, 'A305', 3, 'free', 5),
(16, 'A405', 2, 'free', 4);

-- --------------------------------------------------------

--
-- Table structure for table `room_type`
--

CREATE TABLE `room_type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `deposit` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_type`
--

INSERT INTO `room_type` (`type_id`, `type_name`, `price`, `deposit`) VALUES
(1, 'Standard', 1500000.00, 500000.00),
(4, 'Normal room', 2500000.00, 1000000.00),
(5, 'VIP', 3500000.00, 1500000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cus_id`),
  ADD UNIQUE KEY `id_card` (`id_card`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `idx_rent_month` (`rent_id`,`pay_month`);

--
-- Indexes for table `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`rent_id`),
  ADD KEY `fk_rental_customer` (`cus_id`),
  ADD KEY `fk_rental_employee` (`emp_id`),
  ADD KEY `fk_rental_room` (`room_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_no` (`room_no`),
  ADD KEY `fk_room_type` (`type_id`);

--
-- Indexes for table `room_type`
--
ALTER TABLE `room_type`
  ADD PRIMARY KEY (`type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rental`
--
ALTER TABLE `rental`
  MODIFY `rent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `room_type`
--
ALTER TABLE `room_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_rental` FOREIGN KEY (`rent_id`) REFERENCES `rental` (`rent_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `fk_rental_customer` FOREIGN KEY (`cus_id`) REFERENCES `customer` (`cus_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rental_employee` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`emp_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rental_room` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON UPDATE CASCADE;

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `fk_room_type` FOREIGN KEY (`type_id`) REFERENCES `room_type` (`type_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
