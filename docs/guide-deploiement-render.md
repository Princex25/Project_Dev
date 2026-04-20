# Guide de deploiement (Render)

Ce guide decrit un deploiement sur Render pour l'application web, avec une base MySQL hebergee en externe. Le meme conteneur Docker peut aussi servir pour Railway.

## Pre-requis

- Un compte Render.
- Un compte MySQL accessible depuis Internet (ex: Aiven, Railway MySQL, PlanetScale, ou un serveur MySQL externe).
- Le depot Git du projet.

## Etape 1 - Creer la base MySQL

1. Creez une base MySQL gratuite chez le fournisseur choisi.
2. Notez: hote, port, nom de base, utilisateur, mot de passe.
3. Importez le schema et les donnees initiales:
   - Fichier: `database/unified_database.sql`
   - Methode: importer dans votre console MySQL ou l'interface du fournisseur.

## Etape 2 - Creer le service Render

1. Render -> New -> Web Service.
2. Choisissez le depot du projet.
3. Type: Docker.
   - Option: vous pouvez importer [render.yaml](../render.yaml) comme blueprint.
4. Branche: `main` (ou votre branche).
5. Build et start commandes: laissez Render utiliser le `Dockerfile`.
6. Health check: `/admin2/php-login/index.php`.

## Etape 3 - Variables d'environnement Render

Dans Render -> Environment, ajoutez:

- `DB_HOST` = hote MySQL
- `DB_PORT` = port MySQL
- `DB_NAME` = `gestion_demandes`
- `DB_USER` = utilisateur MySQL
- `DB_PASS` = mot de passe MySQL
- `BASE_URL` = `/admin2`

> Si vous placez le site a la racine (sans `/admin2`), mettez `BASE_URL` a vide ou `/`.

## Etape 4 - Lancer le deploiement

- Render construit l'image Docker et demarre l'application.
- L'URL publique s'affiche dans le dashboard Render.

## Etape 5 - Jenkins (deploy automatique)

1. Dans Render, creez un Deploy Hook.
2. Copiez l'URL du hook.
3. Dans Jenkins, ajoutez une credentielle ou une variable d'environnement:
   - `RENDER_DEPLOY_HOOK_URL`
4. Le pipeline `Jenkinsfile` declenchera un redeploiement apres un build Docker.

## Deploiement Railway

Railway peut reutiliser le meme `Dockerfile` que Render.

1. Creez un projet Railway et connectez le depot GitHub.
2. Railway detecte automatiquement le `Dockerfile`.
3. Ajoutez les variables d'environnement:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
   - `BASE_URL` = `/admin2`
4. Verifiez que votre base MySQL accepte les connexions depuis Railway.
5. Lancez le deploiement puis ouvrez l’URL fournie par Railway.

## Lancer en local (Docker)

```bash
docker compose up --build
```

Acces local:

- http://localhost:8080/admin2/php-login/index.php

## Depannage rapide

- Erreur DB: verifier les variables d'environnement et l'import SQL.
- Page blanche: verifier les logs Render et les droits sur `uploads/`.
- Railway: verifier aussi que `BASE_URL` correspond au chemin expose par votre service.
