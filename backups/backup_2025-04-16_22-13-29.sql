CREATE TABLE `leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_count` int(11) NOT NULL,
  `status` enum('Oczekujący','Zatwierdzony','Odrzucony') NOT NULL DEFAULT 'Oczekujący',
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `leaves_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`start_date` <= `end_date`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `leaves` VALUES('1', '1', '2025-06-01', '2025-06-10', '10', 'Oczekujący', 'Urlop wakacyjny');
INSERT INTO `leaves` VALUES('2', '2', '2025-04-15', '2025-04-20', '6', 'Zatwierdzony', 'Wyjazd rodzinny');
INSERT INTO `leaves` VALUES('3', '3', '2025-09-01', '2025-09-15', '15', 'Odrzucony', 'Za dużo wniosków na ten termin');
INSERT INTO `leaves` VALUES('8', '1', '2025-04-28', '2025-04-30', '3', 'Zatwierdzony', NULL);


CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` enum('user','moderator','admin') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `level` (`level`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` VALUES('1', 'user');
INSERT INTO `roles` VALUES('2', 'moderator');
INSERT INTO `roles` VALUES('3', 'admin');


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(40) NOT NULL,
  `lastname` varchar(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` varchar(10) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 1,
  `employed_from` date NOT NULL,
  `account_created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES('1', 'User', 'User', 'user@example.com', '$2y$10$Bmvou2.27H3jk79sC.K4e.fdNXyQUIqc8swn9Vl.6ZAwDTSTlnZ6q', '2001-05-15', 'male', '1', '2015-06-01', '2025-03-30 23:36:47');
INSERT INTO `users` VALUES('2', 'Moderator', 'Moderator', 'moderator@example.com', '$2y$10$Bmvou2.27H3jk79sC.K4e.fdNXyQUIqc8swn9Vl.6ZAwDTSTlnZ6q', '2000-08-20', 'female', '2', '2018-03-15', '2025-03-30 23:36:47');
INSERT INTO `users` VALUES('3', 'Admin', 'Admin', 'admin@example.com', '$2y$10$Bmvou2.27H3jk79sC.K4e.fdNXyQUIqc8swn9Vl.6ZAwDTSTlnZ6q', '0000-00-00', 'male', '3', '2010-01-10', '2025-03-30 23:36:47');
INSERT INTO `users` VALUES('4', 'Jan', 'Kowal', 'jan@example.com', '$2y$10$jlkzmK8RtNqn5yRqp00aTe9TC3FPU6Lvh0WEKO7.vh9WqrUSN60Ia', '2000-05-04', 'male', '1', '2024-07-05', '2025-04-16 01:04:57');


