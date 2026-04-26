package users

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"html"
	"io"
	"net/http"
	"os"
	"path/filepath"
	"strings"
	"time"

	"main/db"
	"main/models"
	"main/utils"

	"golang.org/x/crypto/bcrypt"
)

func Read_Prestataire(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    query := request.URL.Query()
    limitStr := query.Get("limit")
    pageStr := query.Get("page")
    statusFilter := query.Get("status")

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

    whereClause := ""
    var argsCount []interface{}
    var argsQuery []interface{}

    if statusFilter != "" && statusFilter != "tous" {
        whereClause = " WHERE p.status = ?"
        argsCount = append(argsCount, statusFilter)
        argsQuery = append(argsQuery, statusFilter)
    }

    var total int
    errCount := db.DB.QueryRow("SELECT COUNT(*) FROM PRESTATAIRE p"+whereClause, argsCount...).Scan(&total)
    if errCount != nil {
        http.Error(response, "Erreur lors du comptage", http.StatusInternalServerError)
        return
    }

    argsQuery = append(argsQuery, limit, offset)
    
    sqlQuery := `
        SELECT 
            p.id_prestataire, 
            COALESCE(p.siret, ''), 
            COALESCE(p.prenom, ''), 
            COALESCE(p.nom, ''), 
            COALESCE(p.email, ''), 
            COALESCE(DATE_FORMAT(p.date_naissance, '%Y-%m-%d'), ''), 
            COALESCE(p.num_telephone, ''), 
            COALESCE(p.status, 'en attente'), 
            COALESCE(p.motif_refus, ''), 
            COALESCE(DATE_FORMAT(p.date_creation, '%d/%m/%Y %H:%i:%s'), ''), 
            COALESCE(p.id_abonnement, 0), 
            COALESCE(p.id_categorie, 0), 
            COALESCE(c.nom, '') 
        FROM PRESTATAIRE p
        LEFT JOIN CATEGORIE c ON p.id_categorie = c.id_categorie
        ` + whereClause + `
        LIMIT ? OFFSET ?
    `

    rows, err := db.DB.Query(sqlQuery, argsQuery...)
    if err != nil {
        http.Error(response, "Erreur BDD lors de la récupération", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    var list []models.Prestataire
    for rows.Next() {
        var p models.Prestataire
        
        errScan := rows.Scan(
            &p.ID, 
            &p.Siret, 
            &p.Prenom, 
            &p.Nom, 
            &p.Email, 
            &p.DateNaissance, 
            &p.NumTelephone, 
            &p.Status, 
            &p.MotifRefus, 
            &p.DateCreation, 
            &p.IdAbonnement, 
            &p.IdCategorie, 
            &p.CategorieNom,
        )
        
        if errScan != nil {
            fmt.Println("Ligne ignorée ! Erreur de Scan sur le Prestataire :", errScan)
            continue
        }
        
        list = append(list, p)
    }

    if list == nil {
        list = []models.Prestataire{}
    }

    dataResponse := map[string]interface{}{
        "data":        list,
        "total":       total,
        "currentPage": page,
        "totalPages":  (total + limit - 1) / limit,
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(dataResponse)
}

func List_Prestataires(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	queryParams := request.URL.Query()
	limitStr := queryParams.Get("limit")
	pageStr := queryParams.Get("page")
	userIdStr := queryParams.Get("user_id") 

	limit := 10
	offset := 0
	page := 1
	var userId int

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
    errCount := db.DB.QueryRow("SELECT COUNT(*) FROM PRESTATAIRE WHERE status = 'valide'").Scan(&total)
    if errCount != nil {
        total = 0
    }

	query := `
        SELECT p.id_prestataire, p.nom, p.prenom, p.email, 
               (SELECT COUNT(*) FROM MESSAGE_PRESTATAIRE 
                WHERE id_prestataire = p.id_prestataire 
                AND id_utilisateur = ? AND est_lu = 0 AND expediteur = 1) AS est_lu
        FROM PRESTATAIRE p
        WHERE p.status = 'valide'
        ORDER BY est_lu DESC, p.nom ASC
        LIMIT ? OFFSET ?`

	rows, err := db.DB.Query(query, userId, limit, offset)
	if err != nil {
		http.Error(response, "Erreur serveur", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var tabUtilisateur []models.Utilisateur
	for rows.Next() {
		var user models.Utilisateur
		var estLu int 

		err := rows.Scan(
			&user.ID, &user.Nom, &user.Prenom, &user.Email,
			&estLu,
		)

		if err == nil {
			user.EstLu = estLu

			tabUtilisateur = append(tabUtilisateur, user)
		}
	}

	if tabUtilisateur == nil {
		tabUtilisateur = []models.Utilisateur{}
	}

	dataResponse := map[string]interface{}{
		"data":        tabUtilisateur,
		"total":       total,
		"currentPage": page,
		"totalPages":  (total + limit - 1) / limit,
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(dataResponse)
}

func Get_Prestataire_Top(response http.ResponseWriter, request *http.Request) {
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
    errTotal := db.DB.QueryRow("SELECT COUNT(*) FROM PRESTATAIRE AS p WHERE p.status = 'valide'").Scan(&total)
    if errTotal != nil {
        http.Error(response, "Erreur calcul total", http.StatusInternalServerError)
        return
    }

    sqlQuery := `
        SELECT p.id_prestataire, 
               COALESCE(p.prenom, ''), 
               COALESCE(p.nom, ''), 
               COALESCE(c.nom, ''), 
               COALESCE(AVG(a.note), 0) as moyenne, 
               COUNT(a.id_avis) as nombre_avis 
        FROM PRESTATAIRE AS p 
        LEFT JOIN AVIS AS a ON p.id_prestataire = a.id_prestataire 
        LEFT JOIN CATEGORIE AS c ON p.id_categorie = c.id_categorie
        WHERE p.status = 'valide'
        GROUP BY p.id_prestataire 
        ORDER BY moyenne DESC 
        LIMIT ? OFFSET ?`

    rows, err := db.DB.Query(sqlQuery, limit, offset)
    if err != nil {
        fmt.Println("Erreur SQL:", err)
        http.Error(response, "Erreur BDD", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    list := make([]map[string]interface{}, 0)
    for rows.Next() {
        var id int64
        var prenom, nom, typePresta string
        var moyenne float64
        var nbAvis int

        if err := rows.Scan(&id, &prenom, &nom, &typePresta, &moyenne, &nbAvis); err != nil {
            fmt.Println("Ligne ignorée dans le Top ! Erreur de Scan :", err)
            continue
        }

        list = append(list, map[string]interface{}{
            "id_prestataire":  id,
            "prenom":          prenom,
            "nom":             nom,
            "type_prestation": typePresta,
            "moyenne":         moyenne,
            "nombre_avis":     nbAvis,
        })
    }

    if len(list) == 0 {
        list = []map[string]interface{}{}
    }

    dataResponse := map[string]interface{}{
        "data":        list,
        "total":       total,
        "currentPage": page,
        "totalPages":  (total + limit - 1) / limit,
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(dataResponse)
}

func Get_Note_Moyenne(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") { return }

    prestaID := request.PathValue("id")

    sqlQuery := `
        SELECT 
            COALESCE(AVG(note), 0) as moyenne, 
            COUNT(id_avis) as nombre_avis 
        FROM AVIS 
        WHERE id_prestataire = ?`

    var moyenne float64
    var nbAvis int

    err := db.DB.QueryRow(sqlQuery, prestaID).Scan(&moyenne, &nbAvis)
    if err != nil {
        fmt.Println("Erreur SQL:", err)
        http.Error(response, "Erreur lors de la récupération des notes", http.StatusInternalServerError)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(map[string]interface{}{
        "moyenne":     moyenne,
        "nombre_avis": nbAvis,
    })
}

func Create_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var p models.Prestataire
	if err := json.NewDecoder(request.Body).Decode(&p); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	p.Nom = html.EscapeString(strings.TrimSpace(p.Nom))
	p.Prenom = html.EscapeString(strings.TrimSpace(p.Prenom))
	p.Email = strings.ToLower(strings.TrimSpace(p.Email))
	p.NumTelephone = strings.TrimSpace(p.NumTelephone)
	p.Siret = html.EscapeString(strings.TrimSpace(p.Siret))

	if p.Nom == "" || p.Prenom == "" || p.Email == "" {
		http.Error(response, "Le nom, prénom et email sont obligatoires", http.StatusBadRequest)
		return
	}

	if p.DateNaissance != "" {
		dateParsed, err := time.Parse("2006-01-02", p.DateNaissance)
		if err != nil {
			http.Error(response, "Format de date de naissance invalide (attendu: AAAA-MM-JJ)", http.StatusBadRequest)
			return
		}
		if dateParsed.After(time.Now()) {
			http.Error(response, "La date de naissance ne peut pas être dans le futur", http.StatusBadRequest)
			return
		}

		dateLimite := time.Now().AddDate(-18, 0, 0)
		if dateParsed.After(dateLimite) {
			http.Error(response, "Un prestataire doit avoir au moins 18 ans", http.StatusBadRequest)
			return
		}
	}

	taken, msg := isContactInfoTaken(p.Email, p.NumTelephone)
	if taken {
		http.Error(response, msg, http.StatusConflict)
		return
	}

	hashMdp, err := bcrypt.GenerateFromPassword([]byte("1234"), bcrypt.DefaultCost)
	if err != nil {
		http.Error(response, "Erreur lors du hachage du mot de passe", http.StatusInternalServerError)
		return
	}

	_, err = db.DB.Exec("INSERT INTO PRESTATAIRE (siret, nom, prenom, email, num_telephone, date_naissance, status, motif_refus, id_categorie, mdp) VALUES (?, ?, ?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?, ?)",
		p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.Status, p.MotifRefus, p.IdCategorie, string(hashMdp))

	if err != nil {
		http.Error(response, "Erreur création prestataire", http.StatusInternalServerError)
		return
	}

	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode("OK")
}

func Update_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")
	var p models.Prestataire
	if err := json.NewDecoder(request.Body).Decode(&p); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	p.Nom = html.EscapeString(strings.TrimSpace(p.Nom))
	p.Prenom = html.EscapeString(strings.TrimSpace(p.Prenom))
	p.Email = strings.ToLower(strings.TrimSpace(p.Email))
	p.NumTelephone = strings.TrimSpace(p.NumTelephone)
	p.Siret = html.EscapeString(strings.TrimSpace(p.Siret))

	if p.Nom == "" || p.Prenom == "" || p.Email == "" {
		http.Error(response, "Le nom, prénom et email sont obligatoires", http.StatusBadRequest)
		return
	}

	db.DB.Exec("UPDATE PRESTATAIRE SET siret=?, nom=?, prenom=?, email=?, num_telephone=?, date_naissance=NULLIF(?, ''), status=?, motif_refus=?, id_categorie=? WHERE id_prestataire=?",
		p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.Status, p.MotifRefus, p.IdCategorie, id)

	json.NewEncoder(response).Encode("OK")
}

func Delete_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	rows, err := db.DB.Query("SELECT nom FROM DOCUMENT_PRESTATAIRE WHERE id_prestataire = ?", id)
	if err == nil {
		defer rows.Close()
		for rows.Next() {
			var cheminBDD string
			if errScan := rows.Scan(&cheminBDD); errScan == nil {
				cheminPhysique := "/api/" + cheminBDD
				os.Remove(cheminPhysique)
			}
		}
	}

	db.DB.Exec("DELETE FROM DOCUMENT_PRESTATAIRE WHERE id_prestataire = ?", id)

	_, errDelete := db.DB.Exec("DELETE FROM PRESTATAIRE WHERE id_prestataire = ?", id)
	if errDelete != nil {
		http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	json.NewEncoder(response).Encode("OK")
}

func Read_Prestataire_Documents(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}
	id := request.PathValue("id")

	rows, _ := db.DB.Query("SELECT id_document, type, nom FROM DOCUMENT_PRESTATAIRE WHERE id_prestataire = ?", id)
	defer rows.Close()

	var list []models.Document
	for rows.Next() {
		var doc models.Document
		rows.Scan(&doc.ID, &doc.Type, &doc.Lien)
		list = append(list, doc)
	}

	if list == nil {
		list = make([]models.Document, 0)
	}
	json.NewEncoder(response).Encode(list)
}

func Upload_Prestataire_Document(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") { return }
	
	id := request.PathValue("id")
	if err := request.ParseMultipartForm(10 << 20); err != nil {
		http.Error(response, "Fichier trop volumineux", http.StatusBadRequest)
		return
	}

	file, handler, err := request.FormFile("fichier_document")
	if err != nil {
		http.Error(response, "Fichier manquant", http.StatusBadRequest)
		return
	}
	defer file.Close()

	safeFileName := id + "_" + filepath.Base(handler.Filename)
	uploadPath := "./uploads/documents"
	os.MkdirAll(uploadPath, os.ModePerm)

	dst, err := os.Create(filepath.Join(uploadPath, safeFileName))
	if err != nil {
		http.Error(response, "Erreur création fichier", http.StatusInternalServerError)
		return
	}
	defer dst.Close()
	io.Copy(dst, file)

	typeDoc := html.EscapeString(request.FormValue("type_document"))
	db.DB.Exec("INSERT INTO DOCUMENT_PRESTATAIRE (type, nom, id_prestataire) VALUES (?, ?, ?)", 
        typeDoc, "uploads/documents/"+safeFileName, id)

	json.NewEncoder(response).Encode(map[string]string{"message": "Document uploadé"})
}

func Delete_Prestataire_Document(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}
	idDoc := request.PathValue("id")

	var cheminBDD string
	err := db.DB.QueryRow("SELECT nom FROM DOCUMENT_PRESTATAIRE WHERE id_document = ?", idDoc).Scan(&cheminBDD)

	if err == nil {
		cheminPhysique := "/api/" + cheminBDD
		os.Remove(cheminPhysique)
	}

	db.DB.Exec("DELETE FROM DOCUMENT_PRESTATAIRE WHERE id_document = ?", idDoc)

	json.NewEncoder(response).Encode("OK")
}

func Read_One_Prestataire_Profile(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    idStr := request.PathValue("id")

    prestataire := models.Prestataire{}
    var dateFinBoostPresta sql.NullString 

    err := db.DB.QueryRow(`
        SELECT p.id_prestataire, IFNULL(p.prenom, ''), IFNULL(p.nom, ''), IFNULL(p.email, ''), 
               IFNULL(p.num_telephone, ''), IFNULL(c.nom, ''), p.date_fin_boost
        FROM PRESTATAIRE p
        LEFT JOIN CATEGORIE c ON p.id_categorie = c.id_categorie
        WHERE p.id_prestataire = ? AND p.status = 'validé'
    `, idStr).Scan(&prestataire.ID, &prestataire.Prenom, &prestataire.Nom, &prestataire.Email, &prestataire.NumTelephone, &prestataire.CategorieNom, &dateFinBoostPresta)

    if err != nil {
        http.Error(response, "Prestataire non trouvé ou non validé", http.StatusNotFound)
        return
    }

    rows, err := db.DB.Query(`
        SELECT e.id_evenement, e.nom, e.description, e.lieu, e.nombre_place, e.prix, e.date_debut, e.date_fin, e.image, e.date_fin_boost
        FROM evenement e
        INNER JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
        WHERE pe.id_prestataire = ? AND e.date_debut >= NOW()
        ORDER BY 
            (e.date_fin_boost IS NOT NULL AND e.date_fin_boost > NOW()) DESC,
            e.date_debut ASC
    `, idStr)

    var events []map[string]interface{}
    if err == nil {
        defer rows.Close()
        for rows.Next() {
            var id, places int
            var nom, desc, lieu string
            var prix float64
            var debut, fin, image, dateFinBoostEvt sql.NullString 

            if errScan := rows.Scan(&id, &nom, &desc, &lieu, &places, &prix, &debut, &fin, &image, &dateFinBoostEvt); errScan == nil {
                evt := map[string]interface{}{
                    "id_evenement": id,
                    "nom":          nom,
                    "description":  desc,
                    "lieu":         lieu,
                    "nombre_place": places,
                    "prix":         prix,
                }
                if debut.Valid {
                    evt["date_debut"] = debut.String
                }
                if fin.Valid {
                    evt["date_fin"] = fin.String
                }
                if image.Valid && image.String != "" {
                    evt["image"] = image.String
                }
                if dateFinBoostEvt.Valid {
                    evt["date_fin_boost"] = dateFinBoostEvt.String
                }
                
                events = append(events, evt)
            } else {
                fmt.Println("Erreur Scan Evenement:", errScan)
            }
        }
    }

    if events == nil {
        events = []map[string]interface{}{}
    }

    type PrestataireWithBoost struct {
        models.Prestataire
        DateFinBoost string `json:"date_fin_boost,omitempty"`
    }

    responsePresta := PrestataireWithBoost{
        Prestataire: prestataire,
    }
    if dateFinBoostPresta.Valid {
        responsePresta.DateFinBoost = dateFinBoostPresta.String
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(map[string]interface{}{
        "prestataire": responsePresta,
        "evenements":  events,
    })
}