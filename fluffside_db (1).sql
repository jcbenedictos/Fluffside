-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2026 at 08:19 AM
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
-- Database: `fluffside_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_applications`
--

CREATE TABLE `tbl_applications` (
  `app_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_id` varchar(60) NOT NULL,
  `app_type` enum('Adoption','Foster') NOT NULL,
  `status` enum('active','completed','rejected') NOT NULL DEFAULT 'active',
  `current_step` tinyint(4) NOT NULL DEFAULT 1,
  `last_update` text DEFAULT NULL,
  `rejected` tinyint(1) NOT NULL DEFAULT 0,
  `submitted_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_app_adoption`
--

CREATE TABLE `tbl_app_adoption` (
  `app_id` varchar(20) NOT NULL,
  `interview_date` date DEFAULT NULL,
  `interview_time` time DEFAULT NULL,
  `same_month` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_app_applicant`
--

CREATE TABLE `tbl_app_applicant` (
  `app_id` varchar(20) NOT NULL,
  `first_name` varchar(60) NOT NULL,
  `last_name` varchar(60) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `pronouns` varchar(40) DEFAULT NULL,
  `pronouns_other` varchar(80) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `social_media` varchar(200) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `civil_status` varchar(40) DEFAULT NULL,
  `civil_status_other` varchar(80) DEFAULT NULL,
  `prompt_src` varchar(60) DEFAULT NULL,
  `prompt_src_other` varchar(80) DEFAULT NULL,
  `adopted_before` varchar(10) DEFAULT NULL,
  `alt_first_name` varchar(60) DEFAULT NULL,
  `alt_last_name` varchar(60) DEFAULT NULL,
  `alt_relationship` varchar(60) DEFAULT NULL,
  `alt_phone` varchar(20) DEFAULT NULL,
  `alt_email` varchar(100) DEFAULT NULL,
  `building_type` varchar(60) DEFAULT NULL,
  `building_type_other` varchar(80) DEFAULT NULL,
  `do_rent` varchar(10) DEFAULT NULL,
  `live_with` varchar(200) DEFAULT NULL,
  `live_with_other` varchar(100) DEFAULT NULL,
  `allergic` varchar(10) DEFAULT NULL,
  `household_support` varchar(10) DEFAULT NULL,
  `support_explain` text DEFAULT NULL,
  `other_pets` varchar(10) DEFAULT NULL,
  `past_pets` varchar(10) DEFAULT NULL,
  `near_road` varchar(10) DEFAULT NULL,
  `move_plan` text DEFAULT NULL,
  `care_plan` text DEFAULT NULL,
  `financial_plan` text DEFAULT NULL,
  `emergency_plan` text DEFAULT NULL,
  `hours_alone` text DEFAULT NULL,
  `valid_id_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_app_foster`
--

CREATE TABLE `tbl_app_foster` (
  `app_id` varchar(20) NOT NULL,
  `foster_duration` varchar(60) DEFAULT NULL,
  `shelter_visit` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_messages`
--

CREATE TABLE `tbl_messages` (
  `message_id` int(11) NOT NULL,
  `app_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` enum('admin','user') NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_orders`
--

CREATE TABLE `tbl_orders` (
  `order_id` int(11) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `payment_method` enum('Cash on Delivery','GCash','Credit / Debit Card') NOT NULL DEFAULT 'Cash on Delivery',
  `subtotal` decimal(10,2) NOT NULL,
  `donation_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `ordered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order_items`
--

CREATE TABLE `tbl_order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_title` varchar(200) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pets`
--

CREATE TABLE `tbl_pets` (
  `pet_id` varchar(60) NOT NULL,
  `pet_name` varchar(100) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `animal_type` enum('DOG','CAT','RABBIT','HAMSTER','BIRD','OTHER') NOT NULL,
  `gender` enum('MALE','FEMALE') NOT NULL,
  `age_desc` varchar(60) DEFAULT NULL,
  `age_group` enum('Young','Adult','Senior') DEFAULT 'Adult',
  `image_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pets`
--

INSERT INTO `tbl_pets` (`pet_id`, `pet_name`, `breed`, `animal_type`, `gender`, `age_desc`, `age_group`, `image_path`, `description`, `is_available`, `created_at`) VALUES
('biscuit', 'BISCUIT', 'Netherland Dwarf', 'RABBIT', 'MALE', '9 years old', 'Senior', 'Assets/Residents/Rabbit/Biscuit1.jpg', 'Don\'t let his tiny size fool you—Biscuit has a personality that could fill a whole room! Biscuit is a classic Netherland Dwarf with a beautiful tawny coat and a very expressive face. This breed is known for being energetic and spunky, and Biscuit is no exception. He is a busy little bee who loves to zoom around his play area and perform impressive binkies. Because of his small stature, he would do best in a calm home with older children or adults who can handle him with care. If you are looking for a small, intelligent, and endlessly entertaining companion, Biscuit is ready to hop into your heart!', 1, '2026-06-12 05:05:29'),
('cheddar', 'CHEDDAR', 'Syrian Hamster', 'HAMSTER', 'MALE', '6 months old', 'Young', 'Assets/Residents/Hamster/Cheddar1.jpg', 'Say hello to Cheddar, the tiny explorer! Cheddar is a classic Syrian hamster with a curious and gentle nature. Syrians are known for being the most handleable of the hamster breeds, and Cheddar is already very comfortable interacting with humans. At 6 months old, he is full of energy and loves exploring safe, enclosed play areas. He is a master architect who spends his hours creating elaborate tunnels and cozy nests in his bedding. If you\'re looking for a small, low-maintenance friend who is big on personality, Cheddar is ready to roll into your life!', 1, '2026-06-12 05:05:29'),
('clover', 'CLOVER', 'Lop-eared Rabbit', 'RABBIT', 'FEMALE', '1 year old', 'Adult', 'Assets/Residents/Rabbit/Clover1.jpg', 'Meet Clover, a gentle soul with the softest fur you\'ll ever touch! With her floppy ears and bright, curious eyes, Clover is a bundle of quiet joy. Holland Lops are known for having wonderful, sweet temperaments, making them great indoor companions. Clover is quite the \"binkier\"—when she\'s happy, she\'ll do a little mid-air twist to show it! She is litter-box trained and would love a home where she can have some supervised time to hop around and explore. If you\'re looking for a calm, quiet, and incredibly cute friend to brighten your days, Clover is waiting for you.', 1, '2026-06-12 05:05:29'),
('cosmo', 'COSMO', 'Samoyed', 'DOG', 'MALE', '6 months old', 'Young', 'Assets/Residents/Dog/Cosmo1.jpg', 'Cosmo is a fluffy, energetic Samoyed puppy with the most gorgeous white coat you have ever seen! Full of boundless energy and playful spirit, Cosmo loves outdoor adventures, cuddle sessions, and meeting new friends. Samoyeds are known for their friendly and gentle temperament, making them wonderful family companions. At 6 months old, Cosmo is eager to learn and would thrive with consistent, positive training and plenty of exercise. If you are looking for a loyal, affectionate, and absolutely stunning companion, Cosmo is ready to light up your world!', 1, '2026-06-12 05:05:29'),
('honey', 'HONEY', 'Holland Lop', 'RABBIT', 'FEMALE', '1 year old', 'Adult', 'Assets/Residents/Rabbit/Honey1.png', 'Honey is as sweet as her name suggests! This beautiful cinnamon-colored Holland Lop, is looking for a home where she can be the center of attention. She is a curious and active young rabbit who loves to investigate every corner of her play area. Honey is very social and would likely thrive in a home with a patient family who can give her plenty of floor time to hop and play. She is already working on her litter-box skills and is ready to find a forever home where she can binky to her heart\'s content.', 1, '2026-06-12 05:05:29'),
('loki', 'LOKI', 'Aspin', 'DOG', 'MALE', '1 year old', 'Adult', 'Assets/Residents/Dog/Loki1.png', 'Meet the ultimate \"Good Boy!\". This charming Aspin, Loki, is the definition of a loyal companion. Aspins are celebrated by their hardy nature and high intelligence, making them very quick learners when it comes to new tricks. At around a year old, he has moved past the tiny puppy stage but still has plenty of \"zoomies\" left in him. He is medium-sized, perfectly athletic, and has a short, easy-to-groom coat. Whether you\'re looking for a hiking buddy or a dedicated protector for your home, this sweet boy is ready to give a lifetime of wagging tails and smiles.', 1, '2026-06-12 05:05:29'),
('minty', 'MINTY', 'Fischer\'s Lovebird', 'BIRD', 'FEMALE', '4 months old', 'Young', 'Assets/Residents/Bird/Minty1.jpg', 'Misty is a tiny bundle of sunshine wrapped in feathers! This adorable budgie is full of playful energy and loves fluttering around her perch while happily chirping little tunes throughout the day. She is naturally curious and enjoys investigating new toys, especially anything shiny or colorful. Though she may act shy at first, Misty warms up quickly with patience and soft voices, and she absolutely loves spending time near her favorite humans. With her bright personality and sweet nature, Misty would make a wonderful companion for someone looking for a cheerful little friend.', 1, '2026-06-12 05:05:29'),
('mochi', 'MOCHI', 'Siamese', 'CAT', 'FEMALE', '4 months old', 'Young', 'Assets/Residents/Cat/Mochi1.jpg', 'Meet Mochi, a tiny feline with a big personality! With her striking blue eyes and beautiful pointed coat, she is sure to steal hearts instantly. Mochi is a social butterfly who thrives on interaction and \"chatting\" with her human companions. She is at that perfect age where she is full of curiosity and playfulness, but also highly values her beauty sleep. Siamese-type cats are known for being exceptionally loyal and dog-like in their devotion, and Mochi is already showing those wonderful traits. If you are looking for a clever, talkative, and affectionate companion, Mochi is the purr-fect match for you!', 1, '2026-06-12 05:05:29'),
('pearl', 'PEARL', 'Persian', 'CAT', 'FEMALE', '7 years old', 'Senior', 'Assets/Residents/Cat/Pearl1.jpg', 'Pearl is a true royalty of the shelter! Looking at her, it\'s clear she is a refined and dignified companion. Persians are famous for their sweet, gentle temperaments, and Pearl embodies this perfectly. She isn\'t much for jumping on counters or chasing lasers; she would much rather sit beautifully by your side while you read or work. Because of her long, luxurious fur, she will need a family dedicated to keeping her looking her best with regular grooming. If you are looking for a calm, quiet, and breathtakingly beautiful feline friend, Pearl is ready to grace your home with her presence.', 1, '2026-06-12 05:05:29'),
('pebble', 'PEBBLE', 'Winter White Dwarf Hamster', 'HAMSTER', 'MALE', '5 months old', 'Young', 'Assets/Residents/Hamster/Pebble1.jpg', 'Pebble is a tiny ball of energy! Pebble is a charming Winter White Dwarf hamster with beautiful grey and white markings. Unlike larger hamsters, dwarf breeds are known for being very fast and active, making them a joy to watch as they go about their \"business.\" Pebble is a curious little guy who is slowly learning that human hands often carry tasty treats. He is a master of the wheel and would love a home with plenty of vertical and horizontal space to explore. If you\'re looking for a small, lively, and incredibly cute companion, Pebble is ready to meet you!', 1, '2026-06-12 05:05:29'),
('scout', 'SCOUT', 'Golden Retriever', 'DOG', 'MALE', '12 weeks old', 'Young', 'Assets/Residents/Dog/Scout1.jpg', 'Scout is a bundle of golden sunshine looking for his forever home! At just 12 weeks old, he is in the heart of his \"explorer phase\", curious about every leaf, butterfly, and shoelace he encounters. True to his breed, Scout is incredibly social and views every stranger as a potential best friend. He is currently working on his potty training and basic \"sit\", though he is easily distracted by the promise of a treat or a head pat. If you are looking for a loyal, wiggly companion who will grow into a devoted family member, Scout is ready to join your pack!', 1, '2026-06-12 05:05:29'),
('skye', 'SKYE', 'Cockatiel', 'BIRD', 'FEMALE', '1 year old', 'Adult', 'Assets/Residents/Bird/Skye1.jpg', 'Meet Skye, a cheerful little cockatiel with a curious heart and an adorable crest that pops up whenever she gets excited! Skye loves spending her mornings perched by the window, chirping softly while watching the world go by. She is playful, intelligent, and enjoys learning simple whistles and tricks—especially when treats are involved. Cockatiels are known for forming strong bonds with their humans, and Skye absolutely adores attention and companionship. Whether she\'s hopping onto your shoulder or softly singing nearby, Skye is sure to fill your home with warmth and happy energy.', 1, '2026-06-12 05:05:29'),
('sunny', 'SUNNY', 'Puspin', 'CAT', 'FEMALE', '3 years old', 'Adult', 'Assets/Residents/Cat/Sunny1.png', 'Sunny is a vibrant orange Puspin who is ready to bring a burst of light into your home! As you can see, she has a wonderfully expressive face and a relaxed, friendly vibe. Puspins are known for being incredibly smart and adaptable, and Sunny is a perfect example—she gets along well with almost everyone and settles into new environments with ease. At 3 years old, she has the perfect balance of playfulness and \"chill\" energy. She is a low-maintenance, high-affection companion who just wants a warm lap and a loving family to call her own.', 1, '2026-06-12 05:05:29');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pet_gallery`
--

CREATE TABLE `tbl_pet_gallery` (
  `gallery_id` int(11) NOT NULL,
  `pet_id` varchar(60) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pet_gallery`
--

INSERT INTO `tbl_pet_gallery` (`gallery_id`, `pet_id`, `image_path`, `sort_order`) VALUES
(1, 'scout', 'Assets/Residents/Dog/Scout1.jpg', 0),
(2, 'scout', 'Assets/Residents/Dog/Scout2.jpg', 1),
(3, 'scout', 'Assets/Residents/Dog/Scout3.jpg', 2),
(4, 'clover', 'Assets/Residents/Rabbit/Clover1.jpg', 0),
(5, 'clover', 'Assets/Residents/Rabbit/Clover2.jpg', 1),
(6, 'clover', 'Assets/Residents/Rabbit/Clover3.png', 2),
(7, 'cheddar', 'Assets/Residents/Hamster/Cheddar1.jpg', 0),
(8, 'cheddar', 'Assets/Residents/Hamster/Cheddar2.jpg', 1),
(9, 'cheddar', 'Assets/Residents/Hamster/Cheddar3.jpg', 2),
(10, 'mochi', 'Assets/Residents/Cat/Mochi1.jpg', 0),
(11, 'mochi', 'Assets/Residents/Cat/Mochi2.jpg', 1),
(12, 'mochi', 'Assets/Residents/Cat/Mochi3.jpg', 2),
(13, 'pearl', 'Assets/Residents/Cat/Pearl1.jpg', 0),
(14, 'pearl', 'Assets/Residents/Cat/Pearl2.jpg', 1),
(15, 'pearl', 'Assets/Residents/Cat/Pearl3.png', 2),
(16, 'loki', 'Assets/Residents/Dog/Loki1.png', 0),
(17, 'loki', 'Assets/Residents/Dog/Loki2.png', 1),
(18, 'loki', 'Assets/Residents/Dog/Loki3.jpg', 2),
(19, 'sunny', 'Assets/Residents/Cat/Sunny1.png', 0),
(20, 'sunny', 'Assets/Residents/Cat/Sunny2.jpg', 1),
(21, 'sunny', 'Assets/Residents/Cat/Sunny3.jpg', 2),
(22, 'minty', 'Assets/Residents/Bird/Minty1.jpg', 0),
(23, 'minty', 'Assets/Residents/Bird/Minty2.jpg', 1),
(24, 'minty', 'Assets/Residents/Bird/Minty3.jpg', 2),
(25, 'skye', 'Assets/Residents/Bird/Skye1.jpg', 0),
(26, 'skye', 'Assets/Residents/Bird/Skye2.jpg', 1),
(27, 'skye', 'Assets/Residents/Bird/Skye3.jpg', 2),
(28, 'honey', 'Assets/Residents/Rabbit/Honey1.png', 0),
(29, 'honey', 'Assets/Residents/Rabbit/Honey2.png', 1),
(30, 'honey', 'Assets/Residents/Rabbit/Honey3.jpg', 2),
(31, 'pebble', 'Assets/Residents/Hamster/Pebble1.jpg', 0),
(32, 'pebble', 'Assets/Residents/Hamster/Pebble2.png', 1),
(33, 'pebble', 'Assets/Residents/Hamster/Pebble3.png', 2),
(34, 'biscuit', 'Assets/Residents/Rabbit/Biscuit1.jpg', 0),
(35, 'biscuit', 'Assets/Residents/Rabbit/Biscuit2.jpg', 1),
(36, 'biscuit', 'Assets/Residents/Rabbit/Biscuit3.png', 2),
(37, 'cosmo', 'Assets/Residents/Dog/Cosmo1.jpg', 0),
(38, 'cosmo', 'Assets/Residents/Dog/Cosmo2.jpg', 1),
(39, 'cosmo', 'Assets/Residents/Dog/Cosmo3.png', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pet_traits`
--

CREATE TABLE `tbl_pet_traits` (
  `trait_id` int(11) NOT NULL,
  `pet_id` varchar(60) NOT NULL,
  `trait_type` enum('trait','like','dislike') NOT NULL,
  `trait_value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pet_traits`
--

INSERT INTO `tbl_pet_traits` (`trait_id`, `pet_id`, `trait_type`, `trait_value`) VALUES
(1, 'scout', 'trait', 'Playful'),
(2, 'scout', 'trait', 'Social'),
(3, 'scout', 'trait', 'Affectionate'),
(4, 'scout', 'trait', 'Curious'),
(5, 'scout', 'trait', 'Kid-Friendly'),
(6, 'scout', 'like', 'Soft Toys'),
(7, 'scout', 'like', 'Belly Rubs'),
(8, 'scout', 'like', 'Napping'),
(9, 'scout', 'dislike', 'Being alone'),
(10, 'scout', 'dislike', 'Sudden Loud Noises'),
(11, 'scout', 'dislike', 'Rainy Walks'),
(12, 'clover', 'trait', 'Curious'),
(13, 'clover', 'trait', 'Gentle'),
(14, 'clover', 'trait', 'Affectionate'),
(15, 'clover', 'trait', 'Quiet'),
(16, 'clover', 'trait', 'First-time Owner Friendly'),
(17, 'clover', 'like', 'Fresh Greens'),
(18, 'clover', 'like', 'Exploring in the Grass'),
(19, 'clover', 'like', 'Forehead Rubs'),
(20, 'clover', 'dislike', 'Being Picked Up Suddenly'),
(21, 'clover', 'dislike', 'Loud Noises'),
(22, 'clover', 'dislike', 'Slippery Floors'),
(23, 'cheddar', 'trait', 'Friendly'),
(24, 'cheddar', 'trait', 'Curious'),
(25, 'cheddar', 'trait', 'Gentle'),
(26, 'cheddar', 'trait', 'Independent'),
(27, 'cheddar', 'trait', 'First-time Owner Friendly'),
(28, 'cheddar', 'like', 'Sunflower Seeds'),
(29, 'cheddar', 'like', 'Nighttime Runs'),
(30, 'cheddar', 'like', 'Deep Bedding'),
(31, 'cheddar', 'dislike', 'Daytime Wake-up Calls'),
(32, 'cheddar', 'dislike', 'Roommates'),
(33, 'cheddar', 'dislike', 'Sudden Bright Lights'),
(34, 'mochi', 'trait', 'Playful'),
(35, 'mochi', 'trait', 'Social'),
(36, 'mochi', 'trait', 'Friendly'),
(37, 'mochi', 'trait', 'Affectionate'),
(38, 'mochi', 'trait', 'Curious'),
(39, 'mochi', 'like', 'Warm Laps'),
(40, 'mochi', 'like', 'Soft Blankets'),
(41, 'mochi', 'dislike', 'Closed Doors'),
(42, 'mochi', 'dislike', 'Empty Bowls'),
(43, 'pearl', 'trait', 'Quiet'),
(44, 'pearl', 'trait', 'Gentle'),
(45, 'pearl', 'trait', 'Independent'),
(46, 'pearl', 'trait', 'Affectionate'),
(47, 'pearl', 'like', 'Grooming Session'),
(48, 'pearl', 'like', 'Quiet Corners'),
(49, 'pearl', 'like', 'Being Pampered'),
(50, 'pearl', 'dislike', 'Loud Environment'),
(51, 'pearl', 'dislike', 'Sticky or Dirty Floors'),
(52, 'pearl', 'dislike', 'Being Rushed'),
(53, 'loki', 'trait', 'Protective'),
(54, 'loki', 'trait', 'Playful'),
(55, 'loki', 'trait', 'Independent'),
(56, 'loki', 'trait', 'Curious'),
(57, 'loki', 'trait', 'First-time Owner Friendly'),
(58, 'loki', 'like', 'Outdoor Adventures'),
(59, 'loki', 'like', 'Personal Space'),
(60, 'loki', 'like', 'Playful Interaction'),
(61, 'loki', 'dislike', 'Tight Spaces'),
(62, 'loki', 'dislike', 'Being Ignored'),
(63, 'loki', 'dislike', 'Loud Noises'),
(64, 'sunny', 'trait', 'Friendly'),
(65, 'sunny', 'trait', 'Affectionate'),
(66, 'sunny', 'trait', 'Social'),
(67, 'sunny', 'trait', 'Gentle'),
(68, 'sunny', 'trait', 'First-time Owner Friendly'),
(69, 'sunny', 'like', 'Window Watching'),
(70, 'sunny', 'like', 'Soft Surfaces'),
(71, 'sunny', 'like', 'Chin Scratches'),
(72, 'sunny', 'dislike', 'Cold Floors'),
(73, 'sunny', 'dislike', 'Being Ignored'),
(74, 'sunny', 'dislike', 'Empty Food Bowls'),
(75, 'minty', 'trait', 'Playful'),
(76, 'minty', 'trait', 'Curious'),
(77, 'minty', 'trait', 'Social'),
(78, 'minty', 'trait', 'Friendly'),
(79, 'minty', 'trait', 'Affectionate'),
(80, 'minty', 'like', 'Mirror Toys'),
(81, 'minty', 'like', 'Gentle Head Scratches'),
(82, 'minty', 'like', 'Morning Chirping Sessions'),
(83, 'minty', 'dislike', 'Sudden Darkness'),
(84, 'minty', 'dislike', 'Loud Clapping Sounds'),
(85, 'minty', 'dislike', 'Dirty Water Bowls'),
(86, 'skye', 'trait', 'Social'),
(87, 'skye', 'trait', 'Playful'),
(88, 'skye', 'trait', 'Curious'),
(89, 'skye', 'trait', 'Affectionate'),
(90, 'skye', 'trait', 'Friendly'),
(91, 'skye', 'like', 'Window Perches'),
(92, 'skye', 'like', 'Whistling Songs'),
(93, 'skye', 'like', 'Millet Treats'),
(94, 'skye', 'dislike', 'Being Alone Too Long'),
(95, 'skye', 'dislike', 'Sudden Movements'),
(96, 'skye', 'dislike', 'Dark Rooms'),
(97, 'honey', 'trait', 'Social'),
(98, 'honey', 'trait', 'Curious'),
(99, 'honey', 'trait', 'Playful'),
(100, 'honey', 'trait', 'Friendly'),
(101, 'honey', 'trait', 'Energetic'),
(102, 'honey', 'like', 'Standing Tall'),
(103, 'honey', 'like', 'Social Time'),
(104, 'honey', 'like', 'Soft Toes'),
(105, 'honey', 'dislike', 'Loud, Sudden Noises'),
(106, 'honey', 'dislike', 'Being Picked Up'),
(107, 'honey', 'dislike', 'Strong Scents'),
(108, 'pebble', 'trait', 'Energetic'),
(109, 'pebble', 'trait', 'Curious'),
(110, 'pebble', 'trait', 'Playful'),
(111, 'pebble', 'trait', 'Independent'),
(112, 'pebble', 'like', 'Window Watching'),
(113, 'pebble', 'like', 'Soft Surfaces'),
(114, 'pebble', 'like', 'Chin Scratches'),
(115, 'pebble', 'dislike', 'Cold Floors'),
(116, 'pebble', 'dislike', 'Being Ignored'),
(117, 'pebble', 'dislike', 'Empty Food Bowls'),
(118, 'biscuit', 'trait', 'Energetic'),
(119, 'biscuit', 'trait', 'Playful'),
(120, 'biscuit', 'trait', 'Curious'),
(121, 'biscuit', 'trait', 'Independent'),
(122, 'biscuit', 'like', 'Chin Resting'),
(123, 'biscuit', 'like', 'Gentle Nuzzles'),
(124, 'biscuit', 'like', 'Peeking Around Corners'),
(125, 'biscuit', 'dislike', 'Fast Hand Movements'),
(126, 'biscuit', 'dislike', 'Being Overlooked'),
(127, 'biscuit', 'dislike', 'Slippery Floors'),
(128, 'cosmo', 'trait', 'Energetic'),
(129, 'cosmo', 'trait', 'Playful'),
(130, 'cosmo', 'trait', 'Curious'),
(131, 'cosmo', 'trait', 'Independent'),
(132, 'cosmo', 'like', 'Chin Resting'),
(133, 'cosmo', 'like', 'Gentle Nuzzles'),
(134, 'cosmo', 'like', 'Peeking Around Corners'),
(135, 'cosmo', 'dislike', 'Fast Hand Movements'),
(136, 'cosmo', 'dislike', 'Being Overlooked'),
(137, 'cosmo', 'dislike', 'Slippery Floors');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_products`
--

CREATE TABLE `tbl_products` (
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `category` varchar(60) DEFAULT NULL,
  `pet_type` varchar(60) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `life_stage` varchar(60) DEFAULT NULL,
  `weight_size` varchar(60) DEFAULT NULL,
  `food_form` varchar(60) DEFAULT NULL,
  `storage_type` varchar(60) DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 5.00,
  `review_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `flavors` text DEFAULT NULL,
  `specs` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `guaranteed_analysis` text DEFAULT NULL,
  `feeding_guide` text DEFAULT NULL,
  `materials` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  `use_guide` text DEFAULT NULL,
  `whats_inside` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_products`
--

INSERT INTO `tbl_products` (`product_id`, `image_path`, `title`, `subtitle`, `description`, `full_description`, `price`, `category`, `pet_type`, `brand`, `life_stage`, `weight_size`, `food_form`, `storage_type`, `origin`, `rating`, `review_count`, `is_active`, `created_at`, `flavors`, `specs`, `ingredients`, `guaranteed_analysis`, `feeding_guide`, `materials`, `features`, `use_guide`, `whats_inside`) VALUES
(1, 'Assets/Supplies/Food/dog_food1.jpg', 'Pedigree Adult Complete Nutrition', 'Complete & Balanced Dry Dog Food for Adult Dogs', 'A protein-rich daily dry dog food enriched with vitamins, minerals, and omega-6 fatty acids to support healthy muscles, shiny coats, and strong immunity.', 'Pedigree Adult Complete Nutrition is specially formulated to provide adult dogs with complete daily nutrition for long-term health and vitality. Made with quality meat proteins, grains, essential vitamins, and minerals, this crunchy kibble supports lean muscle maintenance, healthy digestion, stronger immunity, and naturally cleaner teeth through chewing action. Omega-6 fatty acids and zinc help maintain healthy skin and a shiny coat, making it an excellent everyday meal for active adult dogs.', 650.00, 'Foods', 'Dog', 'Pedigree', 'Puppy', '2.8kg', 'Dry Food', 'Room Temperature', 'Philippines Supplier', 4.90, 128, 1, '2026-06-12 05:05:30', '[\"Chicken & Milk\",\"Beef & Vegetables\"]', '{\"Category\":\"Foods and Treats\",\"Brand\":\"Pedigree\",\"Pet Type\":\"Dog\",\"Life Stage\":\"Puppy\",\"Food Form\":\"Dry Food\",\"Flavor\":\"Chicken & Milk / Beef & Vegetables\",\"Weight\":\"2.8kg\",\"Storage\":\"Room Temperature\",\"Origin\":\"Philippines Supplier\"}', '[\"Cereals\",\"Poultry meal\",\"Soybean products\",\"Vitamins and minerals\",\"Vegetable oils\",\"Dried vegetables\"]', '[\"Protein (min) – 22%\",\"Fat (min) – 10%\",\"Fiber (max) – 4%\",\"Moisture (max) – 12%\"]', '[\"Small Puppies (up to 5kg): 60–120g/day\",\"Medium Puppies (5–15kg): 120–220g/day\",\"Large Puppies (15–30kg): 220–380g/day\"]', NULL, NULL, NULL, '[\"PEDIGREE Puppy Dry Dog Food 2.8kg Bag\"]'),
(2, 'Assets/Supplies/Food/hamster_food.png', 'Oxbow Essentials Hamster & Gerbil Food', 'Balanced Timothy-Based Daily Food for Hamsters & Gerbils', 'A fortified pellet food made with Timothy hay, oats, and barley to support digestion, immunity, and daily wellness for hamsters and gerbils.', 'Oxbow Essentials Hamster & Gerbil Food provides complete nutrition using premium Timothy hay combined with wholesome grains. The uniform pellets help prevent selective eating while supporting digestive health and energy needs. Fortified with antioxidants, vitamins, and minerals, it delivers balanced daily nutrition for active small pets.', 540.50, 'Foods', 'Hamster', 'Oxbow', 'Adult', '1LB', 'Dry Pellet', 'Room Temperature', 'Imported by PH pet distributors', 4.60, 473, 1, '2026-06-12 05:05:30', '[\"Oats & Barley\",\"Timothy Hay Blend\"]', '{\"Category\":\"Foods → Hamster & Gerbil Food\",\"Brand\":\"Oxbow\",\"Pet Type\":\"Hamster\",\"Life Stage\":\"Adult\",\"Food Form\":\"Dry Pellet\",\"Weight\":\"1LB\",\"Storage\":\"Room Temperature\",\"Origin\":\"Imported by PH pet distributors\"}', '[\"Timothy hay\",\"Oats\",\"Barley\",\"Soybean hulls\",\"Antioxidants\",\"Vitamins and minerals\"]', '[\"Protein (min) – 15%\",\"Fat (min) – 4%\",\"Fiber (max) – 18%\",\"Moisture (max) – 10%\"]', '[\"Hamsters: 1–2 tablespoons/day\",\"Gerbils: 2–3 tablespoons/day\",\"Supplement with vegetables and water daily\"]', NULL, NULL, NULL, '[\"1× Oxbow Essentials Hamster & Gerbil Food 1LB Bag\"]'),
(3, 'Assets/Supplies/Food/bird_food.png', 'Supreme Canary Mix', 'Premium Seed Blend for Canaries & Finches', 'A nutritious seed blend made from freshly cleaned grains and seeds to support vibrant feathers, digestion, and daily vitality.', 'Supreme Canary Mix is specially blended for canaries and finches using premium seeds and grains rich in protein, fiber, and natural oils. Naturally preserved for freshness, this wholesome mix promotes healthy digestion, vibrant plumage, and active daily living while remaining free from artificial colors and preservatives.', 320.00, 'Foods', 'Bird', 'Supreme', 'Adult', '700g', 'Seed Mix', 'Room Temperature', 'Local Supplier', 4.70, 63, 1, '2026-06-12 05:05:30', '[\"Original Seed Blend\",\"Honey Seed Blend\"]', '{\"Category\":\"Foods → Bird Food\",\"Brand\":\"Supreme\",\"Pet Type\":\"Bird\",\"Life Stage\":\"Adult\",\"Food Form\":\"Seed Mix\",\"Weight\":\"700g\",\"Storage\":\"Room Temperature\",\"Origin\":\"Local Supplier\"}', '[\"Canary grass seed\",\"Red millet\",\"Flaxseed\",\"Oats\",\"Mixed grains\"]', '[\"Protein (min) – 16%\",\"Fat (min) – 5%\",\"Fiber (max) – 8%\",\"Moisture (max) – 12%\"]', '[\"Small Birds (up to 20g): 1–2 teaspoons/day\",\"Medium Birds (20–50g): 2–4 teaspoons/day\",\"Large Birds (50–100g): 4–6 teaspoons/day\"]', NULL, NULL, NULL, '[\"1 × Supreme Canary Mix 700g Pack\"]'),
(4, 'Assets/Supplies/Food/cat_food.jpg', 'Whiskas 1+ Complete Nutrition', 'Crunchy Complete Dry Cat Food for Adult Cats', 'A delicious and balanced dry cat food with crunchy pockets and essential nutrients that support healthy coats and strong immunity.', 'Whiskas 1+ Complete Nutrition is designed for adult cats aged one year and above. This complete daily diet combines crunchy kibble with flavorful pockets filled with tasty centers cats enjoy. Rich in protein, omega-6 fatty acids, zinc, and essential vitamins, it supports healthy skin, shiny coats, digestion, and immune health while satisfying your cat\'s natural craving for crunchy textures.', 1650.00, 'Foods', 'Cat', 'Whiskas', 'Adult', '7kg', 'Dry Food', 'Room Temperature', 'Imported through Philippine distributors', 4.90, 2011, 1, '2026-06-12 05:05:30', '[\"Ocean Fish\",\"Tuna & Salmon\"]', '{\"Category\":\"Food\",\"Brand\":\"Whiskas\",\"Pet Type\":\"Cat\",\"Life Stage\":\"Adult\",\"Weight\":\"7kg\",\"Storage\":\"Room Temperature\",\"Origin\":\"Imported through Philippine distributors\"}', '[\"Fish meal\",\"Poultry meal\",\"Corn gluten\",\"Rice\",\"Animal fats\",\"Vitamins and minerals\",\"Omega fatty acids\",\"Taurine\"]', '[\"Protein (min) – 30%\",\"Fat (min) – 12%\",\"Fiber (max) – 5%\",\"Moisture (max) – 10%\"]', '[\"Small Cats (2–4kg): 35–60g/day\",\"Medium Cats (4–6kg): 60–85g/day\",\"Large Cats (6–8kg): 90–120g/day\"]', NULL, NULL, NULL, '[\"1× Whiskas 1+ Complete Nutrition 7kg Bag\"]'),
(5, 'Assets/Supplies/Food/rabbit_food.png', 'Mazuri Timothy-Based Rabbit Diet', 'High-Fiber Timothy Hay Pellet Food for Rabbits', 'A premium Timothy hay-based rabbit pellet fortified with probiotics and natural nutrients for healthy digestion and long-term wellness.', 'Mazuri Timothy-Based Rabbit Diet is formulated to support rabbits at every life stage with high-fiber nutrition and digestive support. Made using premium Timothy hay, this uniform pellet helps prevent selective feeding while promoting healthy chewing habits. Enhanced with probiotics, flaxseed, and natural vitamin E, it supports digestive balance, coat condition, and overall vitality without artificial additives.', 620.00, 'Foods', 'Rabbit', 'Mazuri', 'Adult', '1.5LB', 'Dry Pellet', 'Room Temperature', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', '[\"Timothy Hay Blend\",\"Garden Herb Blend\"]', '{\"Category\":\"Food\",\"Brand\":\"Mazuri\",\"Pet Type\":\"Rabbit\",\"Life Stage\":\"Adult\",\"Weight\":\"1.5LB\",\"Storage\":\"Room Temperature\",\"Origin\":\"Imported via Philippine suppliers\"}', '[\"Timothy hay\",\"Soybean hulls\",\"Flaxseed\",\"Wheat middlings\",\"Probiotics\",\"Vitamin E\",\"Chelated minerals\"]', '[\"Protein (min) – 14%\",\"Fat (min) – 2%\",\"Fiber (max) – 22%\",\"Moisture (max) – 12%\"]', '[\"Small Rabbits (2–4kg): 35–60g/day\",\"Medium Rabbits (4–6kg): 60–85g/day\",\"Large Rabbits (6–8kg): 90–120g/day\"]', NULL, NULL, NULL, '[\"1× Mazuri Timothy-Based Rabbit Diet 1.5LB Bag\"]'),
(6, 'Assets/Supplies/Treats/dog_treat.png', 'Pedigree Schmackos Stix Grilled Liver Flavor', 'Soft & Chewy Grilled Liver Dog Treats', 'A savory soft chew dog treat packed with meaty flavor, perfect for training rewards, bonding moments, and daily pampering.', 'Pedigree Schmackos Stix Grilled Liver Flavor delivers an irresistibly meaty taste and soft chewy texture that dogs love. These easy-to-break strips are ideal for training sessions or quick rewards throughout the day. Fortified with omega-6 fatty acids and vitamin E, they help support healthy skin, shiny coats, and natural immune defenses while keeping every bite delicious and satisfying.', 115.00, 'Treats', 'Dog', 'Pedigree', 'Adult', '70g', 'Soft Chewy Treat', 'Room Temperature', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', '[\"Grilled Liver\",\"Beef BBQ\"]', '{\"Category\":\"Treats\",\"Brand\":\"Pedigree\",\"Pet Type\":\"Dog\",\"Life Stage\":\"Adult\",\"Weight\":\"70g\",\"Storage\":\"Room Temperature\",\"Origin\":\"Imported via Philippine suppliers\"}', '[\"Meat and animal derivatives\",\"Cereals\",\"Glycerin\",\"Animal fats\",\"Vitamins and minerals\",\"Omega-6 fatty acids\",\"Flavoring agents\"]', '[\"Protein (min) – 14%\",\"Fat (min) – 2%\",\"Fiber (max) – 22%\",\"Moisture (max) – 12%\"]', '[\"Small Dogs (2–4kg): 1–2 strips/day\",\"Medium Dogs (4–6kg): 3–4 strips/day\",\"Large Dogs (6–8kg): 5–6 strips/day\"]', NULL, NULL, NULL, '[\"1× Pedigree Schmackos Stix Grilled Liver Flavor 70g Bag\"]'),
(7, 'Assets/Supplies/Treats/cat_treat.png', 'Inaba Churu Bites', 'Creamy Filled Chicken & Tuna Cat Treats', 'Dual-textured bite-sized cat treats with a soft baked exterior and creamy Churu filling made from real chicken and tuna.', 'Inaba Churu Bites deliver a delicious combination of real chicken and tuna in a creamy filling, surrounded by a soft baked exterior that cats love. These bite-sized treats are perfect for training sessions or as a tasty reward throughout the day. Fortified with omega-6 fatty acids and vitamin E, they help support healthy skin, shiny coats, and natural immune defenses.', 115.00, 'Treats', 'Cat', 'Inaba', 'Adult', '70g', 'Soft Chewy Treat', 'Room Temperature', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', '[\"Chicken & Tuna\",\"Tuna with Salmon\"]', '{\"Category\":\"Treats\",\"Brand\":\"Inaba\",\"Pet Type\":\"Cat\",\"Life Stage\":\"Adult\",\"Weight\":\"70g\",\"Storage\":\"Room Temperature\",\"Origin\":\"Imported via Philippine suppliers\"}', '[\"Farm-raised Chicken\",\"Tuna\",\"Tapioca Starch\",\"Vitamin E\",\"Green tea extract\",\"Omega-6 fatty acids\",\"Flavoring agents\"]', '[\"Protein (min) – 14%\",\"Fat (min) – 2%\",\"Fiber (max) – 22%\",\"Moisture (max) – 12%\"]', '[\"Small Cats (2–4kg): 1–2 bites/day\",\"Medium Cats (4–6kg): 3–4 bites/day\",\"Large Cats (6–8kg): 5–6 bites/day\"]', NULL, NULL, NULL, '[\"1× Inaba Churu Bites Chicken & Tuna 70g Bag\"]'),
(8, 'Assets/Supplies/Treats/rabbit_treat.png', 'No Furries Delights Original Nature\'s Bites', 'A handmade assortment of wholesome crunchy treats crafted for small pets.', 'Handcrafted treats made using organic, human-grade ingredients.', 'No Furries Delights Original Nature\'s Bites is a colorful variety pack of handcrafted treats made using organic, human-grade ingredients. Featuring naturally flavorful blends like pumpkin cilantro, strawberry, papaya, and mixed berries, these treats encourage healthy chewing and natural foraging behaviors. Free from added sugars, preservatives, and artificial ingredients, they are safely portioned for small pets.', 180.00, 'Treats', 'Rabbit', 'No Furries Delights', 'Adult', '50g', 'Crunchy Baked Treat', 'Room Temperature', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', '[\"Pumpkin Cilantro\",\"Strawberry Berry Mix\"]', '{\"Category\":\"Treats\",\"Brand\":\"No Furries Delights\",\"Pet Type\":\"Rabbit & Hamster\",\"Life Stage\":\"Adult\",\"Weight\":\"50g\",\"Storage\":\"Room Temperature\",\"Origin\":\"Imported via Philippine suppliers\"}', '[\"Organic oats\",\"Papaya\",\"Strawberry\",\"Pumpkin\",\"Cilantro\",\"Mixed berries\",\"Plant fiber\"]', '[\"Protein (min) – 10%\",\"Fat (min) – 2%\",\"Fiber (max) – 18%\",\"Moisture (max) – 10%\"]', '[\"Rabbits: 2–3 treats/day\",\"Hamsters: 1–2 treats/day\",\"Always provide fresh water\"]', NULL, NULL, NULL, '[\"1× No Furries Delights Nature\'s Bites 50g Pack\"]'),
(9, 'Assets/Supplies/Treats/hamster_treat.png', 'Vitakraft Drops with Yogurt', 'Creamy Yogurt Treat Drops for Hamsters & Small Pets', 'Delicious yogurt-based bite-sized treats made for hamsters and small pets with a crunchy chewable texture.', 'Vitakraft Drops with Yogurt are creamy, smooth treats specially made for hamsters and small rodents. Crafted with real yogurt and whey protein, these treats provide a tasty reward while encouraging natural chewing behavior. Their hard-baked texture supports dental activity and makes them ideal for bonding, training, or occasional snacking.', 150.00, 'Treats', 'Hamster', 'Vitakrafts', 'Adult', '50g', 'Yogurt Drops', 'Room Temperature', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', '[\"Yogurt Original\",\"Strawberry Yogurt\"]', '{\"Category\":\"Treats\",\"Brand\":\"Vitakrafts\",\"Pet Type\":\"Hamster\",\"Life Stage\":\"Adult\",\"Weight\":\"50g\",\"Storage\":\"Room Temperature\",\"Origin\":\"Imported via Philippine suppliers\"}', '[\"Yogurt powder\",\"Whey protein\",\"Vegetable oils\",\"Vitamin E\",\"Milk derivatives\",\"Natural flavorings\"]', '[\"Protein (min) – 6%\",\"Fat (min) – 18%\",\"Fiber (max) – 1%\",\"Moisture (max) – 8%\"]', '[\"Hamsters: 1–2 drops/day\",\"Gerbils: 2–3 drops/day\",\"Supplement with regular food and water\"]', NULL, NULL, NULL, '[\"1× Assorted Yogurt Treat Drops 50g Pack\"]'),
(10, 'Assets/Supplies/Treats/bird_treat.png', 'No Furries Delights Berrylicious Bites', 'Berry-Infused Crunchy Treats for Small Pets & Birds', 'Crunchy antioxidant-rich berry treats specially crafted for rabbits, hamsters, guinea pigs, lovebirds, and small birds.', 'No Furries Delights Berrylicious Bites combines wholesome berry flavors with crunchy textures pets love. Designed to support natural chewing and foraging behaviors, these plant-based treats are packed with antioxidants while remaining free from artificial colors, preservatives, and added sugars. Their mini-sized portions make them ideal for daily rewarding without excessive sugar intake.', 195.00, 'Treats', 'Bird', 'No Furries Delights', 'Adult', '50g', 'Crunchy Mini Treats', 'Room Temperature', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', '[\"Mixed Berry\",\"Blueberry Strawberry\"]', '{\"Category\":\"Treats\",\"Brand\":\"No Furries Delights\",\"Pet Type\":\"Bird\",\"Life Stage\":\"Adult\",\"Weight\":\"50g\",\"Storage\":\"Room Temperature\",\"Origin\":\"Imported via Philippine suppliers\"}', '[\"Oats\",\"Blueberries\",\"Strawberries\",\"Papaya\",\"Timothy hay\",\"Plant fiber\"]', '[\"Protein (min) – 11%\",\"Fat (min) – 2%\",\"Fiber (max) – 15%\",\"Moisture (max) – 10%\"]', '[\"Small Birds (up to 20g): 1–2 mini treats/day\",\"Medium Birds (20–50g): 2–4 mini treats/day\",\"Large Birds (50–100g): 4–6 mini treats/day\"]', NULL, NULL, NULL, '[\"1× Assorted Berry Crunch Treat Pieces 50g Pack\"]'),
(11, 'Assets/Supplies/Toys/toy1.jpg', '7-Piece Small Dog Toy Set', 'Fun & Engaging Toys for Small Dogs', 'A set of 7 engaging toys designed for small dogs, perfect for play and mental stimulation.', 'The 7-Piece Small Dog Toy Set includes a variety of textures and shapes to keep your small dog entertained and mentally stimulated. Made from safe, non-toxic materials, these toys are built to withstand the playful nature of small dogs. Ideal for daily playtime and training exercises.', 399.00, 'Toys', 'Dog', 'PawPlay Essentials', 'All', '450g Set', '', '', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Toys\",\"Brand\":\"PawPlay Essentials\",\"Pet Type\":\"Dog\",\"Life Stage\":\"All\",\"Weight\":\"450g Set\",\"Origin\":\"Imported via Philippine suppliers\"}', NULL, NULL, NULL, '[\"Cotton rope\",\"Plush fabric\",\"Natural rubber\",\"Polyester stuffing\",\"Non-toxic squeakers\"]', '[\"Helps reduce boredom and anxiety\",\"Supports healthy chewing behavior\",\"Gentle on puppy teeth and gums\",\"Great for fetch and tug games\",\"Lightweight and easy to clean\"]', '[\"Recommended for supervised play\",\"Suitable for small breeds under 10kg\",\"Wash plush toys weekly for hygiene\"]', '[\"2 × Rope Tug Toys\",\"2 × Plush Squeaky Toys\",\"2 × Rubber Chew Toys\",\"1 × Interactive Ball Toy\",\"1 × Training Ring Toy\"]'),
(12, 'Assets/Supplies/Toys/toy2.jpg', 'Niteangel Small Animal Activity Toy Balls', 'Natural Woven Grass Chew Balls for Rabbits & Small Pets', 'Handwoven natural grass balls designed to encourage chewing, rolling, and healthy enrichment activities for rabbits and small animals.', 'Niteangel Small Animal Activity Toy Balls provide natural entertainment and dental enrichment for rabbits, hamsters, and other small pets. Made from pet-safe woven grass materials, these lightweight chew toys help satisfy natural chewing instincts while encouraging active play and exploration. Their organic texture also helps reduce boredom and stress inside cages or habitats.', 250.00, 'Toys', 'Rabbit', 'Niteangel', 'Adult', '120g Pack', '', '', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Toys\",\"Brand\":\"Niteangel\",\"Pet Type\":\"Rabbit\",\"Life Stage\":\"Adult\",\"Weight\":\"120g Pack\",\"Origin\":\"Imported via Philippine suppliers\"}', NULL, NULL, NULL, '[\"Natural woven grass\",\"Timothy hay fiber\",\"Pet-safe plant materials\"]', '[\"Encourages natural chewing behavior\",\"Helps support dental health\",\"Lightweight and safe for tossing\",\"Reduces boredom and stress\"]', '[\"Place inside cage or playpen\",\"Replace when heavily chewed\",\"Safe for supervised nibbling\"]', '[\"6 × Woven Grass Activity Balls\"]'),
(13, 'Assets/Supplies/Toys/toy3.jpg', 'Prevue Pet Products Naturals Rope Ladder', 'Natural Wood & Rope Climbing Ladder for Birds', 'A hanging wooden ladder and rope bridge designed to keep birds active, entertained, and mentally stimulated.', 'Prevue Pet Products Naturals Rope Ladder combines natural wood branches and durable rope materials to create a fun climbing environment for birds. Perfect for lovebirds, finches, cockatiels, and small parrots, it encourages exercise, balance, and natural exploration instincts while adding enrichment to cages and aviaries.', 345.00, 'Toys', 'Bird', 'Prevue Pet Products', 'Adult', '300g', '', '', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Toys\",\"Brand\":\"Prevue Pet Products\",\"Pet Type\":\"Bird\",\"Life Stage\":\"Adult\",\"Weight\":\"300g\",\"Origin\":\"Imported via Philippine suppliers\"}', NULL, NULL, NULL, '[\"Natural wood branches\",\"Cotton rope\",\"Stainless steel hooks\"]', '[\"Encourages climbing and exercise\",\"Supports mental stimulation\",\"Cage-friendly hanging design\",\"Safe natural textures for beaks and claws\"]', '[\"Secure tightly inside cage\",\"Suitable for small to medium birds\",\"Clean monthly using bird-safe disinfectant\"]', '[\"1 × Wooden Rope Ladder Bridge\",\"2 × Hanging Hooks\"]'),
(14, 'Assets/Supplies/Toys/toy4.jpg', 'Silent Exercise Running Wheel', 'Quiet Spinning Exercise Wheel for Hamsters & Small Rodents', 'A smooth and silent running wheel designed to keep hamsters active, healthy, and entertained without noisy squeaking.', 'The Silent Exercise Running Wheel provides hamsters and small rodents with a safe and fun way to stay physically active indoors. Featuring a smooth spinning mechanism and quiet bearings, it minimizes noise while encouraging natural running behavior. Its stable base and textured running surface help improve comfort, exercise, and overall well-being for energetic small pets.', 280.00, 'Toys', 'Hamster', 'TinyTrail Pets', 'Adult', '350g', '', '', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Toys\",\"Brand\":\"TinyTrail Pets\",\"Pet Type\":\"Hamster\",\"Life Stage\":\"Adult\",\"Weight\":\"350g\",\"Origin\":\"Imported via Philippine suppliers\"}', NULL, NULL, NULL, '[\"BPA-free plastic\",\"Silent metal bearings\",\"Non-slip running track\"]', '[\"Ultra-quiet spinning design\",\"Encourages healthy daily exercise\",\"Stable anti-tip base\",\"Easy to clean and maintain\"]', '[\"Place on flat cage surface\",\"Recommended for dwarf hamsters and gerbils\",\"Wipe clean weekly\"]', '[\"1× Silent Running Wheel\",\"1× Stable Base Stand\"]'),
(15, 'Assets/Supplies/Accessories/accessories.jpg', 'Heavy-Duty Nylon Pet Leash', 'Durable and Comfortable Leash for Small Pets', 'A sturdy and reliable leash designed for small pets, providing secure control during walks and outdoor activities.', 'The Heavy-Duty Nylon Pet Leash offers a strong and comfortable grip for your pet, ensuring a safe and enjoyable experience. Made from high-quality nylon material, it is resistant to wear and tear while being gentle on your pet\'s skin. Suitable for daily walks, training, and outdoor adventures.', 249.00, 'Accessories', 'Dog & Cat', 'PawTrail Essentials', 'Adult', '180g', '', '', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Accessories\",\"Brand\":\"PawTrail Essentials\",\"Pet Type\":\"Dog & Cat\",\"Life Stage\":\"Adult\",\"Weight\":\"180g\",\"Origin\":\"Imported via Philippine suppliers\"}', NULL, NULL, NULL, '[\"Reinforced woven nylon\",\"Stainless steel swivel clasp\",\"Soft foam grip handle\",\"Durable stitching reinforcement\"]', '[\"Strong pull-resistant construction\",\"Comfortable anti-slip handle grip\",\"Rust-resistant metal hook\",\"Lightweight and easy to carry\",\"Suitable for daily walks and training\"]', '[\"Attach securely to collar or harness\",\"Recommended for supervised outdoor use\",\"Hand wash and air dry when dirty\",\"Check clasp regularly for wear and tear\"]', '[\"1× Heavy-Duty Nylon Pet Leash\",\"1× Safety Care Instruction Tag\"]'),
(16, 'Assets/Supplies/Beds/bed1.jpg', 'Soft Plush Sleeping Cushion Bed for Cats & Small Dogs', 'Durable and Comfortable Bed for Small Pets', 'A sturdy and reliable bed designed for small cats and dogs, providing a cozy and comfortable resting place.', 'The Soft Plush Sleeping Cushion Bed offers a plush and supportive surface for your pet, ensuring a restful night\'s sleep. Made from high-quality plush material, it is soft to the touch and provides excellent cushioning for cats and small dogs of all ages.', 499.00, 'Bed', 'Dog & Cat', 'CozyPaws', 'All', '650g', '', '', 'Imported via Philippine suppliers', 4.90, 2011, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Bed\",\"Brand\":\"CozyPaws\",\"Pet Type\":\"Dog & Cat\",\"Life Stage\":\"All\",\"Weight\":\"650g\",\"Origin\":\"Imported via Philippine suppliers\"}', NULL, NULL, NULL, '[\"Plush fabric\",\"PP cotton filling\",\"Soft polyester cushion\"]', '[\"Soft and cozy sleeping surface\",\"Includes matching mini pillow\",\"Lightweight and portable\",\"Comfortable for daily naps\"]', '[\"Hand wash recommended\",\"Air dry completely before reuse\",\"Keep away from sharp objects\"]', '[\"1× Pink Cushion Bed\",\"1× Matching Pillow\"]'),
(17, 'Assets/Supplies/Beds/bed2.jpg', 'Washable Bed', 'Machine-Washable Soft Pet Bed for Everyday Comfort', 'A soft and easy-to-clean pet bed designed for daily sleeping comfort with removable washable cushioning.', 'The Washable Bed provides pets with a comfortable resting space while offering easy maintenance for pet owners. Designed using soft fabric and cushioned padding, it helps create a cozy sleeping area for cats and dogs. Its washable design makes cleaning simple and convenient, helping maintain hygiene and freshness even with daily use.', 650.00, 'Bed', 'Dog & Cat', 'PetNest Essentials', 'Young, Adult & Senior', '800g', '', 'Room temperature', 'Philippine pet supplier', 4.70, 602, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Bed\",\"Brand\":\"PetNest Essentials\",\"Pet Type\":\"Dog & Cat\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"800g\",\"Storage\":\"Room temperature\",\"Origin\":\"Philippine pet supplier\"}', NULL, NULL, NULL, '[\"Soft fabric cover\",\"PP cotton filling\",\"Non-slip base lining\"]', '[\"Machine-washable design\",\"Thick soft cushioning\",\"Comfortable raised edges\",\"Lightweight and durable\"]', '[\"Remove loose fur before washing\",\"Machine wash on gentle cycle\",\"Air dry recommended\"]', '[\"1× Washable Pet Bed\"]'),
(18, 'Assets/Supplies/Beds/bed3.jpg', 'Round Bed Sleeping Sofa', 'Round Plush Sofa-Style Bed for Pets', 'A round sofa-inspired pet bed with soft raised edges that provide warmth, comfort, and security during sleep.', 'The Round Bed Sleeping Sofa is designed to give pets a calming and comfortable sleeping experience. Its circular shape and raised cushioned sides help support natural curling positions while creating a sense of safety and warmth. Made with soft plush fabric and thick padding, it is ideal for cats and dogs who enjoy cozy lounging spaces.', 799.00, 'Bed', 'Dog & Cat', 'SnuggleTails', 'Young, Adult & Senior', '900g', '', 'Room temperature', 'Imported through PH pet distributors', 4.90, 744, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Bed\",\"Brand\":\"SnuggleTails\",\"Pet Type\":\"Dog & Cat\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"900g\",\"Storage\":\"Room temperature\",\"Origin\":\"Imported through PH pet distributors\"}', NULL, NULL, NULL, '[\"Plush faux fur fabric\",\"PP cotton filling\",\"Anti-slip bottom fabric\"]', '[\"Raised edges for head support\",\"Soft calming plush texture\",\"Anti-slip bottom layer\",\"Ideal for curling and lounging\"]', '[\"Spot clean when necessary\",\"Hand wash recommended\",\"Dry fully before use\"]', '[\"1× Round Bed Sleeping Sofa\"]'),
(19, 'Assets/Supplies/Health/shampoo1.jpg', 'Earthbath Fragrance-Free Oatmeal & Aloe Pet Shampoo', 'Gentle Oatmeal & Aloe Shampoo for Sensitive Skin Pets', 'A soap-free oatmeal shampoo specially made to gently clean, soothe, and moisturize sensitive skin for cats and dogs.', 'Earthbath Fragrance-Free Oatmeal & Aloe Pet Shampoo is formulated for pets with dry, itchy, or sensitive skin. Made with natural oatmeal and aloe vera, this ultra-gentle shampoo helps relieve irritation while moisturizing the coat and skin during bath time. Its soap-free formula cleans effectively without stripping natural oils, making it suitable for regular use on both cats and dogs.', 895.00, 'Health', 'Dog & Cat', 'Earthbath', 'Puppy, Kitten, Adult & Senior', '473mL', '', 'Room temperature', 'USA imported through PH pet suppliers', 4.90, 1126, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Health\",\"Brand\":\"Earthbath\",\"Pet Type\":\"Dog & Cat\",\"Life Stage\":\"Puppy, Kitten, Adult & Senior\",\"Weight\":\"473mL\",\"Storage\":\"Room temperature\",\"Origin\":\"USA imported through PH pet suppliers\"}', '[\"Purified water\",\"Colloidal oatmeal\",\"Organic aloe vera\",\"Coconut-based cleansers\",\"Vitamin E\",\"Natural preservatives\"]', NULL, NULL, NULL, '[\"Soap-free gentle cleansing\",\"Helps soothe dry itchy skin\",\"Safe for sensitive pets\",\"Moisturizes coat and skin\",\"Suitable for regular bathing\"]', '[\"Wet pet\'s coat thoroughly\",\"Apply shampoo evenly and massage gently\",\"Rinse completely with clean water\",\"Avoid contact with eyes\"]', '[\"1× Earthbath Fragrance-Free Oatmeal & Aloe Pet Shampoo Bottle\"]'),
(20, 'Assets/Supplies/Health/vitamin.jpg', 'Virbac Nutri-Plus Gel', 'High-Calorie Nutritional Supplement Gel for Dogs', 'A vitamin-rich energy supplement gel designed to support puppies, active dogs, and pets recovering from illness or surgery.', 'Virbac Nutri-Plus Gel is a highly palatable nutritional supplement packed with essential vitamins, minerals, fats, and carbohydrates to provide immediate energy support for dogs. Commonly used for growing puppies, highly active pets, underweight dogs, or recovering animals, this easy-to-administer gel helps maintain healthy weight, energy levels, and overall vitality.', 850.00, 'Health', 'Dog', 'Virbac', 'Puppy, Adult & Senior', '120g', '', 'Room temperature', 'Imported through PH veterinary suppliers', 4.90, 638, 1, '2026-06-12 05:05:30', '[\"Malt Flavor\",\"Liver Flavor\"]', '{\"Category\":\"Health\",\"Brand\":\"Virbac\",\"Pet Type\":\"Dog\",\"Life Stage\":\"Puppy, Adult & Senior\",\"Weight\":\"120g\",\"Storage\":\"Room temperature\",\"Origin\":\"Imported through PH veterinary suppliers\"}', '[\"Vitamins A, D3, E\",\"Calcium\",\"Iron\",\"Magnesium\",\"Fish oil\",\"Easily digestible fats\",\"Carbohydrates\"]', '[\"Protein (min) – 5%\",\"Fat (min) – 35%\",\"Fiber (max) – 1%\",\"Moisture (max) – 15%\"]', '[\"Puppies: 1 teaspoon daily\",\"Adult Dogs: 1–2 teaspoons daily\",\"Recovery Support: Follow veterinarian guidance\"]', NULL, '[\"High-calorie nutritional support\",\"Palatable energy gel\",\"Supports recovery and weight maintenance\",\"Suitable for puppies and active dogs\"]', '[\"Use directly or offered with a syringe\",\"Follow feeding guidance for weight and activity\",\"Store at room temperature\"]', '[\"1× Virbac Nutri-Plus Gel Tube\"]'),
(21, 'Assets/Supplies/Health/vitamin1.jpg', 'GimCat Multi-Vitamin Paste', 'Immune Support Vitamin Paste for Cats', 'A tasty multivitamin paste enriched with taurine and essential nutrients to support immunity, coat health, and overall wellness.', 'GimCat Multi-Vitamin Paste contains a balanced blend of 12 essential vitamins, beta-glucan, healthy oils, and taurine to help support a cat\'s immune system and daily vitality. Its smooth, appetizing texture makes feeding easy while promoting healthy vision, heart function, skin, and a glossy coat.', 420.00, 'Health', 'Cat', 'GimCat', 'Kitten, Adult & Senior', '50g', '', 'Room temperature', 'Germany imported through PH pet stores', 4.80, 914, 1, '2026-06-12 05:05:30', '[\"Cheese Flavor\",\"Malt Flavor\"]', '{\"Category\":\"Health\",\"Brand\":\"GimCat\",\"Pet Type\":\"Cat\",\"Life Stage\":\"Kitten, Adult & Senior\",\"Weight\":\"50g\",\"Storage\":\"Room temperature\",\"Origin\":\"Germany imported through PH pet stores\"}', '[\"Taurine\",\"Vitamin complex\",\"Beta-glucan\",\"Fish oils\",\"Milk derivatives\",\"Yeast extracts\"]', '[\"Protein (min) – 6%\",\"Fat (min) – 30%\",\"Fiber (max) – 2%\",\"Moisture (max) – 12%\"]', '[\"Kittens: 3–4cm paste daily\",\"Adult Cats: 5–6cm paste daily\",\"Can be served directly or mixed with food\"]', NULL, '[\"Immune support formula\",\"Taurine-enriched for eye and heart health\",\"Smooth, appetizing paste\",\"Supports coat and overall wellness\"]', '[\"Serve directly from the tube\",\"Mix into food for picky eaters\",\"Use daily as directed\"]', '[\"1× GimCat Multi-Vitamin Paste Tube\"]'),
(22, 'Assets/Supplies/Health/vitamin2.jpg', 'Oxbow Natural Science Multi-Vitamin', 'High-Fiber Vitamin Supplement Tabs for Rabbits & Small Herbivores', 'Veterinarian-formulated Timothy hay supplement tabs that support overall health and daily wellness for rabbits and small herbivores.', 'Oxbow Natural Science Multi-Vitamin is a premium hay-based supplement made to support rabbits and other herbivorous small animals. Formulated using Timothy hay and stabilized vitamins, these easy-to-feed supplement tabs help maintain balanced nutrition while avoiding artificial preservatives or hidden sugars that may upset sensitive digestive systems.', 560.00, 'Health', 'Rabbit & Small Herbivores', 'Oxbow', 'Young, Adult & Senior', '120g', '', 'Room temperature', 'Imported through PH pet distributors', 4.90, 488, 1, '2026-06-12 05:05:30', '[\"Timothy Hay Blend\",\"Garden Veggie Blend\"]', '{\"Category\":\"Health\",\"Brand\":\"Oxbow\",\"Pet Type\":\"Rabbit & Small Herbivores\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"120g\",\"Storage\":\"Room temperature\",\"Origin\":\"Imported through PH pet distributors\"}', '[\"Timothy hay\",\"Stabilized vitamins\",\"Barley flour\",\"Oat groats\",\"Flaxseed\"]', '[\"Protein (min) – 12%\",\"Fat (min) – 4%\",\"Fiber (max) – 20%\",\"Moisture (max) – 10%\"]', '[\"Rabbits: 1 tab daily per 2kg body weight\",\"Guinea Pigs: ½ tab daily\",\"Always provide fresh hay and water\"]', NULL, '[\"Hay-based vitamin supplement\",\"Formulated for herbivores\",\"Supports digestion and daily nutrition\",\"No artificial preservatives\"]', '[\"Feed as directed by weight\",\"Offer with fresh hay and water\",\"Store in a cool, dry place\"]', '[\"60 × Oxbow Supplement Tabs\"]'),
(23, 'Assets/Supplies/Health/vitamin3.jpg', 'Oasis Vita-Drops for Hamsters', 'Liquid Multivitamin Supplement for Hamsters', 'A concentrated liquid multivitamin formulated to support eye health, coat condition, and daily vitality in hamsters and small rodents.', 'Oasis Vita-Drops for Hamsters is a high-potency liquid vitamin supplement specially designed for the nutritional needs of hamsters and pocket pets. Easy to mix into drinking water or apply onto food, it provides essential daily vitamins to help maintain healthy metabolism, vibrant coats, and overall wellness.', 320.00, 'Health', 'Hamster & Small Rodents', 'Oasis', 'Young, Adult & Senior', '57mL', '', 'Room temperature', 'Imported via PH pet suppliers', 4.70, 271, 1, '2026-06-12 05:05:30', '[\"Original\",\"Fruit Blend\"]', '{\"Category\":\"Health\",\"Brand\":\"Oasis\",\"Pet Type\":\"Hamster & Small Rodents\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"57mL\",\"Storage\":\"Room temperature\",\"Origin\":\"Imported via PH pet suppliers\"}', '[\"Vitamins A, D3, E\",\"Vitamin B complex\",\"Calcium\",\"Amino acids\",\"Purified water\"]', '[\"Protein (min) – 0%\",\"Fat (min) – 0%\",\"Fiber (max) – 0%\",\"Moisture (max) – 95%\"]', '[\"Add 2–3 drops to water daily\",\"Replace drinking water every 24 hours\",\"Shake well before use\"]', NULL, '[\"Liquid multivitamin formula\",\"Supports coat and eye health\",\"Easy to add to drinking water or food\",\"Ideal for hamsters and small rodents\"]', '[\"Add drops to water or food\",\"Use daily for best results\",\"Keep bottle tightly closed when not in use\"]', '[\"1× Oasis Vita-Drops Bottle\"]'),
(24, 'Assets/Supplies/Health/vitamin4.jpg', 'Nekton S Multi-Vitamin for Birds', 'Water-Soluble Multivitamin Powder for Birds', 'A trusted multivitamin powder with amino acids and trace minerals to support feather health, metabolism, and overall wellness in birds.', 'Nekton S Multi-Vitamin for Birds is a premium water-soluble supplement designed for caged birds including lovebirds, finches, and parrots. Enriched with amino acids, trace elements, and essential vitamins, it helps prevent nutritional deficiencies while promoting strong plumage, healthy metabolism, and daily vitality.', 580.00, 'Health', 'Bird', 'Nekton', 'Young, Adult & Senior', '35g', '', 'Room temperature', 'Germany imported through PH bird suppliers', 4.80, 346, 1, '2026-06-12 05:05:30', '[\"Original Formula\",\"Honey Blend\"]', '{\"Category\":\"Health\",\"Brand\":\"Nekton\",\"Pet Type\":\"Bird\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"35g\",\"Storage\":\"Room temperature\",\"Origin\":\"Germany imported through PH bird suppliers\"}', '[\"Amino acids\",\"Trace minerals\",\"Vitamins A, D3, E\",\"Calcium\",\"Magnesium\"]', '[\"Protein (min) – 8%\",\"Fat (min) – 1%\",\"Fiber (max) – 0%\",\"Moisture (max) – 6%\"]', '[\"Mix small scoop into drinking water daily\",\"Replace water every 24 hours\",\"Store tightly sealed after use\"]', NULL, '[\"Water-soluble vitamin powder\",\"Supports feather health and metabolism\",\"Easy to mix with drinking water\",\"Designed for birds and small aviary pets\"]', '[\"Mix into water daily\",\"Replace water every 24 hours\",\"Keep container sealed and dry\"]', '[\"1× Nekton-S Multi-Vitamin Powder Container\"]'),
(25, 'Assets/Supplies/Travel/travel1.jpg', 'Multi-Level Small Animal Hamster Cage', 'Spacious Multi-Level Habitat for Hamsters & Small Pets', 'A roomy wire habitat with platforms, ramps, and a hideout area designed for active small pets to explore, climb, and rest comfortably.', 'The Multi-Level Small Animal Hamster Cage provides an engaging living environment for hamsters and other small pets. Featuring elevated platforms, connecting ramps, and a cozy hideout house, it encourages natural climbing, exploration, and exercise behaviors while still providing a secure resting area. Its wire ventilation design helps maintain airflow and visibility for everyday pet care.', 1850.00, 'Travel', 'Hamster', 'TinyTrail Habitats', 'Young & Adult', '2.8kg', '', 'Room temperature', 'Philippine pet supplier', 4.80, 512, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Travel\",\"Brand\":\"TinyTrail Habitats\",\"Pet Type\":\"Hamster & Small Rodents\",\"Life Stage\":\"Young & Adult\",\"Weight\":\"2.8kg\",\"Storage\":\"Room temperature\",\"Origin\":\"Philippine pet supplier\"}', NULL, NULL, NULL, '[\"Powder-coated wire\",\"BPA-free plastic base\",\"Plastic ramps and platforms\"]', '[\"Multi-level climbing design\",\"Includes hideout shelter\",\"Good airflow and visibility\",\"Easy-access front doors\"]', '[\"Clean habitat weekly\",\"Secure ramps before use\",\"Place bedding on base layer\"]', '[\"1 × Wire Cage\",\"2 × Platforms\",\"2 × Ramps\",\"1 × Hideout House\"]'),
(26, 'Assets/Supplies/Travel/travel2.jpg', 'Heavy-Duty Hard Shell Pet Carrier', 'Secure Hard Shell Travel Carrier for Pets', 'A durable ventilated travel crate designed for safely transporting dogs, cats, and small pets during trips or vet visits.', 'The Heavy-Duty Hard Shell Pet Carrier offers secure and comfortable pet transportation with its sturdy shell construction and ventilated design. Featuring a strong wire door, top carry handle, and airflow openings, it helps pets stay safe and comfortable while traveling. Ideal for vet visits, road trips, or short-distance transport.', 1650.00, 'Travel', 'Dog & Cat', 'SafePaws Travel', 'Young, Adult & Senior', '2.3kg', '', 'Room temperature', 'Philippine pet supplier', 4.90, 833, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Travel\",\"Brand\":\"SafePaws Travel\",\"Pet Type\":\"Dog, Cat & Small Pets\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"2.3kg\",\"Storage\":\"Room temperature\",\"Origin\":\"Philippine pet supplier\"}', NULL, NULL, NULL, '[\"Durable hard plastic shell\",\"Steel wire door\",\"Ventilation slots\",\"Carry handle\"]', '[\"Secure locking door\",\"Ventilated airflow design\",\"Durable travel construction\",\"Comfortable for short trips\"]', '[\"Place soft mat inside carrier\",\"Ensure proper latch locking\",\"Clean after travel use\"]', '[\"1 × Hard Shell Pet Carrier\"]'),
(27, 'Assets/Supplies/Travel/travel3.jpg', 'Extra-Large Small Animal Habitat Crate', 'Wide Spacious Habitat Crate for Rabbits & Guinea Pigs', 'A low-profile wire habitat with ramps and platforms that provides rabbits and guinea pigs with extra room for movement and comfort.', 'The Extra-Large Small Animal Habitat Crate is designed to provide rabbits and guinea pigs with a spacious environment for resting, stretching, and movement. Its wide floor area and elevated platform help create a more enriching habitat while maintaining proper airflow and accessibility for feeding and cleaning.', 2950.00, 'Travel', 'Rabbit', 'CozyCritter Homes', 'Young, Adult & Senior', '4.5kg', '', 'Room temperature', 'Imported through PH distributors', 4.80, 406, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Travel\",\"Brand\":\"CozyCritter Homes\",\"Pet Type\":\"Rabbit & Guinea Pig\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"4.5kg\",\"Storage\":\"Room temperature\",\"Origin\":\"Imported through PH distributors\"}', NULL, NULL, NULL, '[\"Powder-coated metal wire\",\"Plastic tray base\",\"Ramp and platform accessories\"]', '[\"Wide low-profile layout\",\"Elevated resting platform\",\"Good ventilation design\",\"Easy-access doors for cleaning\"]', '[\"Place absorbent bedding on base\",\"Spot-clean daily\",\"Secure ramp attachments regularly\"]', '[\"1 × Habitat Crate\",\"1 × Elevated Platform\",\"1 × Ramp\"]'),
(28, 'Assets/Supplies/Travel/travel4.jpg', 'Classic Dome Top Metal Bird Cage', 'Traditional Hanging Bird Cage for Small Birds', 'A dome-top metal bird cage with hanging loop and spacious interior for lovebirds, finches, and other small birds.', 'The Classic Dome Top Metal Bird Cage combines traditional design with practical functionality for small pet birds. Its rounded dome top provides additional vertical space for climbing and movement while the hanging loop allows flexible placement indoors. The ventilated wire design supports airflow and visibility for comfortable bird housing.', 1250.00, 'Travel', 'Bird', 'FeatherNest', 'Young, Adult & Senior', '2kg', '', 'Room temperature', 'Philippine pet supplier', 4.70, 355, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Travel\",\"Brand\":\"FeatherNest\",\"Pet Type\":\"Bird\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"2kg\",\"Storage\":\"Room temperature\",\"Origin\":\"Philippine pet supplier\"}', NULL, NULL, NULL, '[\"Powder-coated metal wire\",\"Plastic tray base\",\"Metal hanging hook\"]', '[\"Dome-top spacious design\",\"Hanging loop for placement\",\"Removable bottom tray\",\"Easy cage access doors\"]', '[\"Clean tray regularly\",\"Position away from direct sunlight\",\"Provide perches and food bowls\"]', '[\"1 × Dome Top Bird Cage\",\"2 × Plastic Feed Cups\",\"1 × Wooden Perch\"]'),
(29, 'Assets/Supplies/Travel/bag1.jpg', 'Astronaut Bubble Pet Carrier Backpack', 'Transparent Bubble Backpack Carrier for Cats & Small Dogs', 'A stylish hands-free travel backpack featuring a clear viewing window and ventilation holes for comfortable pet travel.', 'The Astronaut Bubble Pet Carrier Backpack combines comfort, safety, and modern style for traveling with cats and small dogs. Its transparent panoramic window allows pets to observe their surroundings while multiple ventilation holes help maintain airflow during travel. Adjustable shoulder straps and lightweight materials make carrying easier for everyday outings or vet visits.', 1450.00, 'Travel', 'Dog & Cat', 'PetVoyage', 'Young, Adult & Senior', '1.2kg', '', 'Room temperature', 'Imported through PH pet distributors', 4.80, 1092, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Travel\",\"Brand\":\"PetVoyage\",\"Pet Type\":\"Cat & Small Dog\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"1.2kg\",\"Storage\":\"Room temperature\",\"Origin\":\"Imported through PH pet distributors\"}', NULL, NULL, NULL, '[\"Hard transparent acrylic window\",\"Oxford fabric\",\"Mesh ventilation panels\",\"Adjustable shoulder straps\"]', '[\"Panoramic bubble viewing window\",\"Hands-free backpack design\",\"Breathable ventilation holes\",\"Lightweight and portable\"]', '[\"Do not overload carrier\",\"Allow pets to adjust gradually\",\"Wipe acrylic window regularly\"]', '[\"1 × Bubble Pet Carrier Backpack\"]'),
(30, 'Assets/Supplies/Travel/bag2.jpg', 'Soft-Sided Mesh Pet Travel Duffle', 'Airline-Approved Soft Pet Carrier Duffle', 'A lightweight fabric pet carrier with breathable mesh windows and comfortable straps for portable pet transport.', 'The Soft-Sided Mesh Pet Travel Duffle is designed for convenient and comfortable pet transportation during travel or vet visits. Made with breathable mesh panels for airflow and visibility, it features both shoulder straps and carry handles for flexible carrying options. Its lightweight collapsible design also allows easier storage when not in use.', 1180.00, 'Travel', 'Dog & Cat', 'CozyCarry Pets', 'Young, Adult & Senior', '950g', '', 'Room temperature', 'Philippine pet supplier', 4.70, 621, 1, '2026-06-12 05:05:30', NULL, '{\"Category\":\"Travel\",\"Brand\":\"CozyCarry Pets\",\"Pet Type\":\"Cat & Small Dog\",\"Life Stage\":\"Young, Adult & Senior\",\"Weight\":\"950g\",\"Storage\":\"Room temperature\",\"Origin\":\"Philippine pet supplier\"}', NULL, NULL, NULL, '[\"Oxford fabric\",\"Breathable mesh panels\",\"Zipper closures\",\"Shoulder strap padding\"]', '[\"Airline-approved size\",\"Lightweight collapsible structure\",\"Breathable mesh ventilation\",\"Portable carry handles and strap\"]', '[\"Place soft mat inside for comfort\",\"Ensure zippers are fully closed\",\"Hand wash fabric when necessary\"]', '[\"1 × Soft-Sided Mesh Travel Duffle\",\"1 × Adjustable Shoulder Strap\"]');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product_gallery`
--

CREATE TABLE `tbl_product_gallery` (
  `gallery_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_product_gallery`
--

INSERT INTO `tbl_product_gallery` (`gallery_id`, `product_id`, `image_path`, `sort_order`) VALUES
(1, 1, 'Assets/Supplies/Food/dog_food1.jpg', 0),
(2, 1, 'Assets/Supplies/Food/dog_food2.jpg', 1),
(3, 1, 'Assets/Supplies/Food/dog_food3.jpg', 2),
(4, 2, 'Assets/Supplies/Food/hamster_food.png', 0),
(5, 2, 'Assets/Supplies/Food/hamster_food1.png', 1),
(6, 2, 'Assets/Supplies/Food/hamster_food2.png', 2),
(7, 3, 'Assets/Supplies/Food/bird_food.png', 0),
(8, 3, 'Assets/Supplies/Food/bird_food1.png', 1),
(9, 4, 'Assets/Supplies/Food/cat_food.jpg', 0),
(10, 4, 'Assets/Supplies/Food/cat_food1.jpg', 1),
(11, 4, 'Assets/Supplies/Food/cat_food2.jpg', 2),
(12, 5, 'Assets/Supplies/Food/rabbit_food.png', 0),
(13, 5, 'Assets/Supplies/Food/rabbit_food1.png', 1),
(14, 5, 'Assets/Supplies/Food/rabbit_food2.png', 2),
(15, 6, 'Assets/Supplies/Treats/dog_treat.png', 0),
(16, 6, 'Assets/Supplies/Treats/dog_treat1.png', 1),
(17, 7, 'Assets/Supplies/Treats/cat_treat.png', 0),
(18, 7, 'Assets/Supplies/Treats/cat_treat1.png', 1),
(19, 7, 'Assets/Supplies/Treats/cat_treat2.png', 2),
(20, 8, 'Assets/Supplies/Treats/rabbit_treat.png', 0),
(21, 8, 'Assets/Supplies/Treats/rabbit_treat1.png', 1),
(22, 9, 'Assets/Supplies/Treats/hamster_treat.png', 0),
(23, 9, 'Assets/Supplies/Treats/hamster_treat1.png', 1),
(24, 9, 'Assets/Supplies/Treats/hamster_treat2.png', 2),
(25, 10, 'Assets/Supplies/Treats/bird_treat.png', 0),
(26, 10, 'Assets/Supplies/Treats/bird_treat1.png', 1),
(27, 11, 'Assets/Supplies/Toys/toy1.jpg', 0),
(28, 12, 'Assets/Supplies/Toys/toy2.jpg', 0),
(29, 13, 'Assets/Supplies/Toys/toy3.jpg', 0),
(30, 14, 'Assets/Supplies/Toys/toy4.jpg', 0),
(31, 15, 'Assets/Supplies/Accessories/accessories.jpg', 0),
(32, 16, 'Assets/Supplies/Beds/bed1.jpg', 0),
(33, 17, 'Assets/Supplies/Beds/bed2.jpg', 0),
(34, 18, 'Assets/Supplies/Beds/bed3.jpg', 0),
(35, 19, 'Assets/Supplies/Health/shampoo1.jpg', 0),
(36, 20, 'Assets/Supplies/Health/vitamin.jpg', 0),
(37, 21, 'Assets/Supplies/Health/vitamin1.jpg', 0),
(38, 22, 'Assets/Supplies/Health/vitamin2.jpg', 0),
(39, 23, 'Assets/Supplies/Health/vitamin3.jpg', 0),
(40, 24, 'Assets/Supplies/Health/vitamin4.jpg', 0),
(41, 25, 'Assets/Supplies/Travel/travel1.jpg', 0),
(42, 26, 'Assets/Supplies/Travel/travel2.jpg', 0),
(43, 27, 'Assets/Supplies/Travel/travel3.jpg', 0),
(44, 28, 'Assets/Supplies/Travel/travel4.jpg', 0),
(45, 29, 'Assets/Supplies/Travel/bag1.jpg', 0),
(46, 30, 'Assets/Supplies/Travel/bag2.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sessions`
--

CREATE TABLE `tbl_sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_sessions`
--

INSERT INTO `tbl_sessions` (`session_id`, `user_id`, `ip_address`, `expires_at`) VALUES
('46kuovs5jrl8rg72vndsl08rgd', 1, '::1', '2026-06-10 10:51:43'),
('55aen2284m7kmrefq97nv1p1ic', 1, '::1', '2026-06-04 11:57:44'),
('5c4a56odkf8p0ci49ov2la4at8', 2, '::1', '2026-06-09 06:29:22'),
('5mga9lpqq0hsf0p0j3ngt7vm7o', 1, '::1', '2026-06-10 18:41:07'),
('7nm2r5r5mv7r38bc5jf4jpli96', 1, '::1', '2026-06-12 07:55:05'),
('8uuf1oocg0j5vlncadfempf92h', 1, '::1', '2026-06-09 06:19:05'),
('a1ogmtb21pnl37ef8evfnbn8rf', 1, '::1', '2026-06-12 07:06:47'),
('b33n7h4tjmi927jl7c6hhcaf1v', 1, '::1', '2026-06-04 12:16:06'),
('b6g4qklagrkm7j74bg91aarqna', 1, '::1', '2026-06-12 07:39:22'),
('bmtvr77aa5d9k72jt36sgd1ojt', 1, '::1', '2026-06-04 12:20:39'),
('cs40r3nvnsfuor8l9crcrujmcq', 1, '::1', '2026-06-04 12:14:54'),
('dl1n79ec6e9335vu1phspmp1st', 1, '::1', '2026-06-12 07:16:39'),
('esvnnfh9n8gc6t9ac4vmparqd1', 2, '::1', '2026-06-09 06:36:40'),
('fht6hrv4tsp4i1i9q6gei8nq9r', 1, '::1', '2026-06-09 06:34:23'),
('h35t9a5m9ke6e66jrk38fc8of1', 2, '::1', '2026-06-10 01:44:11'),
('ha9nkbvokrjs2a2753lkn01vhf', 1, '::1', '2026-06-04 12:27:43'),
('i45oe41bpctjv8hdv8i97ohd20', 1, '::1', '2026-06-04 11:56:49'),
('j23sca51nl51uegq3as2423r5m', 1, '::1', '2026-06-12 07:53:25'),
('jj0v63p2g4tpenpj8ba6u6867a', 1, '::1', '2026-06-09 06:36:13'),
('km4fvrdejn56sl8rubu3uagmhh', 1, '::1', '2026-06-12 07:48:36'),
('pdjagjunpsh9r6spd0gnn59f2j', 1, '::1', '2026-06-09 06:05:08'),
('q7ju0q93sd32pev8bcsvpfn8sc', 1, '::1', '2026-06-12 07:43:40'),
('qiudjlnug2n3ct3lnatf4ceftq', 1, '::1', '2026-06-12 07:37:42'),
('r8n9bh2hkn23vtmpsbel0a0evp', 1, '::1', '2026-06-12 07:31:40'),
('rb1hq8n5n1csrn331hfu2lrn2k', 1, '::1', '2026-06-12 06:54:42'),
('s3t6bjnugg2e2je0rr33vdq764', 1, '::1', '2026-06-10 18:35:59'),
('srrliopjuh0gjuj68c3tbhfuma', 1, '::1', '2026-06-04 12:00:06'),
('u1avsfgcgcjt29d0aebs3bgccf', 1, '::1', '2026-06-12 08:00:15'),
('u1cfcjlqcq3pv49i37q2ojaqkd', 2, '::1', '2026-06-09 06:09:11'),
('ut83h4agvb1282q8h5posdokbu', 1, '::1', '2026-06-04 12:12:09'),
('v5imuh54biaed15lmr15itd8o0', 1, '::1', '2026-06-09 06:38:24'),
('va8c4237u289vng3mbnbn4sqpk', 1, '::1', '2026-06-04 12:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'User',
  `is_active` tinyint(1) DEFAULT 1,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `full_name`, `email`, `password_hash`, `role`, `is_active`, `profile_photo`, `created_at`, `phone`, `dob`, `address`) VALUES
(1, 'bini benny', 'bini@gmail.com', '$2y$10$XXaGSc7uVnqY9ZtMmBAyzeZDcf3BXoYBs3u0pljogFPijgLFe7lDm', 'User', 1, NULL, '2026-06-04 09:55:37', '09518965091', '2006-05-25', NULL),
(2, 'Fluffside Admin', 'admin@fluffside.com', '$2y$10$pEF1pWZLnrRjdk.jbMxTnuBPqx9ilnLocaeC4RG9mYDMK3RTQJJl6', 'Admin', 1, NULL, '2026-06-09 04:03:37', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_applications`
--
ALTER TABLE `tbl_applications`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `idx_apps_user` (`user_id`);

--
-- Indexes for table `tbl_app_adoption`
--
ALTER TABLE `tbl_app_adoption`
  ADD PRIMARY KEY (`app_id`);

--
-- Indexes for table `tbl_app_applicant`
--
ALTER TABLE `tbl_app_applicant`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `idx_app_applicant_email` (`email`);

--
-- Indexes for table `tbl_app_foster`
--
ALTER TABLE `tbl_app_foster`
  ADD PRIMARY KEY (`app_id`);

--
-- Indexes for table `tbl_messages`
--
ALTER TABLE `tbl_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_msgs_app` (`app_id`);

--
-- Indexes for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Indexes for table `tbl_order_items`
--
ALTER TABLE `tbl_order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_order_items_ord` (`order_id`);

--
-- Indexes for table `tbl_pets`
--
ALTER TABLE `tbl_pets`
  ADD PRIMARY KEY (`pet_id`);

--
-- Indexes for table `tbl_pet_gallery`
--
ALTER TABLE `tbl_pet_gallery`
  ADD PRIMARY KEY (`gallery_id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- Indexes for table `tbl_pet_traits`
--
ALTER TABLE `tbl_pet_traits`
  ADD PRIMARY KEY (`trait_id`),
  ADD KEY `idx_pet_traits_pet` (`pet_id`);

--
-- Indexes for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `tbl_product_gallery`
--
ALTER TABLE `tbl_product_gallery`
  ADD PRIMARY KEY (`gallery_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tbl_sessions`
--
ALTER TABLE `tbl_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_messages`
--
ALTER TABLE `tbl_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_order_items`
--
ALTER TABLE `tbl_order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_pet_gallery`
--
ALTER TABLE `tbl_pet_gallery`
  MODIFY `gallery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `tbl_pet_traits`
--
ALTER TABLE `tbl_pet_traits`
  MODIFY `trait_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `tbl_products`
--
ALTER TABLE `tbl_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tbl_product_gallery`
--
ALTER TABLE `tbl_product_gallery`
  MODIFY `gallery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_applications`
--
ALTER TABLE `tbl_applications`
  ADD CONSTRAINT `tbl_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_applications_ibfk_2` FOREIGN KEY (`pet_id`) REFERENCES `tbl_pets` (`pet_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_app_adoption`
--
ALTER TABLE `tbl_app_adoption`
  ADD CONSTRAINT `tbl_app_adoption_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `tbl_applications` (`app_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_app_applicant`
--
ALTER TABLE `tbl_app_applicant`
  ADD CONSTRAINT `tbl_app_applicant_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `tbl_applications` (`app_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_app_foster`
--
ALTER TABLE `tbl_app_foster`
  ADD CONSTRAINT `tbl_app_foster_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `tbl_applications` (`app_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_messages`
--
ALTER TABLE `tbl_messages`
  ADD CONSTRAINT `tbl_messages_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `tbl_applications` (`app_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD CONSTRAINT `tbl_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`);

--
-- Constraints for table `tbl_order_items`
--
ALTER TABLE `tbl_order_items`
  ADD CONSTRAINT `tbl_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tbl_orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`product_id`);

--
-- Constraints for table `tbl_pet_gallery`
--
ALTER TABLE `tbl_pet_gallery`
  ADD CONSTRAINT `tbl_pet_gallery_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `tbl_pets` (`pet_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_pet_traits`
--
ALTER TABLE `tbl_pet_traits`
  ADD CONSTRAINT `tbl_pet_traits_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `tbl_pets` (`pet_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_product_gallery`
--
ALTER TABLE `tbl_product_gallery`
  ADD CONSTRAINT `tbl_product_gallery_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_sessions`
--
ALTER TABLE `tbl_sessions`
  ADD CONSTRAINT `tbl_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
