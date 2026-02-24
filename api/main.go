package main

import (
	"fmt"
	"net/http"

	"main/auth"
	"main/db"
)

func main() {
    db.InitDB()

    http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
        fmt.Fprintf(w, "API Go : En ligne !")
    })

    http.HandleFunc("/register", auth.Register)
    http.HandleFunc("/login", auth.Login)
    http.HandleFunc("/logout", auth.Logout)

    if err := http.ListenAndServe(":8082", nil); err != nil {
        fmt.Println("Erreur serveur :", err)
    }
}