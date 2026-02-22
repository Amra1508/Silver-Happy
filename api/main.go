package main

import (
	"fmt"
	"net/http"
)

func main() {
	initDB()

	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "API Go : Je suis en ligne et je fonctionne sur le port 8082 !")
	})

	fmt.Println("Démarrage du serveur sur http://localhost:8082")

	http.HandleFunc("/register", register)
	http.HandleFunc("/login", login)
	http.HandleFunc("/logout", logout)

	if err := http.ListenAndServe(":8082", nil); err != nil {
		fmt.Println("Erreur lors de la création du serveur :", err)
	}
}