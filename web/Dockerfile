# Étape 1 : Builder — installation des extensions et configuration d'Apache
FROM php:8.2-apache AS builder

# Installe les pilotes (mysqli, pdo_mysql) indispensables pour que PHP puisse communiquer avec notre base MariaDB.
RUN docker-php-ext-install mysqli pdo_mysql

# Active le module de réécriture d'URL d'Apache souvent nécessaire pour le routage de l'application Web.
RUN a2enmod rewrite

# Modifie la configuration par défaut d'Apache pour autoriser l'utilisation des fichiers .htaccess
# (passage de AllowOverride None à All).
# (utiliser ici pour la page d'erreur 404)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Étape 2 : Production — image finale avec le code source intégré (utilisée pour le build prod)
FROM builder AS production

EXPOSE 80
# On copie l'intégralité du code source local vers le répertoire par défaut du serveur Apache dans le conteneur.
COPY . /var/www/html/