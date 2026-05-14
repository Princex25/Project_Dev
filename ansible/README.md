# 🎭 Documentation Ansible — Provisioning & Déploiement K3s

## Prérequis

```bash
# Installer Ansible
pip install ansible

# Installer les collections nécessaires
ansible-galaxy collection install community.general
ansible-galaxy collection install community.docker
ansible-galaxy collection install kubernetes.core

# Configurer le vault
cp ansible/.vault_pass.example ansible/.vault_pass
# Modifier .vault_pass avec votre mot de passe sécurisé
```

## Structure des rôles

```
ansible/
├── ansible.cfg                    # Configuration Ansible
├── inventory/
│   ├── hosts                      # Inventaire des hôtes
│   └── group_vars/
│       └── all.yml                # Variables globales
├── roles/
│   ├── common/                    # Configuration de base (Docker, NTP, etc.)
│   │   ├── tasks/main.yml
│   │   ├── handlers/main.yml
│   │   ├── defaults/main.yml
│   │   └── templates/
│   │       └── chrony.conf.j2
│   ├── k3s_server/                # Installation K3s Server (Master)
│   │   ├── tasks/main.yml
│   │   ├── handlers/main.yml
│   │   └── defaults/main.yml
│   ├── k3s_agent/                 # Installation K3s Agent (Worker)
│   │   ├── tasks/main.yml
│   │   └── defaults/main.yml
│   ├── app/                       # Déploiement de l'application
│   │   ├── tasks/main.yml
│   │   ├── handlers/main.yml
│   │   ├── defaults/main.yml
│   │   └── templates/
│   │       ├── namespace.yaml.j2
│   │       ├── configmap.yaml.j2
│   │       ├── secret.yaml.j2
│   │       ├── efs-storage.yaml.j2
│   │       ├── deployment.yaml.j2
│   │       ├── ingress.yaml.j2
│   │       ├── hpa.yaml.j2
│   │       ├── pdb.yaml.j2
│   │       └── config.php.j2
│   └── monitoring/                # Installation Prometheus + Grafana
│       ├── tasks/main.yml
│       └── defaults/main.yml
├── site.yml                       # Playbook principal (complet)
├── provision.yml                  # Playbook de provisioning
├── deploy.yml                     # Playbook de déploiement
├── monitoring.yml                 # Playbook de monitoring
└── .vault_pass.example            # Exemple de fichier vault
```

## Utilisation

### Provisioning complet (Infrastructure + K3s + App + Monitoring)

```bash
ansible-playbook -i inventory/hosts site.yml
```

### Provisioning uniquement (Infrastructure + K3s sans App)

```bash
ansible-playbook -i inventory/hosts provision.yml
```

### Déploiement de l'application uniquement

```bash
ansible-playbook -i inventory/hosts deploy.yml
```

### Installation du monitoring uniquement

```bash
ansible-playbook -i inventory/hosts monitoring.yml
```

### Mise à jour de l'application (après un nouveau push Docker)

```bash
ansible-playbook -i inventory/hosts deploy.yml --tags app
```

## Workflow Complet

```
1. terraform apply          → Crée l'infrastructure (VPC, EC2, RDS, EFS, ALB)
2. ansible-playbook site.yml → Installe K3s, déploie l'app, configure le monitoring
3. Jenkins CI/CD             → Automatise le cycle : Build → Push → Terraform → Ansible
```

## Variables importantes

| Variable | Emplacement | Description |
|---|---|---|
| `k3s_token` | `inventory/hosts` et `inventory/group_vars/all.yml` | Token d'authentification K3s |
| `db_password` | `inventory/group_vars/all.yml` (ou Vault) | Mot de passe MySQL |
| `domain_name` | `inventory/group_vars/all.yml` | Nom de domaine du site |
| `ecr_repo` | `ansible/roles/app/defaults/main.yml` | URL du repository ECR |
| `app_replicas` | `ansible/roles/app/defaults/main.yml` | Nombre de réplicas (défaut: 3) |

## Sécurité

- Les secrets sont gérés via **Ansible Vault**
- Le mot de passe vault est dans `.vault_pass` (⚠️ **NE JAMAIS COMMITER**)
- Les mots de passe base de données doivent être stockés dans **AWS Secrets Manager** en production
- La clé SSH Terraform est générée automatiquement et sauvegardée dans AWS Secrets Manager