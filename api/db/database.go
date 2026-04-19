package db

import (
	"database/sql"
	"fmt"
	"log"
	"os"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

var DB *sql.DB

func InitDB() {
	dsn := fmt.Sprintf("%s:%s@tcp(%s:3306)/%s?parseTime=true&multiStatements=true&loc=Europe%%2FParis",
		os.Getenv("DB_USER"),
		os.Getenv("DB_PASS"),
		os.Getenv("DB_HOST"),
		os.Getenv("DB_NAME"),
	)

	var err error
	for i := 0; i < 10; i++ {
		DB, err = sql.Open("mysql", dsn)
		if err == nil {
			err = DB.Ping()
		}
		if err == nil {
			break
		}
		log.Printf("Attente de MariaDB... (%d/10)", i+1)
		time.Sleep(3 * time.Second)
	}

	if err != nil {
		log.Fatal("Impossible de se connecter à MariaDB :", err)
	}

	creationQuery := `
	CREATE TABLE IF NOT EXISTS PAIEMENT(
		id_paiement INT AUTO_INCREMENT PRIMARY KEY,
		prix DOUBLE,
		date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
		statut ENUM('en_attente', 'valide', 'refuse', 'rembourse'),
		mode_paiement ENUM('carte', 'cheque', 'prelevement'),
		url_facture VARCHAR(500),
		stripe_pi VARCHAR(255) DEFAULT NULL
	);
	CREATE TABLE IF NOT EXISTS PRODUIT(
		id_produit INT AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(100),
		description VARCHAR(200),
		prix DOUBLE,
		stock INT,
		image VARCHAR(250) DEFAULT NULL,
		date_ajout DATE DEFAULT (CURRENT_DATE)
	);
	CREATE TABLE IF NOT EXISTS NOTIFICATION(
		id_notification INT AUTO_INCREMENT PRIMARY KEY,
		destinataire VARCHAR(100),
		contenu VARCHAR(200),
		date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
		statut ENUM('lu','envoye','en attente') DEFAULT 'en attente',
		priorite INT
	);
	CREATE TABLE IF NOT EXISTS CATEGORIE (
		id_categorie INT AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(100) NOT NULL,
		description TEXT
	);
	CREATE TABLE IF NOT EXISTS CONSEIL(
		id_conseil INT AUTO_INCREMENT PRIMARY KEY,
		titre VARCHAR(80),
		description VARCHAR(200),
		image VARCHAR(250) DEFAULT NULL,
		date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
		categorie VARCHAR(80)
	);
	CREATE TABLE IF NOT EXISTS ADRESSE(
		id_adresse INT AUTO_INCREMENT PRIMARY KEY,
		numero VARCHAR(10),
		rue VARCHAR(100),
		ville VARCHAR(80),
		code_postal VARCHAR(10),
		pays VARCHAR(20)
	);
	CREATE TABLE IF NOT EXISTS EVENEMENT(
		id_evenement INT AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(100),
		description VARCHAR(200),
		lieu VARCHAR(100),
		nombre_place INT NOT NULL,
		date_debut DATETIME,
		date_fin DATETIME,
		image VARCHAR(250) DEFAULT NULL,
		date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
		id_categorie INT,
		prix FLOAT DEFAULT 0.0,
		date_fin_boost DATETIME NULL DEFAULT NULL,
		FOREIGN KEY (id_categorie) REFERENCES CATEGORIE(id_categorie) ON DELETE SET NULL
	);
	CREATE TABLE IF NOT EXISTS DISPONIBILITE (
		id_disponibilite INT AUTO_INCREMENT PRIMARY KEY,
		id_prestataire INT NOT NULL,
		date_heure DATETIME NOT NULL,
		est_reserve TINYINT(1) DEFAULT 0,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire) ON DELETE CASCADE
	);
	CREATE TABLE IF NOT EXISTS ABONNEMENT(
		id_abonnement INT AUTO_INCREMENT PRIMARY KEY,
		description VARCHAR(200),
		renouvellement BOOLEAN DEFAULT false,
		type_abonnement ENUM('prestataire', 'seniors'),
		type_paiement ENUM('mensuel', 'annuel'),
		methode_paiement ENUM('carte', 'cheque', 'prelevement'),
		tarif DOUBLE,
		stripe_sub VARCHAR(255) DEFAULT NULL,
		id_paiement INT NOT NULL,
		FOREIGN KEY (id_paiement) REFERENCES PAIEMENT(id_paiement)
	);
	CREATE TABLE IF NOT EXISTS PRESTATAIRE(
		id_prestataire INT AUTO_INCREMENT PRIMARY KEY,
		siret VARCHAR(50) UNIQUE,
		prenom VARCHAR(55),
		nom VARCHAR(55),
		email VARCHAR(100) UNIQUE,
		mdp VARCHAR(250),
		date_naissance DATE,
		num_telephone VARCHAR(20),
		status ENUM('en attente', 'validé', 'refusé') DEFAULT 'en attente',
		motif_refus VARCHAR(250) DEFAULT NULL,
		date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
		tarifs DOUBLE,
		id_abonnement INT,
		debut_abonnement DATETIME NULL, 
		id_categorie INT,
		date_fin_boost DATETIME NULL DEFAULT NULL,
		FOREIGN KEY (id_abonnement) REFERENCES ABONNEMENT(id_abonnement),
		FOREIGN KEY (id_categorie) REFERENCES CATEGORIE(id_categorie)
	);
	CREATE TABLE IF NOT EXISTS PRESTATAIRE_EVENEMENT (
  		id_prestataire INT NOT NULL,
  		id_evenement INT NOT NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire),
		FOREIGN KEY (id_evenement) REFERENCES EVENEMENT(id_evenement)
	);
	CREATE TABLE IF NOT EXISTS UTILISATEUR(
		id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
		prenom VARCHAR(55),
		nom VARCHAR(55),
		email VARCHAR(100) UNIQUE,
		mdp VARCHAR(250),
		date_naissance DATE,
		num_telephone VARCHAR(20),
		statut ENUM('user', 'admin', 'banni') DEFAULT 'user',
		date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
		premiere_connexion BOOLEAN DEFAULT true,
		motif_bannissement VARCHAR(100),
		duree_bannissement INT,
		id_adresse INT NOT NULL,
		id_abonnement INT,
		debut_abonnement DATETIME,
		FOREIGN KEY (id_adresse) REFERENCES ADRESSE(id_adresse),
		FOREIGN KEY (id_abonnement) REFERENCES ABONNEMENT(id_abonnement)
	);
	CREATE TABLE IF NOT EXISTS PANIER(
		id_panier INT AUTO_INCREMENT PRIMARY KEY,
		id_utilisateur INT,
		id_produit INT,
		quantite INT,
		date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_produit) REFERENCES PRODUIT(id_produit)
	);
	CREATE TABLE IF NOT EXISTS COMMANDE(
		id_commande INT AUTO_INCREMENT PRIMARY KEY,
		id_utilisateur INT NOT NULL,
		id_paiement INT NOT NULL,
		date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
		total DOUBLE,
		adresse VARCHAR(255),
		code_postal CHAR(5),
		ville VARCHAR(100),
		id_reduction INT,
		montant_frais_port DOUBLE DEFAULT 0.0,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_paiement) REFERENCES PAIEMENT(id_paiement),
		FOREIGN KEY (id_reduction) REFERENCES CODE_REDUCTION(id_reduction)
	);
	CREATE TABLE IF NOT EXISTS LIGNE_COMMANDE(
		id_ligne INT AUTO_INCREMENT PRIMARY KEY,
		id_commande INT NOT NULL,
		id_produit INT NOT NULL,
		quantite INT,
		prix_unitaire DOUBLE,
		FOREIGN KEY (id_commande) REFERENCES COMMANDE(id_commande),
		FOREIGN KEY (id_produit) REFERENCES PRODUIT(id_produit)
	);
	CREATE TABLE IF NOT EXISTS RECEPTION(
		id_utilisateur INT,
		id_notification INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_notification) REFERENCES NOTIFICATION(id_notification)
	);
	CREATE TABLE IF NOT EXISTS LIKE_CONSEIL (
    id_utilisateur INT,
    id_conseil INT,
    date_like DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_utilisateur, id_conseil),
    FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
    FOREIGN KEY (id_conseil) REFERENCES CONSEIL(id_conseil)
);
	CREATE TABLE IF NOT EXISTS INSCRIPTION(
		id_utilisateur INT,
		id_evenement INT,
		id_paiement INT DEFAULT NULL,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_evenement) REFERENCES EVENEMENT(id_evenement)
	);
	CREATE TABLE IF NOT EXISTS MESSAGE_ADMIN(
		id_message INT AUTO_INCREMENT PRIMARY KEY,
		contenu VARCHAR(250),
		date DATETIME DEFAULT CURRENT_TIMESTAMP,
		id_utilisateur1 INT,
		id_utilisateur2 INT,
		est_lu BOOLEAN DEFAULT 0,
		FOREIGN KEY (id_utilisateur1) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_utilisateur2) REFERENCES UTILISATEUR(id_utilisateur)
	);
	CREATE TABLE IF NOT EXISTS MESSAGE_PRESTATAIRE(
		id_message INT AUTO_INCREMENT PRIMARY KEY,
		contenu VARCHAR(250),
		date DATETIME DEFAULT CURRENT_TIMESTAMP,
		id_prestataire INT,
		id_utilisateur INT,
		expediteur BOOLEAN DEFAULT 0,
		est_lu BOOLEAN DEFAULT 0,
		stripe_account_id VARCHAR(255) DEFAULT NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire),
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
	);
	CREATE TABLE IF NOT EXISTS SERVICE (
		id_service INT AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(255) NOT NULL,
		description TEXT,
		id_categorie INT,
		id_prestataire INT NOT NULL,
		prix DOUBLE NOT NULL DEFAULT 0.0,
		FOREIGN KEY (id_categorie) REFERENCES CATEGORIE(id_categorie) ON DELETE SET NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire) ON DELETE CASCADE
	);
	CREATE TABLE IF NOT EXISTS RESERVATION_SERVICE (
		id_reservation INT AUTO_INCREMENT PRIMARY KEY,
		id_service INT NOT NULL,
		id_utilisateur INT NOT NULL,
		date_heure DATETIME NOT NULL,
		id_paiement INT DEFAULT NULL,
		FOREIGN KEY (id_service) REFERENCES SERVICE(id_service) ON DELETE CASCADE,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur) ON DELETE CASCADE,
		FOREIGN KEY (id_paiement) REFERENCES PAIEMENT(id_paiement) ON DELETE SET NULL
	);
	CREATE TABLE IF NOT EXISTS PRESTATION(
		id_prestation INT AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(100),
		description VARCHAR(200),
		prix DOUBLE,
		lieu VARCHAR(100),
		nombre_place INT NOT NULL,
		date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
		id_paiement INT NOT NULL,
		id_prestataire INT NOT NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire),
		FOREIGN KEY (id_paiement) REFERENCES PAIEMENT(id_paiement)
	);
	CREATE TABLE IF NOT EXISTS DOCUMENT_UTILISATEUR(
		id_document INT AUTO_INCREMENT PRIMARY KEY,
		type VARCHAR(250),
		nom VARCHAR(250),
		id_utilisateur INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
	);
	CREATE TABLE IF NOT EXISTS DOCUMENT_PRESTATAIRE(
		id_document INT AUTO_INCREMENT PRIMARY KEY,
		type VARCHAR(250),
		nom VARCHAR(250),
		id_prestataire INT,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire)
	);
	CREATE TABLE IF NOT EXISTS AVIS(
		id_avis INT AUTO_INCREMENT PRIMARY KEY,
		description VARCHAR(200),
		titre VARCHAR(100),
		note INT,
		date DATETIME DEFAULT CURRENT_TIMESTAMP,
		categorie ENUM('Service', 'Evenement', 'Prestataire', 'Communication', 'Autre') DEFAULT 'Autre',
		id_prestataire INT,
		id_utilisateur INT NOT NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire),
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
	);
	CREATE TABLE IF NOT EXISTS FACTURE(
		id_facture INT AUTO_INCREMENT PRIMARY KEY,
		montant DOUBLE,
		frais_plateforme DOUBLE,
		montant_net DOUBLE,
		mois_annee VARCHAR(7) NOT NULL,
		date DATETIME DEFAULT CURRENT_TIMESTAMP,
		statut ENUM('en_attente', 'paye', 'annule') DEFAULT 'en_attente',
		id_prestataire INT NOT NULL,
		tripe_transfer_id VARCHAR(255) DEFAULT NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire),
		UNIQUE KEY unique_facture_mois (id_prestataire, mois_annee)
	);
	CREATE TABLE IF NOT EXISTS CODE_REDUCTION (
		id_reduction INT AUTO_INCREMENT PRIMARY KEY,
		code VARCHAR(50) UNIQUE NOT NULL,
		valeur INT NOT NULL,
		type ENUM('pourcentage', 'fixe') DEFAULT 'pourcentage',
		actif BOOLEAN DEFAULT TRUE,
		date_expiration DATETIME
	);
	CREATE TABLE IF NOT EXISTS UTILISATION_PROMO (
		id_promo INT AUTO_INCREMENT PRIMARY KEY,
		id_utilisateur INT,
		id_reduction INT,
		date_utilisation DATETIME DEFAULT CURRENT_TIMESTAMP,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_reduction) REFERENCES CODE_REDUCTION(id_reduction)
	);
	CREATE TABLE IF NOT EXISTS DEVIS(
		id_devis INT AUTO_INCREMENT PRIMARY KEY,
		description VARCHAR(200),
		prix DOUBLE,
		id_utilisateur INT NOT NULL,
		id_prestataire INT NOT NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire),
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
	);
	CREATE TABLE IF NOT EXISTS RESERVE(
		id_utilisateur INT,
		id_prestation INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_prestation) REFERENCES PRESTATION(id_prestation)
	);
	CREATE TABLE IF NOT EXISTS NEWSLETTER(
  		id_newsletter INT AUTO_INCREMENT PRIMARY KEY,
  		titre VARCHAR(50) NOT NULL,
  		contenu VARCHAR(200) NOT NULL,
  		date_creation DATE DEFAULT CURRENT_TIMESTAMP
	);`

	_, err = DB.Exec(creationQuery)
	if err != nil {
		log.Fatal("Erreur création table : ", err)
	}
	
	fmt.Println("Base MariaDB initialisée !")
}