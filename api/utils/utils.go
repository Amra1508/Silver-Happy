package utils

import (
	"fmt"
	"net/http"
	"os"
	"strings"
	"time"

	"github.com/jung-kurt/gofpdf"
)

func HandleCORS(response http.ResponseWriter, request *http.Request, methode string) bool {
	frontURL := os.Getenv("FRONT_URL")

	response.Header().Set("Access-Control-Allow-Origin", frontURL)
	response.Header().Set("Access-Control-Allow-Credentials", "true")
	response.Header().Set("Access-Control-Allow-Methods", methode+", OPTIONS")
	response.Header().Set("Access-Control-Allow-Headers", "Content-Type")

	if request.Method == "OPTIONS" {
		response.WriteHeader(http.StatusOK)
		return true
	}

	return false
}

func GetAPIBaseURL() string {
	url := os.Getenv("API_URL")
	if url == "" {
		return "http://localhost:8082"
	}
	return url
}

func GetFrontBaseURL() string {
	url := os.Getenv("FRONT_URL")
	if url == "" {
		return "http://localhost"
	}
	return url
}

func GenerateSubscriptionContract(id int64, nom, prenom, typeUtilisateur, formule, prix string) (string, error) {
	dossier := "./uploads/contracts"
	os.MkdirAll(dossier, os.ModePerm)

	pdf := gofpdf.New("P", "mm", "A4", "")
	pdf.AddPage()

	colTitleR, colTitleG, colTitleB := 40, 40, 40
	colTextR, colTextG, colTextB := 60, 60, 60
	colLineR, colLineG, colLineB := 200, 200, 200

	pdf.SetTextColor(colTitleR, colTitleG, colTitleB)
	pdf.SetFont("Arial", "B", 18)
	pdf.CellFormat(190, 10, "CONTRAT D'ABONNEMENT", "", 1, "C", false, 0, "")
	pdf.SetFont("Arial", "", 12)
	pdf.CellFormat(190, 8, "PLATEFORME SILVER HAPPY", "", 1, "C", false, 0, "")

	pdf.SetDrawColor(colLineR, colLineG, colLineB)
	pdf.Line(15, pdf.GetY()+2, 195, pdf.GetY()+2)
	pdf.Ln(12)

	pdf.SetTextColor(colTextR, colTextG, colTextB)
	pdf.SetFont("Arial", "", 10)
	dateJour := time.Now().Format("02/01/2006")
	refContrat := fmt.Sprintf("REF : SH-%s-%d", strings.ToUpper(typeUtilisateur[:3]), id)

	pdf.CellFormat(190, 5, fmt.Sprintf("Date d'emission : %s", dateJour), "", 1, "R", false, 0, "")
	pdf.CellFormat(190, 5, refContrat, "", 1, "R", false, 0, "")
	pdf.Ln(10)

	pdf.SetFont("Arial", "B", 12)
	pdf.SetTextColor(colTitleR, colTitleG, colTitleB)
	pdf.Cell(0, 8, "ENTRE LES SOUSSIGNES :")
	pdf.Ln(8)

	pdf.SetTextColor(colTextR, colTextG, colTextB)

	pdf.SetFont("Arial", "B", 11)
	pdf.Cell(0, 6, "D'une part, la plateforme :")
	pdf.Ln(5)
	pdf.SetFont("Arial", "", 11)
	pdf.SetX(15)
	pdf.Cell(0, 6, "SILVER HAPPY")
	pdf.Ln(8)

	pdf.SetFont("Arial", "B", 11)
	pdf.Cell(0, 6, "D'autre part, l'abonne(e) :")
	pdf.Ln(5)
	pdf.SetFont("Arial", "", 11)
	pdf.SetX(15)
	pdf.Cell(0, 6, fmt.Sprintf("%s %s (Statut : %s)", strings.ToUpper(nom), prenom, typeUtilisateur))
	pdf.Ln(12)

	pdf.SetDrawColor(colLineR, colLineG, colLineB)
	pdf.Line(15, pdf.GetY(), 195, pdf.GetY())
	pdf.Ln(8)

	drawArticleTitle := func(titre string) {
		pdf.SetFont("Arial", "B", 11)
		pdf.SetTextColor(colTitleR, colTitleG, colTitleB)
		pdf.Cell(0, 8, titre)
		pdf.Ln(7)
		pdf.SetTextColor(colTextR, colTextG, colTextB)
		pdf.SetFont("Arial", "", 11)
	}

	drawArticleTitle("Article 1 - Objet du contrat")
	pdf.MultiCell(0, 6, fmt.Sprintf("Le present contrat a pour objet de definir les conditions dans lesquelles l'abonne(e) beneficie des services de la plateforme Silver Happy, selon la formule choisie : %s.", formule), "", "J", false)
	pdf.Ln(6)

	drawArticleTitle("Article 2 - Conditions Financieres")
	pdf.MultiCell(0, 6, fmt.Sprintf("Le montant de l'abonnement est fixe a %s Euros TTC. Ce reglement a ete valide et traite de maniere securisee lors de la souscription sur le site.", prix), "", "J", false)
	pdf.Ln(6)

	drawArticleTitle("Article 3 - Engagement et Modalites")
	pdf.MultiCell(0, 6, "L'abonnement est effectif des la date de signature numerique du present document. Les conditions de renouvellement, de retractation ou de resiliation sont regies par les Conditions Generales d'Utilisation (CGU) acceptees par l'abonne(e).", "", "J", false)
	pdf.Ln(15)

	pdf.SetFont("Arial", "B", 11)
	pdf.SetTextColor(colTitleR, colTitleG, colTitleB)
	pdf.Cell(0, 10, "Signatures :")
	pdf.Ln(10)

	ySig := pdf.GetY()
	pdf.SetDrawColor(180, 180, 180)

	pdf.Rect(15, ySig, 80, 30, "D")
	pdf.SetXY(15, ySig+2)
	pdf.SetFont("Arial", "B", 10)
	pdf.CellFormat(80, 6, "SILVER HAPPY", "", 2, "C", false, 0, "")
	pdf.SetFont("Arial", "I", 9)
	pdf.SetTextColor(150, 150, 150)
	pdf.CellFormat(80, 15, "Validation numerique certifiee", "", 0, "C", false, 0, "")

	pdf.Rect(115, ySig, 80, 30, "D")
	pdf.SetXY(115, ySig+2)
	pdf.SetFont("Arial", "B", 10)
	pdf.SetTextColor(colTextR, colTextG, colTextB)
	pdf.CellFormat(80, 6, fmt.Sprintf("L'abonne(e) : %s", strings.ToUpper(nom)), "", 2, "C", false, 0, "")
	pdf.SetFont("Arial", "I", 9)
	pdf.SetTextColor(150, 150, 150)
	pdf.CellFormat(80, 15, "Lu et approuve", "", 0, "C", false, 0, "")

	pdf.SetY(-20)
	pdf.SetFont("Arial", "", 8)
	pdf.SetTextColor(150, 150, 150)
	pdf.CellFormat(0, 4, "Silver Happy - Document genere automatiquement a la suite de votre paiement.", "", 1, "C", false, 0, "")

	fileName := fmt.Sprintf("contrat_%s_%d_%d.pdf", typeUtilisateur, id, time.Now().Unix())
	filePath := fmt.Sprintf("%s/%s", dossier, fileName)

	err := pdf.OutputFileAndClose(filePath)
	if err != nil {
		return "", err
	}

	return "/uploads/contracts/" + fileName, nil
}
