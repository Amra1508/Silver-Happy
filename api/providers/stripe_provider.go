package providers

import (
	"database/sql"
	"encoding/json"
	"main/db"
	"main/utils"
	"net/http"
	"os"

	"github.com/stripe/stripe-go/v78"
	"github.com/stripe/stripe-go/v78/account"
	"github.com/stripe/stripe-go/v78/accountlink"
)

func CreateStripeAccountLink(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var req struct {
		ProviderID int `json:"provider_id"`
	}

	if err := json.NewDecoder(request.Body).Decode(&req); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
	if stripe.Key == "" {
		http.Error(response, "Erreur Serveur: Clé STRIPE_SECRET_KEY introuvable dans ton fichier .env !", http.StatusInternalServerError)
		return
	}

	var stripeAccountID sql.NullString
	err := db.DB.QueryRow("SELECT stripe_account_id FROM PRESTATAIRE WHERE id_prestataire = ?", req.ProviderID).Scan(&stripeAccountID)
	if err != nil && err != sql.ErrNoRows {
		http.Error(response, "Erreur BDD: "+err.Error(), http.StatusInternalServerError)
		return
	}

	var acctID string

	if !stripeAccountID.Valid || stripeAccountID.String == "" {
		acctParams := &stripe.AccountParams{
			Type: stripe.String(string(stripe.AccountTypeExpress)),
			Capabilities: &stripe.AccountCapabilitiesParams{
				Transfers: &stripe.AccountCapabilitiesTransfersParams{
					Requested: stripe.Bool(true),
				},
			},
		}

		acct, errAcct := account.New(acctParams)
		if errAcct != nil {
			http.Error(response, "Erreur API Stripe (Création) : "+errAcct.Error(), http.StatusInternalServerError)
			return
		}

		acctID = acct.ID
		db.DB.Exec("UPDATE PRESTATAIRE SET stripe_account_id = ? WHERE id_prestataire = ?", acctID, req.ProviderID)
	} else {
		acctID = stripeAccountID.String
	}

	frontBase := utils.GetFrontBaseURL()
	if frontBase == "" {
		frontBase = "http://localhost"
	}

	linkParams := &stripe.AccountLinkParams{
		Account:    stripe.String(acctID),
		RefreshURL: stripe.String(frontBase + "/providers/account/profile.php?stripe=error"),
		ReturnURL:  stripe.String(frontBase + "/providers/account/profile.php?stripe=success"),
		Type:       stripe.String("account_onboarding"),
	}

	link, errLink := accountlink.New(linkParams)
	if errLink != nil {
		http.Error(response, "Erreur API Stripe (Lien) : "+errLink.Error(), http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]string{"url": link.URL})
}