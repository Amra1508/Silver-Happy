package providers

import (
	"encoding/json"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"
)

func Revenus_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	query := `
        SELECT date_paiement as date, SUM(total) as total
        FROM (
            SELECT DATE(p.date_paiement) as date_paiement, SUM(p.prix * 0.99) as total 
            FROM PAIEMENT p
            JOIN INSCRIPTION i ON p.id_paiement = i.id_paiement
            JOIN PRESTATAIRE_EVENEMENT pe ON i.id_evenement = pe.id_evenement
            WHERE p.statut = 'valide' 
              AND pe.id_prestataire = ?
              AND p.date_paiement >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
            GROUP BY DATE(p.date_paiement) 

            UNION ALL

            SELECT DATE(p.date_paiement) as date_paiement, SUM(p.prix * 0.99) as total 
            FROM PAIEMENT p
            JOIN RESERVATION_SERVICE rs ON p.id_paiement = rs.id_paiement
            JOIN SERVICE s ON rs.id_service = s.id_service
            WHERE p.statut = 'valide' 
              AND s.id_prestataire = ?
              AND p.date_paiement >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
            GROUP BY DATE(p.date_paiement)
        ) as revenus_combines
        GROUP BY date_paiement
        ORDER BY date_paiement ASC
    `

	rows, err := db.DB.Query(query, providerID, providerID)
	if err != nil {
		http.Error(response, "Erreur BDD lors de la récupération des revenus", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var revenues []models.Revenue
	for rows.Next() {
		var r models.Revenue
		if err := rows.Scan(&r.Date, &r.Total); err == nil {
			revenues = append(revenues, r)
		}
	}

	if revenues == nil {
		revenues = []models.Revenue{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(revenues)
}
