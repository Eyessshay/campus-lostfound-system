
CREATE DATABASE `campus_lostfound` 

USE `campus_lostfound`;

DROP TABLE IF EXISTS `admin_logs`;

CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

insert  into `admin_logs`(`log_id`,`admin_id`,`action`,`created_at`) values 
(2,1,'删除失物信息，lost_id = 1','2026-05-31 15:35:58'),
(4,1,'删除招领信息，found_id = 1','2026-05-31 15:37:14'),
(6,1,'删除招领信息，found_id = 1','2026-05-31 15:39:32');

DROP TABLE IF EXISTS `found_items`;

CREATE TABLE `found_items` (
  `found_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `found_location` varchar(100) NOT NULL,
  `found_time` datetime NOT NULL,
  `contact` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('unclaimed','claimed') NOT NULL DEFAULT 'unclaimed',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`found_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `found_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `lost_items`;

CREATE TABLE `lost_items` (
  `lost_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `lost_location` varchar(100) NOT NULL,
  `lost_time` datetime NOT NULL,
  `contact` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('pending','found') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lost_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `lost_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

insert  into `users`(`user_id`,`username`,`password`,`phone`,`role`) values 
(1,'Alice','1425368','1234567890','user');

