package providers

import (
	"encoding/json"
	"html"
	"net/http"
	"regexp"
	"strings"
	"time"

	"main/auth"
	"main/db"
	"main/models"
	"main/utils"
	"os"

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

	var countPresta int
	checkPrestaQuery := `SELECT COUNT(*) FROM PRESTATAIRE WHERE email = ? OR num_telephone = ? OR siret = ?`
	err := db.DB.QueryRow(checkPrestaQuery, provider.Email, provider.NumTelephone, provider.Siret).Scan(&countPresta)
	if err != nil {
		http.Error(response, "Erreur lors de la vérification des données", http.StatusInternalServerError)
		return
	}
	if countPresta > 0 {
		http.Error(response, "Cet email, téléphone ou numéro SIRET est déjà utilisé par un autre prestataire.", http.StatusConflict)
		return
	}

	var countUser int
	checkUserQuery := `SELECT COUNT(*) FROM UTILISATEUR WHERE email = ? OR num_telephone = ?`
	err = db.DB.QueryRow(checkUserQuery, provider.Email, provider.NumTelephone).Scan(&countUser)
	if err != nil {
		http.Error(response, "Erreur lors de la vérification des données (Seniors)", http.StatusInternalServerError)
		return
	}
	if countUser > 0 {
		http.Error(response, "Cet email ou numéro de téléphone est déjà utilisé par un compte client/senior.", http.StatusConflict)
		return
	}

	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(provider.Mdp), bcrypt.DefaultCost)
	if err != nil {
		http.Error(response, "Erreur lors du hashage du mot de passe", http.StatusInternalServerError)
		return
	}

	queryUser := `INSERT INTO PRESTATAIRE (siret, prenom, nom, email, mdp, date_naissance, num_telephone, id_categorie, date_creation) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())`

	res, err := db.DB.Exec(queryUser,
		provider.Siret, provider.Prenom, provider.Nom, provider.Email,
		string(hashedPassword), provider.DateNaissance, provider.NumTelephone,
		provider.IdCategorie,
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

	dummyUser := models.Utilisateur{ID: provider.ID, Email: provider.Email, Statut: "prestataire"}
	tokenString, err := auth.GenerateJWT(dummyUser)
	if err != nil {
		http.Error(response, "Erreur lors de la création de la session", http.StatusInternalServerError)
		return
	}

	cookie := http.Cookie{
		Name:     "provider_token",
		Value:    tokenString,
		Expires:  time.Now().Add(24 * time.Hour),
		Path:     "/",
		Domain:   os.Getenv("COOKIE_DOMAIN"),
		HttpOnly: true,
		Secure:   true,
		SameSite: http.SameSiteNoneMode,
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
		Path:     "/",
		Domain:   os.Getenv("COOKIE_DOMAIN"),
		HttpOnly: true,
		Secure:   true,
		SameSite: http.SameSiteNoneMode,
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
		return auth.JwtKey, nil
	})

	if err != nil || !token.Valid {
		http.Error(response, "Session invalide", http.StatusUnauthorized)
		return
	}

	query := `
		SELECT p.id_prestataire, IFNULL(p.siret,''), IFNULL(p.nom,''), IFNULL(p.prenom,''), p.email, 
		       IFNULL(p.num_telephone,''), IFNULL(p.date_naissance,''), p.status, IFNULL(p.motif_refus,''),
		       IFNULL(p.id_categorie,0), IFNULL(id_abonnement, 0), IFNULL(date_fin_boost, ''), IFNULL(c.nom,'') as categorie_nom, IFNULL(stripe_account_id, '')
		FROM PRESTATAIRE p
		LEFT JOIN CATEGORIE c ON p.id_categorie = c.id_categorie
		WHERE p.id_prestataire = ?
	`

	var provider models.Prestataire
	errDB := db.DB.QueryRow(query, claims.UserID).Scan(
		&provider.ID, &provider.Siret, &provider.Nom, &provider.Prenom, &provider.Email,
		&provider.NumTelephone, &provider.DateNaissance, &provider.Status, &provider.MotifRefus,
		&provider.IdCategorie, &provider.IdAbonnement, &provider.DateFinBoost, &provider.CategorieNom, &provider.IdStripeAccount,
	)

	if errDB != nil {
		http.Error(response, "Prestataire introuvable", http.StatusNotFound)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(provider)
}

func UpdatePrestataire(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	cookie, err := request.Cookie("provider_token")
	if err != nil {
		if err == http.ErrNoCookie {
			http.Error(response, "Non authentifié (aucun cookie)", http.StatusUnauthorized)
			return
		}
		http.Error(response, "Erreur serveur", http.StatusBadRequest)
		return
	}

	tokenString := cookie.Value
	claims := &models.Claims{}
	token, err := jwt.ParseWithClaims(tokenString, claims, func(token *jwt.Token) (interface{}, error) {
		if _, ok := token.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, jwt.ErrSignatureInvalid
		}
		return auth.JwtKey, nil
	})

	if err != nil || !token.Valid {
		http.Error(response, "Session invalide ou expirée", http.StatusUnauthorized)
		return
	}

	providerID := claims.UserID

	var p models.Prestataire
	if err := json.NewDecoder(request.Body).Decode(&p); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	p.Prenom = html.EscapeString(strings.TrimSpace(p.Prenom))
	p.Nom = html.EscapeString(strings.TrimSpace(p.Nom))
	p.Email = strings.ToLower(strings.TrimSpace(p.Email))
	p.NumTelephone = strings.TrimSpace(p.NumTelephone)
	p.Siret = html.EscapeString(strings.TrimSpace(p.Siret))

	if strings.Contains(p.Prenom, " ") || strings.Contains(p.Nom, " ") || strings.Contains(p.Email, " ") || strings.Contains(p.NumTelephone, " ") || strings.Contains(p.Siret, " ") {
		http.Error(response, "Les espaces ne sont pas autorisés pour ces champs.", http.StatusConflict)
		return
	}

	if strings.ContainsAny(p.Prenom, "0123456789") || strings.ContainsAny(p.Nom, "0123456789") {
		http.Error(response, "Le nom et le prénom ne doivent pas contenir de chiffres.", http.StatusConflict)
		return
	}

	if len(p.Prenom) < 2 || len(p.Prenom) > 50 || len(p.Nom) < 2 || len(p.Nom) > 50 {
		http.Error(response, "Le nom et le prénom doivent contenir entre 2 et 50 caractères", http.StatusConflict)
		return
	}

	if len(p.NumTelephone) != 10 {
		http.Error(response, "Le numéro de téléphone est invalide (10 chiffres attendus).", http.StatusConflict)
		return
	}

	if len(p.Siret) != 14 || strings.ContainsAny(p.Siret, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {
		http.Error(response, "Le numéro SIRET doit contenir exactement 14 chiffres.", http.StatusConflict)
		return
	}

	var countPresta int
	errPresta := db.DB.QueryRow(`SELECT COUNT(*) FROM PRESTATAIRE WHERE (email = ? OR num_telephone = ? OR siret = ?) AND id_prestataire != ?`, p.Email, p.NumTelephone, p.Siret, providerID).Scan(&countPresta)
	if errPresta != nil {
		http.Error(response, "Erreur lors de la vérification des données (Prestataires)", http.StatusInternalServerError)
		return
	}
	if countPresta > 0 {
		http.Error(response, "Cet email, numéro de téléphone ou SIRET est déjà utilisé par un autre prestataire.", http.StatusConflict)
		return
	}

	var countUser int
	errUser := db.DB.QueryRow(`SELECT COUNT(*) FROM UTILISATEUR WHERE email = ? OR num_telephone = ?`, p.Email, p.NumTelephone).Scan(&countUser)
	if errUser != nil {
		http.Error(response, "Erreur lors de la vérification des données (Seniors)", http.StatusInternalServerError)
		return
	}
	if countUser > 0 {
		http.Error(response, "Cet email ou numéro de téléphone est déjà utilisé par un compte client/senior.", http.StatusConflict)
		return
	}

	var query string
	var args []interface{}

	if p.Mdp != "" {
		hashedPassword, errHash := bcrypt.GenerateFromPassword([]byte(p.Mdp), bcrypt.DefaultCost)
		if errHash != nil {
			http.Error(response, "Erreur lors du hashage du mot de passe", http.StatusInternalServerError)
			return
		}

		query = `UPDATE PRESTATAIRE 
                 SET prenom = ?, nom = ?, email = ?, num_telephone = ?, siret = ?, id_categorie = ?, mdp = ? 
                 WHERE id_prestataire = ?`
		args = []interface{}{p.Prenom, p.Nom, p.Email, p.NumTelephone, p.Siret, p.IdCategorie, string(hashedPassword), providerID}
	} else {
		query = `UPDATE PRESTATAIRE 
                 SET prenom = ?, nom = ?, email = ?, num_telephone = ?, siret = ?, id_categorie = ? 
                 WHERE id_prestataire = ?`
		args = []interface{}{p.Prenom, p.Nom, p.Email, p.NumTelephone, p.Siret, p.IdCategorie, providerID}
	}

	_, err = db.DB.Exec(query, args...)
	if err != nil {
		http.Error(response, "Erreur lors de la mise à jour du profil : "+err.Error(), http.StatusBadRequest)
		return
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(map[string]string{
		"message": "Profil mis à jour avec succès",
	})
}
