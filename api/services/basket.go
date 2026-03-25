package services

import (
	"encoding/json"
	"net/http"
	"time"

	"main/db"
	"main/utils"
)

func Add_Panier(response http.ResponseWriter, request *http.Request) {
    if utils.HandleCORS(response, request, "POST") {
        return
    }

    id_produit := request.FormValue("id_produit")
    id_user := request.FormValue("id_utilisateur")
    quantity := request.FormValue("quantite")

    resultat, _ := db.DB.Exec("UPDATE PRODUIT SET stock = stock - ? WHERE id_produit = ? AND stock >= ?", quantity, id_produit, quantity)
    
    nb, _ := resultat.RowsAffected()
    if nb == 0 {
        http.Error(response, "Plus de stock !", http.StatusBadRequest)
        return
    }

    db.DB.Exec("INSERT INTO PANIER (id_utilisateur, id_produit, quantite) VALUES (?, ?, ?)", id_user, id_produit, quantity)

    json.NewEncoder(response).Encode(map[string]string{"message": "Ajouté !"})
}

func Auto_Delete_Panier(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
        return
    }

    temps_limite := time.Now().Add(-15 * time.Minute).Format("2006-01-02 15:04:05")

    rows, _ := db.DB.Query("SELECT id_panier, id_produit, quantite FROM PANIER WHERE date_reservation < ?", temps_limite)
	defer rows.Close()

    for rows.Next() {
        var id_panier, id_produit, quantity int
        rows.Scan(&id_panier, &id_produit, &quantity)

        db.DB.Exec("UPDATE PRODUIT SET stock = stock + ? WHERE id_produit = ?", quantity, id_produit)

        db.DB.Exec("DELETE FROM PANIER WHERE id_panier = ?", id_panier)
    }
}