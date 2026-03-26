package services

import (
	"encoding/json"
	"net/http"

	"main/db"
	"main/models"
	"main/utils"
)

func Add_Panier(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") {
        return
    }

    id_produit := request.FormValue("id_produit")
    id_user := request.FormValue("id_utilisateur")
    quantity := request.FormValue("quantite")

    var id_panier int
    var quantitePanier int
    err := db.DB.QueryRow("SELECT id_panier, quantite FROM PANIER WHERE id_utilisateur = ? AND id_produit = ?", id_user, id_produit).Scan(&id_panier, &quantitePanier)
    
    if err == nil {
        result, err := db.DB.Exec("UPDATE PANIER SET quantite = quantite + ? WHERE id_panier = ? AND (quantite + ?) <= (SELECT stock FROM PRODUIT WHERE id_produit = ?)", quantity, id_panier, quantity, id_produit)
        if err != nil {
            http.Error(response, "Erreur lors de la mise à jour", http.StatusInternalServerError)
            return
        }
        rows, _ := result.RowsAffected()
        if rows == 0 {
            http.Error(response, "Stock insuffisant", http.StatusConflict)
            return
        }
        json.NewEncoder(response).Encode(map[string]string{"message": "Quantité mise à jour !"})
        return
    } else {
        _, err = db.DB.Exec("INSERT INTO PANIER (id_utilisateur, id_produit, quantite) VALUES (?, ?, ?)", id_user, id_produit, quantity)
    
        if err != nil {
            http.Error(response, "Impossible d'ajouter au panier", http.StatusBadRequest)
            return
        }
    }

    json.NewEncoder(response).Encode(map[string]string{"message": "Ajouté au panier !"})
}


func Get_Panier(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
		return
	}

	id_user := request.URL.Query().Get("id_utilisateur")

    rows, err := db.DB.Query("SELECT p.id_panier, p.id_produit, p.quantite, pr.nom, pr.prix, pr.image FROM PANIER p JOIN PRODUIT pr ON p.id_produit = pr.id_produit WHERE p.id_utilisateur = ?", id_user)
	
    if err != nil {
		http.Error(response, err.Error(), http.StatusInternalServerError)
		return
	}
    defer rows.Close()

    var items []models.Panier
    
    for rows.Next() {
        var i models.Panier
        rows.Scan(&i.IdPanier, &i.IdProduit, &i.Quantite, &i.Nom, &i.Prix, &i.Image)
        items = append(items, i)
    }

    json.NewEncoder(response).Encode(items)
}

func Delete_Panier(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "DELETE") {
        return
    }

    id_panier := request.URL.Query().Get("id")
    if id_panier == "" {
        http.Error(response, "ID manquant", http.StatusBadRequest)
        return
    }

    _, err := db.DB.Exec("DELETE FROM PANIER WHERE id_panier = ?", id_panier)
    if err != nil {
        http.Error(response, "Erreur lors de la suppression", http.StatusInternalServerError)
        return
    }

    response.Header().Set("Content-Type", "application/json")
    json.NewEncoder(response).Encode(map[string]string{"message": "Article supprimé"})
}