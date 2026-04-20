# Silver Happy — Déploiement Docker

## Lancement en développement

docker compose -f docker-compose.dev.yml up --build

- Site web : http://localhost:80/
- API : http://localhost:8082/
- phpMyAdmin : http://localhost:8081/

## Lancement en production

docker compose -f docker-compose.prod.yml up -d

- Site web : https://silver-happy.fr/
- phpMyAdmin : https://pma.silver-happy.fr/
- Dockhand : https://dockhand.silver-happy.fr/

## Arrêt des services

docker compose -f docker-compose.dev.yml down
docker compose -f docker-compose.prod.yml down
