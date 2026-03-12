package services

import (
	"encoding/json"
	"fmt"
	"net/http"
	"strings"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	query := request.URL.Query()
	limitStr := query.Get("limit")
	pageStr := query.Get("page")

	limit := 10
	offset := 0
	page := 1

	if limitStr != "" {
		fmt.Sscanf(limitStr, "%d", &limit)
	}
	if pageStr != "" {
		fmt.Sscanf(pageStr, "%d", &page)
		offset = (page - 1) * limit
	}

	var total int
	db.DB.QueryRow("SELECT COUNT(*) FROM service").Scan(&total)

	rows, errorFetch := db.DB.Query("SELECT id_service, nom, description, disponibilite, id_utilisateur FROM service LIMIT ? OFFSET ?", limit, offset)
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabService []models.Service
	for rows.Next() {
		var service models.Service
		if err := rows.Scan(&service.ID, &service.Nom, &service.Description, &service.Disponibilite, &service.IdUtilisateur); err != nil {
			fmt.Printf("ERREUR SCAN SUR SERVICE: %v\n", err)
			continue
		}
		tabService = append(tabService, service)
	}

	if tabService == nil {
		tabService = []models.Service{}
	}

	dataResponse := map[string]interface{}{
		"data":        tabService,
		"total":       total,
		"currentPage": page,
		"totalPages":  (total + limit - 1) / limit,
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(dataResponse)
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

	service.Nom = strings.TrimSpace(service.Nom)
	service.Description = strings.TrimSpace(service.Description)

	if service.Nom == "" || service.Description =="" {
		http.Error(response, "Le nom et la description ne peuvent pas être vides.", http.StatusBadRequest)
		return
	}

	if service.Disponibilite == 0 {
		service.IdUtilisateur = nil
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

	service.Nom = strings.TrimSpace(service.Nom)
	service.Description = strings.TrimSpace(service.Description)

	if service.Nom == "" || service.Description =="" {
		http.Error(response, "Le nom et la description ne peuvent pas être vides.", http.StatusBadRequest)
		return
	}

	if service.Disponibilite == 0 {
		service.IdUtilisateur = nil
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