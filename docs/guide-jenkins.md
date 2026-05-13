# Guide Jenkins (Docker)

Ce guide explique comment lancer Jenkins en Docker et configurer un pipeline pour ce projet.

## Definition et objectif

**Jenkins** est un serveur d'automatisation CI/CD. Dans ce projet, il sert a:

- construire l'image Docker de l'application.
- fournir une alternative historique a GitHub Actions.

## Techniques utilisees

- **CI/CD**: automatisation du build et du deploiement.
- **Pipeline as Code**: la definition du pipeline est versionnee dans `Jenkinsfile`.

## Pre-requis

- Docker Desktop installe.
- Acces au depot Git du projet.

## 1. Lancer Jenkins

```bash
docker compose -f docker-compose.jenkins.yml up --build
```

Acces:

- http://localhost:8081

## 2. Recuperer le mot de passe admin

Le mot de passe initial est dans le volume `jenkins_home`.

Exemple (PowerShell):

```powershell
Get-Content "C:\\Users\\<vous>\\.docker\\volumes\\devops-project_jenkins_home\\_data\\secrets\\initialAdminPassword"
```

## 3. Installer les plugins

Plugins recommandes:

- Pipeline
- Git
- Docker
- Credentials Binding


Dans Jenkins:

1. Manage Jenkins -> Credentials -> (Global).
2. Ajouter un secret texte:

## 5. Configurer le job pipeline

1. Nouveau job -> Pipeline.
2. Source: Git (URL du depot).
3. Script Path: `Jenkinsfile`.
4. Ajouter la variable d'environnement au job:

## 6. Lancer le pipeline

- Build Now.

## Explication du Jenkinsfile

Le fichier [Jenkinsfile](../Jenkinsfile) contient les etapes suivantes:

- **Checkout**: recupere le code du depot pour construire l'image.
- **Build Docker image**: lance `docker build` et produit l'image `devops-project-web`.
- **post/failure**: affiche un message clair en cas d'echec du pipeline.

Role dans le projet: automatiser la livraison sans action manuelle apres un push.

## Depannage rapide

- Verifier que Docker est actif.
- Verifier l'URL du deploy hook.
- Consulter les logs du job Jenkins.
