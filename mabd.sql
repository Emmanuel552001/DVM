-- MySQL dump 10.13  Distrib 8.0.39, for Linux (x86_64)
--
-- Host: localhost    Database: mabd
-- ------------------------------------------------------
-- Server version	8.0.39-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `conducteur`
--

DROP TABLE IF EXISTS `conducteur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conducteur` (
  `co_id` int NOT NULL AUTO_INCREMENT,
  `co_nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`co_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equipement`
--

DROP TABLE IF EXISTS `equipement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipement` (
  `eq_id` int NOT NULL AUTO_INCREMENT,
  `eq_libelle` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eq_prix` double NOT NULL,
  PRIMARY KEY (`eq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equipement_vehicule`
--

DROP TABLE IF EXISTS `equipement_vehicule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipement_vehicule` (
  `eqve_id` int NOT NULL AUTO_INCREMENT,
  `eqve_equipement_id` int NOT NULL,
  `eqve_vehicule_id` int NOT NULL,
  `eqve_quantite` int NOT NULL,
  PRIMARY KEY (`eqve_id`),
  KEY `IDX_36FEF13984F274EA` (`eqve_equipement_id`),
  KEY `IDX_36FEF1397E57CABE` (`eqve_vehicule_id`),
  CONSTRAINT `FK_36FEF1397E57CABE` FOREIGN KEY (`eqve_vehicule_id`) REFERENCES `vehicule` (`ve_id`),
  CONSTRAINT `FK_36FEF13984F274EA` FOREIGN KEY (`eqve_equipement_id`) REFERENCES `equipement` (`eq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `vehicule`
--

DROP TABLE IF EXISTS `vehicule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicule` (
  `ve_id` int NOT NULL AUTO_INCREMENT,
  `ve_co_id` int NOT NULL,
  `ve_marque` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ve_modele` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ve_date` datetime DEFAULT NULL,
  PRIMARY KEY (`ve_id`),
  KEY `IDX_292FFF1DF9C68F4` (`ve_co_id`),
  CONSTRAINT `FK_292FFF1DF9C68F4` FOREIGN KEY (`ve_co_id`) REFERENCES `conducteur` (`co_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-09-23  9:36:00
