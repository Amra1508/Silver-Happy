package providers

import (
	"encoding/json"
	"fmt"
	"io"
	"main/auth"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"
	"os"
	"path/filepath"
	"strconv"
	"time"

	"github.com/golang-jwt/jwt/v5"
)

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

func Create_Prestataire_Evenement(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") {
        return
    }

    cookie, err := request.Cookie("provider_token")
    if err != nil {
        http.Error(response, "Non authentifié", http.StatusUnauthorized)
        return
    }

    tokenString := cookie.Value
    claims := &models.Claims{}
    token, err := jwt.ParseWithClaims(tokenString, claims, func(token *jwt.Token) (interface{}, error) {
        return auth.JwtKey, nil
    })

    if err != nil || !token.Valid {
        http.Error(response, "Session invalide", http.StatusUnauthorized)
        return
    }

    providerID := claims.UserID

    err = request.ParseMultipartForm(10 << 20) 
    if err != nil {
        http.Error(response, "Erreur lors de la lecture du formulaire", http.StatusBadRequest)
        return
    }

    nom := request.FormValue("nom")
    description := request.FormValue("description")
    lieu := request.FormValue("lieu")
    dateDebut := request.FormValue("date_debut")
    dateFin := request.FormValue("date_fin")
    
    nombrePlace, _ := strconv.Atoi(request.FormValue("nombre_place"))
    prix, _ := strconv.ParseFloat(request.FormValue("prix"), 64)

    if nom == "" || description == "" || lieu == "" || dateDebut == "" {
        http.Error(response, "Veuillez remplir tous les champs obligatoires", http.StatusBadRequest)
        return
    }

    if errDate := validateDates(dateDebut, dateFin); errDate != nil {
		http.Error(response, errDate.Error(), http.StatusBadRequest)
		return
	}

    var overlapCount int
    overlapQuery := `
        SELECT COALESCE(SUM(conflits), 0) FROM (
	            SELECT COUNT(*) as conflits 
            FROM EVENEMENT e
            INNER JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
            WHERE pe.id_prestataire = ?
              AND e.date_debut < IFNULL(NULLIF(?, ''), DATE_ADD(?, INTERVAL 1 HOUR))
              AND IFNULL(NULLIF(e.date_fin, ''), DATE_ADD(e.date_debut, INTERVAL 1 HOUR)) > ?

            UNION ALL

            SELECT COUNT(*) as conflits
            FROM RESERVATION_SERVICE rs
            INNER JOIN SERVICE s ON rs.id_service = s.id_service
            WHERE s.id_prestataire = ?
              AND rs.date_heure < IFNULL(NULLIF(?, ''), DATE_ADD(?, INTERVAL 1 HOUR))
              AND DATE_ADD(rs.date_heure, INTERVAL 1 HOUR) > ?
        ) as total_conflits
    `
    
    err = db.DB.QueryRow(overlapQuery, 
        providerID, dateFin, dateDebut, dateDebut,
        providerID, dateFin, dateDebut, dateDebut,
    ).Scan(&overlapCount)
    
    if err != nil {
        fmt.Println("Erreur lors de la vérification des conflits horaires:", err)
        http.Error(response, "Erreur lors de la vérification du planning", http.StatusInternalServerError)
        return
    }

    if overlapCount > 0 {
        http.Error(response, "Conflit d'horaire : Vous avez déjà un événement ou un service prévu sur ce créneau.", http.StatusConflict)
        return
    }
    
    var imagePath string
    file, handler, err := request.FormFile("image")
    if err == nil { 
        defer file.Close()

        uploadDir := "./uploads"
        if err := os.MkdirAll(uploadDir, os.ModePerm); err != nil {
            http.Error(response, "Erreur de configuration du serveur pour l'image", http.StatusInternalServerError)
            return
        }

        filename := fmt.Sprintf("%d_%s", time.Now().Unix(), handler.Filename)
        filepathLocal := filepath.Join(uploadDir, filename)

        dst, err := os.Create(filepathLocal)
        if err != nil {
            http.Error(response, "Erreur lors de la sauvegarde de l'image", http.StatusInternalServerError)
            return
        }
        defer dst.Close()

        if _, err := io.Copy(dst, file); err != nil {
            http.Error(response, "Erreur lors de l'écriture de l'image", http.StatusInternalServerError)
            return
        }

        imagePath = filepath.ToSlash("/uploads/" + filename) 
    } else if err != http.ErrMissingFile {
        http.Error(response, "Erreur avec le fichier image", http.StatusBadRequest)
        return
    } 

    tx, err := db.DB.Begin()
    if err != nil {
        http.Error(response, "Erreur serveur", http.StatusInternalServerError)
        return
    }

    var categorieID int
    queryCat := `SELECT id_categorie FROM prestataire WHERE id_prestataire = ?` 
    err = tx.QueryRow(queryCat, providerID).Scan(&categorieID)
    if err != nil {
        tx.Rollback()
        http.Error(response, "Erreur : Impossible de récupérer la catégorie du prestataire", http.StatusInternalServerError)
        return
    }

    queryInsert := `INSERT INTO EVENEMENT (nom, description, lieu, nombre_place, prix, date_debut, date_fin, id_categorie, image) 
                    VALUES (?, ?, ?, ?, ?, ?, NULLIF(?, ''), ?, ?)`

    res, err := tx.Exec(queryInsert, nom, description, lieu, nombrePlace, prix, dateDebut, dateFin, categorieID, imagePath)
    if err != nil {
        tx.Rollback()
        fmt.Println("Erreur INSERT Evenement:", err)
        http.Error(response, "Erreur lors de la création de l'évènement", http.StatusInternalServerError)
        return
    }

    eventID, _ := res.LastInsertId()

    queryLink := `INSERT INTO PRESTATAIRE_EVENEMENT (id_prestataire, id_evenement) VALUES (?, ?)`
    _, err = tx.Exec(queryLink, providerID, eventID)
    if err != nil {
        tx.Rollback()
        http.Error(response, "Erreur lors de la liaison de l'évènement au prestataire", http.StatusInternalServerError)
        return
    }

    if err := tx.Commit(); err != nil {
        http.Error(response, "Erreur lors de la validation", http.StatusInternalServerError)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusCreated)
    json.NewEncoder(response).Encode(map[string]interface{}{
        "id":        eventID,
        "image_url": imagePath,
        "message":   "Événement créé avec succès",
    })
}

func Get_Event_Participants(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	eventID := request.PathValue("id")

	query := `
		SELECT u.id_utilisateur, u.nom, u.prenom, u.email
		FROM UTILISATEUR u
		JOIN INSCRIPTION i ON u.id_utilisateur = i.id_utilisateur
		WHERE i.id_evenement = ?
	`
	
	rows, err := db.DB.Query(query, eventID)
	if err != nil {
		http.Error(response, "Erreur serveur", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var participants []models.Participant
	for rows.Next() {
		var p models.Participant
		if err := rows.Scan(&p.ID, &p.Nom, &p.Prenom, &p.Email); err == nil {
			participants = append(participants, p)
		}
	}

	if participants == nil {
		participants = []models.Participant{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(participants)
}

func fetchAndSendEvents(response http.ResponseWriter, query string, id string) {
    rows, err := db.DB.Query(query, id)
    if err != nil {
        http.Error(response, "Erreur serveur", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    var events []map[string]interface{}
    for rows.Next() {
        var id_evt, places int
        var nom, desc, lieu, debut, fin, image, dateFinBoost string
        var prix float64
        if err := rows.Scan(&id_evt, &nom, &desc, &lieu, &places, &prix, &debut, &fin, &image, &dateFinBoost); err == nil {
            events = append(events, map[string]interface{}{
                "id_evenement": id_evt, "nom": nom, "description": desc, "lieu": lieu,
                "nombre_place": places, "prix": prix, "date_debut": debut, "date_fin": fin,
                "image": image, "date_fin_boost": dateFinBoost,
            })
        }
    }
    if events == nil { events = []map[string]interface{}{} }
    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(events)
}

func Get_Events_A_Venir(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	condition := "IFNULL(NULLIF(e.date_fin, ''), DATE_ADD(e.date_debut, INTERVAL 2 HOUR))"

    sqlQuery := fmt.Sprintf(`
			SELECT e.id_evenement, e.nom, e.description, e.lieu, e.nombre_place, e.prix, e.date_debut, IFNULL(e.date_fin, ''), IFNULL(e.image, ''), IFNULL(e.date_fin_boost, '')
			FROM EVENEMENT e
			JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
			WHERE pe.id_prestataire = ? AND %s >= NOW()
			ORDER BY e.date_debut ASC
		`, condition)


	fetchAndSendEvents(response, sqlQuery, providerID)
}

func Get_Historique_Events(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	providerID := request.PathValue("id")

	condition := "IFNULL(NULLIF(e.date_fin, ''), DATE_ADD(e.date_debut, INTERVAL 2 HOUR))"

    sqlQuery := fmt.Sprintf(`
			SELECT e.id_evenement, e.nom, e.description, e.lieu, e.nombre_place, e.prix, e.date_debut, IFNULL(e.date_fin, ''), IFNULL(e.image, ''), IFNULL(e.date_fin_boost, '')
			FROM EVENEMENT e
			JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
			WHERE pe.id_prestataire = ? AND %s < NOW()
			ORDER BY e.date_debut DESC
		`, condition)

	fetchAndSendEvents(response, sqlQuery, providerID)
}