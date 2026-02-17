package main

import (
	"fmt"
	"net/http"
)

func main() {
	initDB()

	fmt.Println("Démarrage du serveur sur http://localhost:8081")

	http.HandleFunc("POST /register", register)
	http.HandleFunc("POST /login", login)
	http.HandleFunc("POST /logout", logout)

	if err := http.ListenAndServe(":8081", nil); err != nil {
		fmt.Println("Erreur lors de la création du serveur :", err)
	}
}