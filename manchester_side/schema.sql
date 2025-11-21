-- Database: manchester_side
-- Complete Schema with Users Table

-- Tabel Articles
CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `team` enum('manchester-united','manchester-city') NOT NULL,
  `category` enum('news','transfer','injury','match','analysis') DEFAULT 'news',
  `image_url` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `team` (`team`),
  KEY `category` (`category`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Matches
CREATE TABLE IF NOT EXISTS `matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `home_team` varchar(100) NOT NULL,
  `away_team` varchar(100) NOT NULL,
  `match_date` date NOT NULL,
  `match_time` time NOT NULL,
  `competition` varchar(100) NOT NULL,
  `venue` varchar(100) NOT NULL,
  `status` enum('upcoming','live','completed') DEFAULT 'upcoming',
  `score` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `match_date` (`match_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Users (untuk sistem login user)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `favorite_team` enum('manchester-united','manchester-city') NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `name_changed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `favorite_team` (`favorite_team`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambahkan admin default
INSERT INTO `admins` (username, password, full_name)
VALUES ('admin', '$2y$10$Vz4bZ7QdJ1Y6X1Z8W9k0UeBf4gH5j6K7L8M9N0O1P2Q3R4S5T6U7V', 'Admin Utama');

-- Sample Data untuk Articles
INSERT INTO `articles` (`title`, `content`, `team`, `category`) VALUES
('Rashford Cetak Gol Kemenangan', 'Marcus Rashford menjadi pahlawan Manchester United dengan gol kemenangan di menit-menit akhir.', 'manchester-united', 'news'),
('Haaland Raih Hat-trick', 'Erling Haaland menunjukkan kelas dunia dengan mencetak hat-trick melawan Tottenham.', 'manchester-city', 'news'),
('MU Incar Striker Baru', 'Manchester United dikabarkan akan mendatangkan striker muda berbakat dari Bundesliga.', 'manchester-united', 'transfer'),
('De Bruyne Cedera Hamstring', 'Kevin De Bruyne dipastikan absen 3 minggu akibat cedera hamstring.', 'manchester-city', 'injury');

-- Sample Data untuk Matches
INSERT INTO `matches` (`home_team`, `away_team`, `match_date`, `match_time`, `competition`, `venue`, `status`) VALUES
('Manchester United', 'Liverpool', '2024-12-20', '16:30:00', 'Premier League', 'Old Trafford', 'upcoming'),
('Manchester City', 'Chelsea', '2024-12-22', '15:00:00', 'Premier League', 'Etihad Stadium', 'upcoming'),
('Arsenal', 'Manchester United', '2024-12-26', '20:00:00', 'Premier League', 'Emirates Stadium', 'upcoming');
