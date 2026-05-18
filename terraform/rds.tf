# ============================================================
# RDS — Base de données MySQL (Multi-AZ)
# ============================================================

# --- Sous-groupe de sécurité RDS ---
resource "aws_security_group" "rds" {
  name_prefix = "${var.project_name}-rds-${var.environment}"
  description = "Security group pour RDS MySQL"
  vpc_id      = aws_vpc.main.id

  ingress {
    description     = "MySQL depuis les pods K3s"
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.k3s_server.id, aws_security_group.k3s_agent.id]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-rds-sg-${var.environment}"
  })

  lifecycle {
    create_before_destroy = true
  }
}

# --- Subnet Group RDS ---
resource "aws_db_subnet_group" "main" {
  name       = "devops-project-rds-subnet-dev"
  subnet_ids = ["subnet-0991015ed2fbeb708", "subnet-04f6bdcbbb2c31a2f", "subnet-029380706618b788d"]

  tags = merge(var.common_tags, {
    Name = "devops-project-rds-subnet-group-dev"
  })
}

# --- Instance RDS MySQL ---
resource "aws_db_instance" "main" {
  identifier = "${var.project_name}-rds-${var.environment}"

  engine               = "mysql"
  engine_version       = "8.0.46"
  instance_class       = var.rds_instance_class
  allocated_storage    = var.rds_allocated_storage
  max_allocated_storage = 100

  storage_type          = "gp3"
  storage_encrypted     = true
  kms_key_id           = aws_kms_key.rds.arn

  db_name  = "gestion_demandes"
  username = "app"
  password = random_password.rds.result

  multi_az               = var.rds_multi_az
  publicly_accessible    = false
  auto_minor_version_upgrade = true

  backup_retention_period = 0
  backup_window          = "03:00-04:00"
  maintenance_window     = "sun:04:00-sun:05:00"

  skip_final_snapshot = true

  vpc_security_group_ids = [aws_security_group.rds.id]
  db_subnet_group_name   = aws_db_subnet_group.main.name

  enabled_cloudwatch_logs_exports = ["error", "general", "slowquery"]

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-rds-${var.environment}"
  })
}

# --- Clé KMS pour chiffrement RDS ---
resource "aws_kms_key" "rds" {
  description             = "Clé KMS pour chiffrement RDS"
  deletion_window_in_days = 10

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-kms-rds-${var.environment}"
  })
}

resource "aws_kms_alias" "rds" {
  name          = "alias/${var.project_name}-rds-${var.environment}"
  target_key_id = aws_kms_key.rds.id
}

# --- Mot de passe aléatoire pour RDS ---
resource "random_password" "rds" {
  length  = 32
  special = true
  upper   = true
  lower   = true
  numeric = true

  override_special = "!#$%^&*()-_=+"
}