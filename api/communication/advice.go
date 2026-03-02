package communication

import (
	"encoding/json"
	"fmt"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_Conseil(response http.ResponseWriter, request *http.Request) {
	
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	rows, errorFetch := db.DB.Query("SELECT id_conseil, titre, description, date_publication, categorie FROM CONSEIL")
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close() 

	var tabConseil []models.Conseil
	for rows.Next() {
		var conseil models.Conseil
		if err := rows.Scan(&conseil.ID, &conseil.Titre, &conseil.Description, &conseil.Date, &conseil.Categorie); err != nil {
			fmt.Printf("ERREUR SCAN SUR CONSEIL ID %d: %v\n", conseil.ID, err)
			continue
		}
		tabConseil = append(tabConseil, conseil)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabConseil)
}

func Create_Conseil(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var conseil models.Conseil
	if err := json.NewDecoder(request.Body).Decode(&conseil); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO CONSEIL (titre, description, categorie) VALUES (?, ?, ?)", conseil.Titre, conseil.Description, conseil.Categorie)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	conseil.ID = id

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(conseil)
}

func Read_One_Conseil(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var conseil models.Conseil

	err := db.DB.QueryRow("SELECT id_conseil, titre, description, date_publication, categorie FROM CONSEIL WHERE id_conseil = ?", id).Scan(&conseil.ID, &conseil.Titre, &conseil.Description, &conseil.Date, &conseil.Categorie)
	
	if err != nil {
		http.Error(response, "Conseil non trouvé", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(conseil)
}

func Delete_Conseil(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	_, err := db.DB.Exec("DELETE FROM CONSEIL WHERE id_conseil = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusNoContent)
}

func Update_Conseil(response http.ResponseWriter, request *http.Request) {

    if utils.HandleCORS(response, request, "PUT") {
        return
    }

    id := request.PathValue("id")

    var conseil models.Conseil
    if err := json.NewDecoder(request.Body).Decode(&conseil); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    res, err := db.DB.Exec("UPDATE CONSEIL SET titre = ?, description = ?, categorie = ? WHERE id_conseil = ?", conseil.Titre, conseil.Description, conseil.Categorie, id)
    
    if err != nil {
        http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
        return
    }

    rowsAffected, _ := res.RowsAffected()
    if rowsAffected == 0 {
        http.Error(response, "Aucun conseil trouvé avec cet ID", http.StatusNotFound)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Conseil mis à jour avec succès"})
}