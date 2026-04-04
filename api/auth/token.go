package auth

import (
	"main/models"
	"time"

	"github.com/golang-jwt/jwt/v5"
)

var JwtKey = []byte("ma_cle_secrete_super_robuste_123!")

func GenerateJWT(user models.Utilisateur) (string, error) {
    expirationTime := time.Now().Add(24 * time.Hour)

    claims := &models.Claims{
        UserID: user.ID,
        Email:  user.Email,
        Statut: user.Statut,
        RegisteredClaims: jwt.RegisteredClaims{
            ExpiresAt: jwt.NewNumericDate(expirationTime),
            IssuedAt:  jwt.NewNumericDate(time.Now()),
        },
    }

    token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)

    tokenString, err := token.SignedString(JwtKey)
    if err != nil {
        return "", err
    }

    return tokenString, nil
}