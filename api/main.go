package main

import (
	"fmt"
	"net/http"

	"main/auth"
	"main/captcha"
	"main/db"
)

func main() {
    db.InitDB()

    http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
        fmt.Fprintf(w, "API Go : En ligne !")
    })

    http.HandleFunc("/auth/register", auth.Register)
    http.HandleFunc("/auth/login", auth.Login)
    http.HandleFunc("/auth/logout", auth.Logout)

    http.HandleFunc("/captcha/create", captcha.Create)
    http.HandleFunc("/captcha/read", captcha.Read)
    http.HandleFunc("/captcha/read-one/{id}", captcha.Read_One)
    http.HandleFunc("/captcha/delete/{id}", captcha.Delete)
    http.HandleFunc("/captcha/update/{id}", captcha.Update)

    if err := http.ListenAndServe(":8082", nil); err != nil {
        fmt.Println("Erreur serveur :", err)
    }
}