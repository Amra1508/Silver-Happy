package providers

import (
	"database/sql"
	"encoding/json"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"
	"strconv"
)

func Get_Services_Provider(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	query := `
		SELECT id_service, nom, description, id_categorie, prix 
		FROM SERVICE 
		WHERE id_prestataire = ?
		ORDER BY id_service DESC
	`

	rows, err := db.DB.Query(query, providerID)
	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var services []models.Service

	for rows.Next() {
		var s models.Service
		var catID sql.NullInt64

		if err := rows.Scan(&s.ID, &s.Nom, &s.Description, &catID, &s.Prix); err == nil {
			if catID.Valid {
				id := int(catID.Int64)
				s.IDCategorie = &id
			}
			services = append(services, s)
		}
	}

	if services == nil {
		services = []models.Service{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(services)
}

func Update_Service_Provider(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	providerID := request.PathValue("id")
	serviceID := request.PathValue("id_service")

	var s models.Service
	if err := json.NewDecoder(request.Body).Decode(&s); err != nil {
		http.Error(response, "Données invalides", http.StatusBadRequest)
		return
	}

	query := `
		UPDATE SERVICE 
		SET nom = ?, description = ?, prix = ? 
		WHERE id_service = ? AND id_prestataire = ?
	`
	res, err := db.DB.Exec(query, s.Nom, s.Description, s.Prix, serviceID, providerID)
	
	if err != nil {
		http.Error(response, "Erreur serveur lors de la modification", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Service introuvable ou vous n'êtes pas autorisé à le modifier", http.StatusNotFound)
		return
	}

	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Service mis à jour avec succès"})
}

func Create_Service_Provider(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	providerIDStr := request.PathValue("id")
	providerID, err := strconv.ParseInt(providerIDStr, 10, 64)
	if err != nil {
		http.Error(response, "ID Prestataire invalide", http.StatusBadRequest)
		return
	}

	var s models.Service
	if err := json.NewDecoder(request.Body).Decode(&s); err != nil {
		http.Error(response, "Données invalides", http.StatusBadRequest)
		return
	}

	query := `INSERT INTO SERVICE (nom, description, id_categorie, id_prestataire, prix) VALUES (?, ?, ?, ?, ?)`
	_, err = db.DB.Exec(query, s.Nom, s.Description, s.IDCategorie, providerID, s.Prix)
	
	if err != nil {
		http.Error(response, "Erreur lors de la création du service", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(map[string]string{"message": "Service créé avec succès"})
}

func Delete_Service_Provider(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	providerID := request.PathValue("id")
	serviceID := request.PathValue("id_service")

	query := `DELETE FROM SERVICE WHERE id_service = ? AND id_prestataire = ?`
	res, err := db.DB.Exec(query, serviceID, providerID)

	if err != nil {
		http.Error(response, "Erreur base de données", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Service introuvable ou non autorisé", http.StatusNotFound)
		return
	}

	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Service supprimé"})
}