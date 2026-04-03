package auth

import (
	"encoding/json"
	"html"
	"net/http"
	"regexp"
	"strings"
	"time"

	"main/db"
	"main/models"
	"main/utils"

	"github.com/golang-jwt/jwt/v5"
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

	provider.Prenom = html.EscapeString(strings.TrimSpace(provider.Prenom))
	provider.Nom = html.EscapeString(strings.TrimSpace(provider.Nom))
	provider.Email = strings.ToLower(strings.TrimSpace(provider.Email))
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

	if len(provider.Siret) != 14 || strings.ContainsAny(provider.Siret, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {
		http.Error(response, "Le numéro SIRET doit contenir exactement 14 chiffres.", http.StatusConflict)
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
		provider.Siret, provider.Prenom, provider.Nom, provider.Email,
		string(hashedPassword), provider.DateNaissance, provider.NumTelephone,
		provider.IdCategorie, provider.Tarifs,
	)

	if err != nil {
		http.Error(response, "Erreur base de données : "+err.Error(), http.StatusBadRequest)
		return
	}

	provider.ID, _ = res.LastInsertId()
	provider.Mdp = ""

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(provider)
}

func LoginPrestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var creds models.LoginCredentials
	if err := json.NewDecoder(request.Body).Decode(&creds); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	var provider models.Prestataire
	var hashedPassword string

	row := db.DB.QueryRow("SELECT id_prestataire, email, mdp, status FROM PRESTATAIRE WHERE email = ?", creds.Email)
	err := row.Scan(&provider.ID, &provider.Email, &hashedPassword, &provider.Status)

	if err != nil {
		http.Error(response, "Email ou mot de passe incorrect", http.StatusUnauthorized)
		return
	}

	err = bcrypt.CompareHashAndPassword([]byte(hashedPassword), []byte(creds.Mdp))
	if err != nil {
		http.Error(response, "Email ou mot de passe incorrect", http.StatusUnauthorized)
		return
	}

	if provider.Status == "en attente" {
		http.Error(response, "Votre compte est en cours de validation.", http.StatusForbidden)
		return
	} else if provider.Status == "refusé" {
		http.Error(response, "Votre demande d'inscription a été refusée.", http.StatusForbidden)
		return
	}

	dummyUser := models.Utilisateur{ID: provider.ID, Email: provider.Email, Statut: "prestataire"}
	tokenString, err := generateJWT(dummyUser)
	if err != nil {
		http.Error(response, "Erreur lors de la création de la session", http.StatusInternalServerError)
		return
	}

	cookie := http.Cookie{
		Name:     "provider_token",
		Value:    tokenString,
		Expires:  time.Now().Add(24 * time.Hour),
		HttpOnly: true, 
		Path:     "/",
	}
	http.SetCookie(response, &cookie)

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{"message": "Connexion réussie"})
}

func LogoutPrestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	cookie := http.Cookie{
		Name:     "provider_token",
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

func MePrestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
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
		return jwtKey, nil
	})

	if err != nil || !token.Valid {
		http.Error(response, "Session invalide", http.StatusUnauthorized)
		return
	}

	query := `
		SELECT p.id_prestataire, IFNULL(p.siret,''), IFNULL(p.nom,''), IFNULL(p.prenom,''), p.email, 
		       IFNULL(p.num_telephone,''), IFNULL(p.date_naissance,''), p.status, IFNULL(p.motif_refus,''),
		       IFNULL(p.tarifs,0), IFNULL(p.id_categorie,0), IFNULL(c.nom,'') as categorie_nom
		FROM PRESTATAIRE p
		LEFT JOIN CATEGORIE c ON p.id_categorie = c.id_categorie
		WHERE p.id_prestataire = ?
	`

	var provider models.Prestataire
	errDB := db.DB.QueryRow(query, claims.UserID).Scan(
		&provider.ID, &provider.Siret, &provider.Nom, &provider.Prenom, &provider.Email,
		&provider.NumTelephone, &provider.DateNaissance, &provider.Status, &provider.MotifRefus,
		&provider.Tarifs, &provider.IdCategorie, &provider.CategorieNom,
	)

	if errDB != nil {
		http.Error(response, "Prestataire introuvable", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(provider)
}