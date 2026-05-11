-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db-silver-happy
-- Généré le : ven. 08 mai 2026 à 22:30
-- Version du serveur : 10.11.16-MariaDB-ubu2204
-- Version de PHP : 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db-silver-happy`
--

-- --------------------------------------------------------

--
-- Structure de la table `ABONNEMENT`
--

CREATE TABLE `ABONNEMENT` (
  `id_abonnement` int(11) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `renouvellement` tinyint(1) DEFAULT 0,
  `type_abonnement` enum('prestataire','seniors') DEFAULT NULL,
  `type_paiement` enum('mensuel','annuel') DEFAULT NULL,
  `methode_paiement` enum('carte','cheque','prelevement') DEFAULT NULL,
  `tarif` double DEFAULT NULL,
  `id_paiement` int(11) NOT NULL,
  `stripe_sub` varchar(255) DEFAULT NULL,
  `url_contrat` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ABONNEMENT`
--

INSERT INTO `ABONNEMENT` (`id_abonnement`, `description`, `renouvellement`, `type_abonnement`, `type_paiement`, `methode_paiement`, `tarif`, `id_paiement`, `stripe_sub`, `url_contrat`) VALUES
(1, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 1, NULL, NULL),
(2, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'prelevement', 4, 2, NULL, NULL),
(3, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 3, NULL, NULL),
(4, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 4, NULL, NULL),
(5, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'prelevement', 4, 5, NULL, NULL),
(6, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 6, NULL, NULL),
(7, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 7, NULL, NULL),
(8, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'prelevement', 4, 8, NULL, NULL),
(9, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 9, NULL, NULL),
(10, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 10, NULL, NULL),
(11, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 11, NULL, NULL),
(12, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'prelevement', 4, 12, NULL, NULL),
(13, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 13, NULL, NULL),
(14, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 14, NULL, NULL),
(15, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'prelevement', 4, 15, NULL, NULL),
(16, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 16, NULL, NULL),
(17, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 17, NULL, NULL),
(18, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'prelevement', 4, 18, NULL, NULL),
(19, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 19, NULL, NULL),
(20, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 20, NULL, NULL),
(21, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 21, NULL, NULL),
(22, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'prelevement', 4, 22, NULL, NULL),
(23, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 23, NULL, NULL),
(24, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'carte', 4, 24, NULL, NULL),
(25, 'Abonnement Senior Mensuel', 1, 'seniors', 'mensuel', 'prelevement', 4, 25, NULL, NULL),
(26, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'carte', 40, 26, NULL, NULL),
(27, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'prelevement', 40, 27, NULL, NULL),
(28, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'carte', 40, 28, NULL, NULL),
(29, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'carte', 40, 29, NULL, NULL),
(30, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'prelevement', 40, 30, NULL, NULL),
(31, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'carte', 40, 31, NULL, NULL),
(32, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'carte', 40, 32, NULL, NULL),
(33, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'prelevement', 40, 33, NULL, NULL),
(34, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'carte', 40, 34, NULL, NULL),
(35, 'Abonnement Senior Annuel', 1, 'seniors', 'annuel', 'carte', 40, 35, NULL, NULL),
(36, 'Abonnement Silver Happy', 0, 'seniors', 'mensuel', 'carte', 4, 36, NULL, '/uploads/contracts/Contrat_Abonnement_Senior_151_08-05-2026.pdf'),
(37, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'carte', 4, 36, NULL, NULL),
(38, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'prelevement', 4, 37, NULL, NULL),
(39, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'carte', 4, 38, NULL, NULL),
(40, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'prelevement', 4, 39, NULL, NULL),
(41, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'carte', 4, 40, NULL, NULL),
(42, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'carte', 4, 41, NULL, NULL),
(43, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'prelevement', 4, 42, NULL, NULL),
(44, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'carte', 4, 43, NULL, NULL),
(45, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'carte', 4, 44, NULL, NULL),
(46, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'prelevement', 4, 45, NULL, NULL),
(47, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'carte', 4, 46, NULL, NULL),
(48, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'prelevement', 4, 47, NULL, NULL),
(49, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'carte', 4, 48, NULL, NULL),
(50, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'carte', 4, 49, NULL, NULL),
(51, 'Abonnement Pro Mensuel', 1, 'prestataire', 'mensuel', 'prelevement', 4, 50, NULL, NULL),
(52, 'Abonnement Pro Annuel', 1, 'prestataire', 'annuel', 'carte', 40, 51, NULL, NULL),
(53, 'Abonnement Pro Annuel', 1, 'prestataire', 'annuel', 'prelevement', 40, 52, NULL, NULL),
(54, 'Abonnement Pro Annuel', 1, 'prestataire', 'annuel', 'carte', 40, 53, NULL, NULL),
(55, 'Abonnement Pro Annuel', 1, 'prestataire', 'annuel', 'carte', 40, 54, NULL, NULL),
(56, 'Abonnement Pro Annuel', 1, 'prestataire', 'annuel', 'prelevement', 40, 55, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ADRESSE`
--

CREATE TABLE `ADRESSE` (
  `id_adresse` int(11) NOT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `rue` varchar(100) DEFAULT NULL,
  `ville` varchar(80) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `pays` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ADRESSE`
--

INSERT INTO `ADRESSE` (`id_adresse`, `numero`, `rue`, `ville`, `code_postal`, `pays`) VALUES
(1, '15', 'Rue de Rivoli', 'Paris', '75001', 'France'),
(2, '22', 'Avenue des Champs-Élysées', 'Paris', '75008', 'France'),
(3, '8 bis', 'Rue de la Pompe', 'Paris', '75016', 'France'),
(4, '112', 'Boulevard Saint-Germain', 'Paris', '75006', 'France'),
(5, '4', 'Place de la Bastille', 'Paris', '75011', 'France'),
(6, '3', 'Rue de la République', 'Lyon', '69001', 'France'),
(7, '45', 'Avenue Maréchal de Saxe', 'Lyon', '69003', 'France'),
(8, '12 ter', 'Boulevard des Belges', 'Lyon', '69006', 'France'),
(9, '89', 'Rue de la Part-Dieu', 'Lyon', '69003', 'France'),
(10, '21', 'La Canebière', 'Marseille', '13001', 'France'),
(11, '56', 'Promenade Georges Pompidou', 'Marseille', '13008', 'France'),
(12, '5', 'Rue Paradis', 'Marseille', '13006', 'France'),
(13, '90', 'Avenue du Prado', 'Marseille', '13008', 'France'),
(14, '14', 'Rue Sainte-Catherine', 'Bordeaux', '33000', 'France'),
(15, '2', 'Place de la Bourse', 'Bordeaux', '33000', 'France'),
(16, '78', 'Cours Victor Hugo', 'Bordeaux', '33000', 'France'),
(17, '9', 'Place du Capitole', 'Toulouse', '31000', 'France'),
(18, '33', 'Rue d\'Alsace Lorraine', 'Toulouse', '31000', 'France'),
(19, '11', 'Boulevard Carnot', 'Toulouse', '31000', 'France'),
(20, '1', 'Grand Place', 'Lille', '59000', 'France'),
(21, '44', 'Rue Nationale', 'Lille', '59800', 'France'),
(22, '27', 'Boulevard de la Liberté', 'Lille', '59000', 'France'),
(23, '10', 'Promenade des Anglais', 'Nice', '06000', 'France'),
(24, '5', 'Avenue Jean Médecin', 'Nice', '06000', 'France'),
(25, '31', 'Boulevard Victor Hugo', 'Nice', '06000', 'France'),
(26, '18', 'Rue Crébillon', 'Nantes', '44000', 'France'),
(27, '7', 'Place Royale', 'Nantes', '44000', 'France'),
(28, '55', 'Boulevard Gabriel Guist\'hau', 'Nantes', '44000', 'France'),
(29, '2', 'Place Kléber', 'Strasbourg', '67000', 'France'),
(30, '15', 'Rue des Francs-Bourgeois', 'Strasbourg', '67000', 'France'),
(31, '9', 'Avenue de la Marseillaise', 'Strasbourg', '67000', 'France'),
(32, '3', 'Place de la Comédie', 'Montpellier', '34000', 'France'),
(33, '24', 'Rue de la Loge', 'Montpellier', '34000', 'France'),
(34, '16', 'Boulevard du Jeu de Paume', 'Montpellier', '34000', 'France'),
(35, '8', 'Place Sainte-Anne', 'Rennes', '35000', 'France'),
(36, '41', 'Rue Le Bastard', 'Rennes', '35000', 'France'),
(37, '12', 'Place Drouet d\'Erlon', 'Reims', '51100', 'France'),
(38, '50', 'Avenue Jean Jaurès', 'Reims', '51100', 'France'),
(39, '6', 'Rue de la Liberté', 'Dijon', '21000', 'France'),
(40, '19', 'Place de la Libération', 'Dijon', '21000', 'France'),
(41, '11', 'Boulevard Foch', 'Angers', '49100', 'France'),
(42, '4', 'Rue Lenepveu', 'Angers', '49100', 'France'),
(43, '25', 'Cours Jean Jaurès', 'Grenoble', '38000', 'France'),
(44, '7', 'Place Grenette', 'Grenoble', '38000', 'France'),
(45, '33', 'Avenue Foch', 'Le Havre', '76600', 'France'),
(46, '14', 'Rue de Paris', 'Le Havre', '76600', 'France'),
(47, '20', 'Boulevard de Strasbourg', 'Toulon', '83000', 'France'),
(48, '5', 'Cours Lafayette', 'Toulon', '83000', 'France'),
(49, '10', 'Place de l\'Hôtel de Ville', 'Saint-Étienne', '42000', 'France'),
(50, '68', 'Rue des Martyrs de Vingré', 'Saint-Étienne', '42000', 'France'),
(51, NULL, '8 Rue des Bourgognes', 'Coupvray', '77700', 'France');

-- --------------------------------------------------------

--
-- Structure de la table `AVIS`
--

CREATE TABLE `AVIS` (
  `id_avis` int(11) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `titre` varchar(100) DEFAULT NULL,
  `note` int(11) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `categorie` enum('Service','Evenement','Prestataire','Communication','Autre') DEFAULT 'Autre',
  `id_prestataire` int(11) DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `AVIS`
--

INSERT INTO `AVIS` (`id_avis`, `description`, `titre`, `note`, `date`, `categorie`, `id_prestataire`, `id_utilisateur`) VALUES
(101, 'Le travail est soigné et il a pris le temps de m\'expliquer l\'entretien.', 'Jardinier très agréable', 5, '2025-11-10 10:00:00', 'Prestataire', 52, 102),
(102, 'Mon meuble télé a été monté en moins d\'une heure, très satisfait.', 'Montage de meuble rapide', 4, '2025-11-15 14:30:00', 'Prestataire', 55, 110),
(103, 'L\'ourlet de mon pantalon a lâché dès le premier lavage, je suis déçue.', 'Couture ratée', 1, '2025-11-20 09:15:00', 'Prestataire', 60, 115),
(104, 'Intervention rapide pour ma porte bloquée et le devis a été respecté.', 'Serrurier honnête', 5, '2025-12-05 16:45:00', 'Prestataire', 65, 122),
(105, 'La terrasse est comme neuve après son passage au karcher.', 'Nettoyage parfait', 5, '2025-12-10 11:00:00', 'Prestataire', 68, 131),
(106, 'La coiffeuse est arrivée avec 30 minutes de retard, mais la coupe est belle.', 'Un peu de retard', 3, '2026-01-08 15:20:00', 'Prestataire', 56, 105),
(107, 'Il a réussi à enlever tous les virus de mon ordinateur.', 'Aide informatique au top', 5, '2026-01-15 10:30:00', 'Prestataire', 53, 140),
(108, 'Il restait de la poussière sous les meubles du salon.', 'Ménage très moyen', 2, '2026-01-22 14:10:00', 'Prestataire', 54, 118),
(109, 'Conduite douce et agréable pour m\'emmener à mon rendez-vous médical.', 'Chauffeur très prudent', 5, '2026-02-05 08:45:00', 'Prestataire', 72, 125),
(110, 'Les finitions sont parfaites, le peintre a tout bien protégé.', 'Peinture propre', 5, '2026-02-18 16:30:00', 'Prestataire', 57, 133),
(111, 'Un vrai moment de douceur, mes mains sont superbes.', 'Manucure délicate', 5, '2026-03-02 11:15:00', 'Prestataire', 64, 112),
(112, 'Je trouve que le prix demandé pour changer une ampoule est trop élevé.', 'Tarif excessif', 2, '2026-03-10 09:30:00', 'Prestataire', 76, 128),
(113, 'Mon vélo roule parfaitement bien maintenant, les freins sont bien réglés.', 'Réparation vélo', 4, '2026-03-25 15:00:00', 'Prestataire', 94, 104),
(114, 'Une personne très douce avec mon mari, je suis rassurée.', 'Garde malade bienveillante', 5, '2026-04-05 14:20:00', 'Prestataire', 77, 138),
(115, 'Mon petit caniche rentre toujours ravi de sa balade.', 'Promenade du chien', 5, '2026-04-12 17:10:00', 'Prestataire', 67, 121),
(116, 'Il a réparé la fuite sous l\'évier rapidement.', 'Bon plombier', 4, '2026-04-20 10:45:00', 'Prestataire', 51, 109),
(117, 'Nous avons fait une belle partie de belote, très sympathique !', 'Excellent partenaire de jeu', 5, '2026-04-28 15:30:00', 'Prestataire', 74, 142),
(118, 'Les produits frais étaient bien choisis, merci beaucoup.', 'Livraison de courses', 5, '2026-05-02 11:00:00', 'Prestataire', 58, 117),
(119, 'Certaines branches dépassent encore sur le côté rue.', 'Taille de haie incomplète', 3, '2026-05-05 16:15:00', 'Prestataire', 90, 135),
(120, 'Grâce à elle, j\'ai pu ranger tous mes papiers d\'impôts facilement.', 'Aide administrative', 5, '2026-05-07 09:50:00', 'Prestataire', 66, 129),
(121, 'Je trouve toujours quelqu\'un pour m\'aider quand j\'en ai besoin.', 'Plateforme très utile', 5, '2025-12-01 10:00:00', 'Service', NULL, 101),
(122, 'Le site est bien pensé pour nous les seniors.', 'Navigation facile', 4, '2026-01-10 14:30:00', 'Service', NULL, 111),
(123, 'Il n\'y a pas assez de plombiers disponibles dans mon secteur.', 'Manque d\'artisans', 3, '2026-02-15 09:15:00', 'Service', NULL, 124),
(124, 'Avec toutes les réductions, l\'abonnement annuel vaut vraiment le coup.', 'Abonnement vite rentabilisé', 5, '2026-03-05 16:45:00', 'Service', NULL, 132),
(125, 'Savoir que les prestataires sont vérifiés me rassure beaucoup.', 'Je me sens en confiance', 5, '2026-03-20 11:20:00', 'Service', NULL, 141),
(126, 'J\'ai appelé pour un renseignement et on m\'a répondu avec beaucoup de patience.', 'Service client adorable', 5, '2026-04-02 15:00:00', 'Service', NULL, 107),
(127, 'J\'ai eu un message d\'erreur en validant ma carte bancaire hier.', 'Difficulté à réserver', 2, '2026-04-18 10:30:00', 'Service', NULL, 119),
(128, 'C\'est exactement le genre d\'aide qu\'il manquait pour rester à domicile.', 'Très bonne idée', 4, '2026-04-25 14:00:00', 'Service', NULL, 136),
(129, 'Rien à redire, tout fonctionne très bien.', 'Parfait', 5, '2026-05-01 09:10:00', 'Service', NULL, 127),
(130, 'J\'en ai parlé à ma voisine qui vient de s\'inscrire.', 'Je le recommande', 5, '2026-05-06 17:30:00', 'Service', NULL, 145),
(131, 'J\'ai adoré apprendre à faire des macarons, l\'ambiance était super.', 'Atelier cuisine convivial', 5, '2025-11-25 18:00:00', 'Evenement', NULL, 103),
(132, 'Le sujet était intéressant mais rester assis 2 heures a été douloureux pour mon dos.', 'Conférence trop longue', 3, '2025-12-12 11:30:00', 'Evenement', NULL, 114),
(133, 'Chanter les chants de Noël m\'a rappelé de beaux souvenirs.', 'Chorale merveilleuse', 5, '2025-12-20 20:30:00', 'Evenement', NULL, 123),
(134, 'Les mouvements étaient parfaits, ni trop durs ni trop faciles.', 'Gymnastique adaptée', 4, '2026-01-18 10:15:00', 'Evenement', NULL, 130),
(135, 'J\'aurais aimé être prévenue plus tôt de l\'annulation due à la pluie.', 'Marche annulée', 2, '2026-02-10 14:00:00', 'Evenement', NULL, 108),
(136, 'Le guide parlait très fort, j\'ai pu tout entendre sans mon appareil !', 'Super sortie au musée', 5, '2026-03-05 16:20:00', 'Evenement', NULL, 139),
(137, 'Je sais enfin envoyer des photos à ma petite-fille.', 'Atelier tablette très utile', 5, '2026-03-28 11:45:00', 'Evenement', NULL, 116),
(138, 'Bonne organisation, on a passé un très bon après-midi.', 'Tournoi d\'échecs', 4, '2026-04-15 17:00:00', 'Evenement', NULL, 126),
(139, 'La salle associative était mal indiquée, nous étions plusieurs à nous perdre.', 'Lieu difficile à trouver', 3, '2026-04-22 09:30:00', 'Evenement', NULL, 134),
(140, 'Je suis ressortie de cette séance complètement détendue.', 'Yoga relaxant', 5, '2026-05-03 15:10:00', 'Evenement', NULL, 143),
(141, 'Les textes sont écrits en gros, ça ne me fatigue pas les yeux.', 'Très lisible', 5, '2025-12-08 14:00:00', 'Communication', NULL, 106),
(142, 'Mon téléphone sonne trop souvent pour des alertes du site.', 'Notifications pénibles', 2, '2026-01-25 09:45:00', 'Communication', NULL, 120),
(143, 'C\'est facile d\'envoyer un petit mot au prestataire avant qu\'il vienne.', 'Messagerie pratique', 4, '2026-02-28 16:15:00', 'Communication', NULL, 137),
(144, 'Le gros bouton d\'aide est une excellente initiative.', 'Bouton urgence rassurant', 5, '2026-04-05 11:30:00', 'Communication', NULL, 113),
(145, 'J\'aime beaucoup lire les petits conseils santé chaque matin.', 'Astuces du jour', 5, '2026-05-08 08:20:00', 'Communication', NULL, 144),
(146, 'J\'ai commandé un tapis antidérapant, reçu rapidement.', 'Boutique pratique', 4, '2026-01-05 10:00:00', 'Autre', NULL, 101),
(147, 'Le code de réduction pour la fête des mères n\'a pas fonctionné.', 'Code promo expiré', 2, '2026-02-14 15:30:00', 'Autre', NULL, 119),
(148, 'Les couleurs sont douces et apaisantes, très beau site.', 'Beau design', 5, '2026-03-12 09:10:00', 'Autre', NULL, 128),
(149, 'Le carton de ma loupe de lecture est arrivé un peu écrasé.', 'Colis abîmé', 3, '2026-04-10 14:20:00', 'Autre', NULL, 136),
(150, 'Une superbe idée qui aide beaucoup les personnes seules. Continuez !', 'Merci', 5, '2026-05-06 11:45:00', 'Autre', NULL, 142);

-- --------------------------------------------------------

--
-- Structure de la table `CATEGORIE`
--

CREATE TABLE `CATEGORIE` (
  `id_categorie` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CATEGORIE`
--

INSERT INTO `CATEGORIE` (`id_categorie`, `nom`) VALUES
(1, 'Assistant de vie / Aide aux personnes âgées'),
(2, 'Aide administrative'),
(3, 'Couturier / Couturière'),
(4, 'Conseiller Juridique'),
(5, 'Éducateur d\'animaux / Garde d\'animaux'),
(6, 'Livreur de courses'),
(7, 'Agent d\'entretien (ménage et repassage)'),
(8, 'Livreur de repas (portage de repas)'),
(9, 'Aide au déménagement'),
(10, 'Bricoleur (petit bricolage / travaux)'),
(11, 'Jardinier (petit jardinage)'),
(12, 'Spécialiste de l\'amélioration de l\'habitat'),
(13, 'Décorateur / Décoratrice d\'intérieur'),
(14, 'Dépanneur informatique et multimédia'),
(15, 'Conseiller en finances et assurances'),
(16, 'Gestionnaire de patrimoine'),
(17, 'Conseiller en économie d\'énergie'),
(18, 'Médecin'),
(19, 'Thérapeute'),
(20, 'Masseur / Spécialiste du bien-être'),
(21, 'Psychologue'),
(22, 'Kinésithérapeute / Coach de remise en forme'),
(23, 'Opticien'),
(24, 'Prothésiste dentaire'),
(25, 'Ostéopathe'),
(26, 'Spécialiste en soins naturels et bio'),
(27, 'Animateur d\'événements (jeux, visites, soirées)'),
(28, 'Conférencier'),
(29, 'Coach (divers domaines)'),
(30, 'Formateur / Enseignant'),
(31, 'Professeur d\'informatique'),
(32, 'Professeur de musique / de danse'),
(33, 'Professeur de langues / d\'écriture'),
(34, 'Animateur d\'ateliers de cuisine'),
(35, 'Organisateur de voyages et sorties'),
(36, 'Prestataire d\'activités sportives'),
(37, 'Magicien / Artiste de cirque'),
(38, 'Photographe / Vidéaste'),
(39, 'Artisan (création et artisanat)'),
(40, 'Conseiller en emploi pour seniors'),
(41, 'Agent immobilier'),
(42, 'Chauffeur (covoiturage / auto)');

-- --------------------------------------------------------

--
-- Structure de la table `CODE_REDUCTION`
--

CREATE TABLE `CODE_REDUCTION` (
  `id_reduction` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `valeur` int(11) NOT NULL,
  `type` enum('pourcentage','fixe') DEFAULT 'pourcentage',
  `actif` tinyint(1) DEFAULT 1,
  `date_expiration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CODE_REDUCTION`
--

INSERT INTO `CODE_REDUCTION` (`id_reduction`, `code`, `valeur`, `type`, `actif`, `date_expiration`) VALUES
(1, 'BIENVENUE10', 10, 'pourcentage', 1, '2026-12-31 23:59:59'),
(2, 'PRINTEMPS26', 15, 'pourcentage', 1, '2026-06-20 23:59:59'),
(3, 'REDUC5', 5, 'fixe', 1, '2026-09-01 00:00:00'),
(4, 'SILVER20', 20, 'pourcentage', 1, '2027-01-01 00:00:00'),
(5, 'LIVRAISON', 10, 'fixe', 1, '2026-08-15 23:59:59'),
(6, 'FIDELITE15', 15, 'pourcentage', 1, '2026-12-31 23:59:59'),
(7, 'CADEAU10', 10, 'fixe', 1, '2026-10-31 23:59:59'),
(8, 'GRANDPARENTS', 25, 'pourcentage', 1, '2026-11-01 23:59:59'),
(9, 'ETE26', 10, 'pourcentage', 1, '2026-08-31 23:59:59'),
(10, 'SUPERPROMO', 20, 'fixe', 1, NULL),
(11, 'NOEL2025', 15, 'pourcentage', 0, '2025-12-31 23:59:59'),
(12, 'AUTOMNE25', 10, 'pourcentage', 0, '2025-11-30 23:59:59'),
(13, 'LANCEMENT', 20, 'pourcentage', 0, '2024-06-01 00:00:00'),
(14, 'HIVER2025', 5, 'fixe', 0, '2026-03-20 23:59:59'),
(15, 'FETEMERES25', 15, 'pourcentage', 0, '2025-05-31 23:59:59'),
(16, 'BLACKFRIDAY25', 30, 'pourcentage', 0, '2025-11-28 23:59:59'),
(17, 'SOLDES25', 20, 'pourcentage', 0, '2025-08-01 23:59:59'),
(18, 'ANCIENCLIENT', 10, 'fixe', 0, '2026-01-01 00:00:00'),
(19, 'VIP2025', 25, 'pourcentage', 0, '2025-12-31 23:59:59'),
(20, 'REDUCANCIEN', 5, 'fixe', 0, '2024-12-31 23:59:59');

-- --------------------------------------------------------

--
-- Structure de la table `COMMANDE`
--

CREATE TABLE `COMMANDE` (
  `id_commande` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `id_paiement` int(11) NOT NULL,
  `date_commande` datetime DEFAULT current_timestamp(),
  `total` double DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `code_postal` char(5) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `id_reduction` int(11) DEFAULT NULL,
  `montant_frais_port` double DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `CONSEIL`
--

CREATE TABLE `CONSEIL` (
  `id_conseil` int(11) NOT NULL,
  `titre` varchar(80) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `date_publication` datetime DEFAULT current_timestamp(),
  `categorie` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CONSEIL`
--

INSERT INTO `CONSEIL` (`id_conseil`, `titre`, `description`, `date_publication`, `categorie`) VALUES
(1, 'L\'importance de l\'hydratation', 'Buvez au moins 1,5L d\'eau par jour, même sans soif. Avec l\'âge, la sensation de soif diminue.', '2024-02-15 09:30:00', 'Santé'),
(2, 'Marche quotidienne', '30 minutes de marche par jour renforcent le cœur, les os et maintiennent la souplesse des articulations.', '2024-02-28 14:15:00', 'Santé'),
(3, 'Mots de passe sécurisés', 'Ne notez jamais vos mots de passe sur des post-it près de l\'ordinateur. Utilisez un carnet rangé à l\'abri.', '2024-03-05 10:00:00', 'Technologie'),
(4, 'Attention aux faux SMS', 'Ne cliquez jamais sur un lien SMS demandant de payer une amende ou un colis. C\'est souvent une arnaque.', '2024-03-12 11:45:00', 'Sécurité'),
(5, 'Fixez vos tapis', 'Utilisez du ruban adhésif double face pour fixer les bords de vos tapis et éviter les trébuchements.', '2024-03-25 08:20:00', 'Maison'),
(6, 'Vitamine D et soleil', 'Exposez vos bras et votre visage 15 minutes au soleil par jour pour aider vos os à fixer le calcium.', '2024-04-02 16:30:00', 'Nutrition'),
(7, 'Agrandir le texte sur smartphone', 'Vous pouvez augmenter la taille du texte dans les réglages de votre téléphone pour moins fatiguer vos yeux.', '2024-04-18 09:10:00', 'Technologie'),
(8, 'Détecteurs de fumée', 'Pensez à tester les piles de vos détecteurs de fumée tous les 6 mois. Appuyez simplement sur le bouton central.', '2024-05-10 14:00:00', 'Sécurité'),
(9, 'Gymnastique douce', 'Le yoga, le tai-chi ou la natation sont excellents pour améliorer l\'équilibre et prévenir les chutes.', '2024-05-22 10:30:00', 'Bien-être'),
(10, 'Faire ses courses en ligne', 'Le drive ou la livraison à domicile sont très pratiques pour vous éviter de porter des packs d\'eau lourds.', '2024-06-05 15:45:00', 'Quotidien'),
(11, 'Appels en visio', 'Utilisez WhatsApp ou Skype pour voir vos petits-enfants plus souvent. C\'est gratuit avec le Wi-Fi !', '2024-06-19 11:20:00', 'Technologie'),
(12, 'Protéines essentielles', 'Viande, poisson, œufs ou légumineuses sont indispensables pour maintenir votre masse musculaire.', '2024-07-03 08:50:00', 'Nutrition'),
(13, 'Jardiner sans se casser le dos', 'Pensez aux bacs de jardinage surélevés pour cultiver vos légumes et fleurs sans avoir à vous baisser.', '2024-07-15 09:05:00', 'Loisirs'),
(14, 'Éclairage nocturne', 'Installez de petites veilleuses automatiques dans les couloirs pour sécuriser vos levers pendant la nuit.', '2024-08-08 17:30:00', 'Maison'),
(15, 'Vérifier sa vue', 'Prenez rendez-vous chez l\'ophtalmologue une fois par an pour adapter vos lunettes et vérifier la tension oculaire.', '2024-08-25 10:15:00', 'Santé'),
(16, 'Numéros d\'urgence visibles', 'Affichez le 15 (Samu), le 18 (Pompiers) et le 112 en gros caractères près de votre téléphone fixe.', '2024-09-04 14:40:00', 'Sécurité'),
(17, 'Bénévolat et transmission', 'Transmettez votre précieuse expérience en rejoignant une association locale ou en aidant aux devoirs.', '2024-09-16 09:55:00', 'Loisirs'),
(18, 'Tapis antidérapant', 'Placez un tapis en caoutchouc avec ventouses au fond de votre douche ou baignoire pour éviter les glissades.', '2024-10-02 11:10:00', 'Maison'),
(19, 'Sauvegarde des photos', 'Pensez à sauvegarder les photos de votre téléphone sur un disque dur ou une clé USB pour ne rien perdre.', '2024-10-18 15:25:00', 'Technologie'),
(20, 'Voyager en basse saison', 'Profitez de la retraite pour voyager hors vacances scolaires : c\'est moins cher et beaucoup plus calme !', '2024-11-05 10:00:00', 'Loisirs'),
(21, 'Étirements matinaux', '5 minutes d\'étirements doux dans votre lit avant de vous lever permettent de dérouiller le corps en douceur.', '2024-11-20 07:30:00', 'Bien-être'),
(22, 'Achats sur internet', 'Avant de payer en ligne, vérifiez toujours la présence d\'un petit cadenas fermé dans la barre d\'adresse.', '2024-12-08 16:15:00', 'Sécurité'),
(23, 'Adopter un animal', 'La compagnie d\'un chat ou d\'un petit chien réduit le stress, favorise la marche et brise l\'isolement.', '2024-12-20 14:00:00', 'Bien-être'),
(24, 'Rangement intelligent', 'Placez la vaisselle et les objets que vous utilisez le plus souvent à hauteur de vos bras pour éviter les escabeaux.', '2025-01-06 09:20:00', 'Maison'),
(25, 'Anticiper avec le notaire', 'Faire un point sur sa succession permet de se rassurer et de protéger ses proches en toute sérénité.', '2025-01-18 11:05:00', 'Finances'),
(26, 'Démarchage téléphonique', 'Inscrivez-vous gratuitement sur Bloctel.gouv.fr pour réduire considérablement les appels publicitaires indésirables.', '2025-02-04 15:40:00', 'Sécurité'),
(27, 'Sommeil et écrans', 'Évitez de regarder la télévision ou le téléphone au moins 1 heure avant de dormir pour trouver un sommeil réparateur.', '2025-02-21 20:00:00', 'Santé'),
(28, 'Prendre soin de ses pieds', 'Portez de bonnes chaussures fermées à l\'intérieur plutôt que des mules pour un meilleur maintien et éviter les chutes.', '2025-03-10 10:10:00', 'Santé'),
(29, 'Avantages tarifaires', 'Demandez votre carte Avantage Senior SNCF ou renseignez-vous sur les tarifs réduits de votre mairie pour les musées.', '2025-03-25 14:50:00', 'Finances'),
(30, 'Un bon fauteuil', 'Choisissez une assise ferme avec de bons accoudoirs. Il sera beaucoup plus facile de vous en relever.', '2025-04-08 09:30:00', 'Maison'),
(31, 'Entretenir sa mémoire', 'Les mots fléchés, le Sudoku, la lecture ou apprendre de nouvelles choses stimulent activement le cerveau.', '2025-04-22 16:20:00', 'Bien-être'),
(32, 'Utiliser la commande vocale', 'Dites simplement \"Appeler Jean\" à votre smartphone, c\'est plus rapide et très pratique au quotidien.', '2025-05-07 11:35:00', 'Technologie'),
(33, 'Aides à l\'aménagement', 'Des subventions existent (MaPrimeAdapt\') pour remplacer une baignoire par une douche de plain-pied.', '2025-05-20 10:00:00', 'Maison'),
(34, 'Cuisiner en sécurité', 'Privilégiez les bouilloires automatiques et les plaques à induction qui s\'éteignent toutes seules.', '2025-06-03 15:15:00', 'Sécurité'),
(35, 'Les bienfaits du chant', 'Rejoindre une chorale est un excellent moyen de rencontrer du monde tout en travaillant son souffle.', '2025-06-18 18:30:00', 'Loisirs'),
(36, 'Club de lecture', 'Rejoignez le club de lecture de votre médiathèque locale pour échanger et découvrir de nouveaux auteurs.', '2025-07-02 14:00:00', 'Loisirs'),
(37, 'Protéger son audition', 'N\'hésitez pas à faire un bilan auditif si vous faites souvent répéter vos proches. Les appareils sont aujourd\'hui invisibles.', '2025-07-16 09:45:00', 'Santé'),
(38, 'Aides à domicile', 'Renseignez-vous sur l\'APA (Allocation Personnalisée d\'Autonomie) pour vous aider pour le ménage ou les repas.', '2025-08-05 11:20:00', 'Quotidien'),
(39, 'Varier ses sources de calcium', 'Le fromage c\'est bon, mais les amandes, les brocolis et certaines eaux minérales sont aussi très riches en calcium.', '2025-08-25 12:30:00', 'Nutrition'),
(40, 'Attention aux faux techniciens', 'Exigez toujours une carte professionnelle avant de laisser entrer quelqu\'un se présentant comme agent EDF ou plombier.', '2025-09-10 16:00:00', 'Sécurité'),
(41, 'Utiliser un pilulier', 'Un pilulier semainier préparé à l\'avance évite les oublis de médicaments ou les doubles prises.', '2025-09-28 08:15:00', 'Santé'),
(42, 'Liseuses électroniques', 'Les tablettes de lecture permettent de grossir les caractères et sont beaucoup plus légères à tenir qu\'un gros roman.', '2025-10-14 14:10:00', 'Technologie'),
(43, 'Barres d\'appui', 'Installer une barre d\'appui près des toilettes et dans la douche est un petit aménagement qui change la vie.', '2025-11-02 10:45:00', 'Maison'),
(44, 'Ateliers informatiques', 'De nombreuses mairies proposent des cours gratuits pour se familiariser avec l\'ordinateur et les démarches en ligne.', '2025-11-18 15:30:00', 'Loisirs'),
(45, 'Rester hydraté l\'hiver', 'Les soupes, bouillons et tisanes comptent tout à fait dans votre apport en eau quotidien durant la période froide !', '2025-12-05 18:00:00', 'Nutrition'),
(46, 'Renouer le contact', 'N\'hésitez pas à passer un coup de fil à un vieil ami. Une simple attention fait souvent des miracles sur le moral.', '2026-01-12 11:00:00', 'Bien-être'),
(47, 'Assurances santé', 'Comparez votre mutuelle tous les deux ans. Les besoins évoluent, et de meilleurs contrats peuvent vous faire économiser.', '2026-02-08 09:50:00', 'Finances'),
(48, 'Gare aux escabeaux', 'Pour nettoyer vos vitres ou changer une ampoule en hauteur, demandez de l\'aide plutôt que de risquer la chute.', '2026-03-15 14:20:00', 'Sécurité'),
(49, 'Écouter des podcasts', 'La radio à la demande ! Vous pouvez écouter des émissions d\'histoire ou de culture sur votre téléphone quand vous le souhaitez.', '2026-04-10 16:45:00', 'Technologie'),
(50, 'Garder le sourire', 'Rester optimiste, rire et cultiver la bonne humeur sont les secrets les plus efficaces pour vieillir en pleine santé !', '2026-05-01 08:30:00', 'Bien-être');

-- --------------------------------------------------------

--
-- Structure de la table `DISPONIBILITE`
--

CREATE TABLE `DISPONIBILITE` (
  `id_disponibilite` int(11) NOT NULL,
  `id_prestataire` int(11) NOT NULL,
  `date_heure_debut` datetime NOT NULL,
  `date_heure_fin` datetime NOT NULL,
  `notif_rappel_envoyee` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `DISPONIBILITE`
--

INSERT INTO `DISPONIBILITE` (`id_disponibilite`, `id_prestataire`, `date_heure_debut`, `date_heure_fin`, `notif_rappel_envoyee`) VALUES
(1, 51, '2026-05-11 09:00:00', '2026-05-11 12:00:00', 0),
(2, 51, '2026-05-11 14:00:00', '2026-05-11 17:00:00', 0),
(3, 51, '2026-05-12 09:00:00', '2026-05-12 12:00:00', 0),
(4, 51, '2026-05-12 14:00:00', '2026-05-12 17:00:00', 0),
(5, 51, '2026-05-13 09:00:00', '2026-05-13 12:00:00', 0),
(6, 51, '2026-05-13 14:00:00', '2026-05-13 17:00:00', 0),
(7, 51, '2026-05-14 09:00:00', '2026-05-14 12:00:00', 0),
(8, 51, '2026-05-14 14:00:00', '2026-05-14 17:00:00', 0),
(9, 51, '2026-05-18 09:00:00', '2026-05-18 12:00:00', 0),
(10, 51, '2026-05-18 14:00:00', '2026-05-18 17:00:00', 0),
(11, 51, '2026-05-19 09:00:00', '2026-05-19 12:00:00', 0),
(12, 51, '2026-05-19 14:00:00', '2026-05-19 17:00:00', 0),
(13, 51, '2026-05-20 09:00:00', '2026-05-20 12:00:00', 0),
(14, 51, '2026-05-20 14:00:00', '2026-05-20 17:00:00', 0),
(15, 51, '2026-05-21 09:00:00', '2026-05-21 12:00:00', 0),
(16, 51, '2026-05-21 14:00:00', '2026-05-21 17:00:00', 0),
(17, 51, '2026-05-25 09:00:00', '2026-05-25 12:00:00', 0),
(18, 51, '2026-05-25 14:00:00', '2026-05-25 17:00:00', 0),
(19, 51, '2026-05-26 09:00:00', '2026-05-26 12:00:00', 0),
(20, 51, '2026-05-26 14:00:00', '2026-05-26 17:00:00', 0),
(21, 51, '2026-05-27 09:00:00', '2026-05-27 12:00:00', 0),
(22, 51, '2026-05-27 14:00:00', '2026-05-27 17:00:00', 0),
(23, 51, '2026-05-28 09:00:00', '2026-05-28 12:00:00', 0),
(24, 51, '2026-05-28 14:00:00', '2026-05-28 17:00:00', 0),
(25, 51, '2026-06-01 09:00:00', '2026-06-01 12:00:00', 0),
(26, 51, '2026-06-01 14:00:00', '2026-06-01 17:00:00', 0),
(27, 51, '2026-06-02 09:00:00', '2026-06-02 12:00:00', 0),
(28, 51, '2026-06-02 14:00:00', '2026-06-02 17:00:00', 0),
(29, 52, '2026-05-11 09:00:00', '2026-05-11 12:00:00', 0),
(30, 52, '2026-05-13 09:00:00', '2026-05-13 12:00:00', 0),
(31, 52, '2026-05-15 09:00:00', '2026-05-15 12:00:00', 0),
(32, 52, '2026-05-18 09:00:00', '2026-05-18 12:00:00', 0),
(33, 52, '2026-05-20 09:00:00', '2026-05-20 12:00:00', 0),
(34, 52, '2026-05-22 09:00:00', '2026-05-22 12:00:00', 0),
(35, 52, '2026-05-25 09:00:00', '2026-05-25 12:00:00', 0),
(36, 52, '2026-05-27 09:00:00', '2026-05-27 12:00:00', 0),
(37, 52, '2026-05-29 09:00:00', '2026-05-29 12:00:00', 0),
(38, 52, '2026-06-01 09:00:00', '2026-06-01 12:00:00', 0),
(39, 52, '2026-06-03 09:00:00', '2026-06-03 12:00:00', 0),
(40, 52, '2026-06-05 09:00:00', '2026-06-05 12:00:00', 0),
(41, 53, '2026-05-11 14:00:00', '2026-05-11 17:00:00', 0),
(42, 53, '2026-05-12 14:00:00', '2026-05-12 17:00:00', 0),
(43, 53, '2026-05-13 14:00:00', '2026-05-13 17:00:00', 0),
(44, 53, '2026-05-14 14:00:00', '2026-05-14 17:00:00', 0),
(45, 53, '2026-05-15 14:00:00', '2026-05-15 17:00:00', 0),
(46, 53, '2026-05-18 14:00:00', '2026-05-18 17:00:00', 0),
(47, 53, '2026-05-19 14:00:00', '2026-05-19 17:00:00', 0),
(48, 53, '2026-05-20 14:00:00', '2026-05-20 17:00:00', 0),
(49, 53, '2026-05-21 14:00:00', '2026-05-21 17:00:00', 0),
(50, 53, '2026-05-22 14:00:00', '2026-05-22 17:00:00', 0),
(51, 53, '2026-05-25 14:00:00', '2026-05-25 17:00:00', 0),
(52, 53, '2026-05-26 14:00:00', '2026-05-26 17:00:00', 0),
(53, 53, '2026-05-27 14:00:00', '2026-05-27 17:00:00', 0),
(54, 53, '2026-05-28 14:00:00', '2026-05-28 17:00:00', 0),
(55, 53, '2026-05-29 14:00:00', '2026-05-29 17:00:00', 0),
(56, 54, '2026-05-12 09:00:00', '2026-05-12 12:00:00', 0),
(57, 54, '2026-05-12 14:00:00', '2026-05-12 17:00:00', 0),
(58, 54, '2026-05-13 09:00:00', '2026-05-13 12:00:00', 0),
(59, 54, '2026-05-13 14:00:00', '2026-05-13 17:00:00', 0),
(60, 54, '2026-05-14 09:00:00', '2026-05-14 12:00:00', 0),
(61, 54, '2026-05-14 14:00:00', '2026-05-14 17:00:00', 0),
(62, 54, '2026-05-15 09:00:00', '2026-05-15 12:00:00', 0),
(63, 54, '2026-05-15 14:00:00', '2026-05-15 17:00:00', 0),
(64, 54, '2026-05-19 09:00:00', '2026-05-19 12:00:00', 0),
(65, 54, '2026-05-19 14:00:00', '2026-05-19 17:00:00', 0),
(66, 54, '2026-05-20 09:00:00', '2026-05-20 12:00:00', 0),
(67, 54, '2026-05-20 14:00:00', '2026-05-20 17:00:00', 0),
(68, 54, '2026-05-21 09:00:00', '2026-05-21 12:00:00', 0),
(69, 54, '2026-05-21 14:00:00', '2026-05-21 17:00:00', 0),
(70, 54, '2026-05-22 09:00:00', '2026-05-22 12:00:00', 0),
(71, 54, '2026-05-22 14:00:00', '2026-05-22 17:00:00', 0),
(72, 54, '2026-05-26 09:00:00', '2026-05-26 12:00:00', 0),
(73, 54, '2026-05-26 14:00:00', '2026-05-26 17:00:00', 0),
(74, 54, '2026-05-27 09:00:00', '2026-05-27 12:00:00', 0),
(75, 54, '2026-05-27 14:00:00', '2026-05-27 17:00:00', 0),
(76, 54, '2026-05-28 09:00:00', '2026-05-28 12:00:00', 0),
(77, 54, '2026-05-28 14:00:00', '2026-05-28 17:00:00', 0),
(78, 55, '2026-05-11 09:00:00', '2026-05-11 12:00:00', 0),
(79, 55, '2026-05-11 14:00:00', '2026-05-11 17:00:00', 0),
(80, 55, '2026-05-16 09:00:00', '2026-05-16 12:00:00', 0),
(81, 55, '2026-05-16 14:00:00', '2026-05-16 17:00:00', 0),
(82, 55, '2026-05-17 09:00:00', '2026-05-17 12:00:00', 0),
(83, 55, '2026-05-18 09:00:00', '2026-05-18 12:00:00', 0),
(84, 55, '2026-05-18 14:00:00', '2026-05-18 17:00:00', 0),
(85, 55, '2026-05-23 09:00:00', '2026-05-23 12:00:00', 0),
(86, 55, '2026-05-23 14:00:00', '2026-05-23 17:00:00', 0),
(87, 55, '2026-05-24 09:00:00', '2026-05-24 12:00:00', 0),
(88, 55, '2026-05-25 09:00:00', '2026-05-25 12:00:00', 0),
(89, 55, '2026-05-25 14:00:00', '2026-05-25 17:00:00', 0),
(90, 55, '2026-05-30 09:00:00', '2026-05-30 12:00:00', 0),
(91, 55, '2026-05-30 14:00:00', '2026-05-30 17:00:00', 0),
(92, 56, '2026-05-12 09:00:00', '2026-05-12 12:00:00', 0),
(93, 56, '2026-05-12 14:00:00', '2026-05-12 17:00:00', 0),
(94, 56, '2026-05-14 09:00:00', '2026-05-14 12:00:00', 0),
(95, 56, '2026-05-14 14:00:00', '2026-05-14 17:00:00', 0),
(96, 56, '2026-05-19 09:00:00', '2026-05-19 12:00:00', 0),
(97, 56, '2026-05-19 14:00:00', '2026-05-19 17:00:00', 0),
(98, 56, '2026-05-21 09:00:00', '2026-05-21 12:00:00', 0),
(99, 56, '2026-05-21 14:00:00', '2026-05-21 17:00:00', 0),
(100, 56, '2026-05-26 09:00:00', '2026-05-26 12:00:00', 0),
(101, 56, '2026-05-26 14:00:00', '2026-05-26 17:00:00', 0),
(102, 56, '2026-05-28 09:00:00', '2026-05-28 12:00:00', 0),
(103, 56, '2026-05-28 14:00:00', '2026-05-28 17:00:00', 0),
(104, 57, '2026-05-13 09:00:00', '2026-05-13 12:00:00', 0),
(105, 57, '2026-05-13 14:00:00', '2026-05-13 17:00:00', 0),
(106, 57, '2026-05-20 09:00:00', '2026-05-20 12:00:00', 0),
(107, 57, '2026-05-20 14:00:00', '2026-05-20 17:00:00', 0),
(108, 57, '2026-05-27 09:00:00', '2026-05-27 12:00:00', 0),
(109, 57, '2026-05-27 14:00:00', '2026-05-27 17:00:00', 0),
(110, 57, '2026-06-03 09:00:00', '2026-06-03 12:00:00', 0),
(111, 57, '2026-06-03 14:00:00', '2026-06-03 17:00:00', 0),
(112, 58, '2026-05-11 09:00:00', '2026-05-11 12:00:00', 0),
(113, 58, '2026-05-11 14:00:00', '2026-05-11 17:00:00', 0),
(114, 58, '2026-05-15 09:00:00', '2026-05-15 12:00:00', 0),
(115, 58, '2026-05-18 09:00:00', '2026-05-18 12:00:00', 0),
(116, 58, '2026-05-18 14:00:00', '2026-05-18 17:00:00', 0),
(117, 58, '2026-05-22 09:00:00', '2026-05-22 12:00:00', 0),
(118, 58, '2026-05-25 09:00:00', '2026-05-25 12:00:00', 0),
(119, 58, '2026-05-25 14:00:00', '2026-05-25 17:00:00', 0),
(120, 58, '2026-05-29 09:00:00', '2026-05-29 12:00:00', 0),
(121, 59, '2026-05-11 14:00:00', '2026-05-11 17:00:00', 0),
(122, 59, '2026-05-12 14:00:00', '2026-05-12 17:00:00', 0),
(123, 59, '2026-05-14 14:00:00', '2026-05-14 17:00:00', 0),
(124, 59, '2026-05-18 14:00:00', '2026-05-18 17:00:00', 0),
(125, 59, '2026-05-19 14:00:00', '2026-05-19 17:00:00', 0),
(126, 59, '2026-05-21 14:00:00', '2026-05-21 17:00:00', 0),
(127, 59, '2026-05-25 14:00:00', '2026-05-25 17:00:00', 0),
(128, 59, '2026-05-26 14:00:00', '2026-05-26 17:00:00', 0),
(129, 59, '2026-05-28 14:00:00', '2026-05-28 17:00:00', 0),
(130, 60, '2026-05-11 09:00:00', '2026-05-11 12:00:00', 0),
(131, 60, '2026-05-12 09:00:00', '2026-05-12 12:00:00', 0),
(132, 60, '2026-05-13 09:00:00', '2026-05-13 12:00:00', 0),
(133, 60, '2026-05-14 09:00:00', '2026-05-14 12:00:00', 0),
(134, 60, '2026-05-15 09:00:00', '2026-05-15 12:00:00', 0),
(135, 60, '2026-05-18 09:00:00', '2026-05-18 12:00:00', 0),
(136, 60, '2026-05-19 09:00:00', '2026-05-19 12:00:00', 0),
(137, 60, '2026-05-20 09:00:00', '2026-05-20 12:00:00', 0),
(138, 60, '2026-05-21 09:00:00', '2026-05-21 12:00:00', 0),
(139, 60, '2026-05-22 09:00:00', '2026-05-22 12:00:00', 0),
(140, 61, '2026-05-18 09:00:00', '2026-05-18 12:00:00', 0),
(141, 61, '2026-05-18 14:00:00', '2026-05-18 17:00:00', 0),
(142, 62, '2026-05-18 09:00:00', '2026-05-18 12:00:00', 0),
(143, 62, '2026-05-18 14:00:00', '2026-05-18 17:00:00', 0),
(144, 63, '2026-05-19 09:00:00', '2026-05-19 12:00:00', 0),
(145, 63, '2026-05-19 14:00:00', '2026-05-19 17:00:00', 0),
(146, 64, '2026-05-19 09:00:00', '2026-05-19 12:00:00', 0),
(147, 64, '2026-05-19 14:00:00', '2026-05-19 17:00:00', 0),
(148, 65, '2026-05-20 09:00:00', '2026-05-20 12:00:00', 0),
(149, 65, '2026-05-20 14:00:00', '2026-05-20 17:00:00', 0),
(150, 66, '2026-05-20 09:00:00', '2026-05-20 12:00:00', 0),
(151, 66, '2026-05-20 14:00:00', '2026-05-20 17:00:00', 0),
(152, 67, '2026-05-21 09:00:00', '2026-05-21 12:00:00', 0),
(153, 67, '2026-05-21 14:00:00', '2026-05-21 17:00:00', 0),
(154, 68, '2026-05-21 09:00:00', '2026-05-21 12:00:00', 0),
(155, 68, '2026-05-21 14:00:00', '2026-05-21 17:00:00', 0),
(156, 69, '2026-05-22 09:00:00', '2026-05-22 12:00:00', 0),
(157, 69, '2026-05-22 14:00:00', '2026-05-22 17:00:00', 0),
(158, 70, '2026-05-22 09:00:00', '2026-05-22 12:00:00', 0),
(159, 70, '2026-05-22 14:00:00', '2026-05-22 17:00:00', 0),
(160, 71, '2026-05-18 06:00:00', '2026-05-18 09:00:00', 0),
(161, 71, '2026-05-18 09:00:00', '2026-05-18 12:00:00', 0),
(162, 72, '2026-05-19 06:00:00', '2026-05-19 09:00:00', 0),
(163, 72, '2026-05-19 09:00:00', '2026-05-19 12:00:00', 0),
(164, 73, '2026-05-20 06:00:00', '2026-05-20 09:00:00', 0),
(165, 73, '2026-05-20 09:00:00', '2026-05-20 12:00:00', 0),
(166, 74, '2026-05-21 06:00:00', '2026-05-21 09:00:00', 0),
(167, 74, '2026-05-21 09:00:00', '2026-05-21 12:00:00', 0),
(168, 75, '2026-05-22 06:00:00', '2026-05-22 09:00:00', 0),
(169, 75, '2026-05-22 09:00:00', '2026-05-22 12:00:00', 0),
(170, 76, '2026-05-23 06:00:00', '2026-05-23 09:00:00', 0),
(171, 76, '2026-05-23 09:00:00', '2026-05-23 12:00:00', 0),
(172, 77, '2026-05-25 06:00:00', '2026-05-25 09:00:00', 0),
(173, 77, '2026-05-25 09:00:00', '2026-05-25 12:00:00', 0),
(174, 78, '2026-05-26 06:00:00', '2026-05-26 09:00:00', 0),
(175, 78, '2026-05-26 09:00:00', '2026-05-26 12:00:00', 0),
(176, 79, '2026-05-27 06:00:00', '2026-05-27 09:00:00', 0),
(177, 79, '2026-05-27 09:00:00', '2026-05-27 12:00:00', 0),
(178, 80, '2026-05-28 06:00:00', '2026-05-28 09:00:00', 0),
(179, 80, '2026-05-28 09:00:00', '2026-05-28 12:00:00', 0),
(180, 81, '2026-05-18 14:00:00', '2026-05-18 17:00:00', 0),
(181, 81, '2026-05-18 17:00:00', '2026-05-18 20:00:00', 0),
(182, 82, '2026-05-19 14:00:00', '2026-05-19 17:00:00', 0),
(183, 82, '2026-05-19 17:00:00', '2026-05-19 20:00:00', 0),
(184, 83, '2026-05-20 14:00:00', '2026-05-20 17:00:00', 0),
(185, 83, '2026-05-20 17:00:00', '2026-05-20 20:00:00', 0),
(186, 84, '2026-05-21 14:00:00', '2026-05-21 17:00:00', 0),
(187, 84, '2026-05-21 17:00:00', '2026-05-21 20:00:00', 0),
(188, 85, '2026-05-22 14:00:00', '2026-05-22 17:00:00', 0),
(189, 85, '2026-05-22 17:00:00', '2026-05-22 20:00:00', 0),
(190, 86, '2026-05-25 14:00:00', '2026-05-25 17:00:00', 0),
(191, 86, '2026-05-25 17:00:00', '2026-05-25 20:00:00', 0),
(192, 87, '2026-05-26 14:00:00', '2026-05-26 17:00:00', 0),
(193, 87, '2026-05-26 17:00:00', '2026-05-26 20:00:00', 0),
(194, 88, '2026-05-27 14:00:00', '2026-05-27 17:00:00', 0),
(195, 88, '2026-05-27 17:00:00', '2026-05-27 20:00:00', 0),
(196, 89, '2026-05-28 14:00:00', '2026-05-28 17:00:00', 0),
(197, 89, '2026-05-28 17:00:00', '2026-05-28 20:00:00', 0),
(198, 90, '2026-05-29 14:00:00', '2026-05-29 17:00:00', 0),
(199, 90, '2026-05-29 17:00:00', '2026-05-29 20:00:00', 0),
(200, 91, '2026-05-16 09:00:00', '2026-05-16 12:00:00', 0),
(201, 91, '2026-05-17 14:00:00', '2026-05-17 17:00:00', 0),
(202, 92, '2026-05-16 09:00:00', '2026-05-16 12:00:00', 0),
(203, 92, '2026-05-17 14:00:00', '2026-05-17 17:00:00', 0),
(204, 93, '2026-05-23 09:00:00', '2026-05-23 12:00:00', 0),
(205, 93, '2026-05-24 14:00:00', '2026-05-24 17:00:00', 0),
(206, 94, '2026-05-23 09:00:00', '2026-05-23 12:00:00', 0),
(207, 94, '2026-05-24 14:00:00', '2026-05-24 17:00:00', 0),
(208, 95, '2026-05-30 09:00:00', '2026-05-30 12:00:00', 0),
(209, 95, '2026-05-31 14:00:00', '2026-05-31 17:00:00', 0),
(210, 96, '2026-05-30 09:00:00', '2026-05-30 12:00:00', 0),
(211, 96, '2026-05-31 14:00:00', '2026-05-31 17:00:00', 0),
(212, 97, '2026-05-16 10:00:00', '2026-05-16 13:00:00', 0),
(213, 97, '2026-05-23 10:00:00', '2026-05-23 13:00:00', 0),
(214, 98, '2026-05-17 10:00:00', '2026-05-17 13:00:00', 0),
(215, 98, '2026-05-24 10:00:00', '2026-05-24 13:00:00', 0),
(216, 99, '2026-05-16 15:00:00', '2026-05-16 18:00:00', 0),
(217, 99, '2026-05-23 15:00:00', '2026-05-23 18:00:00', 0),
(218, 100, '2026-05-17 15:00:00', '2026-05-17 18:00:00', 0),
(219, 100, '2026-05-24 15:00:00', '2026-05-24 18:00:00', 0),
(220, 61, '2026-05-25 09:00:00', '2026-05-25 12:00:00', 0),
(221, 61, '2026-05-25 14:00:00', '2026-05-25 17:00:00', 0),
(222, 62, '2026-05-26 09:00:00', '2026-05-26 12:00:00', 0),
(223, 62, '2026-05-26 14:00:00', '2026-05-26 17:00:00', 0),
(224, 63, '2026-05-27 09:00:00', '2026-05-27 12:00:00', 0),
(225, 63, '2026-05-27 14:00:00', '2026-05-27 17:00:00', 0),
(226, 64, '2026-05-28 09:00:00', '2026-05-28 12:00:00', 0),
(227, 64, '2026-05-28 14:00:00', '2026-05-28 17:00:00', 0),
(228, 65, '2026-05-29 09:00:00', '2026-05-29 12:00:00', 0),
(229, 65, '2026-05-29 14:00:00', '2026-05-29 17:00:00', 0),
(230, 91, '2026-06-06 09:00:00', '2026-06-06 12:00:00', 0),
(231, 91, '2026-06-07 14:00:00', '2026-06-07 17:00:00', 0),
(232, 92, '2026-06-06 09:00:00', '2026-06-06 12:00:00', 0),
(233, 92, '2026-06-07 14:00:00', '2026-06-07 17:00:00', 0),
(234, 93, '2026-06-06 09:00:00', '2026-06-06 12:00:00', 0),
(235, 93, '2026-06-07 14:00:00', '2026-06-07 17:00:00', 0),
(236, 94, '2026-06-06 09:00:00', '2026-06-06 12:00:00', 0),
(237, 94, '2026-06-07 14:00:00', '2026-06-07 17:00:00', 0),
(238, 95, '2026-06-06 09:00:00', '2026-06-06 12:00:00', 0),
(239, 95, '2026-06-07 14:00:00', '2026-06-07 17:00:00', 0),
(240, 96, '2026-06-06 09:00:00', '2026-06-06 12:00:00', 0),
(241, 96, '2026-06-07 14:00:00', '2026-06-07 17:00:00', 0),
(242, 97, '2026-06-06 10:00:00', '2026-06-06 13:00:00', 0),
(243, 97, '2026-06-07 10:00:00', '2026-06-07 13:00:00', 0),
(244, 98, '2026-06-06 10:00:00', '2026-06-06 13:00:00', 0),
(245, 98, '2026-06-07 10:00:00', '2026-06-07 13:00:00', 0),
(246, 99, '2026-06-06 15:00:00', '2026-06-06 18:00:00', 0),
(247, 99, '2026-06-07 15:00:00', '2026-06-07 18:00:00', 0),
(248, 100, '2026-06-06 15:00:00', '2026-06-06 18:00:00', 0),
(249, 100, '2026-06-07 15:00:00', '2026-06-07 18:00:00', 0),
(250, 91, '2026-06-13 09:00:00', '2026-06-13 12:00:00', 0),
(251, 91, '2026-06-14 14:00:00', '2026-06-14 17:00:00', 0),
(252, 92, '2026-06-13 09:00:00', '2026-06-13 12:00:00', 0),
(253, 92, '2026-06-14 14:00:00', '2026-06-14 17:00:00', 0),
(254, 93, '2026-06-13 09:00:00', '2026-06-13 12:00:00', 0),
(255, 93, '2026-06-14 14:00:00', '2026-06-14 17:00:00', 0),
(256, 94, '2026-06-13 09:00:00', '2026-06-13 12:00:00', 0),
(257, 94, '2026-06-14 14:00:00', '2026-06-14 17:00:00', 0),
(258, 95, '2026-06-13 09:00:00', '2026-06-13 12:00:00', 0),
(259, 95, '2026-06-14 14:00:00', '2026-06-14 17:00:00', 0),
(260, 96, '2026-06-13 09:00:00', '2026-06-13 12:00:00', 0),
(261, 96, '2026-06-14 14:00:00', '2026-06-14 17:00:00', 0),
(262, 97, '2026-06-13 10:00:00', '2026-06-13 13:00:00', 0),
(263, 97, '2026-06-14 10:00:00', '2026-06-14 13:00:00', 0),
(264, 98, '2026-06-13 10:00:00', '2026-06-13 13:00:00', 0),
(265, 98, '2026-06-14 10:00:00', '2026-06-14 13:00:00', 0),
(266, 99, '2026-06-13 15:00:00', '2026-06-13 18:00:00', 0),
(267, 99, '2026-06-14 15:00:00', '2026-06-14 18:00:00', 0),
(268, 100, '2026-06-13 15:00:00', '2026-06-13 18:00:00', 0),
(269, 100, '2026-06-14 15:00:00', '2026-06-14 18:00:00', 0),
(270, 91, '2026-06-20 09:00:00', '2026-06-20 12:00:00', 0),
(271, 91, '2026-06-21 14:00:00', '2026-06-21 17:00:00', 0),
(272, 92, '2026-06-20 09:00:00', '2026-06-20 12:00:00', 0),
(273, 92, '2026-06-21 14:00:00', '2026-06-21 17:00:00', 0),
(274, 93, '2026-06-20 09:00:00', '2026-06-20 12:00:00', 0),
(275, 93, '2026-06-21 14:00:00', '2026-06-21 17:00:00', 0),
(276, 94, '2026-06-20 09:00:00', '2026-06-20 12:00:00', 0),
(277, 94, '2026-06-21 14:00:00', '2026-06-21 17:00:00', 0),
(278, 95, '2026-06-20 09:00:00', '2026-06-20 12:00:00', 0),
(279, 95, '2026-06-21 14:00:00', '2026-06-21 17:00:00', 0),
(280, 96, '2026-06-20 09:00:00', '2026-06-20 12:00:00', 0),
(281, 96, '2026-06-21 14:00:00', '2026-06-21 17:00:00', 0),
(282, 97, '2026-06-20 10:00:00', '2026-06-20 13:00:00', 0),
(283, 97, '2026-06-21 10:00:00', '2026-06-21 13:00:00', 0),
(284, 98, '2026-06-20 10:00:00', '2026-06-20 13:00:00', 0),
(285, 98, '2026-06-21 10:00:00', '2026-06-21 13:00:00', 0),
(286, 99, '2026-06-20 15:00:00', '2026-06-20 18:00:00', 0),
(287, 99, '2026-06-21 15:00:00', '2026-06-21 18:00:00', 0),
(288, 100, '2026-06-20 15:00:00', '2026-06-20 18:00:00', 0),
(289, 100, '2026-06-21 15:00:00', '2026-06-21 18:00:00', 0),
(290, 91, '2026-06-27 09:00:00', '2026-06-27 12:00:00', 0),
(291, 91, '2026-06-28 14:00:00', '2026-06-28 17:00:00', 0),
(292, 92, '2026-06-27 09:00:00', '2026-06-27 12:00:00', 0),
(293, 92, '2026-06-28 14:00:00', '2026-06-28 17:00:00', 0),
(294, 93, '2026-06-27 09:00:00', '2026-06-27 12:00:00', 0),
(295, 93, '2026-06-28 14:00:00', '2026-06-28 17:00:00', 0),
(296, 94, '2026-06-27 09:00:00', '2026-06-27 12:00:00', 0),
(297, 94, '2026-06-28 14:00:00', '2026-06-28 17:00:00', 0),
(298, 95, '2026-06-27 09:00:00', '2026-06-27 12:00:00', 0),
(299, 95, '2026-06-28 14:00:00', '2026-06-28 17:00:00', 0),
(300, 96, '2026-06-27 09:00:00', '2026-06-27 12:00:00', 0),
(301, 96, '2026-06-28 14:00:00', '2026-06-28 17:00:00', 0),
(302, 97, '2026-06-27 10:00:00', '2026-06-27 13:00:00', 0),
(303, 97, '2026-06-28 10:00:00', '2026-06-28 13:00:00', 0),
(304, 98, '2026-06-27 10:00:00', '2026-06-27 13:00:00', 0),
(305, 98, '2026-06-28 10:00:00', '2026-06-28 13:00:00', 0),
(306, 99, '2026-06-27 15:00:00', '2026-06-27 18:00:00', 0),
(307, 99, '2026-06-28 15:00:00', '2026-06-28 18:00:00', 0),
(308, 100, '2026-06-27 15:00:00', '2026-06-27 18:00:00', 0),
(309, 100, '2026-06-28 15:00:00', '2026-06-28 18:00:00', 0);

-- --------------------------------------------------------

--
-- Structure de la table `DOCUMENT_PRESTATAIRE`
--

CREATE TABLE `DOCUMENT_PRESTATAIRE` (
  `id_document` int(11) NOT NULL,
  `type` varchar(250) DEFAULT NULL,
  `nom` varchar(250) DEFAULT NULL,
  `id_prestataire` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `DOCUMENT_UTILISATEUR`
--

CREATE TABLE `DOCUMENT_UTILISATEUR` (
  `id_document` int(11) NOT NULL,
  `type` varchar(250) DEFAULT NULL,
  `nom` varchar(250) DEFAULT NULL,
  `id_utilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `EVENEMENT`
--

CREATE TABLE `EVENEMENT` (
  `id_evenement` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `lieu` varchar(100) DEFAULT NULL,
  `nombre_place` int(11) NOT NULL,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  `date_ajout` datetime DEFAULT current_timestamp(),
  `id_categorie` int(11) DEFAULT NULL,
  `prix` float DEFAULT 0,
  `notif_rappel_envoyee` tinyint(1) DEFAULT 0,
  `date_fin_boost` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `EVENEMENT`
--

INSERT INTO `EVENEMENT` (`id_evenement`, `nom`, `description`, `lieu`, `nombre_place`, `date_debut`, `date_fin`, `image`, `date_ajout`, `id_categorie`, `prix`, `notif_rappel_envoyee`, `date_fin_boost`) VALUES
(1, 'Initiation Tablette', 'Apprenez à utiliser votre tablette pour appeler vos proches en vidéo.', 'Médiathèque centrale', 15, '2025-10-10 14:00:00', '2025-10-10 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(2, 'Atelier Aquarelle', 'Découvrez les bases de la peinture à l\'eau en petit groupe.', 'Salle des fêtes', 10, '2025-10-15 10:00:00', '2025-10-15 12:00:00', NULL, '2026-05-09 00:05:09', 4, 15.5, 0, NULL),
(3, 'Yoga Doux', 'Séance de yoga sur chaise pour assouplir les articulations.', 'Gymnase municipal', 20, '2025-11-05 09:30:00', '2025-11-05 10:30:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(4, 'Visite guidée historique', 'Promenade commentée dans les rues anciennes de la ville.', 'Place de la Mairie', 25, '2025-11-12 14:00:00', '2025-11-12 16:30:00', NULL, '2026-05-09 00:05:09', 5, 10, 0, NULL),
(5, 'Cuisine d\'Automne', 'Préparation de soupes et plats réconfortants de saison.', 'Cuisine associative', 8, '2025-11-20 10:00:00', '2025-11-20 13:00:00', NULL, '2026-05-09 00:05:09', 1, 20, 0, NULL),
(6, 'Prévention des arnaques', 'Conférence sur la sécurité sur internet et le démarchage.', 'Salle polyvalente', 50, '2025-12-02 15:00:00', '2025-12-02 17:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(7, 'Chorale de Noël', 'Répétition et chants traditionnels de fin d\'année.', 'Église du centre', 30, '2025-12-15 18:00:00', '2025-12-15 20:00:00', NULL, '2026-05-09 00:05:09', 4, 2, 0, NULL),
(8, 'Gymnastique douce', 'Mouvements simples pour garder la forme en hiver.', 'Salle de sport', 15, '2026-01-10 09:00:00', '2026-01-10 10:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(9, 'Club de Lecture', 'Échange autour des derniers romans primés.', 'Bibliothèque', 12, '2026-01-22 14:30:00', '2026-01-22 16:30:00', NULL, '2026-05-09 00:05:09', 4, 0, 0, NULL),
(10, 'Atelier mémoire', 'Jeux et exercices ludiques pour stimuler la mémoire.', 'Centre social', 20, '2026-02-05 10:00:00', '2026-02-05 11:30:00', NULL, '2026-05-09 00:05:09', 2, 8, 0, NULL),
(11, 'Tailler ses rosiers', 'Conseils pratiques de jardinage en vue du printemps.', 'Jardin botanique', 15, '2026-02-18 14:00:00', '2026-02-18 16:00:00', NULL, '2026-05-09 00:05:09', 1, 12, 0, NULL),
(12, 'Loto de Printemps', 'Grand loto avec de nombreux lots à gagner.', 'Salle des fêtes', 100, '2026-03-01 14:00:00', '2026-03-01 18:00:00', NULL, '2026-05-09 00:05:09', 5, 10, 0, NULL),
(13, 'Dégustation de thés', 'Découverte de thés du monde et leurs bienfaits.', 'Salon de thé local', 10, '2026-03-12 15:00:00', '2026-03-12 17:00:00', NULL, '2026-05-09 00:05:09', 1, 15, 0, NULL),
(14, 'Gérer son smartphone', 'Formation pour bien utiliser son téléphone portable.', 'Médiathèque', 12, '2026-03-25 10:00:00', '2026-03-25 12:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(15, 'Marche nordique', 'Initiation à la marche avec bâtons dans la forêt.', 'Parc forestier', 20, '2026-04-05 09:00:00', '2026-04-05 11:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(16, 'Création de bijoux', 'Atelier manuel pour créer ses propres colliers.', 'Salle associative', 8, '2026-04-18 14:00:00', '2026-04-18 16:30:00', NULL, '2026-05-09 00:05:09', 4, 25, 0, NULL),
(17, 'Visite du musée', 'Découverte de la nouvelle exposition temporaire.', 'Musée des Beaux-Arts', 20, '2026-04-28 10:00:00', '2026-04-28 12:00:00', NULL, '2026-05-09 00:05:09', 5, 8.5, 0, NULL),
(18, 'Initiation Informatique', 'Les bases de l\'ordinateur pour les débutants.', 'Médiathèque', 10, '2026-05-15 14:00:00', '2026-05-15 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(19, 'Atelier Peinture', 'Peinture sur soie et tissus.', 'Atelier d\'art', 8, '2026-05-20 14:00:00', '2026-05-20 17:00:00', NULL, '2026-05-09 00:05:09', 4, 30, 0, NULL),
(20, 'Yoga sur chaise', 'Détente et respiration.', 'Gymnase municipal', 25, '2026-05-25 10:00:00', '2026-05-25 11:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(21, 'Balade en ville', 'Marche douce d\'une heure.', 'Place centrale', 30, '2026-06-02 09:30:00', '2026-06-02 10:30:00', NULL, '2026-05-09 00:05:09', 5, 0, 0, NULL),
(22, 'Cuisine d\'Été', 'Salades fraîches et desserts légers.', 'Cuisine associative', 10, '2026-06-10 10:00:00', '2026-06-10 12:30:00', NULL, '2026-05-09 00:05:09', 1, 20, 0, NULL),
(23, 'Sécurité domestique', 'Comment prévenir les chutes chez soi.', 'Centre social', 40, '2026-06-18 15:00:00', '2026-06-18 16:30:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(24, 'Chant choral', 'Variété française des années 60.', 'Salle polyvalente', 30, '2026-06-25 18:00:00', '2026-06-25 20:00:00', NULL, '2026-05-09 00:05:09', 4, 5, 0, NULL),
(25, 'Gym douce', 'Renforcement musculaire.', 'Salle de sport', 15, '2026-07-05 09:00:00', '2026-07-05 10:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(26, 'Club de Lecture', 'Polar et romans policiers.', 'Bibliothèque', 15, '2026-07-12 14:30:00', '2026-07-12 16:30:00', NULL, '2026-05-09 00:05:09', 4, 0, 0, NULL),
(27, 'Jeux de société', 'Scrabble, belote et tarot.', 'Centre social', 30, '2026-07-20 14:00:00', '2026-07-20 17:00:00', NULL, '2026-05-09 00:05:09', 2, 2, 0, NULL),
(28, 'Jardinage balcon', 'Faire pousser des tomates cerises.', 'Serre municipale', 12, '2026-08-05 10:00:00', '2026-08-05 12:00:00', NULL, '2026-05-09 00:05:09', 1, 10, 0, NULL),
(29, 'Grand Loto', 'Loto estival.', 'Salle des fêtes', 150, '2026-08-15 14:00:00', '2026-08-15 18:00:00', NULL, '2026-05-09 00:05:09', 5, 15, 0, NULL),
(30, 'Dégustation locale', 'Produits du terroir.', 'Marché couvert', 20, '2026-08-28 11:00:00', '2026-08-28 13:00:00', NULL, '2026-05-09 00:05:09', 1, 12, 0, NULL),
(31, 'Protéger ses données', 'Gérer ses mots de passe.', 'Médiathèque', 15, '2026-09-05 14:00:00', '2026-09-05 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(32, 'Marche douce', 'Balade au bord du lac.', 'Lac municipal', 25, '2026-09-12 09:30:00', '2026-09-12 11:30:00', NULL, '2026-05-09 00:05:09', 2, 0, 0, NULL),
(33, 'Poterie', 'Modelage de l\'argile.', 'Atelier d\'art', 8, '2026-09-20 14:00:00', '2026-09-20 16:30:00', NULL, '2026-05-09 00:05:09', 4, 25, 0, NULL),
(34, 'Exposition photo', 'Photos anciennes de la ville.', 'Galerie d\'art', 20, '2026-10-05 10:00:00', '2026-10-05 12:00:00', NULL, '2026-05-09 00:05:09', 5, 5, 0, NULL),
(35, 'Tablette niveau 2', 'Envoyer des photos par email.', 'Médiathèque', 12, '2026-10-15 14:00:00', '2026-10-15 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(36, 'Dessin au fusain', 'Techniques de base.', 'Salle associative', 10, '2026-10-22 09:30:00', '2026-10-22 11:30:00', NULL, '2026-05-09 00:05:09', 4, 18, 0, NULL),
(37, 'Relaxation', 'Apprendre à respirer.', 'Gymnase', 20, '2026-11-05 10:00:00', '2026-11-05 11:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(38, 'Visite du château', 'Histoire locale.', 'Château', 30, '2026-11-12 14:30:00', '2026-11-12 16:30:00', NULL, '2026-05-09 00:05:09', 5, 12, 0, NULL),
(39, 'Pâtisserie', 'Faire des macarons.', 'Cuisine associative', 8, '2026-11-20 14:00:00', '2026-11-20 17:00:00', NULL, '2026-05-09 00:05:09', 1, 25, 0, NULL),
(40, 'Prévenir les vols', 'Conférence gendarmerie.', 'Salle polyvalente', 50, '2026-12-02 15:00:00', '2026-12-02 17:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(41, 'Concert Classique', 'Quatuor à cordes.', 'Théâtre', 80, '2026-12-10 20:00:00', '2026-12-10 22:00:00', NULL, '2026-05-09 00:05:09', 4, 15, 0, NULL),
(42, 'Gymnastique', 'Entretien de la souplesse.', 'Salle', 15, '2026-01-05 09:00:00', '2026-01-05 10:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(43, 'Lecture', 'Romans classiques.', 'Bibliothèque', 12, '2026-01-12 14:30:00', '2026-01-12 16:30:00', NULL, '2026-05-09 00:05:09', 4, 0, 0, NULL),
(44, 'Jeux', 'Après-midi cartes.', 'Centre', 20, '2026-01-15 14:00:00', '2026-01-15 17:00:00', NULL, '2026-05-09 00:05:09', 2, 2, 0, NULL),
(45, 'Plantes', 'Entretien des orchidées.', 'Jardin', 15, '2026-02-05 10:00:00', '2026-02-05 12:00:00', NULL, '2026-05-09 00:05:09', 1, 10, 0, NULL),
(46, 'Bingo', 'Bingo du mois.', 'Salle', 50, '2026-02-12 14:00:00', '2026-02-12 17:00:00', NULL, '2026-05-09 00:05:09', 5, 10, 0, NULL),
(47, 'Café littéraire', 'Autour d\'un café.', 'Café', 15, '2026-02-20 10:00:00', '2026-02-20 12:00:00', NULL, '2026-05-09 00:05:09', 4, 5, 0, NULL),
(48, 'Sécurité web', 'Achats en ligne.', 'Médiathèque', 20, '2026-03-05 14:00:00', '2026-03-05 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(49, 'Randonnée', 'Marche de 5km.', 'Forêt', 25, '2026-03-12 09:00:00', '2026-03-12 12:00:00', NULL, '2026-05-09 00:05:09', 2, 0, 0, NULL),
(50, 'Peinture acrylique', 'Paysages.', 'Atelier', 10, '2026-03-20 14:00:00', '2026-03-20 16:00:00', NULL, '2026-05-09 00:05:09', 4, 20, 0, NULL),
(51, 'Musée interactif', 'Art moderne.', 'Musée', 20, '2026-04-05 10:00:00', '2026-04-05 12:00:00', NULL, '2026-05-09 00:05:09', 5, 8, 0, NULL),
(52, 'Skype / Zoom', 'Communiquer avec ses proches.', 'Médiathèque', 12, '2026-04-15 14:00:00', '2026-04-15 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(53, 'Cuisine monde', 'Recettes italiennes.', 'Cuisine', 8, '2026-04-20 10:00:00', '2026-04-20 13:00:00', NULL, '2026-05-09 00:05:09', 1, 25, 0, NULL),
(54, 'Étirements', 'Réveil musculaire.', 'Gymnase', 20, '2026-05-02 09:00:00', '2026-05-02 10:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(55, 'Découverte du parc', 'Botanique.', 'Parc', 30, '2026-05-10 14:30:00', '2026-05-10 16:30:00', NULL, '2026-05-09 00:05:09', 5, 0, 0, NULL),
(56, 'Végétarien', 'Cuisiner sans viande.', 'Cuisine', 10, '2026-05-15 10:00:00', '2026-05-15 12:30:00', NULL, '2026-05-09 00:05:09', 1, 20, 0, NULL),
(57, 'Premiers secours', 'Gestes qui sauvent.', 'Salle', 40, '2026-06-05 15:00:00', '2026-06-05 17:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(58, 'Chorale d\'été', 'Chants festifs.', 'Place', 50, '2026-06-15 18:00:00', '2026-06-15 19:30:00', NULL, '2026-05-09 00:05:09', 4, 0, 0, NULL),
(59, 'Tai Chi', 'Initiation.', 'Parc', 20, '2026-06-20 09:30:00', '2026-06-20 10:30:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(60, 'Lecture partagée', 'Biographies.', 'Bibliothèque', 12, '2026-07-02 14:30:00', '2026-07-02 16:30:00', NULL, '2026-05-09 00:05:09', 4, 0, 0, NULL),
(61, 'Jeux en plein air', 'Pétanque et molkky.', 'Parc', 30, '2026-07-10 14:00:00', '2026-07-10 17:00:00', NULL, '2026-05-09 00:05:09', 2, 0, 0, NULL),
(62, 'Potager', 'Légumes d\'été.', 'Jardin', 15, '2026-07-15 09:00:00', '2026-07-15 11:00:00', NULL, '2026-05-09 00:05:09', 1, 10, 0, NULL),
(63, 'Tombola', 'Lots gourmands.', 'Salle', 100, '2026-08-02 14:00:00', '2026-08-02 17:00:00', NULL, '2026-05-09 00:05:09', 5, 5, 0, NULL),
(64, 'Cuisine fraîcheur', 'Gaspachos.', 'Cuisine', 10, '2026-08-10 10:00:00', '2026-08-10 12:00:00', NULL, '2026-05-09 00:05:09', 1, 15, 0, NULL),
(65, 'Sécurité maison', 'Alarmes et serrures.', 'Médiathèque', 25, '2026-08-20 14:00:00', '2026-08-20 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(66, 'Promenade', 'Marche au crépuscule.', 'Bois', 25, '2026-09-02 18:00:00', '2026-09-02 20:00:00', NULL, '2026-05-09 00:05:09', 2, 0, 0, NULL),
(67, 'Sculpture', 'Initiation.', 'Atelier', 8, '2026-09-10 14:00:00', '2026-09-10 16:00:00', NULL, '2026-05-09 00:05:09', 4, 25, 0, NULL),
(68, 'Expo histoire', 'Guerre et paix.', 'Musée', 20, '2026-09-15 10:00:00', '2026-09-15 12:00:00', NULL, '2026-05-09 00:05:09', 5, 8, 0, NULL),
(69, 'Réseaux sociaux', 'Garder le contact.', 'Médiathèque', 15, '2026-10-02 14:00:00', '2026-10-02 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(70, 'Pastel', 'Techniques douces.', 'Salle', 10, '2026-10-10 09:30:00', '2026-10-10 11:30:00', NULL, '2026-05-09 00:05:09', 4, 18, 0, NULL),
(71, 'Méditation', 'Lâcher prise.', 'Gymnase', 20, '2026-10-20 10:00:00', '2026-10-20 11:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(72, 'Visite cathédrale', 'Architecture gothique.', 'Cathédrale', 30, '2026-11-02 14:30:00', '2026-11-02 16:30:00', NULL, '2026-05-09 00:05:09', 5, 10, 0, NULL),
(73, 'Boulangerie', 'Faire son pain.', 'Cuisine', 8, '2026-11-10 10:00:00', '2026-11-10 13:00:00', NULL, '2026-05-09 00:05:09', 1, 20, 0, NULL),
(74, 'Arnaques téléphone', 'Comment réagir.', 'Salle', 40, '2026-11-15 15:00:00', '2026-11-15 16:30:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(75, 'Théâtre', 'Pièce comique.', 'Théâtre', 80, '2026-12-02 20:00:00', '2026-12-02 22:30:00', NULL, '2026-05-09 00:05:09', 4, 20, 0, NULL),
(76, 'Pilates', 'Équilibre.', 'Gymnase', 15, '2026-12-05 09:00:00', '2026-12-05 10:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(77, 'Poésie', 'Écriture et partage.', 'Bibliothèque', 12, '2026-12-10 14:30:00', '2026-12-10 16:30:00', NULL, '2026-05-09 00:05:09', 4, 0, 0, NULL),
(78, 'Echecs', 'Tournoi amical.', 'Centre', 20, '2026-12-15 14:00:00', '2026-12-15 17:00:00', NULL, '2026-05-09 00:05:09', 2, 2, 0, NULL),
(79, 'Balcon fleuri', 'Plantes d\'hiver.', 'Jardin', 15, '2026-12-20 10:00:00', '2026-12-20 12:00:00', NULL, '2026-05-09 00:05:09', 1, 10, 0, NULL),
(80, 'Jeux virtuels', 'S\'amuser sur tablette.', 'Médiathèque', 12, '2025-10-20 14:00:00', '2025-10-20 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(81, 'Soupes du monde', 'Recettes originales.', 'Cuisine', 10, '2025-11-10 10:00:00', '2025-11-10 12:30:00', NULL, '2026-05-09 00:05:09', 1, 15, 0, NULL),
(82, 'Danse de salon', 'Valse et tango.', 'Salle', 30, '2025-11-25 15:00:00', '2025-11-25 17:00:00', NULL, '2026-05-09 00:05:09', 4, 8, 0, NULL),
(83, 'Stretching', 'Souplesse.', 'Gymnase', 20, '2025-12-05 09:30:00', '2025-12-05 10:30:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(84, 'Concert solidaire', 'Au profit d\'associations.', 'Église', 100, '2025-12-20 18:00:00', '2025-12-20 20:00:00', NULL, '2026-05-09 00:05:09', 5, 0, 0, NULL),
(85, 'Mots croisés', 'Concours.', 'Centre', 25, '2026-01-18 14:00:00', '2026-01-18 16:30:00', NULL, '2026-05-09 00:05:09', 2, 2, 0, NULL),
(86, 'Impôts en ligne', 'Aide à la déclaration.', 'Médiathèque', 15, '2026-04-10 14:00:00', '2026-04-10 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(87, 'Cuisine vapeur', 'Manger sain.', 'Cuisine', 8, '2026-05-22 10:00:00', '2026-05-22 12:30:00', NULL, '2026-05-09 00:05:09', 1, 20, 0, NULL),
(88, 'Marche urbaine', 'Découvrir son quartier.', 'Mairie', 25, '2026-06-08 09:30:00', '2026-06-08 11:00:00', NULL, '2026-05-09 00:05:09', 2, 0, 0, NULL),
(89, 'Photographie', 'Avec son smartphone.', 'Médiathèque', 12, '2026-07-08 14:00:00', '2026-07-08 16:00:00', NULL, '2026-05-09 00:05:09', 4, 5, 0, NULL),
(90, 'Visite ferme', 'Animaux et nature.', 'Ferme', 20, '2026-08-12 14:30:00', '2026-08-12 17:00:00', NULL, '2026-05-09 00:05:09', 5, 8, 0, NULL),
(91, 'Chant lyrique', 'Découverte.', 'Théâtre', 40, '2026-09-25 18:00:00', '2026-09-25 19:30:00', NULL, '2026-05-09 00:05:09', 4, 10, 0, NULL),
(92, 'Sophrologie', 'Détente mentale.', 'Gymnase', 15, '2026-10-25 10:00:00', '2026-10-25 11:00:00', NULL, '2026-05-09 00:05:09', 2, 5, 0, NULL),
(93, 'Tricot', 'Pour débutants.', 'Centre', 10, '2026-11-25 14:00:00', '2026-11-25 16:00:00', NULL, '2026-05-09 00:05:09', 4, 5, 0, NULL),
(94, 'Illuminations', 'Balade de Noël.', 'Place', 50, '2026-12-22 18:00:00', '2026-12-22 19:30:00', NULL, '2026-05-09 00:05:09', 5, 0, 0, NULL),
(95, 'Bilan forme', 'Tests d\'équilibre.', 'Gymnase', 30, '2026-01-30 09:00:00', '2026-01-30 12:00:00', NULL, '2026-05-09 00:05:09', 2, 0, 0, NULL),
(96, 'Livre audio', 'Découverte du format.', 'Bibliothèque', 15, '2026-02-28 14:30:00', '2026-02-28 16:00:00', NULL, '2026-05-09 00:05:09', 3, 0, 0, NULL),
(97, 'Pêche', 'Initiation en étang.', 'Lac', 10, '2026-03-28 08:30:00', '2026-03-28 12:00:00', NULL, '2026-05-09 00:05:09', 1, 15, 0, NULL),
(98, 'Jardinage d\'intérieur', 'Créer un terrarium.', 'Serre municipale', 12, '2026-04-10 14:00:00', '2026-04-10 16:00:00', NULL, '2026-05-09 00:08:01', 1, 15, 0, NULL),
(99, 'Concert Jazz', 'Soirée jazz au théâtre.', 'Théâtre', 50, '2026-05-15 20:00:00', '2026-05-15 22:30:00', NULL, '2026-05-09 00:08:01', 4, 12, 0, NULL),
(100, 'Marche en montagne', 'Randonnée niveau intermédiaire.', 'Massif local', 20, '2026-06-20 08:00:00', '2026-06-20 17:00:00', NULL, '2026-05-09 00:08:01', 2, 5, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `FACTURE`
--

CREATE TABLE `FACTURE` (
  `id_facture` int(11) NOT NULL,
  `montant` double DEFAULT NULL,
  `frais_plateforme` double DEFAULT NULL,
  `montant_net` double DEFAULT NULL,
  `mois_annee` varchar(7) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `statut` enum('en_attente','paye','annule') DEFAULT 'en_attente',
  `id_prestataire` int(11) NOT NULL,
  `stripe_transfer_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `INSCRIPTION`
--

CREATE TABLE `INSCRIPTION` (
  `id_utilisateur` int(11) DEFAULT NULL,
  `id_evenement` int(11) DEFAULT NULL,
  `id_paiement` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `LIGNE_COMMANDE`
--

CREATE TABLE `LIGNE_COMMANDE` (
  `id_ligne` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` int(11) DEFAULT NULL,
  `prix_unitaire` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `LIKE_CONSEIL`
--

CREATE TABLE `LIKE_CONSEIL` (
  `id_utilisateur` int(11) NOT NULL,
  `id_conseil` int(11) NOT NULL,
  `date_like` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `LIKE_CONSEIL`
--

INSERT INTO `LIKE_CONSEIL` (`id_utilisateur`, `id_conseil`, `date_like`) VALUES
(101, 1, '2025-10-12 10:15:00'),
(101, 5, '2025-10-14 09:30:00'),
(101, 12, '2025-11-02 14:20:00'),
(101, 45, '2026-01-10 11:00:00'),
(102, 2, '2025-10-15 16:45:00'),
(102, 10, '2025-11-05 10:10:00'),
(102, 25, '2025-12-20 08:30:00'),
(102, 48, '2026-02-14 15:00:00'),
(103, 3, '2025-10-18 09:00:00'),
(103, 11, '2025-11-12 11:20:00'),
(103, 33, '2026-01-25 14:40:00'),
(104, 1, '2025-10-20 15:30:00'),
(104, 4, '2025-11-15 10:05:00'),
(104, 15, '2025-12-05 16:15:00'),
(104, 22, '2026-03-01 09:50:00'),
(105, 5, '2025-10-22 11:45:00'),
(105, 8, '2025-11-18 14:30:00'),
(105, 14, '2026-01-08 10:20:00'),
(106, 6, '2025-10-25 08:15:00'),
(106, 12, '2025-11-22 16:00:00'),
(106, 18, '2026-02-05 11:10:00'),
(106, 24, '2026-03-12 15:40:00'),
(107, 7, '2025-10-28 14:10:00'),
(107, 21, '2025-12-01 09:30:00'),
(107, 35, '2026-02-18 10:45:00'),
(108, 8, '2025-11-02 10:25:00'),
(108, 9, '2025-12-08 15:20:00'),
(108, 42, '2026-03-20 11:30:00'),
(109, 10, '2025-11-05 16:50:00'),
(109, 20, '2025-12-15 10:00:00'),
(109, 30, '2026-04-05 14:15:00'),
(110, 1, '2025-11-10 09:15:00'),
(110, 2, '2025-11-12 11:30:00'),
(110, 3, '2025-11-15 14:45:00'),
(110, 4, '2025-11-20 16:00:00'),
(110, 5, '2025-11-25 08:30:00'),
(111, 11, '2025-12-01 10:10:00'),
(111, 22, '2026-01-10 15:20:00'),
(111, 33, '2026-03-05 09:40:00'),
(112, 12, '2025-12-05 14:30:00'),
(112, 25, '2026-01-15 11:00:00'),
(112, 50, '2026-03-12 16:15:00'),
(113, 13, '2025-12-10 09:45:00'),
(113, 26, '2026-01-20 14:10:00'),
(113, 39, '2026-04-02 10:30:00'),
(114, 14, '2025-12-15 16:00:00'),
(114, 28, '2026-02-05 08:50:00'),
(115, 15, '2025-12-20 11:20:00'),
(115, 30, '2026-02-12 15:30:00'),
(115, 45, '2026-04-18 09:15:00'),
(116, 16, '2026-01-05 14:15:00'),
(116, 32, '2026-02-20 10:45:00'),
(117, 17, '2026-01-10 09:30:00'),
(117, 34, '2026-03-01 16:00:00'),
(117, 49, '2026-05-02 11:10:00'),
(118, 18, '2026-01-15 15:40:00'),
(118, 36, '2026-03-08 08:25:00'),
(119, 19, '2026-01-20 10:05:00'),
(119, 38, '2026-03-15 14:50:00'),
(120, 20, '2026-01-25 16:20:00'),
(120, 40, '2026-04-05 09:35:00'),
(121, 21, '2026-02-01 11:30:00'),
(121, 42, '2026-04-12 15:15:00'),
(122, 22, '2026-02-05 14:45:00'),
(122, 44, '2026-04-20 10:00:00'),
(123, 23, '2026-02-10 09:10:00'),
(123, 46, '2026-04-28 16:30:00'),
(124, 24, '2026-02-15 15:25:00'),
(124, 48, '2026-05-05 11:45:00'),
(125, 25, '2026-02-20 10:40:00'),
(125, 50, '2026-05-08 08:50:00'),
(126, 1, '2026-02-25 16:00:00'),
(126, 10, '2026-03-10 09:15:00'),
(126, 20, '2026-04-15 14:30:00'),
(127, 2, '2026-03-02 11:20:00'),
(127, 11, '2026-04-05 16:10:00'),
(128, 3, '2026-03-08 14:50:00'),
(128, 12, '2026-04-12 08:40:00'),
(129, 4, '2026-03-15 09:35:00'),
(129, 13, '2026-04-20 15:20:00'),
(130, 5, '2026-03-22 15:15:00'),
(130, 14, '2026-04-28 10:05:00'),
(130, 25, '2026-05-02 16:45:00'),
(131, 6, '2026-03-28 10:00:00'),
(131, 15, '2026-05-01 14:10:00'),
(132, 7, '2026-04-05 16:30:00'),
(132, 16, '2026-05-05 09:20:00'),
(133, 8, '2026-04-10 11:10:00'),
(133, 17, '2026-05-08 15:30:00'),
(134, 9, '2026-04-15 14:45:00'),
(134, 18, '2026-05-10 10:40:00'),
(135, 10, '2026-04-20 09:20:00'),
(135, 19, '2026-05-12 16:15:00'),
(136, 11, '2026-04-25 15:00:00'),
(136, 20, '2026-05-15 11:30:00'),
(137, 12, '2026-04-28 10:15:00'),
(137, 21, '2026-05-18 14:45:00'),
(138, 13, '2026-05-02 16:25:00'),
(138, 22, '2026-05-20 09:00:00'),
(139, 14, '2026-05-05 11:40:00'),
(139, 23, '2026-05-22 15:10:00'),
(140, 15, '2026-05-08 14:50:00'),
(140, 24, '2026-05-25 10:20:00'),
(140, 35, '2026-06-01 16:30:00'),
(141, 16, '2026-05-10 09:10:00'),
(141, 25, '2026-05-28 14:15:00'),
(142, 17, '2026-05-12 15:35:00'),
(142, 26, '2026-06-05 10:05:00'),
(143, 18, '2026-05-15 10:25:00'),
(143, 27, '2026-06-10 15:40:00'),
(144, 19, '2026-05-18 16:00:00'),
(144, 28, '2026-06-15 09:50:00'),
(145, 20, '2026-05-20 11:15:00'),
(145, 29, '2026-06-20 14:25:00'),
(145, 40, '2026-06-25 10:30:00');

-- --------------------------------------------------------

--
-- Structure de la table `MESSAGE_ADMIN`
--

CREATE TABLE `MESSAGE_ADMIN` (
  `id_message` int(11) NOT NULL,
  `contenu` varchar(250) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `id_utilisateur1` int(11) DEFAULT NULL,
  `id_utilisateur2` int(11) DEFAULT NULL,
  `est_lu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `MESSAGE_PRESTATAIRE`
--

CREATE TABLE `MESSAGE_PRESTATAIRE` (
  `id_message` int(11) NOT NULL,
  `contenu` varchar(250) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `id_prestataire` int(11) DEFAULT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `expediteur` tinyint(1) DEFAULT 0,
  `est_lu` tinyint(1) DEFAULT 0,
  `id_service` int(11) DEFAULT NULL,
  `id_disponibilite` int(11) DEFAULT NULL,
  `prix_propose` double DEFAULT NULL,
  `etat_offre` enum('en_attente','accepte','refuse','expire') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `PAIEMENT`
--

CREATE TABLE `PAIEMENT` (
  `id_paiement` int(11) NOT NULL,
  `prix` double DEFAULT NULL,
  `date_paiement` datetime DEFAULT current_timestamp(),
  `statut` enum('en_attente','valide','refuse','rembourse') DEFAULT NULL,
  `mode_paiement` enum('carte','cheque','prelevement') DEFAULT NULL,
  `url_facture` varchar(500) DEFAULT NULL,
  `stripe_pi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `PAIEMENT`
--

INSERT INTO `PAIEMENT` (`id_paiement`, `prix`, `date_paiement`, `statut`, `mode_paiement`, `url_facture`, `stripe_pi`) VALUES
(1, 4, '2026-04-21 10:00:00', 'valide', 'carte', NULL, NULL),
(2, 4, '2026-05-01 11:00:00', 'valide', 'prelevement', NULL, NULL),
(3, 4, '2026-05-02 09:00:00', 'valide', 'carte', NULL, NULL),
(4, 4, '2026-05-02 14:00:00', 'valide', 'carte', NULL, NULL),
(5, 4, '2026-05-03 10:30:00', 'valide', 'prelevement', NULL, NULL),
(6, 4, '2026-05-03 16:00:00', 'valide', 'carte', NULL, NULL),
(7, 4, '2026-05-04 08:45:00', 'valide', 'carte', NULL, NULL),
(8, 4, '2026-05-04 11:20:00', 'valide', 'prelevement', NULL, NULL),
(9, 4, '2026-05-05 09:15:00', 'valide', 'carte', NULL, NULL),
(10, 4, '2026-05-05 14:40:00', 'valide', 'carte', NULL, NULL),
(11, 4, '2026-05-01 10:00:00', 'valide', 'carte', NULL, NULL),
(12, 4, '2026-05-01 11:00:00', 'valide', 'prelevement', NULL, NULL),
(13, 4, '2026-05-02 09:00:00', 'valide', 'carte', NULL, NULL),
(14, 4, '2026-05-02 14:00:00', 'valide', 'carte', NULL, NULL),
(15, 4, '2026-05-03 10:30:00', 'valide', 'prelevement', NULL, NULL),
(16, 4, '2026-05-03 16:00:00', 'valide', 'carte', NULL, NULL),
(17, 4, '2026-05-04 08:45:00', 'valide', 'carte', NULL, NULL),
(18, 4, '2026-05-04 11:20:00', 'valide', 'prelevement', NULL, NULL),
(19, 4, '2026-05-05 09:15:00', 'valide', 'carte', NULL, NULL),
(20, 4, '2026-05-05 14:40:00', 'valide', 'carte', NULL, NULL),
(21, 4, '2026-05-06 10:00:00', 'valide', 'carte', NULL, NULL),
(22, 4, '2026-05-06 11:00:00', 'valide', 'prelevement', NULL, NULL),
(23, 4, '2026-05-07 09:00:00', 'valide', 'carte', NULL, NULL),
(24, 4, '2026-05-07 14:00:00', 'valide', 'carte', NULL, NULL),
(25, 4, '2026-05-08 10:30:00', 'valide', 'prelevement', NULL, NULL),
(26, 40, '2026-05-01 10:00:00', 'valide', 'carte', NULL, NULL),
(27, 40, '2026-05-02 11:30:00', 'valide', 'prelevement', NULL, NULL),
(28, 40, '2026-05-03 09:15:00', 'valide', 'carte', NULL, NULL),
(29, 40, '2026-05-04 14:20:00', 'valide', 'carte', NULL, NULL),
(30, 40, '2026-05-05 16:45:00', 'valide', 'prelevement', NULL, NULL),
(31, 40, '2026-05-06 10:10:00', 'valide', 'carte', NULL, NULL),
(32, 40, '2026-05-07 11:00:00', 'valide', 'carte', NULL, NULL),
(33, 40, '2026-05-08 08:30:00', 'valide', 'prelevement', NULL, NULL),
(34, 40, '2026-05-01 15:20:00', 'valide', 'carte', NULL, NULL),
(35, 40, '2026-05-02 09:05:00', 'valide', 'carte', NULL, NULL),
(36, 4, '2026-05-08 23:16:57', 'valide', 'carte', 'https://invoice.stripe.com/i/acct_1TApyvRjlN11pOwE/test_YWNjdF8xVEFweXZSamxOMTFwT3dFLF9VVHRZZWJTR2tHUHFXNnN6T0NrVVZsMHFISDI1N0pNLDE2ODgxNTgxNw0200ffUMN8Sd?s=ap', NULL),
(37, 4, '2026-05-01 08:00:00', 'valide', 'carte', NULL, NULL),
(38, 4, '2026-05-01 09:30:00', 'valide', 'prelevement', NULL, NULL),
(39, 4, '2026-05-02 10:15:00', 'valide', 'carte', NULL, NULL),
(40, 4, '2026-05-02 11:45:00', 'valide', 'prelevement', NULL, NULL),
(41, 4, '2026-05-03 14:20:00', 'valide', 'carte', NULL, NULL),
(42, 4, '2026-05-03 16:10:00', 'valide', 'carte', NULL, NULL),
(43, 4, '2026-05-04 09:05:00', 'valide', 'prelevement', NULL, NULL),
(44, 4, '2026-05-04 15:30:00', 'valide', 'carte', NULL, NULL),
(45, 4, '2026-05-05 10:45:00', 'valide', 'carte', NULL, NULL),
(46, 4, '2026-05-05 11:20:00', 'valide', 'prelevement', NULL, NULL),
(47, 4, '2026-05-06 14:00:00', 'valide', 'carte', NULL, NULL),
(48, 4, '2026-05-06 16:30:00', 'valide', 'prelevement', NULL, NULL),
(49, 4, '2026-05-07 09:15:00', 'valide', 'carte', NULL, NULL),
(50, 4, '2026-05-07 10:50:00', 'valide', 'carte', NULL, NULL),
(51, 4, '2026-05-08 11:10:00', 'valide', 'prelevement', NULL, NULL),
(52, 40, '2026-05-01 10:00:00', 'valide', 'carte', NULL, NULL),
(53, 40, '2026-05-02 14:30:00', 'valide', 'prelevement', NULL, NULL),
(54, 40, '2026-05-03 09:45:00', 'valide', 'carte', NULL, NULL),
(55, 40, '2026-05-04 16:20:00', 'valide', 'carte', NULL, NULL),
(56, 40, '2026-05-05 11:10:00', 'valide', 'prelevement', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `PANIER`
--

CREATE TABLE `PANIER` (
  `id_panier` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `id_produit` int(11) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `date_reservation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `PRESTATAIRE`
--

CREATE TABLE `PRESTATAIRE` (
  `id_prestataire` int(11) NOT NULL,
  `siret` varchar(50) DEFAULT NULL,
  `prenom` varchar(55) DEFAULT NULL,
  `nom` varchar(55) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mdp` varchar(250) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `num_telephone` varchar(20) DEFAULT NULL,
  `status` enum('en attente','validé','refusé') DEFAULT 'en attente',
  `motif_refus` varchar(250) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `id_abonnement` int(11) DEFAULT NULL,
  `id_categorie` int(11) NOT NULL,
  `debut_abonnement` datetime DEFAULT NULL,
  `date_fin_boost` datetime DEFAULT NULL,
  `date_fin_boost_profil` datetime DEFAULT NULL,
  `stripe_account_id` varchar(255) DEFAULT NULL,
  `onesignal_player_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `PRESTATAIRE`
--

INSERT INTO `PRESTATAIRE` (`id_prestataire`, `siret`, `prenom`, `nom`, `email`, `mdp`, `date_naissance`, `num_telephone`, `status`, `motif_refus`, `date_creation`, `id_abonnement`, `id_categorie`, `debut_abonnement`, `date_fin_boost`, `date_fin_boost_profil`, `stripe_account_id`, `onesignal_player_id`) VALUES
(51, '11111111110001', 'Marc', 'Lemoine', 'marc.plombier@pro.fr', 'hash_mdp', '1985-04-12', '0610203040', 'validé', NULL, '2026-05-08 23:34:42', 36, 1, '2026-05-01 08:00:00', NULL, NULL, NULL, NULL),
(52, '11111111110002', 'Sophie', 'Dubois', 'sophie.jardin@pro.fr', 'hash_mdp', '1990-07-25', '0620304050', 'validé', NULL, '2026-05-08 23:34:42', 37, 2, '2026-05-01 09:30:00', NULL, NULL, NULL, NULL),
(53, '11111111110003', 'Luc', 'Bernard', 'luc.informatique@pro.fr', 'hash_mdp', '1982-11-05', '0630405060', 'validé', NULL, '2026-05-08 23:34:42', 38, 3, '2026-05-02 10:15:00', NULL, NULL, NULL, NULL),
(54, '11111111110004', 'Julie', 'Martin', 'julie.menage@pro.fr', 'hash_mdp', '1978-02-14', '0640506070', 'validé', NULL, '2026-05-08 23:34:42', 39, 1, '2026-05-02 11:45:00', NULL, NULL, NULL, NULL),
(55, '11111111110005', 'Antoine', 'Petit', 'antoine.bricolage@pro.fr', 'hash_mdp', '1988-09-22', '0650607080', 'validé', NULL, '2026-05-08 23:34:42', 40, 2, '2026-05-03 14:20:00', NULL, NULL, NULL, NULL),
(56, '11111111110006', 'Claire', 'Robert', 'claire.coiffure@pro.fr', 'hash_mdp', '1995-12-08', '0660708090', 'validé', NULL, '2026-05-08 23:34:42', 41, 4, '2026-05-03 16:10:00', NULL, NULL, NULL, NULL),
(57, '11111111110007', 'Thomas', 'Richard', 'thomas.peinture@pro.fr', 'hash_mdp', '1980-05-17', '0670809000', 'validé', NULL, '2026-05-08 23:34:42', 42, 1, '2026-05-04 09:05:00', NULL, NULL, NULL, NULL),
(58, '11111111110008', 'Élodie', 'Durand', 'elodie.courses@pro.fr', 'hash_mdp', '1992-03-30', '0680900010', 'validé', NULL, '2026-05-08 23:34:42', 43, 5, '2026-05-04 15:30:00', NULL, NULL, NULL, NULL),
(59, '11111111110009', 'Nicolas', 'Leroy', 'nicolas.jardin@pro.fr', 'hash_mdp', '1975-08-11', '0690001020', 'validé', NULL, '2026-05-08 23:34:42', 44, 2, '2026-05-05 10:45:00', NULL, NULL, NULL, NULL),
(60, '11111111110010', 'Céline', 'Moreau', 'celine.couture@pro.fr', 'hash_mdp', '1986-01-24', '0611223344', 'validé', NULL, '2026-05-08 23:34:42', 45, 4, '2026-05-05 11:20:00', NULL, NULL, NULL, NULL),
(61, '11111111110011', 'Julien', 'Simon', 'julien.electricien@pro.fr', 'hash_mdp', '1981-10-09', '0622334455', 'validé', NULL, '2026-05-08 23:34:42', 46, 1, '2026-05-06 14:00:00', NULL, NULL, NULL, NULL),
(62, '11111111110012', 'Aurélie', 'Laurent', 'aurelie.aide@pro.fr', 'hash_mdp', '1989-06-15', '0633445566', 'validé', NULL, '2026-05-08 23:34:42', 47, 5, '2026-05-06 16:30:00', NULL, NULL, NULL, NULL),
(63, '11111111110013', 'Maxime', 'Lefebvre', 'maxime.coach@pro.fr', 'hash_mdp', '1993-02-28', '0644556677', 'validé', NULL, '2026-05-08 23:34:42', 48, 3, '2026-05-07 09:15:00', NULL, NULL, NULL, NULL),
(64, '11111111110014', 'Sandrine', 'Michel', 'sandrine.beaute@pro.fr', 'hash_mdp', '1977-11-19', '0655667788', 'validé', NULL, '2026-05-08 23:34:42', 49, 4, '2026-05-07 10:50:00', NULL, NULL, NULL, NULL),
(65, '11111111110015', 'David', 'Garcia', 'david.serrurier@pro.fr', 'hash_mdp', '1984-07-03', '0666778899', 'validé', NULL, '2026-05-08 23:34:42', 50, 1, '2026-05-08 11:10:00', NULL, NULL, NULL, NULL),
(66, '11111111110016', 'Valérie', 'David', 'valerie.admin@pro.fr', 'hash_mdp', '1988-12-14', '0677889900', 'validé', NULL, '2026-05-08 23:34:42', 51, 3, '2026-05-01 10:00:00', NULL, NULL, NULL, NULL),
(67, '11111111110017', 'Christophe', 'Bertrand', 'christophe.chauffeur@pro.fr', 'hash_mdp', '1979-05-21', '0688990011', 'validé', NULL, '2026-05-08 23:34:42', 52, 5, '2026-05-02 14:30:00', NULL, NULL, NULL, NULL),
(68, '11111111110018', 'Laetitia', 'Roux', 'laetitia.animaux@pro.fr', 'hash_mdp', '1991-09-07', '0699001122', 'validé', NULL, '2026-05-08 23:34:42', 53, 2, '2026-05-03 09:45:00', NULL, NULL, NULL, NULL),
(69, '11111111110019', 'Stéphane', 'Vincent', 'stephane.reparateur@pro.fr', 'hash_mdp', '1983-01-16', '0612345678', 'validé', NULL, '2026-05-08 23:34:42', 54, 1, '2026-05-04 16:20:00', NULL, NULL, NULL, NULL),
(70, '11111111110020', 'Nathalie', 'Fournier', 'nathalie.livraison@pro.fr', 'hash_mdp', '1987-08-29', '0623456789', 'validé', NULL, '2026-05-08 23:34:42', 55, 5, '2026-05-05 11:10:00', NULL, NULL, NULL, NULL),
(71, '11111111110021', 'Pierre', 'Girard', 'pierre.g@pro.fr', 'hash_mdp', '1980-11-12', '0634567890', 'validé', NULL, '2026-05-08 23:34:42', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(72, '11111111110022', 'Marie', 'Andre', 'marie.a@pro.fr', 'hash_mdp', '1994-05-24', '0645678901', 'validé', NULL, '2026-05-08 23:34:42', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(73, '11111111110023', 'Paul', 'Lefevre', 'paul.l@pro.fr', 'hash_mdp', '1976-02-08', '0656789012', 'validé', NULL, '2026-05-08 23:34:42', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(74, '11111111110024', 'Anna', 'Mercier', 'anna.m@pro.fr', 'hash_mdp', '1989-09-17', '0667890123', 'validé', NULL, '2026-05-08 23:34:42', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(75, '11111111110025', 'Jacques', 'Blanc', 'jacques.b@pro.fr', 'hash_mdp', '1983-06-03', '0678901234', 'validé', NULL, '2026-05-08 23:34:42', NULL, 5, NULL, NULL, NULL, NULL, NULL),
(76, '11111111110026', 'Louise', 'Guerin', 'louise.g@pro.fr', 'hash_mdp', '1991-12-21', '0689012345', 'validé', NULL, '2026-05-08 23:34:42', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(77, '11111111110027', 'Victor', 'Boyer', 'victor.b@pro.fr', 'hash_mdp', '1985-04-30', '0690123456', 'validé', NULL, '2026-05-08 23:34:42', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(78, '11111111110028', 'Alice', 'Garnier', 'alice.g@pro.fr', 'hash_mdp', '1979-10-14', '0601234567', 'validé', NULL, '2026-05-08 23:34:42', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(79, '11111111110029', 'Hugo', 'Chevalier', 'hugo.c@pro.fr', 'hash_mdp', '1996-01-07', '0612345670', 'validé', NULL, '2026-05-08 23:34:42', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(80, '11111111110030', 'Emma', 'Francois', 'emma.f@pro.fr', 'hash_mdp', '1982-08-19', '0623456701', 'validé', NULL, '2026-05-08 23:34:42', NULL, 5, NULL, NULL, NULL, NULL, NULL),
(81, '11111111110031', 'Arthur', 'Legrand', 'arthur.l@pro.fr', 'hash_mdp', '1990-03-25', '0634567012', 'validé', NULL, '2026-05-08 23:34:42', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(82, '11111111110032', 'Chloé', 'Gauthier', 'chloe.g@pro.fr', 'hash_mdp', '1987-11-06', '0645670123', 'validé', NULL, '2026-05-08 23:34:42', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(83, '11111111110033', 'Gabriel', 'Perrin', 'gabriel.p@pro.fr', 'hash_mdp', '1984-07-15', '0656701234', 'validé', NULL, '2026-05-08 23:34:42', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(84, '11111111110034', 'Inès', 'Robin', 'ines.r@pro.fr', 'hash_mdp', '1993-02-02', '0667012345', 'validé', NULL, '2026-05-08 23:34:42', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(85, '11111111110035', 'Léo', 'Clement', 'leo.c@pro.fr', 'hash_mdp', '1978-09-28', '0670123456', 'validé', NULL, '2026-05-08 23:34:42', NULL, 5, NULL, NULL, NULL, NULL, NULL),
(86, '11111111110036', 'Manon', 'Morin', 'manon.m@pro.fr', 'hash_mdp', '1986-05-11', '0681234567', 'refusé', 'Numéro de SIRET invalide ou inactif', '2026-05-08 23:34:42', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(87, '11111111110037', 'Maël', 'Roussel', 'mael.r@pro.fr', 'hash_mdp', '1992-12-04', '0692345678', 'refusé', 'Absence d\'assurance responsabilité civile', '2026-05-08 23:34:42', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(88, '11111111110038', 'Lina', 'Mathieu', 'lina.m@pro.fr', 'hash_mdp', '1981-08-22', '0603456789', 'refusé', 'Pièce d\'identité non lisible', '2026-05-08 23:34:42', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(89, '11111111110039', 'Louis', 'Gautier', 'louis.g@pro.fr', 'hash_mdp', '1995-01-14', '0614567890', 'refusé', 'Activité non couverte par notre plateforme', '2026-05-08 23:34:42', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(90, '11111111110040', 'Jade', 'Marchand', 'jade.m@pro.fr', 'hash_mdp', '1977-06-30', '0625678901', 'refusé', 'Suspicion de fraude documentaire', '2026-05-08 23:34:42', NULL, 5, NULL, NULL, NULL, NULL, NULL),
(91, '11111111110041', 'Ethan', 'Dufour', 'ethan.d@pro.fr', 'hash_mdp', '1988-10-18', '0636789012', 'refusé', 'Preuve de domiciliation manquante', '2026-05-08 23:34:42', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(92, '11111111110042', 'Mila', 'Dumas', 'mila.d@pro.fr', 'hash_mdp', '1991-04-05', '0647890123', 'refusé', 'Extrait Kbis de plus de 3 mois', '2026-05-08 23:34:42', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(93, '11111111110043', 'Adam', 'Fontaine', 'adam.f@pro.fr', 'hash_mdp', '1983-11-27', '0658901234', 'refusé', 'Incohérence entre nom et SIRET', '2026-05-08 23:34:42', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(94, '11111111110044', 'Rose', 'Rousseau', 'rose.r@pro.fr', 'hash_mdp', '1994-07-09', '0669012345', 'refusé', 'Casier judiciaire requis pour ce secteur', '2026-05-08 23:34:42', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(95, '11111111110045', 'Nolan', 'Vincent', 'nolan.v@pro.fr', 'hash_mdp', '1980-02-16', '0670123456', 'refusé', 'Document professionnel falsifié', '2026-05-08 23:34:42', NULL, 5, NULL, NULL, NULL, NULL, NULL),
(96, '11111111110046', 'Ambre', 'Muller', 'ambre.m@pro.fr', 'hash_mdp', '1997-09-01', '0681234560', 'en attente', NULL, '2026-05-08 23:34:42', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(97, '11111111110047', 'Raphaël', 'Lefevre', 'raphael.l@pro.fr', 'hash_mdp', '1985-05-13', '0692345601', 'en attente', NULL, '2026-05-08 23:34:42', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(98, '11111111110048', 'Mia', 'Blanc', 'mia.b@pro.fr', 'hash_mdp', '1990-12-25', '0603456012', 'en attente', NULL, '2026-05-08 23:34:42', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(99, '11111111110049', 'Paul', 'Garnier', 'paul.g2@pro.fr', 'hash_mdp', '1982-08-08', '0614560123', 'en attente', NULL, '2026-05-08 23:34:42', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(100, '11111111110050', 'Julia', 'Chevalier', 'julia.c@pro.fr', 'hash_mdp', '1993-04-19', '0625601234', 'en attente', NULL, '2026-05-08 23:34:42', NULL, 5, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `PRESTATAIRE_EVENEMENT`
--

CREATE TABLE `PRESTATAIRE_EVENEMENT` (
  `id_prestataire` int(11) NOT NULL,
  `id_evenement` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `PRESTATAIRE_EVENEMENT`
--

INSERT INTO `PRESTATAIRE_EVENEMENT` (`id_prestataire`, `id_evenement`) VALUES
(51, 1),
(51, 2),
(52, 3),
(52, 4),
(53, 5),
(53, 6),
(54, 7),
(54, 8),
(55, 9),
(55, 10),
(56, 11),
(56, 12),
(57, 13),
(57, 14),
(58, 15),
(58, 16),
(59, 17),
(59, 18),
(60, 19),
(60, 20),
(61, 21),
(61, 22),
(62, 23),
(62, 24),
(63, 25),
(63, 26),
(64, 27),
(64, 28),
(65, 29),
(65, 30),
(66, 31),
(66, 32),
(67, 33),
(67, 34),
(68, 35),
(68, 36),
(69, 37),
(69, 38),
(70, 39),
(70, 40),
(71, 41),
(71, 42),
(72, 43),
(72, 44),
(73, 45),
(73, 46),
(74, 47),
(74, 48),
(75, 49),
(75, 50),
(76, 51),
(76, 52),
(77, 53),
(77, 54),
(78, 55),
(78, 56),
(79, 57),
(79, 58),
(80, 59),
(80, 60),
(81, 61),
(81, 62),
(82, 63),
(82, 64),
(83, 65),
(83, 66),
(84, 67),
(84, 68),
(85, 69),
(85, 70),
(86, 71),
(86, 72),
(87, 73),
(87, 74),
(88, 75),
(88, 76),
(89, 77),
(89, 78),
(90, 79),
(90, 80),
(91, 81),
(91, 82),
(92, 83),
(92, 84),
(93, 85),
(93, 86),
(94, 87),
(94, 88),
(95, 89),
(95, 90),
(96, 91),
(96, 92),
(97, 93),
(97, 94),
(98, 95),
(98, 96),
(99, 97),
(99, 98),
(100, 99),
(100, 100);

-- --------------------------------------------------------

--
-- Structure de la table `PRODUIT`
--

CREATE TABLE `PRODUIT` (
  `id_produit` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `prix` double DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `statut` tinyint(1) DEFAULT 1,
  `image` varchar(250) DEFAULT NULL,
  `date_ajout` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `PRODUIT`
--

INSERT INTO `PRODUIT` (`id_produit`, `nom`, `description`, `prix`, `stock`, `statut`, `image`, `date_ajout`) VALUES
(31, 'Canne de marche pliante', 'Canne légère en aluminium avec poignée ergonomique et embout antidérapant sécurisé.', 25.5, 45, 1, 'uploads/1778277599_shopping (9).webp', '2026-05-08'),
(32, 'Pilulier semainier', 'Pilulier grand format avec 4 cases par jour (matin, midi, soir, nuit) et couleurs distinctes.', 12.9, 100, 1, 'uploads/1778277595_shopping (8).webp', '2026-05-08'),
(33, 'Téléphone à grosses touches', 'Téléphone fixe amplifié avec touches très larges et 3 numéros d&#39;urgence préprogrammables.', 49.99, 30, 1, 'uploads/1778277590_téléchargement (1).jpg', '2026-05-08'),
(34, 'Tapis de bain antidérapant', 'Tapis en caoutchouc extra-long avec ventouses puissantes pour sécuriser la douche.', 18, 85, 1, 'uploads/1778277585_shopping (7).webp', '2026-05-08'),
(35, 'Loupe de lecture éclairante', 'Loupe avec éclairage LED intégré, grossissement x3, idéale pour lire les petits caractères.', 22.5, 60, 1, 'uploads/1778277577_shopping (6).webp', '2026-05-08'),
(36, 'Coussin lombaire à mémoire de forme', 'Coussin ergonomique pour un maintien parfait du dos dans le canapé ou en voiture.', 35, 40, 1, 'uploads/1778277572_shopping (5).webp', '2026-05-08'),
(37, 'Montre connectée SOS', 'Montre simple d&#39;utilisation avec détection de chute, GPS et bouton SOS pour prévenir les proches.', 129.9, 15, 1, 'uploads/1778277567_shopping (4).webp', '2026-05-08'),
(38, 'Tabouret de douche réglable', 'Assise perforée pour l&#39;évacuation de l&#39;eau, pieds antidérapants et hauteur ajustable.', 45.9, 25, 1, 'uploads/1778277563_shopping (3).webp', '2026-05-08'),
(39, 'Ouvre-boîte électrique automatique', 'Ouvre-boîte sans effort, parfait pour soulager les articulations et l&#39;arthrose des mains.', 24.9, 50, 1, 'uploads/1778277558_shopping (2).webp', '2026-05-08'),
(40, 'Plaid chauffant électrique', 'Couverture polaire douce avec 3 niveaux de chauffe et arrêt automatique de sécurité.', 55, 35, 1, 'uploads/1778277552_shopping (1).webp', '2026-05-08'),
(41, 'Chausse-pied long manche', 'Chausse-pied en métal de 70 cm pour enfiler ses chaussures sans avoir à se baisser.', 9.5, 120, 1, 'uploads/1778277185_shopping (7).webp', '2026-05-08'),
(42, 'Enfile-bouton et crochet', 'Outil d&#39;aide à l&#39;habillage pour boutonner facilement les chemises ou fermer les fermetures éclair.', 8.9, 80, 1, 'uploads/1778277180_téléchargement (6).jpg', '2026-05-08'),
(43, 'Barre d&#39;appui à ventouses', 'S&#39;installe en quelques secondes sans percer le mur, avec témoin de verrouillage de sécurité.', 15.9, 65, 1, 'uploads/1778277190_shopping (6).webp', '2026-05-08'),
(44, 'Jeu de cartes grands caractères', 'Jeu de 54 cartes traditionnel avec des index géants pour une lecture facilitée sans lunettes.', 6.5, 150, 1, 'uploads/1778277196_shopping (5).webp', '2026-05-08'),
(45, 'Pèse-personne vocal', 'Balance parlante en français avec un grand plateau en verre sécurisé et antidérapant.', 39.9, 40, 1, 'uploads/1778277048_téléchargement (2).jpg', '2026-05-08'),
(46, 'Tensiomètre au bras', 'Appareil médical simple d&#39;utilisation avec grand écran LCD et lecture vocale des résultats.', 59, 25, 1, 'uploads/1778277053_shopping (4).webp', '2026-05-08'),
(47, 'Lampe de lecture à pince', 'Petite lampe LED flexible à accrocher sur un livre ou une liseuse, rechargeable par USB.', 14.9, 70, 1, 'uploads/1778277058_shopping (3).webp', '2026-05-08'),
(48, 'Caddie de courses à 6 roues', 'Chariot monte-escalier très léger pour faire ses courses sans porter de charges lourdes.', 45, 30, 1, 'uploads/1778277002_shopping (2).webp', '2026-05-08'),
(49, 'Sécateur ergonomique', 'Sécateur de jardin à crémaillère démultipliant la force pour tailler sans se fatiguer la main.', 21, 45, 1, 'uploads/1778276981_shopping (1).webp', '2026-05-08'),
(50, 'Radio de table simplifiée', 'Radio FM/AM au design rétro avec de gros boutons rotatifs et une excellente qualité sonore.', 34.5, 35, 1, 'uploads/1778276974_téléchargement (1).jpg', '2026-05-08'),
(51, 'Ceinture lombaire chauffante', 'Ceinture sans fil avec batterie rechargeable pour soulager les douleurs de dos au quotidien.', 42, 25, 1, 'uploads/1778276915_shopping (10).jpg', '2026-05-08'),
(52, 'Réveil pour malentendants', 'Réveil avec alarme extra-forte, flash lumineux et coussin vibrant à glisser sous l&#39;oreiller.', 49.9, 20, 1, 'uploads/1778276910_shopping (9).jpg', '2026-05-08'),
(53, 'Pédalier d&#39;appartement', 'Mini-vélo d&#39;appartement pour entretenir ses articulations et sa circulation sanguine depuis son fauteuil.', 39.5, 40, 1, 'uploads/1778276905_shopping (8).jpg', '2026-05-08'),
(54, 'Coupe-ongles sur socle', 'Coupe-ongles avec loupe intégrée et large socle en plastique pour une utilisation facile à une main.', 16.9, 60, 1, 'uploads/1778276900_shopping (7).jpg', '2026-05-08'),
(55, 'Thermomètre frontal sans contact', 'Thermomètre précis et rapide avec code couleur (vert, orange, rouge) selon la température.', 29.9, 55, 1, 'uploads/1778276895_shopping (6).jpg', '2026-05-08'),
(56, 'Jeu de Scrabble géant', 'Plateau de jeu avec pions surdimensionnés et encastrés pour éviter de les bousculer par mégarde.', 45, 20, 1, 'uploads/1778276889_shopping (5).jpg', '2026-05-08'),
(57, 'Veilleuse avec détecteur de mouvement', 'S&#39;allume au moindre passage dans l&#39;obscurité pour sécuriser les déplacements nocturnes.', 12.5, 90, 1, 'uploads/1778276883_shopping (4).jpg', '2026-05-08'),
(58, 'Pince de préhension', 'Pince attrape-tout de 80 cm en aluminium léger pour ramasser des objets sans avoir à se pencher.', 14, 75, 1, 'uploads/1778276877_shopping (3).jpg', '2026-05-08'),
(59, 'Gobelet ergonomique à découpe', 'Verre adapté avec découpe nasale permettant de boire sans avoir à pencher la tête en arrière.', 7.5, 100, 1, 'uploads/1778276872_shopping (2).jpg', '2026-05-08'),
(60, 'Télécommande universelle simplifiée', 'Télécommande avec seulement 6 très gros boutons pour contrôler facilement la télévision.', 19.9, 50, 1, 'uploads/1778276867_shopping (1).jpg', '2026-05-08');

-- --------------------------------------------------------

--
-- Structure de la table `RESERVATION_SERVICE`
--

CREATE TABLE `RESERVATION_SERVICE` (
  `id_reservation` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_heure` datetime NOT NULL,
  `id_paiement` int(11) DEFAULT NULL,
  `prix_final` double DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `SERVICE`
--

CREATE TABLE `SERVICE` (
  `id_service` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `statut` enum('en_attente','accepte','refuse') DEFAULT 'en_attente',
  `motif_refus` varchar(250) DEFAULT NULL,
  `id_prestataire` int(11) NOT NULL,
  `prix` double NOT NULL DEFAULT 0,
  `duree` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `SERVICE`
--

INSERT INTO `SERVICE` (`id_service`, `nom`, `description`, `statut`, `motif_refus`, `id_prestataire`, `prix`, `duree`) VALUES
(1, 'Changement de joint', 'Remplacement d\'un joint de robinet fuyant', 'accepte', NULL, 51, 35, 30),
(2, 'Installation mitigeur', 'Pose d\'un nouveau robinet de cuisine', 'en_attente', NULL, 51, 55, 60),
(3, 'Tonte de pelouse', 'Tonte de moins de 100m2 avec ramassage', 'accepte', NULL, 52, 25, 60),
(4, 'Abattage arbre', 'Coupe de branches en hauteur', 'refuse', 'Travaux en hauteur interdits sans assurance spécifique', 52, 120, 120),
(5, 'Nettoyage PC', 'Suppression virus et optimisation du système', 'accepte', NULL, 53, 40, 60),
(6, 'Configuration imprimante', 'Branchement et installation Wi-Fi', 'accepte', NULL, 53, 25, 30),
(7, 'Ménage hebdomadaire', 'Passage de l\'aspirateur, sols et poussières', 'accepte', NULL, 54, 50, 120),
(8, 'Repassage chemises', 'Repassage soigné de chemises et pantalons', 'en_attente', NULL, 54, 25, 60),
(9, 'Montage meuble TV', 'Assemblage d\'un meuble en kit standard', 'accepte', NULL, 55, 30, 60),
(10, 'Pose cuisine', 'Montage et fixation de caissons', 'refuse', 'Description trop vague, merci de préciser la taille', 55, 90, 90),
(11, 'Brushing', 'Shampoing et brushing à domicile', 'accepte', NULL, 56, 20, 30),
(12, 'Coupe et coloration', 'Teinture sur mesure et coupe classique', 'accepte', NULL, 56, 55, 90),
(13, 'Peinture petite chambre', 'Application de 2 couches sur mur préparé', 'accepte', NULL, 57, 75, 120),
(14, 'Rebouchage trous', 'Enduit et lissage rapide de petits trous', 'en_attente', NULL, 57, 20, 30),
(15, 'Livraison drive', 'Récupération de vos courses au drive', 'accepte', NULL, 58, 15, 30),
(16, 'Aide au supermarché', 'Accompagnement en magasin et port des sacs', 'accepte', NULL, 58, 30, 90),
(17, 'Plantation rosiers', 'Mise en terre et ajout d\'engrais', 'refuse', 'Tarif proposé largement au-dessus du marché', 59, 100, 30),
(18, 'Désherbage manuel', 'Arrachage des mauvaises herbes des allées', 'accepte', NULL, 59, 25, 60),
(19, 'Ourlet simple', 'Ourlet de pantalon à la machine', 'accepte', NULL, 60, 15, 30),
(20, 'Reprise vêtement', 'Réparation invisible d\'un accroc', 'en_attente', NULL, 60, 25, 60),
(21, 'Changement ampoules', 'Remplacement d\'ampoules plafonnier', 'accepte', NULL, 61, 20, 60),
(22, 'Rénovation tableau', 'Mise aux normes électriques', 'refuse', 'Durée renseignée techniquement impossible (trop court)', 61, 60, 90),
(23, 'Aide à la toilette', 'Accompagnement bienveillant pour l\'hygiène', 'accepte', NULL, 62, 50, 120),
(24, 'Aide au repas', 'Préparation et aide à la prise du déjeuner', 'accepte', NULL, 62, 40, 120),
(25, 'Gym douce', 'Mouvements d\'assouplissement pour seniors', 'accepte', NULL, 63, 30, 60),
(26, 'Marche au bras', 'Promenade lente dans le quartier', 'en_attente', NULL, 63, 15, 30),
(27, 'Soin visage', 'Gommage doux et masque hydratant', 'accepte', NULL, 64, 45, 90),
(28, 'Manucure', 'Limage et pose de vernis classique', 'accepte', NULL, 64, 25, 60),
(29, 'Changement barillet', 'Remplacement cylindre serrure simple', 'accepte', NULL, 65, 45, 30),
(30, 'Ouverture porte', 'Ouverture de porte blindée claquée', 'refuse', 'Informations insuffisantes sur le type de serrure', 65, 90, 60),
(31, 'Tri administratif', 'Rangement des factures et courriers', 'accepte', NULL, 66, 60, 120),
(32, 'Rédaction lettre', 'Aide à l\'écriture d\'un courrier officiel', 'accepte', NULL, 66, 30, 60),
(33, 'Promenade chien', 'Balade en laisse au parc', 'en_attente', NULL, 67, 12, 60),
(34, 'Nourrir chat', 'Remplissage gamelle et litière', 'accepte', NULL, 67, 10, 30),
(35, 'Nettoyage terrasse', 'Passage du karcher sur dalles', 'accepte', NULL, 68, 50, 90),
(36, 'Nettoyage toiture', 'Démoussage des tuiles', 'refuse', 'Matériel de sécurité obligatoire non mentionné', 68, 100, 120),
(37, 'Recherche de chaînes', 'Mise à jour TNT sur téléviseur', 'accepte', NULL, 69, 25, 60),
(38, 'Explication TV', 'Comment utiliser les services replay', 'accepte', NULL, 69, 30, 60),
(39, 'Aiguisage couteaux', 'Aiguisage manuel avec pierre', 'accepte', NULL, 70, 15, 30),
(40, 'Diagnostic lave-linge', 'Recherche de la cause d\'une panne', 'en_attente', NULL, 70, 40, 90),
(41, 'Grand ménage', 'Nettoyage approfondi de toutes les pièces', 'accepte', NULL, 71, 90, 120),
(42, 'Poussière', 'Passage plumeau', 'refuse', 'Tarif en dessous du seuil minimum légal', 71, 5, 30),
(43, 'Chauffeur médical', 'Aller-retour à l\'hôpital avec attente', 'accepte', NULL, 72, 35, 60),
(44, 'Chauffeur gare', 'Transport et aide avec les valises', 'accepte', NULL, 72, 45, 90),
(45, 'Taille rosiers', 'Coupe de printemps', 'accepte', NULL, 73, 30, 60),
(46, 'Arrosage jardin', 'Arrosage pendant vos vacances', 'en_attente', NULL, 73, 25, 60),
(47, 'Lecture journal', 'Lecture à voix haute de la presse', 'accepte', NULL, 74, 15, 30),
(48, 'Partenaire cartes', 'Jouer à la belote ou au tarot', 'accepte', NULL, 74, 20, 60),
(49, 'Massage thérapeutique', 'Manipulation dos', 'refuse', 'Diplôme de kinésithérapeute non fourni', 75, 80, 120),
(50, 'Soin des pieds', 'Bain et hydratation', 'accepte', NULL, 75, 30, 60),
(51, 'Rangement cave', 'Tri et descente aux encombrants', 'accepte', NULL, 76, 45, 90),
(52, 'Changement ampoule', 'Remplacement basique', 'en_attente', NULL, 76, 10, 30),
(53, 'Garde malade', 'Présence et surveillance de jour', 'accepte', NULL, 77, 60, 120),
(54, 'Préparation dîner', 'Cuisine de repas simples', 'accepte', NULL, 77, 25, 60),
(55, 'Configuration Smartphone', 'Installation d\'applications utiles', 'accepte', NULL, 78, 20, 30),
(56, 'Création site web', 'Développement complet', 'refuse', 'Prestation inadaptée à notre clientèle senior', 78, 300, 60),
(57, 'Nettoyage vitres', 'Lavage intérieur et extérieur', 'accepte', NULL, 79, 55, 120),
(58, 'Nettoyage tapis', 'Shampouinage à sec', 'accepte', NULL, 79, 40, 90),
(59, 'Petite plomberie', 'Changement siphon', 'en_attente', NULL, 80, 35, 60),
(60, 'Débouchage WC', 'Intervention pompe manuelle', 'accepte', NULL, 80, 45, 60),
(61, 'Pose tringle rideau', 'Perçage et pose', 'accepte', NULL, 81, 25, 60),
(62, 'Démolition mur', 'Casser cloison', 'refuse', 'Prestation de gros œuvre non acceptée', 81, 150, 30),
(63, 'Mise en pot', 'Rempotage plantes balcon', 'accepte', NULL, 82, 35, 90),
(64, 'Ramassage feuilles', 'Balayage allées', 'accepte', NULL, 82, 25, 60),
(65, 'Coupe ongles', 'Manucure simple', 'accepte', NULL, 83, 15, 30),
(66, 'Maquillage', 'Mise en beauté complète', 'en_attente', NULL, 83, 50, 120),
(67, 'Couture bouton', 'Remplacement boutons', 'accepte', NULL, 84, 10, 60),
(68, 'Ajustement robe', 'Reprise des coutures latérales', 'accepte', NULL, 84, 30, 90),
(69, 'Garde de nuit', 'Surveillance nocturne', 'refuse', 'Votre statut ne permet pas le travail de nuit', 85, 100, 60),
(70, 'Lecture de contes', 'Pour vos petits-enfants', 'accepte', NULL, 85, 15, 30),
(71, 'Nettoyage voiture', 'Lavage intérieur aspirateur', 'accepte', NULL, 86, 45, 120),
(72, 'Lavage extérieur', 'Nettoyage carrosserie au jet', 'accepte', NULL, 86, 25, 60),
(73, 'Bricolage divers', 'Accrocher des cadres', 'en_attente', NULL, 87, 40, 90),
(74, 'Changement piles', 'Remplacement piles détecteur fumée', 'accepte', NULL, 87, 15, 30),
(75, 'Aide informatique', 'Tri des photos numériques', 'accepte', NULL, 88, 25, 60),
(76, 'Récupération données', 'Sur disque dur cassé', 'refuse', 'Durée trop courte pour ce type d\'intervention technique', 88, 80, 120),
(77, 'Livraison pharmacie', 'Récupération d\'ordonnance', 'accepte', NULL, 89, 10, 30),
(78, 'Accompagnement marche', 'Soutien à la mobilité', 'accepte', NULL, 89, 20, 60),
(79, 'Taille haie', 'Entretien haie basse', 'accepte', NULL, 90, 45, 90),
(80, 'Évacuation déchets', 'Aller-retour déchetterie', 'en_attente', NULL, 90, 30, 60),
(81, 'Ménage classique', 'Aspirateur et serpillère', 'accepte', NULL, 91, 20, 60),
(82, 'Nettoyage façade', 'Passage karcher', 'refuse', 'Matériel inadapté selon votre profil', 91, 50, 30),
(83, 'Conversation', 'Échange et compagnie autour d\'un thé', 'accepte', NULL, 92, 40, 120),
(84, 'Jeu d\'échecs', 'Partenaire de jeu de plateau', 'accepte', NULL, 92, 30, 90),
(85, 'Coiffure courte', 'Mise en plis simple', 'accepte', NULL, 93, 20, 30),
(86, 'Coloration', 'Application de teinture', 'en_attente', NULL, 93, 35, 60),
(87, 'Réparation vélo', 'Crevaison et réglage freins', 'accepte', NULL, 94, 25, 60),
(88, 'Nettoyage vélo', 'Dégraissage chaîne', 'accepte', NULL, 94, 40, 120),
(89, 'Plomberie gaz', 'Changement tuyau gaz', 'refuse', 'Certification gaz manquante à votre dossier', 95, 60, 90),
(90, 'Remplacement robinet', 'Changement robinet extérieur', 'accepte', NULL, 95, 30, 60),
(91, 'Assistance tablette', 'Télécharger des applications', 'accepte', NULL, 96, 15, 30),
(92, 'Appel visio', 'Configuration de Skype', 'accepte', NULL, 96, 15, 30),
(93, 'Garde animaux', 'Présence toute la journée', 'en_attente', NULL, 97, 50, 120),
(94, 'Promenade chien', 'Balade autour du pâté de maison', 'accepte', NULL, 97, 20, 60),
(95, 'Tri vêtements', 'Aide pour le dressing d\'hiver', 'accepte', NULL, 98, 40, 90),
(96, 'Vente brocante', 'Vente de vos objets sur internet', 'refuse', 'Prestation de revente interdite sur la plateforme', 98, 20, 60),
(97, 'Peinture chaise', 'Rénovation de petit mobilier', 'accepte', NULL, 99, 30, 60),
(98, 'Vernissage', 'Application de vernis sur table', 'accepte', NULL, 99, 15, 30),
(99, 'Gros ménage', 'Avant état des lieux ou visite', 'accepte', NULL, 100, 70, 120),
(100, 'Détartrage SDB', 'Nettoyage joints et robinetterie', 'en_attente', NULL, 100, 45, 90);

-- --------------------------------------------------------

--
-- Structure de la table `UTILISATEUR`
--

CREATE TABLE `UTILISATEUR` (
  `id_utilisateur` int(11) NOT NULL,
  `prenom` varchar(55) DEFAULT NULL,
  `nom` varchar(55) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mdp` varchar(250) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `num_telephone` varchar(20) DEFAULT NULL,
  `statut` enum('user','admin','banni', 'comptable') DEFAULT 'user',
  `date_creation` datetime DEFAULT current_timestamp(),
  `premiere_connexion` tinyint(1) DEFAULT 1,
  `motif_bannissement` varchar(100) DEFAULT NULL,
  `duree_bannissement` int(11) DEFAULT NULL,
  `id_adresse` int(11) NOT NULL,
  `id_abonnement` int(11) DEFAULT NULL,
  `debut_abonnement` datetime DEFAULT NULL,
  `onesignal_player_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `UTILISATEUR`
--

INSERT INTO `UTILISATEUR` (`id_utilisateur`, `prenom`, `nom`, `email`, `mdp`, `date_naissance`, `num_telephone`, `statut`, `date_creation`, `premiere_connexion`, `motif_bannissement`, `duree_bannissement`, `id_adresse`, `id_abonnement`, `debut_abonnement`, `onesignal_player_id`) VALUES
(101, 'Jean', 'Dupont', 'jean.dupont@email.fr', 'hash_mdp', '1945-03-12', '0601020304', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 1, 1, '2026-05-01 10:00:00', NULL),
(102, 'Monique', 'Martin', 'monique.m@email.fr', 'hash_mdp', '1948-07-21', '0611223344', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 2, 2, '2026-05-02 11:30:00', NULL),
(103, 'Jacques', 'Bernard', 'jacques.b@email.fr', 'hash_mdp', '1942-11-05', '0622334455', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 3, 3, '2026-05-03 09:15:00', NULL),
(104, 'Nicole', 'Dubois', 'nicole.d@email.fr', 'hash_mdp', '1950-01-30', '0633445566', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 4, 4, '2026-05-03 14:20:00', NULL),
(105, 'Bernard', 'Thomas', 'bernard.t@email.fr', 'hash_mdp', '1947-09-14', '0644556677', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 5, 5, '2026-05-04 16:45:00', NULL),
(106, 'Jacqueline', 'Robert', 'jacqueline.r@email.fr', 'hash_mdp', '1941-04-22', '0655667788', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 6, 6, '2026-05-05 10:10:00', NULL),
(107, 'Michel', 'Richard', 'michel.r@email.fr', 'hash_mdp', '1949-12-08', '0666778899', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 7, 7, '2026-05-05 11:00:00', NULL),
(108, 'Françoise', 'Petit', 'francoise.p@email.fr', 'hash_mdp', '1944-06-17', '0677889900', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 8, 8, '2026-05-06 08:30:00', NULL),
(109, 'Pierre', 'Durand', 'pierre.d@email.fr', 'hash_mdp', '1940-02-25', '0688990011', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 9, 9, '2026-05-07 15:20:00', NULL),
(110, 'Marie', 'Leroy', 'marie.l@email.fr', 'hash_mdp', '1946-08-03', '0699001122', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 10, 10, '2026-05-08 09:05:00', NULL),
(111, 'Alain', 'Moreau', 'alain.m@email.fr', 'hash_mdp', '1952-10-11', '0612345678', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 11, 11, '2026-05-01 10:00:00', NULL),
(112, 'Christiane', 'Simon', 'christiane.s@email.fr', 'hash_mdp', '1943-05-29', '0623456789', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 12, 12, '2026-05-02 11:30:00', NULL),
(113, 'Gérard', 'Laurent', 'gerard.l@email.fr', 'hash_mdp', '1948-12-14', '0634567890', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 13, 13, '2026-05-03 09:15:00', NULL),
(114, 'Suzanne', 'Lefebvre', 'suzanne.l@email.fr', 'hash_mdp', '1939-01-07', '0645678901', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 14, 14, '2026-05-03 14:20:00', NULL),
(115, 'Marcel', 'Michel', 'marcel.m@email.fr', 'hash_mdp', '1941-09-02', '0656789012', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 15, 15, '2026-05-04 16:45:00', NULL),
(116, 'Josiane', 'Garcia', 'josiane.g@email.fr', 'hash_mdp', '1951-03-18', '0667890123', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 16, 16, '2026-05-05 10:10:00', NULL),
(117, 'Yves', 'David', 'yves.d@email.fr', 'hash_mdp', '1947-07-25', '0678901234', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 17, 17, '2026-05-05 11:00:00', NULL),
(118, 'Simone', 'Bertrand', 'simone.b@email.fr', 'hash_mdp', '1938-11-12', '0689012345', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 18, 18, '2026-05-06 08:30:00', NULL),
(119, 'René', 'Roux', 'rene.r@email.fr', 'hash_mdp', '1945-06-30', '0690123456', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 19, 19, '2026-05-07 15:20:00', NULL),
(120, 'Colette', 'Vincent', 'colette.v@email.fr', 'hash_mdp', '1949-04-15', '0601234567', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 20, 20, '2026-05-08 09:05:00', NULL),
(121, 'Claude', 'Fournier', 'claude.f@email.fr', 'hash_mdp', '1942-08-21', '0612345670', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 21, 21, '2026-05-01 10:00:00', NULL),
(122, 'Danielle', 'Morel', 'danielle.m@email.fr', 'hash_mdp', '1950-12-05', '0623456701', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 22, 22, '2026-05-02 11:30:00', NULL),
(123, 'Christian', 'Girard', 'christian.g@email.fr', 'hash_mdp', '1946-02-19', '0634567012', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 23, 23, '2026-05-03 09:15:00', NULL),
(124, 'Chantal', 'Andre', 'chantal.a@email.fr', 'hash_mdp', '1953-10-09', '0645670123', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 24, 24, '2026-05-03 14:20:00', NULL),
(125, 'Daniel', 'Lefevre', 'daniel.l@email.fr', 'hash_mdp', '1944-07-28', '0656701234', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 25, 25, '2026-05-04 16:45:00', NULL),
(126, 'Michèle', 'Mercier', 'michele.m@email.fr', 'hash_mdp', '1948-03-14', '0667012345', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 26, 26, '2026-05-05 10:10:00', NULL),
(127, 'Henri', 'Dupont', 'henri.d@email.fr', 'hash_mdp', '1937-05-06', '0670123456', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 27, 27, '2026-05-05 11:00:00', NULL),
(128, 'Yvette', 'Blanc', 'yvette.b@email.fr', 'hash_mdp', '1941-11-22', '0601234568', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 28, 28, '2026-05-06 08:30:00', NULL),
(129, 'Roger', 'Guerin', 'roger.g@email.fr', 'hash_mdp', '1943-09-03', '0612345689', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 29, 29, '2026-05-07 15:20:00', NULL),
(130, 'Liliane', 'Boyer', 'liliane.b@email.fr', 'hash_mdp', '1952-01-16', '0623456890', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 30, 30, '2026-05-08 09:05:00', NULL),
(131, 'Serge', 'Garnier', 'serge.g@email.fr', 'hash_mdp', '1947-06-11', '0634568901', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 31, 31, '2026-05-01 10:00:00', NULL),
(132, 'Martine', 'Chevalier', 'martine.c@email.fr', 'hash_mdp', '1954-04-29', '0645689012', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 32, 32, '2026-05-02 11:30:00', NULL),
(133, 'Guy', 'Francois', 'guy.f@email.fr', 'hash_mdp', '1939-10-18', '0656890123', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 33, 33, '2026-05-03 09:15:00', NULL),
(134, 'Annie', 'Legrand', 'annie.l@email.fr', 'hash_mdp', '1945-08-07', '0668901234', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 34, 34, '2026-05-03 14:20:00', NULL),
(135, 'Georges', 'Gauthier', 'georges.g@email.fr', 'hash_mdp', '1940-12-24', '0678901235', 'user', '2026-05-08 23:15:43', 1, NULL, NULL, 35, 35, '2026-05-04 16:45:00', NULL),
(136, 'Patrick', 'Perrin', 'patrick.p@email.fr', 'hash_mdp', '1951-02-14', '0689012346', 'banni', '2026-05-08 23:15:43', 1, 'Retards de paiements répétés', 9999, 36, NULL, NULL, NULL),
(137, 'Gisèle', 'Robin', 'gisele.r@email.fr', 'hash_mdp', '1946-05-09', '0690123457', 'banni', '2026-05-08 23:15:43', 1, 'Tentative de fraude', 9999, 37, NULL, NULL, NULL),
(138, 'Didier', 'Clement', 'didier.c@email.fr', 'hash_mdp', '1955-09-21', '0601234569', 'banni', '2026-05-08 23:15:43', 1, 'Non-respect des CGU', 90, 38, NULL, NULL, NULL),
(139, 'Évelyne', 'Morin', 'evelyne.m@email.fr', 'hash_mdp', '1948-11-30', '0612345690', 'banni', '2026-05-08 23:15:43', 1, 'Signalements multiples', 30, 39, NULL, NULL, NULL),
(140, 'Alain', 'Roussel', 'alain.r@email.fr', 'hash_mdp', '1943-07-04', '0623456901', 'banni', '2026-05-08 23:15:43', 1, 'Abus de confiance', 9999, 40, NULL, NULL, NULL),
(141, 'Sylvie', 'Mathieu', 'sylvie.m@email.fr', 'hash_mdp', '1953-01-26', '0634569012', 'banni', '2026-05-08 23:15:43', 1, 'Fausse identité', 9999, 41, NULL, NULL, NULL),
(142, 'Jean-Paul', 'Gautier', 'jp.g@email.fr', 'hash_mdp', '1949-08-13', '0645690123', 'banni', '2026-05-08 23:15:43', 1, 'Paiements refusés', 9999, 42, NULL, NULL, NULL),
(143, 'Mireille', 'Marchand', 'mireille.m@email.fr', 'hash_mdp', '1942-04-01', '0656901234', 'banni', '2026-05-08 23:15:43', 1, 'Incivilités', 60, 43, NULL, NULL, NULL),
(144, 'Thierry', 'Dufour', 'thierry.d@email.fr', 'hash_mdp', '1956-12-18', '0669012345', 'banni', '2026-05-08 23:15:43', 1, 'Propos déplacés', 9999, 44, NULL, NULL, NULL),
(145, 'Chantal', 'Dumas', 'chantal.d@email.fr', 'hash_mdp', '1947-03-27', '0679012346', 'banni', '2026-05-08 23:15:43', 1, 'Compte piraté pour spam', 9999, 45, NULL, NULL, NULL),
(146, 'Amra', 'Admin', 'admin1@silverhappy.fr', 'hash_mdp_admin', '1995-06-15', '0680123456', 'admin', '2026-05-08 23:15:43', 1, NULL, NULL, 46, NULL, NULL, NULL),
(147, 'Sarah', 'Admin', 'admin2@silverhappy.fr', 'hash_mdp_admin', '1992-10-02', '0691234567', 'admin', '2026-05-08 23:15:43', 1, NULL, NULL, 47, NULL, NULL, NULL),
(148, 'Thomas', 'Staff', 'staff.t@silverhappy.fr', 'hash_mdp_admin', '1988-04-20', '0602345678', 'admin', '2026-05-08 23:15:43', 1, NULL, NULL, 48, NULL, NULL, NULL),
(149, 'Camille', 'Modo', 'modo.c@silverhappy.fr', 'hash_mdp_admin', '1990-11-11', '0613456789', 'admin', '2026-05-08 23:15:43', 1, NULL, NULL, 49, NULL, NULL, NULL),
(150, 'Lucas', 'Tech', 'tech.l@silverhappy.fr', 'hash_mdp_admin', '1994-01-05', '0624567890', 'admin', '2026-05-08 23:15:43', 1, NULL, NULL, 50, NULL, NULL, NULL),
(151, 'Axel', 'MICK', 'axelmick15@gmail.com', '$2a$10$oGKok5yh0JY6R4SdOW2ayeqHUje38YN99fmeStB2m9x6HRXiZryEy', '2006-08-15', '0626557687', 'user', '2026-05-08 23:16:05', 0, NULL, NULL, 51, 36, '2026-05-08 23:16:57', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `UTILISATION_PROMO`
--

CREATE TABLE `UTILISATION_PROMO` (
  `id_promo` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `id_reduction` int(11) DEFAULT NULL,
  `date_utilisation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ABONNEMENT`
--
ALTER TABLE `ABONNEMENT`
  ADD PRIMARY KEY (`id_abonnement`),
  ADD KEY `id_paiement` (`id_paiement`);

--
-- Index pour la table `ADRESSE`
--
ALTER TABLE `ADRESSE`
  ADD PRIMARY KEY (`id_adresse`);

--
-- Index pour la table `AVIS`
--
ALTER TABLE `AVIS`
  ADD PRIMARY KEY (`id_avis`),
  ADD KEY `id_prestataire` (`id_prestataire`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `CATEGORIE`
--
ALTER TABLE `CATEGORIE`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `CODE_REDUCTION`
--
ALTER TABLE `CODE_REDUCTION`
  ADD PRIMARY KEY (`id_reduction`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Index pour la table `COMMANDE`
--
ALTER TABLE `COMMANDE`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_paiement` (`id_paiement`),
  ADD KEY `id_reduction` (`id_reduction`);

--
-- Index pour la table `CONSEIL`
--
ALTER TABLE `CONSEIL`
  ADD PRIMARY KEY (`id_conseil`);

--
-- Index pour la table `DISPONIBILITE`
--
ALTER TABLE `DISPONIBILITE`
  ADD PRIMARY KEY (`id_disponibilite`),
  ADD KEY `id_prestataire` (`id_prestataire`);

--
-- Index pour la table `DOCUMENT_PRESTATAIRE`
--
ALTER TABLE `DOCUMENT_PRESTATAIRE`
  ADD PRIMARY KEY (`id_document`),
  ADD KEY `id_prestataire` (`id_prestataire`);

--
-- Index pour la table `DOCUMENT_UTILISATEUR`
--
ALTER TABLE `DOCUMENT_UTILISATEUR`
  ADD PRIMARY KEY (`id_document`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `EVENEMENT`
--
ALTER TABLE `EVENEMENT`
  ADD PRIMARY KEY (`id_evenement`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `FACTURE`
--
ALTER TABLE `FACTURE`
  ADD PRIMARY KEY (`id_facture`),
  ADD UNIQUE KEY `unique_facture_mois` (`id_prestataire`,`mois_annee`);

--
-- Index pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_evenement` (`id_evenement`);

--
-- Index pour la table `LIGNE_COMMANDE`
--
ALTER TABLE `LIGNE_COMMANDE`
  ADD PRIMARY KEY (`id_ligne`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `LIKE_CONSEIL`
--
ALTER TABLE `LIKE_CONSEIL`
  ADD PRIMARY KEY (`id_utilisateur`,`id_conseil`),
  ADD KEY `id_conseil` (`id_conseil`);

--
-- Index pour la table `MESSAGE_ADMIN`
--
ALTER TABLE `MESSAGE_ADMIN`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `id_utilisateur1` (`id_utilisateur1`),
  ADD KEY `id_utilisateur2` (`id_utilisateur2`);

--
-- Index pour la table `MESSAGE_PRESTATAIRE`
--
ALTER TABLE `MESSAGE_PRESTATAIRE`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `id_prestataire` (`id_prestataire`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_service` (`id_service`),
  ADD KEY `id_disponibilite` (`id_disponibilite`);

--
-- Index pour la table `PAIEMENT`
--
ALTER TABLE `PAIEMENT`
  ADD PRIMARY KEY (`id_paiement`);

--
-- Index pour la table `PANIER`
--
ALTER TABLE `PANIER`
  ADD PRIMARY KEY (`id_panier`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `PRESTATAIRE`
--
ALTER TABLE `PRESTATAIRE`
  ADD PRIMARY KEY (`id_prestataire`),
  ADD UNIQUE KEY `siret` (`siret`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_abonnement` (`id_abonnement`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `PRESTATAIRE_EVENEMENT`
--
ALTER TABLE `PRESTATAIRE_EVENEMENT`
  ADD KEY `id_prestataire` (`id_prestataire`),
  ADD KEY `id_evenement` (`id_evenement`);

--
-- Index pour la table `PRODUIT`
--
ALTER TABLE `PRODUIT`
  ADD PRIMARY KEY (`id_produit`);

--
-- Index pour la table `RESERVATION_SERVICE`
--
ALTER TABLE `RESERVATION_SERVICE`
  ADD PRIMARY KEY (`id_reservation`),
  ADD KEY `id_service` (`id_service`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_paiement` (`id_paiement`);

--
-- Index pour la table `SERVICE`
--
ALTER TABLE `SERVICE`
  ADD PRIMARY KEY (`id_service`),
  ADD KEY `id_prestataire` (`id_prestataire`);

--
-- Index pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_adresse` (`id_adresse`),
  ADD KEY `id_abonnement` (`id_abonnement`);

--
-- Index pour la table `UTILISATION_PROMO`
--
ALTER TABLE `UTILISATION_PROMO`
  ADD PRIMARY KEY (`id_promo`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_reduction` (`id_reduction`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `ABONNEMENT`
--
ALTER TABLE `ABONNEMENT`
  MODIFY `id_abonnement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT pour la table `ADRESSE`
--
ALTER TABLE `ADRESSE`
  MODIFY `id_adresse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `AVIS`
--
ALTER TABLE `AVIS`
  MODIFY `id_avis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT pour la table `CATEGORIE`
--
ALTER TABLE `CATEGORIE`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pour la table `CODE_REDUCTION`
--
ALTER TABLE `CODE_REDUCTION`
  MODIFY `id_reduction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `COMMANDE`
--
ALTER TABLE `COMMANDE`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `CONSEIL`
--
ALTER TABLE `CONSEIL`
  MODIFY `id_conseil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `DISPONIBILITE`
--
ALTER TABLE `DISPONIBILITE`
  MODIFY `id_disponibilite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=310;

--
-- AUTO_INCREMENT pour la table `DOCUMENT_PRESTATAIRE`
--
ALTER TABLE `DOCUMENT_PRESTATAIRE`
  MODIFY `id_document` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `DOCUMENT_UTILISATEUR`
--
ALTER TABLE `DOCUMENT_UTILISATEUR`
  MODIFY `id_document` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `EVENEMENT`
--
ALTER TABLE `EVENEMENT`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT pour la table `FACTURE`
--
ALTER TABLE `FACTURE`
  MODIFY `id_facture` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `LIGNE_COMMANDE`
--
ALTER TABLE `LIGNE_COMMANDE`
  MODIFY `id_ligne` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `MESSAGE_ADMIN`
--
ALTER TABLE `MESSAGE_ADMIN`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `MESSAGE_PRESTATAIRE`
--
ALTER TABLE `MESSAGE_PRESTATAIRE`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `PAIEMENT`
--
ALTER TABLE `PAIEMENT`
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT pour la table `PANIER`
--
ALTER TABLE `PANIER`
  MODIFY `id_panier` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `PRESTATAIRE`
--
ALTER TABLE `PRESTATAIRE`
  MODIFY `id_prestataire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT pour la table `PRODUIT`
--
ALTER TABLE `PRODUIT`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `RESERVATION_SERVICE`
--
ALTER TABLE `RESERVATION_SERVICE`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `SERVICE`
--
ALTER TABLE `SERVICE`
  MODIFY `id_service` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT pour la table `UTILISATION_PROMO`
--
ALTER TABLE `UTILISATION_PROMO`
  MODIFY `id_promo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ABONNEMENT`
--
ALTER TABLE `ABONNEMENT`
  ADD CONSTRAINT `ABONNEMENT_ibfk_1` FOREIGN KEY (`id_paiement`) REFERENCES `PAIEMENT` (`id_paiement`);

--
-- Contraintes pour la table `AVIS`
--
ALTER TABLE `AVIS`
  ADD CONSTRAINT `AVIS_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`),
  ADD CONSTRAINT `AVIS_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`);

--
-- Contraintes pour la table `COMMANDE`
--
ALTER TABLE `COMMANDE`
  ADD CONSTRAINT `COMMANDE_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `COMMANDE_ibfk_2` FOREIGN KEY (`id_paiement`) REFERENCES `PAIEMENT` (`id_paiement`),
  ADD CONSTRAINT `COMMANDE_ibfk_3` FOREIGN KEY (`id_reduction`) REFERENCES `CODE_REDUCTION` (`id_reduction`);

--
-- Contraintes pour la table `DISPONIBILITE`
--
ALTER TABLE `DISPONIBILITE`
  ADD CONSTRAINT `DISPONIBILITE_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`) ON DELETE CASCADE;

--
-- Contraintes pour la table `DOCUMENT_PRESTATAIRE`
--
ALTER TABLE `DOCUMENT_PRESTATAIRE`
  ADD CONSTRAINT `DOCUMENT_PRESTATAIRE_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`);

--
-- Contraintes pour la table `DOCUMENT_UTILISATEUR`
--
ALTER TABLE `DOCUMENT_UTILISATEUR`
  ADD CONSTRAINT `DOCUMENT_UTILISATEUR_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`);

--
-- Contraintes pour la table `EVENEMENT`
--
ALTER TABLE `EVENEMENT`
  ADD CONSTRAINT `EVENEMENT_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `CATEGORIE` (`id_categorie`) ON DELETE SET NULL;

--
-- Contraintes pour la table `FACTURE`
--
ALTER TABLE `FACTURE`
  ADD CONSTRAINT `FACTURE_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`);

--
-- Contraintes pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  ADD CONSTRAINT `INSCRIPTION_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `INSCRIPTION_ibfk_2` FOREIGN KEY (`id_evenement`) REFERENCES `EVENEMENT` (`id_evenement`);

--
-- Contraintes pour la table `LIGNE_COMMANDE`
--
ALTER TABLE `LIGNE_COMMANDE`
  ADD CONSTRAINT `LIGNE_COMMANDE_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `COMMANDE` (`id_commande`),
  ADD CONSTRAINT `LIGNE_COMMANDE_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `PRODUIT` (`id_produit`);

--
-- Contraintes pour la table `LIKE_CONSEIL`
--
ALTER TABLE `LIKE_CONSEIL`
  ADD CONSTRAINT `LIKE_CONSEIL_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `LIKE_CONSEIL_ibfk_2` FOREIGN KEY (`id_conseil`) REFERENCES `CONSEIL` (`id_conseil`);

--
-- Contraintes pour la table `MESSAGE_ADMIN`
--
ALTER TABLE `MESSAGE_ADMIN`
  ADD CONSTRAINT `MESSAGE_ADMIN_ibfk_1` FOREIGN KEY (`id_utilisateur1`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `MESSAGE_ADMIN_ibfk_2` FOREIGN KEY (`id_utilisateur2`) REFERENCES `UTILISATEUR` (`id_utilisateur`);

--
-- Contraintes pour la table `MESSAGE_PRESTATAIRE`
--
ALTER TABLE `MESSAGE_PRESTATAIRE`
  ADD CONSTRAINT `MESSAGE_PRESTATAIRE_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`),
  ADD CONSTRAINT `MESSAGE_PRESTATAIRE_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `MESSAGE_PRESTATAIRE_ibfk_3` FOREIGN KEY (`id_service`) REFERENCES `SERVICE` (`id_service`),
  ADD CONSTRAINT `MESSAGE_PRESTATAIRE_ibfk_4` FOREIGN KEY (`id_disponibilite`) REFERENCES `DISPONIBILITE` (`id_disponibilite`);

--
-- Contraintes pour la table `PANIER`
--
ALTER TABLE `PANIER`
  ADD CONSTRAINT `PANIER_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `PANIER_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `PRODUIT` (`id_produit`);

--
-- Contraintes pour la table `PRESTATAIRE`
--
ALTER TABLE `PRESTATAIRE`
  ADD CONSTRAINT `PRESTATAIRE_ibfk_1` FOREIGN KEY (`id_abonnement`) REFERENCES `ABONNEMENT` (`id_abonnement`),
  ADD CONSTRAINT `PRESTATAIRE_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `CATEGORIE` (`id_categorie`);

--
-- Contraintes pour la table `PRESTATAIRE_EVENEMENT`
--
ALTER TABLE `PRESTATAIRE_EVENEMENT`
  ADD CONSTRAINT `PRESTATAIRE_EVENEMENT_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`),
  ADD CONSTRAINT `PRESTATAIRE_EVENEMENT_ibfk_2` FOREIGN KEY (`id_evenement`) REFERENCES `EVENEMENT` (`id_evenement`);

--
-- Contraintes pour la table `RESERVATION_SERVICE`
--
ALTER TABLE `RESERVATION_SERVICE`
  ADD CONSTRAINT `RESERVATION_SERVICE_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `SERVICE` (`id_service`) ON DELETE CASCADE,
  ADD CONSTRAINT `RESERVATION_SERVICE_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `RESERVATION_SERVICE_ibfk_3` FOREIGN KEY (`id_paiement`) REFERENCES `PAIEMENT` (`id_paiement`) ON DELETE SET NULL;

--
-- Contraintes pour la table `SERVICE`
--
ALTER TABLE `SERVICE`
  ADD CONSTRAINT `SERVICE_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`) ON DELETE CASCADE;

--
-- Contraintes pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  ADD CONSTRAINT `UTILISATEUR_ibfk_1` FOREIGN KEY (`id_adresse`) REFERENCES `ADRESSE` (`id_adresse`),
  ADD CONSTRAINT `UTILISATEUR_ibfk_2` FOREIGN KEY (`id_abonnement`) REFERENCES `ABONNEMENT` (`id_abonnement`);

--
-- Contraintes pour la table `UTILISATION_PROMO`
--
ALTER TABLE `UTILISATION_PROMO`
  ADD CONSTRAINT `UTILISATION_PROMO_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `UTILISATION_PROMO_ibfk_2` FOREIGN KEY (`id_reduction`) REFERENCES `CODE_REDUCTION` (`id_reduction`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
