-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping data for table boardapp.boards: ~1 rows (approximately)
REPLACE INTO `boards` (`id`, `boardName`, `created_at`) VALUES
	('c53cd3c1', 'elf board', '2025-06-23 16:57:33');

-- Dumping data for table boardapp.images: ~2 rows (approximately)
REPLACE INTO `images` (`primaryID`, `id`, `className`, `value`, `posX`, `posY`, `height`, `width`, `link`, `type`, `board_id`) VALUES
	(14, '1nd_image', 'image-block', '', 1474, 1559, 320, 344, 'https://i.pinimg.com/736x/e4/ea/7d/e4ea7d948cb46bdc3e5b6e309d784275.jpg', 'image', 'c53cd3c1'),
	(15, '2nd_image', 'image-block', '', 1147, 1587, 320, 320, 'https://i.pinimg.com/736x/22/c2/48/22c24890b11b37eaeeb0cdfe85f3c562.jpg', 'image', 'c53cd3c1');

-- Dumping data for table boardapp.notes: ~2 rows (approximately)
REPLACE INTO `notes` (`primaryID`, `id`, `className`, `value`, `posX`, `posY`, `height`, `width`, `link`, `type`, `board_id`) VALUES
	(14, '1nd_note', 'note-block', 'elven ears', 1467, 1879, 248, 355, '', 'note', 'c53cd3c1'),
	(15, '2nd_note', 'note-block', 'sdfsdfds', 1221, 1847, 220, 220, '', 'note', 'c53cd3c1');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
