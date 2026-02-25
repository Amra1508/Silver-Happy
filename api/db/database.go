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
	dsn := fmt.Sprintf("%s:%s@tcp(%s:3306)/%s?parseTime=true&multiStatements=true",
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
		date_paiement DATETIME,
		statut ENUM('en_attente', 'valide', 'refuse', 'rembourse'),
		mode_paiement ENUM('carte', 'cheque', 'prelevement')
	);
	CREATE TABLE IF NOT EXISTS PRODUIT(
		id_produit INT AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(100),
		description VARCHAR(200),
		prix DOUBLE,
		stock INT,
		date_ajout DATE
	);
	CREATE TABLE IF NOT EXISTS NOTIFICATION(
		id_notification INT AUTO_INCREMENT PRIMARY KEY,
		destinataire VARCHAR(100),
		contenu VARCHAR(200),
		date_envoi DATETIME,
		statut ENUM('lu','envoye','en attente') DEFAULT 'en attente',
		priorite INT
	);
	CREATE TABLE IF NOT EXISTS CONSEIL(
		id_conseil INT AUTO_INCREMENT PRIMARY KEY,
		titre VARCHAR(80),
		description VARCHAR(200),
		date_publication DATETIME,
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
		date_ajout DATETIME
	);
	CREATE TABLE IF NOT EXISTS PLANNING(
		id_planning INT AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(100),
		description VARCHAR(200),
		date_creation DATETIME
	);
	CREATE TABLE IF NOT EXISTS DOCUMENT(
		id_document INT AUTO_INCREMENT PRIMARY KEY,
		type ENUM('CV', 'Lettre de motivation', 'Casier judiciaire', 'Medical')
	);
	CREATE TABLE IF NOT EXISTS CONTIENT_EVENEMENT(
		id_occurence INT AUTO_INCREMENT PRIMARY KEY,
		id_evenement INT,
		id_planning INT,
		date_debut DATETIME,
		date_fin DATETIME,
		FOREIGN KEY (id_evenement) REFERENCES EVENEMENT(id_evenement),
		FOREIGN KEY (id_planning) REFERENCES PLANNING(id_planning)
	);
	CREATE TABLE IF NOT EXISTS ABONNEMENT(
		id_abonnement INT AUTO_INCREMENT PRIMARY KEY,
		description VARCHAR(200),
		renouvellement BOOLEAN DEFAULT false,
		type_abonnement ENUM('prestataire', 'seniors'),
		type_paiement ENUM('mensuel', 'annuel'),
		methode_paiement ENUM('carte', 'cheque', 'prelevement'),
		tarif DOUBLE,
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
		est_valide BOOLEAN DEFAULT false,
		tarifs DOUBLE,
		type_prestation VARCHAR(50),
		id_abonnement INT,
		FOREIGN KEY (id_abonnement) REFERENCES ABONNEMENT(id_abonnement)
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
		date_creation DATETIME,
		premiere_connexion BOOLEAN DEFAULT true,
		motif_bannissement VARCHAR(100),
		duree_bannissement INT,
		id_planning INT NOT NULL,
		id_adresse INT NOT NULL,
		id_abonnement INT,
		debut_abonnement DATETIME,
		FOREIGN KEY (id_planning) REFERENCES PLANNING(id_planning),
		FOREIGN KEY (id_adresse) REFERENCES ADRESSE(id_adresse),
		FOREIGN KEY (id_abonnement) REFERENCES ABONNEMENT(id_abonnement)
	);
	CREATE TABLE IF NOT EXISTS COMMANDE(
		id_commande INT AUTO_INCREMENT PRIMARY KEY,
		date_commande DATETIME,
		total DOUBLE,
		id_paiement INT NOT NULL,
		id_utilisateur INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_paiement) REFERENCES PAIEMENT(id_paiement)
	);
	CREATE TABLE IF NOT EXISTS RECEPTION(
		id_utilisateur INT,
		id_notification INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_notification) REFERENCES NOTIFICATION(id_notification)
	);
	CREATE TABLE IF NOT EXISTS CONSULTATION(
		id_utilisateur INT,
		id_conseil INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_conseil) REFERENCES CONSEIL(id_conseil)
	);
	CREATE TABLE IF NOT EXISTS INSCRIPTION(
		id_utilisateur INT,
		id_evenement INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_evenement) REFERENCES EVENEMENT(id_evenement)
	);
	CREATE TABLE IF NOT EXISTS MESSAGE_UTILISATEUR(
		contenu VARCHAR(250),
		date DATETIME,
		id_utilisateur INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
	);
	CREATE TABLE IF NOT EXISTS MESSAGE_PRESTATAIRE(
		contenu VARCHAR(250),
		date DATETIME,
		id_prestataire INT,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire)
	);
	CREATE TABLE IF NOT EXISTS CAPTCHA(
		id_captcha INT AUTO_INCREMENT PRIMARY KEY,
		question VARCHAR(200),
		reponse VARCHAR(200)
	);
	CREATE TABLE IF NOT EXISTS SERVICE(
		id_service INT AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(100),
		description VARCHAR(200),
		disponibilite BOOLEAN DEFAULT true,
		id_utilisateur INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
	);
	CREATE TABLE IF NOT EXISTS PRESTATION(
		id_prestation INT AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(100),
		description VARCHAR(200),
		prix DOUBLE,
		lieu VARCHAR(100),
		nombre_place INT NOT NULL,
		date_ajout DATETIME,
		id_paiement INT NOT NULL,
		id_prestataire INT NOT NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire),
		FOREIGN KEY (id_paiement) REFERENCES PAIEMENT(id_paiement)
	);
	CREATE TABLE IF NOT EXISTS DEPOSE_UTILISATEUR(
		id_utilisateur INT,
		id_document INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_document) REFERENCES DOCUMENT(id_document)
	);
	CREATE TABLE IF NOT EXISTS DEPOSE_PRESTATAIRE(
		id_prestataire INT,
		id_document INT,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire),
		FOREIGN KEY (id_document) REFERENCES DOCUMENT(id_document)
	);
	CREATE TABLE IF NOT EXISTS AVIS(
		id_avis INT AUTO_INCREMENT PRIMARY KEY,
		description VARCHAR(200),
		titre VARCHAR(100),
		note INT,
		date DATETIME,
		id_prestataire INT NOT NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire)
	);
	CREATE TABLE IF NOT EXISTS FACTURE(
		id_facture INT AUTO_INCREMENT PRIMARY KEY,
		montant DOUBLE,
		date DATETIME,
		id_prestataire INT NOT NULL,
		FOREIGN KEY (id_prestataire) REFERENCES PRESTATAIRE(id_prestataire)
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
	CREATE TABLE IF NOT EXISTS APPARTIENT(
		quantite INT NOT NULL,
		id_produit INT,
		id_commande INT,
		FOREIGN KEY (id_produit) REFERENCES PRODUIT(id_produit),
		FOREIGN KEY (id_commande) REFERENCES COMMANDE(id_commande)
	);
	CREATE TABLE IF NOT EXISTS POSTE(
		id_utilisateur INT,
		id_avis INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_avis) REFERENCES AVIS(id_avis)
	);
	CREATE TABLE IF NOT EXISTS RESERVE(
		id_utilisateur INT,
		id_prestation INT,
		FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
		FOREIGN KEY (id_prestation) REFERENCES PRESTATION(id_prestation)
	);
	CREATE TABLE IF NOT EXISTS CONTIENT(
		id_service INT,
		id_prestation INT,
		id_planning INT,
		FOREIGN KEY (id_service) REFERENCES SERVICE(id_service),
		FOREIGN KEY (id_prestation) REFERENCES PRESTATION(id_prestation),
		FOREIGN KEY (id_planning) REFERENCES PLANNING(id_planning)
	);
	CREATE TABLE IF NOT EXISTS NEWSLETTER(
  		id_newsletter INT AUTO_INCREMENT PRIMARY KEY,
  		title VARCHAR(50) NOT NULL,
  		content VARCHAR(200) NOT NULL,
  		date_creation DATE NOT NULL
	);`

	_, err = DB.Exec(creationQuery)
	if err != nil {
		log.Fatal("Erreur création table : ", err)
	}
	
	fmt.Println("Base MariaDB initialisée !")
}