package auth

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"net/http"
	"strings"
	"time"

	"main/db"
	"main/models"
	"main/utils"

	"github.com/golang-jwt/jwt/v5"
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

    user.Prenom = strings.TrimSpace(user.Prenom)
    user.Nom = strings.TrimSpace(user.Nom)
    user.Email = strings.TrimSpace(user.Email)
    user.NumTelephone = strings.TrimSpace(user.NumTelephone)
    user.CodePostal = strings.TrimSpace(user.CodePostal)
    user.Pays = strings.TrimSpace(user.Pays)
    user.Ville = strings.TrimSpace(user.Ville)
    user.Adresse = strings.TrimSpace(user.Adresse)

    if strings.Contains(user.Prenom, " ") || strings.Contains(user.Nom, " ") || strings.Contains(user.Email, " ") || strings.Contains(user.NumTelephone, " ") || strings.Contains(user.CodePostal, " ") {
        http.Error(response, "Les espaces ne sont pas autorisés pour ces champs.", http.StatusConflict)
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
        http.Error(response, "Le numéro de téléphone est invalide.", http.StatusConflict)
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
        http.Error(response, "Le code postal est invalide.", http.StatusConflict)
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

func Me(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    cookie, err := request.Cookie("session_token")
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
        return jwtKey, nil
    })

    if err != nil || !token.Valid {
        http.Error(response, "Session invalide ou expirée", http.StatusUnauthorized)
        return
    }

    query := `
        SELECT u.id_utilisateur, u.nom, u.prenom, u.email, 
               u.num_telephone, u.date_naissance, u.statut, u.premiere_connexion,
               u.date_creation, u.motif_bannissement, u.duree_bannissement, u.id_abonnement, u.debut_abonnement,
               a.rue, a.ville, a.code_postal, a.pays
        FROM UTILISATEUR u
        LEFT JOIN ADRESSE a ON u.id_adresse = a.id_adresse
        WHERE u.id_utilisateur = ?
    `
    
    var user models.Utilisateur
    
    var numTel, dateNaiss, dateCrea, motifBan, rue, ville, cp, pays, debutAbonnement sql.NullString
    var dureeBan, idAbonnement, premiereConnexion sql.NullInt64 

    errDB := db.DB.QueryRow(query, claims.UserID).Scan(
        &user.ID, &user.Nom, &user.Prenom, &user.Email,
        &numTel, &dateNaiss, &user.Statut, &premiereConnexion, &dateCrea, &motifBan, &dureeBan, &idAbonnement, &debutAbonnement,
        &rue, &ville, &cp, &pays,
    )

    if errDB != nil {
        fmt.Println("Erreur DB Auth Me :", errDB)
        if errDB == sql.ErrNoRows {
            http.Error(response, "Utilisateur introuvable", http.StatusNotFound)
        } else {
            http.Error(response, "Erreur base de données", http.StatusInternalServerError)
        }
        return
    }

    user.NumTelephone = numTel.String
    user.DateNaissance = dateNaiss.String
    user.DateCreation = dateCrea.String
    user.MotifBannissement = motifBan.String
    user.DureeBannissement = int(dureeBan.Int64)
    user.Adresse = rue.String
    user.Ville = ville.String
    user.CodePostal = cp.String
    user.Pays = pays.String
    user.DebutAbonnement = debutAbonnement.String
    
    user.PremiereConnexion = premiereConnexion.Int64 
    user.IdAbonnement = idAbonnement.Int64

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(user)
}

func Update(response http.ResponseWriter, request *http.Request) {

    if utils.HandleCORS(response, request, "PUT") {
        return
    }

    cookie, err := request.Cookie("session_token")
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
        return jwtKey, nil
    })

    if err != nil || !token.Valid {
        http.Error(response, "Session invalide ou expirée", http.StatusUnauthorized)
        return
    }

    userID := claims.UserID

    var user models.Utilisateur
    if err := json.NewDecoder(request.Body).Decode(&user); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    user.Prenom = strings.TrimSpace(user.Prenom)
    user.Nom = strings.TrimSpace(user.Nom)
    user.Email = strings.TrimSpace(user.Email)
    user.NumTelephone = strings.TrimSpace(user.NumTelephone)
    user.CodePostal = strings.TrimSpace(user.CodePostal)
    user.Pays = strings.TrimSpace(user.Pays)
    user.Ville = strings.TrimSpace(user.Ville)
    user.Adresse = strings.TrimSpace(user.Adresse)

    if strings.Contains(user.Prenom, " ") || strings.Contains(user.Nom, " ") || strings.Contains(user.Email, " ") || strings.Contains(user.NumTelephone, " ") || strings.Contains(user.CodePostal, " ") {
        http.Error(response, "Les espaces ne sont pas autorisés pour ces champs.", http.StatusConflict)
        return
    }

    if strings.ContainsAny(user.Prenom, "0123456789") || strings.ContainsAny(user.Nom, "0123456789") || strings.ContainsAny(user.Pays, "0123456789") || strings.ContainsAny(user.Ville, "0123456789") {
        http.Error(response, "Les informations que vous avez saisi sont erronées.", http.StatusConflict)
        return
    }

    if len(user.Prenom) < 2 || len(user.Prenom) > 50 || len(user.Nom) < 2 || len(user.Nom) > 50 {
        http.Error(response, "Le nom et le prénom doivent contenir entre 2 et 50 caractères", http.StatusConflict)
        return
    }

    if len(user.NumTelephone) != 10 || len(user.CodePostal) != 5 {
        http.Error(response, "Le numéro de téléphone ou le code postal est invalide.", http.StatusConflict)
        return
    }

    tx, err := db.DB.Begin()
    if err != nil {
        http.Error(response, "Erreur serveur (Transaction)", http.StatusInternalServerError)
        return
    }

    var idAdresse int
    err = tx.QueryRow(`SELECT id_adresse FROM UTILISATEUR WHERE id_utilisateur = ?`, userID).Scan(&idAdresse)
    if err != nil {
        tx.Rollback()
        if err == sql.ErrNoRows {
            http.Error(response, "Utilisateur introuvable", http.StatusNotFound)
        } else {
            http.Error(response, "Erreur lors de la récupération des données", http.StatusInternalServerError)
        }
        return
    }

    queryAddr := `UPDATE ADRESSE SET rue = ?, ville = ?, code_postal = ?, pays = ? WHERE id_adresse = ?`
    _, err = tx.Exec(queryAddr, user.Adresse, user.Ville, user.CodePostal, user.Pays, idAdresse)
    if err != nil {
        tx.Rollback()
        http.Error(response, "Erreur lors de la mise à jour de l'adresse", http.StatusInternalServerError)
        return
    }

    var queryUser string
    var args []interface{}

    if user.Mdp != "" {
        hashedPassword, errHash := bcrypt.GenerateFromPassword([]byte(user.Mdp), bcrypt.DefaultCost)
        if errHash != nil {
            tx.Rollback()
            http.Error(response, "Erreur lors du hashage du mot de passe", http.StatusInternalServerError)
            return
        }
        queryUser = `UPDATE UTILISATEUR SET prenom = ?, nom = ?, email = ?, date_naissance = ?, num_telephone = ?, mdp = ? WHERE id_utilisateur = ?`
        args = []interface{}{user.Prenom, user.Nom, user.Email, user.DateNaissance, user.NumTelephone, string(hashedPassword), userID}
    } else {
        queryUser = `UPDATE UTILISATEUR SET prenom = ?, nom = ?, email = ?, date_naissance = ?, num_telephone = ? WHERE id_utilisateur = ?`
        args = []interface{}{user.Prenom, user.Nom, user.Email, user.DateNaissance, user.NumTelephone, userID}
    }

    _, err = tx.Exec(queryUser, args...)
    if err != nil {
        tx.Rollback()
        if strings.Contains(err.Error(), "Duplicate entry") {
            http.Error(response, "Cette adresse email est déjà utilisée par un autre compte.", http.StatusConflict)
        } else {
            http.Error(response, "Erreur lors de la mise à jour du profil : "+err.Error(), http.StatusBadRequest)
        }
        return
    }

    if err := tx.Commit(); err != nil {
        http.Error(response, "Erreur lors de la validation finale", http.StatusInternalServerError)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{
        "message": "Profil mis à jour avec succès",
    })
}

func TutorialSeen(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") {
        return
    }

    cookie, err := request.Cookie("session_token")
    if err != nil {
        http.Error(response, "Non authentifié", http.StatusUnauthorized)
        return
    }

    claims := &models.Claims{}
    token, _ := jwt.ParseWithClaims(cookie.Value, claims, func(t *jwt.Token) (interface{}, error) {
        return jwtKey, nil
    })

    if !token.Valid {
        http.Error(response, "Session invalide", http.StatusUnauthorized)
        return
    }

    _, errDB := db.DB.Exec("UPDATE UTILISATEUR SET premiere_connexion = 0 WHERE id_utilisateur = ?", claims.UserID)
    if errDB != nil {
        http.Error(response, "Erreur base de données", http.StatusInternalServerError)
        return
    }

    response.WriteHeader(http.StatusOK)
    response.Write([]byte(`{"message": "Tutoriel validé"}`))
}
