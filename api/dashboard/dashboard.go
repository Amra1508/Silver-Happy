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

func Revenus(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    query := `
        SELECT 
            DATE(date_paiement) as date, 
            SUM(
                CASE 
                    WHEN id_paiement IN (SELECT id_paiement FROM INSCRIPTION WHERE id_paiement IS NOT NULL) THEN prix * 0.01
                    ELSE prix 
                END
            ) as total 
        FROM PAIEMENT 
        WHERE statut = 'valide' 
          AND date_paiement >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
        GROUP BY DATE(date_paiement) 
        ORDER BY date_paiement ASC
    `
    
    rows, err := db.DB.Query(query)
    if err != nil {
        http.Error(response, "Erreur lors de la récupération des revenus", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    var revenues []models.Revenue

    for rows.Next() {
        var r models.Revenue
        if err := rows.Scan(&r.Date, &r.Total); err != nil {
            continue
        }
        revenues = append(revenues, r)
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(revenues)
}