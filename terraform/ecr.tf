# ============================================================
# ECR — Registry Docker privé
# ============================================================

resource "aws_ecr_repository" "web" {
  name = "${var.project_name}-web-${var.environment}"

  image_scanning_configuration {
    scan_on_push = true
  }

  encryption_configuration {
    encryption_type = "AES256"
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-ecr-${var.environment}"
  })
}