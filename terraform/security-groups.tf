# ============================================================
# Security Groups — K3s EC2 + ALB + RDS + EFS
# ============================================================

# --- Security Group EKS Cluster ---
resource "aws_security_group" "eks_cluster" {
  name_prefix = "${var.project_name}-eks-cluster-${var.environment}"
  description = "Security group for EKS cluster control plane"
  vpc_id      = aws_vpc.main.id

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "Allow all outbound"
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-eks-cluster-sg-${var.environment}"
  })

  lifecycle {
    create_before_destroy = true
  }
}

# --- Security Group K3s Server (Master) ---
resource "aws_security_group" "k3s_server" {
  name_prefix = "${var.project_name}-k3s-server-${var.environment}"
  description = "Security group for K3s server (master)"
  vpc_id      = aws_vpc.main.id

  # Kubernetes API Server
  ingress {
    description     = "K3s API Server (kubelet)"
    from_port       = 6443
    to_port         = 6443
    protocol        = "tcp"
    security_groups = [aws_security_group.k3s_agent.id]
  }

  # K3s server port
  ingress {
    description     = "K3s server port"
    from_port       = 6444
    to_port         = 6444
    protocol        = "tcp"
    security_groups = [aws_security_group.k3s_agent.id]
  }

  # Flannel VXLAN backend
  ingress {
    description     = "Flannel VXLAN"
    from_port       = 8472
    to_port         = 8472
    protocol        = "udp"
    security_groups = [aws_security_group.k3s_agent.id]
  }

  # WireGuard (nouveau K3s)
  ingress {
    description     = "WireGuard"
    from_port       = 51820
    to_port         = 51820
    protocol        = "udp"
    security_groups = [aws_security_group.k3s_agent.id]
  }

  # SSH depuis le VPN/bastion
  ingress {
    description = "SSH"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = var.environment == "prod" ? [] : ["10.0.0.0/8"]
  }

  # NodePort Services (30000-32767)
  ingress {
    description     = "NodePort range"
    from_port       = 30000
    to_port         = 32767
    protocol        = "tcp"
    security_groups = [aws_security_group.alb.id]
  }

  # Egress complet
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "Allow all outbound"
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-k3s-server-sg-${var.environment}"
  })

  lifecycle {
    create_before_destroy = true
  }
}

# --- Security Group K3s Agent (Worker) ---
resource "aws_security_group" "k3s_agent" {
  name_prefix = "${var.project_name}-k3s-agent-${var.environment}"
  description = "Security group for K3s agent nodes"
  vpc_id      = aws_vpc.main.id

  # Inter-node communication (Flannel, WireGuard)
  ingress {
    description     = "Flannel VXLAN"
    from_port       = 8472
    to_port         = 8472
    protocol        = "udp"
    self            = true
  }

  ingress {
    description     = "WireGuard"
    from_port       = 51820
    to_port         = 51820
    protocol        = "udp"
    self            = true
  }

  # Kubernetes kubelet
  ingress {
    description = "Kubelet"
    from_port   = 10250
    to_port     = 10250
    protocol    = "tcp"
    self        = true
  }

  # Overlay réseau pods
  ingress {
    description = "Pod overlay"
    from_port   = 0
    to_port     = 65535
    protocol    = "tcp"
    self        = true
  }

  ingress {
    description = "Pod overlay UDP"
    from_port   = 0
    to_port     = 65535
    protocol    = "udp"
    self        = true
  }

  # SSH (via bastion)
  ingress {
    description = "SSH"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = var.environment == "prod" ? [] : ["10.0.0.0/8"]
  }

  # Egress complet
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "Allow all outbound"
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-k3s-agent-sg-${var.environment}"
  })

  lifecycle {
    create_before_destroy = true
  }
}

# --- Security Group ALB ---
resource "aws_security_group" "alb" {
  name_prefix = "${var.project_name}-alb-${var.environment}"
  description = "Security group for Application Load Balancer"
  vpc_id      = aws_vpc.main.id

  ingress {
    description = "HTTP"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "HTTPS"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

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