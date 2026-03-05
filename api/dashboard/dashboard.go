package dashboard

import (
	"encoding/json"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"
)

func Seniors_Count(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	var count int
	query := `SELECT COUNT(*) FROM UTILISATEUR WHERE statut = 'user' AND date_creation >= DATE_SUB(NOW(), INTERVAL 1 MONTH)`
	err := db.DB.QueryRow(query).Scan(&count)
	
	if err != nil {
		http.Error(response, "Erreur lors du comptage des seniors", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(models.Count{Count: count})
}

func Prestataires_Count(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	var count int
	query := `SELECT COUNT(*) FROM PRESTATAIRE WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 1 MONTH)`
	err := db.DB.QueryRow(query).Scan(&count)
	
	if err != nil {
		http.Error(response, "Erreur lors du comptage des prestataires", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(models.Count{Count: count})
}

func Abonnement_Count(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	var count int
	query := `SELECT COUNT(*) FROM UTILISATEUR WHERE id_abonnement IS NOT NULL AND debut_abonnement >= DATE_SUB(NOW(), INTERVAL 1 MONTH)`
	err := db.DB.QueryRow(query).Scan(&count)
	
	if err != nil {
		http.Error(response, "Erreur lors du comptage des abonnements", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(models.Count{Count: count})
}