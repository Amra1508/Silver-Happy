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

func Read_Categorie(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	query := request.URL.Query()
	limitStr := query.Get("limit")
	pageStr := query.Get("page")

	limit := 100 
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
	db.DB.QueryRow("SELECT COUNT(*) FROM CATEGORIE").Scan(&total)

	rows, errorFetch := db.DB.Query("SELECT id_categorie, nom, description FROM CATEGORIE LIMIT ? OFFSET ?", limit, offset)
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabCategorie []models.Categorie
	for rows.Next() {
		var categorie models.Categorie
		if err := rows.Scan(&categorie.ID, &categorie.Nom, &categorie.Description); err != nil {
			fmt.Printf("ERREUR SCAN SUR CATEGORIE: %v\n", err)
			continue
		}
		tabCategorie = append(tabCategorie, categorie)
	}

	if tabCategorie == nil {
		tabCategorie = []models.Categorie{}
	}

	dataResponse := map[string]interface{}{
		"data":        tabCategorie,
		"total":       total,
		"currentPage": page,
		"totalPages":  (total + limit - 1) / limit,
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(dataResponse)
}

func Create_Categorie(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var categorie models.Categorie
	if err := json.NewDecoder(request.Body).Decode(&categorie); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	categorie.Nom = strings.TrimSpace(categorie.Nom)
	categorie.Description = strings.TrimSpace(categorie.Description)

	if categorie.Nom == "" {
		http.Error(response, "Le nom de la catégorie ne peut pas être vide.", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO CATEGORIE (nom, description) VALUES (?, ?)", categorie.Nom, categorie.Description)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	categorie.ID = int(id)

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(categorie)
}

func Read_One_Categorie(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var categorie models.Categorie

	err := db.DB.QueryRow("SELECT id_categorie, nom, description FROM CATEGORIE WHERE id_categorie = ?", id).Scan(&categorie.ID, &categorie.Nom, &categorie.Description)

	if err != nil {
		http.Error(response, "Catégorie non trouvée", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(categorie)
}

func Update_Categorie(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")

	var categorie models.Categorie
	if err := json.NewDecoder(request.Body).Decode(&categorie); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	categorie.Nom = strings.TrimSpace(categorie.Nom)
	categorie.Description = strings.TrimSpace(categorie.Description)

	if categorie.Nom == "" {
		http.Error(response, "Le nom ne peut pas être vide.", http.StatusBadRequest)
		return
	}

	res, err := db.DB.Exec("UPDATE CATEGORIE SET nom = ?, description = ? WHERE id_categorie = ?", categorie.Nom, categorie.Description, id)

	if err != nil {
		http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Aucune catégorie trouvée avec cet ID", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Catégorie mise à jour avec succès"})
}

func Delete_Categorie(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	_, err := db.DB.Exec("DELETE FROM CATEGORIE WHERE id_categorie = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusNoContent)
}