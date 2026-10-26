-- Database Schema for SineSine Movie Rating and Review Website
-- Database: sinesine_db

CREATE DATABASE IF NOT EXISTS `sinesine_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sinesine_db`;

-- Drop tables if existing (in reverse foreign key order)
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `movies`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
  `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  `status` ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `movies`
-- --------------------------------------------------------
CREATE TABLE `movies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `genre` VARCHAR(100) NOT NULL,
  `release_year` INT(4) NOT NULL,
  `director` VARCHAR(100) NOT NULL,
  `cast` TEXT NOT NULL,
  `duration` VARCHAR(50) NOT NULL,
  `poster` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `reviews`
-- --------------------------------------------------------
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `movie_id` INT NOT NULL,
  `rating` TINYINT(2) NOT NULL,
  `review_text` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `unique_user_movie_review` UNIQUE (`user_id`, `movie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes for performance
CREATE INDEX `idx_movies_title` ON `movies` (`title`);
CREATE INDEX `idx_movies_genre` ON `movies` (`genre`);
CREATE INDEX `idx_movies_year` ON `movies` (`release_year`);
CREATE INDEX `idx_reviews_movie` ON `reviews` (`movie_id`);
CREATE INDEX `idx_reviews_user` ON `reviews` (`user_id`);

-- --------------------------------------------------------
-- Seed Data: 15 Initial Movies
-- --------------------------------------------------------
INSERT INTO `movies` (`id`, `title`, `description`, `genre`, `release_year`, `director`, `cast`, `duration`, `poster`) VALUES
(1, 'Inception', 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O., but his tragic past may doom the project and his team to disaster.', 'Science Fiction', 2010, 'Christopher Nolan', 'Leonardo DiCaprio, Joseph Gordon-Levitt, Elliot Page, Tom Hardy', '148 min', 'inception.jpg'),
(2, 'Interstellar', 'When Earth becomes uninhabitable in the future, a farmer and ex-NASA pilot, Joseph Cooper, is tasked to pilot a spacecraft, along with a team of researchers, to find a new planet for humans.', 'Science Fiction', 2014, 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway, Jessica Chastain, Michael Caine', '169 min', 'interstellar.jpg'),
(3, 'The Dark Knight', 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.', 'Action', 2008, 'Christopher Nolan', 'Christian Bale, Heath Ledger, Aaron Eckhart, Michael Caine', '152 min', 'the-dark-knight.jpg'),
(4, 'Avengers: Endgame', 'After the devastating events of Avengers: Infinity War (2018), the universe is in ruins. With the help of remaining allies, the Avengers assemble once more in order to reverse Thanos\' actions and restore balance to the universe.', 'Action', 2019, 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Evans, Mark Ruffalo, Chris Hemsworth, Scarlett Johansson', '181 min', 'avengers-endgame.jpg'),
(5, 'Titanic', 'A seventeen-year-old aristocrat falls in love with a kind but poor artist aboard the luxurious, ill-fated R.M.S. Titanic.', 'Romance', 1997, 'James Cameron', 'Leonardo DiCaprio, Kate Winslet, Billy Zane, Kathy Bates', '194 min', 'titanic.jpg'),
(6, 'The Shawshank Redemption', 'A banker convicted of uxorcide forms a friendship over a quarter of a century with a hardened convict, while maintaining his innocence and trying to remain hopeful through simple compassion.', 'Drama', 1994, 'Frank Darabont', 'Tim Robbins, Morgan Freeman, Bob Gunton, William Sadler', '142 min', 'the-shawshank-redemption.jpg'),
(7, 'Parasite', 'Greed and class discrimination threaten the newly formed symbiotic relationship between the wealthy Park family and the destitute Kim clan.', 'Thriller', 2019, 'Bong Joon Ho', 'Song Kang-ho, Lee Sun-kyun, Cho Yeo-jeong, Choi Woo-shik', '132 min', 'parasite.jpg'),
(8, 'Joker', 'During the 1980s, a failed stand-up comedian is driven insane and turns to a life of crime and chaos in Gotham City while becoming an infamous psychopathic crime figure.', 'Drama', 2019, 'Todd Phillips', 'Joaquin Phoenix, Robert De Niro, Zazie Beetz, Frances Conroy', '122 min', 'joker.jpg'),
(9, 'Spider-Man: No Way Home', 'With Spider-Man\'s identity now revealed, Peter asks Doctor Strange for help. When a spell goes wrong, dangerous foes from other worlds start to appear, forcing Peter to discover what it truly means to be Spider-Man.', 'Action', 2021, 'Jon Watts', 'Tom Holland, Zendaya, Benedict Cumberbatch, Jacob Batalon', '148 min', 'spider-man-no-way-home.jpg'),
(10, 'Oppenheimer', 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb during World War II.', 'Drama', 2023, 'Christopher Nolan', 'Cillian Murphy, Emily Blunt, Matt Damon, Robert Downey Jr.', '180 min', 'oppenheimer.jpg'),
(11, 'Dune: Part Two', 'Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family. Facing a choice between the love of his life and the fate of the universe, he endeavors to prevent a terrible future only he can foresee.', 'Science Fiction', 2024, 'Denis Villeneuve', 'Timothée Chalamet, Zendaya, Rebecca Ferguson, Javier Bardem', '166 min', 'dune-part-two.jpg'),
(12, 'The Matrix', 'When a beautiful stranger leads computer hacker Neo to a forbidding underworld, he discovers the shocking truth--the life he knows is the elaborate deception of an evil cyber-intelligence.', 'Science Fiction', 1999, 'Lana Wachowski, Lilly Wachowski', 'Keanu Reeves, Laurence Fishburne, Carrie-Anne Moss, Hugo Weaving', '136 min', 'the-matrix.jpg'),
(13, 'Forrest Gump', 'The history of the United States from the 1950s to the \'70s unfolds from the perspective of an Alabama man with an IQ of 75, who yearns to be reunited with his childhood sweetheart.', 'Drama', 1994, 'Robert Zemeckis', 'Tom Hanks, Robin Wright, Gary Sinise, Sally Field', '142 min', 'forrest-gump.jpg'),
(14, 'The Conjuring', 'Paranormal investigators Ed and Lorraine Warren work to help a family terrorized by a dark presence in their farmhouse.', 'Horror', 2013, 'James Wan', 'Patrick Wilson, Vera Farmiga, Ron Livingston, Lili Taylor', '112 min', 'the-conjuring.jpg'),
(15, 'Inside Out 2', 'Follows Riley in her teenage years as she encounters new emotions like Anxiety, Envy, Ennui, and Embarrassment while navigating high school hockey camp.', 'Animation', 2024, 'Kelsey Mann', 'Amy Poehler, Maya Hawke, Kensington Tallman, Liza Lapira', '96 min', 'inside-out-2.jpg');

