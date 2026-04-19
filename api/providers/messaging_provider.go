package providers

import (
	"encoding/json"
	"fmt"
	"html"
	"net/http"
	"strings"

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

	_, errUpdate := db.DB.Exec("UPDATE MESSAGE_PRESTATAIRE SET est_lu = 1 WHERE id_utilisateur = ? AND id_prestataire = ? AND expediteur = 0 AND est_lu = 0", id2, id1)
	if errUpdate != nil {
		fmt.Printf("Erreur lors de la mise à jour de est_lu")
	}

	query := `SELECT id_message, contenu, date, id_utilisateur, id_prestataire, expediteur, est_lu, 
                     id_service, id_disponibilite, prix_propose, etat_offre
              FROM MESSAGE_PRESTATAIRE 
              WHERE (id_utilisateur = ? AND id_prestataire = ?) 
                 OR (id_utilisateur = ? AND id_prestataire = ?)`

    rows, err := db.DB.Query(query, id1, id2, id2, id1)
    if err != nil {
        http.Error(response, "Erreur SQL", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

	tabMessage := []models.Message{}
	for rows.Next() {
		var message models.Message
		var idU, idP int64
		var exp bool

		err := rows.Scan(
            &message.ID, &message.Contenu, &message.Date, &idU, &idP, &exp, &message.Est_Lu,
            &message.ID_Service, &message.ID_Dispo, &message.Prix_Propose, &message.Etat_Offre,
        )
		if err != nil { continue }

		if exp {
            message.ID_Expediteur = idP
            message.ID_Destinataire = idU
        } else {
            message.ID_Expediteur = idU
            message.ID_Destinataire = idP
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

	message.Contenu = strings.TrimSpace(message.Contenu)
	message.Contenu = html.EscapeString(message.Contenu)

	if message.Contenu == "" {
		http.Error(response, "Le message ne peut pas être vide.", http.StatusBadRequest)
		return
	}

	fmt.Printf("DEBUG: Exp=%d, Dest=%d, IsPresta=%v\n", 
               message.ID_Expediteur, message.ID_Destinataire, message.Expediteur)

	var idUser, idPresta int64
    if message.Expediteur { 
        idPresta = message.ID_Expediteur
        idUser = message.ID_Destinataire
    } else { 
        idUser = message.ID_Expediteur
        idPresta = message.ID_Destinataire
    }

	query := `INSERT INTO MESSAGE_PRESTATAIRE 
              (contenu, id_utilisateur, id_prestataire, expediteur, id_service, id_disponibilite, prix_propose, etat_offre) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)`

	res, errorCreate := db.DB.Exec(query, 
        message.Contenu, 
        idUser, 
        idPresta, 
        message.Expediteur, 
        message.ID_Service,
        message.ID_Dispo,
        message.Prix_Propose,
        message.Etat_Offre,
    )

	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	message.ID = id
	message.Est_Lu = false

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(message)
}

func Delete_Message(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	_, err := db.DB.Exec("DELETE FROM MESSAGE_PRESTATAIRE WHERE id_message = ?", id)
	if err != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusNoContent)
}

func Accept_Offer(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "PATCH") {
        return
    }

    idMessage := request.PathValue("id")

    query := `
        UPDATE MESSAGE_PRESTATAIRE 
        SET etat_offre = 'accepte' 
        WHERE id_message = ? AND etat_offre = 'en_attente'
    `
    res, err := db.DB.Exec(query, idMessage)

    if err != nil {
        http.Error(response, "Erreur lors de la validation de l'offre", http.StatusInternalServerError)
        return
    }

    rowsAffected, _ := res.RowsAffected()
    if rowsAffected == 0 {
        http.Error(response, "Offre introuvable ou déjà traitée", http.StatusNotFound)
        return
    }

    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Offre acceptée ! Le client peut maintenant payer."})
}

func Reject_Offer(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "PATCH") {
        return
    }

    idMessage := request.PathValue("id")

    query := `
        UPDATE MESSAGE_PRESTATAIRE 
        SET etat_offre = 'refuse' 
        WHERE id_message = ? AND etat_offre = 'en_attente'
    `
    res, err := db.DB.Exec(query, idMessage)

    if err != nil {
        http.Error(response, "Erreur lors du refus de l'offre", http.StatusInternalServerError)
        return
    }

    rowsAffected, _ := res.RowsAffected()
    if rowsAffected == 0 {
        http.Error(response, "Offre introuvable ou déjà traitée", http.StatusNotFound)
        return
    }

    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Offre refusée."})
}