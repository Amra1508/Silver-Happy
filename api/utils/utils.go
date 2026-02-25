package utils

import "net/http"

func HandleCORS(response http.ResponseWriter, request *http.Request, methode string) bool {

	response.Header().Set("Access-Control-Allow-Origin", "http://localhost")
	response.Header().Set("Access-Control-Allow-Credentials", "true")
	response.Header().Set("Access-Control-Allow-Methods", methode+", OPTIONS")
	response.Header().Set("Access-Control-Allow-Headers", "Content-Type")

	if request.Method == "OPTIONS" {
		response.WriteHeader(http.StatusOK)
		return true
	}

	return false
}