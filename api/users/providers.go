package users

import (
	"encoding/json"
	"io"
	"net/http"
	"os"

	"main/db"
	"main/models"
	"main/utils"

	"golang.org/x/crypto/bcrypt"
)

func Read_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	rows, _ := db.DB.Query(`SELECT id_prestataire, IFNULL(siret, ''), IFNULL(nom, ''), IFNULL(prenom, ''), IFNULL(email, ''), IFNULL(num_telephone, ''), IFNULL(DATE_FORMAT(date_naissance, '%Y-%m-%d'), ''), IFNULL(status, 'en attente'), IFNULL(motif_refus, ''), IFNULL(tarifs, 0), IFNULL(type_prestation, ''), IFNULL(DATE_FORMAT(date_creation, '%d/%m/%Y à %H:%i'), '') FROM PRESTATAIRE`)
	defer rows.Close()

	var list []models.Prestataire
	for rows.Next() {
		var p models.Prestataire
		rows.Scan(&p.ID, &p.Siret, &p.Nom, &p.Prenom, &p.Email, &p.NumTelephone, &p.DateNaissance, &p.Status, &p.MotifRefus, &p.Tarifs, &p.TypePrestation, &p.DateCreation)
		list = append(list, p)
	}

	json.NewEncoder(response).Encode(list)
}

func Create_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var p models.Prestataire
	json.NewDecoder(request.Body).Decode(&p)
	hashMdp, _ := bcrypt.GenerateFromPassword([]byte("1234"), bcrypt.DefaultCost)

	db.DB.Exec("INSERT INTO PRESTATAIRE (siret, nom, prenom, email, num_telephone, date_naissance, status, motif_refus, tarifs, type_prestation, mdp) VALUES (?, ?, ?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?, ?)",
		p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.Status, p.MotifRefus, p.Tarifs, p.TypePrestation, string(hashMdp))

	json.NewEncoder(response).Encode("OK")
}

func Update_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")
	var p models.Prestataire
	json.NewDecoder(request.Body).Decode(&p)

	db.DB.Exec("UPDATE PRESTATAIRE SET siret=?, nom=?, prenom=?, email=?, num_telephone=?, date_naissance=NULLIF(?, ''), status=?, motif_refus=?, tarifs=?, type_prestation=? WHERE id_prestataire=?",
		p.Siret, p.Nom, p.Prenom, p.Email, p.NumTelephone, p.DateNaissance, p.Status, p.MotifRefus, p.Tarifs, p.TypePrestation, id)

	json.NewEncoder(response).Encode("OK")
}

func Delete_Prestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}
	id := request.PathValue("id")
	db.DB.Exec("DELETE FROM PRESTATAIRE WHERE id_prestataire = ?", id)
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

	os.MkdirAll("/api/users/uploads", os.ModePerm)
	newFile, _ := os.Create("/api/users/uploads/" + fileName)
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
		cheminPhysique := "/api/users/" + cheminBDD
		os.Remove(cheminPhysique)
	}

	db.DB.Exec("DELETE FROM DOCUMENT_PRESTATAIRE WHERE id_document = ?", idDoc)

	json.NewEncoder(response).Encode("OK")
}
