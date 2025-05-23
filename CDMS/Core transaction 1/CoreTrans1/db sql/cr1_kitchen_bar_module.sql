-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 19, 2025 at 08:20 PM
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
-- Database: `cr1_kitchen_bar_module`
--

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `IngredientID` int(11) NOT NULL,
  `RecipeID` int(11) DEFAULT NULL,
  `IngredientName` varchar(255) NOT NULL,
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`IngredientID`, `RecipeID`, `IngredientName`, `Quantity`) VALUES
(2, 2, 'Chicken Breast', 1),
(3, 2, 'Burger Bun', 1),
(4, 2, 'Lettuce', 1),
(5, 2, 'Tomato Slice', 2),
(6, 2, 'Mayonnaise', 1),
(12, 3, 'Pizza Dough Ball', 1),
(13, 3, 'Tomato Sauce', 1),
(14, 3, 'Mozzarella Cheese', 100),
(15, 3, 'Fresh Basil Leaves', 5),
(16, 3, 'Olive Oil', 1),
(23, 4, 'Lemon Juice', 1),
(24, 4, 'Water', 1),
(25, 4, 'Sugar', 1),
(28, 5, 'Coffee Beans (grams)', 18),
(29, 5, 'Water (ml)', 30),
(30, 6, 'Hotdog Bun', 1),
(31, 6, 'Sausage', 1),
(32, 6, 'Ketchup', 1),
(33, 6, 'Mustard', 1),
(39, 7, 'Chicken Thigh', 1),
(40, 7, 'Flour', 1),
(41, 7, 'Seasoning', 1),
(42, 7, 'Oil for Frying', 1),
(43, 7, 'Steamed Rice', 1),
(46, 8, 'White Rice', 1),
(47, 8, 'Water', 1),
(48, 9, 'Coke Bottle/Can', 1),
(49, 10, 'Rice Flour (cups)', 1),
(50, 10, 'Coconut Milk (cups)', 1),
(51, 10, 'Sugar (tbsp)', 3),
(52, 10, 'Baking Powder (tsp)', 1),
(53, 10, 'Salted Egg Slices', 2),
(54, 10, 'Grated Cheese (tbsp)', 2),
(55, 10, 'Grated Coconut (tbsp)', 2),
(56, 10, 'Banana Leaf (sheet)', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menuitems`
--

CREATE TABLE `menuitems` (
  `MenuItemID` int(11) NOT NULL,
  `ItemName` varchar(255) NOT NULL,
  `Category` enum('Food','Beverage') NOT NULL,
  `Qty` int(100) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `ImagePath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`MenuItemID`, `ItemName`, `Category`, `Qty`, `Price`, `ImagePath`) VALUES
(1, 'Grilled Chicken Sandwich', 'Food', 100, 470.00, '/images/menu_item_6829f3b61b82d.jpg'),
(2, 'Margarita Pizza', 'Food', 100, 200.50, '/images/menu_item_6829f498efb36.jpg'),
(3, 'Lemonade', 'Beverage', 100, 80.00, '/images/menu_item_6829f538a37b3.jpg'),
(4, 'Espresso', 'Beverage', 100, 39.28, '/images/menu_item_6829f5b57954f.jpg'),
(5, 'Hotdog', 'Food', 100, 60.00, '/images/menu_item_6829f60fd0fa8.jpg'),
(6, 'FriedChicken with Rice', 'Food', 100, 60.99, '/images/menu_item_6829f6898bdcb.jpg'),
(7, 'Rice', 'Food', 30, 15.00, '/images/menu_item_6829f6d6d096c.jpg'),
(8, 'Coke', 'Beverage', 100, 20.00, '/images/menu_item_6829f6ff34ed2.jpg'),
(10, 'Bebe nga', 'Food', 100, 10.00, '/images/menu_item_6829f7d71d97f.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `RecipeID` int(11) NOT NULL,
  `MenuItemID` int(11) DEFAULT NULL,
  `RecipeName` varchar(255) NOT NULL,
  `RecipeDetails` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`RecipeID`, `MenuItemID`, `RecipeName`, `RecipeDetails`) VALUES
(2, 1, 'Grilled Chicken Sandwich Recipe', 'GGrill chicken breast and serve on bun with lettuce and tomato.rilled Chicken Sandwich Recipe'),
(3, 2, 'Margarita Pizza Recipe', 'Add tomato sauce, mozzarella, basil to dough and bake.'),
(4, 3, 'Lemonade Recipe', 'Mix lemon juice, water, and sugar.'),
(5, 4, 'Espresso Recipe', 'Brew ground coffee beans in an espresso machine.'),
(6, 5, 'Hotdog Recipe', 'Boil or grill sausage, place in bun, add condiments.'),
(7, 6, 'Fried Chicken with Rice Recipe', 'Fry seasoned chicken and serve with steamed rice.'),
(8, 7, 'Steamed Rice Recipe', 'Steam white rice with water until soft'),
(9, 8, 'Coke Serving', 'Serve chilled from bottle or can.'),
(10, 10, 'Bibingka Recipe', 'Mix rice flour, coconut milk, and sugar. Pour into a banana leaf-lined mold and bake. Top with salted egg, cheese, and coconut.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`IngredientID`),
  ADD KEY `RecipeID` (`RecipeID`);

--
-- Indexes for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD PRIMARY KEY (`MenuItemID`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`RecipeID`),
  ADD KEY `MenuItemID` (`MenuItemID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `IngredientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `menuitems`
--
ALTER TABLE `menuitems`
  MODIFY `MenuItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `RecipeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`RecipeID`) REFERENCES `recipes` (`RecipeID`);

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`MenuItemID`) REFERENCES `menuitems` (`MenuItemID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
