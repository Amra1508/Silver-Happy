package providers

import (
	"encoding/json"
	"main/auth"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"

	"github.com/golang-jwt/jwt/v5"
)

func Read_Provider_Planning(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	cookie, err := request.Cookie("provider_token")
	if err != nil {
		http.Error(response, "Non authentifié", http.StatusUnauthorized)
		return
	}

	tokenString := cookie.Value
	claims := &models.Claims{}
	token, err := jwt.ParseWithClaims(tokenString, claims, func(token *jwt.Token) (interface{}, error) {
		return auth.JwtKey, nil
	})

	if err != nil || !token.Valid {
		http.Error(response, "Session invalide", http.StatusUnauthorized)
		return
	}

	providerID := claims.UserID

	sqlQuery := `
		SELECT 'evenement' AS type_planning, e.id_evenement AS id, e.nom, e.date_debut AS debut, IFNULL(e.date_fin, '') AS fin, IFNULL(e.lieu, '') AS lieu, e.nombre_place AS places
		FROM evenement e
		INNER JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
		WHERE pe.id_prestataire = ?
		
		UNION ALL
		
		SELECT 'service' AS type_planning, rs.id_reservation AS id, s.nom, rs.date_heure AS debut, '' AS fin, 'Réservation Client' AS lieu, 1 AS places
		FROM RESERVATION_SERVICE rs
		INNER JOIN SERVICE s ON rs.id_service = s.id_service
		WHERE s.id_prestataire = ?
		
		ORDER BY debut ASC
	`

	rows, err := db.DB.Query(sqlQuery, providerID, providerID)
	if err != nil {
		http.Error(response, "Erreur lors de la récupération du planning", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var planning []map[string]interface{}
	for rows.Next() {
		var id, places int
		var typePlanning, nom, lieu, debut, fin string

		if err := rows.Scan(&typePlanning, &id, &nom, &debut, &fin, &lieu, &places); err == nil {
			evt := map[string]interface{}{
				"id":           id,
				"type":         typePlanning,
				"nom":          nom,
				"lieu":         lieu,
				"nombre_place": places,
				"date_debut":   debut,
			}
			if fin != "" {
				evt["date_fin"] = fin
			}
			planning = append(planning, evt)
		}
	}

	if planning == nil {
		planning = []map[string]interface{}{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]interface{}{
		"data": planning,
	})
}