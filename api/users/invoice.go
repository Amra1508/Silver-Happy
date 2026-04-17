package users

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"net/http"
	"time"

	"main/db"
	"main/utils"

	"github.com/jung-kurt/gofpdf"
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

        UNION ALL

        SELECT p.id_paiement, p.prix, p.date_paiement, p.url_facture, CONCAT('Commande n°', c.id_commande) AS description
        FROM PAIEMENT p
        JOIN COMMANDE c ON p.id_paiement = c.id_paiement
        WHERE c.id_utilisateur = ? AND p.statut = 'valide'

        ORDER BY date_paiement DESC
    `

    rows, err := db.DB.Query(query, userID, userID, userID)
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

func GenerateInvoicePDF(response http.ResponseWriter, request *http.Request) {
    paymentID := request.PathValue("id")
    if paymentID == "" {
        http.Error(response, "ID manquant", http.StatusBadRequest)
        return
    }

    type InvoiceLine struct {
        Description string
        Qty         int
        UnitPrice   float64
        Total       float64
    }

    var lines []InvoiceLine
    var datePaiementStr string
    var clientNom, clientPrenom, clientEmail string
    var typeFacture string 
    var typePaiementSub string 

    query := `
        SELECT 
            'ABONNEMENT' as type_f, p.date_paiement, u.nom, u.prenom, u.email, 
            a.description, 1 as qty, p.prix as unit_p, p.prix as total_p, a.type_paiement
        FROM PAIEMENT p
        JOIN ABONNEMENT a ON p.id_paiement = a.id_paiement
        JOIN UTILISATEUR u ON u.id_abonnement = a.id_abonnement
        WHERE p.id_paiement = ?

        UNION ALL

        SELECT 
            'INSCRIPTION' as type_f, p.date_paiement, u.nom, u.prenom, u.email, 
            CONCAT('Inscription : ', e.nom), 1, p.prix, p.prix, ''
        FROM PAIEMENT p
        JOIN INSCRIPTION i ON p.id_paiement = i.id_paiement
        JOIN EVENEMENT e ON i.id_evenement = e.id_evenement
        JOIN UTILISATEUR u ON i.id_utilisateur = u.id_utilisateur
        WHERE p.id_paiement = ?

        UNION ALL

        SELECT 
            'COMMANDE' as type_f, p.date_paiement, u.nom, u.prenom, u.email, 
            pr.nom as description, lc.quantite, lc.prix_unitaire, (lc.quantite * lc.prix_unitaire), ''
        FROM PAIEMENT p
        JOIN COMMANDE c ON p.id_paiement = c.id_paiement
        JOIN LIGNE_COMMANDE lc ON c.id_commande = lc.id_commande
        JOIN PRODUIT pr ON lc.id_produit = pr.id_produit
        JOIN UTILISATEUR u ON c.id_utilisateur = u.id_utilisateur
        WHERE p.id_paiement = ?
    `

    rows, err := db.DB.Query(query, paymentID, paymentID, paymentID)
    if err != nil {
        http.Error(response, "Erreur SQL", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    totalFacture := 0.0
    for rows.Next() {
        var line InvoiceLine
        err := rows.Scan(&typeFacture, &datePaiementStr, &clientNom, &clientPrenom, &clientEmail, 
                         &line.Description, &line.Qty, &line.UnitPrice, &line.Total, &typePaiementSub)
        if err != nil { continue }
        lines = append(lines, line)
        totalFacture += line.Total
    }

    if len(lines) == 0 {
        http.Error(response, "Paiement introuvable", http.StatusNotFound)
        return
    }

    pdf := gofpdf.New("P", "mm", "A4", "")
    tr := pdf.UnicodeTranslatorFromDescriptor("")
    pdf.SetMargins(20, 20, 20)
    pdf.AddPage()

    pdf.SetFont("Arial", "B", 24)
    pdf.Cell(0, 10, tr("Facture"))
    pdf.SetXY(120, 20)
    pdf.SetFont("Arial", "B", 12)
    pdf.SetTextColor(150, 150, 150)
    pdf.CellFormat(70, 6, tr("Silver Happy"), "0", 0, "R", false, 0, "")
    
    pdf.SetY(45)
    pdf.SetFont("Arial", "", 10)
    pdf.SetTextColor(80, 80, 80)
    
    pdf.Text(20, 50, tr("Silver Happy - France"))
    pdf.SetFont("Arial", "B", 10)
    pdf.CellFormat(0, 5, tr("Numéro : ") + "SILVER-" + paymentID, "", 1, "R", false, 0, "")
    pdf.SetFont("Arial", "", 10)
    pdf.CellFormat(0, 5, tr("Émise le : ") + datePaiementStr[:10], "", 1, "R", false, 0, "")

    pdf.Ln(15)
    pdf.SetFont("Arial", "B", 11)
    pdf.Cell(0, 5, tr("Facturer à"))
    pdf.Ln(7)
    pdf.SetFont("Arial", "", 10)
    pdf.SetTextColor(100, 100, 100)
    pdf.Cell(0, 5, tr(clientPrenom + " " + clientNom))
    pdf.Ln(5)
    pdf.Cell(0, 5, tr(clientEmail))
    pdf.Ln(15)

    pdf.SetFont("Arial", "B", 10)
    pdf.SetFillColor(245, 245, 245)
    pdf.CellFormat(100, 10, "Description", "B", 0, "L", true, 0, "")
    pdf.CellFormat(20, 10, tr("Qté"), "B", 0, "C", true, 0, "")
    pdf.CellFormat(25, 10, "Prix unitaire", "B", 0, "R", true, 0, "")
    pdf.CellFormat(25, 10, "Montant", "B", 1, "R", true, 0, "")

    pdf.SetFont("Arial", "", 10)
    pdf.SetTextColor(50, 50, 50)

    for _, l := range lines {
        pdf.CellFormat(100, 10, tr(l.Description), "B", 0, "L", false, 0, "")
        pdf.CellFormat(20, 10, fmt.Sprintf("%d", l.Qty), "B", 0, "C", false, 0, "")
        pdf.CellFormat(25, 10, fmt.Sprintf("%.2f", l.UnitPrice) + tr("€"), "B", 0, "R", false, 0, "")
        pdf.CellFormat(25, 10, fmt.Sprintf("%.2f", l.Total) + tr("€"), "B", 1, "R", false, 0, "")

        if typeFacture == "ABONNEMENT" {
            pdf.SetFont("Arial", "I", 8)
            pdf.SetTextColor(120, 120, 120)
            
            dateDebut, _ := time.Parse("02/01/2006", datePaiementStr[:10])
            var dateFin time.Time
            if typePaiementSub == "mensuel" {
                dateFin = dateDebut.AddDate(0, 1, 0)
            } else {
                dateFin = dateDebut.AddDate(1, 0, 0)
            }
            periode := fmt.Sprintf("%s au %s", dateDebut.Format("02/01/2006"), dateFin.Format("02/01/2006"))
            pdf.Cell(0, 5, tr("Période du ") + periode)
            pdf.Ln(8)
            pdf.SetFont("Arial", "", 10)
            pdf.SetTextColor(50, 50, 50)
        }
    }

    pdf.Ln(10)
    pdf.SetFont("Arial", "B", 12)
    pdf.SetTextColor(28, 91, 143)
    pdf.Cell(120, 10, "")
    pdf.CellFormat(25, 10, tr("Total dû"), "T", 0, "R", false, 0, "")
    pdf.CellFormat(25, 10, fmt.Sprintf("%.2f", totalFacture) + tr("€"), "T", 1, "R", false, 0, "")

    fileName := fmt.Sprintf("Facture_SILVER_HAPPY_%s.pdf", paymentID)
    response.Header().Set("Content-Type", "application/pdf")
    response.Header().Set("Content-Disposition", "inline; filename="+fileName)
    pdf.Output(response)
}