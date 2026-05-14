# ============================================================
# Variables
# ============================================================

# --- Général ---
variable "aws_region" {
  description = "Région AWS"
  type        = string
  default     = "us-east-1"
}

variable "environment" {
  description = "Environnement (dev, staging, prod)"
  type        = string
  default     = "dev"

  validation {
    condition     = contains(["dev", "staging", "prod"], var.environment)
    error_message = "L'environnement doit être dev, staging ou prod."
  }
}

variable "project_name" {
  description = "Nom du projet"
  type        = string
  default     = "devops-project"
}

# --- VPC ---
variable "vpc_cidr" {
  description = "CIDR du VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "public_subnet_cidrs" {
  description = "CIDRs des sous-réseaux publics"
  type        = list(string)
  default     = ["10.0.1.0/24", "10.0.2.0/24", "10.0.3.0/24"]
}

variable "private_subnet_cidrs" {
  description = "CIDRs des sous-réseaux privés"
  type        = list(string)
  default     = ["10.0.10.0/24", "10.0.20.0/24", "10.0.30.0/24"]
}

# --- EKS ---
variable "eks_cluster_version" {
  description = "Version de Kubernetes"
  type        = string
  default     = "1.30"
}

variable "eks_node_min_size" {
  description = "Nombre minimum de nœuds"
  type        = number
  default     = 3
}

variable "eks_node_max_size" {
  description = "Nombre maximum de nœuds"
  type        = number
  default     = 6
}

variable "eks_node_desired_size" {
  description = "Nombre souhaité de nœuds"
  type        = number
  default     = 3
}

variable "eks_instance_type" {
  description = "Type d'instance EC2 pour les nœuds"
  type        = string
  default     = "t3.medium"
}

# --- RDS ---
variable "rds_instance_class" {
  description = "Classe d'instance RDS"
  type        = string
  default     = "db.t3.micro"
}

variable "rds_allocated_storage" {
  description = "Stockage alloué (Go)"
  type        = number
  default     = 20
}

variable "rds_multi_az" {
  description = "Activer Multi-AZ"
  type        = bool
  default     = true
}

# --- EFS ---
variable "efs_throughput_mode" {
  description = "Mode de débit EFS"
  type        = string
  default     = "elastic"
}

# --- Domaine & SSL ---
variable "domain_name" {
  description = "Nom de domaine pour le site"
  type        = string
  default     = ""
}

variable "acm_certificate_arn" {
  description = "ARN du certificat ACM (laisser vide pour staging)"
  type        = string
  default     = ""
}

# --- Tags communs ---
variable "common_tags" {
  description = "Tags communs à toutes les ressources"
  type        = map(string)
  default = {
    Project     = "devops-project"
    ManagedBy   = "terraform"
  }
}