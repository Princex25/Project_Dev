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

- http://localhost:8080/admin2/php-login/index.php

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

- Guide: [docs/guide-kubernetes.md](docs/guide-kubernetes.md)
- Manifests: [kubernetes/kustomization.yaml](kubernetes/kustomization.yaml)

## Ansible

- Guide: [docs/guide-ansible.md](docs/guide-ansible.md)
- Playbook: [ansible/deploy-kubernetes.yml](ansible/deploy-kubernetes.yml)

## Mode haute disponibilite

Pour lancer deux instances applicatives derriere un reverse proxy Nginx:

```bash
docker compose -f docker-compose.ha.yml up --build
```

Ce mode permet a une instance de reprendre la charge si l'autre tombe, mais la base MySQL reste un point de panne unique.

## Déploiement Render

- Guide: [docs/guide-deploiement-render.md](docs/guide-deploiement-render.md)
- Blueprint: [render.yaml](render.yaml)

## GitHub Actions

- Guide: [docs/guide-github-actions.md](docs/guide-github-actions.md)
- Workflow: [.github/workflows/deploy.yml](.github/workflows/deploy.yml)

## Depot GitHub

- Guide: [docs/guide-github-repository.md](docs/guide-github-repository.md)

## Déploiement Railway

- Guide: [docs/guide-deploiement-railway.md](docs/guide-deploiement-railway.md)
- Le projet peut être déployé avec le même `Dockerfile`.

## Documents

- Cahier des charges: [docs/cahier-des-charges.md](docs/cahier-des-charges.md)
- Rapport projet: [docs/rapport-projet-web-v2.md](docs/rapport-projet-web-v2.md)
- Rapport DevOps: [docs/rapport-devops-outils.md](docs/rapport-devops-outils.md)
- Guide Grafana: [docs/guide-grafana-monitoring.md](docs/guide-grafana-monitoring.md)
- Guide Kubernetes: [docs/guide-kubernetes.md](docs/guide-kubernetes.md)
- Guide Ansible: [docs/guide-ansible.md](docs/guide-ansible.md)
- Guide Depot GitHub: [docs/guide-github-repository.md](docs/guide-github-repository.md)
- Guide Jenkins: [docs/guide-jenkins.md](docs/guide-jenkins.md)

GitHub Actions est maintenant le chemin d'automatisation principal.
