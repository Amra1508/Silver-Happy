package auth

import (
	"database/sql"
	"encoding/json"
	"net/http"
	"strings"
	"time"

	"main/db"
	"main/models"
	"main/utils"

	"golang.org/x/crypto/bcrypt"
)


func Register(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "POST") {
        return
    }

    var user models.Utilisateur
    if err := json.NewDecoder(request.Body).Decode(&user); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

	if strings.ContainsAny(user.Prenom, "0123456789") || strings.ContainsAny(user.Nom, "0123456789") || strings.ContainsAny(user.Pays, "0123456789") || strings.ContainsAny(user.Ville, "0123456789"){
		http.Error(response, "Les informations que vous avez saisi sont erronées.", http.StatusConflict)
        return
	}

	if len(user.Prenom) < 2 || len(user.Prenom) > 50 {
        http.Error(response, "Le prénom doit contenir entre 2 et 50 caractères", http.StatusConflict)
        return
    }

	if len(user.Nom) < 2 || len(user.Nom) > 50 {
        http.Error(response, "Le nom doit contenir entre 2 et 50 caractères", http.StatusConflict)
        return
    }

	if len(user.NumTelephone) != 10 {
		http.Error(response, "Les informations que vous avez saisi sont erronées.", http.StatusConflict)
        return
	}

	var count int
    checkQuery := `SELECT COUNT(*) FROM UTILISATEUR WHERE email = ? OR num_telephone = ?`
    err := db.DB.QueryRow(checkQuery, user.Email, user.NumTelephone).Scan(&count)
    if err != nil {
        http.Error(response, "Erreur lors de la vérification des données", http.StatusInternalServerError)
        return
    }

    if count > 0 {
        http.Error(response, "Cette adresse email ou ce numéro de téléphone est déjà utilisé.", http.StatusConflict)
        return
    }

	if len(user.CodePostal) != 5 {
		http.Error(response, "Les informations que vous avez saisi sont erronées.", http.StatusConflict)
        return
	}

    hashedPassword, err := bcrypt.GenerateFromPassword([]byte(user.Mdp), bcrypt.DefaultCost)
    if err != nil {
        http.Error(response, "Erreur lors du hashage du mot de passe", http.StatusInternalServerError)
        return
    }

    tx, err := db.DB.Begin()
    if err != nil {
        http.Error(response, "Erreur serveur (Transaction)", http.StatusInternalServerError)
        return
    }

    queryAddr := `INSERT INTO ADRESSE (rue, ville, code_postal, pays) VALUES (?, ?, ?, ?)`
    resAddr, err := tx.Exec(queryAddr, 
        user.Adresse, 
        user.Ville, 
        user.CodePostal, 
        user.Pays,
    )
    
    if err != nil {
        tx.Rollback()
        http.Error(response, "Erreur lors de la création de l'adresse", http.StatusInternalServerError)
        return
    }
    idAdresse, _ := resAddr.LastInsertId()

    queryPlan := `INSERT INTO PLANNING (nom, description, date_creation) VALUES (?, ?, NOW())`
    resPlan, err := tx.Exec(queryPlan, 
        "Planning de "+user.Prenom, 
        "Planning personnel", 
    )

    if err != nil {
        tx.Rollback()
        http.Error(response, "Erreur lors de la création du planning", http.StatusInternalServerError)
        return
    }
    idPlanning, _ := resPlan.LastInsertId()

    queryUser := `INSERT INTO UTILISATEUR (prenom, nom, email, mdp, date_naissance, num_telephone, statut, date_creation, id_planning, id_adresse) 
                VALUES (?, ?, ?, ?, ?, ?, 'user', NOW(), ?, ?)`

    res, err := tx.Exec(queryUser, 
        user.Prenom, 
        user.Nom, 
        user.Email, 
        string(hashedPassword), 
        user.DateNaissance, 
        user.NumTelephone, 
        idPlanning, 
        idAdresse,
    )

    if err != nil {
        tx.Rollback()
        if strings.Contains(err.Error(), "Duplicate entry") {
            http.Error(response, "Cette adresse email est déjà utilisée.", http.StatusConflict)
        } else {
            http.Error(response, "Erreur base de données : "+err.Error(), http.StatusBadRequest)
        }
        return
    }

    if err := tx.Commit(); err != nil {
        http.Error(response, "Erreur lors de la validation finale", http.StatusInternalServerError)
        return
    }

    user.ID, _ = res.LastInsertId()
    user.Mdp = "" 

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusCreated)
    json.NewEncoder(response).Encode(user)
}

func Login(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "POST") {
        return
    }

    var creds models.LoginCredentials
    if err := json.NewDecoder(request.Body).Decode(&creds); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    var user models.Utilisateur
    var hashedPassword string

    row := db.DB.QueryRow("SELECT id_utilisateur, email, mdp, statut FROM UTILISATEUR WHERE email = ?", creds.Email)
    err := row.Scan(&user.ID, &user.Email, &hashedPassword, &user.Statut)
    if err != nil {
        if err == sql.ErrNoRows {
            http.Error(response, "Email ou mot de passe incorrect", http.StatusUnauthorized)
        } else {
            http.Error(response, "Erreur serveur", http.StatusInternalServerError)
        }
        return
    }

    err = bcrypt.CompareHashAndPassword([]byte(hashedPassword), []byte(creds.Mdp))
    if err != nil {
        http.Error(response, "Email ou mot de passe incorrect", http.StatusUnauthorized)
        return
    }

    tokenString, err := generateJWT(user)
    if err != nil {
        http.Error(response, "Erreur lors de la création de la session", http.StatusInternalServerError)
        return
    }

    cookie := http.Cookie{
        Name:     "session_token",
        Value:    tokenString, 
        Expires:  time.Now().Add(24 * time.Hour), 
        Path:     "/",
    }
    http.SetCookie(response, &cookie)

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusOK)
    
    json.NewEncoder(response).Encode(map[string]string{
        "message": "Connexion réussie",
        "statut":  user.Statut, 
    })
}

func Logout(response http.ResponseWriter, request *http.Request) {

    if utils.HandleCORS(response, request, "POST") {
        return
    }

	cookie := http.Cookie{
		Name:     "session_token",
		Value:    "",
		Expires:  time.Now().Add(-1 * time.Hour),
		HttpOnly: true,
		Path:     "/",
	}
	http.SetCookie(response, &cookie)

	response.WriteHeader(http.StatusOK)
	response.Header().Add("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]string{"message": "Déconnexion réussie"})
}