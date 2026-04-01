package users

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"os"
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
	db.DB.QueryRow("SELECT COUNT(*) FROM PRESTATAIRE p"+whereClause, argsCount...).Scan(&total)

	argsQuery = append(argsQuery, limit, offset)
	
	sqlQuery := `
		SELECT p.id_prestataire, IFNULL(p.siret, ''), IFNULL(p.nom, ''), IFNULL(p.prenom, ''), 
			   IFNULL(p.email, ''), IFNULL(p.num_telephone, ''), IFNULL(DATE_FORMAT(p.date_naissance, '%Y-%m-%d'), ''), 
			   IFNULL(p.status, 'en attente'), IFNULL(p.motif_refus, ''), IFNULL(p.tarifs, 0), 
			   IFNULL(p.id_categorie, 0), IFNULL(c.nom, ''), IFNULL(DATE_FORMAT(p.date_creation, '%d/%m/%Y à %H:%i'), '') 
		FROM PRESTATAIRE p
		LEFT JOIN CATEGORIE c ON p.id_categorie = c.id_categorie
		` + whereClause + `
		LIMIT ? OFFSET ?
	`

	rows, err := db.DB.Query(sqlQuery, argsQuery...)
	if err != nil {
		http.Error(response, "Erreur BDD", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var list []models.Prestataire
	for rows.Next() {
		var p models.Prestataire
		rows.Scan(&p.ID, &p.Siret, &p.Nom, &p.Prenom, &p.Email, &p.NumTelephone, &p.DateNaissance, &p.Status, &p.MotifRefus, &p.Tarifs, &p.IdCategorie, &p.CategorieNom, &p.DateCreation)
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
		SELECT p.id_prestataire, p.prenom, p.nom, c.nom AS categorie, 
			   IFNULL(AVG(a.note), 0) as moyenne, 
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
			continue
		}

		list = append(list, map[string]interface{}{
			"id_prestataire":  id,
			"prenom":          prenom,
			"nom":             nom,
			"categorie":       typePresta,
			"moyenne":         moyenne,
			"nombre_avis":     nbAvis,
		})
	}

	if list == nil {
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

func Create_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var p models.Prestataire
	if err := json.NewDecoder(request.Body).Decode(&p); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	p.Nom = strings.TrimSpace(p.Nom)
	p.Prenom = strings.TrimSpace(p.Prenom)
	p.Email = strings.TrimSpace(p.Email)
	p.NumTelephone = strings.TrimSpace(p.NumTelephone)
	p.Siret = strings.TrimSpace(p.Siret)

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

	_, err = db.DB.Exec("INSERT INTO PRESTATAIRE (siret, nom, prenom, email, num_telephone, date_naissance, status, motif_refus, tarifs, id_categorie, mdp) VALUES (?, ?, ?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?, ?)",
		p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.Status, p.MotifRefus, p.Tarifs, p.IdCategorie, string(hashMdp))

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

	p.Nom = strings.TrimSpace(p.Nom)
	p.Prenom = strings.TrimSpace(p.Prenom)
	p.Email = strings.TrimSpace(p.Email)
	p.NumTelephone = strings.TrimSpace(p.NumTelephone)
	p.Siret = strings.TrimSpace(p.Siret)

	if p.Nom == "" || p.Prenom == "" || p.Email == "" {
		http.Error(response, "Le nom, prénom et email sont obligatoires", http.StatusBadRequest)
		return
	}

	db.DB.Exec("UPDATE PRESTATAIRE SET siret=?, nom=?, prenom=?, email=?, num_telephone=?, date_naissance=NULLIF(?, ''), status=?, motif_refus=?, tarifs=?, id_categorie=? WHERE id_prestataire=?",
		p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.Status, p.MotifRefus, p.Tarifs, p.IdCategorie, id)

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
	if utils.HandleCORS(response, request, "POST") {
		return
	}
	id := request.PathValue("id")
	request.ParseMultipartForm(10 << 20)

	file, handler, _ := request.FormFile("fichier_document")
	defer file.Close()

	typeDoc := request.FormValue("type_document")
	fileName := id + "_" + handler.Filename

	os.MkdirAll("/api/uploads", os.ModePerm)
	newFile, _ := os.Create("/api/uploads/" + fileName)
	defer newFile.Close()
	io.Copy(newFile, file)

	db.DB.Exec("INSERT INTO DOCUMENT_PRESTATAIRE (type, nom, id_prestataire) VALUES (?, ?, ?)", typeDoc, "uploads/"+fileName, id)

	json.NewEncoder(response).Encode("OK")
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

	err := db.DB.QueryRow(`
		SELECT p.id_prestataire, IFNULL(p.prenom, ''), IFNULL(p.nom, ''), IFNULL(p.email, ''), 
			   IFNULL(p.num_telephone, ''), IFNULL(c.nom, ''), IFNULL(p.tarifs, 0)
		FROM PRESTATAIRE p
		LEFT JOIN CATEGORIE c ON p.id_categorie = c.id_categorie
		WHERE p.id_prestataire = ? AND p.status = 'validé'
	`, idStr).Scan(&prestataire.ID, &prestataire.Prenom, &prestataire.Nom, &prestataire.Email, &prestataire.NumTelephone, &prestataire.CategorieNom, &prestataire.Tarifs)

	if err != nil {
		http.Error(response, "Prestataire non trouvé ou non validé", http.StatusNotFound)
		return
	}

	rows, err := db.DB.Query(`
		SELECT e.id_evenement, e.nom, e.description, e.lieu, e.nombre_place, e.prix, e.date_debut, e.date_fin 
		FROM evenement e
		INNER JOIN PRESTATAIRE_EVENEMENT pe ON e.id_evenement = pe.id_evenement
		WHERE pe.id_prestataire = ? AND e.date_debut >= NOW()
		ORDER BY e.date_debut ASC
	`, idStr)

	var events []map[string]interface{}
	if err == nil {
		defer rows.Close()
		for rows.Next() {
			var id, places int
			var nom, desc, lieu string
			var prix float64
			var debut, fin sql.NullString

			if errScan := rows.Scan(&id, &nom, &desc, &lieu, &places, &prix, &debut, &fin); errScan == nil {
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
				events = append(events, evt)
			}
		}
	}

	if events == nil {
		events = []map[string]interface{}{}
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]interface{}{
		"prestataire": prestataire,
		"evenements":  events,
	})
}