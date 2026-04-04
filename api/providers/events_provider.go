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

        imagePath = "/uploads/" + filename 
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

    queryInsert := `INSERT INTO evenement (nom, description, lieu, nombre_place, prix, date_debut, date_fin, id_categorie, image) 
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