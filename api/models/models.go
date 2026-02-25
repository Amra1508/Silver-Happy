package models

import "github.com/golang-jwt/jwt/v5"

type Utilisateur struct {
	ID                int64  `json:"id"`
	Prenom            string `json:"prenom"`
	Nom               string `json:"nom"`
	DateNaissance     string `json:"date_naissance"`
	NumTelephone      string `json:"num_telephone"`
	Email             string `json:"email"`
	Mdp               string `json:"mdp,omitempty"`
	Pays              string `json:"pays"`
	Adresse           string `json:"adresse"`
	Ville             string `json:"ville"`
	CodePostal        string `json:"code_postal"`
	Statut            string `json:"statut"`
	DateCreation      string `json:"date_creation"`
	PremiereConnexion bool   `json:"premiere_connexion"`
	MotifBannissement string `json:"motif_bannisement"`
	DureeBannissement int    `json:"duree_bannissement"`
}

type LoginCredentials struct {
	Email string `json:"email"`
	Mdp   string `json:"mdp"`
}

type Claims struct {
	UserID int64  `json:"user_id"`
	Email  string `json:"email"`
	Statut string `json:"statut"`
	jwt.RegisteredClaims
}

type Captcha struct {
	ID int64 `json:"id"`
	Question string `json:"question"`
	Reponse string `json:"reponse"`
}