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

func Read_Conseil(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    query := request.URL.Query()
    limitStr := query.Get("limit")
    pageStr := query.Get("page")
    userIdStr := query.Get("user_id")

    limit := 10
    offset := 0
    page := 1
    userId := 0

    if limitStr != "" {
        fmt.Sscanf(limitStr, "%d", &limit)
    }
    if pageStr != "" {
        fmt.Sscanf(pageStr, "%d", &page)
        offset = (page - 1) * limit
    }
    if userIdStr != "" {
        fmt.Sscanf(userIdStr, "%d", &userId)
    }

    var total int
    db.DB.QueryRow("SELECT COUNT(*) FROM CONSEIL").Scan(&total)

    sqlQuery := `
        SELECT 
            c.id_conseil, c.titre, c.description, c.date_publication, c.categorie,
            (SELECT COUNT(*) FROM LIKE_CONSEIL WHERE id_conseil = c.id_conseil) AS likes,
            (SELECT COUNT(*) > 0 FROM LIKE_CONSEIL WHERE id_conseil = c.id_conseil AND id_utilisateur = ?) AS is_liked
        FROM CONSEIL c 
        LIMIT ? OFFSET ?
    `

    rows, errorFetch := db.DB.Query(sqlQuery, userId, limit, offset)
    if errorFetch != nil {
        http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    var tabConseil []models.Conseil
    for rows.Next() {
        var conseil models.Conseil
        if err := rows.Scan(&conseil.ID, &conseil.Titre, &conseil.Description, &conseil.Date, &conseil.Categorie, &conseil.Likes, &conseil.IsLiked); err != nil {
            continue
        }
        tabConseil = append(tabConseil, conseil)
    }

    if tabConseil == nil {
        tabConseil = []models.Conseil{}
    }

    dataResponse := map[string]interface{}{
        "data":        tabConseil,
        "total":       total,
        "currentPage": page,
        "totalPages":  (total + limit - 1) / limit,
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(dataResponse)
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

	conseil.Titre = strings.TrimSpace(conseil.Titre)
	conseil.Description = strings.TrimSpace(conseil.Description)
	conseil.Categorie = strings.TrimSpace(conseil.Categorie)

	if conseil.Titre == "" || conseil.Description == "" || conseil.Categorie == "" {
		http.Error(response, "Les champs ne peuvent pas être vides.", http.StatusBadRequest)
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
    userIdStr := request.URL.Query().Get("user_id")
    userId := 0
    if userIdStr != "" {
        fmt.Sscanf(userIdStr, "%d", &userId)
    }

    var conseil models.Conseil

    sqlQuery := `
        SELECT 
            c.id_conseil, c.titre, c.description, c.date_publication, c.categorie,
            (SELECT COUNT(*) FROM LIKE_CONSEIL WHERE id_conseil = c.id_conseil) AS likes,
            (SELECT COUNT(*) > 0 FROM LIKE_CONSEIL WHERE id_conseil = c.id_conseil AND id_utilisateur = ?) AS is_liked
        FROM CONSEIL c 
        WHERE c.id_conseil = ?
    `

    err := db.DB.QueryRow(sqlQuery, userId, id).Scan(
        &conseil.ID, 
        &conseil.Titre, 
        &conseil.Description, 
        &conseil.Date, 
        &conseil.Categorie, 
        &conseil.Likes, 
        &conseil.IsLiked,
    )
    
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

	conseil.Titre = strings.TrimSpace(conseil.Titre)
	conseil.Description = strings.TrimSpace(conseil.Description)
	conseil.Categorie = strings.TrimSpace(conseil.Categorie)

	if conseil.Titre == "" || conseil.Description == "" || conseil.Categorie == "" {
		http.Error(response, "Les champs ne peuvent pas être vides.", http.StatusBadRequest)
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

func Like_Conseil(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") {
        return
    }

    idConseil := request.PathValue("id")

    var reqBody struct {
        IDUtilisateur int `json:"id_utilisateur"`
    }

    if err := json.NewDecoder(request.Body).Decode(&reqBody); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    if reqBody.IDUtilisateur == 0 {
        http.Error(response, "ID utilisateur manquant", http.StatusBadRequest)
        return
    }

    _, err := db.DB.Exec("INSERT INTO like_conseil (id_conseil, id_utilisateur) VALUES (?, ?)", idConseil, reqBody.IDUtilisateur)
    
    if err != nil {
        http.Error(response, "Erreur lors de l'ajout du like", http.StatusInternalServerError)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusCreated)
    json.NewEncoder(response).Encode(map[string]string{"message": "Conseil liké avec succès"})
}

func Unlike_Conseil(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "DELETE") {
        return
    }

    idConseil := request.PathValue("id")

    var reqBody struct {
        IDUtilisateur int `json:"id_utilisateur"`
    }

    if err := json.NewDecoder(request.Body).Decode(&reqBody); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    if reqBody.IDUtilisateur == 0 {
        http.Error(response, "ID utilisateur manquant", http.StatusBadRequest)
        return
    }

    res, err := db.DB.Exec("DELETE FROM like_conseil WHERE id_conseil = ? AND id_utilisateur = ?", idConseil, reqBody.IDUtilisateur)
    
    if err != nil {
        http.Error(response, "Erreur lors de la suppression du like", http.StatusInternalServerError)
        return
    }

    rowsAffected, _ := res.RowsAffected()
    if rowsAffected == 0 {
        http.Error(response, "Aucun like trouvé pour cet utilisateur sur ce conseil", http.StatusNotFound)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Like retiré avec succès"})
}