package main

import (
	"fmt"
	"net/http"

	"main/admin"
	"main/auth"
	"main/communication"
	"main/dashboard"
	"main/db"
	"main/providers"
	"main/services"
	"main/users"
)

func main() {
	db.InitDB()

	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "API Go : En ligne !")
	})


	http.Handle("/uploads/", http.StripPrefix("/uploads/", http.FileServer(http.Dir("./uploads"))))
	http.HandleFunc("/create-checkout", users.Paiement_Abonnement)

	http.HandleFunc("/auth/register", auth.Register)
	http.HandleFunc("/auth/login", auth.Login)
	http.HandleFunc("/auth/logout", auth.Logout)
	http.HandleFunc("/auth/me", auth.Me)
	http.HandleFunc("/auth/update", auth.Update)
	http.HandleFunc("/auth/tutorial-seen", auth.TutorialSeen)

	http.HandleFunc("/produit/create", services.Create_Produit)
	http.HandleFunc("/produit/read", services.Read_Produit)
	http.HandleFunc("/produit/read-one/{id}", services.Read_One_Produit)
	http.HandleFunc("/produit/delete/{id}", services.Delete_Produit)
	http.HandleFunc("/produit/update/{id}", services.Update_Produit)
	http.HandleFunc("/code/create", services.Create_Code)
	http.HandleFunc("/code/read", services.Read_Code)
	http.HandleFunc("/code/delete/{id}", services.Delete_Code)
	http.HandleFunc("/code/update/{id}", services.Update_Code)

	http.HandleFunc("/panier/add", services.Add_Panier)
	http.HandleFunc("/panier/get", services.Get_Panier)
	http.HandleFunc("/panier/delete", services.Delete_Panier)
	http.HandleFunc("/panier/check", services.Check_Panier)
	http.HandleFunc("/paiement-panier", services.Paiement_Panier)
	http.HandleFunc("/success-basket", services.Success_Basket)

	http.HandleFunc("/seniors/read", users.Read_User_Admin)
	http.HandleFunc("/seniors/read-presta", users.Read_User_Prestataire)
	http.HandleFunc("/admin/read", users.Read_Admin)
	http.HandleFunc("/seniors/create", users.Create_User)
	http.HandleFunc("/seniors/update/{id}", users.Update_User)
	http.HandleFunc("/seniors/delete/{id}", users.Delete_User)
	http.HandleFunc("/seniors/ban/{id}", users.Ban_User)
	http.HandleFunc("/success-subscription", users.Success_Subscription)
	http.HandleFunc("/abonnement/cancel", users.Cancel_Subscription)

	http.HandleFunc("/factures/user/{id}", users.GetUserInvoices)
	http.HandleFunc("/factures/generate/{id}", users.GenerateInvoicePDF)

	http.HandleFunc("/planning", users.Read_User_Planning)
	http.HandleFunc("/search", users.SearchAll)

	http.HandleFunc("/service/read", services.Read_Service)
	http.HandleFunc("/service/create", services.Create_Service)
	http.HandleFunc("/service/read-one/{id}", services.Read_One_Service)
	http.HandleFunc("/service/update/{id}", services.Update_Service)
	http.HandleFunc("/service/delete/{id}", services.Delete_Service)
    http.HandleFunc("/service/user/{id}", services.Read_User_Services)
    http.HandleFunc("/service/register/{id}", services.Register_Service)
    http.HandleFunc("/service/unregister/{id}", services.Unregister_Service)
	http.HandleFunc("/services/filter", services.GetServicesByCategory)
	http.HandleFunc("/service/checkout/{id}", services.CreateServiceCheckoutSession)
	http.HandleFunc("/success-service", services.Success_Service_Payment)

	http.HandleFunc("/categorie/read", services.Read_Categorie)
	http.HandleFunc("/categorie/read/{id}", services.Read_One_Categorie)
	http.HandleFunc("/categorie/create", services.Create_Categorie)
	http.HandleFunc("/categorie/update/{id}", services.Update_Categorie)
	http.HandleFunc("/categorie/delete/{id}", services.Delete_Categorie)

	http.HandleFunc("/conseil/read", communication.Read_Conseil)
	http.HandleFunc("/conseil/create", communication.Create_Conseil)
	http.HandleFunc("/conseil/read-one/{id}", communication.Read_One_Conseil)
	http.HandleFunc("/conseil/update/{id}", communication.Update_Conseil)
	http.HandleFunc("/conseil/delete/{id}", communication.Delete_Conseil)
	http.HandleFunc("/conseil/like/{id}", communication.Like_Conseil)
	http.HandleFunc("/conseil/unlike/{id}", communication.Unlike_Conseil)

	http.HandleFunc("/avis/read", communication.Read_Avis)
	http.HandleFunc("/avis/read-one/{id}", communication.Read_One_Avis)
	http.HandleFunc("/avis/read-mine/{id}", communication.Read_My_Avis)
	http.HandleFunc("/avis/update/{id}", communication.Update_Avis)
	http.HandleFunc("/avis/delete/{id}", communication.Delete_Avis)
	http.HandleFunc("/avis/create", communication.Create_Avis)

	http.HandleFunc("/prestataires/read", users.Read_Prestataire)
	http.HandleFunc("/prestataires/top", users.Get_Prestataire_Top)
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
	http.HandleFunc("/dashboard/revenus/details", dashboard.GetRevenusDetails)

	http.HandleFunc("/message/get/{id1}/with/{id2}", communication.Get_Message)
	http.HandleFunc("/message/add", communication.Add_Message)
	http.HandleFunc("/message/delete/{id}", communication.Delete_Message)

	http.HandleFunc("/evenement/read", services.Read_Evenement)
	http.HandleFunc("/evenement/create", services.Create_Evenement)
	http.HandleFunc("/evenement/update/{id}", services.Update_Evenement)
	http.HandleFunc("/evenement/delete/{id}", services.Delete_Evenement)
	http.HandleFunc("/evenement/prestataires/link/{id}", services.Link_Prestataire_Evenement)
    http.HandleFunc("/evenement/prestataires/read/{id}", services.Read_Prestataires_For_Evenement)
    http.HandleFunc("/evenement/prestataires/unlink/{id}/{id_prestataire}", services.Unlink_Prestataire_Evenement)
	http.HandleFunc("/evenement/register/{id}", services.Register_Evenement)
	http.HandleFunc("/evenement/user/{id}", services.Read_User_Evenements)
	http.HandleFunc("/evenement/unregister/{id}", services.Unregister_Evenement)
	http.HandleFunc("/evenement/filter", services.GetEvenementsByCategory)

	http.HandleFunc("/evenement/checkout/{id}", services.CreateEventCheckoutSession)
	http.HandleFunc("/success-event", services.Success_Event_Payment)

	http.HandleFunc("/auth/register-provider", providers.RegisterPrestataire)
	http.HandleFunc("/auth/login-provider", providers.LoginPrestataire)
    http.HandleFunc("/auth/logout-provider", providers.LogoutPrestataire)
    http.HandleFunc("/auth/me-provider", providers.MePrestataire)
	http.HandleFunc("/auth/update-provider", providers.UpdatePrestataire)

	providers.StartInvoiceCron()
	http.HandleFunc("/admin/test-virements", providers.TriggerInvoicesManual)


	http.HandleFunc("/prestataire/stripe-connect", providers.CreateStripeAccountLink)

	http.HandleFunc("/prestataire/evenement/create", providers.Create_Prestataire_Evenement)
	http.HandleFunc("/prestataire/{id}/events", providers.Get_Prestataire_Events)
	http.HandleFunc("/prestataire/evenement/{id}/participants", providers.Get_Event_Participants)

	http.HandleFunc("/prestataire/planning", providers.Read_Provider_Planning)
	
	http.HandleFunc("/prestataire/{id}/profile", users.Read_One_Prestataire_Profile)

	http.HandleFunc("/prestataire/paiement-abonnement", providers.Paiement_Abonnement_Prestataire)
	http.HandleFunc("/prestataire/success-subscription", providers.Success_Subscription_Prestataire)
	http.HandleFunc("/prestataire/cancel-subscription", providers.Cancel_Subscription_Prestataire)
	http.HandleFunc("/prestataire/paiement-boost", providers.Paiement_Boost)
	http.HandleFunc("/prestataire/success-boost", providers.Success_Boost)

	http.HandleFunc("/prestataire/{id}/invoices", providers.Get_Invoices_Prestataire)
	http.HandleFunc("/prestataire/{id}/factures-mensuelles", providers.Get_Monthly_Invoices)
	http.HandleFunc("/prestataire/facture/{id}/download", providers.Download_Facture_Mensuelle)

	http.HandleFunc("/prestataire/{id}/revenues", providers.Revenus_Prestataire)

	http.HandleFunc("/prestataire/{id}/read-avis", communication.Read_Prestataire_Avis)
	http.HandleFunc("/prestataire/{id}/read-one", communication.Read_One_Prestataire_Avis)
	http.HandleFunc("/prestataire/{id}/note-moyenne", users.Get_Note_Moyenne)
	
	http.HandleFunc("/message/prestataire/get/{id1}/with/{id2}", providers.Get_Message)
	http.HandleFunc("/message/prestataire/add", providers.Add_Message)
	http.HandleFunc("/message/prestataire/delete/{id}", providers.Delete_Message)
	http.HandleFunc("/prestataire/read", users.List_Prestataires)
	http.HandleFunc("/message/prestataire/accept/{id}", providers.Accept_Offer)
	http.HandleFunc("/message/prestataire/reject/{id}", providers.Reject_Offer)

	http.HandleFunc("/prestataire/services/{id}/get", providers.Get_Services_Provider)
	http.HandleFunc("/prestataire/services/{id}/create", providers.Create_Service_Provider)
	http.HandleFunc("/prestataire/services/{id}/{id_service}/delete", providers.Delete_Service_Provider)
	http.HandleFunc("/prestataire/services/{id}/{id_service}/update", providers.Update_Service_Provider)
	http.HandleFunc("/prestataire/disponibilites/{id}/date/{date}/delete", providers.Delete_Disponibilites_By_Date)

	http.HandleFunc("/prestataire/disponibilites/{id}/create", providers.Create_Disponibilite_Slot)
	http.HandleFunc("/prestataire/disponibilites/{id}/get", providers.Get_Provider_Dispos)
	http.HandleFunc("/prestataire/disponibilites/{id}/{id_disponibilite}/delete", providers.Delete_Disponibilite_Slot)

	http.HandleFunc("/prestataire/planning/{id}/available", providers.Get_Available_Slots)
	http.HandleFunc("/senior/reservations/create", providers.Create_Reservation)

	http.HandleFunc("/prestataire/documents/{id}/get", providers.Get_Documents)
	http.HandleFunc("/prestataire/documents/{id}/create", providers.Upload_Document)
	http.HandleFunc("/documents/{id}/delete", providers.Delete_Document)

	http.HandleFunc("/comptable/factures", admin.Get_All_Invoices_For_Accountant)

	if err := http.ListenAndServe(":8082", nil); err != nil {
		fmt.Println("Erreur serveur :", err)
	}
}
