# Guide GitHub Actions

Ce guide remplace Jenkins par GitHub Actions pour construire l'image Docker.

## Definition et objectif

**GitHub Actions** est la plateforme CI/CD integree a GitHub. Dans ce projet, elle sert a:

- automatiser le build Docker a chaque push.
- centraliser la CI/CD directement dans le depot.

## Techniques utilisees

- **CI/CD** base sur des workflows YAML.
- **Infrastructure as Code (CI)**: le workflow est versionne dans `.github/workflows`.

## Principe

Le workflow [.github/workflows/deploy.yml](../.github/workflows/deploy.yml) fait trois choses:

1. Recupere le code du depot.
2. Construit l'image Docker du projet.

## Declenchement

Le workflow se lance:

- a chaque push sur `main`
- manuellement via `workflow_dispatch`

## Verification

Apres un push sur `main`, verifier l'onglet Actions du depot pour confirmer le build et le deploiement (si le flux s'étend par la suite).

## Notes

- Le fichier [Jenkinsfile](../Jenkinsfile) peut rester en place comme reference, mais il n'est plus necessaire pour l'automatisation courante.
- Le build Docker utilise la meme base que le pipeline Jenkins.

## Explication du workflow

Le workflow [.github/workflows/deploy.yml](../.github/workflows/deploy.yml) contient:

- **Triggers**: `push` sur `main` et `workflow_dispatch` pour un lancement manuel.
- **Checkout**: recupere le code source.
- **Build Docker image**: construit l'image `devops-project-web` avec un tag lie au run GitHub.

Role dans le projet: automatiser la livraison de l'application sans serveur Jenkins dedie.
