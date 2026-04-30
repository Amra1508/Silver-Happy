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
	tr := pdf.UnicodeTranslatorFromDescriptor("")
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

	pdf.CellFormat(190, 5, tr("Date d'émission : ") + dateJour, "", 1, "R", false, 0, "")
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
	pdf.Cell(0, 6, tr("D'autre part, l'abonné(e) :"))
	pdf.Ln(5)
	pdf.SetFont("Arial", "", 11)
	pdf.SetX(15)
	pdf.Cell(0, 6, prenom + " " + strings.ToUpper(nom) + " Statut (" + typeUtilisateur + ")")
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
	pdf.MultiCell(0, 6, tr("Le présent contrat a pour objet de définir les conditions dans lesquelles l'abonné(e) bénéficie des services de la plateforme Silver Happy, selon la formule choisie : " + formule), "", "J", false)
	pdf.Ln(6)

	var renouvellement string

	if strings.Contains(strings.ToLower(formule), "mensuel") {
		renouvellement = "3 euros"
	} else {
		renouvellement = "35 euros"
	}

	drawArticleTitle(tr("Article 2 - Conditions Financières"))
	pdf.MultiCell(0, 6, tr("Le montant de l'abonnement est de " + prix + " euros TTC, puis sera de " + renouvellement + " par prélèvement automatique. Ce règlement a été validé et traité de manière sécurisée lors de la souscription sur le site."), "", "J", false)
	pdf.Ln(6)

	drawArticleTitle(tr("Article 3 - Engagement et Modalités"))
	pdf.MultiCell(0, 6, tr("L'abonnement est effectif dès la date de signature numérique du présent document. Les conditions de renouvellement, de rétractation ou de résiliation sont régies par les Conditions Générales d'Utilisation (CGU) acceptées par l'abonné(e)."), "", "J", false)
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
	pdf.CellFormat(80, 15, tr("Validation numérique certifiée"), "", 0, "C", false, 0, "")

	pdf.Rect(115, ySig, 80, 30, "D")
	pdf.SetXY(115, ySig+2)
	pdf.SetFont("Arial", "B", 10)
	pdf.SetTextColor(colTextR, colTextG, colTextB)
	pdf.CellFormat(80, 6, tr("L'abonné(e) : " + prenom + " " + strings.ToUpper(nom)), "", 2, "C", false, 0, "")
	pdf.SetFont("Arial", "I", 9)
	pdf.SetTextColor(150, 150, 150)
	pdf.CellFormat(80, 15, tr("Lu et approuvé"), "", 0, "C", false, 0, "")

	pdf.SetY(-20)
	pdf.SetFont("Arial", "", 8)
	pdf.SetTextColor(150, 150, 150)
	pdf.CellFormat(0, 4, tr("Silver Happy - Document généré automatiquement à la suite de votre paiement."), "", 1, "C", false, 0, "")

	dateFichier := time.Now().Format("02-01-2006")
	fileName := fmt.Sprintf("Contrat_Abonnement_%s_%d_%s.pdf", typeUtilisateur, id, dateFichier)
	filePath := fmt.Sprintf("%s/%s", dossier, fileName)

	err := pdf.OutputFileAndClose(filePath)
	if err != nil {
		return "", err
	}

	return "/uploads/contracts/" + fileName, nil
}
