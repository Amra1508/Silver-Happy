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
	ID       int64  `json:"id"`
	Question string `json:"question"`
	Reponse  string `json:"reponse"`
}

type Produit struct {
	ID          int64   `json:"id"`
	Nom         string  `json:"nom"`
	Description string  `json:"description"`
	Prix        float64 `json:"prix"`
	Stock       int64   `json:"stock"`
	Image       string  `json:"image"`
}

type Service struct {
	ID            int    `json:"id"`
	Nom           string `json:"nom"`
	Description   string `json:"description"`
	Disponibilite int    `json:"disponibilite"`
	IdUtilisateur int    `json:"id_utilisateur"`
}

type Conseil struct {
	ID          int64  `json:"id"`
	Titre       string `json:"titre"`
	Description string `json:"description"`
	Date        string `json:"date"`
	Categorie   string `json:"categorie"`
}

type Prestataire struct {
	ID             int64   `json:"id"`
	Siret          string  `json:"siret"`
	Nom            string  `json:"nom"`
	Prenom         string  `json:"prenom"`
	Email          string  `json:"email"`
	NumTelephone   string  `json:"num_telephone"`
	DateNaissance  string  `json:"date_naissance"`
	Status         string  `json:"status"`
	MotifRefus     string  `json:"motif_refus"`
	Tarifs         float64 `json:"tarifs"`
	TypePrestation string  `json:"type_prestation"`
	DateCreation   string  `json:"date_creation"`
}

type Document struct {
	ID   int64  `json:"id_document"`
	Type string `json:"type"`
	Lien string `json:"lien"`
}

type Count struct {
	Count int `json:"count"`
}
