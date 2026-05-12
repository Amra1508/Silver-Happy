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
	if idx := strings.Index(dateStr, "+"); idx != -1 { dateStr = dateStr[:idx] }
	if idx := strings.LastIndex(dateStr, " "); idx != -1 && len(dateStr)-idx <= 6 {
		if strings.Contains(dateStr[:idx], "T") { dateStr = dateStr[:idx] }
	}
	dateStr = strings.Replace(dateStr, "T", " ", 1)
	if len(dateStr) == 16 { dateStr += ":00" }
	return dateStr
}

func Read_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	query := request.URL.Query()
	statusFilter := query.Get("statut")
	limit := 10
	page := 1
	if l := query.Get("limit"); l != "" { fmt.Sscanf(l, "%d", &limit) }
	if p := query.Get("page"); p != "" { fmt.Sscanf(p, "%d", &page) }
	offset := (page - 1) * limit

	var total int
	if statusFilter != "" {
		db.DB.QueryRow("SELECT COUNT(*) FROM SERVICE WHERE statut = ?", statusFilter).Scan(&total)
	} else {
		db.DB.QueryRow("SELECT COUNT(*) FROM SERVICE").Scan(&total)
	}

	sqlQuery := `
		SELECT s.id_service, s.nom, s.description, s.prix, s.id_prestataire,
			IF(p.date_fin_boost > NOW(), 1, 0) as is_boosted, p.nom, p.prenom, s.statut, s.duree
		FROM SERVICE s LEFT JOIN PRESTATAIRE p ON s.id_prestataire = p.id_prestataire`

	var rows *sql.Rows
	var err error
	if statusFilter != "" {
		sqlQuery += ` WHERE s.statut = ? ORDER BY is_boosted DESC, s.id_service DESC LIMIT ? OFFSET ?`
		rows, err = db.DB.Query(sqlQuery, statusFilter, limit, offset)
	} else {
		sqlQuery += ` ORDER BY is_boosted DESC, s.id_service DESC LIMIT ? OFFSET ?`
		rows, err = db.DB.Query(sqlQuery, limit, offset)
	}
	if err != nil { http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError); return }
	defer rows.Close()

	var tabService []models.Service
	for rows.Next() {
		var s models.Service
		if err := rows.Scan(&s.ID, &s.Nom, &s.Description, &s.Prix, &s.IDPrestataire, &s.IsBoosted, &s.PrestataireNom, &s.PrestatairePrenom, &s.Statut, &s.Duree); err != nil { continue }
		tabService = append(tabService, s)
	}
	if tabService == nil { tabService = []models.Service{} }

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]interface{}{
		"data": tabService, "total": total, "currentPage": page, "totalPages": (total + limit - 1) / limit,
	})
}

func Create_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") { return }

	var service models.Service
	if err := json.NewDecoder(request.Body).Decode(&service); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest); return
	}
	service.Nom = html.EscapeString(strings.TrimSpace(service.Nom))
	service.Description = html.EscapeString(strings.TrimSpace(service.Description))
	if service.Nom == "" || service.Description == "" {
		http.Error(response, "Le nom et la description sont requis.", http.StatusBadRequest); return
	}

	res, err := db.DB.Exec("INSERT INTO SERVICE (nom, description, prix, id_prestataire, statut) VALUES (?, ?, ?, ?, 'accepte')",
		service.Nom, service.Description, service.Prix, service.IDPrestataire)
	if err != nil { http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError); return }

	id, _ := res.LastInsertId()
	service.ID = int(id)
	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(service)
}

func Update_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") { return }

	id := request.PathValue("id")
	var service models.Service
	if err := json.NewDecoder(request.Body).Decode(&service); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest); return
	}
	service.Nom = html.EscapeString(strings.TrimSpace(service.Nom))
	service.Description = html.EscapeString(strings.TrimSpace(service.Description))
	if service.Nom == "" { http.Error(response, "Le nom du service est requis", http.StatusBadRequest); return }

	if _, err := db.DB.Exec(`UPDATE SERVICE SET nom=?, description=?, prix=?, id_prestataire=? WHERE id_service=?`,
		service.Nom, service.Description, service.Prix, service.IDPrestataire, id); err != nil {
		http.Error(response, "Erreur lors de la mise à jour en base de données", http.StatusInternalServerError); return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]string{"message": "Service mis à jour avec succès"})
}

func Update_Service_Status(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") { return }

	id := request.PathValue("id")
	var data struct {
		Statut     string `json:"statut"`
		MotifRefus string `json:"motif_refus"`
	}
	if err := json.NewDecoder(request.Body).Decode(&data); err != nil {
		http.Error(response, "Erreur format", http.StatusBadRequest); return
	}
	if _, err := db.DB.Exec("UPDATE SERVICE SET statut=?, motif_refus=? WHERE id_service=?", data.Statut, data.MotifRefus, id); err != nil {
		http.Error(response, "Erreur DB", http.StatusInternalServerError); return
	}
	json.NewEncoder(response).Encode("Statut service mis à jour")
}

func Delete_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") { return }
	db.DB.Exec("DELETE FROM SERVICE WHERE id_service = ?", request.PathValue("id"))
	response.WriteHeader(http.StatusNoContent)
}

func Read_One_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	var service models.Service
	if err := db.DB.QueryRow("SELECT id_service, nom, description, prix, statut FROM SERVICE WHERE id_service = ?",
		request.PathValue("id")).Scan(&service.ID, &service.Nom, &service.Description, &service.Prix, &service.Statut); err != nil {
		http.Error(response, "Service non trouvé", http.StatusNotFound); return
	}
	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(service)
}

func Read_User_Services(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	rows, err := db.DB.Query(`
		SELECT r.id_reservation, s.id_service, s.nom, s.description, r.date_heure 
		FROM RESERVATION_SERVICE r JOIN SERVICE s ON r.id_service = s.id_service 
		WHERE r.id_utilisateur = ? ORDER BY r.date_heure ASC`, request.PathValue("id"))
	if err != nil { http.Error(response, "Erreur base de données", http.StatusInternalServerError); return }
	defer rows.Close()

	var tabRes []models.UserReservation
	for rows.Next() {
		var res models.UserReservation
		if err := rows.Scan(&res.IdReservation, &res.IdService, &res.Nom, &res.Description, &res.DateHeure); err == nil {
			tabRes = append(tabRes, res)
		}
	}
	if tabRes == nil { tabRes = []models.UserReservation{} }
	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabRes)
}

func Register_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") { return }

	idService := request.PathValue("id")
	var payload struct {
		IdUtilisateur   int    `json:"id_utilisateur"`
		DateHeure       string `json:"date_heure"`
		IdDisponibilite int    `json:"id_disponibilite"`
	}
	if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest); return
	}
	if payload.IdUtilisateur == 0 || payload.DateHeure == "" {
		http.Error(response, "Données manquantes", http.StatusBadRequest); return
	}

	var duree, idPresta int
	db.DB.QueryRow("SELECT duree, id_prestataire FROM SERVICE WHERE id_service = ?", idService).Scan(&duree, &idPresta)
	cleanDate := CleanDateForMySQL(payload.DateHeure)

	var conflictDirect int
	db.DB.QueryRow(`
		SELECT COUNT(*) FROM RESERVATION_SERVICE rs JOIN SERVICE s ON rs.id_service = s.id_service
		WHERE s.id_prestataire = ? AND rs.date_heure < DATE_ADD(?, INTERVAL ? MINUTE)
		AND DATE_ADD(rs.date_heure, INTERVAL s.duree MINUTE) > ?`,
		idPresta, cleanDate, duree, cleanDate).Scan(&conflictDirect)
	if conflictDirect > 0 {
		http.Error(response, "Créneau indisponible : chevauchement avec une réservation existante.", http.StatusConflict); return
	}

	var dispoFit int
	db.DB.QueryRow(`
		SELECT COUNT(*) FROM DISPONIBILITE WHERE id_prestataire = ?
		AND date_heure_debut <= ? AND date_heure_fin >= DATE_ADD(?, INTERVAL ? MINUTE)`,
		idPresta, cleanDate, cleanDate, duree).Scan(&dispoFit)
	if dispoFit == 0 {
		http.Error(response, "Ce créneau dépasse les disponibilités du prestataire.", http.StatusConflict); return
	}

	var conflictEvt int
	db.DB.QueryRow(`
		SELECT COUNT(*) FROM INSCRIPTION i JOIN EVENEMENT e ON i.id_evenement = e.id_evenement
		WHERE i.id_utilisateur = ? AND e.date_debut < DATE_ADD(?, INTERVAL ? MINUTE) AND e.date_fin > ?`,
		payload.IdUtilisateur, cleanDate, duree, cleanDate).Scan(&conflictEvt)
	if conflictEvt > 0 {
		http.Error(response, "Conflit d'horaire : vous êtes déjà inscrit à un événement sur ce créneau.", http.StatusConflict); return
	}

	if _, err := db.DB.Exec("INSERT INTO RESERVATION_SERVICE (id_service, id_utilisateur, date_heure) VALUES (?, ?, ?)",
		idService, payload.IdUtilisateur, payload.DateHeure); err != nil {
		http.Error(response, "Erreur lors de la réservation.", http.StatusInternalServerError); return
	}

	if payload.IdDisponibilite > 0 {
		db.DB.Exec("DELETE FROM DISPONIBILITE WHERE id_disponibilite = ?", payload.IdDisponibilite)
	}

	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Rendez-vous confirmé !"})
}

func Unregister_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") { return }

	idReservation := request.PathValue("id")
	var payload map[string]int
	if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest); return
	}
	idUser, exists := payload["id_utilisateur"]
	if !exists { http.Error(response, "ID Utilisateur manquant", http.StatusBadRequest); return }

	var stripePI sql.NullString
	var idPaiement sql.NullInt64
	var idService int
	var dateHeure string

	if err := db.DB.QueryRow(`
		SELECT p.stripe_pi, p.id_paiement, rs.id_service, rs.date_heure
		FROM RESERVATION_SERVICE rs LEFT JOIN PAIEMENT p ON rs.id_paiement = p.id_paiement
		WHERE rs.id_reservation = ? AND rs.id_utilisateur = ?`, idReservation, idUser).Scan(&stripePI, &idPaiement, &idService, &dateHeure); err != nil {
		http.Error(response, "Réservation introuvable ou non autorisée.", http.StatusForbidden); return
	}

	if stripePI.Valid && stripePI.String != "" {
		stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
		if _, err := refund.New(&stripe.RefundParams{PaymentIntent: stripe.String(stripePI.String)}); err != nil {
			http.Error(response, "L'annulation a échoué car le remboursement Stripe n'a pas pu aboutir.", http.StatusInternalServerError); return
		}
		db.DB.Exec("UPDATE PAIEMENT SET statut = 'remboursé' WHERE id_paiement = ?", idPaiement.Int64)
	}

	res, err := db.DB.Exec("DELETE FROM RESERVATION_SERVICE WHERE id_reservation = ? AND id_utilisateur = ?", idReservation, idUser)
	if err != nil { http.Error(response, "Erreur lors de la suppression de la réservation.", http.StatusInternalServerError); return }

	if affected, _ := res.RowsAffected(); affected == 0 {
		http.Error(response, "Réservation introuvable.", http.StatusForbidden); return
	}

	var idPrestataire int
	if err := db.DB.QueryRow("SELECT id_prestataire FROM SERVICE WHERE id_service = ?", idService).Scan(&idPrestataire); err == nil {
		db.DB.Exec("INSERT INTO DISPONIBILITE (id_prestataire, date_heure_debut) VALUES (?, ?)", idPrestataire, CleanDateForMySQL(dateHeure))
	}

	msg := "Rendez-vous annulé avec succès !"
	if stripePI.Valid && stripePI.String != "" {
		msg = "Rendez-vous annulé ! Vous serez remboursé sous 5 à 10 jours sur votre carte bancaire."
	}
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": msg})
}

func GetServicesSortedByPrice(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	rows, err := db.DB.Query(`SELECT id_service, nom, description, prix, id_prestataire FROM SERVICE WHERE statut = 'accepte' ORDER BY prix ASC`)
	if err != nil { http.Error(response, "Erreur lors de la récupération des prix", http.StatusInternalServerError); return }
	defer rows.Close()

	var services []models.Service
	for rows.Next() {
		var s models.Service
		if err := rows.Scan(&s.ID, &s.Nom, &s.Description, &s.Prix, &s.IDPrestataire); err == nil {
			services = append(services, s)
		}
	}
	if services == nil { services = []models.Service{} }
	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(services)
}

func CreateServiceCheckoutSession(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") { return }

    stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
    idServiceStr := request.PathValue("id")

    var payload struct {
        IdUtilisateur   int    `json:"id_utilisateur"`
        DateHeure       string `json:"date_heure"`
        IdDisponibilite int    `json:"id_disponibilite"`
    }
    if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest); return
    }
    if payload.IdUtilisateur == 0 {
        http.Error(response, "Données manquantes (ID Utilisateur)", http.StatusBadRequest); return
    }

    if payload.DateHeure == "" || payload.DateHeure == "undefined" {
        http.Error(response, "Date manquante ou invalide.", http.StatusBadRequest); return
    }

    var nomSvc, statut string
    var prixStandard, prixFinal float64
    if err := db.DB.QueryRow("SELECT nom, prix, statut FROM SERVICE WHERE id_service = ?", idServiceStr).Scan(&nomSvc, &prixStandard, &statut); err != nil {
        http.Error(response, "Service introuvable.", http.StatusNotFound); return
    }
    if statut != "accepte" {
        http.Error(response, "Ce service n'est pas encore disponible à la réservation.", http.StatusForbidden); return
    }

    var dureeService, idPresta int
    db.DB.QueryRow("SELECT duree, id_prestataire FROM SERVICE WHERE id_service = ?", idServiceStr).Scan(&dureeService, &idPresta)
    cleanDate := CleanDateForMySQL(payload.DateHeure)

    var conflictCount int
	db.DB.QueryRow(`
		SELECT COUNT(*) FROM RESERVATION_SERVICE rs JOIN SERVICE s ON rs.id_service = s.id_service
		WHERE s.id_prestataire = ? AND rs.date_heure < DATE_ADD(?, INTERVAL ? MINUTE)
		AND DATE_ADD(rs.date_heure, INTERVAL s.duree MINUTE) > ?`,
		idPresta, cleanDate, dureeService, cleanDate).Scan(&conflictCount)
	if conflictCount > 0 {
		http.Error(response, "Ce créneau n'est plus disponible.", http.StatusConflict); return
	}

    var dispoCount int
	db.DB.QueryRow(`
		SELECT COUNT(*) FROM DISPONIBILITE WHERE id_prestataire = ?
		AND date_heure_debut <= ? 
		AND date_heure_fin >= DATE_ADD(?, INTERVAL ? MINUTE)`,
		idPresta, cleanDate, cleanDate, dureeService).Scan(&dispoCount)
	if dispoCount == 0 {
		http.Error(response, "Ce créneau dépasse les disponibilités du prestataire.", http.StatusConflict); return
	}

	var conflictPrestaEvt int
	db.DB.QueryRow(`
		SELECT COUNT(*) FROM EVENEMENT e
		JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
		WHERE pe.id_prestataire = ?
		AND e.date_debut < DATE_ADD(?, INTERVAL ? MINUTE)
		AND e.date_fin > ?`,
		idPresta, cleanDate, dureeService, cleanDate).Scan(&conflictPrestaEvt)
	if conflictPrestaEvt > 0 {
		http.Error(response, "Le prestataire est indisponible : il a un événement sur ce créneau.", http.StatusConflict); return
	}

	var conflictUserService int
	db.DB.QueryRow(`
		SELECT COUNT(*) FROM RESERVATION_SERVICE rs 
		JOIN SERVICE s ON rs.id_service = s.id_service
		WHERE rs.id_utilisateur = ? 
		AND rs.date_heure < DATE_ADD(?, INTERVAL ? MINUTE)
		AND DATE_ADD(rs.date_heure, INTERVAL s.duree MINUTE) > ?`,
		payload.IdUtilisateur, cleanDate, dureeService, cleanDate).Scan(&conflictUserService)
	if conflictUserService > 0 {
		http.Error(response, "Vous avez déjà un service réservé sur ce créneau.", http.StatusConflict); return
	}

    var conflictEvt int
    db.DB.QueryRow(`
        SELECT COUNT(*) FROM INSCRIPTION i JOIN EVENEMENT e ON i.id_evenement = e.id_evenement
        WHERE i.id_utilisateur = ? AND e.date_debut < DATE_ADD(?, INTERVAL ? MINUTE) AND e.date_fin > ?`,
        payload.IdUtilisateur, cleanDate, dureeService, cleanDate).Scan(&conflictEvt)
    if conflictEvt > 0 {
        http.Error(response, "Conflit d'horaire : vous êtes déjà inscrit à un événement sur ce créneau.", http.StatusConflict); return
    }

    var prixNegocie sql.NullFloat64
    if err := db.DB.QueryRow(`
        SELECT prix_propose FROM MESSAGE_PRESTATAIRE 
        WHERE id_utilisateur = ? AND id_service = ? AND id_disponibilite = ? AND etat_offre = 'accepte'
        ORDER BY date DESC LIMIT 1`,
        payload.IdUtilisateur, idServiceStr, payload.IdDisponibilite).Scan(&prixNegocie); err == nil {
        prixFinal = prixNegocie.Float64
    } else {
        prixFinal = prixStandard
    }

    idService, _ := strconv.Atoi(idServiceStr)

    if prixFinal <= 0 {
        cleanD := CleanDateForMySQL(payload.DateHeure)
		if _, err := db.DB.Exec("INSERT INTO RESERVATION_SERVICE (id_service, id_utilisateur, date_heure) VALUES (?, ?, ?)",
			idService, payload.IdUtilisateur, cleanD); err != nil {
			http.Error(response, "Erreur lors de la réservation.", http.StatusInternalServerError); return
		}
        json.NewEncoder(response).Encode(map[string]interface{}{"isFree": true, "message": "Réservation gratuite confirmée !"})
        return
    }

    params := &stripe.CheckoutSessionParams{
        PaymentMethodTypes: stripe.StringSlice([]string{"card"}),
        Mode:               stripe.String(string(stripe.CheckoutSessionModePayment)),
        ClientReferenceID:  stripe.String(strconv.Itoa(payload.IdUtilisateur)),
        LineItems: []*stripe.CheckoutSessionLineItemParams{{
            PriceData: &stripe.CheckoutSessionLineItemPriceDataParams{
                Currency:    stripe.String("eur"),
                ProductData: &stripe.CheckoutSessionLineItemPriceDataProductDataParams{Name: stripe.String("Réservation Service : " + nomSvc)},
                UnitAmount:  stripe.Int64(int64(prixFinal * 100)),
            },
            Quantity: stripe.Int64(1),
        }},
        SuccessURL: stripe.String(fmt.Sprintf("%s/success-service?session_id={CHECKOUT_SESSION_ID}&service_id=%d&user_id=%d&date_heure=%s&id_dispo=%d&prix=%f",
            utils.GetAPIBaseURL(), idService, payload.IdUtilisateur, url.QueryEscape(payload.DateHeure), payload.IdDisponibilite, prixFinal)),
        CancelURL: stripe.String(utils.GetFrontBaseURL() + "/front/services/catalog.php"),
    }

    s, err := session.New(params)
    if err != nil { http.Error(response, "Erreur Stripe", http.StatusInternalServerError); return }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(map[string]interface{}{"isFree": false, "url": s.URL})
}

func Success_Service_Payment(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
	sessionID := request.URL.Query().Get("session_id")
	serviceIDStr := request.URL.Query().Get("service_id")
	userIDStr := request.URL.Query().Get("user_id")
	idDispoStr := request.URL.Query().Get("id_dispo")
	dateHeure := request.URL.Query().Get("date_heure")

	s, err := session.Get(sessionID, nil)
	if err != nil || s.PaymentStatus != stripe.CheckoutSessionPaymentStatusPaid {
		http.Redirect(response, request, utils.GetFrontBaseURL()+"/front/services/catalog.php?error=paiement_echoue", http.StatusSeeOther)
		return
	}

	var urlFacture, stripePI string
	if s.PaymentIntent != nil {
		stripePI = s.PaymentIntent.ID
		if pi, err := paymentintent.Get(s.PaymentIntent.ID, nil); err == nil && pi.LatestCharge != nil {
			if ch, err := charge.Get(pi.LatestCharge.ID, nil); err == nil {
				urlFacture = ch.ReceiptURL
			}
		}
	}

	prixPaye := float64(s.AmountTotal) / 100.0
	resPaiement, errP := db.DB.Exec(
		"INSERT INTO PAIEMENT (prix, statut, mode_paiement, url_facture, stripe_pi) VALUES (?, 'valide', 'carte', ?, ?)",
		prixPaye, urlFacture, stripePI)
	if errP != nil {
		http.Error(response, "Erreur lors de l'enregistrement du paiement", http.StatusInternalServerError); return
	}

	idPaiement, _ := resPaiement.LastInsertId()
	serviceID, _ := strconv.Atoi(serviceIDStr)
	userID, _ := strconv.Atoi(userIDStr)
	cleanDate := CleanDateForMySQL(dateHeure)

	if _, err := db.DB.Exec(`INSERT INTO RESERVATION_SERVICE (id_service, id_utilisateur, date_heure, id_paiement, prix_final) VALUES (?, ?, ?, ?, ?)`,
		serviceID, userID, cleanDate, idPaiement, prixPaye); err != nil {
		fmt.Fprintf(response, "ERREUR SQL : %v\n", err)
		return
	}

	if idDispoStr != "" && idDispoStr != "0" {
		if idDispo, _ := strconv.Atoi(idDispoStr); idDispo > 0 {
			db.DB.Exec("UPDATE MESSAGE_PRESTATAIRE SET etat_offre = 'paye' WHERE id_utilisateur = ? AND id_disponibilite = ? AND etat_offre = 'accepte' AND id_service = ?",
				userID, idDispo, serviceID)
		}
	}

	http.Redirect(response, request, utils.GetFrontBaseURL()+"/front/services/catalog.php?success=reservation_validee", http.StatusSeeOther)
}