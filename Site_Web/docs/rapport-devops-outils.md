# Rapport DevOps - Outils utilises dans le projet

Date: 15/04/2026

## 1. Objet du rapport

Ce document presente les outils DevOps utilises pour developper, tester, construire, deployer et superviser l'application web de gestion des demandes. Il decrit le role de chaque outil, sa place dans l'architecture, ainsi que les avantages, limites et bonnes pratiques associes.

## 2. Vue d'ensemble

La chaine DevOps du projet repose sur les composants suivants:

- Docker pour containeriser l'application.
- Docker Compose pour lancer un environnement local complet.
- Grafana, Prometheus et cAdvisor pour le monitoring local.
- Kubernetes pour une description declarative de l'application en cluster.
- Ansible pour automatiser le deploiement Kubernetes.
- GitHub pour heberger le code source et declencher l'automatisation.
- GitHub Actions pour la CI/CD principale.
- Jenkins comme ancienne solution d'automatisation, conservee en reference.
- Render pour l'hebergement de l'application en production.
- Railway comme alternative de deploiement possible.
- MySQL pour la base de donnees.

L'objectif est de rendre le cycle de livraison simple, reproductible et proche de la production, tout en gardant un deploiement accessible pour un petit projet web PHP.

## 3. Contexte technique du projet

L'application est une solution web PHP/MySQL organisee autour de trois espaces metiers:

- Demandeur
- Validateur
- Administrateur

La structure du depot montre une separation fonctionnelle claire:

- `Demander/` pour les parcours demandeur.
- `Validateur/` pour le suivi et la validation.
- `admin/` pour l'administration.
- `php-login/` pour l'authentification.
- `shared/` pour la configuration partagee.

Cette organisation se prete bien a une containerisation et a un deploiement en conteneur.

## 4. Outils utilises

### 4.1 Docker

Docker est l'outil central du projet. Le `Dockerfile` construit une image basee sur `php:8.2-apache`, installe les extensions PDO MySQL, active `rewrite` et copie l'application dans le conteneur.

Role principal:

- Fournir un environnement d'execution identique en local et en production.
- Eviter les differences entre postes de developpement.
- Standardiser le build applicatif.

Points forts:

- Image simple et reproductible.
- Deploiement compatible avec Render et Railway.
- Dependances PHP isolees dans un conteneur.

Limites:

- Le container ne gere pas a lui seul les sauvegardes ou la supervision.
- Les chemins de fichiers et droits d'ecriture doivent etre verifies.

### 4.2 Docker Compose

Le fichier `docker-compose.yml` orchestre l'application et la base MySQL pour le developpement local.

Role principal:

- Lancer l'application et la base de donnees avec une seule commande.
- Monter le volume d'uploads.
- Simplifier les tests locaux et l'onboarding.

Configuration notable:

- Service web PHP/Apache.
- Service MySQL 8.0.
- Variables d'environnement centralisees.
- Import initial de `database/unified_database.sql`.

Avantages:

- Environnement local proche de la production.
- Facile a relancer et a nettoyer.
- Convient aux demonstrations et aux tests manuels.

Limites:

- Pas de tests automatises integres.
- Pas de healthchecks applicatifs avances.

### 4.2.1 Compose haute disponibilite

Le fichier `docker-compose.ha.yml` ajoute une variante de deploiement locale avec deux instances PHP/Apache derriere un reverse proxy Nginx.

Role principal:

- Permettre a une instance de reprendre le trafic si l'autre est en defaillance.
- Illustrer une logique de redondance applicative sans modifier le compose standard.

Fonctionnement:

- `web1` et `web2` executent la meme image Docker.
- `proxy` distribue les requetes vers les deux instances.
- `php_sessions` partage les sessions pour reduire les ruptures de connexion.

Avantages:

- Redondance de l'application.
- Continuite de service en cas d'indisponibilite d'un conteneur.
- Mise en place claire pour une demonstration ou un environnement de test.

Limites:

- La base MySQL reste un point de panne unique tant qu'elle n'est pas elle aussi dupliquee.
- La vraie haute disponibilite necessite aussi une strategie pour les sessions, la base de donnees et le stockage partage.
- Cette solution reste a l'echelle de Docker Compose; en production, un orchestrateur comme Kubernetes ou Docker Swarm serait plus adapte.

### 4.2.2 Stack de monitoring

Le fichier `docker-compose.monitoring.yml` ajoute un socle de supervision locale avec Prometheus, Grafana et cAdvisor. Il est lance avec `docker compose -f docker-compose.yml -f docker-compose.monitoring.yml up --build` pour partager le meme reseau Docker que l'application.

Role principal:

- Suivre l'etat des conteneurs.
- Visualiser l'utilisation CPU et memoire.
- Offrir une interface de tableaux de bord via Grafana.

Fonctionnement:

- Prometheus collecte les metriques.
- cAdvisor expose les metriques des conteneurs Docker.
- Grafana interroge Prometheus et affiche les tableaux de bord.

Avantages:

- Monitoring simple a demarrer en local.
- Vue centralisee sur le service et les conteneurs.
- Base utile pour evoluer vers une supervision plus avancee.

Limites:

- Le service PHP du projet n'expose pas encore de vraies metriques applicatives via `/metrics`.
- La supervision de la base MySQL peut etre amelioree avec un exporteur dedie.
- Cette pile reste locale et ne remplace pas une solution d'observabilite de production complete.

### 4.2.3 Kubernetes

Le repertoire `kubernetes/` contient les manifests declaratifs du projet.

Role principal:

- Deployer l'application dans un cluster Kubernetes.
- Separer les ressources en Namespace, ConfigMap, Secret, Deployment, Service et Ingress.
- Permettre un deploiement reproductible et portable.

Fonctionnement:

- `namespace.yaml` isole les ressources du projet.
- `configmap.yaml` porte les valeurs non sensibles.
- `secret.example.yaml` documente les secrets a fournir.
- `deployment.yaml` definit le pod applicatif.
- `service.yaml` expose l'application en interne.
- `ingress.yaml` publie l'application via un Ingress Controller.

Avantages:

- Infrastructure declarative.
- Facile a versionner et a auditer.
- Base propre pour ajouter l'auto-scaling ou des strategies de rollout.

Limites:

- Le projet suppose encore une base MySQL externe.
- Le stockage des uploads exige une attention particuliere si plusieurs replicas sont utilises.
- Un cluster et un Ingress Controller restent necessaires pour exploiter cette couche.

### 4.2.4 Ansible

Le repertoire `ansible/` automatise le deploiement Kubernetes avec un playbook simple.

Role principal:

- Orchestrer l'application des manifests Kubernetes.
- Verifier la disponibilite de `kubectl`.
- Fournir un point d'entree d'automatisation supplementaire.

Fonctionnement:

- `inventory.ini` cible la machine locale ou une machine d'administration.
- `deploy-kubernetes.yml` lance `kubectl apply` sur les ressources du dossier `kubernetes/`.

Avantages:

- Automatisation lisible et simple.
- Permet de standardiser le deploiement d'un cluster deja prepare.
- Facile a etendre avec d'autres roles Ansible.

Limites:

- Ansible ne remplace pas Kubernetes; il orchestre seulement le deploiement.
- Le playbook suppose que `kubectl` et l'acces au cluster existent deja.
- Pour une vraie mise en production, il faut souvent completer avec des roles de provisioning ou des secrets managers.

### 4.3 GitHub

GitHub sert de depot source et de point d'entree pour l'automatisation.

Role principal:

- Stocker le code.
- Gerer les versions.
- Declencher les workflows CI/CD.

Bonnes pratiques observees:

- Un workflow principal pour le deploiement.
- Utilisation de secrets pour le webhook Render.

### 4.4 GitHub Actions

GitHub Actions est desormais la solution d'automatisation principale.

Le workflow `deploy.yml` fait les operations suivantes:

1. Recupere le code source.
2. Construit l'image Docker.
3. Declenche le webhook de Render si le secret est defini.

Role principal:

- Remplacer le pipeline Jenkins par une CI/CD integree au depot.
- Simplifier la maintenance de l'automatisation.
- Reagir aux pushes sur la branche principale et aux lancements manuels.

Avantages:

- Pas d'infrastructure CI a maintenir.
- Integration native avec GitHub.
- Configuration simple via les secrets du depot.

Limites:

- Le workflow ne lance pas encore de tests automatiques.
- Le build sert surtout de validation de packaging et de redeploiement.

### 4.5 Jenkins

Jenkins a ete utilise comme pipeline historique du projet. Le fichier `Jenkinsfile` montre un flux tres simple:

- checkout du code.
- build de l'image Docker.
- appel du webhook Render.

Role principal:

- Fournir une premiere automatisation CI/CD.
- Documenter la logique de build et de deploiement.

Statut actuel:

- Jenkins n'est plus la voie d'automatisation principale.
- Le fichier et la documentation peuvent rester comme reference technique.

Avantages:

- Pipeline facile a comprendre.
- Separable de GitHub Actions si l'on veut une CI externe.

Limites:

- Necessite une instance Jenkins a heberger et administrer.
- Introduit une couche supplementaire sans benefice majeur ici.

### 4.6 Render

Render est la plateforme de production principale du projet.

Role principal:

- Heberger l'application web dans un service Docker.
- Gerer les redements automatiques.
- Fournir une URL publique et des logs.

Le fichier `render.yaml` definit:

- un service web Docker.
- le chemin de healthcheck.
- les variables d'environnement.

Avantages:

- Deploiement simple a partir d'une image Docker.
- Bonne adequation avec un projet PHP conteneurise.
- Automatisation possible via deploy hook.

Limites:

- La base MySQL n'est pas fournie en gratuit dans ce contexte.
- La supervision avancee reste limitee selon le plan.

### 4.7 Railway

Railway est documente comme alternative de deploiement.

Role principal:

- Proposer un autre hebergeur compatible Docker.
- Permettre une portabilite du projet.

Avantages:

- Reutilise le meme `Dockerfile`.
- Peut convenir a des tests de deploiement ou a un autre environnement de production.

Limites:

- Depend toujours d'une base MySQL externe.
- Necessite une configuration reseau correcte pour l'acces a la base.

### 4.8 MySQL

MySQL est la base de donnees transactionnelle de l'application.

Role principal:

- Stocker les utilisateurs, demandes, types, services et donnees liees au workflow.

Le schema initial est charge depuis `database/unified_database.sql`.

Points d'attention:

- Sauvegardes regulieres.
- Acces distant securise.
- Correspondance entre les variables d'environnement et le service heberge.

## 5. Architecture de la chaine DevOps

### 5.1 Developpement local

Le developpeur lance:

```bash
docker compose up --build
```

Ce demarrage cree:

- l'application PHP/Apache.
- la base MySQL.
- le chargement initial du schema.
- le volume d'uploads.

### 5.2 Integration continue

Le depot GitHub declenche le workflow GitHub Actions sur:

- chaque push sur `main`.
- un lancement manuel via `workflow_dispatch`.

La CI verifie principalement que l'image se construit correctement.

### 5.3 Deploiement continu

Apres la construction de l'image, GitHub Actions envoie une requete au webhook Render.

Render relance alors le service avec la nouvelle image et l'application devient disponible via l'URL publique.

### 5.4 Flux simplifie

```text
Developpement local -> Push GitHub -> GitHub Actions -> Build Docker -> Webhook Render -> Deploiement
```

## 6. Gestion des secrets et variables d'environnement

Les parametres sensibles ne doivent pas etre versionnes dans le code.

Variables principales utilisees:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `BASE_URL`
- `RENDER_DEPLOY_HOOK_URL`

Bonnes pratiques:

- Stocker les secrets dans GitHub, Render ou Jenkins selon le contexte.
- Ne jamais hardcoder les identifiants de base de donnees.
- Garder `BASE_URL` coherent entre local et production.

## 7. Securite et exploitation

### 7.1 Securite

Le projet applique une base raisonnable pour un petit site PHP:

- Authentification separee.
- Gestion de roles.
- Connexion PDO.

Recommandations complementaires:

- Forcer HTTPS en production.
- Utiliser des mots de passe forts.
- Limiter les droits de l'utilisateur MySQL.
- Proteger les uploads.

### 7.2 Exploitation

En exploitation, les points essentiels sont:

- logs de build et de deploiement.
- disponibilite de la base MySQL.
- droits d'ecriture sur `uploads/`.
- verification du healthcheck Render.

### 7.3 Sauvegardes

La base MySQL doit etre sauvegardee regulierement.

Recommandation:

- mettre en place un export automatise.
- conserver une copie hors de la plateforme d'hebergement.
- documenter une procedure de restauration.

## 8. Tests et qualite

Le projet ne contient pas encore une suite de tests automatises complete.

Etat actuel:

- Validation surtout manuelle.
- Verification du build Docker.
- Verification du deploiement Render via webhook.

Evolutions recommandees:

- tests de fumee apres deploiement.
- tests unitaires pour la logique metier.
- controle lint ou syntaxique PHP dans le workflow.

## 9. Bilan des choix DevOps

Les choix retenus sont adaptes a un projet PHP/MySQL de taille moyenne:

- Docker apporte la reproductibilite.
- Docker Compose facilite le local.
- Grafana et Prometheus ajoutent la visibilite operationnelle.
- Kubernetes apporte une couche de deploiement declarative.
- Ansible simplifie l'automatisation de ce deploiement.
- GitHub Actions simplifie la CI/CD.
- Render fournit un hebergement rapide a mettre en place.
- Railway reste disponible comme alternative.
- Jenkins reste documente comme historique.

Cette combinaison limite les couts de maintenance tout en gardant une chaine de livraison claire.

## 10. Limites et risques

- Dependance a une base MySQL externe.
- Absence de tests automatises complets.
- Gestion des fichiers uploades a surveiller.
- Risque de divergence entre environnements si les variables ne sont pas homogenes.
- En mode redondant, la base MySQL peut rester un point de panne unique si elle n'est pas elle aussi dupliquee.
- Sans exporteur applicatif, Grafana supervise surtout l'infrastructure conteneurisee et pas le comportement metier du site.
- Kubernetes devient plus utile si l'on ajoute une strategie d'upload partage et un vrai service MySQL dans le cluster.

## 11. Recommandations futures

1. Ajouter des tests automatiques dans GitHub Actions.
2. Ajouter un controle syntaxique PHP avant build.
3. Documenter une strategie de backup et restauration MySQL.
4. Ajouter une verification post-deploiement.
5. Centraliser les secrets et les variables d'environnement dans un tableau de configuration.
6. Ajouter des metriques applicatives propres pour enrichir les dashboards Grafana.
7. Ajouter une versioning strategy pour les manifests Kubernetes et les roles Ansible.

## 12. Conclusion

La stack DevOps du projet est volontairement simple et efficace. Docker et Docker Compose assurent un socle reproductible, Grafana avec Prometheus ajoute une couche de supervision locale, GitHub Actions pilote la livraison, et Render heberge l'application de maniere directe. Jenkins reste documente comme historique, mais la voie principale est maintenant GitHub Actions. Le mode haute disponibilite ajoute une redondance applicative utile pour absorber la defaillance d'un conteneur, tout en laissant la base MySQL comme composant a traiter pour une vraie haute disponibilite de bout en bout.
