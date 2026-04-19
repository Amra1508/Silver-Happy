package providers

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"net/http"
	"os"
	"time"

	"main/db"
	"main/utils"

	"github.com/robfig/cron/v3"
	"github.com/stripe/stripe-go/v78"
	"github.com/stripe/stripe-go/v78/transfer"
)

func StartInvoiceCron() {
	c := cron.New()

	_, err := c.AddFunc("1 0 1 * *", func() {
		fmt.Println("Début de la génération automatique des factures mensuelles...")
		GenerateMonthlyInvoices(false)
	})

	if err != nil {
		panic(err)
	}

	c.Start()
}

func TriggerInvoicesManual(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}
	
	GenerateMonthlyInvoices(true) 

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]string{"message": "Processus de facturation et de virements terminé."})
}

func GenerateMonthlyInvoices(forceCurrentMonth bool) {
	now := time.Now()
	
	var firstDay, lastDay time.Time
	var monthYearStr string

	if forceCurrentMonth {
		firstDay = time.Date(now.Year(), now.Month(), 1, 0, 0, 0, 0, time.UTC)
		lastDay = now
		monthYearStr = firstDay.Format("2006-01") + "-TEST"
	} else {
		firstDayOfCurrentMonth := time.Date(now.Year(), now.Month(), 1, 0, 0, 0, 0, time.UTC)
		lastDay = firstDayOfCurrentMonth.Add(-1 * time.Second)
		firstDay = time.Date(lastDay.Year(), lastDay.Month(), 1, 0, 0, 0, 0, time.UTC)
		monthYearStr = firstDay.Format("2006-01")
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
	if stripe.Key == "" {
		return
	}

	query := `
		SELECT provider_id, SUM(total) as total_revenue, stripe_account_id
		FROM (
			SELECT pe.id_prestataire as provider_id, SUM(p.prix) as total, pr.stripe_account_id
			FROM PAIEMENT p
			JOIN INSCRIPTION i ON p.id_paiement = i.id_paiement
			JOIN PRESTATAIRE_EVENEMENT pe ON i.id_evenement = pe.id_evenement
			JOIN PRESTATAIRE pr ON pe.id_prestataire = pr.id_prestataire
			WHERE p.statut = 'valide' AND p.date_paiement >= ? AND p.date_paiement <= ?
			GROUP BY pe.id_prestataire

			UNION ALL

			SELECT s.id_prestataire as provider_id, SUM(p.prix) as total, pr.stripe_account_id
			FROM PAIEMENT p
			JOIN RESERVATION_SERVICE rs ON p.id_paiement = rs.id_paiement
			JOIN SERVICE s ON rs.id_service = s.id_service
			JOIN PRESTATAIRE pr ON s.id_prestataire = pr.id_prestataire
			WHERE p.statut = 'valide' AND p.date_paiement >= ? AND p.date_paiement <= ?
			GROUP BY s.id_prestataire
		) as combined
		GROUP BY provider_id, stripe_account_id
	`

	rows, err := db.DB.Query(query, firstDay, lastDay, firstDay, lastDay)
	if err != nil {
		return
	}
	defer rows.Close()

	facturesCreees := 0

	for rows.Next() {
		var providerID int
		var totalRevenue float64
		var stripeAccountID sql.NullString

		if err := rows.Scan(&providerID, &totalRevenue, &stripeAccountID); err != nil {
			continue
		}

		if totalRevenue <= 0 {
			continue 
		}

		platformFee := totalRevenue * 0.01 
		netAmount := totalRevenue - platformFee

		statutFacture := "en_attente"
		var transferID string

		if stripeAccountID.Valid && stripeAccountID.String != "" {
			
			amountInCents := int64(netAmount * 100)

			params := &stripe.TransferParams{
				Amount:      stripe.Int64(amountInCents),
				Currency:    stripe.String("eur"),
				Destination: stripe.String(stripeAccountID.String),
				Description: stripe.String(fmt.Sprintf("Revenus SilverHappy - Période %s", monthYearStr)),
			}

			t, errTransfer := transfer.New(params)

			if errTransfer != nil {
				statutFacture = "annule"
			} else {
				statutFacture = "paye"
				transferID = t.ID
			}
		} else {
			statutFacture = "en_attente"
		}

		insertQuery := `
			INSERT INTO FACTURE (montant, frais_plateforme, montant_net, mois_annee, statut, id_prestataire, stripe_transfer_id)
			VALUES (?, ?, ?, ?, ?, ?, ?)
		`

		var stripeTransferIDVal interface{} = nil
		if transferID != "" {
			stripeTransferIDVal = transferID
		}

		_, errInsert := db.DB.Exec(insertQuery, totalRevenue, platformFee, netAmount, monthYearStr, statutFacture, providerID, stripeTransferIDVal)
		if errInsert != nil {
			fmt.Println("Impossible de sauvegarder la facture en BDD :", errInsert)
		} else {
			fmt.Println("Facture enregistrée en base de données avec succès.")
			facturesCreees++
		}
	}
}