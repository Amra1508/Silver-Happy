package users

import (
	"database/sql"
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

	query := `
        SELECT u.id_utilisateur, u.nom, u.prenom, u.email, 
               u.num_telephone, u.date_naissance, u.statut, 
               u.date_creation, u.motif_bannissement, u.duree_bannissement,
               a.rue, a.ville, a.code_postal, a.pays
        FROM UTILISATEUR u
        LEFT JOIN ADRESSE a ON u.id_adresse = a.id_adresse
    `
	rows, err := db.DB.Query(query)
	if err != nil {
		http.Error(response, "Erreur serveur", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabUtilisateur []models.Utilisateur
	for rows.Next() {
		var user models.Utilisateur
		var numTel, dateNaiss, dateCrea, motifBan, rue, ville, cp, pays sql.NullString
		var dureeBan sql.NullInt64

		err := rows.Scan(
			&user.ID, &user.Nom, &user.Prenom, &user.Email,
			&numTel, &dateNaiss, &user.Statut, &dateCrea, &motifBan, &dureeBan,
			&rue, &ville, &cp, &pays,
		)

		if err == nil {
			user.NumTelephone = numTel.String
			user.DateNaissance = dateNaiss.String
			user.DateCreation = dateCrea.String
			user.MotifBannissement = motifBan.String
			user.DureeBannissement = int(dureeBan.Int64)
			user.Adresse = rue.String
			user.Ville = ville.String
			user.CodePostal = cp.String
			user.Pays = pays.String

			tabUtilisateur = append(tabUtilisateur, user)
		}
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

	var dateNaissance interface{} = user.DateNaissance
	if user.DateNaissance == "" {
		dateNaissance = nil
	}

	resPlan, _ := db.DB.Exec("INSERT INTO PLANNING (nom, description, date_creation) VALUES (?, 'Planning généré automatiquement', NOW())", "Planning de "+user.Prenom)
	idPlanning, _ := resPlan.LastInsertId()

	resAdr, _ := db.DB.Exec("INSERT INTO ADRESSE (numero, rue, ville, code_postal, pays) VALUES (NULL, ?, ?, ?, ?)", user.Adresse, user.Ville, user.CodePostal, user.Pays)
	idAdresse, _ := resAdr.LastInsertId()

	res, err := db.DB.Exec(`
        INSERT INTO UTILISATEUR (nom, prenom, email, num_telephone, date_naissance, statut, date_creation, motif_bannissement, duree_bannissement, mdp, id_planning, id_adresse) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, '1234', ?, ?)`,
		user.Nom, user.Prenom, user.Email, user.NumTelephone, dateNaissance, user.Statut, user.MotifBannissement, user.DureeBannissement, idPlanning, idAdresse)

	if err != nil {
		http.Error(response, "Erreur création utilisateur", http.StatusInternalServerError)
		return
	}

	user.ID, _ = res.LastInsertId()
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(user)
}

func Update_User(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}
	id := request.PathValue("id")
	var user models.Utilisateur
	json.NewDecoder(request.Body).Decode(&user)

	var dateNaissance interface{} = user.DateNaissance
	if user.DateNaissance == "" {
		dateNaissance = nil
	}

	db.DB.Exec(`
        UPDATE UTILISATEUR SET nom = ?, prenom = ?, email = ?, num_telephone = ?, date_naissance = ?, statut = ?, motif_bannissement = ?, duree_bannissement = ? 
        WHERE id_utilisateur = ?`,
		user.Nom, user.Prenom, user.Email, user.NumTelephone, dateNaissance, user.Statut, user.MotifBannissement, user.DureeBannissement, id)

	db.DB.Exec("UPDATE ADRESSE SET rue = ?, ville = ?, code_postal = ?, pays = ? WHERE id_adresse = (SELECT id_adresse FROM UTILISATEUR WHERE id_utilisateur = ?)",
		user.Adresse, user.Ville, user.CodePostal, user.Pays, id)

	response.WriteHeader(http.StatusOK)
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

func Ban_User(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")

	var user models.Utilisateur
	if err := json.NewDecoder(request.Body).Decode(&user); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	_, err := db.DB.Exec("UPDATE UTILISATEUR SET statut = ?, motif_bannissement = ?, duree_bannissement = ? WHERE id_utilisateur = ?",
		user.Statut, user.MotifBannissement, user.DureeBannissement, id)

	if err != nil {
		http.Error(response, "Erreur lors de la mise à jour du statut", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Statut mis à jour avec succès"})
}
