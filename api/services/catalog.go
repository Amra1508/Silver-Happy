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
	"github.com/stripe/stripe-go/v78/paymentintent"
	"github.com/stripe/stripe-go/v78/refund"
)

func CleanDateForMySQL(dateStr string) string {
	if idx := strings.Index(dateStr, "+"); idx != -1 {
		dateStr = dateStr[:idx]
	}
	if idx := strings.LastIndex(dateStr, " "); idx != -1 && len(dateStr)-idx <= 6 {
		if strings.Contains(dateStr[:idx], "T") {
			dateStr = dateStr[:idx]
		}
	}
	
	dateStr = strings.Replace(dateStr, "T", " ", 1)
	
	if len(dateStr) == 16 { 
		dateStr += ":00"
	}
	
	return dateStr
}

func Read_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	query := request.URL.Query()
	limitStr := query.Get("limit")
	pageStr := query.Get("page")

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
	db.DB.QueryRow("SELECT COUNT(*) FROM SERVICE").Scan(&total)

	sqlQuery := `
		SELECT s.id_service, s.nom, s.description, s.prix, s.id_categorie, s.id_prestataire, 
		       IFNULL(c.nom, 'Autre') as categorie_nom,
		       IF(p.date_fin_boost > NOW(), 1, 0) as is_boosted
		FROM SERVICE s
		LEFT JOIN CATEGORIE c ON s.id_categorie = c.id_categorie
		JOIN PRESTATAIRE p ON s.id_prestataire = p.id_prestataire
		ORDER BY is_boosted DESC, s.id_service DESC
		LIMIT ? OFFSET ?
	`

	rows, errorFetch := db.DB.Query(sqlQuery, limit, offset)
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabService []models.Service
	for rows.Next() {
		var service models.Service
		if err := rows.Scan(&service.ID, &service.Nom, &service.Description, &service.Prix, &service.IDCategorie, &service.IDPrestataire, &service.CategorieNom, &service.IsBoosted); err != nil {
			fmt.Printf("ERREUR SCAN SUR SERVICE: %v\n", err)
			continue
		}
		tabService = append(tabService, service)
	}

	if tabService == nil {
		tabService = []models.Service{}
	}

	dataResponse := map[string]interface{}{
		"data":        tabService,
		"total":       total,
		"currentPage": page,
		"totalPages":  (total + limit - 1) / limit,
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(dataResponse)
}

func Create_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var service models.Service
	if err := json.NewDecoder(request.Body).Decode(&service); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	service.Nom = html.EscapeString(strings.TrimSpace(service.Nom))
	service.Description = html.EscapeString(strings.TrimSpace(service.Description))

	if service.Nom == "" || service.Description == "" {
		http.Error(response, "Le nom et la description sont requis.", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO SERVICE (nom, description, id_categorie) VALUES (?, ?, ?)", service.Nom, service.Description, service.IDCategorie)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	service.ID = int(id)

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(service)
}

func Update_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")

	var service models.Service
	if err := json.NewDecoder(request.Body).Decode(&service); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	service.Nom = html.EscapeString(strings.TrimSpace(service.Nom))
	service.Description = html.EscapeString(strings.TrimSpace(service.Description))

	if service.Nom == "" {
		http.Error(response, "Le nom du service est requis", http.StatusBadRequest)
		return
	}

	query := `
		UPDATE SERVICE 
		SET nom = ?, description = ?, id_categorie = ?, prix = ?, id_prestataire = ? 
		WHERE id_service = ?
	`

	res, err := db.DB.Exec(query, service.Nom, service.Description, service.IDCategorie, service.Prix, service.IDPrestataire, id)

	if err != nil {
		http.Error(response, "Erreur lors de la mise à jour en base de données", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{
		"message": "Service mis à jour avec succès",
	})
}

func Delete_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")
	db.DB.Exec("DELETE FROM SERVICE WHERE id_service = ?", id)
	response.WriteHeader(http.StatusNoContent)
}

func Read_One_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var service models.Service

	err := db.DB.QueryRow("SELECT id_service, nom, description FROM service WHERE id_service = ?", id).Scan(&service.ID, &service.Nom, &service.Description)

	if err != nil {
		http.Error(response, "Service non trouvé", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(service)
}

func Read_User_Services(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	idUser := request.PathValue("id")

	query := `
		SELECT r.id_reservation, s.id_service, s.nom, s.description, r.date_heure 
		FROM reservation_service r 
		JOIN service s ON r.id_service = s.id_service 
		WHERE r.id_utilisateur = ? 
		ORDER BY r.date_heure ASC
	`
	
	rows, err := db.DB.Query(query, idUser)
	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabRes []models.UserReservation
	for rows.Next() {
		var res models.UserReservation
		if err := rows.Scan(&res.IdReservation, &res.IdService, &res.Nom, &res.Description, &res.DateHeure); err == nil {
			tabRes = append(tabRes, res)
		}
	}

	if tabRes == nil {
		tabRes = []models.UserReservation{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabRes)
}

func Register_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	idService := request.PathValue("id")
	
	var payload struct {
		IdUtilisateur   int    `json:"id_utilisateur"`
		DateHeure       string `json:"date_heure"`
		IdDisponibilite int    `json:"id_disponibilite"`
	}

	if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	if payload.IdUtilisateur == 0 || payload.DateHeure == "" {
		http.Error(response, "Données manquantes", http.StatusBadRequest)
		return
	}

	_, err := db.DB.Exec("INSERT INTO reservation_service (id_service, id_utilisateur, date_heure) VALUES (?, ?, ?)", idService, payload.IdUtilisateur, payload.DateHeure)
	if err != nil {
		http.Error(response, "Erreur lors de la réservation.", http.StatusInternalServerError)
		return
	}

	if payload.IdDisponibilite > 0 {
		db.DB.Exec("UPDATE DISPONIBILITE SET est_reserve = 1 WHERE id_disponibilite = ?", payload.IdDisponibilite)
	}

	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Rendez-vous confirmé !"})
}

func Unregister_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	idReservation := request.PathValue("id")
	
	var payload map[string]int
	if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	idUser, exists := payload["id_utilisateur"]
	if !exists {
		http.Error(response, "ID Utilisateur manquant", http.StatusBadRequest)
		return
	}

	var stripePI sql.NullString
	var idPaiement sql.NullInt64
	var idService int
	var dateHeure string

	query := `
		SELECT p.stripe_pi, p.id_paiement, rs.id_service, rs.date_heure
		FROM RESERVATION_SERVICE rs
		LEFT JOIN PAIEMENT p ON rs.id_paiement = p.id_paiement
		WHERE rs.id_reservation = ? AND rs.id_utilisateur = ?
	`
	
	err := db.DB.QueryRow(query, idReservation, idUser).Scan(&stripePI, &idPaiement, &idService, &dateHeure)
	if err != nil {
		http.Error(response, "Réservation introuvable ou non autorisée.", http.StatusForbidden)
		return
	}

	if stripePI.Valid && stripePI.String != "" {
		stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
		
		params := &stripe.RefundParams{
			PaymentIntent: stripe.String(stripePI.String),
		}
		
		_, errRefund := refund.New(params)
		if errRefund != nil {
			http.Error(response, "L'annulation a échoué car le remboursement Stripe n'a pas pu aboutir.", http.StatusInternalServerError)
			return
		}

		db.DB.Exec("UPDATE PAIEMENT SET statut = 'remboursé' WHERE id_paiement = ?", idPaiement.Int64)
	}

	res, errDel := db.DB.Exec("DELETE FROM RESERVATION_SERVICE WHERE id_reservation = ? AND id_utilisateur = ?", idReservation, idUser)
	if errDel != nil {
		http.Error(response, "Erreur lors de la suppression de la réservation.", http.StatusInternalServerError)
		return
	}

	affected, _ := res.RowsAffected()
	if affected == 0 {
		http.Error(response, "Réservation introuvable.", http.StatusForbidden)
		return
	}

	var idPrestataire int
	err = db.DB.QueryRow("SELECT id_prestataire FROM SERVICE WHERE id_service = ?", idService).Scan(&idPrestataire)
	
	if err != nil {
		fmt.Println("❌ ERREUR : Impossible de trouver le prestataire pour le service :", err)
	} else {
		cleanDate := CleanDateForMySQL(dateHeure)

		resUpdate, errUpdate := db.DB.Exec(
			"UPDATE DISPONIBILITE SET est_reserve = 0 WHERE id_prestataire = ? AND date_heure = ?", 
			idPrestataire, cleanDate,
		)

		if errUpdate != nil {
			fmt.Println("❌ ERREUR UPDATE DISPONIBILITE :", errUpdate)
		} else {
			affectedRows, _ := resUpdate.RowsAffected()
			fmt.Printf("✅ DEBUG - Créneaux libérés : %d (Prestataire: %d, Date: %s)\n", affectedRows, idPrestataire, cleanDate)
		}
	}

	response.WriteHeader(http.StatusOK)
	
	msg := "Rendez-vous annulé avec succès !"
	if stripePI.Valid && stripePI.String != "" {
		msg = "Rendez-vous annulé ! Vous serez remboursé sous 5 à 10 jours sur votre carte bancaire."
	}
	json.NewEncoder(response).Encode(map[string]string{"message": msg})
}

func GetServicesByCategory(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	categorieIDStr := request.URL.Query().Get("categorie")
	
	query := `SELECT id_service, nom, description, id_categorie FROM SERVICE WHERE id_categorie = ?`
	rows, err := db.DB.Query(query, categorieIDStr)
	if err != nil {
		http.Error(response, "Erreur récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var services []models.Service
	for rows.Next() {
		var s models.Service
		if err := rows.Scan(&s.ID, &s.Nom, &s.Description, &s.IDCategorie); err == nil {
			services = append(services, s)
		}
	}

	if services == nil {
		services = []models.Service{}
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(services)
}

func CreateServiceCheckoutSession(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
	idServiceStr := request.PathValue("id")
	
	var payload struct {
		IdUtilisateur   int    `json:"id_utilisateur"`
		DateHeure       string `json:"date_heure"`
		IdDisponibilite int    `json:"id_disponibilite"`
	}

	if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	if payload.IdUtilisateur == 0 || payload.DateHeure == "" || payload.IdDisponibilite == 0 {
		http.Error(response, "Données manquantes (ID, Date ou Créneau)", http.StatusBadRequest)
		return
	}

	var nomSvc string
	var prix float64

	err := db.DB.QueryRow("SELECT nom, prix FROM SERVICE WHERE id_service = ?", idServiceStr).Scan(&nomSvc, &prix)
	if err != nil {
		http.Error(response, "Service introuvable.", http.StatusNotFound)
		return
	}

    idService, _ := strconv.Atoi(idServiceStr)

	if prix <= 0 {
		cleanDate := CleanDateForMySQL(payload.DateHeure)

		_, errInsert := db.DB.Exec(
			"INSERT INTO RESERVATION_SERVICE (id_service, id_utilisateur, date_heure) VALUES (?, ?, ?)", 
			idService, payload.IdUtilisateur, cleanDate,
		)
		if errInsert != nil {
			fmt.Println("ERREUR INSERTION GRATUITE:", errInsert)
			http.Error(response, "Erreur lors de la réservation.", http.StatusInternalServerError)
			return
		}
		
		db.DB.Exec("UPDATE DISPONIBILITE SET est_reserve = 1 WHERE id_disponibilite = ?", payload.IdDisponibilite)

		json.NewEncoder(response).Encode(map[string]interface{}{"isFree": true, "message": "Réservation gratuite confirmée !"})
		return
	}

	encodedDate := url.QueryEscape(payload.DateHeure)

	params := &stripe.CheckoutSessionParams{
		PaymentMethodTypes: stripe.StringSlice([]string{"card"}),
		Mode:               stripe.String(string(stripe.CheckoutSessionModePayment)),
		ClientReferenceID:  stripe.String(strconv.Itoa(payload.IdUtilisateur)),
		LineItems: []*stripe.CheckoutSessionLineItemParams{
			{
				PriceData: &stripe.CheckoutSessionLineItemPriceDataParams{
					Currency: stripe.String("eur"),
					ProductData: &stripe.CheckoutSessionLineItemPriceDataProductDataParams{
						Name: stripe.String("Réservation Service : " + nomSvc),
					},
					UnitAmount: stripe.Int64(int64(prix * 100)),
				},
				Quantity: stripe.Int64(1),
			},
		},
		SuccessURL: stripe.String(fmt.Sprintf("%s/success-service?session_id={CHECKOUT_SESSION_ID}&service_id=%d&user_id=%d&date_heure=%s&id_dispo=%d", utils.GetAPIBaseURL(), idService, payload.IdUtilisateur, encodedDate, payload.IdDisponibilite)),
		CancelURL:  stripe.String(utils.GetFrontBaseURL() + "/front/services/catalog.php"),
	}

	s, err := session.New(params)
	if err != nil {
		http.Error(response, "Erreur Stripe", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]interface{}{"isFree": false, "url": s.URL})
}

func Success_Service_Payment(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
	sessionID := request.URL.Query().Get("session_id")
	serviceIDStr := request.URL.Query().Get("service_id")
	userIDStr := request.URL.Query().Get("user_id")
	idDispoStr := request.URL.Query().Get("id_dispo") 
	dateHeure, _ := url.QueryUnescape(request.URL.Query().Get("date_heure"))

	s, err := session.Get(sessionID, nil)
	if err != nil || s.PaymentStatus != stripe.CheckoutSessionPaymentStatusPaid {
		http.Redirect(response, request, utils.GetFrontBaseURL()+"/front/services/catalog.php?error=paiement_echoue", http.StatusSeeOther)
		return
	}

	var urlFacture string
	var stripePI string

	if s.PaymentIntent != nil {
		stripePI = s.PaymentIntent.ID
		pi, errPI := paymentintent.Get(s.PaymentIntent.ID, nil)
		if errPI == nil && pi.LatestCharge != nil {
			ch, errC := charge.Get(pi.LatestCharge.ID, nil)
			if errC == nil {
				urlFacture = ch.ReceiptURL
			}
		}
	}

	prixPaye := float64(s.AmountTotal) / 100.0
	resPaiement, errP := db.DB.Exec(
		"INSERT INTO PAIEMENT (prix, statut, mode_paiement, url_facture, stripe_pi) VALUES (?, 'valide', 'carte', ?, ?)",
		prixPaye, urlFacture, stripePI,
	)

	if errP != nil {
		fmt.Println("ERREUR INSERTION PAIEMENT:", errP)
	} else {
		idPaiement, _ := resPaiement.LastInsertId()
		
		serviceID, _ := strconv.Atoi(serviceIDStr)
		userID, _ := strconv.Atoi(userIDStr)

		cleanDate := CleanDateForMySQL(dateHeure)

		_, errRes := db.DB.Exec(
			"INSERT INTO RESERVATION_SERVICE (id_service, id_utilisateur, date_heure, id_paiement) VALUES (?, ?, ?, ?)", 
			serviceID, userID, cleanDate, idPaiement,
		)

		if errRes != nil {
			fmt.Println("ERREUR INSERTION RESERVATION_SERVICE:", errRes)
		} else {
			if idDispoStr != "" {
				idDispo, _ := strconv.Atoi(idDispoStr)
				_, errDispo := db.DB.Exec("UPDATE DISPONIBILITE SET est_reserve = 1 WHERE id_disponibilite = ?", idDispo)
				
				if errDispo != nil {
					fmt.Println("ERREUR UPDATE DISPONIBILITE:", errDispo)
				}
			}
		}
	}

	http.Redirect(response, request, utils.GetFrontBaseURL()+"/front/services/catalog.php?success=reservation_validee", http.StatusSeeOther)
}