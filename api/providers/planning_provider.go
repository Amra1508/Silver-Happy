package providers

import (
	"database/sql"
	"encoding/json"
	"main/auth"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"
	"sort"
	"strings"
	"time"

	"github.com/golang-jwt/jwt/v5"
)

func Read_Provider_Planning(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	cookie, err := request.Cookie("provider_token")
	if err != nil { http.Error(response, "Non authentifié", http.StatusUnauthorized); return }

	claims := &models.Claims{}
	token, err := jwt.ParseWithClaims(cookie.Value, claims, func(t *jwt.Token) (interface{}, error) {
		return auth.JwtKey, nil
	})
	if err != nil || !token.Valid { http.Error(response, "Session invalide", http.StatusUnauthorized); return }

	providerID := claims.UserID
	layout := "2006-01-02 15:04:05"

	type Bloc struct {
		ID       int
		Start    time.Time
		End      time.Time
		Nom      string
		TypeBloc string
		Client   string
		Tel      string
		Email    string
		Lieu     string
		Places   int
	}

	type Dispo struct {
		ID    int
		Start time.Time
		End   time.Time
	}

	var blocs []Bloc
	var dispos []Dispo

	rowsOcc, err := db.DB.Query(`
		SELECT rs.id_reservation, rs.date_heure, s.duree, s.nom, u.prenom, u.nom, u.num_telephone, u.email
		FROM RESERVATION_SERVICE rs
		JOIN SERVICE s ON rs.id_service = s.id_service
		LEFT JOIN UTILISATEUR u ON rs.id_utilisateur = u.id_utilisateur
		WHERE s.id_prestataire = ?`, providerID)
	if err == nil {
		defer rowsOcc.Close()
		for rowsOcc.Next() {
			var id, dur int
			var st time.Time
			var nomS, prenomU, nomU, telU, emailU sql.NullString
			if err := rowsOcc.Scan(&id, &st, &dur, &nomS, &prenomU, &nomU, &telU, &emailU); err != nil { continue }
			blocs = append(blocs, Bloc{
				ID: id, Start: st, End: st.Add(time.Duration(dur) * time.Minute),
				Nom: nomS.String, TypeBloc: "service_reserve",
				Client: strings.TrimSpace(prenomU.String + " " + nomU.String),
				Tel: telU.String, Email: emailU.String,
			})
		}
	}

	rowsEvt, err := db.DB.Query(`
		SELECT e.id_evenement, e.date_debut, e.date_fin, e.nom, e.lieu, e.nombre_place
		FROM EVENEMENT e JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
		WHERE pe.id_prestataire = ?`, providerID)
	if err == nil {
		defer rowsEvt.Close()
		for rowsEvt.Next() {
			var id, places int
			var debut, fin time.Time
			var nom, lieu sql.NullString
			if err := rowsEvt.Scan(&id, &debut, &fin, &nom, &lieu, &places); err != nil { continue }
			blocs = append(blocs, Bloc{
				ID: id, Start: debut, End: fin, Nom: nom.String,
				TypeBloc: "evenement", Lieu: lieu.String, Places: places,
			})
		}
	}

	rowsDispo, err := db.DB.Query(`
		SELECT id_disponibilite, date_heure_debut, date_heure_fin
		FROM DISPONIBILITE WHERE id_prestataire = ?`, providerID)
	if err == nil {
		defer rowsDispo.Close()
		for rowsDispo.Next() {
			var id int
			var s, e time.Time
			if err := rowsDispo.Scan(&id, &s, &e); err != nil { continue }
			dispos = append(dispos, Dispo{ID: id, Start: s, End: e})
		}
	}

	var finalPlanning []map[string]interface{}

	for _, dispo := range dispos {
		var overlapping []Bloc
		for _, b := range blocs {
			if b.Start.Before(dispo.End) && b.End.After(dispo.Start) {
				overlapping = append(overlapping, b)
			}
		}

		if len(overlapping) == 0 {
			finalPlanning = append(finalPlanning, map[string]interface{}{
				"type": "creneau_libre", "id": dispo.ID,
				"date_debut": dispo.Start.Format(layout), "date_fin": dispo.End.Format(layout), "nom": "Libre",
			})
			continue
		}

		sort.Slice(overlapping, func(i, j int) bool { return overlapping[i].Start.Before(overlapping[j].Start) })

		cursor := dispo.Start
		for _, b := range overlapping {
			bStart, bEnd := b.Start, b.End
			if bStart.Before(dispo.Start) { bStart = dispo.Start }
			if bEnd.After(dispo.End) { bEnd = dispo.End }
			if cursor.Before(bStart) {
				finalPlanning = append(finalPlanning, map[string]interface{}{
					"type": "creneau_libre", "id": dispo.ID,
					"date_debut": cursor.Format(layout), "date_fin": bStart.Format(layout), "nom": "Libre",
				})
			}
			cursor = bEnd
		}
		if cursor.Before(dispo.End) {
			finalPlanning = append(finalPlanning, map[string]interface{}{
				"type": "creneau_libre", "id": dispo.ID,
				"date_debut": cursor.Format(layout), "date_fin": dispo.End.Format(layout), "nom": "Libre",
			})
		}
	}

	for _, b := range blocs {
		if b.TypeBloc != "service_reserve" { continue }
		finalPlanning = append(finalPlanning, map[string]interface{}{
			"type": "service_reserve", "id": b.ID,
			"date_debut": b.Start.Format(layout), "date_fin": b.End.Format(layout),
			"nom": b.Nom, "client_nom": b.Client, "client_tel": b.Tel, "client_email": b.Email,
		})
	}

	for _, b := range blocs {
		if b.TypeBloc != "evenement" { continue }
		var segments []map[string]interface{}
		for _, dispo := range dispos {
			segStart, segEnd := b.Start, b.End
			if dispo.Start.After(segStart) { segStart = dispo.Start }
			if dispo.End.Before(segEnd) { segEnd = dispo.End }
			if segStart.Before(segEnd) {
				segments = append(segments, map[string]interface{}{
					"type": "evenement", "id": b.ID,
					"date_debut": segStart.Format(layout), "date_fin": segEnd.Format(layout),
					"nom": b.Nom, "lieu": b.Lieu, "nombre_place": b.Places,
				})
			}
		}
		if len(segments) == 0 {
			finalPlanning = append(finalPlanning, map[string]interface{}{
				"type": "evenement", "id": b.ID,
				"date_debut": b.Start.Format(layout), "date_fin": b.End.Format(layout),
				"nom": b.Nom, "lieu": b.Lieu, "nombre_place": b.Places,
			})
		} else {
			finalPlanning = append(finalPlanning, segments...)
		}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]interface{}{"data": finalPlanning})
}