-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 08:30 PM
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
CREATE DATABASE IF NOT EXISTS `web_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `web_db`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
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
('SC006', 'LEGO', 'Classic interlocking brick sets for creative construction and learning.', 'assets/images/categories/lego.png', 'C002', 'active', '2025-08-05 18:22:07', '2025-08-18 16:57:44'),
('SC007', 'MEGA BLOCKS', 'Larger block toys great for younger builders with themed sets.', 'assets/images/categories/mega_blocks.png', 'C002', 'active', '2025-08-05 18:22:07', '2025-08-18 16:57:54'),
('SC008', 'Cars & Vehicles', 'Discover toy cars and vehicles that spark creativity and endless play.', 'assets/images/categories/cat_68a36b6d7ec940.27591070.png', 'C003', 'active', '2025-08-18 18:05:33', '2025-08-18 18:05:33'),
('SC009', 'Radio & Remote Control Vehicles', 'Radio & Remote Control (R/C) Vehicles', 'assets/images/categories/cat_68a370f759de23.71487171.png', 'C003', 'active', '2025-08-18 18:29:11', '2025-08-18 18:29:11');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
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

DROP TABLE IF EXISTS `products`;
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
('P001', 'How to Train Your Dragon 12-Inch Toothless', 'Bring home the magic of How to Train Your Dragon with the 12-Inch Toothless Figure! This large-scale, highly detailed version of the beloved Night Fury is perfect for both play and display.', 79.00, 59.25, 'SKU-0001', 30, 'SC001', 'assets/images/products/toothless_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:46:58', '-Authentic Design. Detailed sculpting and vibrant colors bring Toothless to life just like in the movies.\r\n-Poseable Features. Moveable wings and legs let you create dynamic action poses.\r\n-Large 12-Inch Size. A standout piece for playtime or any How to Train Your Dragon collection.\r\n-Perfect for Fans & Collectors. Whether you\'re reliving epic battles or displaying Toothless on your shelf, this figure is a must-have.\r\n-Join Hiccup and Toothless on new adventures with the 12-Inch Toothless Figure the ultimate companion for every dragon trainer!', 'Includes: 1 Dragon\r\nBrand: HowToTrainYourDragon, DreamWorks\r\nDimensions(cm): 11 x 34 x 6\r\nWeight (kg): 0.372\r\nAge Range: 3+\r\nBattery Required: No'),
('P002', 'Jurassic World Dinosaur Jurassic Park Dr. Ian Malcolm Glider Escape Pack', 'The Jurassic Park Dr. Ian Malcolm Glider Escape Pack! This Dr. Ian Malcolm 3.75 inch action with a Dilophosaurus and Triceratops. Accessories include a launcher and projectile, a wing-pack, a harness  and capture gear restraints for the Dilophosaurus!', 200.00, 150.00, 'SKU-0002', 30, 'SC003', 'assets/images/products/jurassic_malcolm_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:47:08', '-The \'90s come roaring back with the Jurassic Park \'93 Classic Dr. Ian Malcolm Glider Escape Pack!\r\n-Launch the projectile with button activation and attach the wing-pack glider to the Dr. Ian Malcolm 3.75 in-scale articulated character figure for a quick escape!\r\n-Attach 1 capture gear accessory to the Dilophosaurus dinosaur toy\'s head and 1 restraint accessory to the arms and legs -- plus, place the young Triceratops in the harness!\r\n-Connected play! Scan the dinosaur\'s hidden Tracking Code in the free Jurassic World Facts App with a compatible smart device (not included) to initiate AUGMENTED REALITY activities and games.', 'Includes: 1 Dr. Ian Malcolm figure, 1 Dilophosaurus dinosaur, 1 young Triceratops dinosaur, and 6 accessories\r\nBrand: Jurassic World\r\nDimensions (cm): 27 x 7 x 13\r\nWeight (kg): 0.3\r\nAge Range: 4+\r\nBattery Required: No'),
('P003', 'Soldier Force Falcon Command Jet Playset', 'Take the mission to the skies! The action figure fits inside the cockpit and is ready for combat. Lower the windshield and raise the aircraft into the sky. Package includes a zipline and hooks so he can traverse the terrain when needed.', 90.00, 67.50, 'SKU-0003', 51, 'SC004', 'assets/images/products/soldier_jet_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:47:15', '-F35 Jet with 2 different light and sound function\r\n-Attachable Weapon and Accessories', 'Includes: 1 F35 Jet, 1 figure and accessories\r\nBrand:	Soldier Force\r\nDimensions (cm): 28 x 25 x 9\r\nWeight (kg): 0.3666\r\nAge Range: 4+\r\nBattery Required: Yes'),
('P004', 'Super Wings Transforming Lucie', 'Season 8 of Super Wings: THE ELECTRIC HEROES! Jett and friends return with sleek electric upgrades, new powers, and 5 new allies. Their new base, the World Spaceport, launches them into a clean, green future!', 112.00, 84.00, 'SKU-0004', 12, 'SC001', 'assets/images/products/superwings_lucie_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:47:26', '-True to animation content, S8 new characters\r\n-2 modes! Easily transforms from toy airplane to bot\r\n-With transforming electric shield\r\n5-inch scale transforming figure\r\n-Real working wheels', 'Includes: 5-inch scale transforming figure\r\nBrand: Super Wings\r\nDimensions (cm): 12 x 13 x 12\r\nWeight (kg): 0.135\r\nAge Range: 3+\r\nBattery Required: No'),
('P005', 'Transformers One Power Flip Optimus Prime', 'Experience the epic origins of legendary Transformer robots with this Transformer One Power Flip Optimus Prime action figure, inspired by the iconic character in the Transformer One movie!', 289.00, 216.75, 'SKU-0005', 12, 'SC001', 'assets/images/products/optimusprime_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:47:39', '-POWER FLIP OPTIMUS PRIME ACTION FIGURE: Movie-inspired Optimus Prime toy grows in height from 8-10 inches (20-25 cm) and changes between 4 different converting modes: Orion Pax, Cybertronian Truck toy, Optimus Prime, and Ultimate Optimus Prime\r\n-ELECTRONIC TOY WITH LIGHTS, SOUNDS, & PHRASES: Activate lights, sounds and phrases at the push of a button or by converting between modes. Each mode has special effects. Includes 3x A76/LR44 button cell batteries. Speaks in English only\r\n-INITIATE POWER FLIP CONVERSION: Hold the Transformersnsformers toy’s arms and flip the figure to quickly convert from Optimus Prime to Ultimate Optimus Prime mode\r\n-GEAR UP WITH BATTLE ARMOR: Battle armor engages automatically after Power Flipping the robot toy to Ultimate Optimus Prime mode\r\n-4 CONVERTING MODES: 4-in-1 figure converts from truck toy to Orion Pax figure in 13 steps, from Orion Pax to Optimus Prime figure in 3 steps, and from Optimus Prime to Ultimate Optimus Prime figure with Power Flip action and pull-down leg extension\r\n-MOVIE-INSPIRED ACCESSORIES: Power Flip Optimus Prime toy comes with Star Warsord, ENerfgon axe, shield, and Matrix of Leadership accessories that attach to the action figure in each mode\r\n-TransformersNSFORMERS ONE MOVIE: This action figure is inspired by the Optimus Prime character from the movie Transformersnsformers One, the untold origin story of Optimus Prime and Megatron, once friends bonded like brothers, who changed the fate of Cybertron forever', 'Includes: Figure, 4 accessories, Instructions\r\nBrand: Transformers\r\nDimensions (cm): 26 x 30 x 10\r\nWeight (kg): 0.579\r\nAge Range: 6+\r\nBattery Required: Yes'),
('P006', 'How to Train Your Dragon Red Death Chomping Rampage', 'Unleash the ultimate dragon battle with the Red Death Chomping Rampage Figure! This massive and fearsome dragon, the legendary villain from How to Train Your Dragon!', 139.00, 104.25, 'SKU-0006', 51, 'SC001', 'assets/images/products/red_death_dragon_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:47:48', '-Authentic Movie-Inspired Design - Highly detailed sculpting captures the monstrous size and power of Red Death.\r\n-Chomping Jaw Action - Press to activate Red Death powerful bite!\r\n-Poseable Wings & Limbs - Create epic battle poses and dynamic action scenes.\r\nPerfect for Play & Display - A must-have for How to Train Your Dragon fans and collectors.\r\n-Prepare for an earth-shaking showdown with the Red Death Chomping Rampage Figure , will you be able to defeat this legendary beast?', 'Includes: 2 Dragons\r\nBrand: HowToTrainYourDragon, DreamWorks\r\nDimensions (cm): 31 x 21 x 14\r\nWeight (kg): 0.350\r\nAge Range: 3+\r\nBattery Required: No'),
('P007', 'Transformers YOLOPARK Transformers: Rise of Beasts AMK Bumblebee', 'Colored semi-finished products made of PVC, which need to be assembled by themselves. 2. The height of the product is about 16~20 cm, with replacement accessories.', 159.00, 119.25, 'SKU-0007', 12, 'SC005', 'assets/images/products/bumblebee_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:47:59', '- Colored semi-finished products made of PVC, which need to be assembled by themselves\r\n- The height of the product is about 16~20 cm, with replacement accessories', 'Includes: Robot, Accessories\r\nBrand: Transformers Rise of the Beasts\r\nDimensions (cm): 21 X 15 X6\r\nWeight (kg):0.48\r\nAge Range: 8+\r\nBattery Required: No'),
('P008', 'Marvel Legends Alist Iron Man Mark 85', 'This collectible 6-inch-scale Marvel Avengers figure is detailed to look like Iron Man from Marvel Studios Avengers: Endgame.', 125.00, 93.75, 'SKU-0008', 24, 'SC001', 'assets/images/products/ironman_mark85_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:48:08', '-MARVEL STUDIOS’ AVENGERS: ENDGAME: This collectible Marvel figure is inspired by Iron Man’s appearance in the epic conclusion to the Infinity Saga, Marvel Studios’ Avengers: Endgame -- a great gift for collectors and fans ages 4 and up\r\n-PREMIUM DESIGN AND ARTICULATION: Marvel fans and collectors can display this 6 inch action figure (15 cm) -- featuring premium movie-accurate deco and design, and over 20 points of articulation -- in their Avengers action figure collections\r\n-IRON MAN TAKES FLIGHT: This officially licensed Iron Man Mark LXXXV figure comes with 2 alternate repulsor hands and 4 repulsor FX for dynamic poseability\r\n-WINDOW BOX PACKAGING: Display the MCU on your shelf with collectible window box packaging featuring movie-inspired package art\r\n-THE FINAL STAND: To release Thanos’ grip on the universe, Tony Stark fights alongside his fellow Avengers in his high-powered Mark LXXXV armor', 'Includes: Figure, 6 accessories\r\nBrand: Marvel\r\nDimensions (cm): 16 x 27 x 6\r\nWeight (kg): 0.218\r\nAge Range: 4+\r\nBattery Required: No'),
('P009', 'Marvel Spider-Man Titan Hero Series', 'Imagine swinging into the newest Spider-Man adventure with Spider-Man figures, vehicles, and roleplay items inspired by the Marvel comics. With this classic inspired line of toys, kids can imagine the web-slinging, wall-crawling action.', 45.00, 33.75, 'SKU-0009', 20, 'SC001', 'assets/images/products/spiderman_titan_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:48:29', '-Your friendly neighborhood Spider-Man is now available in titan size! This 12-inch scale Spider-Man action figure features Spidey\'s classic suit, and the figure\'s multiple flex points make it easy for kids to imagine this super-sized superhero whizzing into the most epic battles.\r\n-Experience comic book fun with the figure.', 'Includes: 12-inch-scale Spider-Man figure\r\nBrand: Marvel\r\nDimensions (cm): 5 x 10 x 30\r\nWeight (kg): 0.380\r\nAge Range: 4+\r\nBattery Required: No\r\n'),
('P010', 'DC Comics 12-Inch Figure The Dark Knight Batman', 'Step into Gotham City with the DC Comics 12-Inch Figure of The Dark Knight Batman! This impressively detailed action figure stands a commanding 12 inches tall, making it a striking addition to any collection.', 69.00, 51.75, 'SKU-0010', 42, 'SC001', 'assets/images/products/batman_darkknight_1.png', 'active', 1, '2025-08-02 20:33:54', '2025-08-18 19:48:46', '-Crafted with exceptional attention to detail, this figure showcases Batman in his iconic suit, complete with a flowing cape and signature bat emblem. \r\n-The realistic facial features capture the intensity and determination of the legendary hero, making it perfect for both play and display. \r\n-With multiple points of articulation, this figure allows fans to pose Batman in dynamic action scenes or classic stances. -Whether you\'re reenacting epic battles against Gotham\'s villains or showcasing him on your shelf, this Dark Knight figure is a must-have for DC Comics enthusiasts. ', 'Includes: 12-Inch Figure \r\nBrand: DC Comics\r\nDimensions (cm): 11 x 34 x 6\r\nWeight (kg): 0.1\r\nAge Range: 3+\r\nBattery Required: No'),
('P011', 'LEGO Disney 43249 Stitch', 'The incorrigible extraterrestrial from the hit Disney movie, dressed in a Hawaiian shirt, has movable ears and a turning head, a buildable ice-cream cone that the character can hold and a buildable flower that can be added or removed. This kids building kit looks great on display in any room and makes a fun Disney gift idea for older children and movie-lovers as they set up the buildable character.', 281.18, 239.00, 'SKU-11', 12, 'SC006', 'assets/images/products/prod_68a44026104874.60685478.png', 'active', 0, '2025-08-19 09:13:10', '2025-08-19 09:49:27', '-Disney gift for kids – A building toy set featuring a Disney character with functions and accessories that makes a gift for movie-lovers, girls and boys aged 9+ to share at school or home\r\n-A helping hand – Let the LEGO® Builder app guide kids on an intuitive building adventure, where they can save sets, track progress and zoom in and rotate models in 3D while they build\r\n-Expand life skills – With a LEGO® ? Disney buildable character, accessories and functions to enhance display, this kids’ construction toy helps foster life skills through fun', 'Includes: 730 pieces LEGO blocks\r\nBrand: LEGO\r\nDimensions(cm): 26.2 × 28.2 × 9.6\r\nWeight (kg): 0.869\r\nAge Range: 9+\r\nBattery Required: No');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
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
(39, 'P011', 'assets/images/products/prod_68a44026143050.49089338.png', '2025-08-19 09:13:10');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
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

DROP TABLE IF EXISTS `user`;
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
('USR63982070', 'admin1', 'admin@toyland.com', '$2y$10$sW4MNThEw1W5l0S6rz/GG.N0rA/A7oCMVgRCy/Hy9cFHU8MaO1wla', 'admin', NULL, NULL, '2025-08-17 18:05:07', '2025-08-17 18:05:07'),
('USR86216351', 'stephanie_mak', 'stepmak724@gmail.com', '$2y$10$22aFdByU874jeA4pePRZvu8flto58GGa.GCU8ezi/xqkgZHLjYZXm', 'member', NULL, NULL, '2025-08-06 04:32:29', '2025-08-06 04:32:29'),
('USR94995839', 'salman_moosa', 'salmaanmoosa498@gmail.com', '$2y$10$mDAihTtzdNvX1kRt8kv7cOQ0SLmpEIrKg2ylRBlvnl7b2VrgiF7/6', 'member', NULL, NULL, '2025-08-02 16:06:19', '2025-08-02 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
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
(0, 'USR86216351', 'Stephanie', 'Mak', '0112524000', '2000-06-10', '21, Jalan 23, Taman Bunga Raya, Setapak, Kuala Lumpur 51200, CA', 1, 1, '2025-08-06 04:32:29', '2025-08-06 04:32:29'),
(1, 'USR00000001', 'Admin', 'User', '555-0000', NULL, 'ToyLand Store HQ, Admin City, AC 12345, USA', 0, 0, '2025-08-02 16:04:16', '2025-08-02 16:04:16'),
(2, 'USR94995839', 'Salman', 'Moosa', '0185891720', '2004-04-03', 'J004, Jalan Malinja, Taman Bunga Raya, Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur 53000, US', 0, 0, '2025-08-02 16:06:19', '2025-08-02 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
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
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

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
