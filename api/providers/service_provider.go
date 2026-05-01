package providers

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"
	"strconv"
	"time"
)

func Get_Services_Provider(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	query := `
		SELECT id_service, nom, description, id_categorie, prix, statut, motif_refus 
		FROM SERVICE 
		WHERE id_prestataire = ?
		ORDER BY id_service DESC
	`

	rows, err := db.DB.Query(query, providerID)
	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var services []models.Service

	for rows.Next() {
		var s models.Service
		var catID sql.NullInt64
		var motif sql.NullString

		if err := rows.Scan(&s.ID, &s.Nom, &s.Description, &catID, &s.Prix, &s.Statut, &motif); err == nil {
			if catID.Valid {
				id := int(catID.Int64)
				s.IDCategorie = &id
			}
			s.MotifRefusJS = ""
			if motif.Valid {
				s.MotifRefusJS = motif.String
			}
			services = append(services, s)
		}
	}

	if services == nil {
		services = []models.Service{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(services)
}

func Get_Historique_Services(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	sqlQuery := `
			SELECT r.id_reservation, r.id_utilisateur, r.prix_final, r.date_heure, s.nom, s.description, u.prenom, u.nom
			FROM RESERVATION_SERVICE r
			JOIN SERVICE s ON r.id_service = s.id_service
			JOIN UTILISATEUR u ON r.id_utilisateur = u.id_utilisateur
			WHERE s.id_prestataire = ? AND r.date_heure < NOW()
			ORDER BY r.date_heure DESC
		`

	rows, err := db.DB.Query(sqlQuery, providerID)
	if err != nil {
		http.Error(response, "Erreur SQL", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	history := []map[string]interface{}{}
	for rows.Next() {
		var idRes, idUser int
		var prix float64
		var dateHeure, sNom, sDescription, uPrenom, uNom string

		err := rows.Scan(&idRes, &idUser, &prix, &dateHeure, &sNom, &sDescription, &uPrenom, &uNom)
		if err != nil {
			continue
		}

		history = append(history, map[string]interface{}{
			"id_reservation":      idRes,
			"id_utilisateur":      idUser,
			"prix_final":          prix,
			"date_heure":          dateHeure,
			"service_nom":         sNom,
			"service_description": sDescription,
			"client_prenom":       uPrenom,
			"client_nom":          uNom,
		})
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(history)
}

func Update_Service_Provider(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	providerID := request.PathValue("id")
	serviceID := request.PathValue("id_service")

	var s models.Service
	if err := json.NewDecoder(request.Body).Decode(&s); err != nil {
		http.Error(response, "Données invalides", http.StatusBadRequest)
		return
	}

	query := `
		UPDATE SERVICE 
		SET nom = ?, description = ?, prix = ?, statut = 'en_attente'
		WHERE id_service = ? AND id_prestataire = ?
	`
	res, err := db.DB.Exec(query, s.Nom, s.Description, s.Prix, serviceID, providerID)

	if err != nil {
		http.Error(response, "Erreur serveur lors de la modification", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Service introuvable ou vous n'êtes pas autorisé à le modifier", http.StatusNotFound)
		return
	}

	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Service mis à jour avec succès"})
}

func Create_Service_Provider(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	providerIDStr := request.PathValue("id")
	providerID, err := strconv.ParseInt(providerIDStr, 10, 64)
	if err != nil {
		http.Error(response, "ID Prestataire invalide", http.StatusBadRequest)
		return
	}

	var idAbonnement sql.NullInt64
	db.DB.QueryRow("SELECT id_abonnement FROM PRESTATAIRE WHERE id_prestataire = ?", providerID).Scan(&idAbonnement)

	if !idAbonnement.Valid || idAbonnement.Int64 == 0 {
		http.Error(response, "Abonnement Pro requis pour générer des créneaux.", http.StatusForbidden)
		return
	}

	var s models.Service
	if err := json.NewDecoder(request.Body).Decode(&s); err != nil {
		http.Error(response, "Données invalides", http.StatusBadRequest)
		return
	}

	var providerCatID sql.NullInt64
	err = db.DB.QueryRow("SELECT id_categorie FROM PRESTATAIRE WHERE id_prestataire = ?", providerID).Scan(&providerCatID)

	if err == nil && providerCatID.Valid {
		id := int(providerCatID.Int64)
		s.IDCategorie = &id
	}

	query := `INSERT INTO SERVICE (nom, description, id_categorie, id_prestataire, prix, statut) VALUES (?, ?, ?, ?, ?, 'en_attente')`
	_, err = db.DB.Exec(query, s.Nom, s.Description, s.IDCategorie, providerID, s.Prix)

	if err != nil {
		http.Error(response, "Erreur lors de la création du service", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(map[string]string{"message": "Service créé avec succès"})
}

func Delete_Service_Provider(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	providerID := request.PathValue("id")
	serviceID := request.PathValue("id_service")

	query := `DELETE FROM SERVICE WHERE id_service = ? AND id_prestataire = ?`
	res, err := db.DB.Exec(query, serviceID, providerID)

	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Service introuvable ou non autorisé", http.StatusNotFound)
		return
	}

	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Service supprimé"})
}

func Create_Disponibilite_Slot(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	providerID := request.PathValue("id")

	var idAbonnement sql.NullInt64
	db.DB.QueryRow("SELECT id_abonnement FROM PRESTATAIRE WHERE id_prestataire = ?", providerID).Scan(&idAbonnement)

	if !idAbonnement.Valid || idAbonnement.Int64 == 0 {
		http.Error(response, "Abonnement Pro requis pour générer des créneaux.", http.StatusForbidden)
		return
	}

	var req models.CreationDisponibilite

	if err := json.NewDecoder(request.Body).Decode(&req); err != nil {
		http.Error(response, "Données invalides", http.StatusBadRequest)
		return
	}

	var targetWeekday time.Weekday
	if req.JourSemaine == 7 {
		targetWeekday = time.Sunday
	} else {
		targetWeekday = time.Weekday(req.JourSemaine)
	}

	hasExclusion := req.ExclusionDebut != "" && req.ExclusionFin != ""
	var exDebut, exFin time.Time
	if hasExclusion {
		exDebut, _ = time.Parse("2006-01-02", req.ExclusionDebut)
		exFin, _ = time.Parse("2006-01-02", req.ExclusionFin)
	}

	now := time.Now()
	daysUntil := int(targetWeekday - now.Weekday())
	if daysUntil < 0 {
		daysUntil += 7
	}

	firstDate := now.AddDate(0, 0, daysUntil)
	mois := req.RecurrenceMois
	if mois <= 0 {
		mois = 3
	}
	weeksToGenerate := mois * 4

	slotsAdded := 0

	for w := 0; w < weeksToGenerate; w++ {
		currentDate := firstDate.AddDate(0, 0, w*7)
		dateStr := currentDate.Format("2006-01-02")

		if hasExclusion {
			dateOnly, _ := time.Parse("2006-01-02", dateStr)
			if (dateOnly.After(exDebut) || dateOnly.Equal(exDebut)) && (dateOnly.Before(exFin) || dateOnly.Equal(exFin)) {
				continue
			}
		}

		startTime, errStart := time.Parse("2006-01-02 15:04", dateStr+" "+req.HeureDebut)
		endTime, errEnd := time.Parse("2006-01-02 15:04", dateStr+" "+req.HeureFin)

		if errStart != nil || errEnd != nil || !startTime.Before(endTime) {
			continue
		}

		hasPause := req.PauseDebut != "" && req.PauseFin != ""
		var pauseStart, pauseEnd time.Time
		if hasPause {
			pauseStart, _ = time.Parse("2006-01-02 15:04", dateStr+" "+req.PauseDebut)
			pauseEnd, _ = time.Parse("2006-01-02 15:04", dateStr+" "+req.PauseFin)
		}

		for t := startTime; t.Before(endTime); t = t.Add(time.Duration(req.DureeMinutes) * time.Minute) {

			slotStart := t
			slotEnd := t.Add(time.Duration(req.DureeMinutes) * time.Minute)

			if slotStart.Before(time.Now()) {
				continue
			}

			if hasPause {
				if slotStart.Before(pauseEnd) && slotEnd.After(pauseStart) {
					continue
				}
			}

			formattedStart := slotStart.Format("2006-01-02 15:04:00")
			formattedEnd := slotEnd.Format("2006-01-02 15:04:00")

			var conflicts int
			conflictQuery := `
				SELECT COALESCE(SUM(conflits), 0) FROM (
					SELECT COUNT(*) as conflits 
					FROM DISPONIBILITE 
					WHERE id_prestataire = ? AND date_heure = ?
					
					UNION ALL
					
					SELECT COUNT(*) as conflits 
					FROM EVENEMENT e
					INNER JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
					WHERE pe.id_prestataire = ?
					  AND e.date_debut < ?
					  AND IFNULL(NULLIF(e.date_fin, ''), DATE_ADD(e.date_debut, INTERVAL 1 HOUR)) > ?
					  
					UNION ALL
					
					SELECT COUNT(*) as conflits 
					FROM RESERVATION_SERVICE rs
					INNER JOIN SERVICE s ON rs.id_service = s.id_service
					WHERE s.id_prestataire = ?
					  AND rs.date_heure < ?
					  AND DATE_ADD(rs.date_heure, INTERVAL 1 HOUR) > ?
				) as total_conflits
			`

			errCheck := db.DB.QueryRow(conflictQuery,
				providerID, formattedStart,
				providerID, formattedEnd, formattedStart,
				providerID, formattedEnd, formattedStart,
			).Scan(&conflicts)

			if errCheck == nil && conflicts == 0 {
				db.DB.Exec("INSERT INTO DISPONIBILITE (id_prestataire, date_heure, est_reserve) VALUES (?, ?, 0)", providerID, formattedStart)
				slotsAdded++
			}
		}
	}

	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(map[string]interface{}{
		"message": fmt.Sprintf("%d créneaux générés avec succès pour les %d prochains mois !", slotsAdded, mois),
	})
}

func Get_Available_Slots(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	query := `
        SELECT id_disponibilite, date_heure 
        FROM DISPONIBILITE 
        WHERE id_prestataire = ? AND est_reserve = 0 
        ORDER BY date_heure ASC
    `

	rows, err := db.DB.Query(query, providerID)
	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var slots []models.Disponibilite
	for rows.Next() {
		var d models.Disponibilite
		if err := rows.Scan(&d.ID, &d.DateHeure); err == nil {
			slots = append(slots, d)
		}
	}

	if slots == nil {
		slots = []models.Disponibilite{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(slots)
}

func Create_Reservation(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var req struct {
		IDService       int    `json:"id_service"`
		IDUtilisateur   int    `json:"id_utilisateur"`
		DateHeure       string `json:"date_heure"`
		IDDisponibilite int    `json:"id_disponibilite"`
	}

	if err := json.NewDecoder(request.Body).Decode(&req); err != nil {
		http.Error(response, "Données invalides", http.StatusBadRequest)
		return
	}

	queryRes := `INSERT INTO RESERVATION_SERVICE (id_service, id_utilisateur, date_heure) VALUES (?, ?, ?)`
	_, err := db.DB.Exec(queryRes, req.IDService, req.IDUtilisateur, req.DateHeure)

	if err != nil {
		http.Error(response, "Erreur de réservation", http.StatusInternalServerError)
		return
	}

	queryPlan := `UPDATE DISPONIBILITE SET est_reserve = 1 WHERE id_disponibilite = ?`
	db.DB.Exec(queryPlan, req.IDDisponibilite)

	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(map[string]string{"message": "Réservation confirmée !"})
}

func Get_Provider_Dispos(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	query := `
        SELECT id_disponibilite, date_heure, est_reserve 
        FROM DISPONIBILITE 
        WHERE id_prestataire = ? 
        ORDER BY date_heure ASC
    `

	rows, err := db.DB.Query(query, providerID)
	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var dispos []models.Disponibilite
	for rows.Next() {
		var d models.Disponibilite
		if err := rows.Scan(&d.ID, &d.DateHeure, &d.EstReserve); err == nil {
			dispos = append(dispos, d)
		}
	}

	if dispos == nil {
		dispos = []models.Disponibilite{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(dispos)
}

func Delete_Disponibilite_Slot(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	providerID := request.PathValue("id")
	dispoID := request.PathValue("id_disponibilite")

	query := `DELETE FROM DISPONIBILITE WHERE id_disponibilite = ? AND id_prestataire = ?`
	res, err := db.DB.Exec(query, dispoID, providerID)

	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Créneau introuvable ou vous n'êtes pas autorisé à le supprimer", http.StatusNotFound)
		return
	}

	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Créneau supprimé avec succès"})
}

func Delete_Disponibilites_By_Date(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	providerID := request.PathValue("id")
	dateStr := request.PathValue("date")

	query := `
        DELETE FROM DISPONIBILITE 
        WHERE id_prestataire = ? 
        AND DATE(date_heure) = ? 
        AND est_reserve = 0
    `

	result, err := db.DB.Exec(query, providerID, dateStr)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression en base de données", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := result.RowsAffected()

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]interface{}{
		"message": fmt.Sprintf("%d créneaux libres ont été supprimés pour cette journée.", rowsAffected),
	})
}
