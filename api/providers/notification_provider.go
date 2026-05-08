package providers

import (
	"bytes"
	"database/sql"
	"encoding/json"
	"fmt"
	"log"
	"main/models"
	"main/utils"
	"net/http"
	"os"
	"time"
)

func StartNotificationCron(db *sql.DB) {
	ticker := time.NewTicker(1 * time.Minute)
	
	go func() {
		for {
			<-ticker.C
			processUpcomingEvents(db) 
			notifySeniorsUpcomingEvents(db)
			notifyProvidersUpcomingDisponibilites(db)
		}
	}()
	log.Println("Tâche Cron des notifications OneSignal démarrée (1 minute).")
}

func SyncOneSignal(db *sql.DB) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		if utils.HandleCORS(w, r, "POST") {
			return
		}

		var req models.SyncOneSignalRequest
		if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
			http.Error(w, "Requête invalide", http.StatusBadRequest)
			return
		}

		if req.OneSignalID == "" {
			w.WriteHeader(http.StatusOK)
			return
		}

		var query string
		if req.TypeUtilisateur == "prestataire" {
			query = "UPDATE PRESTATAIRE SET onesignal_player_id = ? WHERE id_prestataire = ?"
		} else {
			query = "UPDATE UTILISATEUR SET onesignal_player_id = ? WHERE id_utilisateur = ?"
		}

		_, err := db.Exec(query, req.OneSignalID, req.IDUtilisateur)
		if err != nil {
			http.Error(w, "Erreur lors de la mise à jour en BDD", http.StatusInternalServerError)
			return
		}

		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(map[string]string{"status": "success", "message": "OneSignal ID mis à jour"})
	}
}

func notifyProvidersUpcomingDisponibilites(db *sql.DB) {
	query := `
		SELECT d.id_disponibilite, d.date_heure_debut, p.onesignal_player_id
		FROM DISPONIBILITE d
		JOIN PRESTATAIRE p ON d.id_prestataire = p.id_prestataire
		WHERE d.date_heure_debut BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR)
		AND d.notif_rappel_envoyee = 0
		AND p.onesignal_player_id IS NOT NULL AND p.onesignal_player_id != ''
	`

	rows, err := db.Query(query)
	if err != nil {
		log.Println("Erreur lors de la récupération des disponibilités imminentes:", err)
		return
	}
	defer rows.Close()

	for rows.Next() {
		var idDispo int
		var dateHeure string
		var playerID string

		if err := rows.Scan(&idDispo, &dateHeure, &playerID); err != nil {
			log.Println("Erreur lecture ligne disponibilité:", err)
			continue
		}

		titre := "Rappel : Prestation imminente !"
		message := fmt.Sprintf("Vous avez une prestation réservée qui commence bientôt (%s).", dateHeure)
		data := map[string]interface{}{"type": "RAPPEL_DISPO", "reference_id": idDispo}

		err := SendPushNotification([]string{playerID}, titre, message, data)
		if err != nil {
			log.Println("Erreur envoi notification OneSignal (Dispo Prestataire):", err)
		} else {
			saveNotificationToDB(db, playerID, message, "RAPPEL_DISPO", idDispo)
			db.Exec("UPDATE DISPONIBILITE SET notif_rappel_envoyee = 1 WHERE id_disponibilite = ?", idDispo)
		}
	}
}

func processUpcomingEvents(db *sql.DB) {
	query := `
		SELECT e.id_evenement, e.nom, p.onesignal_player_id 
		FROM EVENEMENT e
		JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
		JOIN PRESTATAIRE p ON pe.id_prestataire = p.id_prestataire
		WHERE e.date_debut BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR)
		AND e.notif_rappel_envoyee = 0
		AND p.onesignal_player_id IS NOT NULL AND p.onesignal_player_id != ''
	`

	rows, err := db.Query(query)
	if err != nil {
		log.Println("Erreur lors de la récupération des événements imminents:", err)
		return
	}
	defer rows.Close()

	for rows.Next() {
		var idEvent int
		var nomEvent string
		var playerID string

		if err := rows.Scan(&idEvent, &nomEvent, &playerID); err != nil {
			log.Println("Erreur lecture ligne événement:", err)
			continue
		}

		titre := "Un événement approche !"
		message := "Votre événement '" + nomEvent + "' commence bientôt."
		data := map[string]interface{}{"type": "RAPPEL_EVENT", "reference_id": idEvent}
		
		err := SendPushNotification([]string{playerID}, titre, message, data)
		if err != nil {
			log.Println("Erreur envoi notification OneSignal:", err)
		} else {
			saveNotificationToDB(db, playerID, message, "RAPPEL_EVENT", idEvent)
			db.Exec("UPDATE EVENEMENT SET notif_rappel_envoyee = 1 WHERE id_evenement = ?", idEvent)
		}
	}
}

func notifySeniorsUpcomingEvents(db *sql.DB) {
	query := `
		SELECT e.id_evenement, e.nom, u.onesignal_player_id 
		FROM EVENEMENT e
		JOIN INSCRIPTION i ON e.id_evenement = i.id_evenement
		JOIN UTILISATEUR u ON i.id_utilisateur = u.id_utilisateur
		WHERE e.date_debut BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR)
		AND e.notif_rappel_envoyee = 0
		AND u.onesignal_player_id IS NOT NULL AND u.onesignal_player_id != ''
	`

	rows, err := db.Query(query)
	if err != nil {
		log.Println("Erreur lors de la récupération des seniors pour les événements imminents:", err)
		return
	}
	defer rows.Close()

	for rows.Next() {
		var idEvent int
		var nomEvent string
		var playerID string

		if err := rows.Scan(&idEvent, &nomEvent, &playerID); err != nil {
			log.Println("Erreur lecture ligne senior/événement:", err)
			continue
		}

		titre := "Rappel : Votre événement approche !"
		message := "L'événement '" + nomEvent + "' auquel vous êtes inscrit va bientôt commencer."
		data := map[string]interface{}{
			"type":         "RAPPEL_EVENT_SENIOR",
			"reference_id": idEvent,
		}

		err := SendPushNotification([]string{playerID}, titre, message, data)
		if err != nil {
			log.Println("Erreur envoi notification OneSignal (Senior):", err)
		} else {
			saveNotificationToDB(db, playerID, message, "RAPPEL_EVENT_SENIOR", idEvent)
		}
	}
}

func saveNotificationToDB(db *sql.DB, destinataire, contenu, typeNotif string, refID int) {
	query := `INSERT INTO NOTIFICATION (destinataire, contenu, statut, type_notification, reference_id) 
			  VALUES (?, ?, 'envoye', ?, ?)`
	_, err := db.Exec(query, destinataire, contenu, typeNotif, refID)
	if err != nil {
		log.Println("Erreur sauvegarde notification en BDD:", err)
	}
}

func SendPushNotification(playerIDs []string, title string, message string, customData map[string]interface{}) error {
	appID := os.Getenv("ONESIGNAL_APP_ID")
	apiKey := os.Getenv("ONESIGNAL_REST_API_KEY")

	if appID == "" || apiKey == "" {
		return fmt.Errorf("les clés OneSignal sont introuvables dans l'environnement")
	}

	if len(playerIDs) == 0 {
		return fmt.Errorf("aucun destinataire (playerID) fourni")
	}

	payload := models.OneSignalPayload{
		AppID:            appID,
		IncludePlayerIDs: playerIDs,
		Headings:         map[string]string{"en": title, "fr": title},
		Contents:         map[string]string{"en": message, "fr": message},
		Data:             customData,
	}

	jsonData, err := json.Marshal(payload)
	if err != nil {
		return err
	}

	req, err := http.NewRequest("POST", "https://onesignal.com/api/v1/notifications", bytes.NewBuffer(jsonData))
	if err != nil {
		return err
	}

	req.Header.Set("Content-Type", "application/json; charset=utf-8")
	req.Header.Set("Authorization", "Basic "+apiKey)

	client := &http.Client{}
	resp, err := client.Do(req)
	if err != nil {
		return err
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return fmt.Errorf("erreur OneSignal, statut HTTP: %d", resp.StatusCode)
	}

	return nil
}