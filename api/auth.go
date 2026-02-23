package main

import (
	"time"

	"github.com/golang-jwt/jwt/v5"
)

var jwtKey = []byte("ma_cle_secrete_super_robuste_123!")

type Claims struct {
    UserID int64  `json:"user_id"`
    Email  string `json:"email"`
    Statut string `json:"statut"`
    jwt.RegisteredClaims
}

func generateJWT(user Utilisateur) (string, error) {
    expirationTime := time.Now().Add(24 * time.Hour)

    claims := &Claims{
        UserID: user.ID,
        Email:  user.Email,
        Statut: user.Statut,
        RegisteredClaims: jwt.RegisteredClaims{
            ExpiresAt: jwt.NewNumericDate(expirationTime),
            IssuedAt:  jwt.NewNumericDate(time.Now()),
        },
    }

    token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)

    tokenString, err := token.SignedString(jwtKey)
    if err != nil {
        return "", err
    }

    return tokenString, nil
}