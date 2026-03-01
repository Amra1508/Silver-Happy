package main

import (
	"fmt"
	"net/http"

	"main/auth"
	"main/captcha"
	"main/db"
	"main/services"
	"main/users"
)

func main() {
	db.InitDB()

	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "API Go : En ligne !")
	})

	http.HandleFunc("/auth/register", auth.Register)
	http.HandleFunc("/auth/login", auth.Login)
	http.HandleFunc("/auth/logout", auth.Logout)

	http.HandleFunc("/captcha/create", captcha.Create_Captcha)
	http.HandleFunc("/captcha/read", captcha.Read_Captcha)
	http.HandleFunc("/captcha/read-one/{id}", captcha.Read_One_Captcha)
	http.HandleFunc("/captcha/delete/{id}", captcha.Delete_Captcha)
	http.HandleFunc("/captcha/update/{id}", captcha.Update_Captcha)

	http.HandleFunc("/produit/create", services.Create_Produit)
	http.HandleFunc("/produit/read", services.Read_Produit)
	http.HandleFunc("/produit/read-one/{id}", services.Read_One_Produit)
	http.HandleFunc("/produit/delete/{id}", services.Delete_Produit)
	http.HandleFunc("/produit/update/{id}", services.Update_Produit)

	http.HandleFunc("/seniors/read", users.Read_User)
	http.HandleFunc("/seniors/create", users.Create_User)
	http.HandleFunc("/seniors/update/{id}", users.Update_User)
	http.HandleFunc("/seniors/delete/{id}", users.Delete_User)
	http.HandleFunc("/seniors/ban/{id}", users.Ban_User)

	if err := http.ListenAndServe(":8082", nil); err != nil {
		fmt.Println("Erreur serveur :", err)
	}
}
