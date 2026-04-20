# Guide de déploiement (Railway)

Ce guide décrit un déploiement de l'application sur Railway à partir du `Dockerfile` existant.

## Pré-requis

- Un compte Railway.
- Le dépôt Git du projet connecté à Railway.
- Une base MySQL externe ou un service MySQL accessible depuis Railway.

## 1. Créer ou connecter le projet

1. Ouvrez Railway.
2. Créez un nouveau projet ou importez le dépôt GitHub.
3. Railway détecte le `Dockerfile` à la racine du projet.

## 2. Variables d'environnement

Ajoutez les variables suivantes dans Railway:

- `DB_HOST` = hôte MySQL
- `DB_PORT` = port MySQL
- `DB_NAME` = nom de la base
- `DB_USER` = utilisateur MySQL
- `DB_PASS` = mot de passe MySQL
- `BASE_URL` = `/admin2`

> Si vous servez l’application à la racine du domaine via un reverse proxy ou une réécriture de chemin, adaptez `BASE_URL` en conséquence.

## 3. Base de données

1. Créez une base MySQL accessible publiquement ou via un tunnel supporté par votre fournisseur.
2. Importez le schéma contenu dans [database/unified_database.sql](../database/unified_database.sql).
3. Vérifiez que la base est accessible depuis Railway.

## 4. Déploiement

1. Lancez le déploiement depuis Railway.
2. Attendez la fin du build Docker.
3. Ouvrez l’URL fournie par Railway.

## 5. Vérifications

- Page de connexion: `/admin2/php-login/index.php`
- Chargement des assets CSS/JS.
- Connexion à la base MySQL.
- Droits d’écriture sur `uploads/` si vous utilisez l’envoi de fichiers.

## Dépannage

- Si la page ne s’affiche pas, vérifiez les logs de déploiement Railway.
- Si la base est inaccessible, revérifiez `DB_HOST`, `DB_PORT` et les règles réseau du fournisseur MySQL.
- Si les uploads échouent, assurez-vous que le conteneur peut écrire dans `uploads/`.
