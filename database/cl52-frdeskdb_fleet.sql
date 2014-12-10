CREATE DATABASE  IF NOT EXISTS `cl52-frdeskdb` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `cl52-frdeskdb`;
-- MySQL dump 10.13  Distrib 5.6.13, for Win32 (x86)
--
-- Host: 217.199.187.69    Database: cl52-frdeskdb
-- ------------------------------------------------------
-- Server version	5.5.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `fleet`
--

DROP TABLE IF EXISTS `fleet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fleet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Code` int(11) NOT NULL,
  `Unit` text COLLATE utf8_unicode_ci NOT NULL,
  `Cost` int(11) NOT NULL,
  `Family_id` int(11) NOT NULL,
  `Supplier_adress_id` int(11) NOT NULL,
  `Date_Hired` text COLLATE utf8_unicode_ci NOT NULL,
  `Available` tinyint(1) NOT NULL DEFAULT '0',
  `Active` text COLLATE utf8_unicode_ci NOT NULL,
  `Weight` int(11) NOT NULL,
  `Income` int(11) NOT NULL,
  `Disposal` text COLLATE utf8_unicode_ci NOT NULL,
  `Description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `Total_Days_Hired` int(11) NOT NULL,
  `Branch` text COLLATE utf8_unicode_ci NOT NULL,
  `Stock` int(11) NOT NULL,
  `Current_Available_Multi` text COLLATE utf8_unicode_ci NOT NULL,
  `Sale` int(11) NOT NULL DEFAULT '0',
  `creation_date` text COLLATE utf8_unicode_ci NOT NULL,
  `mod_date` text COLLATE utf8_unicode_ci NOT NULL,
  `Returned_date` text COLLATE utf8_unicode_ci,
  `Sold_date` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-11-26 17:39:05
