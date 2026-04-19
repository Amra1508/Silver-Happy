package admin

import (
	"encoding/json"
	"main/db"
	"main/utils"
	"net/http"
)

func Get_All_Invoices_For_Accountant(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	query := `
		SELECT f.id_facture, f.montant, f.frais_plateforme, f.montant_net, f.mois_annee, f.date, f.statut, IFNULL(f.stripe_transfer_id, ''),
			p.nom, p.prenom, p.email, p.siret, p.id_prestataire
		FROM FACTURE f
		JOIN PRESTATAIRE p ON f.id_prestataire = p.id_prestataire
		ORDER BY f.date DESC
	`

	rows, err := db.DB.Query(query)
	if err != nil {
		http.Error(response, "Erreur lors de la récupération des factures", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var factures []map[string]interface{}

	for rows.Next() {
		var id, id_prestataire int
		var montant, frais, net float64
		var mois, date, statut, transferId, nom, prenom, email, siret string

		if err := rows.Scan(&id, &montant, &frais, &net, &mois, &date, &statut, &transferId, &nom, &prenom, &email, &siret, &id_prestataire); err == nil {
			factures = append(factures, map[string]interface{}{
				"id_facture":         id,
				"montant":            montant,
				"frais_plateforme":   frais,
				"montant_net":        net,
				"mois_annee":         mois,
				"date":               date,
				"statut":             statut,
				"stripe_transfer_id": transferId,
				"prestataire":        prenom + " " + nom,
				"email":              email,
				"siret":              siret,
				"id_prestataire": 	  id_prestataire,
			})
		}
	}

	if factures == nil {
		factures = []map[string]interface{}{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(factures)
}