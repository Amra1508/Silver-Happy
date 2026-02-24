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
		date_naissance DATE,
		num_telephone VARCHAR(20),
		email VARCHAR(150) UNIQUE,
		mdp VARCHAR(255),
		pays VARCHAR(100),
		adresse VARCHAR(100),
		ville VARCHAR(100),
		code_postal VARCHAR(5),
		statut VARCHAR(50) DEFAULT 'user',
		date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
	);`

	_, err = DB.Exec(creationQuery)
	if err != nil {
		log.Fatal("Erreur création table : ", err)
	}
	
	fmt.Println("Base MariaDB initialisée !")
}