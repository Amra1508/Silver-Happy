package services

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"os"
	"path/filepath"
	"time"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_Produit(response http.ResponseWriter, request *http.Request) {
	
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	rows, errorFetch := db.DB.Query("SELECT id_produit, nom, description, prix, stock, image FROM PRODUIT")
	if errorFetch != nil {
		http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close() 

	var tabProduit []models.Produit
	for rows.Next() {
		var produit models.Produit
		if err := rows.Scan(&produit.ID, &produit.Nom, &produit.Description, &produit.Prix, &produit.Stock, &produit.Image); err != nil {
			fmt.Printf("ERREUR SCAN SUR PRODUIT ID %d: %v\n", produit.ID, err)
			continue
		}
		tabProduit = append(tabProduit, produit)
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(tabProduit)
}

const uploadDir = "./uploads"

func Create_Produit(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	err := request.ParseMultipartForm(10 << 20)
	if err != nil {
		http.Error(response, "Fichier trop volumineux", http.StatusBadRequest)
		return
	}

	nom := request.FormValue("nom")
	desc := request.FormValue("description")
	prix := request.FormValue("prix")
	stock := request.FormValue("stock")

	file, handler, err := request.FormFile("image")
	var imagePath string

	if err == nil {
		defer file.Close()
		
		os.MkdirAll(uploadDir, os.ModePerm)

		fileName := fmt.Sprintf("%d_%s", time.Now().Unix(), handler.Filename)
		imagePath = filepath.Join(uploadDir, fileName)

		dst, err := os.Create(imagePath)
		if err != nil {
			http.Error(response, "Erreur lors de la sauvegarde du fichier", http.StatusInternalServerError)
			return
		}
		defer dst.Close()
		io.Copy(dst, file)
	}

	res, err := db.DB.Exec("INSERT INTO PRODUIT (nom, description, prix, stock, image) VALUES (?, ?, ?, ?, ?)", 
		nom, desc, prix, stock, imagePath)
	
	if err != nil {
		http.Error(response, "Erreur BDD", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(map[string]interface{}{"id": id, "status": "success", "image": imagePath})
}


func Read_One_Produit(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id := request.PathValue("id")
	var produit models.Produit

	err := db.DB.QueryRow("SELECT id_produit, nom, description, prix, stock FROM PRODUIT WHERE id_produit = ?", id).Scan(&produit.ID, &produit.Nom, &produit.Description, &produit.Prix, &produit.Stock)
	
	if err != nil {
		http.Error(response, "Produit non trouvé", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(produit)
}

func Delete_Produit(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")

	var imagePath sql.NullString
	errQuery := db.DB.QueryRow("SELECT image FROM PRODUIT WHERE id_produit = ?", id).Scan(&imagePath)
	
	if errQuery != nil && errQuery != sql.ErrNoRows {
		http.Error(response, "Erreur lors de la recherche du produit", http.StatusInternalServerError)
		return
	}

	_, errDelete := db.DB.Exec("DELETE FROM PRODUIT WHERE id_produit = ?", id)
	if errDelete != nil {
		http.Error(response, "Erreur lors de la suppression en BDD", http.StatusInternalServerError)
		return
	}

	if imagePath.Valid && imagePath.String != "" {
		os.Remove(imagePath.String) 
	}

	response.WriteHeader(http.StatusNoContent)
}

func Update_Produit(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")

	err := request.ParseMultipartForm(10 << 20)
	if err != nil {
		http.Error(response, "Erreur de formulaire ou fichier trop volumineux", http.StatusBadRequest)
		return
	}

	nom := request.FormValue("nom")
	desc := request.FormValue("description")
	prix := request.FormValue("prix")
	stock := request.FormValue("stock")

	file, handler, errFile := request.FormFile("image")
	var imagePath string

	if errFile == nil {
		defer file.Close()
		
		os.MkdirAll("./uploads", os.ModePerm)
		fileName := fmt.Sprintf("%d_%s", time.Now().Unix(), handler.Filename)
		imagePath = filepath.Join("uploads", fileName) 

		dst, errCreate := os.Create(imagePath)
		if errCreate != nil {
			http.Error(response, "Erreur lors de la sauvegarde du fichier", http.StatusInternalServerError)
			return
		}
		defer dst.Close()
		io.Copy(dst, file)
	}

	var res sql.Result
	var errDb error

	if imagePath != "" {
		res, errDb = db.DB.Exec(
			"UPDATE PRODUIT SET nom = ?, description = ?, prix = ?, stock = ?, image = ? WHERE id_produit = ?", 
			nom, desc, prix, stock, imagePath, id,
		)
	} else {
		res, errDb = db.DB.Exec(
			"UPDATE PRODUIT SET nom = ?, description = ?, prix = ?, stock = ? WHERE id_produit = ?", 
			nom, desc, prix, stock, id,
		)
	}

	if errDb != nil {
		http.Error(response, "Erreur lors de la mise à jour en base de données", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Aucun produit trouvé ou aucune modification détectée", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Produit mis à jour avec succès"})
}