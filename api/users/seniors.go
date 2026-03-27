package users

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"net/http"
	"net/url"
	"os"
	"regexp"
	"strconv"
	"strings"
	"time"

	"main/db"
	"main/models"
	"main/utils"

	"github.com/stripe/stripe-go/v78"
	"github.com/stripe/stripe-go/v78/charge"
	"github.com/stripe/stripe-go/v78/checkout/session"
	"github.com/stripe/stripe-go/v78/invoice"
	"github.com/stripe/stripe-go/v78/paymentintent"
	"golang.org/x/crypto/bcrypt"
)

func isContactInfoTaken(email, numTelephone string) (bool, string) {
	var emailCount int
	var phoneCount int

	db.DB.QueryRow(`
        SELECT 
            (SELECT COUNT(*) FROM UTILISATEUR WHERE email = ?) + 
            (SELECT COUNT(*) FROM PRESTATAIRE WHERE email = ?)`,
		email, email).Scan(&emailCount)

	if emailCount > 0 {
		return true, "Cet email est déjà utilisé par un utilisateur ou un prestataire."
	}

	db.DB.QueryRow(`
        SELECT 
            (SELECT COUNT(*) FROM UTILISATEUR WHERE num_telephone = ?) + 
            (SELECT COUNT(*) FROM PRESTATAIRE WHERE num_telephone = ?)`,
		numTelephone, numTelephone).Scan(&phoneCount)

	if phoneCount > 0 {
		return true, "Ce numéro de téléphone est déjà utilisé."
	}

	return false, ""
}

func Read_User(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	queryParams := request.URL.Query()
	limitStr := queryParams.Get("limit")
	pageStr := queryParams.Get("page")

	limit := 10
	offset := 0
	page := 1

	if limitStr != "" {
		fmt.Sscanf(limitStr, "%d", &limit)
	}
	if pageStr != "" {
		fmt.Sscanf(pageStr, "%d", &page)
		offset = (page - 1) * limit
	}

	var total int
	db.DB.QueryRow("SELECT COUNT(*) FROM UTILISATEUR WHERE statut = 'user'").Scan(&total)

	query := `
        SELECT u.id_utilisateur, u.nom, u.prenom, u.email, 
               u.num_telephone, u.date_naissance, u.statut, 
               u.date_creation, u.motif_bannissement, u.duree_bannissement,
               a.rue, a.ville, a.code_postal, a.pays,
               COALESCE(SUM(CASE WHEN m.est_lu = 0 AND m.id_utilisateur1 = u.id_utilisateur THEN 1 ELSE 0 END), 0) AS est_lu
        FROM UTILISATEUR u
        LEFT JOIN ADRESSE a ON u.id_adresse = a.id_adresse
        LEFT JOIN MESSAGE_ADMIN m ON u.id_utilisateur = m.id_utilisateur1
        WHERE u.statut = 'user' OR u.statut = 'banni'
        GROUP BY u.id_utilisateur, u.nom, u.prenom, u.email, 
                 u.num_telephone, u.date_naissance, u.statut, 
                 u.date_creation, u.motif_bannissement, u.duree_bannissement,
                 a.rue, a.ville, a.code_postal, a.pays
        ORDER BY est_lu DESC, u.id_utilisateur ASC
        LIMIT ? OFFSET ?
    `
	rows, err := db.DB.Query(query, limit, offset)
	if err != nil {
		http.Error(response, "Erreur serveur", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabUtilisateur []models.Utilisateur
	for rows.Next() {
		var user models.Utilisateur
		var numTel, dateNaiss, dateCrea, motifBan, rue, ville, cp, pays sql.NullString
		var dureeBan sql.NullInt64
		var estLu int

		err := rows.Scan(
			&user.ID, &user.Nom, &user.Prenom, &user.Email,
			&numTel, &dateNaiss, &user.Statut, &dateCrea, &motifBan, &dureeBan,
			&rue, &ville, &cp, &pays, &estLu,
		)

		if err == nil {
			user.NumTelephone = numTel.String
			user.DateNaissance = dateNaiss.String
			user.DateCreation = dateCrea.String
			user.MotifBannissement = motifBan.String
			user.DureeBannissement = int(dureeBan.Int64)
			user.Adresse = rue.String
			user.Ville = ville.String
			user.CodePostal = cp.String
			user.Pays = pays.String
			user.EstLu = estLu

			tabUtilisateur = append(tabUtilisateur, user)
		}
	}

	if tabUtilisateur == nil {
		tabUtilisateur = []models.Utilisateur{}
	}

	dataResponse := map[string]interface{}{
		"data":        tabUtilisateur,
		"total":       total,
		"currentPage": page,
		"totalPages":  (total + limit - 1) / limit,
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(dataResponse)
}

func Read_Admin(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	queryParams := request.URL.Query()
	limitStr := queryParams.Get("limit")
	pageStr := queryParams.Get("page")
	userIdStr := queryParams.Get("user_id") 

	limit := 10
	offset := 0
	page := 1
	var userId int

	if limitStr != "" {
		fmt.Sscanf(limitStr, "%d", &limit)
	}
	if pageStr != "" {
		fmt.Sscanf(pageStr, "%d", &page)
		offset = (page - 1) * limit
	}
	if userIdStr != "" {
		fmt.Sscanf(userIdStr, "%d", &userId)
	}

	var total int
	db.DB.QueryRow("SELECT COUNT(*) FROM UTILISATEUR WHERE statut = 'admin'").Scan(&total)

	query := `
		SELECT u.id_utilisateur, u.nom, u.prenom, u.email, 
			   u.num_telephone,
			   COALESCE(SUM(CASE WHEN m.est_lu = 0 AND m.id_utilisateur2 = ? AND m.id_utilisateur1 = u.id_utilisateur THEN 1 ELSE 0 END), 0) AS est_lu
		FROM UTILISATEUR u
		LEFT JOIN MESSAGE_ADMIN m ON u.id_utilisateur = m.id_utilisateur1
		WHERE u.statut = 'admin'
		GROUP BY u.id_utilisateur, u.nom, u.prenom, u.email, u.num_telephone
		ORDER BY est_lu DESC, u.id_utilisateur ASC
		LIMIT ? OFFSET ?
	`
	rows, err := db.DB.Query(query, userId, limit, offset)
	if err != nil {
		http.Error(response, "Erreur serveur", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabUtilisateur []models.Utilisateur
	for rows.Next() {
		var user models.Utilisateur
		var numTel sql.NullString
		var estLu int 

		err := rows.Scan(
			&user.ID, &user.Nom, &user.Prenom, &user.Email,
			&numTel, &estLu,
		)

		if err == nil {
			user.NumTelephone = numTel.String
			user.EstLu = estLu

			tabUtilisateur = append(tabUtilisateur, user)
		}
	}

	if tabUtilisateur == nil {
		tabUtilisateur = []models.Utilisateur{}
	}

	dataResponse := map[string]interface{}{
		"data":        tabUtilisateur,
		"total":       total,
		"currentPage": page,
		"totalPages":  (total + limit - 1) / limit,
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(dataResponse)
}

func Create_User(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var user models.Utilisateur
	if err := json.NewDecoder(request.Body).Decode(&user); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	user.Nom = strings.TrimSpace(user.Nom)
	user.Prenom = strings.TrimSpace(user.Prenom)
	user.Email = strings.TrimSpace(user.Email)
	user.NumTelephone = strings.TrimSpace(user.NumTelephone)
	user.CodePostal = strings.TrimSpace(user.CodePostal)
	user.Adresse = strings.TrimSpace(user.Adresse)
	user.Ville = strings.TrimSpace(user.Ville)
	user.Pays = strings.TrimSpace(user.Pays)

	if user.Nom == "" || user.Prenom == "" || user.Email == "" {
		http.Error(response, "Le nom, prénom et email sont obligatoires et ne peuvent pas être vides", http.StatusBadRequest)
		return
	}

	emailRegex := regexp.MustCompile(`^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$`)
    if !emailRegex.MatchString(user.Email) {
        http.Error(response, "Le format de l'adresse e-mail est invalide.", http.StatusBadRequest)
        return
    }

	if user.DateNaissance != "" {
		dateParsed, err := time.Parse("2006-01-02", user.DateNaissance)
		if err != nil {
			http.Error(response, "Format de date de naissance invalide (attendu: AAAA-MM-JJ)", http.StatusBadRequest)
			return
		}
		if dateParsed.After(time.Now()) {
			http.Error(response, "La date de naissance ne peut pas être dans le futur", http.StatusBadRequest)
			return
		}
	}

	taken, msg := isContactInfoTaken(user.Email, user.NumTelephone)
	if taken {
		http.Error(response, msg, http.StatusConflict)
		return
	}

	var dateNaissance interface{} = user.DateNaissance
	if user.DateNaissance == "" {
		dateNaissance = nil
	}

	hashMdp, err := bcrypt.GenerateFromPassword([]byte("1234"), bcrypt.DefaultCost)
	if err != nil {
		http.Error(response, "Erreur interne", http.StatusInternalServerError)
		return
	}

	resPlan, _ := db.DB.Exec("INSERT INTO PLANNING (nom, description, date_creation) VALUES (?, 'Planning généré automatiquement', NOW())", "Planning de "+user.Prenom)
	idPlanning, _ := resPlan.LastInsertId()

	resAdr, _ := db.DB.Exec("INSERT INTO ADRESSE (numero, rue, ville, code_postal, pays) VALUES (NULL, ?, ?, ?, ?)", user.Adresse, user.Ville, user.CodePostal, user.Pays)
	idAdresse, _ := resAdr.LastInsertId()

	res, err := db.DB.Exec(`
        INSERT INTO UTILISATEUR (nom, prenom, email, num_telephone, date_naissance, statut, date_creation, motif_bannissement, duree_bannissement, mdp, id_planning, id_adresse) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)`,
		user.Nom, user.Prenom, user.Email, user.NumTelephone, dateNaissance, user.Statut, user.MotifBannissement, user.DureeBannissement, string(hashMdp), idPlanning, idAdresse)

	if err != nil {
		http.Error(response, "Erreur création utilisateur", http.StatusInternalServerError)
		return
	}

	user.ID, _ = res.LastInsertId()
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(user)
}

func Update_User(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}
	id := request.PathValue("id")
	
	var user models.Utilisateur
	if err := json.NewDecoder(request.Body).Decode(&user); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	user.Nom = strings.TrimSpace(user.Nom)
	user.Prenom = strings.TrimSpace(user.Prenom)
	user.Email = strings.TrimSpace(user.Email)
	user.NumTelephone = strings.TrimSpace(user.NumTelephone)
	user.CodePostal = strings.TrimSpace(user.CodePostal)
	user.Adresse = strings.TrimSpace(user.Adresse)
	user.Ville = strings.TrimSpace(user.Ville)
	user.Pays = strings.TrimSpace(user.Pays)

	if user.Nom == "" || user.Prenom == "" || user.Email == "" {
		http.Error(response, "Le nom, prénom et email ne peuvent pas être vides", http.StatusBadRequest)
		return
	}

	emailRegex := regexp.MustCompile(`^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$`)
    if !emailRegex.MatchString(user.Email) {
        http.Error(response, "Le format de l'adresse e-mail est invalide.", http.StatusBadRequest)
        return
    }

	var dateNaissance interface{} = user.DateNaissance
	if user.DateNaissance == "" {
		dateNaissance = nil
	}

	db.DB.Exec(`
        UPDATE UTILISATEUR SET nom = ?, prenom = ?, email = ?, num_telephone = ?, date_naissance = ?, statut = ?, motif_bannissement = ?, duree_bannissement = ? 
        WHERE id_utilisateur = ?`,
		user.Nom, user.Prenom, user.Email, user.NumTelephone, dateNaissance, user.Statut, user.MotifBannissement, user.DureeBannissement, id)

	db.DB.Exec("UPDATE ADRESSE SET rue = ?, ville = ?, code_postal = ?, pays = ? WHERE id_adresse = (SELECT id_adresse FROM UTILISATEUR WHERE id_utilisateur = ?)",
		user.Adresse, user.Ville, user.CodePostal, user.Pays, id)

	response.WriteHeader(http.StatusOK)
}

func Delete_User(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}
	id := request.PathValue("id")

	var idPlanning, idAdresse int

	db.DB.QueryRow("SELECT id_planning, id_adresse FROM UTILISATEUR WHERE id_utilisateur = ?", id).Scan(&idPlanning, &idAdresse)

	_, errMsg := db.DB.Exec("DELETE FROM MESSAGE_ADMIN WHERE id_utilisateur1 = ? OR id_utilisateur2 = ?", id, id)
	if errMsg != nil {
		http.Error(response, "Erreur lors de la suppression des messages de l'utilisateur", http.StatusInternalServerError)
		return
	}

	_, err := db.DB.Exec("DELETE FROM UTILISATEUR WHERE id_utilisateur = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression de l'utilisateur", http.StatusInternalServerError)
		return
	}

	db.DB.Exec("DELETE FROM PLANNING WHERE id_planning = ?", idPlanning)
	db.DB.Exec("DELETE FROM ADRESSE WHERE id_adresse = ?", idAdresse)

	response.WriteHeader(http.StatusNoContent)
}

func Ban_User(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")

	var user models.Utilisateur
	if err := json.NewDecoder(request.Body).Decode(&user); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	_, err := db.DB.Exec("UPDATE UTILISATEUR SET statut = ?, motif_bannissement = ?, duree_bannissement = ? WHERE id_utilisateur = ?",
		user.Statut, user.MotifBannissement, user.DureeBannissement, id)

	if err != nil {
		http.Error(response, "Erreur lors de la mise à jour du statut", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Statut mis à jour avec succès"})
}

func Paiement_Abonnement(response http.ResponseWriter, request *http.Request) {
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
		SELECT u.id_abonnement, 
		       u.debut_abonnement, 
		       a.type_paiement,
		       COALESCE((CASE 
		           WHEN a.type_paiement = 'mensuel' THEN DATE_ADD(u.debut_abonnement, INTERVAL 1 MONTH) > NOW()
		           WHEN a.type_paiement = 'annuel' THEN DATE_ADD(u.debut_abonnement, INTERVAL 1 YEAR) > NOW()
		           ELSE DATE_ADD(u.debut_abonnement, INTERVAL 1 YEAR) > NOW()
		       END), 0) as est_actif
		FROM UTILISATEUR u 
		LEFT JOIN ABONNEMENT a ON u.id_abonnement = a.id_abonnement 
		WHERE u.id_utilisateur = ?`, req.UserID).Scan(&idAbo, &debutAbo, &typePaiement, &estActif)

	if errDB != nil {
		if errDB == sql.ErrNoRows {
			http.Error(response, "Utilisateur introuvable", http.StatusNotFound)
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
		http.Error(response, "Le tarif de renouvellement est réservé aux utilisateurs ayant déjà été abonnés.", http.StatusForbidden)
		return
	}

	interval := "month"
	if req.Periode == "annuel" {
		interval = "year"
	}
    
	encodedType := url.QueryEscape(req.TypeAbonnement)

    params := &stripe.CheckoutSessionParams{
        PaymentMethodTypes: stripe.StringSlice([]string{"card"}),
        Mode:               stripe.String(string(stripe.CheckoutSessionModeSubscription)),
        ClientReferenceID:  stripe.String(strconv.Itoa(req.UserID)),
        LineItems: []*stripe.CheckoutSessionLineItemParams{
            {
                PriceData: &stripe.CheckoutSessionLineItemPriceDataParams{
                    Currency: stripe.String("eur"),
                    ProductData: &stripe.CheckoutSessionLineItemPriceDataProductDataParams{
                        Name: stripe.String("Silver Happy - " + req.TypeAbonnement),
                    },
                    UnitAmount: stripe.Int64(req.Tarif * 100),
                    Recurring: &stripe.CheckoutSessionLineItemPriceDataRecurringParams{
                        Interval: stripe.String(interval),
                    },
                },
                Quantity: stripe.Int64(1),
            },
        },
        SuccessURL: stripe.String(fmt.Sprintf("http://localhost:8082/success-subscription?session_id={CHECKOUT_SESSION_ID}&user_id=%d&tarif=%d&periode=%s&type=%s", req.UserID, req.Tarif, req.Periode, encodedType)),
    	CancelURL:  stripe.String("http://localhost/front/services/subscription.php"),
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

func Success_Subscription(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")

	sessionID := request.URL.Query().Get("session_id")
	userID := request.URL.Query().Get("user_id")
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
		http.Redirect(response, request, "http://localhost/front/abonnement.php?error=paiement_echoue", http.StatusSeeOther)
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

	resAbo, errA := db.DB.Exec(`
		INSERT INTO ABONNEMENT (description, renouvellement, type_abonnement, type_paiement, methode_paiement, tarif, id_paiement)
		VALUES ('Abonnement Silver Happy', ?, 'seniors', ?, 'carte', ?, ?)`,
		renouvellement, periode, tarif, idPaiement)

	if errA != nil {
		fmt.Println("Erreur création abonnement :", errA)
		http.Error(response, "Erreur base de données (Abonnement)", http.StatusInternalServerError)
		return
	}

	idAbonnement, _ := resAbo.LastInsertId()

	_, errU := db.DB.Exec(`
		UPDATE UTILISATEUR 
		SET id_abonnement = ?, debut_abonnement = NOW() 
		WHERE id_utilisateur = ?`,
		idAbonnement, userID)

	if errU != nil {
		fmt.Println("Erreur mise à jour utilisateur :", errU)
		http.Error(response, "Erreur mise à jour utilisateur", http.StatusInternalServerError)
		return
	}

	http.Redirect(response, request, "http://localhost/front/account/profile.php?success=abonnement_valide", http.StatusSeeOther)
}