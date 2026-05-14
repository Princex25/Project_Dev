# ============================================================
# Outputs — Informations importantes de l'infrastructure
# ============================================================

# --- VPC ---
output "vpc_id" {
  description = "ID du VPC"
  value       = aws_vpc.main.id
}

output "public_subnet_ids" {
  description = "IDs des sous-réseaux publics"
  value       = aws_subnet.public[*].id
}

output "private_subnet_ids" {
  description = "IDs des sous-réseaux privés"
  value       = aws_subnet.private[*].id
}

# --- K3s EC2 ---
output "k3s_server_ip" {
  description = "Adresse IP privée du serveur K3s"
  value       = aws_instance.k3s_server.private_ip
}

output "k3s_server_public_ip" {
  description = "Adresse IP publique du serveur K3s"
  value       = aws_instance.k3s_server.public_ip
}

output "k3s_instance_id" {
  description = "ID de l'instance K3s Server"
  value       = aws_instance.k3s_server.id
}

output "k3s_keypair_name" {
  description = "Nom de la paire de clés SSH"
  value       = aws_key_pair.k3s.key_name
}

output "k3s_private_key" {
  description = "Clé privée SSH pour K3s (SENSIBLE)"
  value       = tls_private_key.ssh.private_key_pem
  sensitive   = true
}

# --- ASG K3s Agents ---
output "k3s_asg_name" {
  description = "Nom de l'Auto Scaling Group des agents"
  value       = aws_autoscaling_group.k3s_agents.name
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

# --- ALB ---
output "alb_dns_name" {
  description = "Nom DNS de l'ALB"
  value       = aws_lb.main.dns_name
}

output "alb_zone_id" {
  description = "Zone ID de l'ALB (pour Route53)"
  value       = aws_lb.main.zone_id
}

# --- Security Groups ---
output "k3s_server_sg_id" {
  description = "ID du security group du serveur K3s"
  value       = aws_security_group.k3s_server.id
}

output "k3s_agent_sg_id" {
  description = "ID du security group des agents K3s"
  value       = aws_security_group.k3s_agent.id
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