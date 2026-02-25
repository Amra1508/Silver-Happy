package captcha

import (
	"encoding/json"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_Captcha(response http.ResponseWriter, request *http.Request) {
	
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	rows, errorFetch := db.DB.Query("SELECT id_captcha, question, reponse FROM CAPTCHA")
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close() 

	var tabCaptcha []models.Captcha
	for rows.Next() {
		var captcha models.Captcha
		if err := rows.Scan(&captcha.ID, &captcha.Question, &captcha.Reponse); err != nil {
			continue
		}
		tabCaptcha = append(tabCaptcha, captcha)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabCaptcha)
}

func Create_Captcha(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var captcha models.Captcha
	if err := json.NewDecoder(request.Body).Decode(&captcha); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO CAPTCHA (question, reponse) VALUES (?, ?)", captcha.Question, captcha.Reponse)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	captcha.ID = id

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(captcha)
}

func Read_One_Captcha(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var captcha models.Captcha

	err := db.DB.QueryRow("SELECT id_captcha, question, reponse FROM CAPTCHA WHERE id_captcha = ?", id).Scan(&captcha.ID, &captcha.Question, &captcha.Reponse)
	
	if err != nil {
		http.Error(response, "Captcha non trouvé", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(captcha)
}

func Delete_Captcha(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	_, err := db.DB.Exec("DELETE FROM CAPTCHA WHERE id_captcha = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusNoContent)
}

func Update_Captcha(response http.ResponseWriter, request *http.Request) {

    if utils.HandleCORS(response, request, "PUT") {
        return
    }

    id := request.PathValue("id")

    var captcha models.Captcha
    if err := json.NewDecoder(request.Body).Decode(&captcha); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    res, err := db.DB.Exec("UPDATE CAPTCHA SET question = ?, reponse = ? WHERE id_captcha = ?", captcha.Question, captcha.Reponse, id)
    
    if err != nil {
        http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
        return
    }

    rowsAffected, _ := res.RowsAffected()
    if rowsAffected == 0 {
        http.Error(response, "Aucun captcha trouvé avec cet ID", http.StatusNotFound)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Captcha mis à jour avec succès"})
}