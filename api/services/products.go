package services

import (
	"encoding/json"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"
)

func Read(response http.ResponseWriter, request *http.Request) {
	
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	rows, errorFetch := db.DB.Query("SELECT id_produit, nom, description, prix, stock FROM PRODUIT")
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close() 

	var tabProduit []models.Produit
	for rows.Next() {
		var produit models.Produit
		if err := rows.Scan(&produit.ID, &produit.Nom, &produit.Description, &produit.Prix, &produit.Stock); err != nil {
			continue
		}
		tabProduit = append(tabProduit, produit)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabProduit)
}

func Create(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var produit models.Produit
	if err := json.NewDecoder(request.Body).Decode(&produit); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO PRODUIT (nom, description, prix, stock) VALUES (?, ?, ?, ?)", produit.Nom, produit.Description, produit.Prix, produit.Stock)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	produit.ID = id

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(produit)
}

func Read_One(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var produit models.Produit

	err := db.DB.QueryRow("SELECT id_produit, nom, description, prix, stock FROM PRODUIT WHERE id_produit = ?", id).Scan(&produit.ID, &produit.Nom, &produit.Description, &produit.Prix, &produit.Stock)
	
	if err != nil {
		http.Error(response, "Produit non trouvé", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(produit)
}

func Delete(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	_, err := db.DB.Exec("DELETE FROM PRODUIT WHERE id_produit = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusNoContent)
}

func Update(response http.ResponseWriter, request *http.Request) {

    if utils.HandleCORS(response, request, "PUT") {
        return
    }

    id := request.PathValue("id")

    var produit models.Produit
    if err := json.NewDecoder(request.Body).Decode(&produit); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    res, err := db.DB.Exec("UPDATE PRODUIT SET nom = ?, description = ?, prix = ?, stock = ? WHERE id_produit = ?", produit.Nom, produit.Description, produit.Prix, produit.Stock, id)
    
    if err != nil {
        http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
        return
    }

    rowsAffected, _ := res.RowsAffected()
    if rowsAffected == 0 {
        http.Error(response, "Aucun produit trouvé avec cet ID", http.StatusNotFound)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Produit mis à jour avec succès"})
}