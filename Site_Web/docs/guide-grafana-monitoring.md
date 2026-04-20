# Guide Grafana Monitoring

Ce guide ajoute un monitoring local base sur Prometheus, Grafana et cAdvisor.

## Objectif

Cette pile permet de suivre:

- l'etat des conteneurs Docker.
- l'utilisation CPU et memoire.
- les metriques accessibles via Prometheus.

## Lancement

Demarrez l'application et le monitoring ensemble avec les deux fichiers Compose:

```bash
docker compose -f docker-compose.yml -f docker-compose.monitoring.yml up --build
```

## Acces

- Application: http://localhost:8080/admin2/php-login/index.php
- Prometheus: http://localhost:9090
- Grafana: http://localhost:3000
- cAdvisor: http://localhost:8081

## Connexion Grafana

Identifiants par defaut:

- utilisateur: `admin`
- mot de passe: `admin123`

Au premier acces, changez le mot de passe.

## Datasource

Grafana est preconfigure avec une source de donnees Prometheus.

## Premiers tableaux de bord

Dans Grafana, vous pouvez creer des tableaux de bord pour:

- l'utilisation memoire du conteneur web.
- l'utilisation CPU du conteneur web.
- l'etat des conteneurs.
- la charge globale du systeme de monitoring.

Le service applicatif PHP ne publie pas encore de metriques propres; Grafana sert donc d'abord a visualiser l'infrastructure conteneurisee.

## Limites

- La base de donnees MySQL peut aussi etre monitorée plus finement avec un exporteur dedie si besoin.
