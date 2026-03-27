package users

import (
	"database/sql"
	"encoding/json"
	"net/http"

	"main/db"
	"main/utils"
)

func GetUserInvoices(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	userID := request.PathValue("id")

	query := `
		SELECT p.id_paiement, p.prix, p.date_paiement, p.url_facture, a.description
		FROM PAIEMENT p
		JOIN ABONNEMENT a ON p.id_paiement = a.id_paiement
		JOIN UTILISATEUR u ON u.id_abonnement = a.id_abonnement
		WHERE u.id_utilisateur = ? AND p.statut = 'valide'

		UNION ALL

		SELECT p.id_paiement, p.prix, p.date_paiement, p.url_facture, CONCAT('Inscription : ', e.nom) AS description
		FROM PAIEMENT p
		JOIN INSCRIPTION i ON p.id_paiement = i.id_paiement
		JOIN EVENEMENT e ON i.id_evenement = e.id_evenement
		WHERE i.id_utilisateur = ? AND p.statut = 'valide'

		ORDER BY date_paiement DESC
	`

	rows, err := db.DB.Query(query, userID, userID)
	if err != nil {
		http.Error(response, "Erreur serveur", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var factures []map[string]interface{}
	for rows.Next() {
		var id int
		var prix float64
		var date sql.NullString
		var url sql.NullString
		var desc string

		if err := rows.Scan(&id, &prix, &date, &url, &desc); err == nil {
			factures = append(factures, map[string]interface{}{
				"id":          id,
				"montant":     prix,
				"date":        date.String,
				"description": desc,
				"url":         url.String,
			})
		}
	}

	if factures == nil {
		factures = []map[string]interface{}{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(factures)
}