-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: hungerhub
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (2,'Aayush Kumar','aayush.kr.gope@gmail.com','$2y$10$G0/vXRlzVMtG6gMnBfY4cu5EOOk/kfK4C1c5y4VqSNKFLMmh4QgMe','../uploads/ADMIN_68789c49197ff9.49474001.png','2025-07-17 06:46:33'),(4,'Anita Kumari','anita@gmail.com','$2y$10$FVVky8i4TuCx.fhYw90Yu.tvkPQ4WifXf6mxu41wFz5OLuN5ChcMC','../uploads/ADMIN_68789f6cd58d61.02855811.jpg','2025-07-17 06:59:56'),(5,'Jyoti Kumari','jyoti@gmail.com','$2y$10$MMeMwYIDPWB9WmGsIrd6Y.5Kg5bTLjmrBHExK9yoQQtmmne98a8Te','../uploads/ADMIN_68789fc81481a7.36182904.png','2025-07-17 07:01:28'),(6,'Sonu kumar','sonu811670@gmail.com','$2y$10$7YqbZjXIin.kGYpLWFWfoOP5QzZi5BYcsXCmaTstZvKSHL.Jt5oLm','../uploads/ADMIN_6a98331c5cc7f1.16272668.jpeg','2026-09-02 14:30:52');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
INSERT INTO `contact_messages` VALUES (1,NULL,'John Doe','john@example.com','Great Service!','I love ordering from HungerHub. The food is always fresh and delivery is quick.','read','2025-09-20 18:34:58','2025-09-20 18:53:06'),(2,NULL,'Jane Smith','jane@example.com','Delivery Issue','My last order was delivered to the wrong address. Please help resolve this.','read','2025-09-20 18:34:58','2025-09-20 18:53:07'),(3,NULL,'Mike Johnson','mike@example.com','Menu Suggestion','Could you please add more vegetarian options to your menu?','read','2025-09-20 18:34:58','2025-09-20 18:34:58'),(4,NULL,'Sarah Wilson','sarah@example.com','Payment Problem','I was charged twice for my order #123. Please refund the duplicate charge.','read','2025-09-20 18:34:58','2025-09-20 18:53:09');
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_code` (`code`),
  KEY `idx_active` (`active`),
  KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'SAVE10',10.00,1,NULL,'2026-01-06 17:06:17');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `main_category` varchar(20) NOT NULL,
  `sub_category` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (2,'Margherita Pizza',179.00,'Classic cheese pizza with tomato sauce','uploads/FOOD_6878a9dadad627.26675920.jpeg','2025-07-17 07:44:26','Veg','Pizza'),(3,'Paneer Tikka Pizza',229.00,'Tandoori paneer chunks on a spicy crust','uploads/FOOD_6878ac2767e122.32918694.jpeg','2025-07-17 07:54:15','Veg','Pizza'),(4,'Veg Biryani',159.00,'Fragrant rice with vegetables and spices','uploads/FOOD_6878acd31fd441.45362017.jpg','2025-07-17 07:57:07','Veg','Biryani'),(5,'Masala Dosa',99.00,'Crispy dosa stuffed with spicy mashed potato','uploads/FOOD_6878ad2abe0e18.35318970.jpg','2025-07-17 07:58:34','Veg','South Indian'),(6,'Spring Rolls',89.00,'Crispy rolls filled with veggie mix','uploads/FOOD_6878ada1d4e361.51861149.jpg','2025-07-17 08:00:33','Veg','Chinese'),(7,'Gulab Jamun',59.00,'Sweet milk balls soaked in sugar syrup','uploads/FOOD_6878ae468e7b60.06308617.jpg','2025-07-17 08:03:18','Veg','Desserts'),(8,'Aloo Tikki Chaat',79.00,'Spicy potato patties topped with chutneys','uploads/FOOD_6878af19602ac3.30739324.png','2025-07-17 08:06:49','Veg','Street Food'),(9,'Mixed Veg Thali',149.00,'Complete meal with rice, chapati &amp; sabzi','uploads/FOOD_6878b0090b4ce8.27923697.png','2025-07-17 08:10:49','Veg','Thali'),(10,'Chicken Biryani',199.00,'Basmati rice with chicken &amp; aromatic spices','uploads/FOOD_6878b1fa7b8c57.55417892.jpg','2025-07-17 08:19:06','Non-Veg','Biryani'),(11,'Egg Fried Rice',139.00,'Stir-fried rice with egg and veggies','uploads/FOOD_6878b2c5eadcd3.14874529.jpeg','2025-07-17 08:22:29','Non-Veg','Chinese'),(12,'Chicken Lollipop',169.00,'Deep-fried chicken wings in spicy coating','uploads/FOOD_6878b37461e4d1.71571377.jpg','2025-07-17 08:25:24','Non-Veg','Snacks'),(13,'Butter Chicken',249.00,'Creamy tomato-based chicken curry','uploads/FOOD_6878b3fbadec79.56463848.jpg','2025-07-17 08:27:39','Non-Veg','Thali'),(14,'Fish Fry',189.00,'Crispy marinated fish, deep fried','uploads/FOOD_6878b50ea59ae9.05624856.jpg','2025-07-17 08:32:14','Non-Veg','Street Food'),(15,'Chole Bhature',119.00,'Spicy chole served with puffed bhature','uploads/FOOD_6878b592d130c2.26483780.png','2025-07-17 08:34:26','Veg','Street Food'),(16,'Chicken Kebab Roll',149.00,'Grilled chicken in soft roll with sauces','uploads/FOOD_6878b5d63185c0.30733193.jpg','2025-07-17 08:35:34','Non-Veg','Snacks'),(17,'Mixed Fruit Salad',89.00,'Healthy mix of seasonal fruits','uploads/FOOD_6878b6572f18a6.24799902.jpg','2025-07-17 08:37:43','Veg','Salads'),(18,'Idli Sambar',69.00,'Steamed rice cakes served with sambar','uploads/FOOD_6878b6863fc019.40364017.jpg','2025-07-17 08:38:30','Veg','South Indian'),(19,'Chicken Tikka Pizza',220.00,'Spicy chicken tikka with onion and bell pepper.','uploads/FOOD_6879e38f92ae32.89073099.jpg','2025-07-18 06:02:55','Non-Veg','Pizza'),(20,'Paneer Pakora',90.00,'Deep-fried paneer fritters served with chutney.','uploads/FOOD_6879e4887ddaf0.61372277.jpg','2025-07-18 06:07:04','Veg','Snacks'),(21,'Chicken 65',130.00,'Crispy, spicy South Indian chicken starter.','uploads/FOOD_6879e5706faf27.94084407.jpg','2025-07-18 06:10:56','Non-Veg','Snacks'),(22,'Veg Chowmein',110.00,'Stir-fried noodles with veggies and sauces.','uploads/FOOD_6879e5ece1aaa1.92601971.jpg','2025-07-18 06:13:00','Veg','Chinese'),(23,'Chicken Manchurian',140.00,'Spicy Indo-Chinese chicken balls in thick gravy.','uploads/FOOD_6879e68ae7cbc3.10340276.jpg','2025-07-18 06:15:38','Non-Veg','Chinese'),(24,'Non-Veg Deluxe Thali',180.00,'Chicken curry, rice, dal, salad, and roti.','uploads/FOOD_6879e9898fa501.61481792.png','2025-07-18 06:28:25','Non-Veg','Thali'),(25,'Pav Bhaji',90.00,'Mashed spiced veggies served with buttered pav.','uploads/FOOD_6879eaa6bd89e6.50885377.jpg','2025-07-18 06:33:10','Veg','Street Food');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `item_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `menu_item_id` (`menu_item_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `items` text DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Confirmed','Preparing','Ready','Out for Delivery','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `payment_method` enum('COD','Razorpay','PayPal','UPI','Card') DEFAULT 'COD',
  `payment_status` enum('Pending','Processing','Completed','Paid','Failed','Refunded') DEFAULT 'Pending',
  `payment_id` varchar(255) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'INR',
  `payment_date` datetime DEFAULT NULL,
  `transaction_fee` decimal(10,2) DEFAULT 0.00,
  `payment_gateway_response` text DEFAULT NULL,
  `estimated_delivery` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `coupon_discount_percent` decimal(5,2) DEFAULT NULL,
  `coupon_discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rider_name` varchar(100) DEFAULT NULL,
  `rider_phone` varchar(20) DEFAULT NULL,
  `rider_vehicle` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `payment_status` (`payment_status`),
  KEY `payment_method` (`payment_method`),
  KEY `payment_date` (`payment_date`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'rahul','7894563210','ratu','Chicken Manchurian (x1), Pav Bhaji (x1)',230.00,'Delivered','COD','Pending',NULL,NULL,'INR',NULL,0.00,NULL,NULL,NULL,'2025-07-18 07:41:35',1,NULL,NULL,0.00,NULL,NULL,NULL),(2,'Ayush Kumar','9110160470','8A ,VILL PO - HUNDRU ,PS- AIRPORT(DORANDA)','Non-Veg Deluxe Thali (x1)',180.00,'Pending','UPI','Pending',NULL,180.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:49:24',4,NULL,NULL,0.00,NULL,NULL,NULL),(3,'Test User','9999999999','Test Address, Test City, 123456','Margherita Pizza x3, Paneer Tikka Pizza x2, Veg Biryani x1',1154.00,'Pending','COD','Pending',NULL,1154.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:51:36',5,NULL,NULL,0.00,NULL,NULL,NULL),(4,'Test User','9999999999','Test Address, Test City, 123456','Margherita Pizza x3, Paneer Tikka Pizza x2, Veg Biryani x1',1181.23,'Pending','Razorpay','Pending',NULL,1181.23,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:51:36',5,NULL,NULL,0.00,NULL,NULL,NULL),(5,'Test User','9999999999','Test Address, Test City, 123456','Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x3',1243.00,'Pending','COD','Pending',NULL,1243.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:52:11',5,NULL,NULL,0.00,NULL,NULL,NULL),(6,'Test User','9999999999','Test Address, Test City, 123456','Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x3',1272.33,'Pending','Razorpay','Pending',NULL,1272.33,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:52:11',5,NULL,NULL,0.00,NULL,NULL,NULL),(7,'Test User','9999999999','Test Address, Test City, 123456','Margherita Pizza x2, Paneer Tikka Pizza x3, Veg Biryani x1',1204.00,'Pending','COD','Pending',NULL,1204.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:52:22',5,NULL,NULL,0.00,NULL,NULL,NULL),(8,'Test User','9999999999','Test Address, Test City, 123456','Margherita Pizza x2, Paneer Tikka Pizza x3, Veg Biryani x1',1232.41,'Pending','Razorpay','Pending',NULL,1232.41,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:52:22',5,NULL,NULL,0.00,NULL,NULL,NULL),(9,'Test User','9999999999','Test Address, Test City, 123456','Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x2',1084.00,'Pending','COD','Pending',NULL,1084.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:52:45',5,NULL,NULL,0.00,NULL,NULL,NULL),(10,'Test User','9999999999','Test Address, Test City, 123456','Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x2',1109.58,'Pending','Razorpay','Pending',NULL,1109.58,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:52:45',5,NULL,NULL,0.00,NULL,NULL,NULL),(11,'Test User','9999999999','Test Address, Test City, 123456','Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x2',1115.44,'Pending','PayPal','Pending',NULL,1115.44,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:52:45',5,NULL,NULL,0.00,NULL,NULL,NULL),(12,'Ayush Kumar','9110160470','8A ,VILL PO - HUNDRU ,PS- AIRPORT(DORANDA)','Non-Veg Deluxe Thali (x1)',180.00,'Pending','UPI','Pending',NULL,180.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-20 17:58:18',4,NULL,NULL,0.00,NULL,NULL,NULL),(13,'Sumit Kumar Yadav','9110160470','8A ,VILL PO - HUNDRU ,PS- AIRPORT(DORANDA)','Non-Veg Deluxe Thali (x1)',180.00,'Confirmed','COD','Pending',NULL,180.00,'INR',NULL,0.00,NULL,NULL,'','2025-09-20 17:58:24',4,NULL,NULL,0.00,NULL,NULL,NULL),(14,'Aayush Kumar','9110160470','Hinoo','Veg Chowmein (x1), Pav Bhaji (x1)',200.00,'Delivered','UPI','Pending',NULL,200.00,'INR',NULL,0.00,NULL,NULL,'','2025-09-20 18:22:12',4,NULL,NULL,0.00,NULL,NULL,NULL),(15,'Aayush Kumar','9110160470','Hinoo','Veg Chowmein (x1), Pav Bhaji (x1)',200.00,'Delivered','UPI','Pending',NULL,200.00,'INR',NULL,0.00,NULL,NULL,'ddd','2025-09-20 18:22:37',4,NULL,NULL,0.00,NULL,NULL,NULL),(16,'Aayush Kumar','9110160470','8A ,VILL PO - HUNDRU ,PS- AIRPORT(DORANDA)','Idli Sambar (x1), Non-Veg Deluxe Thali (x1)',249.00,'Pending','UPI','Pending',NULL,249.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 07:24:57',4,NULL,NULL,0.00,NULL,NULL,NULL),(17,'Test Customer','1234567890','Test Address',NULL,NULL,'Pending','COD','Pending',NULL,NULL,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 08:07:49',NULL,NULL,NULL,0.00,NULL,NULL,NULL),(18,'Test Customer','1234567890','Test Address',NULL,NULL,'Pending','COD','Pending',NULL,NULL,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 08:07:57',NULL,NULL,NULL,0.00,NULL,NULL,NULL),(19,'Test Customer','1234567890','Test Address',NULL,NULL,'Pending','COD','Pending',NULL,NULL,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 08:08:05',NULL,NULL,NULL,0.00,NULL,NULL,NULL),(20,'Test Customer','1234567890','Test Address',NULL,NULL,'Pending','COD','Pending',NULL,NULL,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 08:08:10',NULL,NULL,NULL,0.00,NULL,NULL,NULL),(21,'Aayush Kumar','9110160470','8A ,VILL PO - HUNDRU ,PS- AIRPORT(DORANDA)','Idli Sambar (x1), Non-Veg Deluxe Thali (x1)',249.00,'Pending','UPI','',NULL,249.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 08:09:44',4,NULL,NULL,0.00,NULL,NULL,NULL),(22,'Test Customer','1234567890','Test Address',NULL,NULL,'Pending','COD','Pending',NULL,NULL,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 08:13:32',NULL,NULL,NULL,0.00,NULL,NULL,NULL),(23,'Aayush Kumar','9110160470','Hinoo','Chicken Manchurian (x1), Non-Veg Deluxe Thali (x1)',320.00,'Pending','UPI','',NULL,320.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 08:33:26',4,NULL,NULL,0.00,NULL,NULL,NULL),(24,'Test Customer','1234567890','Test Address',NULL,NULL,'Pending','COD','Pending',NULL,NULL,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 08:37:09',NULL,NULL,NULL,0.00,NULL,NULL,NULL),(25,'Aayush Kumar','9110160470','Hinoo','Veg Chowmein (x1), Chicken Manchurian (x1), Non-Veg Deluxe Thali (x1)',430.00,'Pending','UPI','',NULL,430.00,'INR',NULL,0.00,NULL,NULL,NULL,'2025-09-21 08:38:11',4,NULL,NULL,0.00,NULL,NULL,NULL),(26,'Sonu kumar','0860397252','Virandavan nagar road no.1 opposite ravi fast food ,near sai vihar colony, new madhukam, ranchi , jharkhand','Chicken Manchurian (x1), Non-Veg Deluxe Thali (x1)',288.00,'Delivered','UPI','Pending',NULL,288.00,'INR',NULL,0.00,NULL,NULL,'','2026-09-03 06:47:53',6,'SAVE10',10.00,32.00,NULL,NULL,NULL),(27,'Sonu kumar','8603972526','Virandavan nagar road no.1 opposite ravi fast food ,near sai vihar colony, new madhukam, ranchi , jharkhand','Non-Veg Deluxe Thali (x1), Pav Bhaji (x1)',270.00,'Pending','UPI','Pending',NULL,270.00,'INR',NULL,0.00,NULL,NULL,NULL,'2026-09-04 02:53:04',6,NULL,0.00,0.00,NULL,NULL,NULL),(28,'Sonu kumar','8603972526','Virandavan nagar road no.1 opposite ravi fast food ,near sai vihar colony, new madhukam, ranchi , jharkhand','Non-Veg Deluxe Thali (x1), Pav Bhaji (x1)',270.00,'Pending','UPI','Pending',NULL,270.00,'INR',NULL,0.00,NULL,NULL,NULL,'2026-09-04 02:59:25',6,NULL,0.00,0.00,NULL,NULL,NULL),(29,'Sonu kumar','0860397252','Virandavan nagar road no.1 opposite ravi fast food ,near sai vihar colony, new madhukam, ranchi , jharkhand','Non-Veg Deluxe Thali (x1), Pav Bhaji (x1)',270.00,'Pending','COD','Pending',NULL,270.00,'INR',NULL,0.00,NULL,NULL,NULL,'2026-09-04 03:04:43',6,NULL,0.00,0.00,NULL,NULL,NULL),(30,'Sonu kumar','08603972526','Virandavan nagar road no.1 opposite ravi fast food ,near sai vihar colony, new madhukam, ranchi , jharkhand','Pav Bhaji (x1)',90.00,'Pending','UPI','',NULL,90.00,'INR',NULL,0.00,NULL,NULL,NULL,'2026-09-04 03:10:09',6,NULL,0.00,0.00,NULL,NULL,NULL),(31,'Sonu kumar','08603972526','Virandavan nagar road no.1 opposite ravi fast food ,near sai vihar colony, new madhukam, ranchi , jharkhand','Non-Veg Deluxe Thali (x1)',180.00,'Pending','UPI','',NULL,180.00,'INR',NULL,0.00,NULL,NULL,NULL,'2026-09-04 03:13:07',6,NULL,0.00,0.00,NULL,NULL,NULL),(32,'Sonu Kumar','8603972526','Sai Vihar Colony, Ranchi','Cheese Pizza (x1), Chicken Biryani (x1)',450.00,'Preparing','UPI','Paid','UPI260906889900',NULL,'INR','2026-09-06 09:45:35',0.00,NULL,NULL,NULL,'2026-09-06 04:15:35',1,NULL,NULL,0.00,NULL,NULL,NULL),(33,'Sonu kumar','08603972526','Virandavan nagar road no.1 opposite ravi fast food ,near sai vihar colony, new madhukam, ranchi , jharkhand','Non-Veg Deluxe Thali (x1), Pav Bhaji (x1)',270.00,'Delivered','UPI','Paid','UPI685283878854',270.00,'INR','2026-09-06 09:52:08',0.00,NULL,NULL,NULL,'2026-09-06 04:22:01',6,NULL,0.00,0.00,NULL,NULL,NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_logs`
--

DROP TABLE IF EXISTS `payment_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `event_type` enum('Payment_Created','Payment_Authorized','Payment_Captured','Payment_Failed','Webhook_Received','Refund_Initiated','Refund_Completed') NOT NULL,
  `gateway` varchar(50) NOT NULL,
  `gateway_event_id` varchar(255) DEFAULT NULL,
  `request_data` text DEFAULT NULL,
  `response_data` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `order_id` (`order_id`),
  KEY `event_type` (`event_type`),
  CONSTRAINT `payment_logs_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_logs_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_logs`
--

LOCK TABLES `payment_logs` WRITE;
/*!40000 ALTER TABLE `payment_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_gateway` enum('Razorpay','PayPal','UPI','Card','Wallet') NOT NULL,
  `gateway_payment_id` varchar(255) NOT NULL,
  `gateway_order_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'INR',
  `status` enum('Created','Authorized','Captured','Failed','Cancelled','Refunded') NOT NULL,
  `gateway_response` text DEFAULT NULL,
  `transaction_fee` decimal(10,2) DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `gateway_payment_id` (`gateway_payment_id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,1,'','COD_1_1758390464',NULL,230.00,'INR','Captured',NULL,0.00,230.00,'2025-07-18 07:41:35','2025-09-20 17:47:44'),(2,10,5,'Razorpay','test_RAZORPAY_1758390765',NULL,1109.58,'INR','Captured','{\"test_payment\":true,\"method\":\"RAZORPAY\",\"amount\":1109.5824}',0.00,0.00,'2025-09-20 17:52:45','2025-09-20 17:52:45'),(3,11,5,'PayPal','test_PAYPAL_1758390765',NULL,1115.44,'INR','Captured','{\"test_payment\":true,\"method\":\"PAYPAL\",\"amount\":1115.436}',0.00,0.00,'2025-09-20 17:52:45','2025-09-20 17:52:45'),(6,21,4,'Razorpay','cffghfhgfghf',NULL,249.00,'INR','',NULL,0.00,0.00,'2025-09-21 08:14:07','2025-09-21 08:14:07'),(7,23,4,'Razorpay','jhkjhjghjgjh',NULL,320.00,'INR','',NULL,0.00,0.00,'2025-09-21 08:33:32','2025-09-21 08:33:32'),(8,25,4,'Razorpay','dfsdfsdfsds',NULL,430.00,'INR','',NULL,0.00,0.00,'2025-09-21 08:38:17','2025-09-21 08:38:17'),(9,30,6,'Razorpay','214810323261',NULL,90.00,'INR','',NULL,0.00,0.00,'2026-09-04 03:12:39','2026-09-04 03:12:39'),(10,31,6,'Razorpay','154565',NULL,180.00,'INR','',NULL,0.00,0.00,'2026-09-04 03:13:13','2026-09-04 03:13:13'),(11,32,1,'UPI','UPI260906889900',NULL,450.00,'INR','Captured',NULL,0.00,450.00,'2026-09-06 04:15:35','2026-09-06 04:15:35'),(12,33,6,'UPI','UPI685283878854',NULL,270.00,'INR','Captured',NULL,0.00,270.00,'2026-09-06 04:22:08','2026-09-06 04:22:08');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `gateway_refund_id` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Processing','Completed','Failed') DEFAULT 'Pending',
  `gateway_response` text DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `order_id` (`order_id`),
  KEY `processed_by` (`processed_by`),
  CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
/*!40000 ALTER TABLE `refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_payment_methods`
--

DROP TABLE IF EXISTS `user_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gateway` enum('Razorpay','PayPal') NOT NULL,
  `gateway_customer_id` varchar(255) NOT NULL,
  `payment_method_id` varchar(255) NOT NULL,
  `method_type` enum('Card','UPI','Netbanking','Wallet') NOT NULL,
  `last_four` varchar(4) DEFAULT NULL,
  `card_brand` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_customer_id` (`gateway_customer_id`),
  CONSTRAINT `user_payment_methods_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_payment_methods`
--

LOCK TABLES `user_payment_methods` WRITE;
/*!40000 ALTER TABLE `user_payment_methods` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'rahul kumar','chhoturahul944@gmail.com','$2y$10$fphMM9TNiNha1mdjXyBEsuf9tcdxPtXQgpjpOmMXMX0dmqTVHdq.G','2025-07-18 07:39:21','7894563210',NULL,NULL),(2,'Ayush','abc@gmail.com','$2y$10$.CzExDHIw.k77SzwaWPVMuXaQ.oAh4exQJer5ugTyLSGYAfvJ8zrK','2025-09-20 15:45:13','56466',NULL,NULL),(3,'Munna','munna@gmail.com','$2y$10$7pSjJ/S7di5G4ewRCelR/.SasEtLNeSm0Bo3.vbD4UsHN57GsuEzG','2025-09-20 15:50:39','123456789',NULL,NULL),(4,'Aayush Kumar','aayush.kr.gope@gmail.com','$2y$10$G0/vXRlzVMtG6gMnBfY4cu5EOOk/kfK4C1c5y4VqSNKFLMmh4QgMe','2025-09-20 17:21:49','9110160470',NULL,NULL),(5,'Test User','testuser@example.com','$2y$10$Wo43PFZx/GdQbIV3A2YZ4u9fidrfFGJHS0jo2oqxu5IZcxLB.x.g.','2025-09-20 17:51:36','9999999999',NULL,NULL),(6,'Sonu kumar','sonu811670@gmail.com','$2y$10$DD7Z32kYxMAttctJBgGEa.Jhq9uKmHODWBhgiiUwIvS.DDZb9bcZm','2026-09-02 14:28:31','08603972526','Virandavan nagar road no.1 opposite ravi fast food ,near sai vihar colony, new madhukam, ranchi , jharkhand',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-06 10:03:21
