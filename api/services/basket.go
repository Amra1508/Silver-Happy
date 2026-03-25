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

    resultat, _ := db.DB.Exec("UPDATE PRODUIT SET stock = stock - 1 WHERE id_produit = ? AND stock > 0", id_produit)
    
    nb, _ := resultat.RowsAffected()
    if nb == 0 {
        http.Error(response, "Plus de stock !", http.StatusBadRequest)
        return
    }

    db.DB.Exec("INSERT INTO PANIER (id_utilisateur, id_produit, quantite) VALUES (?, ?, 1)", id_user, id_produit)

    json.NewEncoder(response).Encode(map[string]string{"message": "Ajouté !"})
}

func Auto_Delete_Panier(response http.ResponseWriter, request *http.Request) {
	if utils.HandleCORS(response, request, "GET") {
        return
    }

    temps_limite := time.Now().Add(-15 * time.Minute).Format("2006-01-02 15:04:05")

    rows, _ := db.DB.Query("SELECT id_panier, id_produit FROM PANIER WHERE date_reservation < ?", temps_limite)
	defer rows.Close()

    for rows.Next() {
        var id_panier, id_produit int
        rows.Scan(&id_panier, &id_produit)

        db.DB.Exec("UPDATE PRODUIT SET stock = stock + 1 WHERE id_produit = ?", id_produit)

        db.DB.Exec("DELETE FROM PANIER WHERE id_panier = ?", id_panier)
    }
}