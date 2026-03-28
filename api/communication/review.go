package communication

import (
	"encoding/json"
	"fmt"
	"net/http"
	"strings"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_Avis(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") { return }

    query := request.URL.Query()
    limitStr := query.Get("limit")
    pageStr := query.Get("page")

    limit := 10
    offset := 0
    page := 1

    if limitStr != "" { fmt.Sscanf(limitStr, "%d", &limit) }
    if pageStr != "" {
        fmt.Sscanf(pageStr, "%d", &page)
        offset = (page - 1) * limit
    }

    var total int
    db.DB.QueryRow("SELECT COUNT(*) FROM AVIS").Scan(&total)

    // La requête avec LEFT JOIN
    querySQL := `
        SELECT 
            a.id_avis, a.description, a.titre, a.note, a.date, a.categorie, a.id_prestataire,
            IFNULL(p.nom, '') as nom_presta, 
            IFNULL(p.prenom, '') as prenom_presta
        FROM AVIS a
        LEFT JOIN PRESTATAIRE p ON a.id_prestataire = p.id_prestataire
        ORDER BY a.date DESC
        LIMIT ? OFFSET ?`

    rows, errorFetch := db.DB.Query(querySQL, limit, offset)
    if errorFetch != nil {
        http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    var tabAvis []map[string]interface{}
    for rows.Next() {
        // On utilise sql.NullInt64 pour id_prestataire car il peut être NULL
        var id, note int
        var desc, titre, date, cat, nom, prenom string
        var idPresta interface{} // On utilise interface{} pour accepter NULL ou INT

        // Scan des colonnes
        if err := rows.Scan(&id, &desc, &titre, &note, &date, &cat, &idPresta, &nom, &prenom); err != nil {
            fmt.Println("Erreur Scan:", err) // Pour debug si besoin
            continue 
        }

        tabAvis = append(tabAvis, map[string]interface{}{
            "id_avis":            id,
            "description":        desc,
            "titre":              titre,
            "note":               note,
            "date":               date,
            "categorie":          cat,
            "id_prestataire":     idPresta,
            "nom_prestataire":    nom,
            "prenom_prestataire": prenom,
        })
    }

    dataResponse := map[string]interface{}{
        "data":        tabAvis,
        "total":       total,
        "currentPage": page,
        "totalPages":  (total + limit - 1) / limit,
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(dataResponse)
}

func Read_One_Avis(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    id := request.PathValue("id")

    sqlQuery := `
        SELECT 
            a.id_avis, a.description, a.titre, a.note, a.date, a.categorie, a.id_prestataire,
            IFNULL(p.nom, '') as nom_presta, 
            IFNULL(p.prenom, '') as prenom_presta
        FROM AVIS a
        LEFT JOIN PRESTATAIRE p ON a.id_prestataire = p.id_prestataire
        WHERE a.id_avis = ?`

    var idAvis, note int
    var desc, titre, date, cat, nom, prenom string
    var idPresta interface{}

    err := db.DB.QueryRow(sqlQuery, id).Scan(
        &idAvis, &desc, &titre, &note, &date, &cat, &idPresta, &nom, &prenom,
    )
    
    if err != nil {
        http.Error(response, "Avis non trouvé", http.StatusNotFound)
        return
    }

    avisMap := map[string]interface{}{
        "id_avis":            idAvis,
        "description":        desc,
        "titre":              titre,
        "note":               note,
        "date":               date,
        "categorie":          cat,
        "id_prestataire":     idPresta,
        "nom_prestataire":    nom,
        "prenom_prestataire": prenom,
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(avisMap)
}

func Create_Avis(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var avis models.Avis
	if err := json.NewDecoder(request.Body).Decode(&avis); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	avis.Titre = strings.TrimSpace(avis.Titre)
	avis.Description = strings.TrimSpace(avis.Description)
	avis.Categorie = strings.TrimSpace(avis.Categorie)

	if avis.Titre == "" || avis.Description == "" || avis.Categorie == "" {
		http.Error(response, "Les champs ne peuvent pas être vides.", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO AVIS (description, titre, note, categorie, id_prestataire) VALUES (?, ?, ?, ?, ?)", avis.Description, avis.Titre, avis.Note, avis.Categorie, avis.Prestataire)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	avis.ID = id

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(avis)
}