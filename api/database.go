package main

import (
	"database/sql"
	"fmt"
	"log"

	_ "modernc.org/sqlite"
)

var DB *sql.DB

func initDB() {
	var err error
	DB, err = sql.Open("sqlite", "database.sqlite")
	if err != nil {
		log.Fatal("Erreur lors de l'ouverture de la base de données :", err)
	}

	creationQuery := `
	CREATE TABLE IF NOT EXISTS utilisateur (
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		prenom VARCHAR(100),
		nom VARCHAR(100),
		date_naissance DATE,
		email VARCHAR(150) UNIQUE,
		mdp VARCHAR(255),
		num_telephone VARCHAR(20),
		statut VARCHAR(50) DEFAULT 'actif',
		date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
		premiere_connexion BOOLEAN DEFAULT 1,
		motif_bannisement TEXT,
		duree_bannissement INTEGER
	);`

	_, err = DB.Exec(creationQuery)
	if err != nil {
		log.Fatal("Erreur création table utilisateur : ", err)
	}

	fmt.Println("Base de données initialisée avec succès !")
}