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
        SELECT id_service, nom, description, prix, statut, motif_refus, duree
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
        var motif sql.NullString

        if err := rows.Scan(&s.ID, &s.Nom, &s.Description, &s.Prix, &s.Statut, &motif, &s.Duree); err != nil {
            fmt.Println("Erreur lors du scan de la ligne :", err)
            continue
        }

        s.MotifRefusJS = ""
        if motif.Valid {
            s.MotifRefusJS = motif.String
        }
        
        services = append(services, s)
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
		SET nom = ?, description = ?, prix = ?, statut = 'en_attente', duree = ?
		WHERE id_service = ? AND id_prestataire = ?
	`
	res, err := db.DB.Exec(query, s.Nom, s.Description, s.Prix, s.Duree, serviceID, providerID)

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

	query := `INSERT INTO SERVICE (nom, description, id_prestataire, prix, statut, duree) VALUES (?, ?, ?, ?, 'en_attente', ?)`
	_, err = db.DB.Exec(query, s.Nom, s.Description, providerID, s.Prix, s.Duree)

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

    now := time.Now()
    mois := req.RecurrenceMois
    if mois <= 0 { mois = 3 }
    weeksToGenerate := mois * 4

	hasExclusion := req.ExclusionDebut != "" && req.ExclusionFin != ""
    var exDebut, exFin time.Time
    if hasExclusion {
        exDebut, _ = time.Parse("2006-01-02", req.ExclusionDebut)
        exFin, _ = time.Parse("2006-01-02", req.ExclusionFin)
    }

	slotsAdded := 0
	for _, jourId := range req.JourSemaine {
        var targetWeekday time.Weekday
        if jourId == 7 { targetWeekday = time.Sunday } else { targetWeekday = time.Weekday(jourId) }

        daysUntil := int(targetWeekday - now.Weekday())
        if daysUntil < 0 { daysUntil += 7 }
        firstDateForThisDay := now.AddDate(0, 0, daysUntil)

        for w := 0; w < weeksToGenerate; w++ {
            currentDate := firstDateForThisDay.AddDate(0, 0, w*7)
            dateStr := currentDate.Format("2006-01-02")

            if hasExclusion && !currentDate.Before(exDebut) && !currentDate.After(exFin) {
                continue
            }

            type Interval struct { Start, End string }
            var intervals []Interval

            if req.PauseDebut != "" && req.PauseFin != "" {
                intervals = append(intervals, Interval{req.HeureDebut, req.PauseDebut})
                intervals = append(intervals, Interval{req.PauseFin, req.HeureFin})
            } else {
                intervals = append(intervals, Interval{req.HeureDebut, req.HeureFin})
            }

            for _, interval := range intervals {
                startStr := dateStr + " " + interval.Start + ":00"
                endStr := dateStr + " " + interval.End + ":00"

                var exists int
                db.DB.QueryRow("SELECT COUNT(*) FROM DISPONIBILITE WHERE id_prestataire = ? AND date_heure_debut = ?", providerID, startStr).Scan(&exists)

                if exists == 0 {
                    _, err := db.DB.Exec(`INSERT INTO DISPONIBILITE (id_prestataire, date_heure_debut, date_heure_fin) VALUES (?, ?, ?)`,
                        providerID, startStr, endStr)
                    if err == nil { slotsAdded++ }
                }
            }
        }
    }

	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(map[string]interface{}{"message": fmt.Sprintf("%d blocs de disponibilité créés.", slotsAdded)})
}

func Get_Available_Slots(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	dureeStr := request.URL.Query().Get("duree")
    duree := 0
    if dureeStr != "" {
        fmt.Sscanf(dureeStr, "%d", &duree)
    }

	layout := "2006-01-02 15:04:05"

	rowsDispo, err := db.DB.Query(`
        SELECT date_heure_debut, date_heure_fin 
        FROM DISPONIBILITE 
        WHERE id_prestataire = ? AND date_heure_debut > NOW()`, providerID)
    if err != nil {
        http.Error(response, "Erreur lors de la récupération des disponibilités", http.StatusInternalServerError)
        return
    }
    defer rowsDispo.Close()

	var openings []models.TimeRange
    for rowsDispo.Next() {
        var st, et time.Time
        if err := rowsDispo.Scan(&st, &et); err == nil {
            openings = append(openings, models.TimeRange{Start: st, End: et})
        }
    }

	rowsOcc, err := db.DB.Query(`
        SELECT rs.date_heure, s.duree
        FROM RESERVATION_SERVICE rs
        JOIN SERVICE s ON rs.id_service = s.id_service
        WHERE s.id_prestataire = ?
        UNION ALL
        SELECT e.date_debut, 60
        FROM EVENEMENT e
        JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
        WHERE pe.id_prestataire = ?`, providerID, providerID)
    if err != nil {
        http.Error(response, "Erreur lors de la récupération des occupations", http.StatusInternalServerError)
        return
    }
    defer rowsOcc.Close()

	var occupations []models.TimeRange
    for rowsOcc.Next() {
        var st time.Time
        var dur int
        if err := rowsOcc.Scan(&st, &dur); err == nil {
            et := st.Add(time.Duration(dur) * time.Minute)
            occupations = append(occupations, models.TimeRange{Start: st, End: et})
        }
    }

	var availableSlots []map[string]interface{}
    slotStep := 30
	now := time.Now()

	for _, open := range openings {
        current := open.Start

        for !current.Add(time.Duration(slotStep) * time.Minute).After(open.End) {
            
            slotEnd := current.Add(time.Duration(slotStep) * time.Minute)
            
            if current.Before(now) {
                current = slotEnd
                continue
            }

            isOccupied := false
            for _, occ := range occupations {
                if current.Before(occ.End) && slotEnd.After(occ.Start) {
                    isOccupied = true
                    break
                }
            }

            if !isOccupied {
                availableSlots = append(availableSlots, map[string]interface{}{
                    "id_disponibilite": 0,
                    "date_heure":       current.Format(layout),
                    "debut":            current.Format(layout),
                    "fin":              slotEnd.Format(layout),
                })
            }

            current = slotEnd
        }
    }

    if availableSlots == nil {
        availableSlots = []map[string]interface{}{}
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(availableSlots)
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

	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(map[string]string{"message": "Réservation confirmée !"})
}

func Get_Provider_Dispos(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") { return }

    providerID := request.PathValue("id")

    rows, _ := db.DB.Query(`SELECT id_disponibilite, date_heure_debut, date_heure_fin FROM DISPONIBILITE WHERE id_prestataire = ?`, providerID)
    defer rows.Close()
    
    type DispoOut struct {
        ID   int    `json:"id_disponibilite"`
        Time string `json:"time"`
        Fin  string `json:"fin"`
    }

    var res []DispoOut
    for rows.Next() {
        var id int
        var d, f string
        rows.Scan(&id, &d, &f)
        res = append(res, DispoOut{ID: id, Time: d, Fin: f})
    }
    
    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(res)
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
	dateFull := request.PathValue("date")

	dateStr := dateFull
    if len(dateFull) > 10 {
        dateStr = dateFull[:10]
    }

	query := `
        DELETE FROM DISPONIBILITE 
        WHERE id_prestataire = ? 
        AND DATE(date_heure_debut) = ? 
        AND NOT EXISTS (
            SELECT 1 FROM RESERVATION_SERVICE rs
            JOIN SERVICE s ON rs.id_service = s.id_service
            WHERE s.id_prestataire = DISPONIBILITE.id_prestataire
            AND rs.date_heure >= DISPONIBILITE.date_heure_debut 
            AND rs.date_heure < DISPONIBILITE.date_heure_fin
        )`

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
