-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db-silver-happy
-- Généré le : ven. 08 mai 2026 à 18:25
-- Version du serveur : 10.11.16-MariaDB-ubu2204
-- Version de PHP : 8.3.30

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

-- --------------------------------------------------------

--
-- Structure de la table `CATEGORIE`
--

CREATE TABLE `CATEGORIE` (
  `id_categorie` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Structure de la table `FACTURE`
--

CREATE TABLE `FACTURE` (
  `id_facture` int(11) NOT NULL,
  `montant` double DEFAULT NULL,
  `frais_plateforme` double DEFAULT NULL,
  `montant_net` double DEFAULT NULL,
  `mois_annee` varchar(12) NOT NULL,
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

-- --------------------------------------------------------

--
-- Structure de la table `PRESTATAIRE_EVENEMENT`
--

CREATE TABLE `PRESTATAIRE_EVENEMENT` (
  `id_prestataire` int(11) NOT NULL,
  `id_evenement` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `image` varchar(250) DEFAULT NULL,
  `date_ajout` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id_abonnement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ADRESSE`
--
ALTER TABLE `ADRESSE`
  MODIFY `id_adresse` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `AVIS`
--
ALTER TABLE `AVIS`
  MODIFY `id_avis` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `CATEGORIE`
--
ALTER TABLE `CATEGORIE`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `CODE_REDUCTION`
--
ALTER TABLE `CODE_REDUCTION`
  MODIFY `id_reduction` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `COMMANDE`
--
ALTER TABLE `COMMANDE`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `CONSEIL`
--
ALTER TABLE `CONSEIL`
  MODIFY `id_conseil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `DISPONIBILITE`
--
ALTER TABLE `DISPONIBILITE`
  MODIFY `id_disponibilite` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `PANIER`
--
ALTER TABLE `PANIER`
  MODIFY `id_panier` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `PRESTATAIRE`
--
ALTER TABLE `PRESTATAIRE`
  MODIFY `id_prestataire` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `PRODUIT`
--
ALTER TABLE `PRODUIT`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `RESERVATION_SERVICE`
--
ALTER TABLE `RESERVATION_SERVICE`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `SERVICE`
--
ALTER TABLE `SERVICE`
  MODIFY `id_service` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `abonnement_ibfk_1` FOREIGN KEY (`id_paiement`) REFERENCES `PAIEMENT` (`id_paiement`);

--
-- Contraintes pour la table `AVIS`
--
ALTER TABLE `AVIS`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`),
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`);

--
-- Contraintes pour la table `COMMANDE`
--
ALTER TABLE `COMMANDE`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`id_paiement`) REFERENCES `PAIEMENT` (`id_paiement`),
  ADD CONSTRAINT `commande_ibfk_3` FOREIGN KEY (`id_reduction`) REFERENCES `CODE_REDUCTION` (`id_reduction`);

--
-- Contraintes pour la table `DISPONIBILITE`
--
ALTER TABLE `DISPONIBILITE`
  ADD CONSTRAINT `disponibilite_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`) ON DELETE CASCADE;

--
-- Contraintes pour la table `DOCUMENT_PRESTATAIRE`
--
ALTER TABLE `DOCUMENT_PRESTATAIRE`
  ADD CONSTRAINT `document_prestataire_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`);

--
-- Contraintes pour la table `DOCUMENT_UTILISATEUR`
--
ALTER TABLE `DOCUMENT_UTILISATEUR`
  ADD CONSTRAINT `document_utilisateur_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`);

--
-- Contraintes pour la table `EVENEMENT`
--
ALTER TABLE `EVENEMENT`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `CATEGORIE` (`id_categorie`) ON DELETE SET NULL;

--
-- Contraintes pour la table `FACTURE`
--
ALTER TABLE `FACTURE`
  ADD CONSTRAINT `facture_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`);

--
-- Contraintes pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`id_evenement`) REFERENCES `EVENEMENT` (`id_evenement`);

--
-- Contraintes pour la table `LIGNE_COMMANDE`
--
ALTER TABLE `LIGNE_COMMANDE`
  ADD CONSTRAINT `ligne_commande_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `COMMANDE` (`id_commande`),
  ADD CONSTRAINT `ligne_commande_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `PRODUIT` (`id_produit`);

--
-- Contraintes pour la table `LIKE_CONSEIL`
--
ALTER TABLE `LIKE_CONSEIL`
  ADD CONSTRAINT `like_conseil_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `like_conseil_ibfk_2` FOREIGN KEY (`id_conseil`) REFERENCES `CONSEIL` (`id_conseil`);

--
-- Contraintes pour la table `MESSAGE_ADMIN`
--
ALTER TABLE `MESSAGE_ADMIN`
  ADD CONSTRAINT `message_admin_ibfk_1` FOREIGN KEY (`id_utilisateur1`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `message_admin_ibfk_2` FOREIGN KEY (`id_utilisateur2`) REFERENCES `UTILISATEUR` (`id_utilisateur`);

--
-- Contraintes pour la table `MESSAGE_PRESTATAIRE`
--
ALTER TABLE `MESSAGE_PRESTATAIRE`
  ADD CONSTRAINT `message_prestataire_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`),
  ADD CONSTRAINT `message_prestataire_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `message_prestataire_ibfk_3` FOREIGN KEY (`id_service`) REFERENCES `SERVICE` (`id_service`),
  ADD CONSTRAINT `message_prestataire_ibfk_4` FOREIGN KEY (`id_disponibilite`) REFERENCES `DISPONIBILITE` (`id_disponibilite`);

--
-- Contraintes pour la table `PANIER`
--
ALTER TABLE `PANIER`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `PRODUIT` (`id_produit`);

--
-- Contraintes pour la table `PRESTATAIRE`
--
ALTER TABLE `PRESTATAIRE`
  ADD CONSTRAINT `prestataire_ibfk_1` FOREIGN KEY (`id_abonnement`) REFERENCES `ABONNEMENT` (`id_abonnement`),
  ADD CONSTRAINT `prestataire_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `CATEGORIE` (`id_categorie`);

--
-- Contraintes pour la table `PRESTATAIRE_EVENEMENT`
--
ALTER TABLE `PRESTATAIRE_EVENEMENT`
  ADD CONSTRAINT `prestataire_evenement_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`),
  ADD CONSTRAINT `prestataire_evenement_ibfk_2` FOREIGN KEY (`id_evenement`) REFERENCES `EVENEMENT` (`id_evenement`);

--
-- Contraintes pour la table `RESERVATION_SERVICE`
--
ALTER TABLE `RESERVATION_SERVICE`
  ADD CONSTRAINT `reservation_service_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `SERVICE` (`id_service`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_service_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_service_ibfk_3` FOREIGN KEY (`id_paiement`) REFERENCES `PAIEMENT` (`id_paiement`) ON DELETE SET NULL;

--
-- Contraintes pour la table `SERVICE`
--
ALTER TABLE `SERVICE`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`id_prestataire`) REFERENCES `PRESTATAIRE` (`id_prestataire`) ON DELETE CASCADE;

--
-- Contraintes pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_adresse`) REFERENCES `ADRESSE` (`id_adresse`),
  ADD CONSTRAINT `utilisateur_ibfk_2` FOREIGN KEY (`id_abonnement`) REFERENCES `ABONNEMENT` (`id_abonnement`);

--
-- Contraintes pour la table `UTILISATION_PROMO`
--
ALTER TABLE `UTILISATION_PROMO`
  ADD CONSTRAINT `utilisation_promo_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`),
  ADD CONSTRAINT `utilisation_promo_ibfk_2` FOREIGN KEY (`id_reduction`) REFERENCES `CODE_REDUCTION` (`id_reduction`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
