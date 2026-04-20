# Guide GitHub Actions

Ce guide remplace Jenkins par GitHub Actions pour construire l'image Docker et declencher le deploy Render.

## Principe

Le workflow [.github/workflows/deploy.yml](../.github/workflows/deploy.yml) fait trois choses:

1. Recupere le code du depot.
2. Construit l'image Docker du projet.
3. Declenche le webhook de deploiement Render si le secret est configure.

## Configuration

Ajouter un secret GitHub dans le depot:

- Nom: `RENDER_DEPLOY_HOOK_URL`
- Valeur: URL du Deploy Hook Render

## Declenchement

Le workflow se lance:

- a chaque push sur `main`
- manuellement via `workflow_dispatch`

## Verification

Apres un push sur `main`, verifier l'onglet Actions du depot puis la page Render pour confirmer le deploiement.

## Notes

- Le fichier [Jenkinsfile](../Jenkinsfile) peut rester en place comme reference, mais il n'est plus necessaire pour l'automatisation courante.
- Le build Docker utilise la meme base que le pipeline Jenkins.
