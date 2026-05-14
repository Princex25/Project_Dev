# Guide Docker & Docker Compose

Ce guide decrit l'utilisation de Docker et Docker Compose pour executer l'application en local et en mode haute disponibilite.

## Definition et objectif

- **Docker**: moteur de conteneurs qui isole l'application et ses dependances.
- **Docker Compose**: orchestre plusieurs conteneurs (web, base de donnees, proxy) via un fichier YAML.

Objectifs dans ce projet:

- fournir un environnement de developpement reproductible.
- lancer l'application et MySQL en une commande.
- proposer un mode haute disponibilite avec deux conteneurs web.

## Techniques utilisees

- **Containerisation**: un `Dockerfile` unique pour l'application PHP.
- **Multi-conteneurs**: services web + base MySQL + proxy Nginx.
- **Volumes**: persistance des donnees et des uploads.
- **Reverse proxy / load balancing**: Nginx repartit les requetes entre deux conteneurs web.

## Dockerfile (application)

Le fichier [Dockerfile](../Dockerfile) :

- utilise `webdevops/php-apache:8.2` comme base.
- configure la racine web via `WEB_DOCUMENT_ROOT`.
- copie le code dans `/app/devops-project`.
- force Apache en mode `mpm_prefork` pour la compatibilite PHP.
- prepare le dossier `uploads/` et les droits d'ecriture.

Role dans le projet: fournir une image applicative unique reutilisable (local, Kubernetes).

## Docker Compose (mode standard)

Le fichier [docker-compose.yml](../docker-compose.yml) demarre:

- **web**: le conteneur PHP/Apache.
- **db**: MySQL avec un volume `db_data`.

Il expose l'application sur `http://localhost:8080/devops-project/php-login/index.php`.

## Docker Compose (mode haute disponibilite)

Le fichier [docker-compose.ha.yml](../docker-compose.ha.yml) demarre:

- **web1** et **web2**: deux conteneurs web identiques.
- **proxy**: Nginx qui fait l'equilibrage de charge.
- **db**: MySQL partage pour les deux instances.

Le fichier [nginx/ha.conf](../nginx/ha.conf) definit un upstream Nginx qui redirige vers `web1` ou `web2`. En cas de panne d'une instance, l'autre continue de servir les requetes.

## Lancement

Mode standard:

```bash
docker compose up --build
```

Mode haute disponibilite:

```bash
docker compose -f docker-compose.ha.yml up --build
```

## Limites

- La base MySQL reste un point de panne unique en local.
- Le partage des uploads en local passe par un volume commun.
- En production, la haute disponibilite doit aussi couvrir la base de donnees et le stockage.
- Remplacer docker compose par kubernety et suprimer toutes les file de docker compose
