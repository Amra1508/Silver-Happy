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
	PremiereConnexion int64  `json:"premiere_connexion"`
	IdAbonnement 	  int64  `json:"id_abonnement"`
	DebutAbonnement   string `json:"debut_abonnement"`
	MotifBannissement string `json:"motif_bannissement"`
	DureeBannissement int  `json:"duree_bannissement"`
	TypePaiement string `json:"type_paiement"`
	EstLu 		   int     `json:"est_lu"`
}

type LoginCredentials struct {
	Email               string `json:"email"`
	Mdp                 string `json:"mdp"`
	CfTurnstileResponse string `json:"cf-turnstile-response"`
}

type Claims struct {
	UserID int64  `json:"user_id"`
	Email  string `json:"email"`
	Statut string `json:"statut"`
	jwt.RegisteredClaims
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
    ID          int    `json:"id_service"`
    Nom         string `json:"nom"`
    Description string `json:"description"`
	IDCategorie *int   `json:"id_categorie"`
	CategorieNom string `json:"categorie_nom"`
}

type UserReservation struct {
    IdReservation int    `json:"id_reservation"`
    IdService     int    `json:"id_service"`
    Nom           string `json:"nom"`
    Description   string `json:"description"`
    DateHeure     string `json:"date_heure"`
}

type Conseil struct {
    ID          int64  `json:"id"`
    Titre       string `json:"titre"`
    Description string `json:"description"`
    Date        string `json:"date"`
    Categorie   string `json:"categorie"`
    Likes       int    `json:"likes"`
    IsLiked     bool   `json:"is_liked"`
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

type Revenue struct {
	Date  string  `json:"date"`
	Total float64 `json:"total"`
}

type Message struct {
	ID              int64  `json:"id"`
	Contenu         string `json:"contenu"`
	Date            string `json:"date"`
	ID_Expediteur   int64  `json:"id_expediteur"`
	ID_Destinataire int64  `json:"id_destinataire"`
	Prénom          string `json:"prenom"`
	Nom             string `json:"nom"`
	Est_Lu			bool 	`json:"est_lu"`
}

type Evenement struct {
	ID          int    `json:"id_evenement"`
	Nom         string `json:"nom"`
	Description string `json:"description"`
	Lieu        string `json:"lieu"`
	NombrePlace int    `json:"nombre_place"`
	Image       string `json:"image"`
	DateDebut   string `json:"date_debut"`
	DateFin     string `json:"date_fin"`
	IDCategorie *int   `json:"id_categorie"`
	Prix        float64 `json:"prix"`
}

type Req struct {
        UserID         int    `json:"user_id"`
        TypeAbonnement string `json:"type_abonnement"`
        Periode        string `json:"periode"`         
        Tarif          int64  `json:"tarif"`
    }

type Avis struct {
	ID          int64  `json:"id"`
	Description string `json:"description"`
	Titre       string `json:"titre"`
	Date        string `json:"date"`
	Note   		int64  `json:"note"`
	Categorie   string  `json:"categorie"`
	Prestataire *int64  `json:"id_prestataire"`
	Utilisateur int64  `json:"id_utilisateur"`
}

type Planning struct {
	ID          string `json:"id"`
	Titre       string `json:"titre"`
	Debut       string `json:"debut"`
	Fin         string `json:"fin,omitempty"`
	Description string `json:"description"`
	Lieu        string `json:"lieu"`
	Type        string `json:"type"`
	Couleur     string `json:"couleur"`
}

type Categorie struct {
    ID          int    `json:"id_categorie"`
    Nom         string `json:"nom"`
    Description string `json:"description"`
}

type Panier struct {
        IdPanier int     `json:"id_panier"`
		IdProduit int    `json:"id_produit"`
        Quantite int     `json:"quantite"`
        Nom      string  `json:"nom"`
        Prix     float64 `json:"prix"`
        Image    string  `json:"image"`
}

type Livraison struct {
    UserID  int    `json:"user_id"`
    Code  	string `json:"code"`   
    Adresse string `json:"adresse"`  
    CP      string `json:"cp"`       
    Ville   string `json:"ville"`    
}