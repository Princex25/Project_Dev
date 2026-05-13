# Guide Kubernetes

Ce guide decrit les manifestes Kubernetes du dossier `kubernetes/`.

## Vue d'ensemble

Le kustomize deploie:

- Frontend (`devops-project-frontend`) avec 2 replicas + HPA + PDB
- Backend (`devops-project-backend`) avec 2 replicas + HPA + PDB
- MySQL (`mysql`) avec PVC
- Jenkins (`jenkins`) avec PVC
- Monitoring: Prometheus + Grafana
- cAdvisor (DaemonSet)
- Ingress pour le routage `/devops-project` et `/devops-project/api`

Les **conteneurs de secours** sont assures par:

- des replicas multiples (frontend/back)
- les PDB (1 pod minimum disponible)
- les HPA (autoscaling)

## Prerequis

- Un cluster Kubernetes fonctionnel
- Un Ingress Controller (ex: NGINX) si vous utilisez `ingress.yaml`
- Docker Desktop ou un registre pour l'image applicative

## Images

L'application utilise `devops-project-web:latest`. Deux options:

- **Docker Desktop**: construisez l'image localement et elle sera disponible dans le cluster.
- **Cluster distant**: poussez l'image dans un registre et remplacez l'image dans `app-frontend.yaml` et `app-backend.yaml`.

Build local:

```bash
docker build -t devops-project-web:latest .
```

## Secrets

Copiez `secret.example.yaml` en `secret.yaml` et adaptez les valeurs:

```bash
cp kubernetes/secret.example.yaml kubernetes/secret.yaml
```

Puis modifiez `kustomization.yaml` pour utiliser `secret.yaml` a la place de l'exemple.

## Deploiement

```bash
kubectl apply -k kubernetes/
```

## Acces

Port-forward (rapide et portable):

```bash
kubectl -n devops-project port-forward svc/devops-project-frontend 8080:80
kubectl -n devops-project port-forward svc/grafana 3000:3000
kubectl -n devops-project port-forward svc/prometheus 9090:9090
kubectl -n devops-project port-forward svc/cadvisor 8081:8080
kubectl -n devops-project port-forward svc/jenkins 8082:8080
```

Avec Ingress:

- Ajoutez `devops-project.local` dans votre fichier hosts
- Acces: `http://devops-project.local/devops-project`

## Notes

- `cadvisor` utilise des `hostPath`; certains clusters geres peuvent le bloquer.
- MySQL en single-replica est suffisant pour un environnement de test; pour la HA, utilisez un service MySQL gere.
