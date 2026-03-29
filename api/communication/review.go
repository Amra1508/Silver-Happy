package communication

import (
	"encoding/json"
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"main/db"
	"main/utils"
)

func Read_Avis(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	query := request.URL.Query()
	limitStr := query.Get("limit")
	pageStr := query.Get("page")
	user, _ := strconv.Atoi(query.Get("user"))

	limit := 10
	offset := 0
	page := 1

	if limitStr != "" { fmt.Sscanf(limitStr, "%d", &limit) }
	if pageStr != "" {
		fmt.Sscanf(pageStr, "%d", &page)
		offset = (page - 1) * limit
	}

	var total int
	db.DB.QueryRow("SELECT COUNT(*) FROM AVIS WHERE id_utilisateur != ?", user).Scan(&total)

	querySQL := `
		SELECT 
			a.id_avis, a.description, a.titre, a.note, a.date, a.categorie, a.id_prestataire,
			IFNULL(p.nom, '') as nom_presta, 
			IFNULL(p.prenom, '') as prenom_presta
		FROM AVIS a
		LEFT JOIN PRESTATAIRE p ON a.id_prestataire = p.id_prestataire
		WHERE a.id_utilisateur != ?
		ORDER BY a.date DESC
		LIMIT ? OFFSET ?`

	rows, errorFetch := db.DB.Query(querySQL, user, limit, offset)
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabAvis []map[string]interface{}
	for rows.Next() {
		var id, note int
		var desc, titre, date, cat, nom, prenom string
		var idPresta interface{}

		if err := rows.Scan(&id, &desc, &titre, &note, &date, &cat, &idPresta, &nom, &prenom); err != nil {
			continue 
		}

		tabAvis = append(tabAvis, map[string]interface{}{
			"id_avis":            id,
			"description":        desc,
			"titre":              titre,
			"note":               note,
			"date":               date,
			"categorie":          cat,
			"id_prestataire":     idPresta,
			"nom_prestataire":    nom,
			"prenom_prestataire": prenom,
		})
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
	if utils.HandleCORS(response, request, "GET") { return }

	id := request.PathValue("id")

	sqlQuery := `
		SELECT 
			a.id_avis, a.description, a.titre, a.note, a.date, a.categorie, a.id_prestataire,
			IFNULL(p.nom, '') as nom_presta, IFNULL(p.prenom, '') as prenom_presta
		FROM AVIS a
		LEFT JOIN PRESTATAIRE p ON a.id_prestataire = p.id_prestataire
		WHERE a.id_avis = ?`

	var idAvis, note int
	var desc, titre, date, cat, nom, prenom string
	var idPresta interface{}

	err := db.DB.QueryRow(sqlQuery, id).Scan(&idAvis, &desc, &titre, &note, &date, &cat, &idPresta, &nom, &prenom)
	if err != nil {
		http.Error(response, "Avis non trouvé", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]interface{}{
		"id_avis": idAvis, "description": desc, "titre": titre, "note": note,
		"date": date, "categorie": cat, "id_prestataire": idPresta,
		"nom_prestataire": nom, "prenom_prestataire": prenom,
	})
}

func Read_My_Avis(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	userID := request.PathValue("id")

	query := `
		SELECT a.id_avis, a.description, a.titre, a.note, a.date, a.categorie,
				IFNULL(p.nom, '') as nom_p, IFNULL(p.prenom, '') as prenom_p
		FROM AVIS a
		LEFT JOIN PRESTATAIRE p ON a.id_prestataire = p.id_prestataire
		WHERE a.id_utilisateur = ?
		ORDER BY a.date DESC`

	rows, err := db.DB.Query(query, userID)
	if err != nil {
		http.Error(response, "Erreur serveur", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var reviews []map[string]interface{}
	for rows.Next() {
		var id, note int
		var desc, titre, date, cat, nomP, prenomP string
		rows.Scan(&id, &desc, &titre, &note, &date, &cat, &nomP, &prenomP)
		reviews = append(reviews, map[string]interface{}{
			"id_avis": id, "titre": titre, "description": desc, "note": note,
			"date": date, "categorie": cat, "prestataire": prenomP + " " + nomP,
		})
	}
	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(reviews)
}

func Create_Avis(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") { return }

    var data map[string]interface{}
    if err := json.NewDecoder(request.Body).Decode(&data); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    titre := strings.TrimSpace(fmt.Sprintf("%v", data["titre"]))
    description := strings.TrimSpace(fmt.Sprintf("%v", data["description"]))
    categorie := strings.TrimSpace(fmt.Sprintf("%v", data["categorie"]))
    
    note, _ := data["note"].(float64)
    idUser, _ := data["id_utilisateur"].(float64)

    var idPresta interface{}
    if val, ok := data["id_prestataire"]; ok && val != nil {
        idPresta, _ = val.(float64)
    } else {
        idPresta = nil
    }

    _, err := db.DB.Exec("INSERT INTO AVIS (description, titre, note, categorie, id_prestataire, id_utilisateur) VALUES (?, ?, ?, ?, ?, ?)", 
        description, titre, int(note), categorie, idPresta, int(idUser))
    
    if err != nil {
        fmt.Println("Erreur SQL:", err)
        http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusCreated)
    json.NewEncoder(response).Encode(map[string]interface{}{"status": "success"})
}

func Update_Avis(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") { return }

	idAvis := request.PathValue("id")

	var data map[string]interface{}
	json.NewDecoder(request.Body).Decode(&data)

	query := "UPDATE AVIS SET titre=?, description=?, note=?, categorie=?, id_prestataire=? WHERE id_avis=? AND id_utilisateur=?"
	_, err := db.DB.Exec(query, data["titre"], data["description"], data["note"], data["categorie"], data["id_prestataire"], idAvis, data["id_utilisateur"])

	if err != nil {
		http.Error(response, "Erreur modification", http.StatusInternalServerError)
		return
	}
	json.NewEncoder(response).Encode(map[string]string{"message": "Avis mis à jour"})
}

func Dlete_Avis(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") { return }

	idAvis := request.PathValue("id")
	idUser := request.URL.Query().Get("user_id") 

	query := "DELETE FROM AVIS WHERE id_avis = ? AND id_utilisateur = ?"
	_, err := db.DB.Exec(query, idAvis, idUser)

	if err != nil {
		http.Error(response, "Erreur suppression", http.StatusInternalServerError)
		return
	}
	json.NewEncoder(response).Encode(map[string]string{"message": "Avis supprimé"})
}