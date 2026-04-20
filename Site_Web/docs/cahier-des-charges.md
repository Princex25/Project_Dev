# Cahier des charges - Projet de gestion des demandes

Date: 14/04/2026

## 1. Contexte

L'organisation souhaite digitaliser le processus de demandes internes afin de reduire les delais, assurer la tracabilite et faciliter le suivi.

## 2. Objectifs

- Centraliser la creation et la validation des demandes.
- Offrir un suivi clair aux demandeurs.
- Mettre a disposition un espace administrateur pour la gestion globale.

## 3. Perimetre

Inclus:

- Authentification et gestion des roles.
- Creation et suivi des demandes.
- Validation, rejet, affectation.
- Notifications internes.

Exclus:

- Gestion des achats reelle (ERP).
- Integrations externes avancees.

## 4. Acteurs

- Demandeur (employe)
- Validateur (chef d'equipe)
- Administrateur

## 5. Exigences fonctionnelles

- Soumission d'une demande avec description et priorite.
- Historique personnel des demandes.
- Validation/rejet avec commentaire.
- Gestion des utilisateurs et types de besoins.
- Mise a jour des statuts.

## 6. Exigences non fonctionnelles

- Temps de reponse < 3 s sur les pages principales.
- Disponibilite cible: 99% (free tier).
- Sauvegardes regulieres de la base.
- Confidentialite des donnees (roles, sessions).

## 7. Contraintes

- Hebergement web gratuit (Render).
- Base MySQL hebergee en externe.
- Stack imposee: PHP/MySQL/Bootstrap.

## 8. Donnees

- Base MySQL avec tables: users, demandes, types, notifications, historique.
- Import initial via `database/unified_database.sql`.

## 9. Livrables

- Code source du projet.
- Dockerfile + docker-compose.
- Jenkinsfile + guide Jenkins.
- Guide de deploiement Render.
- Rapport v2 et presentation.

## 10. Criteres d'acceptation

- Authentification fonctionnelle.
- Creation et validation des demandes.
- Deploiement stable sur Render.
- Pipeline Jenkins fonctionnel (build + deploy hook).

## 11. Risques

- Limites de l'hebergement gratuit.
- Evolution des quotas sur MySQL externe.
- Absence de tests automatiques.
