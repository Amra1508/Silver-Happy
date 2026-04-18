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
		SELECT 'evenement' AS type_planning, e.id_evenement AS id, e.nom, e.date_debut AS debut, IFNULL(e.date_fin, '') AS fin, IFNULL(e.lieu, '') AS lieu, e.nombre_place AS places,
			   '' AS client_nom, '' AS client_email, '' AS client_tel, '' AS client_adresse
		FROM EVENEMENT e
		INNER JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
		WHERE pe.id_prestataire = ?
		
		UNION ALL
		
		SELECT 'service_reserve' AS type_planning, rs.id_reservation AS id, s.nom, rs.date_heure AS debut, '' AS fin, 
			   IFNULL(CONCAT_WS(' ', a.numero, a.rue, '-', a.code_postal, a.ville), 'Adresse non spécifiée') AS lieu, 
			   1 AS places,
			   IFNULL(CONCAT_WS(' ', u.prenom, u.nom), 'Client inconnu') AS client_nom, 
			   IFNULL(u.email, 'Non renseigné') AS client_email,
			   IFNULL(u.num_telephone, 'Non renseigné') AS client_tel,
			   IFNULL(CONCAT_WS(' ', a.numero, a.rue, '-', a.code_postal, a.ville), 'Non renseignée') AS client_adresse
		FROM RESERVATION_SERVICE rs
		INNER JOIN SERVICE s ON rs.id_service = s.id_service
		LEFT JOIN UTILISATEUR u ON rs.id_utilisateur = u.id_utilisateur
		LEFT JOIN ADRESSE a ON u.id_adresse = a.id_adresse
		WHERE s.id_prestataire = ?
		
		UNION ALL

		SELECT 'creneau_libre' AS type_planning, d.id_disponibilite AS id, 'Créneau disponible' AS nom, d.date_heure AS debut, '' AS fin, 'En attente' AS lieu, 1 AS places,
			   '' AS client_nom, '' AS client_email, '' AS client_tel, '' AS client_adresse
		FROM DISPONIBILITE d
		WHERE d.id_prestataire = ? AND d.est_reserve = 0

		ORDER BY debut ASC
	`

	rows, err := db.DB.Query(sqlQuery, providerID, providerID, providerID)
	if err != nil {
		http.Error(response, "Erreur lors de la récupération du planning", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var planning []map[string]interface{}
	for rows.Next() {
		var id, places int
		var typePlanning, nom, lieu, debut, fin, clientNom, clientEmail, clientTel, clientAdresse string

		if err := rows.Scan(&typePlanning, &id, &nom, &debut, &fin, &lieu, &places, &clientNom, &clientEmail, &clientTel, &clientAdresse); err == nil {
			evt := map[string]interface{}{
				"id":             id,
				"type":           typePlanning,
				"nom":            nom,
				"lieu":           lieu,
				"nombre_place":   places,
				"date_debut":     debut,
				"client_nom":     clientNom,
				"client_email":   clientEmail,
				"client_tel":     clientTel,
				"client_adresse": clientAdresse,
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