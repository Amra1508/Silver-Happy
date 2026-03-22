package services

import (
	"encoding/json"
	"fmt"
	"net/http"
	"strings"

	"main/db"
	"main/models"
	"main/utils"
)

func Read_Service(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    query := request.URL.Query()
    limitStr := query.Get("limit")
    pageStr := query.Get("page")

    limit := 10
    offset := 0
    page := 1

    if limitStr != "" {
        fmt.Sscanf(limitStr, "%d", &limit)
    }
    if pageStr != "" {
        fmt.Sscanf(pageStr, "%d", &page)
        offset = (page - 1) * limit
    }

    var total int
    db.DB.QueryRow("SELECT COUNT(*) FROM SERVICE").Scan(&total)

    sqlQuery := `
        SELECT s.id_service, s.nom, s.description, s.id_categorie, IFNULL(c.nom, 'Autre') as categorie_nom
        FROM SERVICE s
        LEFT JOIN CATEGORIE c ON s.id_categorie = c.id_categorie
        LIMIT ? OFFSET ?
    `

    rows, errorFetch := db.DB.Query(sqlQuery, limit, offset)
    if errorFetch != nil {
        http.Error(response, "Erreur lors de la récupération", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    var tabService []models.Service
    for rows.Next() {
        var service models.Service
        if err := rows.Scan(&service.ID, &service.Nom, &service.Description, &service.IDCategorie, &service.CategorieNom); err != nil {
            fmt.Printf("ERREUR SCAN SUR SERVICE: %v\n", err)
            continue
        }
        tabService = append(tabService, service)
    }

    if tabService == nil {
        tabService = []models.Service{}
    }

    dataResponse := map[string]interface{}{
        "data":        tabService,
        "total":       total,
        "currentPage": page,
        "totalPages":  (total + limit - 1) / limit,
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(dataResponse)
}

func Create_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "POST") {
		return
	}

	var service models.Service
	if err := json.NewDecoder(request.Body).Decode(&service); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	service.Nom = strings.TrimSpace(service.Nom)
	service.Description = strings.TrimSpace(service.Description)

	if service.Nom == "" || service.Description == "" {
		http.Error(response, "Le nom et la description sont requis.", http.StatusBadRequest)
		return
	}

	res, errorCreate := db.DB.Exec("INSERT INTO SERVICE (nom, description, id_categorie) VALUES (?, ?, ?)", service.Nom, service.Description, service.IDCategorie)
	if errorCreate != nil {
		http.Error(response, "Erreur lors de l'insertion", http.StatusInternalServerError)
		return
	}

	id, _ := res.LastInsertId()
	service.ID = int(id)

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusCreated)
	json.NewEncoder(response).Encode(service)
}

func Update_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "PUT") {
		return
	}

	id := request.PathValue("id")

	var service models.Service
	if err := json.NewDecoder(request.Body).Decode(&service); err != nil {
		http.Error(response, "Format JSON invalide", http.StatusBadRequest)
		return
	}

	service.Nom = strings.TrimSpace(service.Nom)
	service.Description = strings.TrimSpace(service.Description)

	res, err := db.DB.Exec("UPDATE SERVICE SET nom = ?, description = ?, id_categorie = ? WHERE id_service = ?", service.Nom, service.Description, service.IDCategorie, id)

	if err != nil {
		http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
		return
	}

	rowsAffected, _ := res.RowsAffected()
	if rowsAffected == 0 {
		http.Error(response, "Aucun service trouvé", http.StatusNotFound)
		return
	}

	response.WriteHeader(http.StatusOK)
}

func Delete_Service(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "DELETE") {
		return
	}

	id := request.PathValue("id")
	db.DB.Exec("DELETE FROM SERVICE WHERE id_service = ?", id)
	response.WriteHeader(http.StatusNoContent)
}

func Read_One_Service(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    id := request.PathValue("id")
    var service models.Service

    err := db.DB.QueryRow("SELECT id_service, nom, description FROM service WHERE id_service = ?", id).Scan(&service.ID, &service.Nom, &service.Description)

    if err != nil {
        http.Error(response, "Service non trouvé", http.StatusNotFound)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(service)
}

func Read_User_Services(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "GET") {
        return
    }

    idUser := request.PathValue("id")

    query := `
        SELECT r.id_reservation, s.id_service, s.nom, s.description, r.date_heure 
        FROM reservation_service r 
        JOIN service s ON r.id_service = s.id_service 
        WHERE r.id_utilisateur = ? 
        ORDER BY r.date_heure ASC
    `
    
    rows, err := db.DB.Query(query, idUser)
    if err != nil {
        http.Error(response, "Erreur base de données", http.StatusInternalServerError)
        return
    }
    defer rows.Close()

    var tabRes []models.UserReservation
    for rows.Next() {
        var res models.UserReservation
        if err := rows.Scan(&res.IdReservation, &res.IdService, &res.Nom, &res.Description, &res.DateHeure); err == nil {
            tabRes = append(tabRes, res)
        }
    }

    if tabRes == nil {
        tabRes = []models.UserReservation{}
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(tabRes)
}

func Register_Service(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") {
        return
    }

    idService := request.PathValue("id")
    
    var payload struct {
        IdUtilisateur int    `json:"id_utilisateur"`
        DateHeure     string `json:"date_heure"`
    }

    if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    if payload.IdUtilisateur == 0 {
        http.Error(response, "ID Utilisateur manquant", http.StatusBadRequest)
        return
    }
    if payload.DateHeure == "" {
        http.Error(response, "Veuillez choisir une date et une heure.", http.StatusBadRequest)
        return
    }

    _, err := db.DB.Exec("INSERT INTO reservation_service (id_service, id_utilisateur, date_heure) VALUES (?, ?, ?)", idService, payload.IdUtilisateur, payload.DateHeure)
    if err != nil {
        http.Error(response, "Erreur lors de la réservation.", http.StatusInternalServerError)
        return
    }

    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Rendez-vous confirmé !"})
}

func Unregister_Service(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") {
        return
    }

    idReservation := request.PathValue("id")
    
    var payload map[string]int
    if err := json.NewDecoder(request.Body).Decode(&payload); err != nil {
        http.Error(response, "Format JSON invalide", http.StatusBadRequest)
        return
    }

    idUser, exists := payload["id_utilisateur"]
    if !exists {
        http.Error(response, "ID Utilisateur manquant", http.StatusBadRequest)
        return
    }

    res, err := db.DB.Exec("DELETE FROM reservation_service WHERE id_reservation = ? AND id_utilisateur = ?", idReservation, idUser)
    if err != nil {
        http.Error(response, "Erreur lors de l'annulation.", http.StatusInternalServerError)
        return
    }

    affected, _ := res.RowsAffected()
    if affected == 0 {
        http.Error(response, "Réservation introuvable ou non autorisée.", http.StatusForbidden)
        return
    }

    response.WriteHeader(http.StatusOK)
    json.NewEncoder(response).Encode(map[string]string{"message": "Rendez-vous annulé !"})
}

func GetServicesByCategory(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	categorieIDStr := request.URL.Query().Get("categorie")
	
	query := `SELECT id_service, nom, description, id_categorie FROM SERVICE WHERE id_categorie = ?`
	rows, err := db.DB.Query(query, categorieIDStr)
	if err != nil {
		http.Error(response, "Erreur récupération", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var services []models.Service
	for rows.Next() {
		var s models.Service
		if err := rows.Scan(&s.ID, &s.Nom, &s.Description, &s.IDCategorie); err == nil {
			services = append(services, s)
		}
	}

	if services == nil {
		services = []models.Service{}
	}

	response.Header().Set("Content-Type", "application/json")
	response.WriteHeader(http.StatusOK)
	json.NewEncoder(response).Encode(services)
}