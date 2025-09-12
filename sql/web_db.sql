-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2025 at 07:31 PM
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
-- Database: `web_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `user_id` varchar(11) NOT NULL,
  `product_id` varchar(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
('USR41317469', 'P004', 12, '2025-09-11 22:32:57', '2025-09-12 09:30:19'),
('USR41317469', 'P006', 1, '2025-09-11 22:41:59', '2025-09-11 22:41:59'),
('USR41317469', 'P059', 1, '2025-09-11 22:33:58', '2025-09-11 22:33:58'),
('USR96518622', 'P035', 1, '2025-09-10 17:55:54', '2025-09-10 17:55:54');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` varchar(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` varchar(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `image`, `parent_id`, `status`, `created_at`, `updated_at`) VALUES
('C001', 'Action figures', 'Collectible figures of superheroes, movie characters, and more.', 'assets/images/categories/category-action-figures.jpg', NULL, 'active', '2025-08-02 20:31:42', '2025-08-18 16:50:56'),
('C002', 'Building blocks & LEGO', 'Construction toys that develop creativity and problem-solving skills.', 'assets/images/categories/category-building-blocks-lego.jpg', NULL, 'active', '2025-08-02 20:31:42', '2025-08-18 16:51:33'),
('C003', 'Cars, trucks, trains', 'Vehicles for racing, transporting, and imaginative play.', 'assets/images/categories/category-cars-trucks-trains.jpg', NULL, 'active', '2025-08-02 20:31:42', '2025-08-18 17:13:53'),
('C004', 'Dolls', 'Classic and modern dolls for imaginative storytelling and nurturing play.', 'assets/images/categories/dolls.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-18 16:52:33'),
('C005', 'Games & puzzles', 'Board games, card games, and puzzles for all ages.', 'assets/images/categories/category-games-puzzles.jpg', NULL, 'active', '2025-08-02 20:31:42', '2025-08-18 16:53:39'),
('C006', 'Outdoor & sports', 'Toys for active play outside, including balls, bikes, and water games.', 'assets/images/categories/outdoor_sports.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-18 16:54:06'),
('C007', 'Pretend Play & costumes', 'Dress-up clothes and playsets for creative role-play.', 'assets/images/categories/pretend_play_costume.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-18 16:54:28'),
('C008', 'Blind box', 'Mystery toys that offer a surprise with every unboxing.', 'assets/images/categories/category-blind-boxes.jpg', NULL, 'active', '2025-08-02 20:31:42', '2025-08-18 16:55:12'),
('C009', 'Soft toys', 'Plush animals and cuddly characters for comfort and play.', 'assets/images/categories/category-soft-toys.jpg', NULL, 'active', '2025-08-02 20:31:42', '2025-08-18 16:55:48'),
('SC001', 'Cartoon Figures', 'Poseable figures from various media including superheroes, movies, and cartoons.', 'assets/images/categories/cartoon_figures.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-18 16:56:26'),
('SC002', 'Animals & Creatures', 'Animal-themed and mythical creature figures for imaginative play and collection.', 'assets/images/categories/animals.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-18 16:56:35'),
('SC003', 'Dinosaurs', 'Dinosaur action figures and playsets that teach and entertain.', 'assets/images/categories/dinosaur.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-18 16:56:45'),
('SC004', 'Military & Fantasy Toys', 'Figures and accessories themed around military, knights, and fantasy adventures.', 'assets/images/categories/military.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-18 16:56:53'),
('SC005', 'Models', 'Buildable models of vehicles, robots, and other complex figures.', 'assets/images/categories/models.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-18 16:57:02'),
('SC006', 'LEGO', 'Classic interlocking brick sets for creative construction and learning.', 'assets/images/categories/cat_68c456eb4252f2.12086318.png', 'C002', 'active', '2025-08-05 18:22:07', '2025-09-12 17:22:51'),
('SC008', 'Cars & Vehicles', 'Discover toy cars and vehicles that spark creativity and endless play.', 'assets/images/categories/cat_68a36b6d7ec940.27591070.png', 'C003', 'active', '2025-08-18 18:05:33', '2025-08-18 18:05:33'),
('SC009', 'Radio & Remote Control Vehicles', 'Radio & Remote Control (R/C) Vehicles', 'assets/images/categories/cat_68a370f759de23.71487171.png', 'C003', 'active', '2025-08-18 18:29:11', '2025-08-18 18:29:11'),
('SC010', 'Trains Sets', 'Classic train tracks and locomotives for kids to build, connect, and play with.', 'assets/images/categories/cat_68b33002a33bf8.41434876.jpg', 'C003', 'active', '2025-08-30 17:08:18', '2025-08-30 17:08:18'),
('SC011', 'Baby Dolls', 'Soft, cuddly dolls designed for nurturing and role-play fun.', 'assets/images/categories/cat_68b3302d51ec20.79525169.png', 'C004', 'active', '2025-08-30 17:09:01', '2025-08-30 17:09:01'),
('SC012', 'Fashion Dolls', 'Stylish dolls with trendy outfits and accessories for imaginative play.', 'assets/images/categories/cat_68b33055651930.55806902.png', 'C004', 'active', '2025-08-30 17:09:41', '2025-08-30 17:09:41'),
('SC013', 'Doll Houses & Accessories', 'Miniature houses, furniture, and add-ons for doll play adventures.', 'assets/images/categories/cat_68b330773d2355.54821585.png', 'C004', 'active', '2025-08-30 17:10:15', '2025-08-30 17:10:15'),
('SC014', 'Board Games', 'Family-friendly games for strategy, fun, and bonding time.', 'assets/images/categories/cat_68b330af818e98.47778490.jpg', 'C005', 'active', '2025-08-30 17:11:11', '2025-08-30 17:11:11'),
('SC015', 'Card Games', 'Fast-paced collectible or traditional card games for all ages.', 'assets/images/categories/cat_68b330cf9d6147.54834206.jpg', 'C005', 'active', '2025-08-30 17:11:43', '2025-08-30 17:11:43'),
('SC016', 'Puzzles', 'Challenging jigsaws and brain-teasers to spark problem-solving skills.', 'assets/images/categories/cat_68b330f39495a1.73496798.jpg', 'C005', 'active', '2025-08-30 17:12:19', '2025-08-30 17:12:19'),
('SC017', 'Balls', 'Sports and play balls for indoor and outdoor activities.', 'assets/images/categories/cat_68b33115c85c04.04330977.jpg', 'C006', 'active', '2025-08-30 17:12:53', '2025-08-30 17:12:53'),
('SC018', 'Bikes, Scooters & Ride-ons', 'Fun ride-on toys and wheels for active outdoor play.', 'assets/images/categories/cat_68b33135da8be1.56087920.png', 'C006', 'active', '2025-08-30 17:13:25', '2025-08-30 17:13:25'),
('SC019', 'NERF & Blasters', 'Action blasters with soft darts for exciting battles and challenges.', 'assets/images/categories/cat_68b3315b8759f2.14334683.jpg', 'C006', 'active', '2025-08-30 17:14:03', '2025-08-30 17:14:03'),
('SC020', 'Slides & Outdoor Play Centres', 'Outdoor sets with slides, swings, and climbing fun for kids.', 'assets/images/categories/cat_68b3317e2819e8.54337721.jpg', 'C006', 'active', '2025-08-30 17:14:38', '2025-08-30 17:14:38'),
('SC021', 'Costumes & Dressups', 'Themed outfits and accessories for pretend play and parties.', 'assets/images/categories/cat_68b331991270a4.24522092.png', 'C007', 'active', '2025-08-30 17:15:05', '2025-08-30 17:15:05'),
('SC022', 'Fashion Crafts & Jewellery', 'DIY kits to create stylish jewellery and craft designs.', 'assets/images/categories/cat_68b331c05210e0.02826859.jpg', 'C007', 'active', '2025-08-30 17:15:44', '2025-08-30 17:15:44'),
('SC023', 'Food Sets & Tableware', 'Play kitchens, pretend food, and dining sets for role-play cooking fun.', 'assets/images/categories/cat_68b331d97d0804.27213638.png', 'C007', 'active', '2025-08-30 17:16:09', '2025-08-30 17:16:09'),
('SC024', 'Teddy Bears', 'Classic plush bears, perfect for cuddles and gifts.', 'assets/images/categories/cat_68b331f67efaf5.83899638.jpg', 'C009', 'active', '2025-08-30 17:16:38', '2025-08-30 17:16:38'),
('SC025', 'Stuffed Animals', 'Soft plush animals like cats, dogs, bunnies, and more.', 'assets/images/categories/cat_68b3320f4bd857.70451257.png', 'C009', 'active', '2025-08-30 17:17:03', '2025-08-30 17:17:03'),
('SC026', 'Cartoon / Movie Characters', 'Plush and figures of popular TV, movie, and game characters.', 'assets/images/categories/cat_68b3322acddbd6.71453581.jpg', 'C009', 'active', '2025-08-30 17:17:30', '2025-08-30 17:17:30'),
('SC027', 'Pop Mart', 'Trendy collectible blind boxes featuring creative characters and designs.', 'assets/images/categories/cat_68b33243d78d77.41940908.jpg', 'C008', 'active', '2025-08-30 17:17:55', '2025-08-30 17:17:55'),
('SC028', 'Sanrio Characters', 'Cute collectibles of Hello Kitty, My Melody, Kuromi, and friends.', 'assets/images/categories/cat_68b3330a001089.38209441.png', 'C008', 'active', '2025-08-30 17:18:24', '2025-08-30 17:21:14'),
('SC029', 'Anime / Comic Series', 'Figures and toys from popular anime, manga, and comic franchises.', 'assets/images/categories/cat_68b3327a1af782.44752948.jpg', 'C008', 'active', '2025-08-30 17:18:50', '2025-08-30 17:18:50');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` varchar(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `voucher_id` varchar(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` enum('pending','processing','shipped','delivered','cancel_requested','cancelled') DEFAULT 'pending',
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipping_courier` varchar(100) DEFAULT NULL,
  `payment_id` varchar(11) DEFAULT NULL,
  `shipping_first_name` varchar(50) DEFAULT NULL,
  `shipping_last_name` varchar(50) DEFAULT NULL,
  `shipping_address_line1` varchar(255) DEFAULT NULL,
  `shipping_address_line2` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_postal_code` varchar(20) DEFAULT NULL,
  `shipping_area` enum('west','east') NOT NULL,
  `billing_first_name` varchar(50) DEFAULT NULL,
  `billing_last_name` varchar(50) DEFAULT NULL,
  `billing_address_line1` varchar(255) DEFAULT NULL,
  `billing_address_line2` varchar(255) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_state` varchar(100) DEFAULT NULL,
  `billing_postal_code` varchar(20) DEFAULT NULL,
  `billing_area` enum('west','east') DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `order_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `shipped_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_number`, `subtotal`, `shipping_cost`, `discount_amount`, `voucher_id`, `total_amount`, `order_status`, `tracking_number`, `shipping_courier`, `payment_id`, `shipping_first_name`, `shipping_last_name`, `shipping_address_line1`, `shipping_address_line2`, `shipping_city`, `shipping_state`, `shipping_postal_code`, `shipping_area`, `billing_first_name`, `billing_last_name`, `billing_address_line1`, `billing_address_line2`, `billing_city`, `billing_state`, `billing_postal_code`, `billing_area`, `contact_email`, `contact_phone`, `order_notes`, `created_at`, `updated_at`, `shipped_date`) VALUES
('ORD0000001', 'USR79561298', 'ORD20250903436', 49.90, 12.00, 0.00, NULL, 61.90, 'delivered', '99897544656598989', 'gdex', 'PAY0000001', 'John', 'Lee', '12, Jalan ABC, Taman EFG', '12, Jalan ABC, Taman EFG', 'Setapak', 'Kuala Lumpur', '51200', 'east', 'John', 'Lee', '12, Jalan ABC, Taman EFG', '12, Jalan ABC, Taman EFG', 'Setapak', 'Kuala Lumpur', '51200', 'east', 'john123@gmail.com', '01122554100', '', '2025-09-03 19:29:55', '2025-09-03 19:53:01', NULL),
('ORD0000006', 'USR41317469', 'ORD20250905565', 159.80, 0.00, 0.00, NULL, 159.80, 'shipped', '155485948484858589', 'dhl', 'PAY0000006', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '0115488799', '', '2025-09-05 14:01:17', '2025-09-05 14:01:56', NULL),
('ORD0000007', 'USR41317469', 'ORD20250905774', 69.90, 8.00, 0.00, NULL, 77.90, 'shipped', '155485948484858589', 'dhl', 'PAY0000007', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '0115488799', '', '2025-09-05 14:01:31', '2025-09-05 14:01:56', NULL),
('ORD0000009', 'USR41317469', 'ORD20250905877', 84.00, 12.00, 0.00, NULL, 96.00, 'shipped', '65564984784845', 'gdex', 'PAY0000009', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'east', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'east', 'step724mak@gmail.com', '0115488799', '', '2025-09-05 14:02:48', '2025-09-05 14:16:38', NULL),
('ORD0000010', 'USR41317469', 'ORD20250905143', 118.25, 8.00, 0.00, NULL, 126.25, 'delivered', '65564984784845', 'gdex', 'PAY0000010', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '0115488799', '', '2025-09-05 14:12:54', '2025-09-05 15:43:31', NULL),
('ORD0000012', 'USR41317469', 'ORD20250905256', 100.00, 8.00, 0.00, NULL, 108.00, 'delivered', '655556565659', 'pos_laju', 'PAY0000012', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '0115488799', '', '2025-09-05 14:17:57', '2025-09-05 15:43:22', NULL),
('ORD0000013', 'USR41317469', 'ORD20250907598', 104.25, 8.00, 0.00, NULL, 112.25, 'delivered', '5988989454655', 'gdex', 'PAY0000013', 'John', 'Lee', '12, Jalan ABC, Taman EFG', '12, Jalan ABC, Taman EFG', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'John', 'Lee', '12, Jalan ABC, Taman EFG', '12, Jalan ABC, Taman EFG', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '01122554100', '', '2025-09-06 22:04:22', '2025-09-11 21:41:16', NULL),
('ORD0000015', 'USR96518622', 'ORD20250907700', 259.90, 0.00, 20.00, 'VCH003', 239.90, 'delivered', '265656565892366', 'dhl', 'PAY0000015', 'Mike', 'MM', '12, Jalan RN, Taman BLD', '', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Mike', 'MM', '12, Jalan RN, Taman BLD', '', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'mike123@gmail.com', '0125454848', '', '2025-09-07 20:26:19', '2025-09-10 03:12:37', NULL),
('ORD0000016', 'USR96518622', 'ORD20250907378', 200.00, 0.00, 20.00, 'VCH001', 180.00, 'delivered', '265656565892366', 'dhl', 'PAY0000016', 'Mike', 'MM', '12, Jalan RN, Taman BLD', '', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Mike', 'MM', '12, Jalan RN, Taman BLD', '', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'mike123@gmail.com', '0125454848', '', '2025-09-07 20:30:46', '2025-09-10 03:12:34', NULL),
('ORD0000018', 'USR41317469', 'ORD20250910455', 159.80, 0.00, 25.57, 'V000000001', 134.23, 'shipped', '122546667774410', 'pos_laju', 'PAY0000018', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '01145785525', '', '2025-09-10 21:43:48', '2025-09-10 21:45:11', NULL),
('ORD0000019', 'USR41317469', 'ORD20250910853', 233.65, 0.00, 0.00, NULL, 233.65, 'shipped', '122546667774410', 'pos_laju', 'PAY0000019', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '0115481527', '', '2025-09-10 21:44:27', '2025-09-10 21:45:11', NULL),
('ORD0000020', 'USR42881374', 'ORD20250911359', 220.00, 0.00, 0.00, NULL, 220.00, 'shipped', '669565545484', 'gdex', 'PAY0000020', 'Mike', 'Magic', '12, Jalan NN, Taman G', '', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Mike', 'Magic', '12, Jalan NN, Taman G', '', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'stepmak724@gmail.com', '01153215454', '', '2025-09-10 22:11:28', '2025-09-11 20:13:00', NULL),
('ORD0000021', 'USR42881374', 'ORD20250911452', 60.00, 8.00, 6.00, 'VCH001', 62.00, 'shipped', '01014077741145', 'citylink', 'PAY0000021', 'Mike', 'Magic', '12, Jalan NN, Taman G', '', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Mike', 'Magic', '12, Jalan NN, Taman G', '', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'stepmak724@gmail.com', '01157624534', '', '2025-09-10 22:16:43', '2025-09-10 22:27:34', NULL),
('ORD0000022', 'USR41317469', 'ORD20250911258', 40.00, 8.00, 0.00, NULL, 48.00, 'shipped', '110540480480', 'fedex', 'PAY0000022', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '0112514454', '', '2025-09-10 22:26:38', '2025-09-10 22:27:31', NULL),
('ORD0000024', 'USR46904770', 'ORD20250911777', 139.90, 8.00, 20.00, 'VCH003', 127.90, 'delivered', '959899878784545', 'fedex', 'PAY0000024', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'ame0908@gmail.com', '01125144545', '', '2025-09-11 18:36:00', '2025-09-11 18:41:57', NULL),
('ORD0000025', 'USR46904770', 'ORD20250911810', 60.00, 8.00, 0.00, NULL, 68.00, 'delivered', '959899878784545', 'fedex', 'PAY0000025', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'ame0908@gmail.com', '01125144545', '', '2025-09-11 18:37:26', '2025-09-11 18:41:53', NULL),
('ORD0000026', 'USR46904770', 'ORD20250911342', 49.90, 8.00, 0.00, NULL, 57.90, 'shipped', '59655121515451', 'gdex', 'PAY0000026', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'ame0908@gmail.com', '01125144545', '', '2025-09-11 19:26:53', '2025-09-11 20:02:55', NULL),
('ORD0000027', 'USR46904770', 'ORD20250911900', 59.90, 8.00, 0.00, NULL, 67.90, 'shipped', '6549878454545', 'gdex', 'PAY0000027', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'ame0908@gmail.com', '01125144545', '', '2025-09-11 20:21:01', '2025-09-11 20:25:31', NULL),
('ORD0000028', 'USR41317469', 'ORD20250911870', 59.80, 8.00, 0.00, NULL, 67.80, 'delivered', '2222554561313', 'fedex', 'PAY0000028', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '01125144545', '', '2025-09-11 20:26:45', '2025-09-11 21:41:32', NULL),
('ORD0000031', 'USR41317469', 'ORD20250911691', 67.50, 8.00, 0.00, NULL, 75.50, 'cancelled', NULL, NULL, 'PAY0000031', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '01154887998', '', '2025-09-11 20:46:17', '2025-09-11 20:50:27', NULL),
('ORD0000033', 'USR41317469', 'ORD20250911854', 93.75, 8.00, 0.00, NULL, 101.75, 'cancelled', NULL, NULL, 'PAY0000033', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '01154887998', '', '2025-09-11 21:02:28', '2025-09-11 21:02:45', NULL),
('ORD0000036', 'USR41317469', 'ORD20250912662', 109.80, 8.00, 0.00, NULL, 117.80, 'pending', NULL, NULL, 'PAY0000036', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'Kim', 'Lee', '215, Jalan 234, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', 'west', 'step724mak@gmail.com', '01154887998', '', '2025-09-11 22:33:40', '2025-09-11 22:33:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_id` varchar(11) NOT NULL,
  `product_id` varchar(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
('ORD0000001', 'P026', 1, 49.90, 49.90, '2025-09-03 19:29:55', '2025-09-03 19:29:55'),
('ORD0000006', 'P013', 1, 9.90, 9.90, '2025-09-05 14:01:17', '2025-09-05 14:01:17'),
('ORD0000006', 'P019', 1, 149.90, 149.90, '2025-09-05 14:01:17', '2025-09-05 14:01:17'),
('ORD0000007', 'P027', 1, 69.90, 69.90, '2025-09-05 14:01:31', '2025-09-05 14:01:31'),
('ORD0000009', 'P004', 1, 84.00, 84.00, '2025-09-05 14:02:48', '2025-09-05 14:02:48'),
('ORD0000010', 'P001', 1, 59.25, 59.25, '2025-09-05 14:12:54', '2025-09-05 14:12:54'),
('ORD0000010', 'P025', 1, 59.00, 59.00, '2025-09-05 14:12:54', '2025-09-05 14:12:54'),
('ORD0000012', 'P043', 1, 100.00, 100.00, '2025-09-05 14:17:57', '2025-09-05 14:17:57'),
('ORD0000013', 'P006', 1, 104.25, 104.25, '2025-09-06 22:04:22', '2025-09-06 22:04:22'),
('ORD0000015', 'P041', 1, 200.00, 200.00, '2025-09-07 20:26:19', '2025-09-07 20:26:19'),
('ORD0000015', 'P059', 1, 59.90, 59.90, '2025-09-07 20:26:19', '2025-09-07 20:26:19'),
('ORD0000016', 'P041', 1, 200.00, 200.00, '2025-09-07 20:30:46', '2025-09-07 20:30:46'),
('ORD0000018', 'P020', 1, 59.90, 59.90, '2025-09-10 21:43:48', '2025-09-10 21:43:48'),
('ORD0000018', 'P035', 1, 99.90, 99.90, '2025-09-10 21:43:48', '2025-09-10 21:43:48'),
('ORD0000019', 'P009', 1, 33.75, 33.75, '2025-09-10 21:44:27', '2025-09-10 21:44:27'),
('ORD0000019', 'P034', 1, 199.90, 199.90, '2025-09-10 21:44:27', '2025-09-10 21:44:27'),
('ORD0000020', 'P055', 1, 220.00, 220.00, '2025-09-10 22:11:28', '2025-09-10 22:11:28'),
('ORD0000021', 'P056', 1, 60.00, 60.00, '2025-09-10 22:16:43', '2025-09-10 22:16:43'),
('ORD0000022', 'P053', 1, 40.00, 40.00, '2025-09-10 22:26:38', '2025-09-10 22:26:38'),
('ORD0000024', 'P035', 1, 99.90, 99.90, '2025-09-11 18:36:00', '2025-09-11 18:36:00'),
('ORD0000024', 'P053', 1, 40.00, 40.00, '2025-09-11 18:36:00', '2025-09-11 18:36:00'),
('ORD0000025', 'P050', 1, 60.00, 60.00, '2025-09-11 18:37:26', '2025-09-11 18:37:26'),
('ORD0000026', 'P057', 1, 49.90, 49.90, '2025-09-11 19:26:53', '2025-09-11 19:26:53'),
('ORD0000027', 'P059', 1, 59.90, 59.90, '2025-09-11 20:21:01', '2025-09-11 20:21:01'),
('ORD0000028', 'P032', 1, 19.90, 19.90, '2025-09-11 20:26:45', '2025-09-11 20:26:45'),
('ORD0000028', 'P061', 1, 39.90, 39.90, '2025-09-11 20:26:45', '2025-09-11 20:26:45'),
('ORD0000031', 'P003', 1, 67.50, 67.50, '2025-09-11 20:46:17', '2025-09-11 20:46:17'),
('ORD0000033', 'P008', 1, 93.75, 93.75, '2025-09-11 21:02:28', '2025-09-11 21:02:28'),
('ORD0000036', 'P059', 1, 59.90, 59.90, '2025-09-11 22:33:40', '2025-09-11 22:33:40'),
('ORD0000036', 'P060', 1, 49.90, 49.90, '2025-09-11 22:33:40', '2025-09-11 22:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` varchar(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `token` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`reset_id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
('R00001', 'USR41317469', '2d4ee69a31c35e8204e998eae72ef3f5d0466fc08e924ae2b163c8cbd48f8bd7', '2025-09-05 09:11:26', 1, '2025-09-05 00:11:26'),
('R00002', 'USR28091321', '84345e36ec9eb55a33735e610ac050f4b193ce06c5f8075232c219a9b08bbb0b', '2025-09-12 01:52:11', 1, '2025-09-11 17:22:11');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` varchar(11) NOT NULL,
  `order_id` varchar(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded','success') DEFAULT 'pending',
  `transaction_id` varchar(50) DEFAULT NULL,
  `wallet_id` varchar(20) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `user_id`, `amount`, `payment_method`, `payment_status`, `transaction_id`, `wallet_id`, `payment_date`, `created_at`, `updated_at`) VALUES
('PAY0000001', 'ORD0000001', 'USR79561298', 61.90, 'credit_card', 'success', 'TXN0000001', NULL, '2025-09-03 19:29:55', '2025-09-03 19:29:55', '2025-09-03 19:29:55'),
('PAY0000006', 'ORD0000006', 'USR41317469', 159.80, 'credit_card', 'success', 'TXN0000006', NULL, '2025-09-05 14:01:17', '2025-09-05 14:01:17', '2025-09-05 14:01:17'),
('PAY0000007', 'ORD0000007', 'USR41317469', 77.90, 'credit_card', 'success', 'TXN0000007', NULL, '2025-09-05 14:01:31', '2025-09-05 14:01:31', '2025-09-05 14:01:31'),
('PAY0000009', 'ORD0000009', 'USR41317469', 96.00, 'credit_card', 'success', 'TXN0000009', NULL, '2025-09-05 14:02:48', '2025-09-05 14:02:48', '2025-09-05 14:02:48'),
('PAY0000010', 'ORD0000010', 'USR41317469', 126.25, 'credit_card', 'success', 'TXN0000010', NULL, '2025-09-05 14:12:54', '2025-09-05 14:12:54', '2025-09-05 14:12:54'),
('PAY0000012', 'ORD0000012', 'USR41317469', 108.00, 'credit_card', 'success', 'TXN0000012', NULL, '2025-09-05 14:17:57', '2025-09-05 14:17:57', '2025-09-05 14:17:57'),
('PAY0000013', 'ORD0000013', 'USR41317469', 112.25, 'ewallet', 'success', 'TXN0000013', NULL, '2025-09-06 22:04:22', '2025-09-06 22:04:22', '2025-09-06 22:04:22'),
('PAY0000015', 'ORD0000015', 'USR96518622', 239.90, 'credit_card', 'success', 'TXN0000015', NULL, '2025-09-07 20:26:19', '2025-09-07 20:26:19', '2025-09-07 20:26:19'),
('PAY0000016', 'ORD0000016', 'USR96518622', 180.00, 'credit_card', 'success', 'TXN0000016', NULL, '2025-09-07 20:30:46', '2025-09-07 20:30:46', '2025-09-07 20:30:46'),
('PAY0000018', 'ORD0000018', 'USR41317469', 134.23, 'credit_card', 'success', 'TXN0000018', NULL, '2025-09-10 21:43:48', '2025-09-10 21:43:48', '2025-09-10 21:43:48'),
('PAY0000019', 'ORD0000019', 'USR41317469', 233.65, 'credit_card', 'success', 'TXN0000019', NULL, '2025-09-10 21:44:27', '2025-09-10 21:44:27', '2025-09-10 21:44:27'),
('PAY0000020', 'ORD0000020', 'USR42881374', 220.00, 'credit_card', 'success', 'TXN0000020', NULL, '2025-09-10 22:11:28', '2025-09-10 22:11:28', '2025-09-10 22:11:28'),
('PAY0000021', 'ORD0000021', 'USR42881374', 62.00, 'credit_card', 'success', 'TXN0000021', NULL, '2025-09-10 22:16:43', '2025-09-10 22:16:43', '2025-09-10 22:16:43'),
('PAY0000022', 'ORD0000022', 'USR41317469', 48.00, 'ewallet', 'success', 'TXN0000022', NULL, '2025-09-10 22:26:38', '2025-09-10 22:26:38', '2025-09-10 22:26:38'),
('PAY0000024', 'ORD0000024', 'USR46904770', 127.90, 'credit_card', 'success', 'TXN0000024', NULL, '2025-09-11 18:36:00', '2025-09-11 18:36:00', '2025-09-11 18:36:00'),
('PAY0000025', 'ORD0000025', 'USR46904770', 68.00, 'credit_card', 'success', 'TXN0000025', NULL, '2025-09-11 18:37:26', '2025-09-11 18:37:26', '2025-09-11 18:37:26'),
('PAY0000026', 'ORD0000026', 'USR46904770', 57.90, 'credit_card', 'success', 'TXN0000026', NULL, '2025-09-11 19:26:53', '2025-09-11 19:26:53', '2025-09-11 19:26:53'),
('PAY0000027', 'ORD0000027', 'USR46904770', 67.90, 'credit_card', 'success', 'TXN0000027', NULL, '2025-09-11 20:21:01', '2025-09-11 20:21:01', '2025-09-11 20:21:01'),
('PAY0000028', 'ORD0000028', 'USR41317469', 67.80, 'credit_card', 'success', 'TXN0000028', NULL, '2025-09-11 20:26:45', '2025-09-11 20:26:45', '2025-09-11 20:26:45'),
('PAY0000031', 'ORD0000031', 'USR41317469', 75.50, 'credit_card', 'success', 'TXN0000031', NULL, '2025-09-11 20:46:17', '2025-09-11 20:46:17', '2025-09-11 20:46:17'),
('PAY0000033', 'ORD0000033', 'USR41317469', 101.75, 'credit_card', 'success', 'TXN0000033', NULL, '2025-09-11 21:02:28', '2025-09-11 21:02:28', '2025-09-11 21:02:28'),
('PAY0000036', 'ORD0000036', 'USR41317469', 117.80, 'ewallet', 'success', 'TXN0000036', NULL, '2025-09-11 22:33:40', '2025-09-11 22:33:40', '2025-09-11 22:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` varchar(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `sku` varchar(50) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `category_id` varchar(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','out_of_stock') DEFAULT 'active',
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `product_features` text DEFAULT NULL,
  `product_specifications` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `sale_price`, `sku`, `stock_quantity`, `category_id`, `image`, `status`, `featured`, `created_at`, `updated_at`, `product_features`, `product_specifications`) VALUES
('P001', 'How to Train Your Dragon 12-Inch Toothless', 'Bring home the magic of How to Train Your Dragon with the 12-Inch Toothless Figure! This large-scale, highly detailed version of the beloved Night Fury is perfect for both play and display.', 79.00, 59.25, 'SKU-0001', 31, 'SC001', 'assets/images/products/toothless_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-09-02 12:29:57', '-Authentic Design. Detailed sculpting and vibrant colors bring Toothless to life just like in the movies.\r\n-Poseable Features. Moveable wings and legs let you create dynamic action poses.\r\n-Large 12-Inch Size. A standout piece for playtime or any How to Train Your Dragon collection.\r\n-Perfect for Fans & Collectors. Whether you\'re reliving epic battles or displaying Toothless on your shelf, this figure is a must-have.\r\n-Join Hiccup and Toothless on new adventures with the 12-Inch Toothless Figure the ultimate companion for every dragon trainer!', 'Includes: 1 Dragon\r\nBrand: HowToTrainYourDragon, DreamWorks\r\nDimensions(cm): 11 x 34 x 6\r\nWeight (kg): 0.372\r\nAge Range: 3+\r\nBattery Required: No'),
('P002', 'Jurassic World Dinosaur Jurassic Park Dr. Ian Malcolm Glider Escape Pack', 'The Jurassic Park Dr. Ian Malcolm Glider Escape Pack! This Dr. Ian Malcolm 3.75 inch action with a Dilophosaurus and Triceratops. Accessories include a launcher and projectile, a wing-pack, a harness  and capture gear restraints for the Dilophosaurus!', 200.00, 150.00, 'SKU-0002', 30, 'SC003', 'assets/images/products/jurassic_malcolm_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-09-02 12:29:57', '-The \'90s come roaring back with the Jurassic Park \'93 Classic Dr. Ian Malcolm Glider Escape Pack!\r\n-Launch the projectile with button activation and attach the wing-pack glider to the Dr. Ian Malcolm 3.75 in-scale articulated character figure for a quick escape!\r\n-Attach 1 capture gear accessory to the Dilophosaurus dinosaur toy\'s head and 1 restraint accessory to the arms and legs -- plus, place the young Triceratops in the harness!\r\n-Connected play! Scan the dinosaur\'s hidden Tracking Code in the free Jurassic World Facts App with a compatible smart device (not included) to initiate AUGMENTED REALITY activities and games.', 'Includes: 1 Dr. Ian Malcolm figure, 1 Dilophosaurus dinosaur, 1 young Triceratops dinosaur, and 6 accessories\r\nBrand: Jurassic World\r\nDimensions (cm): 27 x 7 x 13\r\nWeight (kg): 0.3\r\nAge Range: 4+\r\nBattery Required: No'),
('P003', 'Soldier Force Falcon Command Jet Playset', 'Take the mission to the skies! The action figure fits inside the cockpit and is ready for combat. Lower the windshield and raise the aircraft into the sky. Package includes a zipline and hooks so he can traverse the terrain when needed.', 90.00, 67.50, 'SKU-0003', 45, 'SC004', 'assets/images/products/soldier_jet_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-09-12 09:28:21', '-F35 Jet with 2 different light and sound function\r\n-Attachable Weapon and Accessories', 'Includes: 1 F35 Jet, 1 figure and accessories\r\nBrand:	Soldier Force\r\nDimensions (cm): 28 x 25 x 9\r\nWeight (kg): 0.3666\r\nAge Range: 4+\r\nBattery Required: Yes'),
('P004', 'Super Wings Transforming Lucie', 'Season 8 of Super Wings: THE ELECTRIC HEROES! Jett and friends return with sleek electric upgrades, new powers, and 5 new allies. Their new base, the World Spaceport, launches them into a clean, green future!', 112.00, 84.00, 'SKU-0004', 12, 'SC001', 'assets/images/products/superwings_lucie_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-09-01 16:33:58', '-True to animation content, S8 new characters\r\n-2 modes! Easily transforms from toy airplane to bot\r\n-With transforming electric shield\r\n5-inch scale transforming figure\r\n-Real working wheels', 'Includes: 5-inch scale transforming figure\r\nBrand: Super Wings\r\nDimensions (cm): 12 x 13 x 12\r\nWeight (kg): 0.135\r\nAge Range: 3+\r\nBattery Required: No'),
('P005', 'Transformers One Power Flip Optimus Prime', 'Experience the epic origins of legendary Transformer robots with this Transformer One Power Flip Optimus Prime action figure, inspired by the iconic character in the Transformer One movie!', 289.00, 216.75, 'SKU-0005', 12, 'SC001', 'assets/images/products/optimusprime_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:47:39', '-POWER FLIP OPTIMUS PRIME ACTION FIGURE: Movie-inspired Optimus Prime toy grows in height from 8-10 inches (20-25 cm) and changes between 4 different converting modes: Orion Pax, Cybertronian Truck toy, Optimus Prime, and Ultimate Optimus Prime\r\n-ELECTRONIC TOY WITH LIGHTS, SOUNDS, & PHRASES: Activate lights, sounds and phrases at the push of a button or by converting between modes. Each mode has special effects. Includes 3x A76/LR44 button cell batteries. Speaks in English only\r\n-INITIATE POWER FLIP CONVERSION: Hold the Transformersnsformers toy’s arms and flip the figure to quickly convert from Optimus Prime to Ultimate Optimus Prime mode\r\n-GEAR UP WITH BATTLE ARMOR: Battle armor engages automatically after Power Flipping the robot toy to Ultimate Optimus Prime mode\r\n-4 CONVERTING MODES: 4-in-1 figure converts from truck toy to Orion Pax figure in 13 steps, from Orion Pax to Optimus Prime figure in 3 steps, and from Optimus Prime to Ultimate Optimus Prime figure with Power Flip action and pull-down leg extension\r\n-MOVIE-INSPIRED ACCESSORIES: Power Flip Optimus Prime toy comes with Star Warsord, ENerfgon axe, shield, and Matrix of Leadership accessories that attach to the action figure in each mode\r\n-TransformersNSFORMERS ONE MOVIE: This action figure is inspired by the Optimus Prime character from the movie Transformersnsformers One, the untold origin story of Optimus Prime and Megatron, once friends bonded like brothers, who changed the fate of Cybertron forever', 'Includes: Figure, 4 accessories, Instructions\r\nBrand: Transformers\r\nDimensions (cm): 26 x 30 x 10\r\nWeight (kg): 0.579\r\nAge Range: 6+\r\nBattery Required: Yes'),
('P006', 'How to Train Your Dragon Red Death Chomping Rampage', 'Unleash the ultimate dragon battle with the Red Death Chomping Rampage Figure! This massive and fearsome dragon, the legendary villain from How to Train Your Dragon!', 139.00, 104.25, 'SKU-0006', 52, 'SC001', 'assets/images/products/red_death_dragon_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-09-02 12:29:57', '-Authentic Movie-Inspired Design - Highly detailed sculpting captures the monstrous size and power of Red Death.\r\n-Chomping Jaw Action - Press to activate Red Death powerful bite!\r\n-Poseable Wings & Limbs - Create epic battle poses and dynamic action scenes.\r\nPerfect for Play & Display - A must-have for How to Train Your Dragon fans and collectors.\r\n-Prepare for an earth-shaking showdown with the Red Death Chomping Rampage Figure , will you be able to defeat this legendary beast?', 'Includes: 2 Dragons\r\nBrand: HowToTrainYourDragon, DreamWorks\r\nDimensions (cm): 31 x 21 x 14\r\nWeight (kg): 0.350\r\nAge Range: 3+\r\nBattery Required: No'),
('P007', 'Transformers YOLOPARK Transformers: Rise of Beasts AMK Bumblebee', 'Colored semi-finished products made of PVC, which need to be assembled by themselves. 2. The height of the product is about 16~20 cm, with replacement accessories.', 159.00, 119.25, 'SKU-0007', 12, 'SC005', 'assets/images/products/bumblebee_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:47:59', '- Colored semi-finished products made of PVC, which need to be assembled by themselves\r\n- The height of the product is about 16~20 cm, with replacement accessories', 'Includes: Robot, Accessories\r\nBrand: Transformers Rise of the Beasts\r\nDimensions (cm): 21 X 15 X6\r\nWeight (kg):0.48\r\nAge Range: 8+\r\nBattery Required: No'),
('P008', 'Marvel Legends Alist Iron Man Mark 85', 'This collectible 6-inch-scale Marvel Avengers figure is detailed to look like Iron Man from Marvel Studios Avengers: Endgame.', 125.00, 93.75, 'SKU-0008', 23, 'SC001', 'assets/images/products/ironman_mark85_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-09-01 17:41:58', '-MARVEL STUDIOS’ AVENGERS: ENDGAME: This collectible Marvel figure is inspired by Iron Man’s appearance in the epic conclusion to the Infinity Saga, Marvel Studios’ Avengers: Endgame -- a great gift for collectors and fans ages 4 and up\r\n-PREMIUM DESIGN AND ARTICULATION: Marvel fans and collectors can display this 6 inch action figure (15 cm) -- featuring premium movie-accurate deco and design, and over 20 points of articulation -- in their Avengers action figure collections\r\n-IRON MAN TAKES FLIGHT: This officially licensed Iron Man Mark LXXXV figure comes with 2 alternate repulsor hands and 4 repulsor FX for dynamic poseability\r\n-WINDOW BOX PACKAGING: Display the MCU on your shelf with collectible window box packaging featuring movie-inspired package art\r\n-THE FINAL STAND: To release Thanos’ grip on the universe, Tony Stark fights alongside his fellow Avengers in his high-powered Mark LXXXV armor', 'Includes: Figure, 6 accessories\r\nBrand: Marvel\r\nDimensions (cm): 16 x 27 x 6\r\nWeight (kg): 0.218\r\nAge Range: 4+\r\nBattery Required: No'),
('P009', 'Marvel Spider-Man Titan Hero Series', 'Imagine swinging into the newest Spider-Man adventure with Spider-Man figures, vehicles, and roleplay items inspired by the Marvel comics. With this classic inspired line of toys, kids can imagine the web-slinging, wall-crawling action.', 45.00, 33.75, 'SKU-0009', 19, 'SC001', 'assets/images/products/spiderman_titan_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-09-01 18:08:35', '-Your friendly neighborhood Spider-Man is now available in titan size! This 12-inch scale Spider-Man action figure features Spidey\'s classic suit, and the figure\'s multiple flex points make it easy for kids to imagine this super-sized superhero whizzing into the most epic battles.\r\n-Experience comic book fun with the figure.', 'Includes: 12-inch-scale Spider-Man figure\r\nBrand: Marvel\r\nDimensions (cm): 5 x 10 x 30\r\nWeight (kg): 0.380\r\nAge Range: 4+\r\nBattery Required: No\r\n'),
('P010', 'DC Comics 12-Inch Figure The Dark Knight Batman', 'Step into Gotham City with the DC Comics 12-Inch Figure of The Dark Knight Batman! This impressively detailed action figure stands a commanding 12 inches tall, making it a striking addition to any collection.', 69.00, 51.75, 'SKU-0010', 41, 'SC001', 'assets/images/products/batman_darkknight_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-09-01 18:26:42', '-Crafted with exceptional attention to detail, this figure showcases Batman in his iconic suit, complete with a flowing cape and signature bat emblem. \r\n-The realistic facial features capture the intensity and determination of the legendary hero, making it perfect for both play and display. \r\n-With multiple points of articulation, this figure allows fans to pose Batman in dynamic action scenes or classic stances. -Whether you\'re reenacting epic battles against Gotham\'s villains or showcasing him on your shelf, this Dark Knight figure is a must-have for DC Comics enthusiasts. ', 'Includes: 12-Inch Figure \r\nBrand: DC Comics\r\nDimensions (cm): 11 x 34 x 6\r\nWeight (kg): 0.1\r\nAge Range: 3+\r\nBattery Required: No'),
('P011', 'LEGO Disney 43249 Stitch', 'The incorrigible extraterrestrial from the hit Disney movie, dressed in a Hawaiian shirt, has movable ears and a turning head, a buildable ice-cream cone that the character can hold and a buildable flower that can be added or removed. This kids building kit looks great on display in any room and makes a fun Disney gift idea for older children and movie-lovers as they set up the buildable character.', 281.18, 239.00, 'SKU-11', 12, 'SC006', 'assets/images/products/prod_68a44026104874.60685478.png', 'active', 0, '2025-08-19 09:13:10', '2025-09-12 16:38:10', '-Disney gift for kids – A building toy set featuring a Disney character with functions and accessories that makes a gift for movie-lovers, girls and boys aged 9+ to share at school or home\r\n-A helping hand – Let the LEGO® Builder app guide kids on an intuitive building adventure, where they can save sets, track progress and zoom in and rotate models in 3D while they build\r\n-Expand life skills – With a LEGO® ? Disney buildable character, accessories and functions to enhance display, this kids’ construction toy helps foster life skills through fun', 'Includes: 730 pieces LEGO blocks\r\nBrand: LEGO\r\nDimensions(cm): 26.2 × 28.2 × 9.6\r\nWeight (kg): 0.869\r\nAge Range: 9+\r\nBattery Required: No'),
('P012', 'Speed City 1967 Pontiac Firebird', 'Rev up the streets with the Speed City 1967 Pontiac Firebird! This finely crafted 1:60 die-cast vehicle captures the authentic details of the classic muscle car, perfect for both play and display.', 20.00, 9.90, 'SKU-12', 20, 'SC008', 'assets/images/products/prod_68b74e2bae93a7.75181520.png', 'active', 0, '2025-09-02 12:06:03', '2025-09-02 12:06:03', 'Realistic design inspired by the 1967 Pontiac Firebird\r\n\r\nDurable die-cast construction with detailed finish\r\n\r\nPerfect for imaginative city play or collectible display\r\n\r\nCompact 1:60 scale, easy to take anywhere\r\n\r\nGreat addition to any car enthusiast’s collection', 'Vehicle Type: 1967 Pontiac Firebird\r\n\r\nScale: 1:60 die-cast model\r\n\r\nMaterial: Metal with plastic detailing\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual blister pack'),
('P013', 'Playpop 1:60 Diecast Car Nissan Skyline GT-R (R34)', 'Take the streets by storm with the Playpop 1:60 Diecast Car Nissan Skyline GT-R (R34)! This officially licensed scaled model brings the legendary sports car to life with authentic detailing and free-wheeling action—perfect for both play and display.', 20.00, 9.90, 'SKU-13', 20, 'SC008', 'assets/images/products/prod_68b74ef33c0987.38263910.png', 'active', 0, '2025-09-02 12:09:23', '2025-09-02 12:09:23', 'Officially licensed Nissan Skyline GT-R (R34) model\r\n\r\nAuthentic 1:60 scale design with realistic details\r\n\r\nDurable die-cast metal body with plastic parts\r\n\r\nFree-wheeling action for smooth play\r\n\r\nIdeal for collectors and car enthusiasts', 'Vehicle Type: Nissan Skyline GT-R (R34)\r\n\r\nScale: 1:60\r\n\r\nMaterial: Die-cast metal with plastic detailing\r\n\r\nFunction: Free-wheeling\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual blister pack'),
('P014', 'Speed City Big Recycling Truck', 'Clean up the city with the Speed City Big Recycling Truck! Packed with lights, sounds, and realistic features, this garbage truck turns playtime into a fun, hands-on learning experience. Kids can collect, lift, and empty bins just like a real recycling truck.', 150.00, 119.00, 'SKU-14', 10, 'SC008', 'assets/images/products/prod_68b74fbb4e6aa9.15466856.png', 'active', 0, '2025-09-02 12:12:43', '2025-09-02 12:12:43', 'Realistic light and sound effects (headlights, siren, engine, rubbish emptying)\r\n\r\nFunctional lever to lift and empty bins into the truck\r\n\r\nTipping tank for realistic dumping action\r\n\r\nEncourages imaginative and educational play\r\n\r\nDurable design for hours of fun', 'Vehicle Type: Recycling / Garbage Truck\r\n\r\nFunctions: Lights, sounds, bin-lifting lever, tipping tank\r\n\r\nIncludes: 1 recycling truck + 1 rubbish bin\r\n\r\nPower: Battery operated (type may vary)\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual display box'),
('P015', 'Speed City Motorised Street Racer Bike', 'Rev up the fun with the Speed City Motorised Street Racer Bike! With lights, sounds, and the ability to pop a wheelie at the push of a button, this high-energy bike is built for nonstop action and imaginative play.', 120.00, 89.00, 'SKU-15', 10, 'SC008', 'assets/images/products/prod_68b750b428ba66.85659829.png', 'active', 0, '2025-09-02 12:16:52', '2025-09-02 12:16:52', 'Performs a wheelie at the push of a button\r\n\r\nFree-wheeling option for classic push play\r\n\r\nRealistic engine sounds, headlights, and music effects\r\n\r\nSleek, sporty design for exciting play or display\r\n\r\nEncourages imaginative, social, and cognitive play', 'Vehicle Type: Motorised Street Racer Bike\r\n\r\nFunctions: Lights, sounds, music, wheelie action, free-wheeling\r\n\r\nIncludes: 1 street racer bike\r\n\r\nPower: Battery operated (type may vary)\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual box'),
('P016', 'Disney Pixar Cars Mack Mini Racers Hauler', 'Hit the road with the Disney Pixar Cars Mack Mini Racers Hauler! Designed with authentic movie details, each transporter carries up to 18 mini metal vehicles and features an extendable ramp for quick pit stops. Complete with a matching mini die-cast racer, it’s the perfect set for every Cars fan.', 130.00, 89.90, 'SKU-16', 10, 'SC008', 'assets/images/products/prod_68b7513793a1a3.68822757.png', 'active', 0, '2025-09-02 12:19:03', '2025-09-02 12:19:03', 'Officially licensed Disney Pixar Cars hauler\r\n\r\nTrue-to-movie design with fun character details\r\n\r\nTwo-in-one play: push-along truck + drive-in transporter\r\n\r\nExtendable ramp for easy loading and unloading\r\n\r\nHolds up to 18 mini racers (sold separately)\r\n\r\nEach hauler comes with a metal mini die-cast vehicle\r\n\r\nCollect Mack (with Lightning McQueen) and Gale Beaufort (with Jackson Storm) – sold separately', 'Vehicle Type: Transporter truck with mini racer\r\n\r\nScale: Fits 1:55 mini die-cast vehicles\r\n\r\nIncludes: 1 transporter + 1 metal mini racer\r\n\r\nCapacity: Holds up to 18 mini racers\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual box (assortment varies)'),
('P017', 'Speed City Fire Command Transporter', 'Answer every call with the Speed City Fire Command Transporter! Packed with lights, sirens, vehicles, and even a helicopter, this multi-functional playset transforms from a transporter into a full fire command center—ready for action at a moment’s notice.', 150.00, 99.00, 'SKU-17', 10, 'SC008', 'assets/images/products/prod_68b75220951c71.40771126.png', 'active', 0, '2025-09-02 12:22:56', '2025-09-02 12:22:56', 'Realistic siren sounds and flashing lights\r\n\r\nConverts from transporter to full fire command playset\r\n\r\nIncludes helipad flight deck with helicopter\r\n\r\nSpeed ramp, speeder track, and working car lift\r\n\r\nFree-wheel action for smooth vehicle play\r\n\r\nEncourages imaginative rescue roleplay', 'Set Includes: Fire Command Transporter, 1 helicopter, 2 vehicles\r\n\r\nFunctions: Lights, sounds, helipad, speed ramp, car lift, track play\r\n\r\nPower: Battery operated (type may vary)\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual display box'),
('P018', 'Speed City Little City Heroes', 'Step up to the rescue with Speed City Little City Heroes! This fun-sized set includes a helicopter and fire engine, both equipped with flashing lights and realistic sounds to spark imaginative, action-packed play.', 85.00, 69.90, 'SKU-18', 10, 'SC008', 'assets/images/products/prod_68b75314719366.78889033.png', 'active', 0, '2025-09-02 12:27:00', '2025-09-02 12:27:00', 'Includes rescue helicopter and fire engine\r\n\r\nFlashing lights for realistic emergency action\r\n\r\nAuthentic sound effects to enhance play\r\n\r\nCompact design for easy play at home or on the go\r\n\r\nEncourages creativity, roleplay, and storytelling', 'Set Includes: 1 helicopter + 1 fire engine\r\n\r\nFunctions: Flashing lights & realistic sounds\r\n\r\nPower: Battery operated (type may vary)\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual display box'),
('P019', 'Playpop Helicopter', 'Take flight with the Playpop Helicopter (39 cm)! Packed with realistic features including a working winch, light and sound effects, a dart shooter, and a rotating propeller, this action-packed helicopter is perfect for rescue missions and imaginative play.', 180.00, 149.90, 'SKU-19', 10, 'SC008', 'assets/images/products/prod_68b753fbbc50d0.79072119.png', 'active', 0, '2025-09-02 12:30:51', '2025-09-02 12:30:51', 'Mechanical rotor blades with button control\r\n\r\nMotorized cable winch for lifting and lowering the stretcher\r\n\r\nSide-mounted cannon with dart shooting function\r\n\r\nRealistic light and sound effects (radio + siren)\r\n\r\nIncludes accessories: stretcher, 2 projectiles, and 3 cones\r\n\r\nEncourages imaginative rescue play and storytelling', 'Set Includes: 1 helicopter, 3 cones, 1 stretcher, 2 projectiles\r\n\r\nSize: 39 cm helicopter\r\n\r\nFunctions: Lights, sounds, mechanical propeller, lifting winch, dart shooter\r\n\r\nPower: Includes batteries (for light & sound functions)\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual display box'),
('P020', 'Disney Pixar Cars Mini Racers 3-Pack- Assorted', 'Race into fun with the Disney Pixar Cars Mini Racers 3-Pack! Each set includes three die-cast vehicles featuring fan-favorite characters from the Cars movies and Disney+ Cars On The Road series. Perfect for storytelling, racing play, and collectible display.', 85.00, 59.90, 'SKU-20', 9, 'SC008', 'assets/images/products/prod_68b7548273db87.37191204.png', 'active', 0, '2025-09-02 12:33:06', '2025-09-12 01:18:15', 'Includes 3 die-cast mini racers in each pack\r\n\r\nAuthentic designs with big personality details from Cars\r\n\r\nFree-rolling wheels for racing fun at home or on the go\r\n\r\nMultiple themed 3-packs available (sold separately)\r\n\r\nPerfect for play, display, and collecting\r\n\r\nGreat gift for Cars fans aged 3 and up', 'Vehicle Type: Disney Pixar Cars mini die-cast vehicles\r\n\r\nSet Includes: 3 mini racers (assorted characters per pack)\r\n\r\nMaterial: Die-cast metal with plastic detailing\r\n\r\nFunction: Free-rolling wheels\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: 3-pack blister card (assorted styles, sold separately)'),
('P021', 'JADA 1:24 Fast & Furious Han\'s Mazda Rx-7', 'Bring the Fast & Furious action home with the JADA 1:24 Han’s Mazda RX-7! This highly detailed die-cast model replicates Han’s iconic ride from the blockbuster franchise, perfect for collectors and car enthusiasts alike.', 169.00, 120.00, 'SKU-21', 7, 'SC008', 'assets/images/products/prod_68b75550802ac0.30480158.png', 'active', 0, '2025-09-02 12:36:32', '2025-09-02 12:36:32', 'Officially licensed Fast & Furious die-cast model\r\n\r\nAuthentic 1:24 scale replica of Han’s Mazda RX-7\r\n\r\nDetailed interior, exterior, and engine design\r\n\r\nOpening doors, hood, and trunk for realism\r\n\r\nDurable die-cast metal body with premium finish\r\n\r\nPerfect for display or adding to a Fast & Furious collection', 'Model: Han’s Mazda RX-7\r\n\r\nScale: 1:24\r\n\r\nMaterial: Die-cast metal with plastic parts\r\n\r\nFunctions: Opening doors, hood, and trunk\r\n\r\nRecommended Age: 8+ years\r\n\r\nPackaging: Collector’s display box'),
('P022', 'Petit Collage Mps Construction Magnetic Play Scene', 'Build endless adventures with the Petit Collage Construction Magnetic Play Scene! Featuring two interchangeable backgrounds and 40 magnetic pieces, kids can create busy worksites filled with animal construction workers, vehicles, and fun details—all in a portable, eco-friendly playset.', 110.00, 69.90, 'SKU-22', 10, 'SC016', 'assets/images/products/prod_68b7571b1a2a49.17818735.png', 'active', 0, '2025-09-02 12:44:11', '2025-09-02 13:11:25', 'Includes 2 magnetic scene backgrounds and 40 magnetic pieces\r\n\r\nPortable easel-style box with elastic loop for easy storage\r\n\r\nEncourages imaginative play and storytelling\r\n\r\nSturdy and durable construction for long-lasting fun\r\n\r\nMade with recycled materials and printed with vegetable inks\r\n\r\nPerfect for travel and play on the go', 'Set Includes: 2 magnetic scene backgrounds + 40 magnetic pieces\r\n\r\nPlayset Style: Portable easel box with storage loop\r\n\r\nMaterial: Recycled paper & card, non-toxic inks\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Compact, travel-ready box'),
('P023', 'playpop 12 Sides Fidget Toy - Assorted', 'Keep hands busy and minds focused with the Playpop 12 Sides Fidget Toy! This dodecagon-shaped fidget cube offers 12 unique functions to reduce stress, ease anxiety, and improve concentration—perfect for on-the-go sensory play.', 60.00, 43.99, 'SKU-23', 15, 'SC016', 'assets/images/products/prod_68b75857ae0e94.97158744.png', 'active', 0, '2025-09-02 12:49:27', '2025-09-02 12:49:27', '12 sides with different fidget functions: clicks, rolls, flicks, and spins\r\n\r\nHelps relieve stress, anxiety, and restlessness\r\n\r\nSupports focus and concentration\r\n\r\nCompact and portable—great for travel, school, or downtime\r\n\r\nMade with high-quality ABS plastic for durability\r\n\r\nAvailable in 2 assorted colors', 'Product Type: 12-sided fidget toy (dodecagon cube)\r\n\r\nSet Includes: 1 fidget toy (assorted colors)\r\n\r\nMaterial: ABS plastic, smooth finish\r\n\r\nSize: Compact, travel-friendly design\r\n\r\nRecommended Age: 6+ years\r\n\r\nPackaging: Individual unit (2-color assortment)'),
('P024', 'Taboo Classic Game', 'It’s the classic game of unspeakable fun! In Taboo Classic, race against the timer as you try to get your team to guess the word—without using the forbidden clues. With 212 cards and 848 words inspired by pop culture and trends, this fast-paced word game is perfect for parties, family nights, or on-the-go fun.', 110.00, 79.90, 'SKU-24', 10, 'SC014', 'assets/images/products/prod_68b75911d4e712.43863130.png', 'active', 0, '2025-09-02 12:52:33', '2025-09-02 12:52:33', 'Fan-favorite party game with a modern twist\r\n\r\nAvoid saying the “taboo” words while giving clues\r\n\r\nIncludes 212 cards with 848 guess words for endless replay value\r\n\r\nOnline tools available for timer, buzzer, and scorekeeping—or use the included accessories\r\n\r\nGreat group game for teens and adults, ages 13+\r\n\r\nPerfect for parties, family nights, road trips, or gatherings with friends', 'Set Includes: 212 cards (848 words), squeaker, notepad, sand timer\r\n\r\nPlayers: 4+ players\r\n\r\nRecommended Age: 13+ years\r\n\r\nGameplay: Team-based guessing game with timed rounds\r\n\r\nPackaging: Classic Taboo game box'),
('P025', 'playpop 9X9 Sudoku Strategy Game', 'Test your logic and sharpen your mind with the Playpop 9x9 Sudoku Strategy Game! Choose from puzzles in the included manual, then solve them by arranging number tiles on the board. With multiple difficulty levels, it’s a fun and challenging game for the whole family.', 90.00, 59.00, 'SKU-25', 19, 'SC014', 'assets/images/products/prod_68b7599e4eb5e2.66923837.png', 'active', 0, '2025-09-02 12:54:54', '2025-09-02 12:54:54', 'Classic Sudoku puzzle in an interactive board game format\r\n\r\nArrange numbers 1–9 without repeating in rows, columns, or zones\r\n\r\nUse red tiles for possible answers and flip to black when certain\r\n\r\nMultiple levels of difficulty for beginners to experts\r\n\r\nEncourages critical thinking, logic, and problem-solving skills\r\n\r\nPerfect for solo play or family challenges', 'Set Includes: 1 board, 99 number pieces, question & answer book, instructions\r\n\r\nGame Type: Logic and strategy puzzle game\r\n\r\nPlayers: 1+ players\r\n\r\nRecommended Age: 8+ years\r\n\r\nPackaging: Boxed set'),
('P026', 'playpop Fishing Game', 'Bring the fun of the fairground home with the Playpop Fishing Game! Watch the pond spin and the fish pop up and down as you race to catch them with your rod. A timeless family favourite that helps kids build hand-eye coordination while having endless fun.', 70.00, 49.90, 'SKU-26', 19, 'SC014', 'assets/images/products/prod_68b75a40a56b20.05290943.png', 'active', 0, '2025-09-02 12:57:36', '2025-09-02 12:57:36', 'Classic fishing game with a rotating pond and moving fish\r\n\r\nBright, colourful design to engage kids\r\n\r\n1–4 players can play together\r\n\r\nEncourages hand-eye coordination and fine motor skills\r\n\r\nFun, competitive play for the whole family', 'Set Includes: 1 rotating pond, 4 fishing rods, 21 colourful fish, manual\r\n\r\nPlayers: 1–4 players\r\n\r\nGame Type: Action/coordination game\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Boxed set'),
('P027', 'Carnival Games Tabletop Pool', '', 100.00, 69.90, 'SKU-27', 19, 'SC014', 'assets/images/products/prod_68b75b52cf2469.17931405.png', 'active', 0, '2025-09-02 13:02:10', '2025-09-02 13:02:10', 'Compact tabletop pool set (50cm length)\r\n\r\nCarnival-inspired graphics on panels and surface\r\n\r\nSmooth, fast-action playing surface\r\n\r\nSimple-setup corner design\r\n\r\nIncludes full set of balls, cues, and rack\r\n\r\nSturdy wood construction for durability', 'Set Includes: 1 pool table, 16 billiard balls, 2 cues, 1 triangle rack, instructions\r\n\r\nDimensions: 31cm x 50cm (assembled)\r\n\r\nMaterial: Wood construction with printed graphics\r\n\r\nGame Type: Tabletop pool/billiards\r\n\r\nRecommended Age: 6+ years\r\n\r\nAssembly: Easy setup'),
('P028', 'UNO Show Em No Mercy', 'Brace yourself for the most ruthless version of UNO yet! UNO Show ‘Em No Mercy packs in extra cards, brutal new rules, and merciless penalties like Draw 10 and Skip Everyone. Outlast your rivals by emptying your hand—or knocking them out completely!', 40.00, 25.00, 'SKU-28', 15, 'SC015', 'assets/images/products/prod_68b75bba1557b1.71260742.png', 'active', 0, '2025-09-02 13:03:54', '2025-09-02 13:03:54', 'Includes 56 extra cards for a super-charged, merciless edition of UNO\r\n\r\nNew action cards: Skip Everyone, Wild Draw 6, Wild Draw 10\r\n\r\nStacking Rule lets penalties pile up until one unlucky player takes them all\r\n\r\nPlay a 7 or 0 to swap hands with another player\r\n\r\nMercy Rule: players with 25+ cards are eliminated\r\n\r\nTwo ways to win: empty your hand OR knock everyone else out\r\n\r\nPerfect for family nights, parties, and travel', 'Players: 2–10\r\n\r\nRecommended Age: 7+ years\r\n\r\nContents: Deck with standard UNO cards + 56 additional cards, instructions\r\n\r\nPlay Time: 20–45 minutes (depending on group size)\r\n\r\nBrand: Mattel\r\n\r\nCategory: Card/party game'),
('P029', 'Cardinal Games Jumanji Jumbo Card Game', 'Step into the wild world of Jumanji with this Jumbo Card Game! Solve riddles, complete challenges, and test your luck as you race to escape the jungle. Perfect for family game nights and adventurous players of all ages.', 29.90, 19.00, 'SKU-29', 20, 'SC015', 'assets/images/products/prod_68b75c66ddf2d4.27767434.png', 'active', 0, '2025-09-02 13:06:46', '2025-09-02 13:06:46', 'Inspired by the classic Jumanji adventure game\r\n\r\nIncludes fun challenges and riddles to test creativity and teamwork\r\n\r\nEasy to set up and play—great for family gatherings or parties\r\n\r\nCompact and portable for on-the-go fun\r\n\r\nEncourages imagination, problem-solving, and laughter', 'Players: 2+\r\n\r\nRecommended Age: 5+ years\r\n\r\nContents: 50 Challenge cards, 1 Life Tracker, 1 Riddle Answer card, 1 Character Punch Sheet, 1 Instruction card\r\n\r\nBrand: Cardinal Games\r\n\r\nCategory: Card/party game'),
('P030', 'Monopoly Super Electronic Banking', 'Level up family game night with Monopoly Super Electronic Banking! Featuring tap technology and unique rewards for each player token, this modern twist on the classic game makes buying, selling, and trading faster and more exciting than ever.', 200.00, 149.90, 'SKU-30', 10, 'SC014', 'assets/images/products/prod_68b75cd4aea438.95814908.png', 'active', 0, '2025-09-02 13:08:36', '2025-09-02 13:08:36', 'Electronic Banking Unit: Tap technology keeps the game quick and easy—no paper money needed.\r\n\r\nUnique Rewards: Each token comes with a special bank card that unlocks unique bonuses.\r\n\r\nFlight Spaces: Take a flight and travel instantly to any property on the board.\r\n\r\nTrading Spaces: Land on Forced Trade spaces to swap properties with other players.\r\n\r\nModern Monopoly Fun: A fresh, fast-paced version of the world’s favorite property game.', 'Players: 2–4\r\n\r\nRecommended Age: 8+ years\r\n\r\nIncludes: Gameboard, Ultimate Banking unit, 4 tokens, 4 bank cards, 22 title deed cards, 49 cards, 2 dice, and instructions\r\n\r\nBrand: Hasbro Gaming\r\n\r\nCategory: Board Game'),
('P031', 'Battleship Royale', 'Many will sink. One will survive. Battleship Royale brings the classic naval combat game to a thrilling new level—up to 6 players battle it out on a shared grid to see who will be the last ship standing!', 130.00, 99.99, 'SKU-31', 10, 'SC014', 'assets/images/products/prod_68b75d4403b011.65157123.png', 'active', 0, '2025-09-02 13:10:28', '2025-09-02 13:10:28', 'Party-Style Battleship: The first-ever edition that lets up to 6 players face off at once.\r\n\r\nShared Battle Grid: Everyone plays on one grid, marking hits and misses together.\r\n\r\nSecret Tracking: Ship cards slot into handheld Command Centers so only you know your fleet’s location.\r\n\r\nDice-Driven Combat: Roll, fire, and see who sinks first—last ship afloat wins!\r\n\r\nAdvanced Mode: Add submarines with sonar powers for a strategic twist.\r\n\r\nBuilt-In Storage: Gameboard includes trays for pegs, making setup and cleanup easy.', 'Players: 2–6\r\n\r\nRecommended Age: 8+ years\r\n\r\nIncludes: Battle Grid, 4 storage trays, die, 60 green miss pegs, 60 red hit pegs, 50 orange sunk pegs, standard deck, advanced deck, 6 Command Centers, instructions\r\n\r\nBrand: Hasbro Gaming\r\n\r\nCategory: Strategy / Family Game'),
('P032', 'Barbie Movie Story Starter Pack - Assorted', 'Enhance playtime with the Barbie Basic Accessories Set! This collection includes essential accessories to style, accessorize, and inspire imaginative fun with your Barbie dolls.', 30.00, 19.90, 'SKU-32', 10, 'SC013', 'assets/images/products/prod_68b7613ce98455.60330399.png', 'active', 0, '2025-09-02 13:27:24', '2025-09-02 13:34:57', 'Officially licensed Barbie accessories\r\n\r\nPerfect for fashion, roleplay, and creative storytelling\r\n\r\nCompatible with other Barbie dolls and playsets\r\n\r\nEncourages imaginative play and fine motor skills', 'Brand: Barbie\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual accessory set'),
('P033', 'Disney Princess Mermaid to Princess Ariel Doll', 'Bring Ariel’s magical world to life with the Disney Princess Mermaid to Princess Ariel Doll! Transform her from an undersea mermaid to a glamorous princess with two signature fashions and accessories, inspired by The Little Merma', 80.00, 59.90, 'SKU-33', 10, 'SC012', 'assets/images/products/prod_68b7629ff3b332.40537670.png', 'active', 0, '2025-09-02 13:33:20', '2025-09-02 13:33:20', 'Two enchanting looks: mermaid tail & seashell top and pink princess gown\r\n\r\nIncludes 3 accessories: starfish hair clip, golden tiara, and pink shoes\r\n\r\nLong hair for brushing and styling fun\r\n\r\nPerfect for imaginative play, re-creating favorite scenes, or inventing new adventures\r\n\r\nPart of the Disney Princess collection—fans can collect other dolls and accessories (sold separately)', 'Set Includes: 1 doll, 2 fashions, 3 accessories\r\n\r\nBrand: Disney Princess\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual doll set'),
('P034', 'Disney Princess Ariel\'s Land & Sea Castle', 'Bring Ariel’s world to life with the Disney Princess Ariel’s Land & Sea Castle! This stackable playset features her land kingdom above the waves and underwater kingdom below, complete with furniture, accessories, Flounder, and Ariel in two fashions for endless imaginative adventures.', 250.00, 199.90, 'SKU-34', 10, 'SC013', 'assets/images/products/prod_68b763d2e0a020.58981947.png', 'active', 0, '2025-09-02 13:38:26', '2025-09-02 13:38:26', 'Two magical kingdoms: land above the waves and underwater world\r\n\r\nIncludes Ariel doll (3.5”) with 2 fashions: mermaid outfit and pink castle gown\r\n\r\nFeatures Flounder figure and over 10 accessories for interactive play\r\n\r\nLand kingdom includes spinning dance floor, bedroom, dining room with food-inspired play pieces\r\n\r\nUnderwater kingdom has splashable pool, seashell swing, and spinning fountain\r\n\r\nStackable design: connect multiple Storytime Stackers for a larger kingdom\r\n\r\nEncourages imaginative storytelling and recreating movie moments\r\n\r\nPart of the Disney Princess stacking playsets collection (sold separately)', 'Set Includes: 1 stackable playset, 1 doll, 1 character friend, 10+ accessories\r\n\r\nPlayset Height: 31.1 cm / 12.25”\r\n\r\nBrand: Disney Princess\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual playset\r\n\r\nColors and decorations may vary'),
('P035', 'Barbie Signature Mattel 80th Classic - Enchanted Doll', 'Celebrate 80 years of Mattel with the Barbie Enchanted Evening Doll! Part of the Replay the Classics collection, this elegant doll pays tribute to the iconic 1960s Barbie fashion with a shimmering gown and glamorous accessories.', 150.00, 99.90, 'SKU-35', 8, 'SC012', 'assets/images/products/prod_68b76472f18438.89290573.png', 'active', 0, '2025-09-02 13:41:06', '2025-09-02 13:41:06', 'Part of Mattel’s Replay the Classics collection, celebrating 80 years of Barbie\r\n\r\nElegant satiny pink gown with draped rose accent and faux fur stole\r\n\r\nPosable doll with swept-up hair, white earrings, and matching choker\r\n\r\nIncludes a beaded handbag and delicate strappy heels\r\n\r\nPerfect for imaginative play or display as a collector’s item\r\n\r\nColors and decorations may vary', 'Set Includes: 1 Barbie doll, 1 accessory (doll cannot stand alone)\r\n\r\nBrand: Barbie Signature\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual doll box'),
('P036', 'Monster High Draculaura Doll with Pet Bat-Cat Count Fabulous and Accessories', 'Bring the spooky style of Monster High home with the Draculaura Doll! This set includes Draculaura, her pet bat-cat Count Fabulous, and stylish accessories, perfect for fans of the iconic Monster High franchise.', 150.00, 89.90, 'SKU-36', 8, 'SC012', 'assets/images/products/prod_68b7658e98c6e9.85013496.png', 'active', 0, '2025-09-02 13:45:50', '2025-09-02 13:45:50', 'Features Draculaura with signature pink and black hair and gothic-inspired outfit\r\n\r\nComes with her pet bat-cat, Count Fabulous\r\n\r\nIncludes stylish accessories for imaginative play\r\n\r\nIdeal for collectors or Monster High fans looking to expand their collection\r\n\r\nCompact and lightweight packaging for display or on-the-go fun', 'Set Includes: 1 Draculaura doll, 1 pet bat-cat, multiple accessories\r\n\r\nPackage Size: 12.76 x 9.49 x 2.72 inches\r\n\r\nWeight: 0.9 lb\r\n\r\nBrand: Monster High\r\n\r\nRecommended Age: 6+ years\r\n\r\nPackaging: Individual doll set'),
('P037', 'LEGO Classic Creative Food Friends', 'Unleash your child’s imagination with the LEGO Classic Creative Food Friends Set (11039)! Build and rebuild adorable food-inspired characters—cupcake, ice cream, avocado, and taco—while exploring endless creative possibilities. Perfect for kids 4 years and up.', 200.00, 99.00, 'SKU-37', 10, 'SC006', 'assets/images/products/prod_68b766d59382c3.00800821.png', 'active', 0, '2025-09-02 13:51:17', '2025-09-12 16:39:57', 'Imaginative Play: Create fun food characters and enjoy interactive roleplay\r\n\r\nColorful Bricks & Fun Parts: Includes eyes, mouths, and decorative elements for expressive models\r\n\r\nEndless Creativity: Mix and match bricks to rebuild cupcakes, bubble tea, pears, paninis, and more\r\n\r\nStep-by-Step Instructions: Intuitive building guide helps children develop confidence and skills\r\n\r\nSkill Development: Encourages focus, problem-solving, and fine motor skills\r\n\r\nExpandable Fun: Compatible with other LEGO Classic sets for even more creative building', 'Set Includes: LEGO bricks and instruction booklet\r\n\r\nNumber of Pieces: 150\r\n\r\nModel Sizes: Cupcake model approx. 6 cm tall x 4 cm wide x 1 cm deep\r\n\r\nRecommended Age: 4+ years\r\n\r\nBrand: LEGO Classic\r\n\r\nPackaging: Individual building set'),
('P038', 'LEGO Icons Bonsai Tree 10281', 'Bring calm and creativity into your space with the LEGO Icons Bonsai Tree (10281). This buildable model lets you style the tree with pink blossoms or green leaves, offering a relaxing and customizable display for your home or office.', 200.00, 119.90, 'SKU-38', 10, 'SC006', 'assets/images/products/prod_68b7678cbcf692.73801929.png', 'active', 0, '2025-09-02 13:54:20', '2025-09-12 16:38:42', 'Customizable Design: Swap blossoms for leaves to celebrate the seasons or create your own style\r\n\r\nRelaxing Build: Hands-on LEGO project that promotes mindfulness and creativity\r\n\r\nComplete Display: Includes pot and stand for a polished, finished look\r\n\r\nPerfect for display on desks, shelves, or as a gift for LEGO and bonsai enthusiasts', 'Brand: LEGO Icons\r\n\r\nSet Number: 10281\r\n\r\nRecommended Age: 18+\r\n\r\nPackaging: Individual building set\r\n\r\nCategory: LEGO Creator / Display Model'),
('P039', 'LEGO Classic Creative Suitcase (10713)', 'Take your imagination anywhere with the LEGO Classic Creative Suitcase (10713)! This portable set comes with colorful bricks and accessories in a handy suitcase, perfect for inspiring endless creativity for kids aged 4 and up.', 150.00, 99.00, 'SKU-39', 10, 'SC006', 'assets/images/products/prod_68b7686a185f99.74583076.png', 'active', 0, '2025-09-02 13:58:02', '2025-09-12 16:39:41', 'Portable Creativity: Convenient yellow suitcase with organized compartments for easy storage and play\r\n\r\nBuild Anything: Includes 213 colorful LEGO bricks and accessories for imaginative building\r\n\r\nPerfect Starter Set: Ideal for beginners and young builders exploring creative play\r\n\r\nExpandable Fun: Access more building instructions, ideas, and inspiration at LEGO.com/classic\r\n\r\nSkill Development: Encourages problem-solving, fine motor skills, and creativity', 'Number of Pieces: 213\r\n\r\nAge Recommendation: 4+\r\n\r\nDimensions: 26 cm (H) x 28 cm (W) x 6 cm (D)\r\n\r\nBrand: LEGO Classic\r\n\r\nPackaging: Individual suitcase building set'),
('P040', 'LEGO Minecraft The Wolf Stronghold (21261)', 'Embark on epic adventures with LEGO Minecraft The Wolf Stronghold (21261)! Build the Wolf Tamer’s fortress, explore the forest, and battle skeletons while taming wolves in this action-packed Minecraft-themed LEGO set.', 200.00, 130.00, 'SKU-40', 13, 'SC006', 'assets/images/products/prod_68b769514fef45.93457546.png', 'active', 0, '2025-09-02 14:01:53', '2025-09-12 16:39:03', 'Interactive Adventure Set: Includes Wolf Tamer, 2 skeletons, and 2 wolves for immersive play\r\n\r\nDetailed Fortress: Build a stronghold with a large wolf head that changes expression from friendly to angry\r\n\r\nCreative Tools: Features blast furnace, smithing table, anvil, and crafting station for fun Minecraft-style building\r\n\r\nForest Exploration: Includes trees, boulders, mushrooms, and sweet berry bushes for creative storytelling\r\n\r\nDigital Experience: Use the LEGO Builder app to view 3D instructions, zoom, rotate, save progress, and track your build\r\n\r\nGift-Ready: Perfect for Minecraft fans aged 8+ to inspire imaginative play and hands-on creativity', 'Number of Pieces: 312\r\n\r\nRecommended Age: 8+\r\n\r\nModel Dimensions (assembled): 11+ cm (H) x 16+ cm (W) x 12+ cm (D)\r\n\r\nBrand: LEGO Minecraft\r\n\r\nPackaging: Individual building set'),
('P041', 'LEGO Icons Flower Bouquet 10280', 'Create a stunning and unique gift with the LEGO Icons Flower Bouquet (10280)! Build a vibrant arrangement of colorful LEGO blooms with adjustable stems for a customizable display that will brighten any space.', 250.00, 200.00, 'SKU-41', 9, 'SC006', 'assets/images/products/prod_68b769c598b654.93982856.png', 'active', 0, '2025-09-02 14:03:49', '2025-09-12 16:39:24', 'Customizable Bouquet: Stems can be adjusted to fit any vase or display style\r\n\r\nCreative & Mindful Build: A relaxing hands-on LEGO project for all ages\r\n\r\nVibrant Design: Includes a variety of colorful LEGO flowers for a realistic and eye-catching arrangement\r\n\r\nPerfect Gift: Ideal for special occasions or as a decorative display piece', 'Brand: LEGO Icons\r\n\r\nSet Number: 10280\r\n\r\nRecommended Age: 18+\r\n\r\nPackaging: Individual building set\r\n\r\nCategory: LEGO Creator / Display Model'),
('P042', 'My Story Princess Castle Vanity Table Set', 'Step into a magical fairytale with the My Story Princess Castle Vanity Table Set! This enchanting set features a light-up mirror, magical wand, music, and 15 makeup and hair accessories for endless imaginative play.', 250.00, 150.00, 'SKU-42', 9, 'SC021', 'assets/images/products/prod_68b76a6c91ffa0.51305571.png', 'active', 0, '2025-09-02 14:06:36', '2025-09-02 14:08:22', 'Interactive Vanity Table: Lights, sounds, and a high-definition mirror for magical pretend play\r\n\r\nMagic Wand & Accessories: Includes wand, working hairdryer with sound, brush, lipsticks, nail polish, rings, bracelets, hair clips, and chair stool\r\n\r\nEngaging Play Effects: Four different sounds from the wand and table, plus colored lights and ringing castle bell effects\r\n\r\nAmple Storage: Transparent drawer to neatly store all accessories\r\n\r\nPerfect for Fairytale Fun: Inspires creativity, roleplay, and imaginative storytelling', 'Set Includes: 1 vanity table with lights & music, 1 chair stool, 1 magic wand, 1 brush, 2 lipsticks, 1 working hairdryer, 2 hair clips, 2 bracelets, 3 rings, 3 nail polishes\r\n\r\nBrand: My Story\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual playset'),
('P043', 'Make It Real Party Nails Glitter Studio', 'Turn every nail into a dazzling party with the Make It Real Party Nails Glitter Studio! This mess-free kit makes it easy to create sparkling, glittery nails at home—perfect for parties or everyday fun.', 180.00, 100.00, 'SKU-43', 10, 'SC021', 'assets/images/products/prod_68b76b4f93a203.32738245.png', 'active', 0, '2025-09-02 14:10:23', '2025-09-02 14:10:23', 'Mess-Free Glitter: Self-contained pods make switching colors easy without any spills\r\n\r\nComplete Nail Kit: Includes sparkle spinner, 5 glitter pods, sparkle primer, clear nail polish, brush, sticker sheet, and instructions\r\n\r\nFun & Easy: Create 1 or multiple nails in minutes with simple, mess-free application\r\n\r\nHassle-Free Removal: Glitter can be removed quickly and cleanly\r\n\r\nCompact Design: Ideal for home use, sleepovers, or travel', 'ox Size: 29 cm (W) x 27 cm (H) x 7 cm (D)\r\n\r\nSet Includes: 1 sparkle spinner, 5 glitter pods, 1 sparkle primer, 1 clear nail polish, 1 brush, 1 sticker sheet, 1 instruction sheet\r\n\r\nRecommended Age: 6+ years\r\n\r\nBrand: Make It Real\r\n\r\nPackaging: Individual activity kit'),
('P044', 'My Story Super Smart Cash Register', 'Bring the excitement of shopping to life with the My Story Super Smart Cash Register! This interactive playset features a working calculator, scale, microphone, and realistic sound effects, giving kids a fun and educational supermarket experience.', 180.00, 120.00, 'SKU-44', 10, 'SC021', 'assets/images/products/prod_68b76be8a16275.81280700.png', 'active', 0, '2025-09-02 14:12:56', '2025-09-02 14:12:56', 'Interactive Play: Includes working cash register, calculator, scale, and microphone with voice and sound effects\r\n\r\nRealistic Accessories: Comes with coins, notes, credit card, play food, boxed goods, and shopping basket (25 pieces total)\r\n\r\nBarcode Scanning Fun: Kids can scan items with play barcodes for immersive store play\r\n\r\nEducational & Fun: Encourages social skills, independence, counting, and imaginative roleplay\r\n\r\nComplete Supermarket Experience: Perfect for kids to run their own store and create realistic scenarios', 'Set Includes: 1 cash register, 25 accessories including basket, coins, notes, credit card, play food, milk, and boxed goods\r\n\r\nBrand: My Story\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual playset'),
('P045', 'My Story Grocery Shopping Cart Set', 'Bring the excitement of shopping to life with the My Story Super Smart Cash Register! This interactive playset features a working calculator, scale, microphone, and realistic sound effects, giving kids a fun and educational supermarket experience.', 150.00, 88.00, 'SKU-45', 8, 'SC021', 'assets/images/products/prod_68b76caa22aa90.32416578.png', 'active', 0, '2025-09-02 14:16:10', '2025-09-02 14:16:10', 'Interactive Play: Includes working cash register, calculator, scale, and microphone with voice and sound effects\r\n\r\nRealistic Accessories: Comes with coins, notes, credit card, play food, boxed goods, and shopping basket (25 pieces total)\r\n\r\nBarcode Scanning Fun: Kids can scan items with play barcodes for immersive store play\r\n\r\nEducational & Fun: Encourages social skills, independence, counting, and imaginative roleplay\r\n\r\nComplete Supermarket Experience: Perfect for kids to run their own store and create realistic scenarios', 'Set Includes: 1 cash register, 25 accessories including basket, coins, notes, credit card, play food, milk, and boxed goods\r\n\r\nBrand: My Story\r\n\r\nRecommended Age: 3+ years\r\n\r\nPackaging: Individual playset');
INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `sale_price`, `sku`, `stock_quantity`, `category_id`, `image`, `status`, `featured`, `created_at`, `updated_at`, `product_features`, `product_specifications`) VALUES
('P046', 'Top Tots My First Grand Piano Pink', 'Introduce your little one to the magic of music with the Top Tots My First Grand Piano Pink! This interactive piano encourages creativity, confidence, and imaginative play with lights, sounds, and recording features.', 250.00, 170.00, 'SKU-46', 10, 'SC021', 'assets/images/products/prod_68b76d6335f473.37265005.png', 'active', 0, '2025-09-02 14:19:15', '2025-09-02 14:19:15', 'Interactive Music Play: Includes sound effects, demo songs, drum beats, and microphone for immersive fun\r\n\r\nRecording & Playback: Kids can record their performances and play them back to improve their skills\r\n\r\nFlashing Lights: Adds a realistic performance experience and makes your child feel like a star\r\n\r\n37-Key Keyboard: Full-size keys for creative exploration and musical learning\r\n\r\nAdjustable Stand: Approximately 60 cm high for comfortable play', 'Keys: 37\r\n\r\nAccessories: Built-in microphone\r\n\r\nHeight: Stand approx. 60 cm\r\n\r\nRecommended Age: 3+ years\r\n\r\nBrand: Top Tots\r\n\r\nPackaging: Individual playset'),
('P047', 'NERF Elite 2.0 Stormcharge', 'Gear up for high-speed NERF battles with the NERF Elite 2.0 Stormcharge! This motorized blaster launches darts with power and precision and can be customized into 4 different styles for endless indoor and outdoor fun.', 200.00, 150.00, 'SKU-47', 15, 'SC019', 'assets/images/products/prod_68b76de0d41247.40477072.png', 'active', 0, '2025-09-02 14:21:20', '2025-09-02 14:21:20', 'Motorized Dart Blasting: Fire 10 darts in a row with the clip-fed Stormcharge blaster\r\n\r\nCustomizable Design: Removable stock and barrel allow 4 different blaster styles\r\n\r\nComplete Ammo Supply: Includes 20 NERF Elite 2.0 foam darts for fast reloading and extended play\r\n\r\nFast-Action Play: Acceleration button powers the motor, trigger fires darts quickly\r\n\r\nIndoor & Outdoor Fun: Perfect for active play sessions and NERF battles with friends', 'Includes: Blaster, removable stock, removable barrel, 10-dart clip, 20 NERF Elite 2.0 foam darts, instructions\r\n\r\nBatteries Required: 4x 1.5V AA alkaline (not included)\r\n\r\nRecommended Age: 8+ years\r\n\r\nBrand: NERF (Hasbro)\r\n\r\nPackaging: Individual blaster set'),
('P048', 'Toy Laser Tag Shooting Game – 2 Player Set', 'Bring the excitement of arcade battles home with the Toy Laser Tag Shooting Game! This 2-player set features light-up blasters and vests with sound and vibration effects for immersive gameplay.', 150.00, 88.00, 'SKU-48', 8, 'SC019', 'assets/images/products/prod_68b76e9c35ef93.38727175.png', 'active', 0, '2025-09-02 14:24:28', '2025-09-02 14:30:13', '2-Player Battles: Perfect for 1-vs-1 matches or combine multiple sets for free-for-all fun\r\n\r\nInteractive Blasters & Vests: Lights, sounds, and vibration respond to hits for a realistic laser tag experience\r\n\r\nPortable & Safe: Designed for safe indoor and outdoor play for kids and adults\r\n\r\nExpand Your Arsenal: Add extra sets to increase the number of players and complexity of battles', 'Includes: 2 blaster guns, 2 armor vests\r\n\r\nRecommended Age: 8+ years\r\n\r\nBrand: Sharper Image\r\n\r\nPackaging: 2-player set'),
('P049', 'Playpop Height Adjustable Basketball Stand & Ball', 'Grow your little athlete’s skills with the playpop Height Adjustable Basketball Stand & Ball! This adjustable hoop and stand provide a realistic play experience and develops hand-eye coordination, perfect for both kids and adults.', 110.00, 69.90, 'SKU-49', 10, 'SC017', 'assets/images/products/prod_68b76f1f425346.22348511.png', 'active', 0, '2025-09-02 14:26:39', '2025-09-02 14:26:39', 'Adjustable Height: Stand adjusts from 60 cm to 120 cm to suit children as they grow\r\n\r\nSturdy & Stable: Base can be filled with sand or water for extra stability\r\n\r\nComplete Set: Includes basketball, backboard, hoop, base, and inflator\r\n\r\nSkill Development: Encourages hand-eye coordination, motor skills, and physical activity\r\n\r\nEasy Setup & Storage: Simple to assemble and convenient to store when not in use', 'Dimensions: Approx. 120 cm x 34 cm x 29 cm\r\n\r\nIncludes: 1 adjustable stand, backboard, hoop, base, basketball, inflator, instructions\r\n\r\nAssembly: Required\r\n\r\nRecommended Age: 3+ years\r\n\r\nBrand: playpop\r\n\r\nPackaging: Individual playset'),
('P050', 'Playpop Junior Archery Set', 'Introduce your child to the exciting world of archery with the playpop Junior Archery Set! Safe, lightweight, and fun, this set helps kids develop focus, hand-eye coordination, and confidence through indoor and outdoor play.', 100.00, 60.00, 'SKU-50', 10, 'SC020', 'assets/images/products/prod_68b770a17ec3c8.09594332.png', 'active', 0, '2025-09-02 14:33:05', '2025-09-02 14:33:05', 'Child-Friendly Archery: Safe, lightweight bow and suction-tip arrows designed for kids\r\n\r\nSkill Development: Encourages hand-eye coordination, focus, and confidence\r\n\r\nComplete Set: Includes bow, quiver, target, and 5 suction-tip arrows\r\n\r\nPortable & Organized: Quiver makes it easy to carry and store the arrows\r\n\r\nFun & Vibrant: Bright colors make the set visually appealing and ideal for group play\r\n\r\nTeam Play: Great for friendly competitions and cooperative activities', 'Includes: 1 bow, 1 quiver, 1 target, 5 suction-tip arrows\r\n\r\nRecommended Age: 4+ years\r\n\r\nBrand: playpop\r\n\r\nPackaging: Individual archery set\r\n\r\nAssembly: Minimal assembly required'),
('P051', 'Globber Go Bike New Red', 'Introduce your toddler to the joys of cycling with the Globber Go Bike New Red! This innovative balance bike helps kids aged 2–5 develop balance, coordination, and motor skills while enjoying safe, smooth rides.', 250.00, 190.00, 'SKU-51', 10, 'SC018', 'assets/images/products/prod_68b7715f333be5.41962043.png', 'active', 0, '2025-09-02 14:36:15', '2025-09-02 14:36:15', 'Innovative 2-Stage Reversible Frame: Low frame position for beginners, high frame for continued use as your toddler grows\r\n\r\nAdjustable Saddle & Handlebars: 6-height PU saddle (3 per frame position) and 2-height adjustable curved handlebars with ergonomic grips\r\n\r\nComfortable EVA Foam Wheels: 10” puncture-free wheels provide smooth, safe, and maintenance-free rides\r\n\r\nDurable & Safe Design: Sturdy steel frame supports up to 20kg and ensures longevity\r\n\r\nSkill Development: Helps toddlers improve balance, coordination, and confidence', 'Includes: Bike frame, 2 wheels, handlebars, assembly tools\r\n\r\nRecommended Age: 2–5 years\r\n\r\nMaximum Weight Capacity: 20 kg\r\n\r\nWheel Size: 254 mm (10”) EVA foam\r\n\r\nAdjustable Saddle Heights (Low frame): 34 cm, 36.5 cm, 39 cm\r\n\r\nAdjustable Saddle Heights (High frame): 36.5 cm, 39 cm, 41.5 cm\r\n\r\nHandlebar Heights: 47.5 cm and 50.5 cm from ground\r\n\r\nAssembly: Required'),
('P052', 'My Story Royal Cream Balloon Sheep', 'Cuddle up with the My Story Royal Cream Balloon Sheep! Soft, fluffy, and charmingly designed in a balloon shape, this collectible sheep is perfect for hugs and comfort.', 80.00, 59.90, 'SKU-52', 10, 'SC025', 'assets/images/products/prod_68b772dcefdc50.32686975.png', 'active', 0, '2025-09-02 14:42:36', '2025-09-02 14:42:36', 'Soft & Fluffy: Made with plush material for maximum cuddliness\r\n\r\nUnique Design: Adorable balloon-shaped sheep with embroidery details\r\n\r\nComforting Companion: Great for snuggling, listening, and emotional comfort\r\n\r\nCollectible: Available in multiple colors to collect and display', 'Includes: 1 Balloon Sheep\r\n\r\nRecommended Age: 3+ years\r\n\r\nMaterial: Soft plush\r\n\r\nDimensions: Approx. size varies by design'),
('P053', 'Friends for Life Sit & Smile Mr. Teddy', 'Meet Sit & Smile Mr. Teddy, the perfect companion for cuddles and conversation! Soft, huggable, and always ready to listen, he’s here to brighten your day.', 60.00, 40.00, 'SKU-53', 19, 'SC024', 'assets/images/products/prod_68b7737d90eac7.54977614.png', 'active', 0, '2025-09-02 14:45:17', '2025-09-02 14:45:17', 'Soft & Cuddly: Made with plush fabric for hugs and comfort\r\n\r\nCompanionable Design: Perfect for sitting, chatting, and emotional support\r\n\r\nComforting Presence: Helps provide comfort and companionship for children and adults alike', 'Includes: 1 Teddy\r\n\r\nMaterial: Soft plush fabric\r\n\r\nRecommended Age: 3+ years\r\n\r\nDimensions: Vary depending on model'),
('P054', 'Disney Hugs Of Love Collection - Jumbo Scrump (26 inch)', 'Cuddle up with Jumbo Scrump, a super soft 26-inch plush from the Disney Hugs of Love Collection! Perfect for gifts, collection, or snuggly play.', 300.00, 220.00, 'SKU-54', 10, 'SC026', 'assets/images/products/prod_68b773f4c74414.96646976.png', 'active', 0, '2025-09-02 14:47:16', '2025-09-02 14:49:20', 'uper Soft & Cuddly: Plush design with charming embroidered details\r\n\r\nComforting Companion: Ideal for hugs, comfort, and imaginative play\r\n\r\nGift-Ready: Perfect for birthdays, get well wishes, festivals, or any special occasion\r\n\r\nCollectible: Part of the Disney Stitch series, great for fans and collectors', 'Includes: 1 Jumbo Scrump Plush\r\n\r\nSize: 26 inches (65 cm)\r\n\r\nMaterial: Soft plush with embroidered accents\r\n\r\nRecommended Age: 3+ years'),
('P055', 'Disney Hugs Of Love Collection - Jumbo Stitch (26 inch)', 'Snuggle up with Jumbo Stitch, a 26-inch plush from the Disney Hugs of Love Collection! Soft, adorable, and perfect for cuddles, play, or display.', 300.00, 220.00, 'SKU-55', 10, 'SC026', 'assets/images/products/prod_68b774601c59f3.70629111.png', 'active', 0, '2025-09-02 14:49:04', '2025-09-02 14:49:04', 'Super Soft & Huggable: Plush design with detailed embroidered accents\r\n\r\nComforting Companion: Great for hugs, imaginative play, and emotional comfort\r\n\r\nGift-Ready: Ideal for birthdays, get well wishes, festivals, or any special occasion\r\n\r\nCollectible: Part of the Disney Stitch series, perfe', 'Includes: 1 Jumbo Stitch Plush\r\n\r\nSize: 26 inches (65 cm)\r\n\r\nMaterial: Plush with embroidered details\r\n\r\nRecommended Age: 3+ years'),
('P056', 'Playpop Dancing and Talking Cactus Plush Toy', 'Bring fun to playtime with the Dancing and Talking Cactus Plush Toy! This interactive cactus talks, dances, sings, and repeats everything you say, making it a delightful companion for kids and adults alike.', 80.00, 60.00, 'SKU-56', 10, 'SC002', 'assets/images/products/prod_68b774de6b2220.23690920.png', 'active', 0, '2025-09-02 14:51:10', '2025-09-02 14:51:10', 'Interactive Fun: Talks back, records, repeats, and imitates sounds\r\n\r\nDance & Twist: Moves and dances to built-in music for endless entertainment\r\n\r\nBuilt-in Music: Plays songs to groove along with\r\n\r\nEngaging Play: Encourages imaginative play and interactive fun for all ages', 'Includes: 1 Dancing and Talking Cactus Plush Toy\r\n\r\nFunctions: Talk back, dance, twist, music playback\r\n\r\nRecommended Age: 3+ years'),
('P057', 'Blokees Marvel: Spidey and His Amazing Friends', 'Blokees Marvel: Spidey and His Amazing Friends lets kids build and play with their favorite superheroes. Easy to assemble, fully poseable, and perfect for imaginative adventures.', 70.00, 49.90, 'SKU-57', 10, 'SC027', 'assets/images/products/prod_68b77554163382.35005253.png', 'active', 0, '2025-09-02 14:53:08', '2025-09-02 14:53:08', 'Buildable and poseable superhero figures\r\n\r\nInspired by Spidey and His Amazing Friends animated series\r\n\r\nSimple, child-friendly assembly\r\n\r\nFully articulated joints for dynamic posing\r\n\r\nEncourages imaginative play and creativity', 'Recommended Age: 3+ years\r\n\r\nMaterial: Safe, durable plastic\r\n\r\nAssembly: Snap-together, no tools required\r\n\r\nCharacters: Spidey and friends (varies by set)\r\n\r\nPackaging: Individual figure set'),
('P058', 'Blokees Ultraman Galaxy Version 12: Blazar’s Starlight', 'Unbox the excitement with Blokees Ultraman Galaxy Version 12: Blazar’s Starlight! Each pack includes a surprise Ultraman character, ready to be assembled and posed for action-packed play. Complete with a collectible Ultraman card, it’s a must-have for fans and collectors alike.', 60.00, 49.90, 'SKU-58', 20, 'SC027', 'assets/images/products/prod_68b7759906ec95.33509652.png', 'active', 0, '2025-09-02 14:54:17', '2025-09-02 14:54:17', '9 unique Ultraman characters to collect\r\n\r\nEasy-to-build, articulated action figure\r\n\r\nInspired by the Ultraman Blazar movie\r\n\r\nFully poseable for dynamic play and display\r\n\r\nIncludes a collectible Ultraman card', 'Recommended Age: 3+ years\r\n\r\nMaterial: High-quality, child-safe plastic\r\n\r\nAssembly: Snap-fit, no tools required\r\n\r\nFigure Type: Poseable Ultraman action figure\r\n\r\nCollectibles: 1 character figure + 1 Ultraman card per box\r\n\r\nPackaging: Blind box (character revealed upon opening)'),
('P059', 'My Melody Make it Or Bake It', 'Celebrate the holidays with the My Melody Make it Or Bake It festive building set! Part of the Sanrio-licensed Holiday Collection, each set brings seasonal cheer with charming block creations designed by Play Nation Studio.', 80.00, 59.90, 'SKU-59', 19, 'SC028', 'assets/images/products/prod_68b775d3089235.49445407.png', 'active', 0, '2025-09-02 14:55:15', '2025-09-11 22:33:40', 'Officially licensed Sanrio Holiday Collection\r\n\r\n5 unique festive designs to collect (sold separately)\r\n\r\nFun and engaging building experience for all ages\r\n\r\nPerfect as a holiday gift or display decoration\r\n\r\nExclusively designed by Play Nation Studio', 'Number of Pieces: 125 – 177 blocks per set\r\n\r\nFinished Dimensions: 13cm (L) × 9cm (W) × 15.5cm (H)\r\n\r\nRecommended Age: 6+ years\r\n\r\nMaterial: High-quality, durable plastic blocks\r\n\r\nPackaging: Individual set box (design sold separately)'),
('P060', 'Blokees Marvel Infinity Saga GV02 – Amazing Miracle', 'Unleash heroic power with Blokees Marvel Infinity Saga GV02 – Amazing Miracle! Build and pose your favorite Marvel hero with incredible detail, then relive epic movie moments. Each set also includes a collectible character card for fans and collectors alike.', 60.00, 49.90, 'SKU-60', 19, 'SC027', 'assets/images/products/prod_68b7761abc20a6.30932137.png', 'active', 0, '2025-09-02 14:56:26', '2025-09-11 22:33:40', '9 Marvel Infinity Saga characters to collect\r\n\r\nEasy-to-build, fully articulated action figure\r\n\r\nInspired by iconic Marvel movie moments\r\n\r\nPoseable joints for dynamic play and display\r\n\r\nIncludes collectible Marvel character card\r\n\r\nComes with a display base for stability', 'Recommended Age: 3+ years\r\n\r\nMaterial: Durable, child-safe plastic\r\n\r\nAssembly: Snap-fit, no tools required\r\n\r\nSet Includes: 1 base unit + 1 articulated figure + 1 collectible card\r\n\r\nPackaging: Individual box (character design varies per set)'),
('P061', 'Blokees Sesame Street Friends Amazing Level 01 – Elmo', 'Say hello to Blokees Sesame Street Friends Amazing Level 01 – Elmo! Build, pose, and play with this adorable articulated figure of everyone’s favorite red monster from Sesame Street. Perfect for fans young and old to enjoy.', 70.00, 39.90, 'SKU-61', 20, 'SC027', 'assets/images/products/prod_68b7769baf22f4.98624067.png', 'active', 0, '2025-09-02 14:58:35', '2025-09-02 14:58:35', 'Fun, buildable Elmo figure from Sesame Street Friends\r\n\r\nFully poseable for dynamic play and display\r\n\r\nEasy snap-fit assembly, no tools required\r\n\r\nEncourages creativity and imaginative storytelling\r\n\r\nCollectible figure—part of the Sesame Street Friends series', 'Recommended Age: 3+ years\r\n\r\nMaterial: Safe, durable plastic\r\n\r\nAssembly: Snap-fit construction\r\n\r\nSet Includes: 1 articulated Elmo figure\r\n\r\nPackaging: Individual box (Level 01 Elmo edition)'),
('P062', 'Baby Blush Lovely\'s Wardrobe Backpack Set', 'Take playtime anywhere with the Baby Blush Lovely\'s Wardrobe Backpack Set! This adorable baby doll comes with all the essentials little caregivers need to nurture, dress, and care for their baby. With a portable wardrobe backpack, extra clothes, feeding accessories, and bath items, kids can enjoy hours of imaginative role play at home or on the go.', 120.00, 90.00, 'SKU-62', 10, 'SC011', 'assets/images/products/prod_68c0f7bf7308f0.29889177.png', 'active', 0, '2025-09-08 08:30:12', '2025-09-09 19:59:59', 'Portable Playset: Includes a backpack that doubles as a wardrobe for easy storage and travel\r\n\r\nComplete Care Accessories: Feeding bottles, sippy cup, pacifier, toy blocks, and bath essentials\r\n\r\nDress-Up Fun: Comes with two outfits and hangers for changing looks\r\n\r\nImaginative Role Play: Encourages nurturing, caregiving, and creative storytelling\r\n\r\nPerfectly Sized: Baby doll designed for small hands, easy to carry and cuddle', 'Includes: 1 Baby doll, milk bottle, pacifier, sippy cup, lotion bottle, shampoo bottle, body wash bottle, handkerchief, 2 toy blocks, 2 outfits with hangers, backpack\r\n\r\nBackpack doubles as wardrobe and carrier\r\n\r\nRecommended Age: 3+ years'),
('P063', 'Disney Frozen Singing Doll Assortment', 'Sing along with your favorite Disney Frozen characters! The Disney Frozen Singing Dolls bring the magic of Arendelle to life, with Anna singing “For the First Time in Forever” and Elsa singing “Let It Go”. Each doll comes in her signature outfit, ready for musical adventures and imaginative play.', 150.00, 99.00, 'SKU-63', 30, 'SC026', 'assets/images/products/prod_68c1071dcaa636.15715933.png', 'active', 0, '2025-09-08 08:37:23', '2025-09-09 21:05:33', 'Musical Magic: Each doll sings her iconic Frozen song in English for up to 30 seconds\r\n\r\nAuthentic Design: Dressed in their signature movie-inspired outfits with removable soft skirt or cape\r\n\r\nPlay & Style: Dolls feature soft, brushable hair styled just like in the movies\r\n\r\nCollectible Fun: Choose Anna or Elsa—or collect both to complete the Frozen singing experience\r\n\r\nPerfect Gift: Great for Frozen fans to re-create scenes or create their own magical stories', 'Includes: 1 doll wearing removable fashion and accessories\r\n\r\nSongs: Anna (“For the First Time in Forever”) or Elsa (“Let It Go”)\r\n\r\nDolls cannot stand alone\r\n\r\nRecommended Age: 3+ years'),
('P064', 'Barbie Doll & Accessories Playset', 'It’s bath time for Barbie and her adorable puppies! The Barbie Doll & Accessories Playset comes with Barbie, three cute puppies, a real working bathtub, and fun grooming accessories. Perfect for animal lovers, kids can play out bath time, grooming, and adventures with Barbie and her furry friends.', 150.00, 80.00, 'SKU-64', 20, 'SC012', 'assets/images/products/prod_68c1075c30af30.79877373.png', 'active', 0, '2025-09-08 08:56:07', '2025-09-09 21:06:36', '3 Adorable Puppies: Barbie cares for three cute pets ready for bath and playtime\r\n\r\nFoaming Fun: Fill the tub with water and use the child-sized bottle with soap and water to create bubbles for the puppies\r\n\r\nInteractive Accessories: Includes a doll-sized soap bottle, 2 brushes, towel, and a pet carrier for on-the-go adventures\r\n\r\nRealistic Play: Barbie doll has bendable knees and can hold accessories to bring bath-time stories to life\r\n\r\nStylish Barbie: Wearing a pink patterned tank top, denim shorts, and white sneakers—perfect for a day with her pets\r\n\r\nGreat Gift Idea: Perfect for kids ages 3+ who love puppies, animals, and storytelling play', 'Includes: 1 Barbie doll, 3 puppies, bathtub, child-sized soap bottle, doll-sized soap bottle, 2 brushes, towel, pet carrier\r\n\r\nDoll cannot stand alone\r\n\r\nColors and decorations may vary\r\n\r\nRecommended Age: 3+ years'),
('P065', 'Monster High Lagoona Blue Spa Day', 'Dive into a fab-boo-lous spa day with Monster High Lagoona Blue! This doll and spa set includes over 20 styling accessories and wear-and-share beauty items, encouraging kids to play, style, and express their creativity alongside their favorite sea monster.', 200.00, 99.00, 'SKU-65', 19, 'SC012', 'assets/images/products/prod_68bf0c14a0da69.92565033.png', 'active', 0, '2025-09-08 09:02:12', '2025-09-08 09:02:12', '20+ Styling Accessories: Includes hair chalk, scaly tattoos, clips, ties, and beads for endless hair play.\r\n\r\nWear-and-Share Fun: Kids can join in with teal and purple hair chalk and shimmery body tattoos.\r\n\r\nSpa Day Fashion: Lagoona Blue comes dressed in a sporty-spooky outfit with earrings, sandals, a robe, and face mask.\r\n\r\nCountless Hairstyles: Use the comb, barrettes, hair ties, and beads to create unique undersea-inspired looks.\r\n\r\nEasy Storage: Keep all accessories organized in the seashell storage case.\r\n\r\nPerfect Gift: Great for kids ages 4+, encouraging creativity and storytelling.', 'Includes: 1 Lagoona Blue doll, robe, mask, comb, tattoo sheet, 2 hair chalks, shell storage case, 2 barrettes, 2 clips, 6 hair ties, 16 clip-on beads\r\n\r\nDoll is fully articulated\r\n\r\nDoll cannot stand alone\r\n\r\nColors and decorations may vary\r\n\r\nRecommended Age: 4+ years'),
('P066', 'Disney Princess Rapunzel & Flynn Rider Adventure Set – Assorted', 'Bring the magic of Disney’s Tangled to life with the Rapunzel & Flynn Rider Adventure Set! This playset includes Rapunzel with her iconic long golden hair, Flynn Rider in his signature outfit, Pascal the chameleon, and fun accessories for endless storytelling and styling play.', 200.00, 100.00, 'SKU-66', 19, 'SC026', 'assets/images/products/prod_68c1074b67b3d7.89307000.png', 'active', 0, '2025-09-08 09:05:49', '2025-09-09 21:06:19', 'Beloved Disney Characters: Poseable Rapunzel and Flynn Rider dolls inspired by Tangled.\r\n\r\nStyling Fun: Rapunzel comes with extra-long golden hair, a tiara, frying pan brush, and 7 hair accessories.\r\n\r\nAuthentic Outfits: Rapunzel wears her soft purple fabric dress with removable shoes, while Flynn Rider comes with his classic outfit, saddle belt pouch, and Wanted poster.\r\n\r\nPascal Figure: Includes a clip-on Pascal figure for added play and storytelling.\r\n\r\nEndless Adventures: Perfect for re-creating favorite Tangled movie moments or imagining new ones.\r\n\r\nGift-Ready: A magical set for Disney Princess fans and kids ages 3+.', 'Includes: 2 poseable dolls (Rapunzel & Flynn Rider), 1 Pascal figure, 10 styling accessories\r\n\r\nDolls cannot stand alone\r\n\r\nColors and decorations may vary\r\n\r\nRecommended Age: 3+ years'),
('P067', 'Top Tots Sit \'n Scoot Buggy', 'The Top Tots Sit \'n Scoot Buggy is a fun and interactive ride-on toy designed to spark imagination and build confidence. With lights, sounds, and easy-to-control steering, it helps little ones develop balance and motor skills while enjoying hours of play.', 100.00, 69.00, 'SKU-67', 20, 'SC020', 'assets/images/products/prod_68c0df311b1b29.04552592.png', 'active', 0, '2025-09-09 18:15:13', '2025-09-09 18:18:13', 'Interactive Play: Encourages imaginative scenarios with lights and fun sound effects.\r\n\r\nSkill Development: Builds confidence, balance, and coordination as kids ride independently.\r\n\r\nEasy Steering: Front wheels are designed for smooth turns and control.\r\n\r\nRide-On Fun: A perfect companion for active toddlers.', 'Includes: 1 Sit \'n Scoot Buggy\r\n\r\nFeatures: Lights & sound effects, steering front wheels\r\n\r\nRecommended Age: 3+ years'),
('P068', 'Globber Primo Foldable Lights Sky Blue Scooter', 'The Globber Primo Foldable Lights Scooter is an award-winning 3-wheel scooter for kids aged 3+. With its patented folding system, light-up wheels, and adjustable T-bar, it’s built for fun, safety, and years of use. Perfect for learning balance and coordination while enjoying glowing rides!', 500.00, 299.00, 'SKU-68', 11, 'SC020', 'assets/images/products/prod_68c0dfd09bec38.09290975.png', 'active', 0, '2025-09-09 18:17:52', '2025-09-09 18:17:52', 'Patented Folding System: One-push button fold for easy storage and transport in trolley mode.\r\n\r\nLight-Up Wheels: Battery-free LED wheels flash red, green, and blue as kids ride.\r\n\r\nAdjustable T-Bar: 3 height settings to grow with kids aged 3–6+.\r\n\r\nDurable Design: Wide, anti-slip deck with reinforced metal frame supports up to 50kg.\r\n\r\nSafe Learning: Steering lock button keeps wheels fixed for beginners learning balance.\r\n\r\nReliable Braking: Extra-large integrated rear brake for smooth and safe stops.', 'Includes: 1 T-bar, 1 deck, 1 tool bag, 1 instruction manual\r\n\r\nAge: 6+ years\r\n\r\nMax Weight: 50kg\r\n\r\nWheels: Battery-free LED light-up\r\n\r\nBrake: Wide integrated rear brake\r\n\r\nColor: Sky Blue'),
('P069', 'ReDo Stoked Popsicle Skateboard Red Serif 28 Inches', 'The ReDo Stoked Popsicle Skateboard (28\") is designed for riders who want both style and performance. With a classic popsicle shape, wide kick tails, and durable trucks, this complete skateboard is perfect for cruising or mastering flip tricks.', 150.00, 99.00, 'SKU-69', 19, 'SC020', 'assets/images/products/prod_68c0e090ee0712.34451464.png', 'active', 0, '2025-09-09 18:21:04', '2025-09-09 18:21:04', 'Classic Popsicle Shape: Wide kick tails provide extra pop and stability.\r\n\r\nDurable Trucks: 4.75-inch composite trucks deliver maximum control and balance.\r\n\r\nSmooth Performance: ABEC 3 ReDo bearings offer reliable acceleration and speed.\r\n\r\nMedium Concave Deck: Maintains comfort while supporting technical tricks.\r\n\r\nEye-Catching Graphics: High-definition underside design for standout style.', 'Length: 28 inches\r\n\r\nTrucks: 4.75-inch composite\r\n\r\nBearings: ABEC 3 ReDo\r\n\r\nDeck Shape: Medium concave, popsicle style\r\n\r\nIncludes: 1 skateboard (complete with 4 wheels) + 1 instruction sheet'),
('P070', 'Grow\'n Up Qwikfold Fun Slide (Purple)', 'The Grow\'n Up Qwikfold Fun Slide is the perfect first slide for toddlers, offering endless fun both indoors and outdoors. Designed with a 3.5-foot chute and a quick-fold feature, it’s easy to set up, play, and store away.', 200.00, 150.00, 'SKU-70', 11, 'SC020', 'assets/images/products/prod_68c0e11bafe0a8.29663932.png', 'active', 0, '2025-09-09 18:23:23', '2025-09-09 18:23:23', 'Compact & Portable: Easy fold design allows quick storage and transport.\r\n\r\nPerfect for Toddlers: Safe, sturdy, and fun for children aged 1.5 to 4 years.\r\n\r\nIndoor & Outdoor Play: Lightweight yet durable construction for versatile use.\r\n\r\n3.5-Foot Chute: Just the right size for little ones to slide with confidence.', 'Product Size: 107.95 cm (W) x 57.9 cm (D) x 66.04 cm (H)\r\n\r\nChute Length: 3.5 feet\r\n\r\nAge Range: 1.5 – 4 years'),
('P071', 'playpop Jumbo Shark Plush Toy', 'Dive into comfort with this Jumbo Shark Plush and Babies Set! Super soft, extra huggable, and jumbo in size, it’s perfect for cuddling, lounging, or imaginative play. Pair it with other plush toys to create an underwater adventure your child will love.', 150.00, 99.00, 'SKU-71', 11, 'SC025', 'assets/images/products/prod_68c0e24e09a7d7.28446910.png', 'active', 0, '2025-09-09 18:28:30', '2025-09-09 18:28:30', 'Super Soft Material – Crafted with premium plush fabric for ultimate comfort and snuggles.\r\n\r\nJumbo Size – Large plush shark that makes a perfect cuddle buddy or play companion.\r\n\r\nIncludes Baby Sharks – Comes with cute plush baby sharks for added play value.\r\n\r\nImaginative Play – Encourages creativity and storytelling, transporting kids into an underwater adventure.\r\n\r\nPerfect Gift – Great for birthdays, holidays, or as a comforting bedtime buddy.', 'Main Plush: Jumbo Shark with soft, huggable body\r\n\r\nExtras: Plush baby sharks included\r\n\r\nMaterial: High-quality, child-safe plush fabric & PP cotton filling\r\n\r\nAge Recommendation: Suitable for ages 3+\r\n\r\nCare Instructions: Surface wash only; air dry'),
('P072', 'Friends For Life Homey Ginger Cat Soft Toy 19cm', 'Bring home a cuddly new friend with the Friends For Life Homey Ginger Cat Soft Toy. Standing at 19cm tall, this adorable plush cat is more than just soft and huggable — it wiggles its tail, walks, and makes playful animal sounds for interactive fun. Perfect for kids who dream of having their own little pet, Homey the Ginger Cat encourages nurturing play, imagination, and endless cuddles.', 50.00, 39.00, 'SKU-72', 11, 'SC025', 'assets/images/products/prod_68c0e364c0eb44.69276294.png', 'active', 0, '2025-09-09 18:32:57', '2025-09-09 18:33:08', 'nteractive Play – Wiggles its tail and walks for lifelike fun.\r\n\r\nLifelike Sounds – Makes adorable animal sounds to entertain children.\r\n\r\nSoft & Huggable – Plush design that’s perfect for snuggles.\r\n\r\nEncourages Imagination – Inspires role-play and storytelling.\r\n\r\nCompact Size – Lightweight and portable, great for play at home or on the go.', 'Toy Type: Interactive Soft Toy\r\n\r\nCharacter: Homey Ginger Cat\r\n\r\nHeight: 19 cm\r\n\r\nFunctions: Walks, wiggles tail, makes sounds\r\n\r\nMaterial: Plush fabric with electronic components\r\n\r\nAge Recommendation: 3+ years\r\n\r\nPower: Battery-operated (type and number may vary)\r\n\r\nCare Instructions: Surface clean only'),
('P073', 'playpop Calico Balloon Cat Plush (15cm)', 'The playpop Calico Balloon Cat Plush (15cm) is a charming and cuddly companion designed in a delightful balloon shape. Made with soft, fluffy materials and detailed with embroidered accents, this plush toy is perfect for hugging, comforting, or displaying as a decorative piece. Its compact size makes it an adorable collectible for cat lovers of all ages.', 50.00, 39.00, 'SKU-73', 11, 'SC025', 'assets/images/products/prod_68c0e3dbcb18a0.90359361.png', 'active', 0, '2025-09-09 18:35:07', '2025-09-09 18:35:07', 'Soft & Fluffy – Crafted with plush fabric for maximum comfort.\r\n\r\nCute Balloon Shape – Unique design adds charm and style.\r\n\r\nEmbroidered Details – High-quality stitching for durability and cuteness.\r\n\r\nPerfect for Collectors – Ideal for fans of plush toys and cat lovers.\r\n\r\nCompact Size – Easy to carry, display, or gift.', 'Toy Type: Plush Soft Toy\r\n\r\nCharacter: Calico Balloon Cat\r\n\r\nHeight: 15 cm\r\n\r\nMaterial: Plush fabric with embroidery\r\n\r\nAvailable Colors: Multiple variations\r\n\r\nAge Recommendation: 3+ years\r\n\r\nCare Instructions: Surface clean only'),
('P074', 'The witch from Mercury Series HG 1/144 Beguir-Beu', 'Launched ahead of the latest Gundam series, Bandai introduces the newest HG Gundam from Mobile Suit Gundam: The Witch from Mercury! The Beguir-Beu is the fastest three-dimensional mobile suit to make its debut, bringing fans a sleek and dynamic design that’s perfect for display or battle poses. With precise detailing and high articulation, this model kit is a must-have addition for Gundam collectors and builders alike.', 100.00, 69.00, 'SKU-74', 11, 'SC029', 'assets/images/products/prod_68c0e543ae7710.81485343.jpg', 'active', 0, '2025-09-09 18:41:07', '2025-09-09 18:41:07', 'Latest Gundam Release – Officially from The Witch from Mercury series.\r\n\r\nDynamic Design – Sleek, high-speed mobile suit brought to life in 3D detail.\r\n\r\nExcellent Poseability – Articulated joints allow a wide range of action stances.\r\n\r\nAuthentic Detailing – Intricate parts capture the MS’s unique design.\r\n\r\nGreat for Collectors – Ideal for both display and as part of a Gundam collection.', 'Series: Mobile Suit Gundam: The Witch from Mercury\r\n\r\nGrade: High Grade (HG)\r\n\r\nModel: Beguir-Beu\r\n\r\nScale: 1/144\r\n\r\nIncluded Accessories:\r\n\r\nEquipment set x 1\r\n\r\nSeal sheet x 1\r\n\r\nMaterial: Plastic model kit (assembly required, no glue needed)\r\n\r\nRecommended Age: 15+\r\n\r\nRelease Notes: Initial release quantities may be limited; restocks will follow.'),
('P075', 'The Witch from Mercury HG 1/144 Gundam Calibarn', 'From Mobile Suit Gundam: The Witch from Mercury, Bandai presents the HG 1/144 Gundam Calibarn model kit! This powerful mobile suit joins the High Grade lineup with stunning color gradient effects on the chest and V-Fin for a striking finish. The kit also features a full range of expansion and deployment gimmicks in the broom and shield, making it an exciting build and an impressive display piece for any Gundam fan.', 80.00, 69.00, 'SKU-75', 11, 'SC029', 'assets/images/products/prod_68c0e65903e822.09753247.webp', 'active', 0, '2025-09-09 18:45:45', '2025-09-09 18:45:45', 'Witch from Mercury Mobile Suit – Official HG 1/144 Gundam Calibarn.\r\n\r\nUnique Color Gradients – Special effects on the chest and V-Fin for an eye-catching look.\r\n\r\nExpansion & Deployment Gimmicks – Broom and shield parts include detailed transforming functions.\r\n\r\nDynamic Poseability – Articulated design allows for a wide range of battle stances.\r\n\r\nComplete Arsenal – Includes a variety of weapons for versatile display options.', 'Series: Mobile Suit Gundam: The Witch from Mercury\r\n\r\nGrade: High Grade (HG)\r\n\r\nModel: Gundam Calibarn\r\n\r\nScale: 1/144\r\n\r\nIncluded Accessories:\r\n\r\nGundam Calibarn unit\r\n\r\nShield set\r\n\r\n2x Beam sabers\r\n\r\nBeam rifle\r\n\r\nMaterial: Plastic model kit (assembly required, no glue needed)\r\n\r\nRecommended Age: 15+'),
('P076', 'My Story Little Princess Perfect Blue Classic Dress', 'Let your child feel like royalty with the My Story Little Princess Perfect Blue Classic Dress. With lace trims, layered sleeves, and bow details, this gown captures the magic of fairytales while adding a timeless, historical touch. Made with durable, easy-to-clean fabric, it’s perfect for dress-up play, costume parties, or Halloween adventures. This enchanting gown inspires imaginative role play while supporting creativity, communication, and social skills.', 40.00, 29.90, 'SKU-76', 22, 'SC021', 'assets/images/products/prod_68c1029ba51654.83193156.png', 'active', 0, '2025-09-09 20:46:19', '2025-09-09 20:46:19', 'Elegant fairytale-inspired design with lace trims, layered sleeves, and bows.\r\n\r\nSparkling blue dress that transforms your little one into a princess.\r\n\r\nPerfect for role play, costume parties, Halloween, and special occasions.\r\n\r\nDurable and easy-to-clean fabric for worry-free play.\r\n\r\nEncourages imagination, creativity, and confidence.', 'Includes: 1x Blue Classic Dress\r\n\r\nDress Size (approx.): Length 75 cm, Width 33 cm\r\n\r\nTarget Height: 110–135 cm\r\n\r\nMaterial: Durable, wipe-clean fabric\r\n\r\nRecommended Age: 3 years and above\r\n\r\nAccessories not included'),
('P077', 'Blokees Transformers CC 15 One Bumblebee', 'Bring the action of Transformers One to life with the Blokees Transformers CC 15 One Bumblebee DIY Model Kit. Specially designed for fans and builders aged 12 and up, this kit lets you assemble Bumblebee into a fully articulated figure with 20 movable joints for striking battle-ready poses. Featuring a light-up eye function and a sturdy base, it’s the perfect collectible for both display and play.', 80.00, 69.90, 'SKU-77', 11, 'SC027', 'assets/images/products/prod_68c1048f0863d1.39819665.png', 'active', 0, '2025-09-09 20:54:39', '2025-09-09 20:54:39', 'DIY model kit of Bumblebee from Transformers One.\r\n\r\nIncludes 20 movable joints for dynamic posing.\r\n\r\nEye lights up with a single touch for added realism.\r\n\r\nEncourages hands-on building fun and creativity.\r\n\r\nPerfect for collectors, fans, and hobbyists alike.', 'Includes: 1x Base Unit, 1x Articulated Figure\r\n\r\nCharacter: Bumblebee\r\n\r\nAge Recommendation: 12+ years\r\n\r\nArticulation: 20 movable joints\r\n\r\nSpecial Feature: Light-up eyes (single-touch activation)\r\n\r\nCategory: DIY Model Kit / Collectible'),
('P078', 'Blokees Transformers CC 11 One Optimus Prime', 'Step into the epic world of Transformers One with the Blokees Transformers CC 11 Optimus Prime DIY Model Kit. Designed for fans and builders aged 12 and above, this kit allows you to construct the legendary Autobot leader with incredible detail and articulation. Featuring 20 movable joints for dynamic action poses, light-up eyes with a single touch, and effect parts for dramatic display, Optimus Prime is ready to command your collection.', 80.00, 69.90, 'SKU-78', 11, 'SC027', 'assets/images/products/prod_68c1060401b9c7.29315729.webp', 'active', 0, '2025-09-09 21:00:52', '2025-09-09 21:00:52', 'DIY model kit of Optimus Prime from Transformers One.\r\n\r\nFully articulated figure with 20 movable joints for maximum poseability.\r\n\r\nEye lights up with a single touch for enhanced realism.\r\n\r\nIncludes display stand and effect parts for cinematic action scenes.\r\n\r\nGreat for collectors, hobbyists, and Transformers fans alike.', 'Includes: 1x Transformer Figure, 2x Display Stand, 2x Effect Parts, 1x Instruction Manual\r\n\r\nCharacter: Optimus Prime\r\n\r\nAge Recommendation: 12+ years\r\n\r\nArticulation: 20 movable joints\r\n\r\nSpecial Feature: Single-touch light-up eyes\r\n\r\nCategory: DIY Model Kit / Collectible');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` varchar(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_path`, `created_at`) VALUES
(1, 'P001', 'assets/images/products/toothless_1.png', '2025-08-02 20:43:55'),
(2, 'P001', 'assets/images/products/toothless_2.png', '2025-08-02 20:43:55'),
(3, 'P001', 'assets/images/products/toothless_3.png', '2025-08-02 20:43:55'),
(4, 'P002', 'assets/images/products/jurassic_malcolm_1.png', '2025-08-02 20:43:55'),
(5, 'P002', 'assets/images/products/jurassic_malcolm_2.png', '2025-08-02 20:43:55'),
(6, 'P002', 'assets/images/products/jurassic_malcolm_3.png', '2025-08-02 20:43:55'),
(7, 'P003', 'assets/images/products/soldier_jet_1.png', '2025-08-02 20:43:55'),
(8, 'P003', 'assets/images/products/soldier_jet_2.png', '2025-08-02 20:43:55'),
(9, 'P003', 'assets/images/products/soldier_jet_3.png', '2025-08-02 20:43:55'),
(10, 'P004', 'assets/images/products/superwings_lucie_1.png', '2025-08-02 20:43:55'),
(11, 'P004', 'assets/images/products/superwings_lucie_2.png', '2025-08-02 20:43:55'),
(12, 'P005', 'assets/images/products/optimusprime_1.png', '2025-08-02 20:43:55'),
(13, 'P005', 'assets/images/products/optimusprime_2.png', '2025-08-02 20:43:55'),
(14, 'P005', 'assets/images/products/optimusprime_3.png', '2025-08-02 20:43:55'),
(15, 'P006', 'assets/images/products/red_death_dragon_1.png', '2025-08-02 20:43:55'),
(16, 'P006', 'assets/images/products/red_death_dragon_2.png', '2025-08-02 20:43:55'),
(17, 'P006', 'assets/images/products/red_death_dragon_3.png', '2025-08-02 20:43:55'),
(18, 'P007', 'assets/images/products/bumblebee_1.png', '2025-08-02 20:43:55'),
(19, 'P007', 'assets/images/products/bumblebee_2.png', '2025-08-02 20:43:55'),
(20, 'P007', 'assets/images/products/bumblebee_3.png', '2025-08-02 20:43:55'),
(21, 'P007', 'assets/images/products/bumblebee_4.png', '2025-08-02 20:43:55'),
(22, 'P007', 'assets/images/products/bumblebee_5.png', '2025-08-02 20:43:55'),
(23, 'P008', 'assets/images/products/ironman_mark85_1.png', '2025-08-02 20:43:55'),
(24, 'P008', 'assets/images/products/ironman_mark85_2.png', '2025-08-02 20:43:55'),
(25, 'P008', 'assets/images/products/ironman_mark85_3.png', '2025-08-02 20:43:55'),
(26, 'P008', 'assets/images/products/ironman_mark85_4.png', '2025-08-02 20:43:55'),
(27, 'P008', 'assets/images/products/ironman_mark85_5.png', '2025-08-02 20:43:55'),
(28, 'P009', 'assets/images/products/spiderman_titan_1.png', '2025-08-02 20:43:55'),
(29, 'P009', 'assets/images/products/spiderman_titan_2.png', '2025-08-02 20:43:55'),
(30, 'P009', 'assets/images/products/spiderman_titan_3.png', '2025-08-02 20:43:55'),
(31, 'P010', 'assets/images/products/batman_darkknight_1.png', '2025-08-02 20:43:55'),
(32, 'P010', 'assets/images/products/batman_darkknight_2.png', '2025-08-02 20:43:55'),
(33, 'P010', 'assets/images/products/batman_darkknight_3.png', '2025-08-02 20:43:55'),
(38, 'P011', 'assets/images/products/prod_68a44026127049.76839986.png', '2025-08-19 09:13:10'),
(39, 'P011', 'assets/images/products/prod_68a44026143050.49089338.png', '2025-08-19 09:13:10'),
(51, 'P012', 'assets/images/products/prod_68b74e2baef964.77001963.png', '2025-09-02 12:06:03'),
(52, 'P012', 'assets/images/products/prod_68b74e2baf4103.52135691.png', '2025-09-02 12:06:03'),
(53, 'P013', 'assets/images/products/prod_68b74ef33c5be9.07183399.png', '2025-09-02 12:09:23'),
(54, 'P013', 'assets/images/products/prod_68b74ef33c9d98.99052554.png', '2025-09-02 12:09:23'),
(55, 'P013', 'assets/images/products/prod_68b74ef33ce7e2.66998967.png', '2025-09-02 12:09:23'),
(56, 'P014', 'assets/images/products/prod_68b74fbb4ead05.15912916.png', '2025-09-02 12:12:43'),
(57, 'P014', 'assets/images/products/prod_68b74fbb4ee682.07702654.png', '2025-09-02 12:12:43'),
(58, 'P015', 'assets/images/products/prod_68b750b428e311.51539296.png', '2025-09-02 12:16:52'),
(59, 'P015', 'assets/images/products/prod_68b750b4291040.71973268.png', '2025-09-02 12:16:52'),
(60, 'P016', 'assets/images/products/prod_68b7513793e717.44998506.png', '2025-09-02 12:19:03'),
(61, 'P016', 'assets/images/products/prod_68b75137942635.22668564.png', '2025-09-02 12:19:03'),
(62, 'P016', 'assets/images/products/prod_68b751379493a5.63180470.png', '2025-09-02 12:19:03'),
(63, 'P017', 'assets/images/products/prod_68b75220957668.08266109.png', '2025-09-02 12:22:56'),
(64, 'P017', 'assets/images/products/prod_68b7522095c0d5.40251257.png', '2025-09-02 12:22:56'),
(65, 'P017', 'assets/images/products/prod_68b7522095f047.83098890.png', '2025-09-02 12:22:56'),
(66, 'P018', 'assets/images/products/prod_68b7531471d345.09386978.png', '2025-09-02 12:27:00'),
(67, 'P018', 'assets/images/products/prod_68b753147204c6.32548406.png', '2025-09-02 12:27:00'),
(68, 'P018', 'assets/images/products/prod_68b75314723125.40479873.png', '2025-09-02 12:27:00'),
(69, 'P019', 'assets/images/products/prod_68b753fbbc9632.10478515.png', '2025-09-02 12:30:51'),
(70, 'P020', 'assets/images/products/prod_68b75482742304.88093904.png', '2025-09-02 12:33:06'),
(71, 'P021', 'assets/images/products/prod_68b75550806208.47820209.png', '2025-09-02 12:36:32'),
(72, 'P022', 'assets/images/products/prod_68b7571b1a6c69.58461425.png', '2025-09-02 12:44:11'),
(73, 'P023', 'assets/images/products/prod_68b75857ae6624.51426613.png', '2025-09-02 12:49:27'),
(74, 'P023', 'assets/images/products/prod_68b75857aea469.80959435.png', '2025-09-02 12:49:27'),
(75, 'P024', 'assets/images/products/prod_68b75911d54436.36437418.png', '2025-09-02 12:52:33'),
(76, 'P024', 'assets/images/products/prod_68b75911d581b9.53506306.png', '2025-09-02 12:52:33'),
(77, 'P024', 'assets/images/products/prod_68b75911d5c950.72803661.png', '2025-09-02 12:52:33'),
(78, 'P025', 'assets/images/products/prod_68b7599e4ef7c2.67192817.png', '2025-09-02 12:54:54'),
(79, 'P025', 'assets/images/products/prod_68b7599e4f3aa7.64571046.png', '2025-09-02 12:54:54'),
(80, 'P026', 'assets/images/products/prod_68b75a40a5c481.92911042.png', '2025-09-02 12:57:36'),
(81, 'P026', 'assets/images/products/prod_68b75a40a61267.95250312.png', '2025-09-02 12:57:36'),
(82, 'P027', 'assets/images/products/prod_68b75b52cf6cf9.09504033.png', '2025-09-02 13:02:10'),
(83, 'P027', 'assets/images/products/prod_68b75b52cfb767.85393153.png', '2025-09-02 13:02:10'),
(84, 'P028', 'assets/images/products/prod_68b75bba15add1.40038968.png', '2025-09-02 13:03:54'),
(85, 'P028', 'assets/images/products/prod_68b75bba160a75.66684664.png', '2025-09-02 13:03:54'),
(86, 'P028', 'assets/images/products/prod_68b75bba165278.26197865.png', '2025-09-02 13:03:54'),
(87, 'P029', 'assets/images/products/prod_68b75c66de3ef4.25667360.png', '2025-09-02 13:06:46'),
(88, 'P029', 'assets/images/products/prod_68b75c66de8ca4.71315964.png', '2025-09-02 13:06:46'),
(89, 'P030', 'assets/images/products/prod_68b75cd4aec925.28326284.png', '2025-09-02 13:08:36'),
(90, 'P030', 'assets/images/products/prod_68b75cd4aeeb02.21094535.png', '2025-09-02 13:08:36'),
(91, 'P031', 'assets/images/products/prod_68b75d440413e0.83643613.png', '2025-09-02 13:10:28'),
(92, 'P031', 'assets/images/products/prod_68b75d44045e40.78314632.png', '2025-09-02 13:10:28'),
(93, 'P031', 'assets/images/products/prod_68b75d4404c319.62404369.png', '2025-09-02 13:10:28'),
(94, 'P031', 'assets/images/products/prod_68b75d440506c0.65980934.png', '2025-09-02 13:10:28'),
(95, 'P022', 'assets/images/products/prod_68b75d7d406433.73491111.png', '2025-09-02 13:11:25'),
(96, 'P032', 'assets/images/products/prod_68b7613ce9dcd9.07343746.png', '2025-09-02 13:27:24'),
(97, 'P032', 'assets/images/products/prod_68b7613cea36b7.05306409.png', '2025-09-02 13:27:24'),
(98, 'P032', 'assets/images/products/prod_68b7613cea79c9.16300281.png', '2025-09-02 13:27:24'),
(99, 'P033', 'assets/images/products/prod_68b7629ff401f8.54555762.png', '2025-09-02 13:33:20'),
(100, 'P033', 'assets/images/products/prod_68b762a00019b9.45370495.png', '2025-09-02 13:33:20'),
(101, 'P033', 'assets/images/products/prod_68b762a0005a43.29801917.png', '2025-09-02 13:33:20'),
(102, 'P034', 'assets/images/products/prod_68b763d2e0c757.46105684.png', '2025-09-02 13:38:26'),
(103, 'P034', 'assets/images/products/prod_68b763d2e0ef60.44153828.png', '2025-09-02 13:38:26'),
(104, 'P035', 'assets/images/products/prod_68b76472f1c4b7.56838765.png', '2025-09-02 13:41:06'),
(105, 'P035', 'assets/images/products/prod_68b76472f20248.62086653.png', '2025-09-02 13:41:06'),
(106, 'P036', 'assets/images/products/prod_68b7658e990e52.87880235.png', '2025-09-02 13:45:50'),
(107, 'P036', 'assets/images/products/prod_68b7658e9953b8.89510941.png', '2025-09-02 13:45:50'),
(108, 'P036', 'assets/images/products/prod_68b7658e99a8b1.47710878.png', '2025-09-02 13:45:50'),
(109, 'P037', 'assets/images/products/prod_68b766d593aab9.87331170.png', '2025-09-02 13:51:17'),
(110, 'P037', 'assets/images/products/prod_68b766d593c860.95785821.png', '2025-09-02 13:51:17'),
(111, 'P037', 'assets/images/products/prod_68b766d593e687.74305731.png', '2025-09-02 13:51:17'),
(112, 'P038', 'assets/images/products/prod_68b7678cbd4431.68624114.png', '2025-09-02 13:54:20'),
(113, 'P038', 'assets/images/products/prod_68b7678cbd6780.47383253.png', '2025-09-02 13:54:20'),
(114, 'P039', 'assets/images/products/prod_68b7686a18ab03.25704331.png', '2025-09-02 13:58:02'),
(115, 'P039', 'assets/images/products/prod_68b7686a18e7d0.33143045.png', '2025-09-02 13:58:02'),
(116, 'P039', 'assets/images/products/prod_68b7686a191c26.11654672.png', '2025-09-02 13:58:02'),
(117, 'P040', 'assets/images/products/prod_68b769515049b9.34852114.png', '2025-09-02 14:01:53'),
(118, 'P040', 'assets/images/products/prod_68b76951509036.75462559.png', '2025-09-02 14:01:53'),
(119, 'P040', 'assets/images/products/prod_68b7695150ea21.43969415.png', '2025-09-02 14:01:53'),
(120, 'P041', 'assets/images/products/prod_68b769c598f942.91221168.png', '2025-09-02 14:03:49'),
(121, 'P041', 'assets/images/products/prod_68b769c5995b50.67540841.png', '2025-09-02 14:03:49'),
(122, 'P042', 'assets/images/products/prod_68b76a6c923ca8.75796914.png', '2025-09-02 14:06:36'),
(123, 'P042', 'assets/images/products/prod_68b76a6c926d03.73512019.png', '2025-09-02 14:06:36'),
(124, 'P042', 'assets/images/products/prod_68b76a6c92a0d2.50641611.png', '2025-09-02 14:06:36'),
(125, 'P043', 'assets/images/products/prod_68b76b4f93e331.07469680.png', '2025-09-02 14:10:23'),
(126, 'P043', 'assets/images/products/prod_68b76b4f941be4.10052799.png', '2025-09-02 14:10:23'),
(127, 'P043', 'assets/images/products/prod_68b76b4f9467a7.66161364.png', '2025-09-02 14:10:23'),
(128, 'P044', 'assets/images/products/prod_68b76be8a19869.61886766.png', '2025-09-02 14:12:56'),
(129, 'P044', 'assets/images/products/prod_68b76be8a1c1a6.89856144.png', '2025-09-02 14:12:56'),
(130, 'P044', 'assets/images/products/prod_68b76be8a1ece5.41934413.png', '2025-09-02 14:12:56'),
(131, 'P045', 'assets/images/products/prod_68b76caa22eec7.00530747.png', '2025-09-02 14:16:10'),
(132, 'P045', 'assets/images/products/prod_68b76caa232fe5.33629168.png', '2025-09-02 14:16:10'),
(133, 'P046', 'assets/images/products/prod_68b76d63365243.27125455.png', '2025-09-02 14:19:15'),
(134, 'P046', 'assets/images/products/prod_68b76d6336d8e1.74283852.png', '2025-09-02 14:19:15'),
(135, 'P047', 'assets/images/products/prod_68b76de0d44b98.37023329.png', '2025-09-02 14:21:20'),
(136, 'P047', 'assets/images/products/prod_68b76de0d483e8.99893270.png', '2025-09-02 14:21:20'),
(137, 'P048', 'assets/images/products/prod_68b76e9c3616d0.46922735.png', '2025-09-02 14:24:28'),
(138, 'P048', 'assets/images/products/prod_68b76e9c364948.09106110.png', '2025-09-02 14:24:28'),
(139, 'P048', 'assets/images/products/prod_68b76e9c368d13.81884413.png', '2025-09-02 14:24:28'),
(140, 'P049', 'assets/images/products/prod_68b76f1f428aa8.07366681.png', '2025-09-02 14:26:39'),
(141, 'P049', 'assets/images/products/prod_68b76f1f45ab17.66338197.png', '2025-09-02 14:26:39'),
(142, 'P049', 'assets/images/products/prod_68b76f1f45cdd8.20612593.png', '2025-09-02 14:26:39'),
(143, 'P050', 'assets/images/products/prod_68b770a17eeff6.65446410.png', '2025-09-02 14:33:05'),
(144, 'P051', 'assets/images/products/prod_68b7715f335a96.41124481.png', '2025-09-02 14:36:15'),
(145, 'P051', 'assets/images/products/prod_68b7715f3375a5.02790592.png', '2025-09-02 14:36:15'),
(146, 'P052', 'assets/images/products/prod_68b772dcf03f44.59491662.png', '2025-09-02 14:42:36'),
(147, 'P052', 'assets/images/products/prod_68b772dcf08c54.92441489.png', '2025-09-02 14:42:36'),
(148, 'P053', 'assets/images/products/prod_68b7737d9116e5.94296354.png', '2025-09-02 14:45:17'),
(149, 'P053', 'assets/images/products/prod_68b7737d916a44.91689607.png', '2025-09-02 14:45:17'),
(150, 'P054', 'assets/images/products/prod_68b773f4c789f1.77667524.png', '2025-09-02 14:47:16'),
(151, 'P054', 'assets/images/products/prod_68b773f4c7bf69.86819013.png', '2025-09-02 14:47:16'),
(152, 'P055', 'assets/images/products/prod_68b774601c9672.83617414.png', '2025-09-02 14:49:04'),
(153, 'P055', 'assets/images/products/prod_68b774601cceb7.48031791.png', '2025-09-02 14:49:04'),
(154, 'P056', 'assets/images/products/prod_68b774de6b5c20.73901975.png', '2025-09-02 14:51:10'),
(155, 'P056', 'assets/images/products/prod_68b774de6ba796.68537405.png', '2025-09-02 14:51:10'),
(156, 'P057', 'assets/images/products/prod_68b775541687a4.83495040.png', '2025-09-02 14:53:08'),
(157, 'P057', 'assets/images/products/prod_68b7755416c5e3.32593477.png', '2025-09-02 14:53:08'),
(158, 'P057', 'assets/images/products/prod_68b77554170bf5.20903241.png', '2025-09-02 14:53:08'),
(159, 'P058', 'assets/images/products/prod_68b77599072d03.56081417.png', '2025-09-02 14:54:17'),
(160, 'P058', 'assets/images/products/prod_68b77599074701.11039184.png', '2025-09-02 14:54:17'),
(161, 'P059', 'assets/images/products/prod_68b775d3092fc6.60197595.png', '2025-09-02 14:55:15'),
(162, 'P059', 'assets/images/products/prod_68b775d309ba36.79065291.png', '2025-09-02 14:55:15'),
(163, 'P059', 'assets/images/products/prod_68b775d30a2613.51464005.png', '2025-09-02 14:55:15'),
(164, 'P060', 'assets/images/products/prod_68b7761abca678.73238310.png', '2025-09-02 14:56:26'),
(165, 'P060', 'assets/images/products/prod_68b7761abd1ee8.39154937.png', '2025-09-02 14:56:26'),
(166, 'P061', 'assets/images/products/prod_68b7769baf6fa1.68898256.png', '2025-09-02 14:58:35'),
(167, 'P061', 'assets/images/products/prod_68b7769bafe3b8.09219614.png', '2025-09-02 14:58:35'),
(168, 'P061', 'assets/images/products/prod_68b7769bb02983.96168426.png', '2025-09-02 14:58:35'),
(170, 'P062', 'assets/images/products/prod_68bf04946aacb4.37264867.png', '2025-09-08 08:30:12'),
(171, 'P062', 'assets/images/products/prod_68bf04946af0a6.53816155.png', '2025-09-08 08:30:12'),
(173, 'P063', 'assets/images/products/prod_68bf0643692779.53907472.png', '2025-09-08 08:37:23'),
(174, 'P064', 'assets/images/products/prod_68bf0aa7a3e8f3.30224066.png', '2025-09-08 08:56:07'),
(176, 'P065', 'assets/images/products/prod_68bf0c14a12431.59179435.png', '2025-09-08 09:02:12'),
(177, 'P065', 'assets/images/products/prod_68bf0c14a17bb3.54542954.png', '2025-09-08 09:02:12'),
(178, 'P066', 'assets/images/products/prod_68bf0ced623160.17038106.png', '2025-09-08 09:05:49'),
(180, 'P066', 'assets/images/products/prod_68bf0ced628ce3.37269388.png', '2025-09-08 09:05:49'),
(181, 'P067', 'assets/images/products/prod_68c0df311b6e90.17973321.png', '2025-09-09 18:15:13'),
(182, 'P067', 'assets/images/products/prod_68c0df311bcac2.43937151.png', '2025-09-09 18:15:13'),
(183, 'P068', 'assets/images/products/prod_68c0dfd09c1417.07319670.png', '2025-09-09 18:17:52'),
(184, 'P068', 'assets/images/products/prod_68c0dfd09c3352.11444017.png', '2025-09-09 18:17:52'),
(185, 'P068', 'assets/images/products/prod_68c0dfd09c4f23.20948215.png', '2025-09-09 18:17:52'),
(186, 'P069', 'assets/images/products/prod_68c0e090ee5c84.50353276.png', '2025-09-09 18:21:04'),
(187, 'P069', 'assets/images/products/prod_68c0e090eed994.10776790.png', '2025-09-09 18:21:04'),
(188, 'P070', 'assets/images/products/prod_68c0e11bb01b07.78395208.png', '2025-09-09 18:23:23'),
(189, 'P070', 'assets/images/products/prod_68c0e11bb04db6.56192566.png', '2025-09-09 18:23:23'),
(190, 'P071', 'assets/images/products/prod_68c0e24e09cbe1.85836996.png', '2025-09-09 18:28:30'),
(191, 'P071', 'assets/images/products/prod_68c0e24e09edd2.16287244.png', '2025-09-09 18:28:30'),
(192, 'P071', 'assets/images/products/prod_68c0e24e0a0de5.13438435.png', '2025-09-09 18:28:30'),
(193, 'P071', 'assets/images/products/prod_68c0e24e0a27f2.99932615.png', '2025-09-09 18:28:30'),
(194, 'P072', 'assets/images/products/prod_68c0e35988db38.60105007.png', '2025-09-09 18:32:57'),
(195, 'P072', 'assets/images/products/prod_68c0e359893112.41147459.png', '2025-09-09 18:32:57'),
(196, 'P072', 'assets/images/products/prod_68c0e3598972f9.77351714.png', '2025-09-09 18:32:57'),
(197, 'P073', 'assets/images/products/prod_68c0e3dbcb86c4.47406355.png', '2025-09-09 18:35:07'),
(198, 'P073', 'assets/images/products/prod_68c0e3dbcbe229.78825570.png', '2025-09-09 18:35:07'),
(199, 'P074', 'assets/images/products/prod_68c0e543aea0c5.35672608.jpg', '2025-09-09 18:41:07'),
(200, 'P074', 'assets/images/products/prod_68c0e543aebe53.66504418.jpg', '2025-09-09 18:41:07'),
(201, 'P074', 'assets/images/products/prod_68c0e543aed788.77756325.jpg', '2025-09-09 18:41:07'),
(202, 'P074', 'assets/images/products/prod_68c0e543aefc45.04947259.jpg', '2025-09-09 18:41:07'),
(203, 'P075', 'assets/images/products/prod_68c0e659040a98.22281126.jpg', '2025-09-09 18:45:45'),
(204, 'P075', 'assets/images/products/prod_68c0e659042911.93375270.webp', '2025-09-09 18:45:45'),
(205, 'P075', 'assets/images/products/prod_68c0e659044030.42747889.webp', '2025-09-09 18:45:45'),
(206, 'P013', 'assets/images/products/prod_68c0f4ad39d8d9.88789432.png', '2025-09-09 19:46:53'),
(208, 'P014', 'assets/images/products/prod_68c0f5023b42b2.67770064.png', '2025-09-09 19:48:18'),
(209, 'P014', 'assets/images/products/prod_68c0f5023b8942.17572118.png', '2025-09-09 19:48:18'),
(210, 'P015', 'assets/images/products/prod_68c0f52f384269.97419565.png', '2025-09-09 19:49:03'),
(211, 'P016', 'assets/images/products/prod_68c0f5569982a7.41878567.png', '2025-09-09 19:49:42'),
(212, 'P017', 'assets/images/products/prod_68c0f578ae5404.78951309.png', '2025-09-09 19:50:16'),
(213, 'P018', 'assets/images/products/prod_68c0f5b604a941.48517327.png', '2025-09-09 19:51:18'),
(214, 'P019', 'assets/images/products/prod_68c0f60ac2f552.13553328.png', '2025-09-09 19:52:42'),
(215, 'P021', 'assets/images/products/prod_68c0f64d1c64a4.28703289.png', '2025-09-09 19:53:49'),
(216, 'P057', 'assets/images/products/prod_68c0f6957fcfd3.80686804.png', '2025-09-09 19:55:01'),
(217, 'P058', 'assets/images/products/prod_68c0f6af74d975.95298094.png', '2025-09-09 19:55:27'),
(218, 'P059', 'assets/images/products/prod_68c0f6c8798302.29063490.png', '2025-09-09 19:55:52'),
(219, 'P060', 'assets/images/products/prod_68c0f70e25cdd8.04960045.png', '2025-09-09 19:57:02'),
(220, 'P061', 'assets/images/products/prod_68c0f73d71af42.39926838.png', '2025-09-09 19:57:49'),
(221, 'P062', 'assets/images/products/prod_68c0f7bf740f04.25625143.png', '2025-09-09 19:59:59'),
(222, 'P037', 'assets/images/products/prod_68c0ffc09625c8.06287223.png', '2025-09-09 20:34:08'),
(223, 'P047', 'assets/images/products/prod_68c10018e58854.57806463.png', '2025-09-09 20:35:36'),
(224, 'P048', 'assets/images/products/prod_68c1003ddd9898.15816375.png', '2025-09-09 20:36:13'),
(225, 'P076', 'assets/images/products/prod_68c1029ba56592.33546634.png', '2025-09-09 20:46:19'),
(226, 'P076', 'assets/images/products/prod_68c1029ba5a3d6.46101031.png', '2025-09-09 20:46:19'),
(227, 'P076', 'assets/images/products/prod_68c1029ba5ef07.84686523.png', '2025-09-09 20:46:19'),
(228, 'P043', 'assets/images/products/prod_68c102f344dc48.86201603.png', '2025-09-09 20:47:47'),
(230, 'P077', 'assets/images/products/prod_68c1048f088f47.87898302.png', '2025-09-09 20:54:39'),
(231, 'P077', 'assets/images/products/prod_68c1048f08aff6.02498729.png', '2025-09-09 20:54:39'),
(232, 'P077', 'assets/images/products/prod_68c1048f0c0af0.70995127.png', '2025-09-09 20:54:39'),
(233, 'P078', 'assets/images/products/prod_68c1060401e179.86557933.png', '2025-09-09 21:00:52'),
(234, 'P078', 'assets/images/products/prod_68c106040210d9.34797624.png', '2025-09-09 21:00:52'),
(235, 'P038', 'assets/images/products/prod_68c107e64f6b97.58642755.png', '2025-09-09 21:08:54');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` varchar(11) NOT NULL,
  `order_id` varchar(11) NOT NULL,
  `payment_id` varchar(11) DEFAULT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_method` varchar(50) DEFAULT NULL,
  `refund_status` enum('requested','approved','completed','failed') DEFAULT 'requested',
  `processed_by` varchar(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `refund_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`refund_id`, `order_id`, `payment_id`, `refund_amount`, `refund_method`, `refund_status`, `processed_by`, `notes`, `refund_date`, `created_at`, `updated_at`) VALUES
('RF000008', 'ORD0000031', 'PAY0000031', 75.50, 'original', 'completed', 'USR28091321', '', '2025-09-11 20:46:23', '2025-09-11 20:46:23', '2025-09-11 20:50:27'),
('RF000010', 'ORD0000033', 'PAY0000033', 101.75, 'original', 'completed', 'USR28091321', '', '2025-09-11 21:02:34', '2025-09-11 21:02:34', '2025-09-11 21:02:45');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` varchar(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `product_id` varchar(11) NOT NULL,
  `order_id` varchar(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `title` varchar(200) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `product_id`, `order_id`, `rating`, `title`, `comment`, `status`, `created_at`, `updated_at`) VALUES
('UR000000001', 'USR41317469', 'P043', 'ORD0000012', 4, 'Very fun toy!!', 'I loved it so much!', 'approved', '2025-09-10 02:39:54', '2025-09-10 03:07:58'),
('UR000000002', 'USR96518622', 'P041', 'ORD0000015', 5, 'Beautiful Gift', 'Got the LEGO Flowers set as a gift for my girlfriend and she absolutely loved it! The build was fun, and the final arrangement looks beautiful on display. ', 'approved', '2025-09-10 03:16:30', '2025-09-10 03:18:31'),
('UR000000003', 'USR96518622', 'P059', 'ORD0000015', 4, 'Adorable Collectible', 'Absolutely adorable My Melody blocks! The details are perfect, and it looks super cute.', 'approved', '2025-09-10 16:09:43', '2025-09-10 16:21:52'),
('UR000000005', 'USR41317469', 'P032', 'ORD0000028', 4, 'Great Starter Set', 'It comes with fun accessories and characters that spark creativity and imaginative play. ', 'approved', '2025-09-11 21:42:55', '2025-09-11 21:43:06');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` varchar(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','member') NOT NULL DEFAULT 'member',
  `status` enum('active','blocked') NOT NULL DEFAULT 'active',
  `profile_pic` varchar(255) DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `role`, `status`, `profile_pic`, `remember_token`, `created_at`, `updated_at`) VALUES
('USR28091321', 'admin1', 'atoyland97@gmail.com', '$2y$10$vkOVZgqik7TtFvwsjzZXiuQcOiNEDVhwdelAJMRarOpRV0fV1KLEm', 'admin', 'active', NULL, NULL, '2025-09-11 17:21:09', '2025-09-11 17:23:30'),
('USR29610301', 'jim_chan', 'jimchan1234@gmail.com', '$2y$10$CR.MqRTNhvJd8oprtJ02de4X877qVhekZzFxHQ9eYpqh7xktOqJeC', 'member', 'active', NULL, NULL, '2025-09-04 11:11:30', '2025-09-04 11:11:30'),
('USR41317469', 'kim_lee', 'step724mak@gmail.com', '$2y$10$v6rwCjKSByjo48EEnJ2r1epxNRQRP0bVJFWTQxDzOqyfOMlY8jU/y', 'member', 'active', '/assets/images/profile_pictures/68be63453e2ae_9cf226eb5a62f610c8f2da25be69266d.jpg', NULL, '2025-09-04 21:21:37', '2025-09-08 05:01:57'),
('USR42881374', 'stephanie_mak', 'stepmak724@gmail.com', '$2y$10$GU5yw5CGrZn4DADt6tPmGu1s3voxnLApUUaJv6z9nBkYE6IWNbLaG', 'member', 'active', '/assets/images/profile_pictures/user_68c1f27a4a9962.11160122.jpg', NULL, '2025-09-10 21:49:46', '2025-09-10 21:52:59'),
('USR46904770', 'ame_nori', 'ame0908@gmail.com', '$2y$10$vWsD8pmndCvSdvd6gx/5TuQazXg8RUsd142Vq.c/jpEFFZ80wBSEe', 'member', 'active', '/assets/images/profile_pictures/user_68c307692ed4c9.06070484.jpg', NULL, '2025-09-11 17:31:21', '2025-09-11 17:31:21'),
('USR79561298', 'john_lee', 'john123@gmail.com', '$2y$10$0C0XESVrB0KhW5mN55FRve9PnYeFiqXWmvY8jGZJPqKe4sPtuARpO', 'member', 'active', NULL, NULL, '2025-08-30 18:01:08', '2025-08-30 18:01:08'),
('USR94995839', 'salman_moosa', 'salmaanmoosa498@gmail.com', '$2y$10$mDAihTtzdNvX1kRt8kv7cOQ0SLmpEIrKg2ylRBlvnl7b2VrgiF7/6', 'member', 'active', NULL, NULL, '2025-08-02 16:06:19', '2025-08-02 16:06:19'),
('USR96518622', 'ren_hj', 'mike123@gmail.com', '$2y$10$Le7d636IEpT5v9U//QgmhOory8jPhdEh8BvN7.x1t0bexfKb2o.Z6', 'member', 'active', '/assets/images/profile_pictures/68bdd830552e9_0d93bd198f0ca87a6719a628a9594a69.jpg', NULL, '2025-09-07 18:55:06', '2025-09-10 03:17:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `user_id` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `newsletter_subscription` tinyint(1) DEFAULT 0,
  `marketing_emails` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`user_id`, `first_name`, `last_name`, `phone`, `date_of_birth`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`, `address`, `newsletter_subscription`, `marketing_emails`, `created_at`, `updated_at`) VALUES
('USR41317469', 'Kim', 'Lee', '011548879999', '1992-08-25', '215, Jalan 234, Taman Bunga Raya', '', 'Setapak', 'Kuala Lumpur', '51200', '', 1, 1, '2025-09-04 21:21:37', '2025-09-05 16:09:30'),
('USR42881374', 'Stephanie', 'Mak', '01154887998', '2000-10-01', '21, Jalan 23, Taman Bunga Raya', '215, Jalan 234, Taman Bunga Raya', 'Setapak', 'Kuala Lumpur', '51200', NULL, 0, 1, '2025-09-10 21:49:46', '2025-09-10 21:49:46'),
('USR46904770', 'Ame', 'Nori', '011544512544', '2000-11-05', '23, Jalan 4, Taman HRW', '', 'Setapak', 'Kuala Lumpur', '51200', NULL, 0, 0, '2025-09-11 17:31:21', '2025-09-11 17:31:21'),
('USR94995839', 'Salman', 'Moosa', '0185891720', '2004-04-03', '', NULL, '', '', '', 'J004, Jalan Malinja, Taman Bunga Raya, Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur 53000, US', 0, 0, '2025-08-02 16:06:19', '2025-08-02 16:06:19'),
('USR96518622', 'Ren', 'Honjo', '0112154548', '2000-10-05', '12, Jalan NN, Taman G', '', 'Setapak', 'Kuala Lumpur', '51200', '', 1, 1, '2025-09-07 18:55:06', '2025-09-10 03:17:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_vouchers`
--

CREATE TABLE `user_vouchers` (
  `user_voucher_id` varchar(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `voucher_id` varchar(11) NOT NULL,
  `collected_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_vouchers`
--

INSERT INTO `user_vouchers` (`user_voucher_id`, `user_id`, `voucher_id`, `collected_at`, `used_at`) VALUES
('UV000000001', 'USR41317469', 'V000000001', '2025-09-07 18:32:31', '2025-09-10 21:43:48'),
('UV000000002', 'USR96518622', 'VCH001', '2025-09-07 18:55:06', '2025-09-07 20:30:46'),
('UV000000003', 'USR96518622', 'VCH003', '2025-09-07 19:10:15', '2025-09-07 20:26:19'),
('UV000000004', 'USR41317469', 'VCH001', '2025-09-10 07:09:17', NULL),
('UV000000005', 'USR96518622', 'V000000001', '2025-09-10 07:24:59', '2025-09-10 07:26:51'),
('UV000000006', 'USR42881374', 'VCH001', '2025-09-10 21:49:46', '2025-09-10 22:16:43'),
('UV000000007', 'USR46904770', 'VCH001', '2025-09-11 17:31:21', '2025-09-11 18:31:11'),
('UV000000008', 'USR46904770', 'VCH003', '2025-09-11 18:35:00', '2025-09-11 18:36:00');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `voucher_id` varchar(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `usage_limit` int(11) DEFAULT 0,
  `per_user_limit` int(11) DEFAULT 1,
  `status` enum('scheduled','active','inactive','expired') NOT NULL DEFAULT 'scheduled',
  `created_by` varchar(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`voucher_id`, `code`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `start_date`, `end_date`, `usage_limit`, `per_user_limit`, `status`, `created_by`, `created_at`) VALUES
('V000000001', 'MALAYSIADAY16', 'Malaysia day special discount for minimum spend RM16 and above.', 'percentage', 16.00, 16.00, '2025-09-07 00:23:00', '2025-09-27 23:59:00', 398, 1, 'active', NULL, '2025-09-06 22:26:11'),
('VCH001', 'NEWUSER', 'Welcome voucher for new members - 10% off your first order', 'percentage', 10.00, 0.00, '2025-09-07 03:47:18', '2026-09-07 03:47:18', 0, 1, 'active', NULL, '2025-09-06 19:47:18'),
('VCH002', 'SALE1111', '11.11 Mega Sale - 11% off (max RM50 discount)', 'percentage', 11.00, 50.00, '2025-11-01 00:00:00', '2025-11-11 23:59:59', 5000, 1, 'active', NULL, '2025-09-06 19:50:45'),
('VCH003', 'WEEKEND20', 'Weekend Flash Sale - RM20 off with minimum spend RM100', 'fixed', 20.00, 100.00, '2025-09-07 05:11:46', '2025-09-27 05:11:33', 498, 1, 'active', NULL, '2025-09-06 19:50:45');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` varchar(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `product_id` varchar(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `product_id`, `created_at`) VALUES
('WL000000001', 'USR79561298', 'P003', '2025-08-31 18:03:04'),
('WL000000002', 'USR41317469', 'P032', '2025-09-10 07:04:49'),
('WL000000003', 'USR41317469', 'P035', '2025-09-10 07:04:51'),
('WL000000005', 'USR96518622', 'P032', '2025-09-10 07:34:38'),
('WL000000006', 'USR41317469', 'P005', '2025-09-10 19:46:40'),
('WL000000007', 'USR41317469', 'P057', '2025-09-10 19:48:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`order_status`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `fk_orders_voucher` (`voucher_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `order_items_fk_2` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_sku` (`sku`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_featured` (`featured`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_images_fk_1` (`product_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `idx_processed_by` (`processed_by`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_reviews_orders` (`order_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD PRIMARY KEY (`user_voucher_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`voucher_id`),
  ADD KEY `fk_user_vouchers_voucher` (`voucher_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_vouchers_created_by` (`created_by`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_fk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_fk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`voucher_id`),
  ADD CONSTRAINT `orders_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_fk_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_fk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_fk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_fk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_fk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_fk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_fk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_fk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_fk_2` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `refunds_fk_3` FOREIGN KEY (`processed_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_fk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD CONSTRAINT `fk_user_vouchers_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_vouchers_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`voucher_id`) ON DELETE CASCADE;

--
-- Constraints for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `fk_vouchers_created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_fk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
