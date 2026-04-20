# Guide Depot GitHub

Ce guide resume l'utilisation du depot GitHub pour le projet.

## Objectif

Le depot GitHub sert a:

- heberger le code source.
- declencher GitHub Actions.
- centraliser la documentation et les guides d'exploitation.

## Mise en place

1. Creer ou connecter le depot GitHub.
2. Pousser le contenu du projet dans la branche principale.
3. Verifier que le workflow [.github/workflows/deploy.yml](../.github/workflows/deploy.yml) est actif.
4. Ajouter le secret `RENDER_DEPLOY_HOOK_URL` dans les secrets du depot.

## Bonnes pratiques

- Utiliser `main` comme branche de reference pour le deploiement.
- Garder les images Docker publiees dans un registre accessible.
- Ne pas versionner les secrets ni les identifiants de base de donnees.
- Consulter l'onglet Actions apres chaque push pour verifier le build.

## Lien avec le projet

Le depot sert de point d'entree pour:

- [Guide GitHub Actions](guide-github-actions.md)
- [Rapport DevOps](rapport-devops-outils.md)
