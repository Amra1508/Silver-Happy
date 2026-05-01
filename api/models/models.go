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
	IdAbonnement      int64  `json:"id_abonnement"`
	DebutAbonnement   string `json:"debut_abonnement"`
	MotifBannissement string `json:"motif_bannissement"`
	DureeBannissement int    `json:"duree_bannissement"`
	TypePaiement      string `json:"type_paiement"`
	EstLu             int    `json:"est_lu"`
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
	ID                int     `json:"id_service"`
	Nom               string  `json:"nom"`
	Description       string  `json:"description"`
	IDCategorie       *int    `json:"id_categorie"`
	CategorieNom      string  `json:"categorie_nom"`
	IDPrestataire     int64   `json:"id_prestataire"`
	Prix              float64 `json:"prix"`
	IsBoosted         bool    `json:"is_boosted"`
	PrestataireNom    string  `json:"prestataire_nom"`
	PrestatairePrenom string  `json:"prestataire_prenom"`
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
	ID              int64  `json:"id"`
	Siret           string `json:"siret"`
	Nom             string `json:"nom"`
	Prenom          string `json:"prenom"`
	Email           string `json:"email"`
	Mdp             string `json:"mdp,omitempty"`
	NumTelephone    string `json:"num_telephone"`
	DateNaissance   string `json:"date_naissance"`
	Status          string `json:"status"`
	MotifRefus      string `json:"motif_refus"`
	IdCategorie     int    `json:"id_categorie"`
	IdAbonnement    int    `json:"id_abonnement"`
	CategorieNom    string `json:"categorie,omitempty"`
	DateCreation    string `json:"date_creation"`
	DateFinBoost    string `json:"date_fin_boost"`
	IdStripeAccount string `json:"stripe_account_id"`
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
	ID              int64    `json:"id"`
	Contenu         string   `json:"contenu"`
	Date            string   `json:"date"`
	ID_Expediteur   int64    `json:"id_expediteur"`
	ID_Destinataire int64    `json:"id_destinataire"`
	Prénom          string   `json:"prenom"`
	Nom             string   `json:"nom"`
	Est_Lu          bool     `json:"est_lu"`
	Expediteur      bool     `json:"expediteur"`
	ID_Service      *int     `json:"id_service"`
	Prix_Propose    *float64 `json:"prix_propose"`
	ID_Dispo        *int     `json:"id_dispo"`
	Etat_Offre      *string  `json:"etat_offre"`
}

type Evenement struct {
	ID          int     `json:"id_evenement"`
	Nom         string  `json:"nom"`
	Description string  `json:"description"`
	Lieu        string  `json:"lieu"`
	NombrePlace int     `json:"nombre_place"`
	Image       string  `json:"image"`
	DateDebut   string  `json:"date_debut"`
	DateFin     string  `json:"date_fin"`
	IDCategorie *int    `json:"id_categorie"`
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
	Note        int64  `json:"note"`
	Categorie   string `json:"categorie"`
	Prestataire *int64 `json:"id_prestataire"`
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
	IdPanier  int     `json:"id_panier"`
	IdProduit int     `json:"id_produit"`
	Quantite  int     `json:"quantite"`
	Nom       string  `json:"nom"`
	Prix      float64 `json:"prix"`
	Image     string  `json:"image"`
}

type Livraison struct {
	UserID  int    `json:"user_id"`
	Code    string `json:"code"`
	Adresse string `json:"adresse"`
	CP      string `json:"cp"`
	Ville   string `json:"ville"`
}

type Recherche struct {
	ID          int     `json:"id"`
	Titre       string  `json:"titre"`
	Description string  `json:"description"`
	Prix        float64 `json:"prix"`
	Lien        string  `json:"lien"`
}

type RechercheGlobale struct {
	Produits   []Recherche `json:"produits"`
	Evenements []Recherche `json:"evenements"`
	Services   []Recherche `json:"services"`
	Avis       []Recherche `json:"avis"`
	Conseils   []Recherche `json:"conseils"`
}

type Code struct {
	IdCode         int    `json:"id_reduction"`
	Code           string `json:"code"`
	Valeur         int    `json:"valeur"`
	Type           string `json:"type"`
	DateExpiration string `json:"date_expiration"`
}

type BoostRequest struct {
	ProviderID int    `json:"provider_id"`
	TypeBoost  string `json:"type_boost"`
	TargetID   int    `json:"target_id"`
}

type InvoiceResponse struct {
	ID           int     `json:"id_paiement"`
	DatePaiement string  `json:"date_paiement"`
	Prix         float64 `json:"prix"`
	Statut       string  `json:"statut"`
	URLFacture   string  `json:"url_facture"`
	Description  string  `json:"description"`
	URLContrat   string  `json:"url_contrat"`
}

type InvoiceLine struct {
	Type        string
	Description string
	Qty         int
	UnitPrice   float64
	Total       float64
	Info1       string
	Info2       string
}

type RevenuDetail struct {
	Date        string  `json:"date"`
	Commandes   float64 `json:"commandes"`
	Abonnements float64 `json:"abonnements"`
}

type Participant struct {
	ID     int    `json:"id"`
	Nom    string `json:"nom"`
	Prenom string `json:"prenom"`
	Email  string `json:"email"`
}

type FactureMensuelle struct {
	IDFacture       int     `json:"id_facture"`
	MontantBrut     float64 `json:"montant_brut"`
	FraisPlateforme float64 `json:"frais_plateforme"`
	MontantNet      float64 `json:"montant_net"`
	MoisAnnee       string  `json:"mois_annee"`
	Date            string  `json:"date"`
	Statut          string  `json:"statut"`
}

type FactureDetail struct {
    Date    string
    Libelle string
    Type    string
    Prix    float64
}

type CreationDisponibilite struct {
	JourSemaine    int    `json:"jour_semaine"`
	HeureDebut     string `json:"heure_debut"`
	HeureFin       string `json:"heure_fin"`
	DureeMinutes   int    `json:"duree_minutes"`
	PauseDebut     string `json:"pause_debut"`
	PauseFin       string `json:"pause_fin"`
	ExclusionDebut string `json:"exclusion_debut"`
	ExclusionFin   string `json:"exclusion_fin"`
	RecurrenceMois int    `json:"recurrence_mois"`
}

type Disponibilite struct {
	ID            int    `json:"id_disponibilite"`
	IDPrestataire int    `json:"id_prestataire"`
	DateHeure     string `json:"date_heure"`
	EstReserve    bool   `json:"est_reserve"`
}
