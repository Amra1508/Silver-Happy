package services

import (
	"encoding/json"
	"fmt"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_Service(response http.ResponseWriter, request *http.Request) {
    
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    rows, errorFetch := db.DB.Query("SELECT id_service, nom, description, disponibilite, id_utilisateur FROM service")
    if errorFetch != nil {
        http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
        return
    }
    defer rows.Close() 

    var tabService []models.Service
    for rows.Next() {
        var service models.Service
        if err := rows.Scan(&service.ID, &service.Nom, &service.Description, &service.Disponibilite, &service.IdUtilisateur); err != nil {
            fmt.Printf("ERREUR SCAN SUR SERVICE ID %d: %v\n", service.ID, err)
            continue
        }
        tabService = append(tabService, service)
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(tabService)
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

    res, errorCreate := db.DB.Exec("INSERT INTO service (nom, description, disponibilite, id_utilisateur) VALUES (?, ?, ?, ?)", service.Nom, service.Description, service.Disponibilite, service.IdUtilisateur)
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

func Read_One_Service(response http.ResponseWriter, request *http.Request) {

    if utils.HandleCORS(response, request, "GET") {
        return
    }

    id := request.PathValue("id")
    var service models.Service

    err := db.DB.QueryRow("SELECT id_service, nom, description, disponibilite, id_utilisateur FROM service WHERE id_service = ?", id).Scan(&service.ID, &service.Nom, &service.Description, &service.Disponibilite, &service.IdUtilisateur)
    
    if err != nil {
        http.Error(response, "Service non trouvé", http.StatusNotFound)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(service)
}

func Delete_Service(response http.ResponseWriter, request *http.Request) {

    if utils.HandleCORS(response, request, "DELETE") {
        return
    }

    id := request.PathValue("id")

    _, err := db.DB.Exec("DELETE FROM service WHERE id_service = ?", id)
    if err != nil {
        http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
        return
    }

    response.WriteHeader(http.StatusNoContent)
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

    res, err := db.DB.Exec("UPDATE service SET nom = ?, description = ?, disponibilite = ?, id_utilisateur = ? WHERE id_service = ?", service.Nom, service.Description, service.Disponibilite, service.IdUtilisateur, id)
    
    if err != nil {
        http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
        return
    }

    rowsAffected, _ := res.RowsAffected()
    if rowsAffected == 0 {
        http.Error(response, "Aucun service trouvé avec cet ID", http.StatusNotFound)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Service mis à jour avec succès"})
}