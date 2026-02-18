package main

import (
	"fmt"
	"net/http"
)

func main() {
	initDB()

	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "API Go : Je suis en ligne et je fonctionne sur le port 8080 !")
	})

	fmt.Println("Démarrage du serveur sur http://localhost:8080")

	http.HandleFunc("POST /register", register)
	http.HandleFunc("POST /login", login)
	http.HandleFunc("POST /logout", logout)

	if err := http.ListenAndServe(":8080", nil); err != nil {
		fmt.Println("Erreur lors de la création du serveur :", err)
	}
}