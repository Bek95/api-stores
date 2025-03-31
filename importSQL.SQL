/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 8.0.17 : Database - _wshop_test
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`apistore` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `apistore`;

/*Table structure for table `produit` */

DROP TABLE IF EXISTS `stores`;

CREATE TABLE `stores` (
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                          `name` varchar(100) CHARACTER SET utf8 NOT NULL,
                          `description` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
                          `adress` VARCHAR(255) NOT NULL,
                          `zipcode`varchar(100) NOT NULL,
                          `city` VARCHAR(250) NOT NULL,
                          `country` varchar(100) not null,
                          `phone_number` varchar (100) not null,
                          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          `updated_at`TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `stores` (`name`, `description`, `adress`, `zipcode`, `city`, `country`, `phone_number`, `created_at`, `updated_at`) VALUES
                                                                                                                                     ('Magasin A', 'Magasin de produits électroniques', '123 Rue de Paris', '75001', 'Paris', 'France', '01 23 45 67 89', NOW(), NULL ),
                                                                                                                                     ('Magasin B', 'Supermarché local', '456 Avenue de Lyon', '69001', 'Lyon', 'France', '04 56 78 90 12', NOW(), NULL),
                                                                                                                                     ('Magasin C', 'Boutique de vêtements', '789 Boulevard de Marseille', '13001', 'Marseille', 'France', '09 87 65 43 21', NOW(), NULL),
                                                                                                                                     ('Magasin D', 'Librairie indépendante', '321 Rue de Toulouse', '31000', 'Toulouse', 'France', '05 12 34 56 78', NOW(), NULL),
                                                                                                                                     ('Magasin E', 'Magasin de bricolage', '654 Rue de Bordeaux', '33000', 'Bordeaux', 'France', '06 23 45 67 89', NOW(), NULL);


/*Table structure dor table `users`*/

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
                         `id` INT AUTO_INCREMENT PRIMARY KEY,
                         `email` VARCHAR(50) NOT NULL UNIQUE,
                         `password` VARCHAR(255) NOT NULL,
                         `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
