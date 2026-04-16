package providers

import (
	"database/sql"
	"time"

	"github.com/robfig/cron/v3"
)

func StartInvoiceCron(db *sql.DB) {
	c := cron.New()
	
	_, err := c.AddFunc("1 0 1 * *", func() {
		GenerateMonthlyInvoices(db)
	})

	if err != nil {
		panic(err) 
	}

	c.Start()
}

func GenerateMonthlyInvoices(db *sql.DB) {
	now := time.Now()
	
	firstDayOfCurrentMonth := time.Date(now.Year(), now.Month(), 1, 0, 0, 0, 0, time.UTC)
	lastDayOfLastMonth := firstDayOfCurrentMonth.Add(-1 * time.Second)
	firstDayOfLastMonth := time.Date(lastDayOfLastMonth.Year(), lastDayOfLastMonth.Month(), 1, 0, 0, 0, 0, time.UTC)

	monthYearStr := firstDayOfLastMonth.Format("2006-01")

	query := `
		SELECT pe.id_prestataire, SUM(p.prix) as total_revenue
		FROM PAIEMENT p
		JOIN INSCRIPTION i ON p.id_paiement = i.id_paiement
		JOIN PRESTATAIRE_EVENEMENT pe ON i.id_evenement = pe.id_evenement
		WHERE p.statut = 'valide'
		  AND p.date_paiement >= ? AND p.date_paiement <= ?
		GROUP BY pe.id_prestataire
	`

	rows, err := db.Query(query, firstDayOfLastMonth, lastDayOfLastMonth)
	if err != nil {
		return 
	}
	defer rows.Close()

	for rows.Next() {
		var providerID int
		var totalRevenue float64

		if err := rows.Scan(&providerID, &totalRevenue); err != nil {
			continue
		}

		platformFee := totalRevenue * 0.01
		netAmount := totalRevenue - platformFee

		insertQuery := `
			INSERT INTO FACTURE (montant, frais_plateforme, montant_net, mois_annee, statut, id_prestataire)
			VALUES (?, ?, ?, ?, 'en_attente', ?)
		`
		
		db.Exec(insertQuery, totalRevenue, platformFee, netAmount, monthYearStr, providerID)
	}
}