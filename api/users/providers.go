package users

import (
	"encoding/json"
	"fmt"
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

	// On a ajouté date_creation à la fin du SELECT
	query := `SELECT id_prestataire, IFNULL(siret, ''), IFNULL(nom, ''), IFNULL(prenom, ''), IFNULL(email, ''), IFNULL(num_telephone, ''), IFNULL(DATE_FORMAT(date_naissance, '%Y-%m-%d'), ''), IFNULL(status, 'en attente'), IFNULL(motif_refus, ''), IFNULL(tarifs, 0), IFNULL(type_prestation, ''), IFNULL(DATE_FORMAT(date_creation, '%d/%m/%Y à %H:%i'), '') FROM PRESTATAIRE`

	rows, errorFetch := db.DB.Query(query)
	if errorFetch != nil {
		fmt.Println("Erreur SQL (Read_Prestataire) :", errorFetch)
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var listePrestataires []models.Prestataire

	for rows.Next() {
		var prestataire models.Prestataire

		// On n'oublie pas d'ajouter &prestataire.DateCreation à la fin du Scan
		err := rows.Scan(
			&prestataire.ID, &prestataire.Siret, &prestataire.Nom,
			&prestataire.Prenom, &prestataire.Email, &prestataire.NumTelephone,
			&prestataire.DateNaissance, &prestataire.Status, &prestataire.MotifRefus,
			&prestataire.Tarifs, &prestataire.TypePrestation, &prestataire.DateCreation,
		)

		if err != nil {
			fmt.Println("Erreur Lecture Prestataire :", err)
			continue
		}

		listePrestataires = append(listePrestataires, prestataire)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(listePrestataires)
}

func Create_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var prestataire models.Prestataire
	if err := json.NewDecoder(request.Body).Decode(&prestataire); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	hashMdp, _ := bcrypt.GenerateFromPassword([]byte("1234"), bcrypt.DefaultCost)

	// Pas besoin d'insérer date_creation, la BDD met l'heure actuelle toute seule !
	res, errorCreate := db.DB.Exec(
		"INSERT INTO PRESTATAIRE (siret, nom, prenom, email, num_telephone, date_naissance, status, motif_refus, tarifs, type_prestation, mdp) VALUES (?, ?, ?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?, ?)",
		prestataire.Siret, prestataire.Nom, prestataire.Prenom, prestataire.Email, prestataire.NumTelephone,
		prestataire.DateNaissance, prestataire.Status, prestataire.MotifRefus, prestataire.Tarifs,
		prestataire.TypePrestation, string(hashMdp),
	)

	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	idNouveau, _ := res.LastInsertId()
	prestataire.ID = idNouveau

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(prestataire)
}

func Read_One_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var prestataire models.Prestataire

	query := `SELECT id_prestataire, IFNULL(siret, ''), IFNULL(nom, ''), IFNULL(prenom, ''), IFNULL(email, ''), IFNULL(num_telephone, ''), IFNULL(DATE_FORMAT(date_naissance, '%Y-%m-%d'), ''), IFNULL(status, 'en attente'), IFNULL(motif_refus, ''), IFNULL(tarifs, 0), IFNULL(type_prestation, ''), IFNULL(DATE_FORMAT(date_creation, '%d/%m/%Y à %H:%i'), '') FROM PRESTATAIRE WHERE id_prestataire = ?`

	err := db.DB.QueryRow(query, id).Scan(
		&prestataire.ID, &prestataire.Siret, &prestataire.Nom,
		&prestataire.Prenom, &prestataire.Email, &prestataire.NumTelephone,
		&prestataire.DateNaissance, &prestataire.Status, &prestataire.MotifRefus,
		&prestataire.Tarifs, &prestataire.TypePrestation, &prestataire.DateCreation,
	)

	if err != nil {
		http.Error(response, "Prestataire non trouvé", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(prestataire)
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

	var prestataire models.Prestataire
	if err := json.NewDecoder(request.Body).Decode(&prestataire); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	res, err := db.DB.Exec(
		"UPDATE PRESTATAIRE SET siret = ?, nom = ?, prenom = ?, email = ?, num_telephone = ?, date_naissance = NULLIF(?, ''), status = ?, motif_refus = ?, tarifs = ?, type_prestation = ? WHERE id_prestataire = ?",
		prestataire.Siret, prestataire.Nom, prestataire.Prenom, prestataire.Email, prestataire.NumTelephone,
		prestataire.DateNaissance, prestataire.Status, prestataire.MotifRefus, prestataire.Tarifs,
		prestataire.TypePrestation, id,
	)

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

	var listeDocuments []models.Document

	for rows.Next() {
		var document models.Document
		err := rows.Scan(&document.ID, &document.Type, &document.Lien)
		if err == nil {
			listeDocuments = append(listeDocuments, document)
		}
	}

	if listeDocuments == nil {
		listeDocuments = make([]models.Document, 0)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(listeDocuments)
}
