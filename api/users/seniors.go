package users

import (
	"encoding/json"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_User(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "GET") {
		return
	}

	rows, errorFetch := db.DB.Query("SELECT id_utilisateur, nom, prenom, email, num_telephone, date_naissance, statut, date_creation, motif_bannissement, duree_bannissement FROM UTILISATEUR")
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabUtilisateur []models.Utilisateur
	for rows.Next() {
		var user models.Utilisateur
		if err := rows.Scan(&user.ID, &user.Nom, &user.Prenom, &user.Email, &user.NumTelephone, &user.DateNaissance, &user.Statut, &user.DateCreation, &user.MotifBannissement, &user.DureeBannissement); err != nil {
			continue
		}
		tabUtilisateur = append(tabUtilisateur, user)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabUtilisateur)
}

func Create_User(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var user models.Utilisateur
	if err := json.NewDecoder(request.Body).Decode(&user); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO UTILISATEUR (nom, prenom, email, num_telephone, date_naissance, statut, date_creation, motif_bannissement, duree_bannissement, mdp) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, '1234')", user.Nom, user.Prenom, user.Email, user.NumTelephone, user.DateNaissance, user.Statut, user.MotifBannissement, user.DureeBannissement)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	user.ID = id

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(user)
}

func Read_One_User(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var user models.Utilisateur

	err := db.DB.QueryRow("SELECT id_utilisateur, nom, prenom, email, num_telephone, date_naissance, statut, date_creation, motif_bannissement, duree_bannissement FROM UTILISATEUR WHERE id_utilisateur = ?", id).Scan(&user.ID, &user.Nom, &user.Prenom, &user.Email, &user.NumTelephone, &user.DateNaissance, &user.Statut, &user.DateCreation, &user.MotifBannissement, &user.DureeBannissement)

	if err != nil {
		http.Error(response, "Utilisateur non trouvé", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(user)
}

func Delete_User(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	_, err := db.DB.Exec("DELETE FROM UTILISATEUR WHERE id_utilisateur = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusNoContent)
}

func Update_User(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")

	var user models.Utilisateur
	if err := json.NewDecoder(request.Body).Decode(&user); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	res, err := db.DB.Exec("UPDATE UTILISATEUR SET nom = ?, prenom = ?, email = ?, num_telephone = ?, date_naissance = ?, statut = ?, motif_bannissement = ?, duree_bannissement = ? WHERE id_utilisateur = ?", user.Nom, user.Prenom, user.Email, user.NumTelephone, user.DateNaissance, user.Statut, user.MotifBannissement, user.DureeBannissement, id)

	if err != nil {
		http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Aucun utilisateur trouvé avec cet ID", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Utilisateur mis à jour avec succès"})
}
