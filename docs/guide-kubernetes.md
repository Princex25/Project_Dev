# Guide Kubernetes

Ce guide decrit le deploiement de l'application dans Kubernetes.

## Objectif

Kubernetes sert ici a:

- decrire l'application comme un ensemble de ressources declaratives.
- exposer le service PHP via un Service et un Ingress.
- isoler la configuration dans un ConfigMap et un Secret.

## Ressources fournies

- Namespace: [kubernetes/namespace.yaml](../kubernetes/namespace.yaml)
- ConfigMap: [kubernetes/configmap.yaml](../kubernetes/configmap.yaml)
- Secret exemple: [kubernetes/secret.example.yaml](../kubernetes/secret.example.yaml)
- PVC: [kubernetes/persistentvolumeclaim.yaml](../kubernetes/persistentvolumeclaim.yaml)
- Deployment: [kubernetes/deployment.yaml](../kubernetes/deployment.yaml)
- Service: [kubernetes/service.yaml](../kubernetes/service.yaml)
- Ingress: [kubernetes/ingress.yaml](../kubernetes/ingress.yaml)

## Pre-requis

- Un cluster Kubernetes accessible.
- `kubectl` configure avec le bon contexte.
- Un Ingress Controller, par exemple NGINX Ingress.
- Un registre d'images accessible pour l'image PHP construite depuis le `Dockerfile`.

## Image Docker

Le manifest de deploiement utilise une image exemple `ghcr.io/your-org/admin2-web:latest`.

Remplacez cette valeur par votre propre image avant le deploiement.

## Configuration

Le `ConfigMap` contient les valeurs non sensibles.

Le `Secret` doit contenir:

- `DB_USER`
- `DB_PASS`

Adaptez aussi:

- `DB_HOST`
- `DB_NAME`
- `BASE_URL`

## Deploiement manuel

```bash
kubectl apply -k kubernetes/
```

Le manifest `secret.example.yaml` est inclus dans le Kustomize de base pour fournir un exemple complet. Remplacez ses valeurs avant un usage reel.

## Acces

Le manifest Ingress expose l'application sur:

- host: `admin2.local`
- path: `/admin2`

Ajoutez une entree DNS ou `hosts` locale qui pointe `admin2.local` vers votre Ingress Controller.

## Limites

- Le PVC fourni est suffisant pour un seul pod. Une vraie haute disponibilite applicative avec plusieurs replicas demande un stockage partage de type ReadWriteMany ou un stockage objet pour les uploads.
- La base MySQL n'est pas fournie dans le cluster; le deploiement suppose un service MySQL externe ou deja gere.
