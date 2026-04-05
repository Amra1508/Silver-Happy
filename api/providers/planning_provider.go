package providers

import (
	"database/sql"
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
        SELECT e.id_evenement, e.nom, e.date_debut, e.date_fin, e.lieu, e.nombre_place 
        FROM evenement e
        INNER JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
        WHERE pe.id_prestataire = ? AND e.date_debut >= CURRENT_DATE()
        ORDER BY e.date_debut ASC
    `

	rows, err := db.DB.Query(sqlQuery, providerID)
	if err != nil {
		http.Error(response, "Erreur lors de la récupération du planning", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var planning []map[string]interface{}
	for rows.Next() {
		var id, places int
		var nom, lieu string
		var debut, fin sql.NullString

		if err := rows.Scan(&id, &nom, &debut, &fin, &lieu, &places); err == nil {
			evt := map[string]interface{}{
				"id_evenement": id,
				"nom":          nom,
				"lieu":         lieu,
				"nombre_place": places,
			}
			if debut.Valid {
				evt["date_debut"] = debut.String
			}
			if fin.Valid {
				evt["date_fin"] = fin.String
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