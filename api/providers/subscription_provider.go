package providers

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"
	"net/url"
	"os"
	"strconv"

	"github.com/stripe/stripe-go/v78"
	"github.com/stripe/stripe-go/v78/charge"
	"github.com/stripe/stripe-go/v78/checkout/session"
	"github.com/stripe/stripe-go/v78/invoice"
	"github.com/stripe/stripe-go/v78/paymentintent"
	"github.com/stripe/stripe-go/v78/subscription"
)

func Paiement_Abonnement_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")

	req := models.Req{}
	if err := json.NewDecoder(request.Body).Decode(&req); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	var idAbo sql.NullInt64
	var debutAbo, typePaiement sql.NullString
	var estActif bool

	errDB := db.DB.QueryRow(`
		SELECT p.id_abonnement, 
		       p.debut_abonnement, 
		       a.type_paiement,
		       COALESCE((CASE 
		           WHEN a.type_paiement = 'mensuel' THEN DATE_ADD(p.debut_abonnement, INTERVAL 1 MONTH) > NOW()
		           WHEN a.type_paiement = 'annuel' THEN DATE_ADD(p.debut_abonnement, INTERVAL 1 YEAR) > NOW()
		           ELSE DATE_ADD(p.debut_abonnement, INTERVAL 1 YEAR) > NOW()
		       END), 0) as est_actif
		FROM PRESTATAIRE p 
		LEFT JOIN ABONNEMENT a ON p.id_abonnement = a.id_abonnement 
		WHERE p.id_prestataire = ?`, req.UserID).Scan(&idAbo, &debutAbo, &typePaiement, &estActif)

	if errDB != nil {
		if errDB == sql.ErrNoRows {
			http.Error(response, "Prestataire introuvable", http.StatusNotFound)
		} else {
			http.Error(response, "Erreur serveur base de données", http.StatusInternalServerError)
		}
		return
	}

	aDejaEteAbonne := debutAbo.Valid && debutAbo.String != ""

	if estActif {
		http.Error(response, "Vous possédez déjà un abonnement actif.", http.StatusForbidden)
		return
	}

	if req.TypeAbonnement == "Renouvellement" && !aDejaEteAbonne {
		http.Error(response, "Le tarif de renouvellement est réservé aux prestataires ayant déjà été abonnés.", http.StatusForbidden)
		return
	}

	interval := "month"
	if req.Periode == "annuel" {
		interval = "year"
	}

	encodedType := url.QueryEscape(req.TypeAbonnement)

	var prixRenouvellement int64
	var fraisInitiaux int64

	if req.Periode == "annuel" {
		interval = "year"
		prixRenouvellement = 35
		fraisInitiaux = 5
	} else {
		interval = "month"
		prixRenouvellement = 3
		fraisInitiaux = 1
	}

	params := &stripe.CheckoutSessionParams{
		PaymentMethodTypes: stripe.StringSlice([]string{"card"}),
		Mode:               stripe.String(string(stripe.CheckoutSessionModeSubscription)),
		ClientReferenceID:  stripe.String(strconv.Itoa(req.UserID)),

		LineItems: []*stripe.CheckoutSessionLineItemParams{
			{
				PriceData: &stripe.CheckoutSessionLineItemPriceDataParams{
					Currency: stripe.String("eur"),
					ProductData: &stripe.CheckoutSessionLineItemPriceDataProductDataParams{
						Name: stripe.String("Abonnement Pro Silver Happy (" + req.Periode + ")"),
					},
					UnitAmount: stripe.Int64(prixRenouvellement * 100),
					Recurring: &stripe.CheckoutSessionLineItemPriceDataRecurringParams{
						Interval: stripe.String(interval),
					},
				},
				Quantity: stripe.Int64(1),
			},
			{
				PriceData: &stripe.CheckoutSessionLineItemPriceDataParams{
					Currency: stripe.String("eur"),
					ProductData: &stripe.CheckoutSessionLineItemPriceDataProductDataParams{
						Name: stripe.String("Frais de première souscription"),
					},
					UnitAmount: stripe.Int64(fraisInitiaux * 100),
				},
				Quantity: stripe.Int64(1),
			},
		},
		SuccessURL: stripe.String(fmt.Sprintf("%s/prestataire/success-subscription?session_id={CHECKOUT_SESSION_ID}&provider_id=%d&tarif=%d&periode=%s&type=%s", utils.GetAPIBaseURL(), req.UserID, req.Tarif, req.Periode, encodedType)),
		CancelURL:  stripe.String(utils.GetFrontBaseURL() + "/providers/account/profile.php"),
	}

	s, err := session.New(params)
	if err != nil {
		fmt.Println("Erreur lors de la création de session Stripe :", err)
		http.Error(response, "Erreur Stripe", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]string{"url": s.URL})
}

func Success_Subscription_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")

	sessionID := request.URL.Query().Get("session_id")
	providerID := request.URL.Query().Get("provider_id")
	tarifStr := request.URL.Query().Get("tarif")
	periode := request.URL.Query().Get("periode")
	typeAbo := request.URL.Query().Get("type")

	tarif, err := strconv.ParseFloat(tarifStr, 64)
	if err != nil {
		http.Error(response, "Erreur de format de tarif", http.StatusBadRequest)
		return
	}

	s, err := session.Get(sessionID, nil)
	if err != nil || s.PaymentStatus != stripe.CheckoutSessionPaymentStatusPaid {
		http.Redirect(response, request, utils.GetFrontBaseURL()+"/providers/account/profile.php?error=paiement_echoue", http.StatusSeeOther)
		return
	}

	var urlFacture string

	if s.PaymentIntent != nil {
		pi, errPI := paymentintent.Get(s.PaymentIntent.ID, nil)
		if errPI == nil && pi.LatestCharge != nil {
			ch, errC := charge.Get(pi.LatestCharge.ID, nil)
			if errC == nil {
				urlFacture = ch.ReceiptURL
			}
		}
	} else if s.Invoice != nil {
		inv, errI := invoice.Get(s.Invoice.ID, nil)
		if errI == nil {
			urlFacture = inv.HostedInvoiceURL
		}
	}

	renouvellement := 0
	if typeAbo == "Renouvellement" {
		renouvellement = 1
	}

	resPaiement, errP := db.DB.Exec(`
		INSERT INTO PAIEMENT (prix, statut, mode_paiement, url_facture) 
		VALUES (?, 'valide', 'carte', ?)`,
		tarif, urlFacture)

	if errP != nil {
		fmt.Println("Erreur création paiement :", errP)
		http.Error(response, "Erreur base de données (Paiement)", http.StatusInternalServerError)
		return
	}

	idPaiement, _ := resPaiement.LastInsertId()

	switch periode {
	case "annuel":
		tarif += 5
	case "mensuel":
		tarif += 1
	}

	resAbo, errA := db.DB.Exec(`
		INSERT INTO ABONNEMENT (description, renouvellement, type_abonnement, type_paiement, methode_paiement, tarif, id_paiement)
		VALUES ('Abonnement Pro Silver Happy', ?, 'prestataire', ?, 'carte', ?, ?)`,
		renouvellement, periode, tarif, idPaiement)

	if errA != nil {
		fmt.Println("Erreur création abonnement :", errA)
		http.Error(response, "Erreur base de données (Abonnement)", http.StatusInternalServerError)
		return
	}

	idAbonnement, _ := resAbo.LastInsertId()

	_, errU := db.DB.Exec(`
		UPDATE PRESTATAIRE 
		SET id_abonnement = ?, debut_abonnement = NOW() 
		WHERE id_prestataire = ?`,
		idAbonnement, providerID)

	if errU != nil {
		fmt.Println("Erreur mise à jour prestataire :", errU)
		http.Error(response, "Erreur mise à jour prestataire", http.StatusInternalServerError)
		return
	}

	var nomP, prenomP string
	errInfo := db.DB.QueryRow("SELECT nom, prenom FROM PRESTATAIRE WHERE id_prestataire = ?", providerID).Scan(&nomP, &prenomP)

	if errInfo == nil {
		formule := "Abonnement Pro Silver Happy (" + periode + ")"
		prixStr := fmt.Sprintf("%.2f", tarif)

		providerIDInt, _ := strconv.ParseInt(providerID, 10, 64)
		cheminContrat, errPdf := utils.GenerateSubscriptionContract(providerIDInt, nomP, prenomP, "Prestataire", formule, prixStr)

		if errPdf == nil {
			db.DB.Exec("UPDATE ABONNEMENT SET url_contrat = ? WHERE id_abonnement = ?", cheminContrat, idAbonnement)
		} else {
			fmt.Println("Erreur génération contrat prestataire :", errPdf)
		}
	}

	http.Redirect(response, request, utils.GetFrontBaseURL()+"/providers/account/profile.php?success=abonnement_valide", http.StatusSeeOther)
}

func Cancel_Subscription_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var payload map[string]int
	if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	idProvider, exists := payload["id_prestataire"]
	if !exists {
		http.Error(response, "ID Prestataire manquant", http.StatusBadRequest)
		return
	}

	var stripeSub sql.NullString
	err := db.DB.QueryRow(`
		SELECT a.stripe_sub 
		FROM PRESTATAIRE p 
		JOIN ABONNEMENT a ON p.id_abonnement = a.id_abonnement 
		WHERE p.id_prestataire = ?
	`, idProvider).Scan(&stripeSub)

	if err != nil {
		http.Error(response, "Aucun abonnement actif trouvé.", http.StatusNotFound)
		return
	}

	if stripeSub.Valid && stripeSub.String != "" {
		stripe.Key = os.Getenv("STRIPE_SECRET_KEY")

		params := &stripe.SubscriptionParams{
			CancelAtPeriodEnd: stripe.Bool(true),
		}

		_, errStripe := subscription.Update(stripeSub.String, params)
		if errStripe != nil {
			fmt.Println("Erreur lors de l'annulation Stripe:", errStripe)
		}
	}

	_, errUpdate := db.DB.Exec("UPDATE ABONNEMENT SET renouvellement = 0 WHERE stripe_sub = ?", stripeSub.String)

	if errUpdate != nil {
		http.Error(response, "Erreur lors de la mise à jour BDD.", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Renouvellement automatique désactivé."})
}

func Paiement_Boost(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")

	var req models.BoostRequest
	if err := json.NewDecoder(request.Body).Decode(&req); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	var nomProduit string
	var prixBoost int64

	if req.TypeBoost == "evenement" {
		nomProduit = "Boost de l'événement pour 7 jours"
		prixBoost = 500
	} else {
		nomProduit = "Boost du profil Prestataire pour 7 jours"
		prixBoost = 1000
	}

	params := &stripe.CheckoutSessionParams{
		PaymentMethodTypes: stripe.StringSlice([]string{"card"}),
		Mode:               stripe.String(string(stripe.CheckoutSessionModePayment)),
		LineItems: []*stripe.CheckoutSessionLineItemParams{
			{
				PriceData: &stripe.CheckoutSessionLineItemPriceDataParams{
					Currency: stripe.String("eur"),
					ProductData: &stripe.CheckoutSessionLineItemPriceDataProductDataParams{
						Name: stripe.String(nomProduit),
					},
					UnitAmount: stripe.Int64(prixBoost),
				},
				Quantity: stripe.Int64(1),
			},
		},
		SuccessURL: stripe.String(fmt.Sprintf("%s/prestataire/success-boost?session_id={CHECKOUT_SESSION_ID}&provider_id=%d&type=%s&target_id=%d", utils.GetAPIBaseURL(), req.ProviderID, req.TypeBoost, req.TargetID)),
		CancelURL:  stripe.String(utils.GetFrontBaseURL() + "/providers/index.php"),
	}

	s, err := session.New(params)
	if err != nil {
		http.Error(response, "Erreur Stripe", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]string{"url": s.URL})
}

func Success_Boost(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	sessionID := request.URL.Query().Get("session_id")
	providerID := request.URL.Query().Get("provider_id")
	typeBoost := request.URL.Query().Get("type")
	targetID := request.URL.Query().Get("target_id")

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
	s, err := session.Get(sessionID, nil)

	if err != nil || s.PaymentStatus != stripe.CheckoutSessionPaymentStatusPaid {
		http.Redirect(response, request, utils.GetFrontBaseURL()+"/providers/index.php?error=paiement_echoue", http.StatusSeeOther)
		return
	}

	var urlFacture string
	if s.PaymentIntent != nil {
		pi, errPI := paymentintent.Get(s.PaymentIntent.ID, nil)
		if errPI == nil && pi.LatestCharge != nil {
			ch, errC := charge.Get(pi.LatestCharge.ID, nil)
			if errC == nil {
				urlFacture = ch.ReceiptURL
			}
		}
	}

	prix := float64(s.AmountTotal) / 100.0

	_, errP := db.DB.Exec(`
		INSERT INTO PAIEMENT (prix, statut, mode_paiement, url_facture) 
		VALUES (?, 'valide', 'carte', ?)`,
		prix, urlFacture)

	if errP != nil {
		fmt.Println("Erreur création paiement boost :", errP)
		http.Error(response, "Erreur base de données (Paiement)", http.StatusInternalServerError)
		return
	}

	if typeBoost == "evenement" {
		_, errDB := db.DB.Exec(`UPDATE EVENEMENT SET date_fin_boost = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE id_evenement = ?`, targetID)

		if errDB != nil {
			fmt.Println("Erreur MAJ événement boost:", errDB)
			http.Error(response, "Erreur BDD", http.StatusInternalServerError)
			return
		}
		http.Redirect(response, request, utils.GetFrontBaseURL()+"/providers/services/events.php?success=boost_valide", http.StatusSeeOther)
	} else {
		_, errDB := db.DB.Exec(`UPDATE PRESTATAIRE SET date_fin_boost = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE id_prestataire = ?`, providerID)

		if errDB != nil {
			fmt.Println("Erreur MAJ prestataire boost:", errDB)
			http.Error(response, "Erreur BDD", http.StatusInternalServerError)
			return
		}
		http.Redirect(response, request, utils.GetFrontBaseURL()+"/providers/account/profile.php?success=boost_valide", http.StatusSeeOther)
	}
}
