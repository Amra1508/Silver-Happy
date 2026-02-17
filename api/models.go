package main

type Utilisateur struct {
	ID                int64  `json:"id"`
	Prenom            string `json:"prenom"`
	Nom               string `json:"nom"`
	DateNaissance     string `json:"date_naissance"`
	Email             string `json:"email"`
	Mdp               string `json:"mdp,omitempty"`
	NumTelephone      string `json:"num_telephone"`
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