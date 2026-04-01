package auth

import (
	"encoding/json"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"
	"regexp"
	"strings"

	"golang.org/x/crypto/bcrypt"
)

func RegisterPrestataire(response http.ResponseWriter, request *http.Request) {

	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var provider models.Prestataire

	if err := json.NewDecoder(request.Body).Decode(&provider); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	provider.Prenom = strings.TrimSpace(provider.Prenom)
	provider.Nom = strings.TrimSpace(provider.Nom)
	provider.Email = strings.TrimSpace(provider.Email)
	provider.NumTelephone = strings.TrimSpace(provider.NumTelephone)
	provider.Siret = strings.TrimSpace(provider.Siret)

	if strings.Contains(provider.Prenom, " ") || strings.Contains(provider.Nom, " ") || strings.Contains(provider.Email, " ") || strings.Contains(provider.NumTelephone, " ") || strings.Contains(provider.Siret, " ") {
		http.Error(response, "Les espaces ne sont pas autorisés pour ces champs.", http.StatusConflict)
		return
	}

	emailRegex := regexp.MustCompile(`^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$`)
	if !emailRegex.MatchString(provider.Email) {
		http.Error(response, "Le format de l'adresse e-mail est invalide.", http.StatusBadRequest)
		return
	}

	if strings.ContainsAny(provider.Prenom, "0123456789") || strings.ContainsAny(provider.Nom, "0123456789") {
		http.Error(response, "Les informations que vous avez saisi sont erronées.", http.StatusConflict)
		return
	}

	if len(provider.Prenom) < 2 || len(provider.Prenom) > 50 {
		http.Error(response, "Le prénom doit contenir entre 2 et 50 caractères", http.StatusConflict)
		return
	}

	if len(provider.Nom) < 2 || len(provider.Nom) > 50 {
		http.Error(response, "Le nom doit contenir entre 2 et 50 caractères", http.StatusConflict)
		return
	}

	if len(provider.NumTelephone) != 10 {
		http.Error(response, "Le numéro de téléphone est invalide.", http.StatusConflict)
		return
	}

	if len(provider.Siret) != 14 || strings.ContainsAny(provider.Siret, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {
		http.Error(response, "Le numéro SIRET doit contenir exactement 14 chiffres.", http.StatusConflict)
		return
	}

	if provider.Tarifs < 0 {
		http.Error(response, "Le tarif ne peut pas être négatif.", http.StatusBadRequest)
		return
	}

	var catExists bool
	errCat := db.DB.QueryRow("SELECT EXISTS(SELECT 1 FROM CATEGORIE WHERE id_categorie = ?)", provider.IdCategorie).Scan(&catExists)
	if errCat != nil || !catExists {
		http.Error(response, "La catégorie sélectionnée n'existe pas.", http.StatusBadRequest)
		return
	}

	var count int
	checkQuery := `SELECT COUNT(*) FROM PRESTATAIRE WHERE email = ? OR num_telephone = ? OR siret = ?`
	err := db.DB.QueryRow(checkQuery, provider.Email, provider.NumTelephone, provider.Siret).Scan(&count)
	if err != nil {
		http.Error(response, "Erreur lors de la vérification des données", http.StatusInternalServerError)
		return
	}

	if count > 0 {
		http.Error(response, "Cet email, téléphone ou numéro SIRET est déjà utilisé.", http.StatusConflict)
		return
	}

	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(provider.Mdp), bcrypt.DefaultCost)
	if err != nil {
		http.Error(response, "Erreur lors du hashage du mot de passe", http.StatusInternalServerError)
		return
	}

	queryUser := `INSERT INTO PRESTATAIRE (siret, prenom, nom, email, mdp, date_naissance, num_telephone, id_categorie, tarifs, date_creation) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())`

	res, err := db.DB.Exec(queryUser,
		provider.Siret,
		provider.Prenom,
		provider.Nom,
		provider.Email,
		string(hashedPassword),
		provider.DateNaissance,
		provider.NumTelephone,
		provider.IdCategorie,
		provider.Tarifs,
	)

	if err != nil {
		if strings.Contains(err.Error(), "Duplicate entry") {
			http.Error(response, "Une de ces informations est déjà utilisée.", http.StatusConflict)
		} else {
			http.Error(response, "Erreur base de données : "+err.Error(), http.StatusBadRequest)
		}
		return
	}

	provider.ID, _ = res.LastInsertId()
	provider.Mdp = "" 

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(provider)
}