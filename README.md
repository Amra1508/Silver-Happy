## **L'arborescence A AVOIR POUR LANCER DOCKER CORRECTEMENT (déjà fait par mes soins de roumain mais le faire au cas où SINON)**

📂silver-happy # DOSSIER DE BASE QUI CONTIENT TOUT CE QUI EST EN BAS
┣ 📄 .env # contient les mots de passe de la BDD
┣ 📄 docker-compose.yml # le fichier de conf pour docker
┣ 📂 mariadb # NE PAS LE CRÉER car géré tout seul par Docker (mais verifier qu'il y ait bien APRES AVOIR LANCE LA COMMANDE CITE JUSTE EN DESSOUS)
┣ 📂 web # LE CODE WEB VA ICI (PHP, HTML, CSS)
┃ ┣ 📄 Dockerfile # config Apache + PHP
┃ ┣ 📄 db.php # permet la connexion direct à la bdd (genre pas besoin de le créer tout le temps)
┃ ┗ 📄 index.php # SITE TEST (A MODIFIER) qui va indiquer si la connexion se fait bien avec la bdd et si le serv apache se lance bien
┣ 📂 api # LE CODE API VA ICI (Go)
┃ ┣ 📄 Dockerfile # config Go
┃ ┗ 📄 main.go # SITE TEST (A MODIFIER) qui va indiquer si l'API se démarre bien et tout le tralalalala

---

## **Lancer docker compose up -d --build**

ça va faire beaucoup de texte c'est normal, car on telecharge tout les images et les builds qui nous faut

---

## **Pour dev en live, pas besoin de redémarrer Docker à chaque modification !**

grâce au système de volumes, les dossiers `web` et `api` de votre PC sont directement liés à tout docker et au serv.

1. ouvrez le dossier `web` ou `api` dans VSCode.
2. faites vos modifs et tout le tralalalalala NE PAS OUBLIER DE SAVE
3. actualisez la page Web, les modifs sont direct

---

## Conseil pour le code PHP et connexion BDD

Pour garder un code propre et ne pas répéter la connexion à la base de données sur toutes les pages, utilisez direct ceci, normalemen ça doit marcher hein :

**1. Appelez ce fichier au début de vos autres pages (ex: index.php, caca.php) :**

```php
<?php require_once 'db.php'; ?>
```

---

## Les mots de passes se trouve dans le fichier .env (c'est meilleur pour la sécurité que de stocker en dur)

---

## n'oublier pas que vous pouvez vérifier les états des contenaires direct dans docker desktop

---

## Liens utiles

l'api : http://localhost:8082/
phpmyadmin : http://localhost:8081/
serv apache : http://localhost/front

---

## ceci est une 1ère version du deploiement dans docker, il peut évoluer et être modifié par la suite !

---

## voila voila j'espère avoir bien expliqué les choses, si y'a des questions
