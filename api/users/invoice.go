package users

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"net/http"
	"time"

	"main/db"
	"main/models"
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

        UNION ALL

        SELECT p.id_paiement, p.prix, p.date_paiement, p.url_facture, CONCAT('Service : ', s.nom) AS description
        FROM PAIEMENT p
        JOIN RESERVATION_SERVICE r ON p.id_paiement = r.id_paiement
        JOIN SERVICE s ON r.id_service = s.id_service
        WHERE r.id_utilisateur = ? AND p.statut = 'valide'

        ORDER BY date_paiement DESC
    `

    rows, err := db.DB.Query(query, userID, userID, userID, userID)
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

    var lines []models.InvoiceLine
    var datePaiementStr, clientNom, clientPrenom, clientEmail string 

    rowsAbo, _ := db.DB.Query(`
        SELECT p.date_paiement, u.nom, u.prenom, u.email, a.description, p.prix, a.type_paiement
        FROM PAIEMENT p
        JOIN ABONNEMENT a ON p.id_paiement = a.id_paiement
        JOIN UTILISATEUR u ON u.id_abonnement = a.id_abonnement
        WHERE p.id_paiement = ?`, paymentID)
    
    for rowsAbo.Next() {
        var l models.InvoiceLine
        l.Type = "ABONNEMENT"
        l.Qty = 1
        rowsAbo.Scan(&datePaiementStr, &clientNom, &clientPrenom, &clientEmail, &l.Description, &l.UnitPrice, &l.Info1)
        l.Total = l.UnitPrice
        lines = append(lines, l)
    }
    
    rowsEvent, _ := db.DB.Query(`
        SELECT p.date_paiement, u.nom, u.prenom, u.email, e.nom, p.prix, e.date_debut, e.date_fin
        FROM PAIEMENT p
        JOIN INSCRIPTION i ON p.id_paiement = i.id_paiement
        JOIN EVENEMENT e ON i.id_evenement = e.id_evenement
        JOIN UTILISATEUR u ON i.id_utilisateur = u.id_utilisateur
        WHERE p.id_paiement = ?`, paymentID)

    for rowsEvent.Next() {
        var l models.InvoiceLine
        l.Type = "INSCRIPTION"
        l.Qty = 1
        rowsEvent.Scan(&datePaiementStr, &clientNom, &clientPrenom, &clientEmail, &l.Description, &l.UnitPrice, &l.Info1, &l.Info2)
        l.Description = "Inscription : " + l.Description
        l.Total = l.UnitPrice
        lines = append(lines, l)
    }

    rowsCmd, _ := db.DB.Query(`
        SELECT p.date_paiement, u.nom, u.prenom, u.email, pr.nom, lc.quantite, lc.prix_unitaire
        FROM PAIEMENT p
        JOIN COMMANDE c ON p.id_paiement = c.id_paiement
        JOIN LIGNE_COMMANDE lc ON c.id_commande = lc.id_commande
        JOIN PRODUIT pr ON lc.id_produit = pr.id_produit
        JOIN UTILISATEUR u ON c.id_utilisateur = u.id_utilisateur
        WHERE p.id_paiement = ?`, paymentID)

    subTotalCmd := 0.0
    for rowsCmd.Next() {
        var l models.InvoiceLine
        l.Type = "COMMANDE"
        rowsCmd.Scan(&datePaiementStr, &clientNom, &clientPrenom, &clientEmail, &l.Description, &l.Qty, &l.UnitPrice)
        l.Total = float64(l.Qty) * l.UnitPrice
        subTotalCmd += l.Total
        lines = append(lines, l)
    }

    var promoCode string
    var promoValeur float64
    var promoType string

    errPromo := db.DB.QueryRow(`
        SELECT cr.code, cr.valeur, cr.type
        FROM COMMANDE c
        JOIN CODE_REDUCTION cr ON c.id_reduction = cr.id_reduction
        WHERE c.id_paiement = ?`, paymentID).Scan(&promoCode, &promoValeur, &promoType)

    if errPromo == nil && promoCode != "" {
        var discount float64
        if promoType == "pourcentage" {
            discount = subTotalCmd * (promoValeur / 100)
        } else {
            discount = promoValeur
        }

        lines = append(lines, models.InvoiceLine{
            Description: "Code promo : " + promoCode,
            Qty:         1,
            UnitPrice:   -discount,
            Total:       -discount,
            Type:        "PROMO",
        })
    }

    var fraisPort float64
    errFrais := db.DB.QueryRow(`
        SELECT montant_frais_port 
        FROM COMMANDE 
        WHERE id_paiement = ?`, paymentID).Scan(&fraisPort)

    if errFrais == nil && fraisPort > 0 {
        lines = append(lines, models.InvoiceLine{
            Description: "Frais de port",
            Qty:         1,
            UnitPrice:   fraisPort,
            Total:       fraisPort,
            Type:        "LIVRAISON",
        })
    }

    rowsServ, _ := db.DB.Query(`
        SELECT p.date_paiement, u.nom, u.prenom, u.email, s.nom, 1, p.prix, r.date_heure
        FROM PAIEMENT p
        JOIN RESERVATION_SERVICE r ON p.id_paiement = r.id_paiement
        JOIN SERVICE s ON r.id_service = s.id_service
        JOIN UTILISATEUR u ON r.id_utilisateur = u.id_utilisateur
        WHERE p.id_paiement = ?`, paymentID)

    for rowsServ.Next() {
    var l models.InvoiceLine
    var tHeure time.Time
    
    l.Type = "SERVICE"
    l.Qty = 1
    
    err := rowsServ.Scan(&datePaiementStr, &clientNom, &clientPrenom, &clientEmail, &l.Description, &l.Qty, &l.UnitPrice, &tHeure)
    if err != nil {
        continue
    }

    l.Info1 = tHeure.Format("2006-01-02 15:04:05") 
    
    l.Description = "Service : " + l.Description
    l.Total = l.UnitPrice
    lines = append(lines, l)
}

    if len(lines) == 0 {
        http.Error(response, "Facture vide ou introuvable", http.StatusNotFound)
        return
    }

    pdf := gofpdf.New("P", "mm", "A4", "")
    tr := pdf.UnicodeTranslatorFromDescriptor("")
    pdf.SetMargins(15, 15, 15)
    pdf.AddPage()

    pdf.SetY(10)
    pdf.SetFont("Arial", "B", 24)
    pdf.SetTextColor(0, 0, 0)
    pdf.Cell(90, 10, "Facture   -   Silver Happy")

    pdf.Ln(30)

    yClient := pdf.GetY()
    
    pdf.SetFont("Arial", "B", 11)
    pdf.SetTextColor(0, 0, 0)
    pdf.Cell(0, 5, tr("Facturer à :"))
    pdf.Ln(7)
    pdf.SetFont("Arial", "", 10)
    pdf.SetTextColor(100, 100, 100)
    pdf.Cell(0, 5, tr(clientPrenom+" "+clientNom))
    pdf.Ln(5)
    pdf.Cell(0, 5, tr(clientEmail))

    pdf.SetXY(120, yClient) 
    pdf.SetFont("Arial", "B", 10)
    pdf.SetTextColor(0, 0, 0)
    pdf.CellFormat(80, 5, tr("Numéro : ") + "SILVER-HAPPY-" + paymentID, "", 1, "R", false, 0, "")
    pdf.Ln(2)
    pdf.SetX(120)
    dateAffichage, _ := time.Parse("2006-01-02", datePaiementStr[:10])
    pdf.SetFont("Arial", "", 10)
    pdf.SetTextColor(100, 100, 100)
    pdf.CellFormat(80, 5, tr("Émise le : ") + dateAffichage.Format("02/01/2006"), "", 1, "R", false, 0, "")

    pdf.Ln(20)

    pdf.SetFont("Arial", "B", 10)
    pdf.SetFillColor(245, 245, 245)
    pdf.SetTextColor(0, 0, 0)
    pdf.CellFormat(115, 10, "Description", "B", 0, "L", true, 0, "")
    pdf.CellFormat(20, 10, tr("Qté"), "B", 0, "C", true, 0, "")
    pdf.CellFormat(25, 10, "Prix unitaire", "B", 0, "R", true, 0, "")
    pdf.CellFormat(25, 10, "Montant", "B", 1, "R", true, 0, "")

    pdf.SetFont("Arial", "", 10)
    totalFacture := 0.0
    for _, l := range lines {

        pdf.SetTextColor(0, 0, 0)
        pdf.CellFormat(115, 10, tr(l.Description), "0", 0, "L", false, 0, "")
        pdf.CellFormat(20, 10, fmt.Sprintf("%d", l.Qty), "0", 0, "C", false, 0, "")
        pdf.CellFormat(25, 10, fmt.Sprintf("%.2f", l.UnitPrice) + tr("€"), "0", 0, "R", false, 0, "")
        pdf.CellFormat(25, 10, fmt.Sprintf("%.2f", l.Total) + tr("€"), "0", 1, "R", false, 0, "")
        totalFacture += l.Total

        pdf.SetDrawColor(245, 245, 245)
        pdf.Line(15, pdf.GetY(), 200, pdf.GetY()) 

        pdf.SetFont("Arial", "I", 8)
        pdf.SetTextColor(100, 100, 100)

        switch l.Type {
            case "ABONNEMENT":
                tDeb, _ := time.Parse("2006-01-02", datePaiementStr[:10])
                tFin := tDeb.AddDate(0, 1, 0)
                if l.Info1 != "mensuel" { tFin = tDeb.AddDate(1, 0, 0) }
                pdf.Cell(0, 5, tr("Période du ") + tDeb.Format("02/01/2006") + " au " + tFin.Format("02/01/2006"))
                pdf.Ln(7)
            case "INSCRIPTION":
                tD, _ := time.Parse("2006-01-02", l.Info1[:10])
                tF, _ := time.Parse("2006-01-02", l.Info2[:10])
                pdf.Cell(0, 5, tr("Évènement du ") + tD.Format("02/01/2006") + " au " + tF.Format("02/01/2006"))
                pdf.Ln(7)
            case "SERVICE":
                tServ, err := time.Parse("2006-01-02 15:04:05", l.Info1)
                if err != nil {
                    pdf.Cell(0, 5, tr("Date invalide"))
                } else {
                    pdf.Cell(0, 5, tr("Prestation prévue le ") + tServ.Format("02/01/2006") + tr(" à ") + tServ.Format("15h04"))
                }
                pdf.Ln(7)
        }
        pdf.SetTextColor(0, 0, 0)
        pdf.SetFont("Arial", "", 10)
    }

    pdf.Ln(10)
    pdf.SetFont("Arial", "B", 12)
    pdf.SetTextColor(0, 0, 0)
    pdf.SetX(150)
    pdf.SetDrawColor(0, 0, 0)
    pdf.CellFormat(25, 10, tr("Total dû"), "T", 0, "R", false, 0, "")
    pdf.CellFormat(25, 10, fmt.Sprintf("%.2f", totalFacture) + tr("€"), "T", 0, "R", false, 0, "")

    fileName := fmt.Sprintf("Facture_Silver_Happy_%s.pdf", paymentID)
    response.Header().Set("Content-Disposition", "attachment; filename="+fileName)
    response.Header().Set("Content-Type", "application/pdf")
    pdf.Output(response)
}