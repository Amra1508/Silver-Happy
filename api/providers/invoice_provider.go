package providers

import (
	"encoding/json"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"
)

func Get_Invoices_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	sqlQuery := `
		SELECT p.id_paiement, IFNULL(p.date_paiement, NOW()), p.prix, p.statut, IFNULL(p.url_facture, ''), a.description, COALESCE(a.url_contrat, '')
		FROM PAIEMENT p
		JOIN ABONNEMENT a ON p.id_paiement = a.id_paiement
		JOIN PRESTATAIRE pr ON pr.id_abonnement = a.id_abonnement
		WHERE pr.id_prestataire = ?
		ORDER BY p.date_paiement DESC
	`

	rows, err := db.DB.Query(sqlQuery, providerID)
	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var invoices []models.InvoiceResponse

	for rows.Next() {
		var inv models.InvoiceResponse
		var urlContrat string

		if err := rows.Scan(&inv.ID, &inv.DatePaiement, &inv.Prix, &inv.Statut, &inv.URLFacture, &inv.Description, &urlContrat); err == nil {
			if urlContrat != "" {
				inv.URLContrat = utils.GetAPIBaseURL() + urlContrat
			}
			invoices = append(invoices, inv)
		}
	}

	if invoices == nil {
		invoices = []models.InvoiceResponse{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(invoices)
}

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
			JOIN reservation_service rs ON p.id_paiement = rs.id_paiement
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

func Get_Monthly_Invoices(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	sqlQuery := `
		SELECT id_facture, montant, frais_plateforme, montant_net, mois_annee, date, statut
		FROM FACTURE
		WHERE id_prestataire = ?
		ORDER BY mois_annee DESC
	`

	rows, err := db.DB.Query(sqlQuery, providerID)
	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var invoices []models.FactureMensuelle

	for rows.Next() {
		var inv models.FactureMensuelle

		if err := rows.Scan(&inv.IDFacture, &inv.MontantBrut, &inv.FraisPlateforme, &inv.MontantNet, &inv.MoisAnnee, &inv.Date, &inv.Statut); err == nil {
			invoices = append(invoices, inv)
		}
	}

	if invoices == nil {
		invoices = []models.FactureMensuelle{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(invoices)
}