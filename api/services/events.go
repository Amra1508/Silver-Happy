package services

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"os"
	"path/filepath"
	"strings"
	"time"

	"main/db"
	"main/models"
	"main/utils"
)

const uploadDir = "./uploads"

func Read_Evenement(response http.ResponseWriter, request *http.Request) {
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
	db.DB.QueryRow("SELECT COUNT(*) FROM evenement").Scan(&total)

	rows, errorFetch := db.DB.Query(
		"SELECT id_evenement, nom, description, lieu, nombre_place, image, date_debut, date_fin FROM evenement LIMIT ? OFFSET ?",
		limit, offset,
	)
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabEvenement []models.Evenement
	for rows.Next() {
		var evt models.Evenement
		var imagePath sql.NullString
		var dateDebut sql.NullString
		var dateFin sql.NullString

		if err := rows.Scan(&evt.ID, &evt.Nom, &evt.Description, &evt.Lieu, &evt.NombrePlace, &imagePath, &dateDebut, &dateFin); err != nil {
			fmt.Printf("ERREUR SCAN SUR EVENEMENT ID %d: %v\n", evt.ID, err)
			continue
		}

		if imagePath.Valid {
			evt.Image = imagePath.String
		}
		if dateDebut.Valid {
			evt.DateDebut = dateDebut.String
		}
		if dateFin.Valid {
			evt.DateFin = dateFin.String
		}

		tabEvenement = append(tabEvenement, evt)
	}

	if tabEvenement == nil {
		tabEvenement = []models.Evenement{}
	}

	dataResponse := map[string]interface{}{
		"data":        tabEvenement,
		"total":       total,
		"currentPage": page,
		"totalPages":  (total + limit - 1) / limit,
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(dataResponse)
}

func Create_Evenement(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	err := request.ParseMultipartForm(10 << 20)
	if err != nil {
		http.Error(response, "Fichier trop volumineux ou format invalide", http.StatusBadRequest)
		return
	}

	nom := strings.TrimSpace(request.FormValue("nom"))
	desc := strings.TrimSpace(request.FormValue("description"))
	lieu := strings.TrimSpace(request.FormValue("lieu"))
	places := request.FormValue("nombre_place")
	debut := request.FormValue("date_debut")
	fin := request.FormValue("date_fin")

	if nom == "" || desc == "" || lieu == "" {
		http.Error(response, "Les champs ne peuvent pas être vides.", http.StatusBadRequest)
		return
	}

	file, handler, errFile := request.FormFile("image")
	var imagePath string

	if errFile == nil {
		defer file.Close()

		os.MkdirAll(uploadDir, os.ModePerm)
		fileName := fmt.Sprintf("%d_%s", time.Now().Unix(), handler.Filename)
		imagePath = filepath.Join(uploadDir, fileName)

		dst, errCreate := os.Create(imagePath)
		if errCreate != nil {
			http.Error(response, "Erreur lors de la sauvegarde du fichier", http.StatusInternalServerError)
			return
		}
		defer dst.Close()
		io.Copy(dst, file)
	}

	res, errorCreate := db.DB.Exec(
		"INSERT INTO evenement (nom, description, lieu, nombre_place, image, date_debut, date_fin) VALUES (?, ?, ?, ?, ?, ?, ?)",
		nom, desc, lieu, places, imagePath, debut, fin,
	)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(map[string]interface{}{
		"id":      id,
		"status":  "success",
		"message": "Événement créé avec succès",
	})
}

func Read_One_Evenement(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var evt models.Evenement
	var imagePath sql.NullString
	var dateDebut sql.NullString
	var dateFin sql.NullString

	err := db.DB.QueryRow(
		"SELECT id_evenement, nom, description, lieu, nombre_place, image, date_debut, date_fin FROM evenement WHERE id_evenement = ?",
		id,
	).Scan(&evt.ID, &evt.Nom, &evt.Description, &evt.Lieu, &evt.NombrePlace, &imagePath, &dateDebut, &dateFin)

	if err != nil {
		http.Error(response, "Événement non trouvé", http.StatusNotFound)
		return
	}

	if imagePath.Valid {
		evt.Image = imagePath.String
	}
	if dateDebut.Valid {
		evt.DateDebut = dateDebut.String
	}
	if dateFin.Valid {
		evt.DateFin = dateFin.String
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(evt)
}

func Delete_Evenement(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	var imagePath sql.NullString
	errQuery := db.DB.QueryRow("SELECT image FROM evenement WHERE id_evenement = ?", id).Scan(&imagePath)

	if errQuery != nil && errQuery != sql.ErrNoRows {
		http.Error(response, "Erreur lors de la recherche de l'événement", http.StatusInternalServerError)
		return
	}

	_, err := db.DB.Exec("DELETE FROM evenement WHERE id_evenement = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	if imagePath.Valid && imagePath.String != "" {
		os.Remove(imagePath.String)
	}

	response.WriteHeader(http.StatusNoContent)
}

func Update_Evenement(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")

	err := request.ParseMultipartForm(10 << 20)
	if err != nil {
		http.Error(response, "Erreur de formulaire ou fichier trop volumineux", http.StatusBadRequest)
		return
	}

	nom := strings.TrimSpace(request.FormValue("nom"))
	desc := strings.TrimSpace(request.FormValue("description"))
	lieu := strings.TrimSpace(request.FormValue("lieu"))
	places := request.FormValue("nombre_place")
	debut := request.FormValue("date_debut")
	fin := request.FormValue("date_fin")

	if nom == "" || desc == "" || lieu == "" {
		http.Error(response, "Les champs ne peuvent pas être vides.", http.StatusBadRequest)
		return
	}

	file, handler, errFile := request.FormFile("image")
	var imagePath string

	if errFile == nil {
		defer file.Close()

		os.MkdirAll(uploadDir, os.ModePerm)
		fileName := fmt.Sprintf("%d_%s", time.Now().Unix(), handler.Filename)
		imagePath = filepath.Join(uploadDir, fileName)

		dst, errCreate := os.Create(imagePath)
		if errCreate != nil {
			http.Error(response, "Erreur lors de la sauvegarde du fichier", http.StatusInternalServerError)
			return
		}
		defer dst.Close()
		io.Copy(dst, file)
	}

	var res sql.Result
	var errDb error

	if imagePath != "" {
		res, errDb = db.DB.Exec(
			"UPDATE evenement SET nom = ?, description = ?, lieu = ?, nombre_place = ?, image = ?, date_debut = ?, date_fin = ? WHERE id_evenement = ?",
			nom, desc, lieu, places, imagePath, debut, fin, id,
		)
	} else {
		res, errDb = db.DB.Exec(
			"UPDATE evenement SET nom = ?, description = ?, lieu = ?, nombre_place = ?, date_debut = ?, date_fin = ? WHERE id_evenement = ?",
			nom, desc, lieu, places, debut, fin, id,
		)
	}

	if errDb != nil {
		http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Aucun événement trouvé avec cet ID", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Événement mis à jour avec succès"})
}

func Link_Prestataire_Evenement(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	idEvt := request.PathValue("id")
	var payload map[string]int

	if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	idPrest := payload["id_prestataire"]

	_, err := db.DB.Exec("INSERT IGNORE INTO PRESTATAIRE_EVENEMENT (id_prestataire, id_evenement) VALUES (?, ?)", idPrest, idEvt)
	if err != nil {
		http.Error(response, "Erreur lors de la liaison", http.StatusInternalServerError)
		return
	}

	json.NewEncoder(response).Encode(map[string]string{"status": "success"})
}

func Read_Prestataires_For_Evenement(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}
	idEvt := request.PathValue("id")

	rows, err := db.DB.Query(`
        SELECT prestataire.id_prestataire, prestataire.nom, prestataire.prenom, prestataire.type_prestation 
        FROM PRESTATAIRE prestataire
        JOIN PRESTATAIRE_EVENEMENT pevent ON prestataire.id_prestataire = pevent.id_prestataire 
        WHERE pevent.id_evenement = ?`, idEvt)

	if err != nil {
		http.Error(response, "Erreur BDD", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var list []map[string]interface{}
	for rows.Next() {
		var id_prest int
		var nom, prenom, type_prest string
		rows.Scan(&id_prest, &nom, &prenom, &type_prest)
		list = append(list, map[string]interface{}{
			"id": id_prest, "nom": nom, "prenom": prenom, "type": type_prest,
		})
	}

	if list == nil {
		list = make([]map[string]interface{}, 0)
	}
	json.NewEncoder(response).Encode(list)
}

func Unlink_Prestataire_Evenement(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}
	idEvt := request.PathValue("id")
	idPrest := request.PathValue("id_prestataire")

	_, err := db.DB.Exec("DELETE FROM PRESTATAIRE_EVENEMENT WHERE id_evenement = ? AND id_prestataire = ?", idEvt, idPrest)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression du lien", http.StatusInternalServerError)
		return
	}

	json.NewEncoder(response).Encode(map[string]string{"status": "success"})
}

func Register_Evenement(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") {
        return
    }

    idEvt := request.PathValue("id")
    var payload map[string]int

    if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    idUser, exists := payload["id_utilisateur"]
    if !exists {
        http.Error(response, "ID Utilisateur manquant", http.StatusBadRequest)
        return
    }

    var count int
    errCheck := db.DB.QueryRow("SELECT COUNT(*) FROM INSCRIPTION WHERE id_utilisateur = ? AND id_evenement = ?", idUser, idEvt).Scan(&count)
    if errCheck == nil && count > 0 {
        http.Error(response, "Vous êtes déjà inscrit à cet événement.", http.StatusConflict)
        return
    }

    var places int
    err := db.DB.QueryRow("SELECT nombre_place FROM evenement WHERE id_evenement = ?", idEvt).Scan(&places)
    if err != nil {
        http.Error(response, "Événement introuvable", http.StatusNotFound)
        return
    }
    if places <= 0 {
        http.Error(response, "Désolé, cet événement est complet.", http.StatusForbidden)
        return
    }

    _, err = db.DB.Exec("INSERT INTO INSCRIPTION (id_utilisateur, id_evenement) VALUES (?, ?)", idUser, idEvt)
    if err != nil {
        http.Error(response, "Erreur lors de l'inscription en BDD.", http.StatusInternalServerError)
        return
    }

    db.DB.Exec("UPDATE evenement SET nombre_place = nombre_place - 1 WHERE id_evenement = ?", idEvt)

    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Inscription réussie !"})
}

func Read_User_Evenements(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    idUser := request.PathValue("id")

    query := `
        SELECT e.id_evenement, e.nom, e.description, e.lieu, e.image, e.date_debut, e.date_fin 
        FROM evenement e
        JOIN INSCRIPTION i ON e.id_evenement = i.id_evenement
        WHERE i.id_utilisateur = ?
        ORDER BY e.date_debut ASC
    `
    
    rows, err := db.DB.Query(query, idUser)
    if err != nil {
        http.Error(response, "Erreur base de données", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    var tabEvenement []models.Evenement
    for rows.Next() {
        var evt models.Evenement
        var imagePath, dateDebut, dateFin sql.NullString

        err := rows.Scan(&evt.ID, &evt.Nom, &evt.Description, &evt.Lieu, &imagePath, &dateDebut, &dateFin)
        if err == nil {
            evt.Image = imagePath.String
            evt.DateDebut = dateDebut.String
            evt.DateFin = dateFin.String
            tabEvenement = append(tabEvenement, evt)
        }
    }

    if tabEvenement == nil {
        tabEvenement = []models.Evenement{}
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(tabEvenement)
}

func Unregister_Evenement(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") {
        return
    }

    idEvt := request.PathValue("id")
    var payload map[string]int

    if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    idUser, exists := payload["id_utilisateur"]
    if !exists {
        http.Error(response, "ID Utilisateur manquant", http.StatusBadRequest)
        return
    }

    res, err := db.DB.Exec("DELETE FROM INSCRIPTION WHERE id_utilisateur = ? AND id_evenement = ?", idUser, idEvt)
    if err != nil {
        http.Error(response, "Erreur lors de la désinscription en BDD.", http.StatusInternalServerError)
        return
    }

    affected, _ := res.RowsAffected()
    if affected == 0 {
        http.Error(response, "Vous n'étiez pas inscrit à cet événement.", http.StatusNotFound)
        return
    }

    db.DB.Exec("UPDATE evenement SET nombre_place = nombre_place + 1 WHERE id_evenement = ?", idEvt)

    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Désinscription réussie !"})
}