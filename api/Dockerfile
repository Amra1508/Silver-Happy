# On choisit notre image de base avec la version de Go.
FROM golang:1.25.3-alpine

# On définit le répertoire de travail à l'intérieur du conteneur.
WORKDIR /api

# On copie l'intégralité de notre code source local vers le répertoire de travail du conteneur.
COPY . .

# On compile notre application Go pour créer un binaire exécutable nommé "main" à la racine.
RUN go build -o /main .

# On exécute le binaire au démarrage du conteneur.
CMD ["/main"]