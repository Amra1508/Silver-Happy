package users

import (
	"encoding/json"
	"fmt"
	"main/db"
	"main/models"
	"main/utils"
	"net/http"
	"strings"
)

func SearchAll(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	query := request.URL.Query().Get("q")
	query = strings.TrimSpace(query)
	
	resultats := models.RechercheGlobale{
		Produits:   []models.Recherche{},
		Evenements: []models.Recherche{},
		Services:   []models.Recherche{},
		Avis: []models.Recherche{},
		Conseils: []models.Recherche{},
	}

	if query == "" {
		json.NewEncoder(response).Encode(resultats)
		return
	}

	searchPattern := "%" + query + "%"

	rowsProd, errProd := db.DB.Query(`SELECT id_produit, nom, description, prix FROM PRODUIT WHERE nom LIKE ? OR description LIKE ?`, searchPattern, searchPattern)
	if errProd == nil {
		defer rowsProd.Close()
		for rowsProd.Next() {
			var item models.Recherche
			if err := rowsProd.Scan(&item.ID, &item.Titre, &item.Description, &item.Prix); err == nil {
				item.Lien = fmt.Sprintf("/front/services/products.php?id=%d", item.ID)
				resultats.Produits = append(resultats.Produits, item)
			}
		}
	}

	rowsEvt, errEvt := db.DB.Query(`SELECT id_evenement, nom, description, prix FROM EVENEMENT WHERE nom LIKE ? OR description LIKE ?`, searchPattern, searchPattern)
	if errEvt == nil {
		defer rowsEvt.Close()
		for rowsEvt.Next() {
			var item models.Recherche
			if err := rowsEvt.Scan(&item.ID, &item.Titre, &item.Description, &item.Prix); err == nil {
				item.Lien = fmt.Sprintf("/front/services/events.php?id=%d", item.ID)
				resultats.Evenements = append(resultats.Evenements, item)
			}
		}
	}

	rowsPrest, errPrest := db.DB.Query(`SELECT id_service, nom, description FROM SERVICE WHERE nom LIKE ? OR description LIKE ?`, searchPattern, searchPattern)
	if errPrest == nil {
		defer rowsPrest.Close()
		for rowsPrest.Next() {
			var item models.Recherche
			if err := rowsPrest.Scan(&item.ID, &item.Titre, &item.Description); err == nil {
				item.Lien = fmt.Sprintf("/front/services/catalog.php?id=%d", item.ID)
				resultats.Services = append(resultats.Services, item)
			}
		}
	}

    rowsAvis, errAvis := db.DB.Query(`SELECT id_avis, titre, description FROM AVIS WHERE titre LIKE ? OR description LIKE ?`, searchPattern, searchPattern)
    if errAvis == nil {
        defer rowsAvis.Close()
        for rowsAvis.Next() {
            var item models.Recherche
            if err := rowsAvis.Scan(&item.ID, &item.Titre, &item.Description); err == nil {
                item.Lien = fmt.Sprintf("/front/communication/review.php?id=%d", item.ID) 
                resultats.Avis = append(resultats.Avis, item)
            } else {
                fmt.Println("Erreur Scan Avis :", err)
            }
        }
    }

	rowsConseils, errConseils := db.DB.Query(`SELECT id_conseil, titre, description FROM CONSEIL WHERE titre LIKE ? OR description LIKE ?`, searchPattern, searchPattern)
    if errConseils == nil {
        defer rowsConseils.Close()
        for rowsConseils.Next() {
            var item models.Recherche
            if err := rowsConseils.Scan(&item.ID, &item.Titre, &item.Description); err == nil {
                item.Lien = fmt.Sprintf("/front/services/advice.php?id=%d", item.ID) 
                resultats.Conseils = append(resultats.Conseils, item)
            } else {
                fmt.Println("Erreur Scan Conseil :", err)
            }
        }
    }

	response.Header().Set("Content-Type", "application/json")
	json.NewEncoder(response).Encode(resultats)
}