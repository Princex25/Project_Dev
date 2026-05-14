# ============================================================
# Security Groups — EKS Nodes et ALB
# ============================================================

# --- Security Group pour les nœuds EKS ---
resource "aws_security_group" "eks_nodes" {
  name_prefix = "${var.project_name}-eks-nodes-${var.environment}"
  description = "Security group pour les nœuds EKS"
  vpc_id      = aws_vpc.main.id

  # Traffic sortant
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "Allow all outbound"
  }

  # Traffic entrant inter-pods (tous ports)
  ingress {
    from_port       = 0
    to_port         = 65535
    protocol        = "tcp"
    self            = true
    description     = "Allow all traffic between nodes"
  }

  # SSH depuis le VPN/bastion (restreindre en prod)
  ingress {
    description     = "SSH depuis les subnets publics"
    from_port       = 22
    to_port         = 22
    protocol        = "tcp"
    cidr_blocks     = var.environment == "prod" ? [] : ["10.0.0.0/8"]
    ipv6_cidr_blocks = var.environment == "prod" ? [] : ["::/0"]
  }

  # HTTP/HTTPS depuis l'ALB
  ingress {
    description     = "HTTP depuis l'ALB"
    from_port       = 80
    to_port         = 80
    protocol        = "tcp"
    security_groups = [aws_security_group.alb.id]
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-eks-nodes-sg-${var.environment}"
  })

  lifecycle {
    create_before_destroy = true
  }
}

# --- Security Group pour l'ALB ---
resource "aws_security_group" "alb" {
  name_prefix = "${var.project_name}-alb-${var.environment}"
  description = "Security group pour l'Application Load Balancer"
  vpc_id      = aws_vpc.main.id

  # Traffic entrant HTTP
  ingress {
    description = "HTTP"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Traffic entrant HTTPS
  ingress {
    description = "HTTPS"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Traffic sortant
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "Allow all outbound"
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-alb-sg-${var.environment}"
  })

  lifecycle {
    create_before_destroy = true
  }
}