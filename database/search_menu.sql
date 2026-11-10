-- Dori Bakery catalog and ordering tables. Select an empty menu database before importing.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `search_menu`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 7.00,
  `total` decimal(10,2) NOT NULL,
  `payment_method` varchar(30) NOT NULL,
  `payment_status` varchar(30) DEFAULT 'pending',
  `order_status` varchar(30) DEFAULT 'received',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `image_url`, `price`) VALUES
(1, 'COCONAT cookie', 'coconat oil & powder', '../image2/cookie1.jpeg', 3),
(3, 'MATCHA cookie', 'matcha powder & white chocolate', '../image2/cookie2.jpeg', 5),
(5, 'OREO cookie', 'oreo biscuits', '../image2/cookie3.jpeg', 5),
(7, 'CHOCOLATE cookie', 'chocolate pieces', '../image2/cookie4.jpeg', 4),
(9, 'LOTUS cookie', 'lotus biscuit', '../image2/cookie5.jpeg', 5),
(11, 'PINUT cookie', 'pinut piece & butter', '../image2/cookie6.jpeg', 5),
(13, 'FRUIT cookie', 'season fruit', '../image2/cookie8.jpeg', 6),
(14, 'REDVELVET cookie', 'chocolate & cheese', '../image2/cookie7.jpeg', 6),
(15, 'FRUIT cookie', 'season fruit', '../image2/cookie8.jpeg', 6),
(16, 'REDVELVET cookie', 'chocolate & cheese', '../image2/cookie7.jpeg', 6),
(17, 'CHOCOLATE croissant', 'chocolate cream', '../image2/crs1.jpeg', 6),
(18, 'MATCHA croissant', 'matcha powder', '../image2/crs2.jpeg', 8),
(19, 'pinut croissant', 'pinut pieces &amp; butter', '../image2/crs3.jpeg', 6),
(20, 'HAZELNUT croissant', 'hazelnut pieces &amp; butter', '../image2/crs4.jpeg', 8),
(21, 'PISTACHIO croissant', 'pistachio pieces % butter', '../image2/crs5.jpeg', 8),
(22, 'ICECREAM croissant', 'croissant with icecream', '../image2/crs6.jpeg', 6),
(23, 'STRAWBERRY croissant', 'strawberry with cream ', '../image2/crs8.jpeg', 8),
(24, 'LOTUS croissant', 'lotus bisquit &amp; cream', '../image2/crs7.jpeg', 7),
(25, 'FRUIT croissant', 'season fruit &amp; cream', '../image2/crs9.jpeg', 8),
(26, 'COCONAT croissant', 'coconat cream &amp; powder', '../image2/crs10.jpeg', 6),
(27, 'TIRRAMISU', 'designed with chocolare &amp; espresso powder', '../image2/cake1.jpeg', 10),
(28, 'MATCHA TIRRAMISU', 'matcha tea &amp; powder', '../image2/cake2.jpeg', 11),
(29, 'TART', 'designed with season fruits', '../image2/cake3.jpeg', 7),
(30, 'CREAMY TART', 'tart with full cream', '../image2/cake4.jpeg', 8),
(31, 'STRAWBERRY cookie', 'strawberry pieces', '../image2/cookie9.jpeg', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_user_product` (`user_email`, `product_id`),
  ADD KEY `cart_product` (`product_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `favorites_user_product` (`user_email`, `product_id`),
  ADD KEY `favorites_product` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id` (`user_email`, `id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

ALTER TABLE `cart`
  ADD CONSTRAINT `cart_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `cart_quantity` CHECK (`quantity` BETWEEN 1 AND 99);

ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
