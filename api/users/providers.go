package users

import (
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
		whereClause = " WHERE status = ?"
		argsCount = append(argsCount, statusFilter)
		argsQuery = append(argsQuery, statusFilter)
	}

	var total int
	db.DB.QueryRow("SELECT COUNT(*) FROM PRESTATAIRE"+whereClause, argsCount...).Scan(&total)

	argsQuery = append(argsQuery, limit, offset)
	sqlQuery := `
        SELECT id_prestataire, IFNULL(siret, ''), IFNULL(nom, ''), IFNULL(prenom, ''), 
               IFNULL(email, ''), IFNULL(num_telephone, ''), IFNULL(DATE_FORMAT(date_naissance, '%Y-%m-%d'), ''), 
               IFNULL(status, 'en attente'), IFNULL(motif_refus, ''), IFNULL(tarifs, 0), 
               IFNULL(type_prestation, ''), IFNULL(DATE_FORMAT(date_creation, '%d/%m/%Y à %H:%i'), '') 
        FROM PRESTATAIRE
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
		rows.Scan(&p.ID, &p.Siret, &p.Nom, &p.Prenom, &p.Email, &p.NumTelephone, &p.DateNaissance, &p.Status, &p.MotifRefus, &p.Tarifs, &p.TypePrestation, &p.DateCreation)
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
	errTotal := db.DB.QueryRow("SELECT COUNT(*) FROM PRESTATAIRE AS p"+whereClause, argsCount...).Scan(&total)
	if errTotal != nil {
		http.Error(response, "Erreur calcul total", http.StatusInternalServerError)
		return
	}

	sqlQuery := `
		SELECT p.id_prestataire, p.prenom, p.nom, p.type_prestation, 
		       IFNULL(AVG(a.note), 0) as moyenne, 
		       COUNT(a.id_avis) as nombre_avis 
		FROM PRESTATAIRE AS p 
		LEFT JOIN AVIS AS a ON p.id_prestataire = a.id_prestataire 
		` + whereClause + `
		GROUP BY p.id_prestataire 
		ORDER BY moyenne DESC 
		LIMIT ? OFFSET ?`

	argsQuery = append(argsQuery, limit, offset)

	rows, err := db.DB.Query(sqlQuery, argsQuery...)
	if err != nil {
		fmt.Println("Erreur SQL:", err)
		http.Error(response, "Erreur BDD", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var list []map[string]interface{}
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
			"type_prestation": typePresta,
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
	p.TypePrestation = strings.TrimSpace(p.TypePrestation)

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

	_, err = db.DB.Exec("INSERT INTO PRESTATAIRE (siret, nom, prenom, email, num_telephone, date_naissance, status, motif_refus, tarifs, type_prestation, mdp) VALUES (?, ?, ?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?, ?)",
		p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.Status, p.MotifRefus, p.Tarifs, p.TypePrestation, string(hashMdp))

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
	p.TypePrestation = strings.TrimSpace(p.TypePrestation)

	if p.Nom == "" || p.Prenom == "" || p.Email == "" {
		http.Error(response, "Le nom, prénom et email sont obligatoires", http.StatusBadRequest)
		return
	}

	db.DB.Exec("UPDATE PRESTATAIRE SET siret=?, nom=?, prenom=?, email=?, num_telephone=?, date_naissance=NULLIF(?, ''), status=?, motif_refus=?, tarifs=?, type_prestation=? WHERE id_prestataire=?",
		p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.Status, p.MotifRefus, p.Tarifs, p.TypePrestation, id)

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