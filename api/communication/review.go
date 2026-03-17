package communication

import (
	"encoding/json"
	"fmt"
	"net/http"

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

	rows, errorFetch := db.DB.Query("SELECT id_avis, description, titre, note, date FROM AVIS LIMIT ? OFFSET ?", limit, offset)
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabAvis []models.Avis
	for rows.Next() {
		var avis models.Avis
		if err := rows.Scan(&avis.ID, &avis.Description, &avis.Titre, &avis.Note, &avis.Date); err != nil {
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