# Étape 1 : Builder — compilation du binaire Go avec tous les outils nécessaires
FROM golang:1.25.3-alpine AS builder

# On définit le répertoire de travail à l'intérieur du conteneur.
WORKDIR /api

# On copie l'intégralité de notre code source local vers le répertoire de travail du conteneur.
COPY . .

# On compile notre application Go pour créer un binaire exécutable nommé "main" à la racine.
RUN go build -o /main .

# Étape 2 : Production — image finale légère, sans Go ni outils de compilation
FROM alpine:latest AS production

RUN apk add --no-cache tzdata

# On récupère uniquement le binaire compilé depuis l'étape builder.
COPY --from=builder /main /main

EXPOSE 8082

# On exécute le binaire au démarrage du conteneur.
CMD ["/main"]