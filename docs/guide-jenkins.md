# Guide Jenkins (Docker)

Ce guide explique comment lancer Jenkins en Docker et configurer un pipeline pour ce projet.

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
Get-Content "C:\\Users\\<vous>\\.docker\\volumes\\admin2_jenkins_home\\_data\\secrets\\initialAdminPassword"
```

## 3. Installer les plugins

Plugins recommandes:

- Pipeline
- Git
- Docker
- Credentials Binding

## 4. Creer la variable Render

Dans Jenkins:

1. Manage Jenkins -> Credentials -> (Global).
2. Ajouter un secret texte:
   - ID: `render_deploy_hook_url`
   - Secret: URL du Deploy Hook Render

## 5. Configurer le job pipeline

1. Nouveau job -> Pipeline.
2. Source: Git (URL du depot).
3. Script Path: `Jenkinsfile`.
4. Ajouter la variable d'environnement au job:
   - `RENDER_DEPLOY_HOOK_URL` -> credentielle `render_deploy_hook_url`.

## 6. Lancer le pipeline

- Build Now.
- Le pipeline construit l'image Docker et declenche le deploy Render.

## Depannage rapide

- Verifier que Docker est actif.
- Verifier l'URL du deploy hook.
- Consulter les logs du job Jenkins.
