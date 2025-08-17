-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2025 at 08:22 PM
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
('C001', 'Action figures', 'Collectible figures of superheroes, movie characters, and more.', 'assets/images/action_figures.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-05 17:44:11'),
('C002', 'Building blocks & LEGO', 'Construction toys that develop creativity and problem-solving skills.', 'assets/images/building_blocks_lego.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-05 17:44:56'),
('C003', 'Cars, trucks, trains', 'Vehicles for racing, transporting, and imaginative play.', 'assets/images/cars_trucks_trains.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-05 17:45:23'),
('C004', 'Dolls', 'Classic and modern dolls for imaginative storytelling and nurturing play.', 'assets/images/dolls.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-05 17:46:05'),
('C005', 'Games & puzzles', 'Board games, card games, and puzzles for all ages.', 'assets/images/games_puzzles.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-05 17:46:31'),
('C006', 'Outdoor & sports', 'Toys for active play outside, including balls, bikes, and water games.', 'assets/images/outdoor_sports.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-05 17:46:54'),
('C007', 'Pretend Play & costumes', 'Dress-up clothes and playsets for creative role-play.', 'assets/images/pretend_play_costume.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-05 17:47:28'),
('C008', 'Blind box', 'Mystery toys that offer a surprise with every unboxing.', 'assets/images/blind_box.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-05 17:48:08'),
('C009', 'Soft toys', 'Plush animals and cuddly characters for comfort and play.', 'assets/images/soft_toys.png', NULL, 'active', '2025-08-02 20:31:42', '2025-08-05 17:48:54'),
('SC001', 'Action Figures', 'Poseable figures from various media including superheroes, movies, and cartoons.', 'assets/images/action_figures1.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-05 18:22:07'),
('SC002', 'Animals & Creatures', 'Animal-themed and mythical creature figures for imaginative play and collection.', 'assets/images/animals.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-05 18:22:07'),
('SC003', 'Dinosaurs', 'Dinosaur action figures and playsets that teach and entertain.', 'assets/images/dinosaur.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-05 18:22:07'),
('SC004', 'Military & Fantasy Toys', 'Figures and accessories themed around military, knights, and fantasy adventures.', 'assets/images/military.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-05 18:22:07'),
('SC005', 'Models', 'Buildable models of vehicles, robots, and other complex figures.', 'assets/images/models.png', 'C001', 'active', '2025-08-05 18:22:07', '2025-08-05 18:22:07'),
('SC006', 'LEGO', 'Classic interlocking brick sets for creative construction and learning.', 'assets/images/lego.png', 'C002', 'active', '2025-08-05 18:22:07', '2025-08-05 18:22:07'),
('SC007', 'MEGA BLOCKS', 'Larger block toys great for younger builders with themed sets.', 'assets/images/mega_blocks.png', 'C002', 'active', '2025-08-05 18:22:07', '2025-08-05 18:22:07');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` varchar(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `billing_address` text NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
('P001', 'How to Train Your Dragon 12-Inch Toothless', 'Bring home the magic of How to Train Your Dragon with the 12-Inch Toothless Figure! This large-scale, highly detailed version of the beloved Night Fury is perfect for both play and display.', 79.00, 59.25, 'SKU-0001', 30, 'C001', 'assets/images/toothless_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:47:18', '-Authentic Design. Detailed sculpting and vibrant colors bring Toothless to life just like in the movies.\r\n-Poseable Features. Moveable wings and legs let you create dynamic action poses.\r\n-Large 12-Inch Size. A standout piece for playtime or any How to Train Your Dragon collection.\r\n-Perfect for Fans & Collectors. Whether you\'re reliving epic battles or displaying Toothless on your shelf, this figure is a must-have.\r\n-Join Hiccup and Toothless on new adventures with the 12-Inch Toothless Figure the ultimate companion for every dragon trainer!', 'Includes: 1 Dragon\r\nBrand: HowToTrainYourDragon, DreamWorks\r\nDimensions(cm): 11 x 34 x 6\r\nWeight (kg): 0.372\r\nAge Range: 3+\r\nBattery Required: No'),
('P002', 'Jurassic World Dinosaur Jurassic Park Dr. Ian Malcolm Glider Escape Pack', 'The Jurassic Park Dr. Ian Malcolm Glider Escape Pack! This Dr. Ian Malcolm 3.75 inch action with a Dilophosaurus and Triceratops. Accessories include a launcher and projectile, a wing-pack, a harness  and capture gear restraints for the Dilophosaurus!', 200.00, 150.00, 'SKU-0002', 30, 'C001', 'assets/images/jurassic_malcolm_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:47:38', '-The \'90s come roaring back with the Jurassic Park \'93 Classic Dr. Ian Malcolm Glider Escape Pack!\r\n-Launch the projectile with button activation and attach the wing-pack glider to the Dr. Ian Malcolm 3.75 in-scale articulated character figure for a quick escape!\r\n-Attach 1 capture gear accessory to the Dilophosaurus dinosaur toy\'s head and 1 restraint accessory to the arms and legs -- plus, place the young Triceratops in the harness!\r\n-Connected play! Scan the dinosaur\'s hidden Tracking Code in the free Jurassic World Facts App with a compatible smart device (not included) to initiate AUGMENTED REALITY activities and games.', 'Includes: 1 Dr. Ian Malcolm figure, 1 Dilophosaurus dinosaur, 1 young Triceratops dinosaur, and 6 accessories\r\nBrand: Jurassic World\r\nDimensions (cm): 27 x 7 x 13\r\nWeight (kg): 0.3\r\nAge Range: 4+\r\nBattery Required: No'),
('P003', 'Soldier Force Falcon Command Jet Playset', 'Take the mission to the skies! The action figure fits inside the cockpit and is ready for combat. Lower the windshield and raise the aircraft into the sky. Package includes a zipline and hooks so he can traverse the terrain when needed.', 90.00, 67.50, 'SKU-0003', 51, 'C001', 'assets/images/soldier_jet_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:47:48', '-F35 Jet with 2 different light and sound function\r\n-Attachable Weapon and Accessories', 'Includes: 1 F35 Jet, 1 figure and accessories\r\nBrand:	Soldier Force\r\nDimensions (cm): 28 x 25 x 9\r\nWeight (kg): 0.3666\r\nAge Range: 4+\r\nBattery Required: Yes'),
('P004', 'Super Wings Transforming Lucie', 'Season 8 of Super Wings: THE ELECTRIC HEROES! Jett and friends return with sleek electric upgrades, new powers, and 5 new allies. Their new base, the World Spaceport, launches them into a clean, green future!', 112.00, 84.00, 'SKU-0004', 12, 'C001', 'assets/images/superwings_lucie_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:48:03', '-True to animation content, S8 new characters\r\n-2 modes! Easily transforms from toy airplane to bot\r\n-With transforming electric shield\r\n5-inch scale transforming figure\r\n-Real working wheels', 'Includes: 5-inch scale transforming figure\r\nBrand: Super Wings\r\nDimensions (cm): 12 x 13 x 12\r\nWeight (kg): 0.135\r\nAge Range: 3+\r\nBattery Required: No'),
('P005', 'Transformers One Power Flip Optimus Prime', 'Experience the epic origins of legendary Transformer robots with this Transformer One Power Flip Optimus Prime action figure, inspired by the iconic character in the Transformer One movie!', 289.00, 216.75, 'SKU-0005', 12, 'C001', 'assets/images/optimusprime_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:24:22', '-POWER FLIP OPTIMUS PRIME ACTION FIGURE: Movie-inspired Optimus Prime toy grows in height from 8-10 inches (20-25 cm) and changes between 4 different converting modes: Orion Pax, Cybertronian Truck toy, Optimus Prime, and Ultimate Optimus Prime\r\n-ELECTRONIC TOY WITH LIGHTS, SOUNDS, & PHRASES: Activate lights, sounds and phrases at the push of a button or by converting between modes. Each mode has special effects. Includes 3x A76/LR44 button cell batteries. Speaks in English only\r\n-INITIATE POWER FLIP CONVERSION: Hold the Transformersnsformers toy’s arms and flip the figure to quickly convert from Optimus Prime to Ultimate Optimus Prime mode\r\n-GEAR UP WITH BATTLE ARMOR: Battle armor engages automatically after Power Flipping the robot toy to Ultimate Optimus Prime mode\r\n-4 CONVERTING MODES: 4-in-1 figure converts from truck toy to Orion Pax figure in 13 steps, from Orion Pax to Optimus Prime figure in 3 steps, and from Optimus Prime to Ultimate Optimus Prime figure with Power Flip action and pull-down leg extension\r\n-MOVIE-INSPIRED ACCESSORIES: Power Flip Optimus Prime toy comes with Star Warsord, ENerfgon axe, shield, and Matrix of Leadership accessories that attach to the action figure in each mode\r\n-TransformersNSFORMERS ONE MOVIE: This action figure is inspired by the Optimus Prime character from the movie Transformersnsformers One, the untold origin story of Optimus Prime and Megatron, once friends bonded like brothers, who changed the fate of Cybertron forever', 'Includes: Figure, 4 accessories, Instructions\r\nBrand: Transformers\r\nDimensions (cm): 26 x 30 x 10\r\nWeight (kg): 0.579\r\nAge Range: 6+\r\nBattery Required: Yes'),
('P006', 'How to Train Your Dragon Red Death Chomping Rampage', 'Unleash the ultimate dragon battle with the Red Death Chomping Rampage Figure! This massive and fearsome dragon, the legendary villain from How to Train Your Dragon!', 139.00, 104.25, 'SKU-0006', 51, 'C001', 'assets/images/red_death_dragon_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:27:44', '-Authentic Movie-Inspired Design - Highly detailed sculpting captures the monstrous size and power of Red Death.\r\n-Chomping Jaw Action - Press to activate Red Death powerful bite!\r\n-Poseable Wings & Limbs - Create epic battle poses and dynamic action scenes.\r\nPerfect for Play & Display - A must-have for How to Train Your Dragon fans and collectors.\r\n-Prepare for an earth-shaking showdown with the Red Death Chomping Rampage Figure , will you be able to defeat this legendary beast?', 'Includes: 2 Dragons\r\nBrand: HowToTrainYourDragon, DreamWorks\r\nDimensions (cm): 31 x 21 x 14\r\nWeight (kg): 0.350\r\nAge Range: 3+\r\nBattery Required: No'),
('P007', 'Transformers YOLOPARK Transformers: Rise of Beasts AMK Bumblebee', 'Colored semi-finished products made of PVC, which need to be assembled by themselves. 2. The height of the product is about 16~20 cm, with replacement accessories.', 159.00, 119.25, 'SKU-0007', 12, 'C001', 'assets/images/bumblebee_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:30:53', '- Colored semi-finished products made of PVC, which need to be assembled by themselves\r\n- The height of the product is about 16~20 cm, with replacement accessories', 'Includes: Robot, Accessories\r\nBrand: Transformers Rise of the Beasts\r\nDimensions (cm): 21 X 15 X6\r\nWeight (kg):0.48\r\nAge Range: 8+\r\nBattery Required: No'),
('P008', 'Marvel Legends Alist Iron Man Mark 85', 'This collectible 6-inch-scale Marvel Avengers figure is detailed to look like Iron Man from Marvel Studios Avengers: Endgame.', 125.00, 93.75, 'SKU-0008', 24, 'C001', 'assets/images/ironman_mark85_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:35:27', '-MARVEL STUDIOS’ AVENGERS: ENDGAME: This collectible Marvel figure is inspired by Iron Man’s appearance in the epic conclusion to the Infinity Saga, Marvel Studios’ Avengers: Endgame -- a great gift for collectors and fans ages 4 and up\r\n-PREMIUM DESIGN AND ARTICULATION: Marvel fans and collectors can display this 6 inch action figure (15 cm) -- featuring premium movie-accurate deco and design, and over 20 points of articulation -- in their Avengers action figure collections\r\n-IRON MAN TAKES FLIGHT: This officially licensed Iron Man Mark LXXXV figure comes with 2 alternate repulsor hands and 4 repulsor FX for dynamic poseability\r\n-WINDOW BOX PACKAGING: Display the MCU on your shelf with collectible window box packaging featuring movie-inspired package art\r\n-THE FINAL STAND: To release Thanos’ grip on the universe, Tony Stark fights alongside his fellow Avengers in his high-powered Mark LXXXV armor', 'Includes: Figure, 6 accessories\r\nBrand: Marvel\r\nDimensions (cm): 16 x 27 x 6\r\nWeight (kg): 0.218\r\nAge Range: 4+\r\nBattery Required: No'),
('P009', 'Marvel Spider-Man Titan Hero Series', 'Imagine swinging into the newest Spider-Man adventure with Spider-Man figures, vehicles, and roleplay items inspired by the Marvel comics. With this classic inspired line of toys, kids can imagine the web-slinging, wall-crawling action.', 45.00, 33.75, 'SKU-0009', 20, 'C001', 'assets/images/spiderman_titan_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:41:16', '-Your friendly neighborhood Spider-Man is now available in titan size! This 12-inch scale Spider-Man action figure features Spidey\'s classic suit, and the figure\'s multiple flex points make it easy for kids to imagine this super-sized superhero whizzing into the most epic battles.\r\n-Experience comic book fun with the figure.', 'Includes: 12-inch-scale Spider-Man figure\r\nBrand: Marvel\r\nDimensions (cm): 5 x 10 x 30\r\nWeight (kg): 0.380\r\nAge Range: 4+\r\nBattery Required: No\r\n'),
('P010', 'DC Comics 12-Inch Figure The Dark Knight Batman', 'Step into Gotham City with the DC Comics 12-Inch Figure of The Dark Knight Batman! This impressively detailed action figure stands a commanding 12 inches tall, making it a striking addition to any collection.', 69.00, 51.75, 'SKU-0010', 42, 'C001', 'assets/images/batman_darkknight_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-04 10:48:30', '-Crafted with exceptional attention to detail, this figure showcases Batman in his iconic suit, complete with a flowing cape and signature bat emblem. \r\n-The realistic facial features capture the intensity and determination of the legendary hero, making it perfect for both play and display. \r\n-With multiple points of articulation, this figure allows fans to pose Batman in dynamic action scenes or classic stances. -Whether you\'re reenacting epic battles against Gotham\'s villains or showcasing him on your shelf, this Dark Knight figure is a must-have for DC Comics enthusiasts. ', 'Includes: 12-Inch Figure \r\nBrand: DC Comics\r\nDimensions (cm): 11 x 34 x 6\r\nWeight (kg): 0.1\r\nAge Range: 3+\r\nBattery Required: No');

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
(1, 'P001', 'assets/images/toothless_1.png', '2025-08-02 20:43:55'),
(2, 'P001', 'assets/images/toothless_2.png', '2025-08-02 20:43:55'),
(3, 'P001', 'assets/images/toothless_3.png', '2025-08-02 20:43:55'),
(4, 'P002', 'assets/images/jurassic_malcolm_1.png', '2025-08-02 20:43:55'),
(5, 'P002', 'assets/images/jurassic_malcolm_2.png', '2025-08-02 20:43:55'),
(6, 'P002', 'assets/images/jurassic_malcolm_3.png', '2025-08-02 20:43:55'),
(7, 'P003', 'assets/images/soldier_jet_1.png', '2025-08-02 20:43:55'),
(8, 'P003', 'assets/images/soldier_jet_2.png', '2025-08-02 20:43:55'),
(9, 'P003', 'assets/images/soldier_jet_3.png', '2025-08-02 20:43:55'),
(10, 'P004', 'assets/images/superwings_lucie_1.png', '2025-08-02 20:43:55'),
(11, 'P004', 'assets/images/superwings_lucie_2.png', '2025-08-02 20:43:55'),
(12, 'P005', 'assets/images/optimusprime_1.png', '2025-08-02 20:43:55'),
(13, 'P005', 'assets/images/optimusprime_2.png', '2025-08-02 20:43:55'),
(14, 'P005', 'assets/images/optimusprime_3.png', '2025-08-02 20:43:55'),
(15, 'P006', 'assets/images/red_death_dragon_1.png', '2025-08-02 20:43:55'),
(16, 'P006', 'assets/images/red_death_dragon_2.png', '2025-08-02 20:43:55'),
(17, 'P006', 'assets/images/red_death_dragon_3.png', '2025-08-02 20:43:55'),
(18, 'P007', 'assets/images/bumblebee_1.png', '2025-08-02 20:43:55'),
(19, 'P007', 'assets/images/bumblebee_2.png', '2025-08-02 20:43:55'),
(20, 'P007', 'assets/images/bumblebee_3.png', '2025-08-02 20:43:55'),
(21, 'P007', 'assets/images/bumblebee_4.png', '2025-08-02 20:43:55'),
(22, 'P007', 'assets/images/bumblebee_5.png', '2025-08-02 20:43:55'),
(23, 'P008', 'assets/images/ironman_mark85_1.png', '2025-08-02 20:43:55'),
(24, 'P008', 'assets/images/ironman_mark85_2.png', '2025-08-02 20:43:55'),
(25, 'P008', 'assets/images/ironman_mark85_3.png', '2025-08-02 20:43:55'),
(26, 'P008', 'assets/images/ironman_mark85_4.png', '2025-08-02 20:43:55'),
(27, 'P008', 'assets/images/ironman_mark85_5.png', '2025-08-02 20:43:55'),
(28, 'P009', 'assets/images/spiderman_titan_1.png', '2025-08-02 20:43:55'),
(29, 'P009', 'assets/images/spiderman_titan_2.png', '2025-08-02 20:43:55'),
(30, 'P009', 'assets/images/spiderman_titan_3.png', '2025-08-02 20:43:55'),
(31, 'P010', 'assets/images/batman_darkknight_1.png', '2025-08-02 20:43:55'),
(32, 'P010', 'assets/images/batman_darkknight_2.png', '2025-08-02 20:43:55'),
(33, 'P010', 'assets/images/batman_darkknight_3.png', '2025-08-02 20:43:55');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` varchar(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `product_id` varchar(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `title` varchar(200) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `profile_pic` varchar(255) DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `role`, `profile_pic`, `remember_token`, `created_at`, `updated_at`) VALUES
('USR00000001', 'admin', 'admin@toylandstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, '2025-08-02 16:04:16', '2025-08-02 16:04:16'),
('USR94995839', 'salman_moosa', 'salmaanmoosa498@gmail.com', '$2y$10$mDAihTtzdNvX1kRt8kv7cOQ0SLmpEIrKg2ylRBlvnl7b2VrgiF7/6', 'member', NULL, NULL, '2025-08-02 16:06:19', '2025-08-02 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `newsletter_subscription` tinyint(1) DEFAULT 0,
  `marketing_emails` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`profile_id`, `user_id`, `first_name`, `last_name`, `phone`, `date_of_birth`, `address`, `newsletter_subscription`, `marketing_emails`, `created_at`, `updated_at`) VALUES
(1, 'USR00000001', 'Admin', 'User', '555-0000', NULL, 'ToyLand Store HQ, Admin City, AC 12345, USA', 0, 0, '2025-08-02 16:04:16', '2025-08-02 16:04:16'),
(2, 'USR94995839', 'Salman', 'Moosa', '0185891720', '2004-04-03', 'J004, Jalan Malinja, Taman Bunga Raya, Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur 53000, US', 0, 0, '2025-08-02 16:06:19', '2025-08-02 16:06:19');

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
-- Indexes for dumped tables
--

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
  ADD KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_status` (`status`);

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
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_status` (`status`);

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
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `idx_user_id` (`user_id`);

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
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_fk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

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
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_fk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

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