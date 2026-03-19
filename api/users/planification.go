package users

import (
	"encoding/json"
	"fmt"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_User_Planning(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    userIdStr := request.URL.Query().Get("user_id")
    if userIdStr == "" {
        http.Error(response, "L'ID utilisateur (user_id) est requis", http.StatusBadRequest)
        return
    }

    var userId int
    fmt.Sscanf(userIdStr, "%d", &userId)

    if userId == 0 {
        http.Error(response, "ID utilisateur invalide", http.StatusBadRequest)
        return
    }

    sqlQuery := `
        SELECT 
            e.id_evenement, e.nom, e.description, e.lieu, e.date_debut, e.date_fin, e.image
        FROM EVENEMENT e
        JOIN INSCRIPTION i ON e.id_evenement = i.id_evenement
        WHERE i.id_utilisateur = ?
        ORDER BY e.date_debut ASC
    `

    rows, errorFetch := db.DB.Query(sqlQuery, userId)
    if errorFetch != nil {
        http.Error(response, "Erreur lors de la récupération du planning", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    var tabPlanning []models.Evenement

    for rows.Next() {
        var evt models.Evenement
        
        err := rows.Scan(
            &evt.ID, 
            &evt.Nom, 
            &evt.Description, 
            &evt.Lieu, 
            &evt.DateDebut, 
            &evt.DateFin, 
            &evt.Image,
        )
        
        if err != nil {
            fmt.Println("Erreur lors du scan de l'événement:", err)
            continue
        }
        tabPlanning = append(tabPlanning, evt)
    }

    if tabPlanning == nil {
        tabPlanning = []models.Evenement{}
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(tabPlanning)
}