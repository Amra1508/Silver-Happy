package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

var DB *sql.DB

func initDB() {
	dsn := fmt.Sprintf("%s:%s@tcp(%s:3306)/%s?parseTime=true",
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
	CREATE TABLE IF NOT EXISTS utilisateur (
		id INT AUTO_INCREMENT PRIMARY KEY,
		prenom VARCHAR(100),
		nom VARCHAR(100),
		email VARCHAR(150) UNIQUE,
		mdp VARCHAR(255),
		num_telephone VARCHAR(20),
		statut VARCHAR(50) DEFAULT 'actif',
		date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
	);`

	_, err = DB.Exec(creationQuery)
	if err != nil {
		log.Fatal("Erreur création table : ", err)
	}
	fmt.Println("Base MariaDB initialisée !")
}