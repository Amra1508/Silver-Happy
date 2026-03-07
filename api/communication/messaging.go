package communication

import (
	"encoding/json"
	"fmt"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"
)

func Get_Message(response http.ResponseWriter, request *http.Request) {
	
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id1 := request.PathValue("id1")
	id2 := request.PathValue("id2")

	rows, errorFetch := db.DB.Query("SELECT id_message, contenu, date, id_utilisateur1, id_utilisateur2 FROM MESSAGE_ADMIN WHERE (id_utilisateur1 = ? AND id_utilisateur2 = ?) OR (id_utilisateur1 = ? AND id_utilisateur2 = ?)", id1, id2, id2, id1)
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close() 

	tabMessage := []models.Message{}
	for rows.Next() {
		var message models.Message
		if err := rows.Scan(&message.ID, &message.Contenu, &message.Date, &message.ID_Expediteur, &message.ID_Destinataire); err != nil {
			fmt.Printf("ERREUR SCAN SUR MESSAGE ID %d: %v\n", message.ID, err)
			continue
		}
		tabMessage = append(tabMessage, message)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabMessage)
}

func Add_Message(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var message models.Message
	if err := json.NewDecoder(request.Body).Decode(&message); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO MESSAGE_ADMIN (contenu, id_utilisateur1, id_utilisateur2) VALUES (?, ?, ?)", message.Contenu, message.ID_Expediteur, message.ID_Destinataire)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	message.ID = id

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(message)
}

func Delete_Message(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	_, err := db.DB.Exec("DELETE FROM MESSAGE_ADMIN WHERE id_message = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusNoContent)
}
