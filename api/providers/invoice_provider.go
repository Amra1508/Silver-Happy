package providers

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"

	"github.com/jung-kurt/gofpdf"
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

func Download_Facture_Mensuelle(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	idFacture := request.PathValue("id")

	query := `
		SELECT f.mois_annee, f.montant, f.frais_plateforme, f.montant_net, f.date, f.statut, f.stripe_transfer_id,
		       p.nom, p.prenom, p.email, p.siret
		FROM FACTURE f
		JOIN PRESTATAIRE p ON f.id_prestataire = p.id_prestataire
		WHERE f.id_facture = ?
	`

	var moisAnnee, dateFacture, statut, nom, prenom, email, siret string
	var transferID sql.NullString
	var montantBrut, frais, montantNet float64

	err := db.DB.QueryRow(query, idFacture).Scan(
		&moisAnnee, &montantBrut, &frais, &montantNet, &dateFacture, &statut, &transferID,
		&nom, &prenom, &email, &siret,
	)

	if err != nil {
		http.Error(response, "Facture introuvable", http.StatusNotFound)
		return
	}

	pdf := gofpdf.New("P", "mm", "A4", "")
	pdf.AddPage()
	pdf.SetMargins(15, 15, 15)

	pdf.SetFont("Arial", "B", 16)
	pdf.SetTextColor(28, 91, 143)
	pdf.CellFormat(0, 10, "Silver Happy Pro", "", 0, "L", false, 0, "")
	
	pdf.SetFont("Arial", "B", 12)
	pdf.SetTextColor(107, 114, 128)
	pdf.CellFormat(0, 10, "RELEVE DE BENEFICES", "", 1, "R", false, 0, "")
	pdf.Ln(5)

	pdf.SetFont("Arial", "", 10)
	pdf.CellFormat(0, 5, fmt.Sprintf("Facture n : #%s", idFacture), "", 1, "R", false, 0, "")
	pdf.CellFormat(0, 5, fmt.Sprintf("Date : %s", dateFacture[:10]), "", 1, "R", false, 0, "")
	pdf.Ln(10)

	yPos := pdf.GetY()
	pdf.SetFillColor(249, 250, 251)
	pdf.Rect(15, yPos, 85, 35, "F")
	pdf.SetXY(18, yPos+5)
	pdf.SetFont("Arial", "B", 10)
	pdf.Cell(0, 5, "Emis par :")
	pdf.Ln(5)
	pdf.SetFont("Arial", "", 10)
	pdf.SetX(18)
	pdf.Cell(0, 5, "Silver Happy")
	pdf.Ln(5)
	pdf.SetX(18)
	pdf.Cell(0, 5, "Plateforme de mise en relation")

	pdf.SetXY(110, yPos)
	pdf.Rect(110, yPos, 85, 35, "F")
	pdf.SetXY(113, yPos+5)
	pdf.SetFont("Arial", "B", 10)
	pdf.Cell(0, 5, "Destinataire :")
	pdf.Ln(5)
	pdf.SetFont("Arial", "", 10)
	pdf.SetX(113)
	pdf.Cell(0, 5, fmt.Sprintf("%s %s", prenom, nom))
	pdf.Ln(5)
	pdf.SetX(113)
	pdf.Cell(0, 5, fmt.Sprintf("Email: %s", email))
	pdf.Ln(5)
	pdf.SetX(113)
	pdf.Cell(0, 5, fmt.Sprintf("SIRET: %s", siret))
	pdf.Ln(15)

	pdf.SetFillColor(28, 91, 143)
	pdf.SetTextColor(255, 255, 255)
	pdf.SetFont("Arial", "B", 10)
	pdf.CellFormat(100, 10, "Description", "1", 0, "L", true, 0, "")
	pdf.CellFormat(40, 10, "Periode", "1", 0, "C", true, 0, "")
	pdf.CellFormat(40, 10, "Montant Brut", "1", 1, "R", true, 0, "")

	pdf.SetTextColor(51, 51, 51)
	pdf.SetFont("Arial", "", 10)
	pdf.CellFormat(100, 10, "Revenus (Prestations & Evenements)", "1", 0, "L", false, 0, "")
	pdf.CellFormat(40, 10, moisAnnee, "1", 0, "C", false, 0, "")
	pdf.CellFormat(40, 10, fmt.Sprintf("%.2f EUR", montantBrut), "1", 1, "R", false, 0, "")
	pdf.Ln(10)

	pdf.SetX(120)
	pdf.SetFont("Arial", "B", 10)
	pdf.CellFormat(40, 8, "Total Brut :", "", 0, "L", false, 0, "")
	pdf.CellFormat(30, 8, fmt.Sprintf("%.2f EUR", montantBrut), "", 1, "R", false, 0, "")

	pdf.SetX(120)
	pdf.SetTextColor(239, 68, 68) 
	pdf.CellFormat(40, 8, "Frais Plateforme (1%) :", "", 0, "L", false, 0, "")
	pdf.CellFormat(30, 8, fmt.Sprintf("- %.2f EUR", frais), "", 1, "R", false, 0, "")

	pdf.SetX(120)
	pdf.SetFont("Arial", "B", 12)
	pdf.SetTextColor(16, 185, 129)
	pdf.CellFormat(40, 10, "Montant Net Verse :", "T", 0, "L", false, 0, "")
	pdf.CellFormat(30, 10, fmt.Sprintf("%.2f EUR", montantNet), "T", 1, "R", false, 0, "")

	pdf.SetY(-40)
	pdf.SetFont("Arial", "I", 8)
	pdf.SetTextColor(156, 163, 175)
	virementRef := "En attente"
	if transferID.Valid { virementRef = transferID.String }
	pdf.CellFormat(0, 5, fmt.Sprintf("Statut du virement : %s | Ref Stripe : %s", statut, virementRef), "", 1, "C", false, 0, "")
	pdf.CellFormat(0, 5, "Ce document est un justificatif de paiement genere automatiquement par Silver Happy.", "", 1, "C", false, 0, "")

	response.Header().Set("Content-Type", "application/pdf")
	response.Header().Set("Content-Disposition", fmt.Sprintf("attachment; filename=Facture_SilverHappy_%s.pdf", idFacture))
	
	err = pdf.Output(response)
	if err != nil {
		fmt.Println("Erreur lors de la generation du PDF:", err)
	}
}