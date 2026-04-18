package services

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"html"
	"net/http"
	"net/url"
	"os"
	"strconv"
	"strings"

	"main/db"
	"main/models"
	"main/utils"

	"github.com/stripe/stripe-go/v78"
	"github.com/stripe/stripe-go/v78/charge"
	"github.com/stripe/stripe-go/v78/checkout/session"
	"github.com/stripe/stripe-go/v78/coupon"
	"github.com/stripe/stripe-go/v78/invoice"
	"github.com/stripe/stripe-go/v78/paymentintent"
)

func Add_Panier(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	id_produit := strings.TrimSpace(request.FormValue("id_produit"))
	id_user := strings.TrimSpace(request.FormValue("id_utilisateur"))
	quantityStr := strings.TrimSpace(request.FormValue("quantite"))
	action := strings.TrimSpace(request.FormValue("action"))

	quantity, _ := strconv.Atoi(quantityStr)

	var id_panier int
	var currentQty int
	err := db.DB.QueryRow("SELECT id_panier, quantite FROM PANIER WHERE id_utilisateur = ? AND id_produit = ?", id_user, id_produit).Scan(&id_panier, &currentQty)

	if err == nil {
		query := ""
		if action == "update" {
			query = "UPDATE PANIER SET quantite = ? WHERE id_panier = ? AND ? <= (SELECT stock FROM PRODUIT WHERE id_produit = ?)"
			result, err := db.DB.Exec(query, quantity, id_panier, quantity, id_produit)
			Error_Stock(result, err, response)
		} else {
			query = "UPDATE PANIER SET quantite = quantite + ? WHERE id_panier = ? AND (quantite + ?) <= (SELECT stock FROM PRODUIT WHERE id_produit = ?)"
			result, err := db.DB.Exec(query, quantity, id_panier, quantity, id_produit)
			Error_Stock(result, err, response)
		}
		return
	} else {
		_, err = db.DB.Exec("INSERT INTO PANIER (id_utilisateur, id_produit, quantite) VALUES (?, ?, ?)", id_user, id_produit, quantity)
		if err != nil {
			http.Error(response, "Impossible d'ajouter au panier", http.StatusBadRequest)
			return
		}
	}
	json.NewEncoder(response).Encode(map[string]string{"message": "Ajouté au panier !"})
}

func Error_Stock(result sql.Result, err error, response http.ResponseWriter) {
	if err != nil {
		http.Error(response, "Erreur serveur", http.StatusInternalServerError)
		return
	}
	rows, _ := result.RowsAffected()
	if rows == 0 {
		http.Error(response, "Stock insuffisant !", http.StatusConflict)
		return
	}
	json.NewEncoder(response).Encode(map[string]string{"message": "Quantité mise à jour !"})
}

func Get_Panier(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id_user := request.URL.Query().Get("id_utilisateur")

	rows, err := db.DB.Query("SELECT p.id_panier, p.id_produit, p.quantite, pr.nom, pr.prix, pr.image FROM PANIER p JOIN PRODUIT pr ON p.id_produit = pr.id_produit WHERE p.id_utilisateur = ?", id_user)

	if err != nil {
		http.Error(response, err.Error(), http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var items []models.Panier

	for rows.Next() {
		var i models.Panier
		rows.Scan(&i.IdPanier, &i.IdProduit, &i.Quantite, &i.Nom, &i.Prix, &i.Image)
		items = append(items, i)
	}

	json.NewEncoder(response).Encode(items)
}

func Delete_Panier(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id_panier := request.URL.Query().Get("id")
	if id_panier == "" {
		http.Error(response, "ID manquant", http.StatusBadRequest)
		return
	}

	_, err := db.DB.Exec("DELETE FROM PANIER WHERE id_panier = ?", id_panier)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]string{"message": "Article supprimé"})
}

func Check_Panier(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	code := request.URL.Query().Get("code")
	id_user := request.URL.Query().Get("id_utilisateur")

	var idCode int
	var valeur float64
	var typeReduc string

	err := db.DB.QueryRow("SELECT id_reduction, valeur, type FROM CODE_REDUCTION WHERE code = ? AND actif = 1", code).Scan(&idCode, &valeur, &typeReduc)
	if err != nil {
		http.Error(response, "Code invalide", http.StatusNotFound)
		return
	}

	var exists int
	db.DB.QueryRow("SELECT COUNT(*) FROM UTILISATION_PROMO WHERE id_utilisateur = ? AND id_reduction = ?", id_user, idCode).Scan(&exists)

	if exists > 0 {
		http.Error(response, "Vous avez déjà utilisé ce code promo", http.StatusForbidden)
		return
	}

	json.NewEncoder(response).Encode(map[string]interface{}{"valeur": valeur, "type": typeReduc})
}

func Paiement_Panier(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")

	var livraison models.Livraison

	if err := json.NewDecoder(request.Body).Decode(&livraison); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	livraison.Adresse = html.EscapeString(strings.TrimSpace(livraison.Adresse))
	livraison.Ville = html.EscapeString(strings.TrimSpace(livraison.Ville))
	livraison.CP = html.EscapeString(strings.TrimSpace(livraison.CP))
	livraison.Code = strings.ToUpper(strings.TrimSpace(livraison.Code))

	rows, errDB := db.DB.Query(`
		SELECT p.id_produit, p.quantite, pr.id_produit, pr.nom, pr.prix, pr.stock 
		FROM PANIER AS p JOIN PRODUIT pr ON p.id_produit = pr.id_produit
		WHERE p.id_utilisateur = ?`, livraison.UserID)

	if errDB != nil {
		if errDB == sql.ErrNoRows {
			http.Error(response, "Utilisateur introuvable", http.StatusNotFound)
		} else {
			http.Error(response, "Erreur serveur base de données", http.StatusInternalServerError)
		}
		return
	}
	defer rows.Close()

	var lineItems []*stripe.CheckoutSessionLineItemParams
	var total float64

	for rows.Next() {
		var idProduitP, quantite, idProduit, stock int
		var nom string
		var prix float64

		err := rows.Scan(&idProduitP, &quantite, &idProduit, &nom, &prix, &stock)

		if err != nil {
			fmt.Println("Erreur Scan:", err)
			continue
		}

		if quantite > stock {
			http.Error(response, "Il n'y a plus assez de stock pour le produit suivant : "+nom, http.StatusForbidden)
			return
		}

		total += (prix * float64(quantite))

		item := &stripe.CheckoutSessionLineItemParams{
			PriceData: &stripe.CheckoutSessionLineItemPriceDataParams{
				Currency: stripe.String("eur"),
				ProductData: &stripe.CheckoutSessionLineItemPriceDataProductDataParams{
					Name: stripe.String(nom),
				},
				UnitAmount: stripe.Int64(int64(prix * 100)),
			},
			Quantity: stripe.Int64(int64(quantite)),
		}

		lineItems = append(lineItems, item)
	}

	var discounts []*stripe.CheckoutSessionDiscountParams

	if livraison.Code != "" {
		var valeur float64
		var typeReduc string

		err := db.DB.QueryRow("SELECT valeur, type FROM CODE_REDUCTION WHERE code = ? AND actif = 1", livraison.Code).Scan(&valeur, &typeReduc)

		if err == nil {
			couponParams := &stripe.CouponParams{
				Duration: stripe.String("once"),
			}

			if typeReduc == "pourcentage" {
				couponParams.PercentOff = stripe.Float64(valeur)
				total = total * (1 - (valeur / 100))
			} else {
				couponParams.AmountOff = stripe.Int64(int64(valeur * 100))
				couponParams.Currency = stripe.String("eur")
				total = total - valeur
			}

			stCoupon, errCoupon := coupon.New(couponParams)

			if errCoupon == nil {
				discounts = append(discounts, &stripe.CheckoutSessionDiscountParams{
					Coupon: stripe.String(stCoupon.ID),
				})
			}
		}
	}

	fraisPort := 0.00
	var shippingOptions []*stripe.CheckoutSessionShippingOptionParams

	if total > 0 && total <= 100.00 {
		fraisPort = 4.99
		total += fraisPort

		shippingOptions = append(shippingOptions, &stripe.CheckoutSessionShippingOptionParams{
			ShippingRateData: &stripe.CheckoutSessionShippingOptionShippingRateDataParams{
				Type: stripe.String("fixed_amount"),
				FixedAmount: &stripe.CheckoutSessionShippingOptionShippingRateDataFixedAmountParams{
					Amount:   stripe.Int64(499),
					Currency: stripe.String("eur"),
				},
				DisplayName: stripe.String("Frais de livraison"),
			},
		})
	}

	if total < 0 {
		total = 0
	}

	params := &stripe.CheckoutSessionParams{
		PaymentMethodTypes: stripe.StringSlice([]string{"card"}),
		Mode:               stripe.String(string(stripe.CheckoutSessionModePayment)),
		ClientReferenceID:  stripe.String(strconv.Itoa(livraison.UserID)),
		LineItems:          lineItems,
		Discounts:          discounts,
		ShippingOptions:    shippingOptions,
		SuccessURL: stripe.String(fmt.Sprintf("%s/success-basket?session_id={CHECKOUT_SESSION_ID}&user_id=%d&total=%f&frais_port=%f&adresse=%s&ville=%s&cp=%s&code=%s",
			utils.GetAPIBaseURL(),
			livraison.UserID,
			total,
			fraisPort,
			url.QueryEscape(livraison.Adresse),
			url.QueryEscape(livraison.Ville),
			url.QueryEscape(livraison.CP),
			url.QueryEscape(livraison.Code),
		)),
		CancelURL: stripe.String(utils.GetFrontBaseURL() + "/front/services/basket.php")}

	s, err := session.New(params)
	if err != nil {
		fmt.Println("Erreur lors de la création de session Stripe :", err)
		http.Error(response, "Erreur Stripe", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]string{"url": s.URL})
}

func Success_Basket(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")

	sessionID := request.URL.Query().Get("session_id")
	userID := html.EscapeString(request.URL.Query().Get("user_id"))
	totalStr := request.URL.Query().Get("total")
	fraisPortStr := request.URL.Query().Get("frais_port")
	adresse := html.EscapeString(request.URL.Query().Get("adresse"))
	ville := html.EscapeString(request.URL.Query().Get("ville"))
	cp := html.EscapeString(request.URL.Query().Get("cp"))
	codePromoUtilise := strings.ToUpper(strings.TrimSpace(request.URL.Query().Get("code")))

	total, err := strconv.ParseFloat(totalStr, 64)
	fraisPort, _ := strconv.ParseFloat(fraisPortStr, 64)
	if err != nil {
		http.Error(response, "Erreur de format du total", http.StatusBadRequest)
		return
	}

	s, err := session.Get(sessionID, nil)
	if err != nil || s.PaymentStatus != stripe.CheckoutSessionPaymentStatusPaid {
		http.Redirect(response, request, utils.GetFrontBaseURL()+"/front/basket.php?error=paiement_echoue", http.StatusSeeOther)
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

	var idReduction sql.NullInt64
	if codePromoUtilise != "" {
		var idCode int
		err := db.DB.QueryRow("SELECT id_reduction FROM CODE_REDUCTION WHERE code = ?", codePromoUtilise).Scan(&idCode)

		if err == nil {
			idReduction.Int64 = int64(idCode)
			idReduction.Valid = true
		}
	}

	resPaiement, errP := db.DB.Exec(`
		INSERT INTO PAIEMENT (prix, statut, mode_paiement, url_facture) 
		VALUES (?, 'valide', 'carte', ?)`,
		total, urlFacture)

	if errP != nil {
		fmt.Println("Erreur création paiement :", errP)
		http.Error(response, "Erreur base de données (Paiement)", http.StatusInternalServerError)
		return
	}

	idPaiement, _ := resPaiement.LastInsertId()

	resCmd, errCmd := db.DB.Exec(`
		INSERT INTO COMMANDE (id_utilisateur, id_paiement, total, adresse, ville, code_postal, montant_frais_port)
		VALUES(?, ?, ?, ?, ?, ?, ?)`,
		userID, idPaiement, total, adresse, ville, cp, fraisPort)

	if errCmd != nil {
		fmt.Println("Erreur insertion commande :", errCmd)
		http.Error(response, "Erreur base de données (Commande)", http.StatusInternalServerError)
		return
	}

	idCommande, _ := resCmd.LastInsertId()

	_, errLines := db.DB.Exec(`
		INSERT INTO LIGNE_COMMANDE (id_commande, id_produit, quantite, prix_unitaire)
		SELECT ?, p.id_produit, p.quantite, pr.prix
		FROM PANIER p
		JOIN PRODUIT pr ON p.id_produit = pr.id_produit
		WHERE p.id_utilisateur = ?`,
		idCommande, userID)

	if errLines != nil {
		fmt.Println("Erreur lignes:", errLines)
	}

	if idReduction.Valid {
		db.DB.Exec("INSERT INTO UTILISATION_PROMO (id_utilisateur, id_reduction) VALUES (?, ?)", userID, idReduction.Int64)
	}

	db.DB.Exec(`
		UPDATE PRODUIT pr
		JOIN PANIER p ON pr.id_produit = p.id_produit
		SET pr.stock = pr.stock - p.quantite
		WHERE p.id_utilisateur = ?`, userID)

	db.DB.Exec("DELETE FROM PANIER WHERE id_utilisateur = ?", userID)

	http.Redirect(response, request, utils.GetFrontBaseURL()+"/front/services/products.php?success=paiement_valide", http.StatusSeeOther)
}
