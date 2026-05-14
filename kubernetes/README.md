# 🚀 Déploiement Kubernetes — Gestion des Demandes

## Architecture

```
┌─────────────────────────────────────────────────────┐
│                   AWS EKS Cluster                    │
│                                                     │
│   ┌──────────┐ ┌──────────┐ ┌──────────┐           │
│   │ Pod #1   │ │ Pod #2   │ │ Pod #3   │           │
│   │ App PHP  │ │ App PHP  │ │ App PHP  │           │
│   └────┬─────┘ └────┬─────┘ └────┬─────┘           │
│        │             │             │                 │
│   ┌────▼─────────────▼─────────────▼────┐           │
│   │       Service (ClusterIP: 80)       │           │
│   └─────────────────┬───────────────────┘           │
│                     │                               │
│              ┌──────▼───────┐                       │
│              │  ALB Ingress │                       │
│              │  (HTTPS/80)  │                       │
│              └──────┬───────┘                       │
│                     │                               │
│              ┌──────▼───────┐                       │
│              │   Internet   │                       │
│              └──────────────┘                       │
│                                                     │
│  Stockage : Amazon EFS (lecture/écriture partagée)  │
│  Base de données : Amazon RDS MySQL                 │
│  Auto-scaling : HPA (3-10 pods)                     │
└─────────────────────────────────────────────────────┘
```

## Prérequis

- [AWS CLI](https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html) configurée
- [kubectl](https://kubernetes.io/docs/tasks/tools/) installé
- [eksctl](https://eksctl.io/) installé
- Cluster EKS existant

## Structure des fichiers

```
kubernetes/
├── namespace.yaml        # Espace de noms du projet
├── configmap.yaml        # Variables de configuration (non sensibles)
├── secret.yaml           # Secrets (mots de passe DB)
├── deployment.yaml       # Déploiement (3 réplicas) + Service
├── ingress.yaml          # Ingress ALB (AWS Load Balancer)
├── hpa.yaml              # Auto-scaling horizontal
├── pdb.yaml              # Pod Disruption Budget
├── efs-storage.yaml      # Volume persistant EFS
└── kustomization.yaml    # Kustomize pour déploiement groupé
```

## Déploiement rapide

```bash
# 1. Créer le cluster EKS (une seule fois)
eksctl create cluster \
  --name devops-project-cluster \
  --region us-east-1 \
  --nodegroup-name standard-workers \
  --node-type t3.medium \
  --nodes 3 \
  --nodes-min 3 \
  --nodes-max 5

# 2. Déployer via kustomize
kubectl apply -k kubernetes/

# 3. Vérifier le statut
kubectl -n devops-project get pods
kubectl -n devops-project get svc
kubectl -n devops-project get ingress
```

## Haute disponibilité (99.99%)

| Mécanisme | Description |
|---|---|
| **3 réplicas minimum** | Si un pod tombe, les 2 autres prennent le relais |
| **PodAntiAffinity** | Les pods sont répartis sur des nœuds différents |
| **PodDisruptionBudget** | Garantit au moins 2 pods disponibles pendant les mises à jour |
| **HPA (Auto-scaling)** | Scale automatiquement de 3 à 10 pods selon la charge |
| **RollingUpdate** | Mise à jour sans interruption (0 indisponibilité) |
| **Readiness/Liveness probes** | Kubernetes détecte et remplace les pods défaillants |
| **Amazon RDS** | Base de données managée avec multi-AZ |
| **Amazon EFS** | Stockage partagé entre tous les pods |
| **ALB Ingress** | Load balancer avec health checks automatiques |

## Variables d'environnement

| Variable | Source | Description |
|---|---|---|
| `DB_HOST` | ConfigMap | Adresse du serveur RDS MySQL |
| `DB_PORT` | ConfigMap | Port MySQL (3306) |
| `DB_NAME` | ConfigMap | Nom de la base (gestion_demandes) |
| `BASE_URL` | ConfigMap | URL de base de l'application |
| `DB_USER` | Secret | Utilisateur MySQL |
| `DB_PASS` | Secret | Mot de passe MySQL |

## Monitoring

```bash
# Voir les pods
kubectl -n devops-project get pods -w

# Voir les logs d'un pod
kubectl -n devops-project logs <nom-du-pod> -f

# Voir le statut du HPA
kubectl -n devops-project get hpa

# Voir les événements
kubectl -n devops-project get events --sort-by='.lastTimestamp'
```

## Nettoyage

```bash
kubectl delete -k kubernetes/
```