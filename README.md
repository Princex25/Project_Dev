# Site_Web

Application web PHP/MySQL pour la gestion des demandes, avec trois espaces principaux: Demandeur, Validateur et Administrateur.

## Stack

- PHP 8.2 + Apache
- MySQL
- Bootstrap 5
- Docker

## Démarrage local

```bash
docker compose up --build
```

Accès local:

- http://localhost:8080/devops-project/php-login/index.php

## Docker & Docker Compose

- Guide: [docs/guide-docker-compose.md](docs/guide-docker-compose.md)

## Monitoring Grafana

Pour lancer le monitoring local:

```bash
docker compose -f docker-compose.yml -f docker-compose.monitoring.yml up --build
```

Acces:

- Grafana: http://localhost:3000
- Prometheus: http://localhost:9090
- cAdvisor: http://localhost:8081

## Tests

Installer les dépendances de test:

```bash
composer install
```

Lancer l'ensemble des tests (unitaires, intégration, smoke):

```bash
composer test
```

Variables utiles (à adapter selon votre environnement):

- `DB_HOST` (ex: `127.0.0.1` ou l'IP Docker Toolbox)
- `DB_PORT` (par défaut `3306`)
- `DB_NAME` (par défaut `gestion_demandes`)
- `DB_USER` / `DB_PASS`
- `APP_BASE_URL` (ex: `http://localhost:8080`)

Les tests d’intégration et smoke nécessitent que les conteneurs soient démarrés.

## Kubernetes

Ce projet inclut des manifestes Kubernetes pour orchestrer:

- **Frontend** (2 replicas + HPA + PDB)
- **Backend** (2 replicas + HPA + PDB)
- **MySQL** (PVC)
- **Jenkins** (PVC)
- **Monitoring** (Prometheus + Grafana)
- **cAdvisor** (DaemonSet)

Deploiement rapide:

```bash
kubectl apply -k kubernetes/
```

Acces (port-forward):

```bash
kubectl -n devops-project port-forward svc/devops-project-frontend 8080:80
kubectl -n devops-project port-forward svc/grafana 3000:3000
kubectl -n devops-project port-forward svc/prometheus 9090:9090
kubectl -n devops-project port-forward svc/cadvisor 8081:8080
kubectl -n devops-project port-forward svc/jenkins 8082:8080
```

Si vous utilisez l'Ingress, ajoutez `devops-project.local` dans votre fichier hosts et activez un Ingress Controller (ex: NGINX).

Pour lancer deux instances applicatives derriere un reverse proxy Nginx:

```bash
docker compose -f docker-compose.ha.yml up --build
```

Ce mode permet a une instance de reprendre la charge si l'autre tombe, mais la base MySQL reste un point de panne unique.

Pour une haute disponibilite en Kubernetes (replicas, PDB, HPA), voir le guide Kubernetes: [docs/guide-kubernetes.md](docs/guide-kubernetes.md).

## GitHub Actions

- Guide: [docs/guide-github-actions.md](docs/guide-github-actions.md)
- Workflow: [.github/workflows/deploy.yml](.github/workflows/deploy.yml)

## Depot GitHub

- Guide: [docs/guide-github-repository.md](docs/guide-github-repository.md)

## Documents

- Cahier des charges: [docs/cahier-des-charges.md](docs/cahier-des-charges.md)
- Rapport projet: [docs/rapport-projet-web-v2.md](docs/rapport-projet-web-v2.md)
- Rapport DevOps: [docs/rapport-devops-outils.md](docs/rapport-devops-outils.md)
- Guide Grafana: [docs/guide-grafana-monitoring.md](docs/guide-grafana-monitoring.md)
- Guide Kubernetes: [docs/guide-kubernetes.md](docs/guide-kubernetes.md)

- Guide Depot GitHub: [docs/guide-github-repository.md](docs/guide-github-repository.md)
- Guide Jenkins: [docs/guide-jenkins.md](docs/guide-jenkins.md)
- Guide Docker & Compose: [docs/guide-docker-compose.md](docs/guide-docker-compose.md)
- Guide MySQL HA: [docs/guide-mysql-ha.md](docs/guide-mysql-ha.md)

GitHub Actions est maintenant le chemin d'automatisation principal.
