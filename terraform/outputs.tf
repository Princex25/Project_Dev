# ============================================================
# Outputs — Informations importantes de l'infrastructure
# ============================================================

# --- VPC ---
output "vpc_id" {
  description = "ID du VPC"
  value       = aws_vpc.main.id
}

output "vpc_cidr" {
  description = "CIDR du VPC"
  value       = aws_vpc.main.cidr_block
}

output "public_subnet_ids" {
  description = "IDs des sous-réseaux publics"
  value       = aws_subnet.public[*].id
}

output "private_subnet_ids" {
  description = "IDs des sous-réseaux privés"
  value       = aws_subnet.private[*].id
}

# --- EKS ---
output "eks_cluster_name" {
  description = "Nom du cluster EKS"
  value       = module.eks.cluster_name
}

output "eks_cluster_endpoint" {
  description = "Endpoint du cluster EKS"
  value       = module.eks.cluster_endpoint
}

output "eks_cluster_arn" {
  description = "ARN du cluster EKS"
  value       = module.eks.cluster_arn
}

output "eks_cluster_ca_certificate" {
  description = "Certificat CA du cluster EKS (base64)"
  value       = module.eks.cluster_certificate_authority_data
  sensitive   = true
}

output "eks_oidc_provider_arn" {
  description = "ARN du fournisseur OIDC EKS"
  value       = module.eks.oidc_provider_arn
}

# --- RDS ---
output "rds_endpoint" {
  description = "Endpoint de l'instance RDS"
  value       = aws_db_instance.main.endpoint
}

output "rds_address" {
  description = "Adresse de l'instance RDS"
  value       = aws_db_instance.main.address
}

output "rds_port" {
  description = "Port de l'instance RDS"
  value       = aws_db_instance.main.port
}

output "rds_database_name" {
  description = "Nom de la base de données"
  value       = aws_db_instance.main.db_name
}

output "rds_connection_string" {
  description = "Chaîne de connexion RDS (sensible)"
  value       = "mysql://${aws_db_instance.main.username}:${random_password.rds.result}@${aws_db_instance.main.endpoint}/${aws_db_instance.main.db_name}"
  sensitive   = true
}

# --- EFS ---
output "efs_id" {
  description = "ID du système de fichiers EFS"
  value       = aws_efs_file_system.main.id
}

output "efs_dns_name" {
  description = "Nom DNS de l'EFS"
  value       = aws_efs_file_system.main.dns_name
}

# --- ECR ---
output "ecr_repository_url" {
  description = "URL du repository ECR"
  value       = aws_ecr_repository.web.repository_url
}

# --- Security Groups ---
output "eks_nodes_sg_id" {
  description = "ID du security group des nœuds EKS"
  value       = aws_security_group.eks_nodes.id
}

output "alb_sg_id" {
  description = "ID du security group de l'ALB"
  value       = aws_security_group.alb.id
}

output "rds_sg_id" {
  description = "ID du security group de RDS"
  value       = aws_security_group.rds.id
}

output "efs_sg_id" {
  description = "ID du security group d'EFS"
  value       = aws_security_group.efs.id
}