# Rapport Projet Web - Version 2

Date: 14/04/2026

## 1. Resume

Ce rapport presente l'application de gestion des demandes (employes, validateurs, administrateurs) et les ajouts DevOps realises pour le deploiement gratuit sur Render avec Docker et Jenkins.

## 2. Contexte et objectifs

- Digitaliser la gestion des demandes internes.
- Assurer la tracabilite, la rapidite de traitement et le suivi.
- Centraliser la validation et l'assignation des demandes.

## 3. Fonctionnalites principales

- Espace Demandeur: creation, suivi, historique, modification avant validation.
- Espace Validateur: consultation, validation/rejet, filtrage.
- Espace Administrateur: gestion des utilisateurs, types, demandes, services.
- Notifications internes pour le suivi des statuts.

## 4. Architecture fonctionnelle

- Application web PHP avec separation par roles:
  - `Demander/` (demandeur)
  - `Validateur/` (validateur)
  - `admin/` (administrateur)
  - `php-login/` (authentification)
- Configuration partagée dans `shared/config.php`.

## 5. Architecture technique

- Backend: PHP 7+ (PDO / MySQL)
- Frontend: Bootstrap 5, HTML/CSS, JS
- Base de donnees: MySQL
- Environnements: local (Docker Compose) et production (Render)

## 6. Base de donnees (synthetique)

Tables principales:

- `users`, `demandes`, `types_besoins`, `services`, `notifications`, `historique`.

## 7. Ajouts DevOps (version 2)

- Dockerisation de l'application (Dockerfile).
- Orchestration locale (docker-compose.yml).
- Pipeline Jenkins (Jenkinsfile) pour build et redeploiement via hook Render.
- Guide de deploiement Render (docs/guide-deploiement-render.md).
- Variables d'environnement centralisees (DB\_\* et BASE_URL).

## 8. CI/CD (Jenkins)

- Build d'une image Docker a chaque execution.
- Redeploiement automatique via Render Deploy Hook.
- Possibilite d'ajouter des tests a terme.

## 9. Deploiement et exploitation

- Hebergement web sur Render (Docker).
- Base MySQL hebergee en externe (Aiven/Railway/PlanetScale).
- Import initial des donnees via `database/unified_database.sql`.
- Logs accessibles via Render.

## 10. Securite et qualite

- Connexion PDO securisee.
- Gestion des roles et sessions.
- Recommandations: rotation des mots de passe, sauvegardes MySQL, HTTPS.

## 11. Limites et risques

- Dependances externes pour la base MySQL gratuite.
- Besoin d'un plan de sauvegarde (backup).
- Tests automatiques a ajouter.

## 12. Evolutions futures

- Ajout de tests (unitaires et smoke).
- Ajout d'observabilite (monitoring, alerting).
- Amelioration du stockage des fichiers (S3 compatible).
