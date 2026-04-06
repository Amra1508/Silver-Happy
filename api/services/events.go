package services

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"html"
	"io"
	"net/http"
	"os"
	"path/filepath"
	"strconv"
	"strings"
	"time"

	"main/db"
	"main/models"
	"main/utils"

	"github.com/stripe/stripe-go/v78"
	"github.com/stripe/stripe-go/v78/charge"
	"github.com/stripe/stripe-go/v78/checkout/session"
	"github.com/stripe/stripe-go/v78/invoice"
	"github.com/stripe/stripe-go/v78/paymentintent"
	"github.com/stripe/stripe-go/v78/refund"
)

const uploadDir = "./uploads"

func validateDates(debut, fin string) error {
	formats := []string{"2006-01-02T15:04", "2006-01-02T15:04:05", "2006-01-02 15:04:05"}
	var tDebut, tFin time.Time
	var err error

	for _, f := range formats {
		tDebut, err = time.Parse(f, debut)
		if err == nil {
			break
		}
	}
	if err != nil {
		return fmt.Errorf("Format de date de début invalide")
	}

	for _, f := range formats {
		tFin, err = time.Parse(f, fin)
		if err == nil {
			break
		}
	}
	if err != nil {
		return fmt.Errorf("Format de date de fin invalide")
	}

	if tDebut.Before(time.Now()) {
		return fmt.Errorf("La date de début ne peut pas être dans le passé")
	}
	if tFin.Before(tDebut) {
		return fmt.Errorf("La date de fin ne peut pas être avant la date de début")
	}

	return nil
}

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

    sqlQuery := `
        SELECT id_evenement, nom, description, lieu, nombre_place, image, date_debut, date_fin, id_categorie, prix 
        FROM evenement 
        ORDER BY 
            (date_fin_boost IS NOT NULL AND date_fin_boost > NOW()) DESC,
            date_debut ASC 
        LIMIT ? OFFSET ?
    `

    rows, errorFetch := db.DB.Query(sqlQuery, limit, offset)
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

        if err := rows.Scan(&evt.ID, &evt.Nom, &evt.Description, &evt.Lieu, &evt.NombrePlace, &imagePath, &dateDebut, &dateFin, &evt.IDCategorie, &evt.Prix); err != nil {
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

	nom := html.EscapeString(strings.TrimSpace(request.FormValue("nom")))
	desc := html.EscapeString(strings.TrimSpace(request.FormValue("description")))
	lieu := html.EscapeString(strings.TrimSpace(request.FormValue("lieu")))
	places := strings.TrimSpace(request.FormValue("nombre_place"))
	debut := strings.TrimSpace(request.FormValue("date_debut"))
	fin := strings.TrimSpace(request.FormValue("date_fin"))
	catStr := strings.TrimSpace(request.FormValue("id_categorie"))
	prixStr := strings.TrimSpace(request.FormValue("prix"))

	if nom == "" || desc == "" || lieu == "" {
		http.Error(response, "Les champs ne peuvent pas être vides.", http.StatusBadRequest)
		return
	}

	var prix float64
	if prixStr != "" {
		prix, _ = strconv.ParseFloat(prixStr, 64)
	}

	if errDate := validateDates(debut, fin); errDate != nil {
		http.Error(response, errDate.Error(), http.StatusBadRequest)
		return
	}

	var idCategorie *int
	if catStr != "" && catStr != "null" {
		id, err := strconv.Atoi(catStr)
		if err == nil {
			idCategorie = &id
		}
	}

	file, handler, errFile := request.FormFile("image")
	var imagePath string

	if errFile == nil {
		defer file.Close()
		os.MkdirAll(uploadDir, os.ModePerm)
		fileName := fmt.Sprintf("%d_%s", time.Now().Unix(), handler.Filename)
		imagePath = filepath.Join(uploadDir, fileName)

		dst, _ := os.Create(imagePath)
		defer dst.Close()
		io.Copy(dst, file)
	}

	res, errorCreate := db.DB.Exec(
		"INSERT INTO evenement (nom, description, lieu, nombre_place, image, date_debut, date_fin, id_categorie, prix) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
		nom, desc, lieu, places, imagePath, debut, fin, idCategorie, prix,
	)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(map[string]interface{}{"id": id, "status": "success", "message": "Événement créé"})
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
		"SELECT id_evenement, nom, description, lieu, nombre_place, image, date_debut, date_fin, id_categorie, prix FROM evenement WHERE id_evenement = ?",
		id,
	).Scan(&evt.ID, &evt.Nom, &evt.Description, &evt.Lieu, &evt.NombrePlace, &imagePath, &dateDebut, &dateFin, &evt.IDCategorie, &evt.Prix)

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

func Update_Evenement(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "PUT") {
        return
    }

    id := request.PathValue("id")

    err := request.ParseMultipartForm(10 << 20)
    if err != nil {
        http.Error(response, "Erreur de formulaire", http.StatusBadRequest)
        return
    }

    nom := html.EscapeString(strings.TrimSpace(request.FormValue("nom")))
    desc := html.EscapeString(strings.TrimSpace(request.FormValue("description")))
    lieu := html.EscapeString(strings.TrimSpace(request.FormValue("lieu")))
    placesStr := strings.TrimSpace(request.FormValue("nombre_place"))
    debut := strings.TrimSpace(request.FormValue("date_debut"))
    fin := strings.TrimSpace(request.FormValue("date_fin"))
    catStr := strings.TrimSpace(request.FormValue("id_categorie"))
    prixStr := strings.TrimSpace(request.FormValue("prix"))

    if nom == "" || desc == "" || lieu == "" {
        http.Error(response, "Champs requis manquants", http.StatusBadRequest)
        return
    }

    places, _ := strconv.Atoi(placesStr)

    var prix float64
    if prixStr != "" {
        prix, _ = strconv.ParseFloat(prixStr, 64)
    }

    if errDate := validateDates(debut, fin); errDate != nil {
        http.Error(response, errDate.Error(), http.StatusBadRequest)
        return
    }

    var dateFinPtr *string
    if fin != "" {
        dateFinPtr = &fin
    }

    var idCategorie *int
    if catStr != "" && catStr != "null" {
        idCat, err := strconv.Atoi(catStr)
        if err == nil {
            idCategorie = &idCat
        }
    }

    file, handler, errFile := request.FormFile("image")
    var imagePath string

    if errFile == nil {
        defer file.Close()
        os.MkdirAll(uploadDir, os.ModePerm)
        
        cleanFileName := filepath.Base(handler.Filename)
        fileName := fmt.Sprintf("%d_%s", time.Now().Unix(), cleanFileName)
        imagePath = filepath.ToSlash(filepath.Join(uploadDir, fileName))
        
        dst, errCreate := os.Create(imagePath)
        if errCreate != nil {
            fmt.Println("Erreur création fichier image :", errCreate)
            http.Error(response, "Erreur lors de la sauvegarde de l'image", http.StatusInternalServerError)
            return
        }
        defer dst.Close()
        
        _, errCopy := io.Copy(dst, file)
        if errCopy != nil {
            fmt.Println("Erreur copie fichier image :", errCopy)
            http.Error(response, "Erreur lors de la copie de l'image", http.StatusInternalServerError)
            return
        }
    } else if errFile != http.ErrMissingFile {
        fmt.Println("Erreur FormFile :", errFile)
        http.Error(response, "Fichier invalide", http.StatusBadRequest)
        return
    }

    var errDb error

    if imagePath != "" {
        var oldImage sql.NullString
        db.DB.QueryRow("SELECT image FROM evenement WHERE id_evenement = ?", id).Scan(&oldImage)
        if oldImage.Valid && oldImage.String != "" {
            os.Remove(oldImage.String)
        }

        _, errDb = db.DB.Exec(
            "UPDATE evenement SET nom = ?, description = ?, lieu = ?, nombre_place = ?, image = ?, date_debut = ?, date_fin = ?, id_categorie = ?, prix = ? WHERE id_evenement = ?",
            nom, desc, lieu, places, imagePath, debut, dateFinPtr, idCategorie, prix, id, 
        )
    } else {
        _, errDb = db.DB.Exec(
            "UPDATE evenement SET nom = ?, description = ?, lieu = ?, nombre_place = ?, date_debut = ?, date_fin = ?, id_categorie = ?, prix = ? WHERE id_evenement = ?",
            nom, desc, lieu, places, debut, dateFinPtr, idCategorie, prix, id,
        )
    }

    if errDb != nil {
        fmt.Println("Erreur UPDATE MySQL :", errDb)
        http.Error(response, "Erreur serveur lors de la mise à jour", http.StatusInternalServerError)
        return
    }

    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Mise à jour réussie"})
}

func Delete_Evenement(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "DELETE") {
        return
    }
    
    id := request.PathValue("id")
    
    var imagePath sql.NullString
    err := db.DB.QueryRow("SELECT image FROM evenement WHERE id_evenement = ?", id).Scan(&imagePath)
    if err != nil && err != sql.ErrNoRows {
        fmt.Println("Erreur SELECT image :", err)
        http.Error(response, "Erreur lors de la lecture de l'évènement", http.StatusInternalServerError)
        return
    }

    _, err = db.DB.Exec("DELETE FROM PRESTATAIRE_EVENEMENT WHERE id_evenement = ?", id)
    if err != nil {
        fmt.Println("Erreur DELETE PRESTATAIRE_EVENEMENT :", err)
        http.Error(response, "Erreur lors de la suppression des liens prestataires", http.StatusInternalServerError)
        return
    }

    _, err = db.DB.Exec("DELETE FROM inscription WHERE id_evenement = ?", id)
    if err != nil {
        fmt.Println("Erreur DELETE inscription :", err)
        http.Error(response, "Erreur lors de la suppression des inscriptions", http.StatusInternalServerError)
        return
    }

    _, err = db.DB.Exec("DELETE FROM evenement WHERE id_evenement = ?", id)
    if err != nil {
        fmt.Println("Erreur DELETE evenement :", err)
        http.Error(response, "Erreur lors de la suppression de l'évènement", http.StatusInternalServerError)
        return
    }

    if imagePath.Valid && imagePath.String != "" {
        os.Remove(imagePath.String)
    }

    response.WriteHeader(http.StatusNoContent)
}

func Read_User_Evenements(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}
	idUser := request.PathValue("id")
	query := `
		SELECT e.id_evenement, e.nom, e.description, e.lieu, e.image, e.date_debut, e.date_fin, e.id_categorie, e.prix
		FROM evenement e
		JOIN INSCRIPTION i ON e.id_evenement = i.id_evenement
		WHERE i.id_utilisateur = ? ORDER BY e.date_debut ASC`
	
	rows, _ := db.DB.Query(query, idUser)
	defer rows.Close()

	var tabEvenement []models.Evenement
	for rows.Next() {
		var evt models.Evenement
		var imagePath, dateDebut, dateFin sql.NullString
		if err := rows.Scan(&evt.ID, &evt.Nom, &evt.Description, &evt.Lieu, &imagePath, &dateDebut, &dateFin, &evt.IDCategorie, &evt.Prix); err == nil {
			if imagePath.Valid { evt.Image = imagePath.String }
			if dateDebut.Valid { evt.DateDebut = dateDebut.String }
			if dateFin.Valid { evt.DateFin = dateFin.String }
			tabEvenement = append(tabEvenement, evt)
		}
	}
	if tabEvenement == nil { tabEvenement = []models.Evenement{} }
	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabEvenement)
}

func GetEvenementsByCategory(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	categorieIDStr := request.URL.Query().Get("categorie")
	query := `SELECT id_evenement, nom, description, lieu, nombre_place, image, date_debut, date_fin, id_categorie, prix FROM evenement WHERE id_categorie = ?`
	
	rows, err := db.DB.Query(query, categorieIDStr)
	if err != nil {
		http.Error(response, "Erreur", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var evts []models.Evenement
	for rows.Next() {
		var evt models.Evenement
		var imagePath, dateDebut, dateFin sql.NullString
		if err := rows.Scan(&evt.ID, &evt.Nom, &evt.Description, &evt.Lieu, &evt.NombrePlace, &imagePath, &dateDebut, &dateFin, &evt.IDCategorie, &evt.Prix); err == nil {
			if imagePath.Valid { evt.Image = imagePath.String }
			if dateDebut.Valid { evt.DateDebut = dateDebut.String }
			if dateFin.Valid { evt.DateFin = dateFin.String }
			evts = append(evts, evt)
		}
	}
	if evts == nil { evts = []models.Evenement{} }
	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(evts)
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
    db.DB.QueryRow("SELECT COUNT(*) FROM INSCRIPTION WHERE id_utilisateur = ? AND id_evenement = ?", idUser, idEvt).Scan(&count)
    if count > 0 {
        http.Error(response, "Vous êtes déjà inscrit à cet événement.", http.StatusConflict)
        return
    }

    var dateCible string
    errDate := db.DB.QueryRow("SELECT date_debut FROM evenement WHERE id_evenement = ?", idEvt).Scan(&dateCible)
    if errDate != nil {
        http.Error(response, "Événement introuvable", http.StatusNotFound)
        return
    }

    var conflictCount int
    queryConflict := `
        SELECT COUNT(*) 
        FROM INSCRIPTION i
        JOIN evenement e ON i.id_evenement = e.id_evenement
        WHERE i.id_utilisateur = ? 
        AND DATE_FORMAT(e.date_debut, '%Y-%m-%d %H:%i') = DATE_FORMAT(?, '%Y-%m-%d %H:%i')`

    db.DB.QueryRow(queryConflict, idUser, dateCible).Scan(&conflictCount)

    if conflictCount > 0 {
        http.Error(response, "Conflit d'horaire : vous avez déjà une activité à cette heure-là.", http.StatusConflict)
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

func Unregister_Evenement(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") { return }

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

	var idPaiement sql.NullInt64
	var stripePI sql.NullString

	errSearch := db.DB.QueryRow(`
		SELECT i.id_paiement, p.stripe_pi 
		FROM INSCRIPTION i
		LEFT JOIN PAIEMENT p ON i.id_paiement = p.id_paiement
		WHERE i.id_utilisateur = ? AND i.id_evenement = ?
	`, idUser, idEvt).Scan(&idPaiement, &stripePI)

	if errSearch != nil {
		http.Error(response, "Vous n'étiez pas inscrit à cet événement.", http.StatusNotFound)
		return
	}

	if stripePI.Valid && stripePI.String != "" {
		stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
		params := &stripe.RefundParams{
			PaymentIntent: stripe.String(stripePI.String),
		}
		
		_, errRefund := refund.New(params)
		if errRefund != nil {
			fmt.Println("Erreur remboursement Stripe :", errRefund)
			http.Error(response, "Erreur lors du remboursement Stripe. Veuillez contacter le support.", http.StatusInternalServerError)
			return
		}

		if idPaiement.Valid {
			db.DB.Exec("UPDATE PAIEMENT SET statut = 'remboursé' WHERE id_paiement = ?", idPaiement.Int64)
		}
	}

	res, errDel := db.DB.Exec("DELETE FROM INSCRIPTION WHERE id_utilisateur = ? AND id_evenement = ?", idUser, idEvt)
	if errDel != nil {
		http.Error(response, "Erreur lors de la désinscription en BDD.", http.StatusInternalServerError)
		return
	}

	affected, _ := res.RowsAffected()
	if affected > 0 {
		db.DB.Exec("UPDATE evenement SET nombre_place = nombre_place + 1 WHERE id_evenement = ?", idEvt)
	}

	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Désinscription et remboursement réussis !"})
}

func CreateEventCheckoutSession(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") { return }

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
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
    db.DB.QueryRow("SELECT COUNT(*) FROM INSCRIPTION WHERE id_utilisateur = ? AND id_evenement = ?", idUser, idEvt).Scan(&count)
    if count > 0 {
        http.Error(response, "Vous êtes déjà inscrit à cet événement.", http.StatusConflict)
        return
    }

    var dateCible string
    errDate := db.DB.QueryRow("SELECT date_debut FROM evenement WHERE id_evenement = ?", idEvt).Scan(&dateCible)
    if errDate != nil {
        http.Error(response, "Événement introuvable", http.StatusNotFound)
        return
    }

    var conflictCount int
    queryConflict := `
        SELECT COUNT(*) 
        FROM INSCRIPTION i
        JOIN evenement e ON i.id_evenement = e.id_evenement
        WHERE i.id_utilisateur = ? 
        AND DATE_FORMAT(e.date_debut, '%Y-%m-%d %H:%i') = DATE_FORMAT(?, '%Y-%m-%d %H:%i')`

    db.DB.QueryRow(queryConflict, idUser, dateCible).Scan(&conflictCount)

    if conflictCount > 0 {
        http.Error(response, "Conflit d'horaire : vous avez déjà une activité à cette heure-là.", http.StatusConflict)
        return
    }

	var nomEvt string
	var places int
	var prix float64
	
	err := db.DB.QueryRow("SELECT nom, nombre_place, prix FROM evenement WHERE id_evenement = ?", idEvt).Scan(&nomEvt, &places, &prix)
	if err != nil || places <= 0 {
		http.Error(response, "Événement introuvable ou complet.", http.StatusForbidden)
		return
	}

	if prix <= 0 {
		_, errInsert := db.DB.Exec("INSERT INTO INSCRIPTION (id_utilisateur, id_evenement) VALUES (?, ?)", idUser, idEvt)
		if errInsert == nil {
			db.DB.Exec("UPDATE evenement SET nombre_place = nombre_place - 1 WHERE id_evenement = ?", idEvt)
		}
		json.NewEncoder(response).Encode(map[string]interface{}{"isFree": true, "message": "Inscription gratuite réussie"})
		return
	}

	params := &stripe.CheckoutSessionParams{
		PaymentMethodTypes: stripe.StringSlice([]string{"card"}),
		Mode:               stripe.String(string(stripe.CheckoutSessionModePayment)),
		ClientReferenceID:  stripe.String(strconv.Itoa(idUser)),
		LineItems: []*stripe.CheckoutSessionLineItemParams{
			{
				PriceData: &stripe.CheckoutSessionLineItemPriceDataParams{
					Currency: stripe.String("eur"),
					ProductData: &stripe.CheckoutSessionLineItemPriceDataProductDataParams{
						Name: stripe.String("Inscription : " + nomEvt),
					},
					UnitAmount: stripe.Int64(int64(prix * 100)),
				},
				Quantity: stripe.Int64(1),
			},
		},
		SuccessURL: stripe.String(fmt.Sprintf("http://localhost:8082/success-event?session_id={CHECKOUT_SESSION_ID}&event_id=%s&user_id=%d", idEvt, idUser)),
		CancelURL:  stripe.String("http://localhost/front/services/menu_activity.php"),
	}

	s, err := session.New(params)
	if err != nil {
		http.Error(response, "Erreur Stripe", http.StatusInternalServerError)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]interface{}{"isFree": false, "url": s.URL})
}

func Success_Event_Payment(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")
	sessionID := request.URL.Query().Get("session_id")
	eventID := request.URL.Query().Get("event_id")
	userID := request.URL.Query().Get("user_id")

	s, err := session.Get(sessionID, nil)
	if err != nil || s.PaymentStatus != stripe.CheckoutSessionPaymentStatusPaid {
		http.Redirect(response, request, "http://localhost/front/services/menu_activity.php?error=paiement_echoue", http.StatusSeeOther)
		return
	}

	var urlFacture string
	var stripePI string

	if s.PaymentIntent != nil {
		stripePI = s.PaymentIntent.ID
		pi, errPI := paymentintent.Get(s.PaymentIntent.ID, nil)
		if errPI == nil && pi.LatestCharge != nil {
			ch, errC := charge.Get(pi.LatestCharge.ID, nil)
			if errC == nil { urlFacture = ch.ReceiptURL }
		}
	} else if s.Invoice != nil {
		inv, errI := invoice.Get(s.Invoice.ID, nil)
		if errI == nil { urlFacture = inv.HostedInvoiceURL }
	}

	prixPaye := float64(s.AmountTotal) / 100.0
	resPaiement, errP := db.DB.Exec(
		"INSERT INTO PAIEMENT (prix, statut, mode_paiement, url_facture, stripe_pi) VALUES (?, 'valide', 'carte', ?, ?)", 
		prixPaye, urlFacture, stripePI,
	)
	
	idPaiement, _ := resPaiement.LastInsertId()

	if errP == nil {
		res, errInsert := db.DB.Exec("INSERT IGNORE INTO INSCRIPTION (id_utilisateur, id_evenement, id_paiement) VALUES (?, ?, ?)", userID, eventID, idPaiement)
		if errInsert == nil {
			affected, _ := res.RowsAffected()
			if affected > 0 {
				db.DB.Exec("UPDATE evenement SET nombre_place = nombre_place - 1 WHERE id_evenement = ?", eventID)
			}
		}
	}

	http.Redirect(response, request, "http://localhost/front/services/events.php?success=inscription_validee", http.StatusSeeOther)
}