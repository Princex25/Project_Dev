# 🏗️ Infrastructure Terraform — Gestion des Demandes

## Vue d'ensemble

Ce dossier contient toute l'infrastructure AWS provisionnée via **Terraform** pour le projet **Gestion des Demandes**.

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                         AWS                                  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │                    VPC (10.0.0.0/16)                 │   │
│  │                                                      │   │
│  │  ┌───────┐  ┌───────┐  ┌───────┐    Internet        │   │
│  │  │Public 1│  │Public 2│  │Public 3│ ←── Gateway      │   │
│  │  └───┬───┘  └───┬───┘  └───┬───┘                    │   │
│  │      │           │           │                        │   │
│  │  ┌───▼───┐  ┌───▼───┐  ┌───▼───┐    NAT Gateway     │   │
│  │  │Priv 1 │  │Priv 2 │  │Priv 3 │ ◄── Elastic IP    │   │
│  │  └───┬───┘  └───┬───┘  └───┬───┘                    │   │
│  │      │           │           │                        │   │
│  │  ┌───▼───────────────────────────────┐               │   │
│  │  │         EKS Cluster               │               │   │
│  │  │  ┌────────┐┌────────┐┌────────┐  │               │   │
│  │  │  │ Pod #1 ││ Pod #2 ││ Pod #3 │  │               │   │
│  │  │  └───┬────┘└───┬────┘└───┬────┘  │               │   │
│  │  │      └─────────┴─────────┘        │               │   │
│  │  │  ┌────────────────────────────┐   │               │   │
│  │  │  │  Service + ALB Ingress     │   │               │   │
│  │  │  └──────────┬─────────────────┘   │               │   │
│  │  └─────────────┼────────────────────┘               │   │
│  │                │                                   │   │
│  │  ┌─────────────▼────────────────────────────────────┐│   │
│  │  │            AWS ALB (Load Balancer)                ││   │
│  │  └─────────────┬────────────────────────────────────┘│   │
│  └────────────────│─────────────────────────────────────┘   │
│                  │                                          │
│                  ▼                                          │
│         ┌──────────────┐                                   │
│         │   Internet   │                                   │
│         └──────────────┘                                   │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Amazon RDS MySQL (Multi-AZ)                          │   │
│  │  ┌─────────┐  ┌─────────┐                            │   │
│  │  │ Primary │→ │ Standby │ (auto-failover)             │   │
│  │  └─────────┘  └─────────┘                            │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Amazon EFS (stockage partagé)                        │   │
│  │  ┌──────────────────────────────────────────────────┐ │   │
│  │  │ Mount Targets (3 AZ) + Backups automatiques      │ │   │
│  │  └──────────────────────────────────────────────────┘ │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌──────────────────────────┐                               │
│  │  Amazon ECR              │                               │
│  │  (Registry Docker privé) │                               │
│  └──────────────────────────┘                               │
└─────────────────────────────────────────────────────────────┘
```

## Fichiers

| Fichier | Rôle |
|---|---|
| `main.tf` | Fournisseurs AWS, Kubernetes, Helm + données |
| `variables.tf` | Toutes les variables configurables |
| `versions.tf` | Versions Terraform + providers + backend S3 |
| `vpc.tf` | VPC, subnets, NAT Gateway, Internet Gateway |
| `eks.tf` | Cluster EKS + Node Groups + IAM roles |
| `rds.tf` | Base de données MySQL Multi-AZ + KMS |
| `efs.tf` | Système de fichiers EFS + backup + mount targets |
| `ecr.tf` | Registry Docker privé |
| `alb.tf` | Load Balancer + Listeners + Target Groups |
| `security-groups.tf` | Security Groups (EKS, ALB, RDS, EFS) |
| `iam.tf` | Rôles IAM + politiques |
| `outputs.tf` | Valeurs de sortie de l'infrastructure |
| `terraform.tfvars` | Configuration réelle (⚠️ NE PAS COMMITER) |
| `terraform.tfvars.example` | Exemple de configuration |

## Prérequis

```bash
# Installer Terraform (v1.5+)
# https://developer.hashicorp.com/terraform/install

# Configurer les credentials AWS
aws configure
# ou utiliser un profil :
export AWS_PROFILE=devops-project
```

## Déploiement

### 1. Initialisation

```bash
cd terraform/
terraform init
```

### 2. Plan (aperçu des changements)

```bash
# Copier et configurer le fichier tfvars
cp terraform.tfvars.example terraform.tfvars
# Éditer terraform.tfvars avec vos valeurs

terraform plan -var-file="terraform.tfvars"
```

### 3. Appliquer

```bash
terraform apply -var-file="terraform.tfvars"
```

### 4. Récupérer les outputs

```bash
terraform output
```

### 5. Déployer les manifests Kubernetes

```bash
# Configurer kubectl
aws eks update-kubeconfig --region us-east-1 --name devops-project-cluster-dev

# Déployer l'application
cd ..
kubectl apply -k kubernetes/
```

## Environnements

| Environnement | Utilisation | Nodes | Instance Type | RDS |
|---|---|---|---|---|
| `dev` | Développement | 3 | t3.medium | db.t3.micro |
| `staging` | Pré-production | 3 | t3.medium | db.t3.small |
| `prod` | Production | 3-6 | t3.large | db.t3.medium (Multi-AZ) |

## Destruction

⚠️ **ATTENTION** — Ceci supprime TOUTE l'infrastructure :

```bash
terraform destroy -var-file="terraform.tfvars"
```

## Sécurité

- VPC isolé avec sous-réseaux publics et privés
- RDS chiffré avec KMS dans un sous-réseau privé
- EFS chiffré en transit et au repos
- Security Groups restrictifs (least privilege)
- IAM avec politiques minimales
- ALB avec redirection HTTP → HTTPS
- Secrets stockés dans Kubernetes Secrets (ou AWS Secrets Manager)