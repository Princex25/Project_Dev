# Guide Ansible

Ce guide montre comment Ansible automatise le deploiement Kubernetes du projet.

## Objectif

Ansible sert ici de couche d'automatisation pour:

- verifier la disponibilite de `kubectl`.
- appliquer les manifests Kubernetes de facon reproductible.
- lancer le deploiement depuis une machine d'administration.

## Fichiers fournis

- Inventory: [ansible/inventory.ini](../ansible/inventory.ini)
- Playbook: [ansible/deploy-kubernetes.yml](../ansible/deploy-kubernetes.yml)

## Lancement

```bash
ansible-playbook -i ansible/inventory.ini ansible/deploy-kubernetes.yml
```

## Pre-requis

- Ansible installe sur la machine de lancement.
- `kubectl` installe et configure.
- Acces au cluster Kubernetes via le contexte courant.

## Flux

1. Ansible teste `kubectl version --client`.
2. Ansible applique la base Kustomize du repertoire `kubernetes/`.
3. Kubernetes cree le namespace, la configuration, le stockage, le secret exemple, le deployment, le service et l'ingress.

## Limites

- Ce playbook ne cree pas le cluster Kubernetes lui-meme.
- Il suppose que l'image Docker du projet est deja publiee dans un registre accessible.
- Pour une configuration complete d'infrastructure, on peut ajouter des roles Ansible supplementaires pour installer un cluster ou preparer un serveur d'administration.
