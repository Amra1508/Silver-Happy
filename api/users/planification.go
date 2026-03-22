package users

import (
	"database/sql"
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
    var userId int
    fmt.Sscanf(userIdStr, "%d", &userId)

    if userId == 0 {
        http.Error(response, "ID utilisateur invalide", http.StatusBadRequest)
        return
    }

    var tabPlanning []models.PlanningItem

    sqlEvents := `
        SELECT e.id_evenement, e.nom, e.description, e.lieu, e.date_debut, e.date_fin
        FROM evenement e
        JOIN inscription i ON e.id_evenement = i.id_evenement
        WHERE i.id_utilisateur = ?
    `
    rowsEvents, err := db.DB.Query(sqlEvents, userId)
    if err == nil {
        defer rowsEvents.Close()
        for rowsEvents.Next() {
            var id int
            var nom string
            var desc, lieu, dateDebut, dateFin sql.NullString
            
            rowsEvents.Scan(&id, &nom, &desc, &lieu, &dateDebut, &dateFin)
            
            tabPlanning = append(tabPlanning, models.PlanningItem{
                ID:          fmt.Sprintf("evt_%d", id),
                Title:       nom,
                Start:       dateDebut.String,
                End:         dateFin.String,
                Description: desc.String,
                Location:    lieu.String,
                Type:        "evenement",
                Color:       "#E1AB2B",
            })
        }
    }

    sqlServices := `
        SELECT r.id_reservation, s.nom, s.description, r.date_heure
        FROM reservation_service r
        JOIN service s ON r.id_service = s.id_service
        WHERE r.id_utilisateur = ?
    `
    rowsServices, err2 := db.DB.Query(sqlServices, userId)
    if err2 == nil {
        defer rowsServices.Close()
        for rowsServices.Next() {
            var id int
            var nom string
            var desc, dateHeure sql.NullString
            
            rowsServices.Scan(&id, &nom, &desc, &dateHeure)
            
            tabPlanning = append(tabPlanning, models.PlanningItem{
                ID:          fmt.Sprintf("srv_%d", id),
                Title:       nom,
                Start:       dateHeure.String,
                Description: desc.String,
                Location:    "À définir avec le prestataire",
                Type:        "service",
                Color:       "#1C5B8F",
            })
        }
    }

    if tabPlanning == nil {
        tabPlanning = []models.PlanningItem{}
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(tabPlanning)
}