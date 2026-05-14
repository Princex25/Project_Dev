# ============================================================
# EFS — Système de fichiers partagé (pour uploads, sessions PHP)
# ============================================================

# --- Système de fichiers EFS ---
resource "aws_efs_file_system" "main" {
  creation_token = "${var.project_name}-efs-${var.environment}"

  encrypted = true

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-efs-${var.environment}"
  })

  lifecycle_policy {
    transition_to_ia = "AFTER_90_DAYS"
  }
}

# --- Politique de cycle de vie EFS (backup) ---
resource "aws_efs_backup_policy" "main" {
  file_system_id = aws_efs_file_system.main.id

  backup_policy {
    automatic_backup_policy {
      status = "ENABLED"
    }
  }
}

# --- Cible de montage EFS (un par AZ = 3 cibles) ---
resource "aws_efs_mount_target" "main" {
  count = length(aws_subnet.private)

  file_system_id  = aws_efs_file_system.main.id
  subnet_id       = aws_subnet.private[count.index].id
  security_groups = [aws_security_group.efs.id]
}

# --- Sous-groupe de sécurité EFS ---
resource "aws_security_group" "efs" {
  name_prefix = "${var.project_name}-efs-${var.environment}"
  description = "Security group pour EFS mount targets"
  vpc_id      = aws_vpc.main.id

  ingress {
    description     = "NFS depuis les pods EKS"
    from_port       = 2049
    to_port         = 2049
    protocol        = "tcp"
    security_groups = [aws_security_group.eks_nodes.id]
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-efs-sg-${var.environment}"
  })

  lifecycle {
    create_before_destroy = true
  }
}