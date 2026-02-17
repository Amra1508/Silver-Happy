package main

import (
	"database/sql"
	"encoding/json"
	"net/http"
	"time"

	"golang.org/x/crypto/bcrypt"
)

func register(response http.ResponseWriter, request *http.Request) {
	var user Utilisateur
	if err := json.NewDecoder(request.Body).Decode(&user); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(user.Mdp), bcrypt.DefaultCost)
	if err != nil {
		http.Error(response, "Erreur lors du hashage du mot de passe", http.StatusInternalServerError)
		return
	}

	query := `INSERT INTO utilisateur (prenom, nom, email, mdp, num_telephone) VALUES (?, ?, ?, ?, ?)`
	res, err := DB.Exec(query, user.Prenom, user.Nom, user.Email, string(hashedPassword), user.NumTelephone)

	if err != nil {
		http.Error(response, "Erreur d'inscription (L'email existe peut-être déjà)", http.StatusBadRequest)
		return
	}

	user.ID, _ = res.LastInsertId()
	user.Mdp = "" 
	response.WriteHeader(http.StatusCreated)
	response.Header().Add("Content-Type", "application/json")
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