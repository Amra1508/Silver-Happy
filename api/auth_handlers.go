package main

import (
	"database/sql"
	"encoding/json"
	"net/http"
	"strings"
	"time"

	"golang.org/x/crypto/bcrypt"
)

func handleCORS(response http.ResponseWriter, request *http.Request, methode string) bool {
    response.Header().Set("Access-Control-Allow-Origin", "*")
    response.Header().Set("Access-Control-Allow-Methods", methode + ", OPTIONS")
    response.Header().Set("Access-Control-Allow-Headers", "Content-Type")

    if request.Method == "OPTIONS" {
        response.WriteHeader(http.StatusOK)
        return true
    }
    
    return false
}

func register(response http.ResponseWriter, request *http.Request) {

	if handleCORS(response, request, "POST") {
        return
    }

    var user Utilisateur
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
    checkQuery := `SELECT COUNT(*) FROM utilisateur WHERE email = ? OR num_telephone = ?`
    err := DB.QueryRow(checkQuery, user.Email, user.NumTelephone).Scan(&count)
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

    query := `INSERT INTO utilisateur (prenom, nom, date_naissance, num_telephone, email, mdp, pays, adresse, ville, code_postal) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`
              
    res, err := DB.Exec(query, 
        user.Prenom, 
        user.Nom, 
        user.DateNaissance, 
        user.NumTelephone, 
        user.Email, 
        string(hashedPassword),
        user.Pays,
        user.Adresse,
        user.Ville,
        user.CodePostal,
    )

    if err != nil {
        http.Error(response, "Erreur d'inscription dans la base de données", http.StatusBadRequest)
        return
    }

    user.ID, _ = res.LastInsertId()
    user.Mdp = ""
    
    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusCreated)
    json.NewEncoder(response).Encode(user)
}

func login(response http.ResponseWriter, request *http.Request) {
	var creds LoginCredentials
	if err := json.NewDecoder(request.Body).Decode(&creds); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	var user Utilisateur
	var hashedPassword string

	row := DB.QueryRow("SELECT id, email, mdp FROM utilisateur WHERE email = ?", creds.Email)
	err := row.Scan(&user.ID, &user.Email, &hashedPassword)
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

	cookie := http.Cookie{
		Name:     "session_token",
		Value:    "token_actif",
		Expires:  time.Now().Add(24 * time.Hour), 
		HttpOnly: true, 
		Path:     "/",
	}
	http.SetCookie(response, &cookie)

	response.WriteHeader(http.StatusOK)
	response.Header().Add("Content-Type", "application/json")
	json.NewEncoder(response).Encode(map[string]string{"message": "Connexion réussie"})
}

func logout(response http.ResponseWriter, request *http.Request) {
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