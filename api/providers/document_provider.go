package providers

import (
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"os"
	"path/filepath"
	"time"

	"main/db"
	"main/utils"
)

func Get_Documents(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") { return }

	providerID := request.PathValue("id")
	query := `SELECT id_document, type, nom FROM DOCUMENT_PRESTATAIRE WHERE id_prestataire = ?`
	
	rows, err := db.DB.Query(query, providerID)
	if err != nil {
		http.Error(response, "Erreur BDD", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var docs []map[string]interface{}
	for rows.Next() {
		var id int
		var typeDoc, nom string
		rows.Scan(&id, &typeDoc, &nom)
		
		docs = append(docs, map[string]interface{}{
			"id": id, 
			"type": typeDoc, 
			"url": nom,
			"nom": filepath.Base(nom), 
		})
	}

	if docs == nil { docs = []map[string]interface{}{} }
	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(docs)
}

func Upload_Document(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") { return }

	providerID := request.PathValue("id")
	
	request.ParseMultipartForm(10 << 20)

	file, handler, err := request.FormFile("document")
	if err != nil {
		http.Error(response, "Erreur récupération fichier", http.StatusBadRequest)
		return
	}
	defer file.Close()

	typeDoc := request.FormValue("type_document")
	if typeDoc == "" { typeDoc = "Autre" }

	os.MkdirAll(filepath.Join("uploads", "documents"), os.ModePerm)

	ext := filepath.Ext(handler.Filename)
	filename := fmt.Sprintf("%s_doc_%d%s", providerID, time.Now().Unix(), ext)
	
	savePath := filepath.Join("uploads", "documents", filename)
	
	dbPath := "uploads/documents/" + filename

	dst, err := os.Create(savePath)
	if err != nil {
		http.Error(response, "Erreur sauvegarde serveur", http.StatusInternalServerError)
		return
	}
	defer dst.Close()
	io.Copy(dst, file)

	db.DB.Exec("INSERT INTO DOCUMENT_PRESTATAIRE (id_prestataire, type, nom) VALUES (?, ?, ?)", 
		providerID, typeDoc, dbPath)

	response.WriteHeader(http.StatusOK)
}

func Delete_Document(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") { return }

	docID := request.PathValue("id")
	
	var filePath string
	err := db.DB.QueryRow("SELECT nom FROM DOCUMENT_PRESTATAIRE WHERE id_document = ?", docID).Scan(&filePath)
	if err == nil {
		os.Remove(filePath)
	}

	db.DB.Exec("DELETE FROM DOCUMENT_PRESTATAIRE WHERE id_document = ?", docID)
	response.WriteHeader(http.StatusOK)
}