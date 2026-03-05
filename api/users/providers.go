package users

import (
	"encoding/json"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"

	"golang.org/x/crypto/bcrypt"
)

func Read_Prestataire(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "GET") {
		return
	}

	query := `SELECT id_prestataire, IFNULL(siret, ''), IFNULL(nom, ''), IFNULL(prenom, ''), IFNULL(email, ''), IFNULL(num_telephone, ''), IFNULL(DATE_FORMAT(date_naissance, '%Y-%m-%d'), ''), IFNULL(est_valide, 0), IFNULL(tarifs, 0), IFNULL(type_prestation, '') FROM PRESTATAIRE`

	rows, errorFetch := db.DB.Query(query)
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabPrestataire []models.Prestataire
	for rows.Next() {
		var p models.Prestataire
		if err := rows.Scan(&p.ID, &p.Siret, &p.Nom, &p.Prenom, &p.Email, &p.NumTelephone, &p.DateNaissance, &p.EstValide, &p.Tarifs, &p.TypePrestation); err != nil {
			continue
		}
		tabPrestataire = append(tabPrestataire, p)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabPrestataire)
}

func Create_Prestataire(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var p models.Prestataire
	if err := json.NewDecoder(request.Body).Decode(&p); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	hashMdp, _ := bcrypt.GenerateFromPassword([]byte("1234"), bcrypt.DefaultCost)

	res, errorCreate := db.DB.Exec("INSERT INTO PRESTATAIRE (siret, nom, prenom, email, num_telephone, date_naissance, est_valide, tarifs, type_prestation, mdp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.EstValide, p.Tarifs, p.TypePrestation, string(hashMdp))

	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	p.ID = id

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(p)
}

func Read_One_Prestataire(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var p models.Prestataire

	query := `SELECT id_prestataire, IFNULL(siret, ''), IFNULL(nom, ''), IFNULL(prenom, ''), IFNULL(email, ''), IFNULL(num_telephone, ''), IFNULL(DATE_FORMAT(date_naissance, '%Y-%m-%d'), ''), IFNULL(est_valide, 0), IFNULL(tarifs, 0), IFNULL(type_prestation, '') FROM PRESTATAIRE WHERE id_prestataire = ?`

	err := db.DB.QueryRow(query, id).Scan(&p.ID, &p.Siret, &p.Nom, &p.Prenom, &p.Email, &p.NumTelephone, &p.DateNaissance, &p.EstValide, &p.Tarifs, &p.TypePrestation)

	if err != nil {
		http.Error(response, "Prestataire non trouvé", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(p)
}

func Delete_Prestataire(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	_, err := db.DB.Exec("DELETE FROM PRESTATAIRE WHERE id_prestataire = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusNoContent)
}

func Update_Prestataire(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")

	var p models.Prestataire
	if err := json.NewDecoder(request.Body).Decode(&p); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	res, err := db.DB.Exec("UPDATE PRESTATAIRE SET siret = ?, nom = ?, prenom = ?, email = ?, num_telephone = ?, date_naissance = ?, est_valide = ?, tarifs = ?, type_prestation = ? WHERE id_prestataire = ?", p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.EstValide, p.Tarifs, p.TypePrestation, id)

	if err != nil {
		http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Aucun prestataire trouvé avec cet ID", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Prestataire mis à jour avec succès"})
}

func Read_Prestataire_Documents(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")

	query := `
		SELECT d.id_document, d.type, d.lien 
		FROM DOCUMENT d
		INNER JOIN DEPOSE_PRESTATAIRE dp ON d.id_document = dp.id_document
		WHERE dp.id_prestataire = ?
	`

	rows, err := db.DB.Query(query, id)
	if err != nil {
		http.Error(response, "Erreur lors de la récupération des documents", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabDocuments []models.Document
	for rows.Next() {
		var doc models.Document
		if err := rows.Scan(&doc.ID, &doc.Type, &doc.Lien); err == nil {
			tabDocuments = append(tabDocuments, doc)
		}
	}

	if tabDocuments == nil {
		tabDocuments = make([]models.Document, 0)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabDocuments)
}
