package main

import (
	"fmt"
	"net/http"

	"main/auth"
	"main/captcha"
	"main/communication"
	"main/dashboard"
	"main/db"
	"main/services"
	"main/users"
)

func main() {
	db.InitDB()

	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "API Go : En ligne !")
	})

	http.Handle("/uploads/", http.StripPrefix("/uploads/", http.FileServer(http.Dir("./uploads"))))

	http.HandleFunc("/auth/register", auth.Register)
	http.HandleFunc("/auth/login", auth.Login)
	http.HandleFunc("/auth/logout", auth.Logout)
	http.HandleFunc("/auth/me", auth.Me)
	http.HandleFunc("/auth/update", auth.Update)

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

	http.HandleFunc("/service/read", services.Read_Service)
	http.HandleFunc("/service/create", services.Create_Service)
	http.HandleFunc("/service/read-one/{id}", services.Read_One_Service)
	http.HandleFunc("/service/update/{id}", services.Update_Service)
	http.HandleFunc("/service/delete/{id}", services.Delete_Service)

	http.HandleFunc("/conseil/read", communication.Read_Conseil)
	http.HandleFunc("/conseil/create", communication.Create_Conseil)
	http.HandleFunc("/conseil/read-one/{id}", communication.Read_One_Conseil)
	http.HandleFunc("/conseil/update/{id}", communication.Update_Conseil)
	http.HandleFunc("/conseil/delete/{id}", communication.Delete_Conseil)

	http.HandleFunc("/prestataires/read", users.Read_Prestataire)
	http.HandleFunc("/prestataires/create", users.Create_Prestataire)
	http.HandleFunc("/prestataires/update/{id}", users.Update_Prestataire)
	http.HandleFunc("/prestataires/delete/{id}", users.Delete_Prestataire)
	http.HandleFunc("/prestataires/documents/{id}", users.Read_Prestataire_Documents)
	http.HandleFunc("/prestataires/upload/{id}", users.Upload_Prestataire_Document)
	http.HandleFunc("/prestataires/document/delete/{id}", users.Delete_Prestataire_Document)

	http.HandleFunc("/dashboard/seniors", dashboard.Seniors_Count)
	http.HandleFunc("/dashboard/prestataires", dashboard.Prestataires_Count)
	http.HandleFunc("/dashboard/abonnement", dashboard.Abonnement_Count)
	http.HandleFunc("/dashboard/revenus", dashboard.Revenus)

	http.HandleFunc("/message/get/{id1}/with/{id2}", communication.Get_Message)
	http.HandleFunc("/message/add", communication.Add_Message)
	http.HandleFunc("/message/delete/{id}", communication.Delete_Message)

	http.HandleFunc("/evenement/read", services.Read_Evenement)
	http.HandleFunc("/evenement/create", services.Create_Evenement)
	http.HandleFunc("/evenement/update/{id}", services.Update_Evenement)
	http.HandleFunc("/evenement/delete/{id}", services.Delete_Evenement)

	if err := http.ListenAndServe(":8082", nil); err != nil {
		fmt.Println("Erreur serveur :", err)
	}
}
