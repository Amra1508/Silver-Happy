package communication

import (
	"encoding/json"
	"fmt"
	"net/http"
	"strings"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_Avis(response http.ResponseWriter, request *http.Request) {
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
	db.DB.QueryRow("SELECT COUNT(*) FROM AVIS").Scan(&total)

	rows, errorFetch := db.DB.Query("SELECT id_avis, description, titre, note, date, categorie, id_prestataire FROM AVIS LIMIT ? OFFSET ?", limit, offset)
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabAvis []models.Avis
	for rows.Next() {
		var avis models.Avis
		if err := rows.Scan(&avis.ID, &avis.Description, &avis.Titre, &avis.Note, &avis.Date, &avis.Categorie, &avis.Prestataire); err != nil {
			continue
		}
		tabAvis = append(tabAvis, avis)
	}

	if tabAvis == nil {
		tabAvis = []models.Avis{}
	}

	dataResponse := map[string]interface{}{
		"data":        tabAvis,
		"total":       total,
		"currentPage": page,
		"totalPages":  (total + limit - 1) / limit,
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(dataResponse)
}

func Read_One_Avis(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    id := request.PathValue("id")
    userIdStr := request.URL.Query().Get("user_id")
    userId := 0
    if userIdStr != "" {
        fmt.Sscanf(userIdStr, "%d", &userId)
    }

    var avis models.Avis

    sqlQuery := `SELECT id_avis, description, titre, note, date, categorie, id_prestataire FROM AVIS WHERE id_avis = ?`

    err := db.DB.QueryRow(sqlQuery, id).Scan(
        &avis.ID, 
        &avis.Description, 
        &avis.Titre, 
		&avis.Note,
        &avis.Date, 
        &avis.Categorie,
		&avis.Prestataire,
    )
    
    if err != nil {
        http.Error(response, "Avis non trouvé", http.StatusNotFound)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(avis)
}

func Create_Avis(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var avis models.Avis
	if err := json.NewDecoder(request.Body).Decode(&avis); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	avis.Titre = strings.TrimSpace(avis.Titre)
	avis.Description = strings.TrimSpace(avis.Description)
	avis.Categorie = strings.TrimSpace(avis.Categorie)

	if avis.Titre == "" || avis.Description == "" || avis.Categorie == "" {
		http.Error(response, "Les champs ne peuvent pas être vides.", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO AVIS (description, titre, note, categorie, id_prestataire) VALUES (?, ?, ?, ?, ?)", avis.Description, avis.Titre, avis.Note, avis.Categorie, avis.Prestataire)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	avis.ID = id

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(avis)
}