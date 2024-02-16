-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 08 déc. 2023 à 21:38
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `librairie_en_ligne`
--

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id_client` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `adresse` text,
  `numero` text,
  `mail` varchar(255) DEFAULT NULL,
  `mdp` varchar(255) DEFAULT NULL,
  `ID_STRIPE` varchar(100) NOT NULL,
  PRIMARY KEY (`id_client`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom`, `prenom`, `adresse`, `numero`, `mail`, `mdp`, `ID_STRIPE`) VALUES
(1, 'Laura', 'Sénécaille', '123 Rue du Lunaret', '0233451123', 'l.s@gmail.com', '$2y$10$ZSWTbnxCbI2NJB7Md18GvuqzRoIP5tYc0sUU/AV49VGJaNd68EQT2', ''),
(2, 'Benosmane', 'Yacine', '14 rue Shybuya', '0122334455', 'benos.yac@icloud.com', '$2y$10$easVAXLPDpQRUu8n.7zTX.TdVlhT/bbN5taS3gb0zzmOSNoNf2vhW', ''),
(3, 'Martinez', 'Mathias', '67 Rue des ecoles', '0611345793', 'martinez.mathias@gmail.com', '$2y$10$WiegeDE3OgJaW.Lpm47wfuc12RIL52w7m4GfO9dhdo2GsSZyXahJG', ''),
(4, 'Bransolle', 'Line', '12 Rue des universite', '0233456178', 'l.bransolle@gmail.com', '$2y$10$TyKp/TG6vK1lfzlWsnuxuepRZzgj8m.jei.SAcIHnjHc0p8zKMMEK', 'cus_P45l5kbz7PzpQd'),
(6, 'Akabane', 'Light', '14 rue Shybuya', '9999999999', 'li.akabane@mail.com', '$2y$10$ogXz3dp5MWxw.5vJ1EeBjezcHZC1Jr3ZPi7.1/3XEhuI7lWfm.1Na', 'cus_P6OPDUeoc8J95r'),
(7, 'Gilibert', 'Rémy', '56 rue du faubourg', '0645771245', 'remy.gilibert@gmail.com', '$2y$10$ouPvrNVHCqboK1rFBpoFyOrISmQO9vakzrdUquJUnGG0h5IQEex.G', 'cus_P6R1RFCkB9Ylwq'),
(8, 'Cozar', 'Lilou', '134 Rue des lilas', '0633457819', 'lilou.cozar@gmail.com', '$2y$10$tdmHNTimz.IBe1TrZeCHYescSHBc.bEgMPVSz.gF3qdQ.k7KJ5PVi', 'cus_P8iyb8WKrETlLm');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id_commande` int NOT NULL AUTO_INCREMENT,
  `id_art` int DEFAULT NULL,
  `id_client` int DEFAULT NULL,
  `quantite` int DEFAULT NULL,
  `envoi` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_commande`),
  KEY `id_art` (`id_art`),
  KEY `id_client` (`id_client`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `id_art`, `id_client`, `quantite`, `envoi`) VALUES
(1, 1, 1, 2, 0),
(2, 2, 1, 3, 0),
(3, 1, 2, 3, 0),
(4, 3, 2, 2, 0),
(5, 2, 3, 3, 0),
(6, 3, 3, 3, 0),
(7, 3, 4, 1, 0),
(8, 1, 4, 3, 0),
(9, 2, 4, 1, 0),
(10, 2, 7, 1, 0),
(11, 3, 7, 1, 0),
(12, 1, 4, 2, 0),
(13, 3, 8, 2, 0),
(14, 1, 8, 15, 0),
(15, 1, 4, 2, 0);

-- --------------------------------------------------------

--
-- Structure de la table `librairie`
--

DROP TABLE IF EXISTS `librairie`;
CREATE TABLE IF NOT EXISTS `librairie` (
  `id_art` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(300) NOT NULL,
  `quantite` int NOT NULL,
  `prix` float NOT NULL,
  `url_photo` varchar(300) NOT NULL,
  `description` text NOT NULL,
  `ID_STRIPE` varchar(200) NOT NULL,
  PRIMARY KEY (`id_art`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `librairie`
--

INSERT INTO `librairie` (`id_art`, `nom`, `quantite`, `prix`, `url_photo`, `description`, `ID_STRIPE`) VALUES
(1, 'Harry Potter à l\'école des sorciers', 10, 14.99, '../images/harry_potter.jpeg', 'Le jour de ses onze ans, Harry Potter, un orphelin élevé par un oncle et une tante qui le détestent, voit son existence bouleversée.\nUn géant vient le chercher pour l\'emmener à Poudlard, une école de sorcellerie !\nVoler en balai, jeter des sorts, combattre les trolls : Harry se révèle un sorcier doué.\nMais un mystère entoure sa naissance et l\'effroyable V., le mage dont personne n\'ose prononcer le nom.', 'price_1OFxLrCmz9ktjvlGb9LoZjk5'),
(2, 'Divergente', 20, 19.99, '../images/divergente.jpeg', 'Différente. Déterminée. Dangereuse. DIVERGENTE\nTris vit dans un monde post-apocalyptique où la société est divisée en cinq factions.\nÀ 16 ans, elle doit choisir son appartenance pour le reste de sa vie. Cas rarissime, son test d\'aptitudes n\'est pas concluant.\nElle est divergente.\nCe secret peut la sauver... ou la tuer.', 'price_1OFxNECmz9ktjvlG7IukIByV'),
(3, 'Hunger Games - Tome 1', 25, 9.99, '../images/hunger_games.jpg', 'Peeta et Katniss sont tirés au sort pour participer aux Hunger Games. La règle est simple : 24 candidats pour un seul survivant, le tout sous le feu des caméras...\n\nDans un futur sombre, sur les ruines des États-Unis, un jeu télévisé est créé pour contrôler le peuple par la terreur.\nDouze garçons et douze filles tirés au sort participent à cette sinistre téléréalité, que tout le monde est forcé de regarder en direct. Une seule règle dans l\'arène : survivre, à tout prix.\nQuand sa petite sœur est appelée pour participer aux Hunger Games, Katniss n\'hésite pas une seconde. Elle prend sa place, consciente du danger. À seize ans, Katniss a déjà été confrontée plusieurs fois à la mort. Chez elle, survivre est comme une seconde nature...', 'price_1OFxO8Cmz9ktjvlGYimVWJnk');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `message` varchar(256) DEFAULT NULL,
  `id_client` int DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`),
  KEY `id_client` (`id_client`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
