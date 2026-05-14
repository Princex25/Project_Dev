# ============================================================
# EC2 — Instances K3s (Server + Agents)
# ============================================================

# --- SSH Key Pair ---
resource "tls_private_key" "ssh" {
  algorithm = "ED25519"
}

resource "aws_key_pair" "k3s" {
  key_name   = "${var.project_name}-k3s-${var.environment}"
  public_key = tls_private_key.ssh.public_key_openssh

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-k3s-keypair-${var.environment}"
  })
}

# --- Données AMI Amazon Linux 2023 ---
data "aws_ami" "amazon_linux" {
  most_recent = true
  owners      = ["amazon"]

  filter {
    name   = "name"
    values = ["al2023-ami-2024*"]
  }

  filter {
    name   = "architecture"
    values = ["x86_64"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }
}

# --- Launch Template pour K3s Server ---
resource "aws_launch_template" "k3s_server" {
  name_prefix   = "${var.project_name}-k3s-server-"
  image_id      = data.aws_ami.amazon_linux.id
  instance_type = var.k3s_server_instance_type

  key_name = aws_key_pair.k3s.key_name

  vpc_security_group_ids = [aws_security_group.k3s_server.id]

  user_data = base64encode(templatefile("${path.module}/templates/k3s-server-cloud-init.sh", {
    k3s_version     = var.k3s_version
    k3s_token       = random_string.k3s_token.result
    k3s_cluster_name = "${var.project_name}-cluster"
    db_endpoint     = aws_db_instance.main.endpoint
    db_name         = "gestion_demandes"
  }))

  tag_specifications {
    resource_type = "instance"
    tags = merge(var.common_tags, {
      Name    = "${var.project_name}-k3s-server-${var.environment}"
      Role    = "k3s-server"
    })
  }

  tag_specifications {
    resource_type = "volume"
    tags = merge(var.common_tags, {
      Name = "${var.project_name}-k3s-server-volume-${var.environment}"
    })
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-k3s-server-lt-${var.environment}"
  })

  monitoring {
    enabled = true
  }

  metadata_options {
    http_endpoint               = "enabled"
    http_tokens                 = "required"  # IMDSv2 obligatoire
    http_put_response_hop_limit = 2
  }

  lifecycle {
    create_before_destroy = true
  }
}

# --- Instance K3s Server ---
resource "aws_instance" "k3s_server" {
  ami                    = data.aws_ami.amazon_linux.id
  instance_type          = var.k3s_server_instance_type
  key_name               = aws_key_pair.k3s.key_name
  subnet_id              = aws_subnet.private[0].id
  vpc_security_group_ids  = [aws_security_group.k3s_server.id]

  user_data = base64encode(templatefile("${path.module}/templates/k3s-server-cloud-init.sh", {
    k3s_version       = var.k3s_version
    k3s_token         = random_string.k3s_token.result
    k3s_cluster_name  = "${var.project_name}-cluster"
    db_endpoint       = aws_db_instance.main.endpoint
    db_name           = "gestion_demandes"
  }))

  root_block_device {
    volume_size           = 50
    volume_type           = "gp3"
    encrypted             = true
    delete_on_termination = true
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-k3s-server-${var.environment}"
    Role = "k3s-server"
  })

  monitoring = true

  metadata_options {
    http_endpoint               = "enabled"
    http_tokens                 = "required"
    http_put_response_hop_limit = 2
  }
}

# --- Launch Template pour K3s Agents ---
resource "aws_launch_template" "k3s_agent" {
  name_prefix   = "${var.project_name}-k3s-agent-"
  image_id      = data.aws_ami.amazon_linux.id
  instance_type = var.k3s_agent_instance_type

  key_name = aws_key_pair.k3s.key_name

  vpc_security_group_ids = [aws_security_group.k3s_agent.id]

  user_data = base64encode(templatefile("${path.module}/templates/k3s-agent-cloud-init.sh", {
    k3s_version       = var.k3s_version
    k3s_token         = random_string.k3s_token.result
    k3s_server_url    = "https://${aws_instance.k3s_server.private_ip}:6443"
    k3s_cluster_name  = "${var.project_name}-cluster"
  }))

  tag_specifications {
    resource_type = "instance"
    tags = merge(var.common_tags, {
      Name    = "${var.project_name}-k3s-agent-${var.environment}"
      Role    = "k3s-agent"
    })
  }

  tag_specifications {
    resource_type = "volume"
    tags = merge(var.common_tags, {
      Name = "${var.project_name}-k3s-agent-volume-${var.environment}"
    })
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-k3s-agent-lt-${var.environment}"
  })

  metadata_options {
    http_endpoint               = "enabled"
    http_tokens                 = "required"
    http_put_response_hop_limit = 2
  }

  lifecycle {
    create_before_destroy = true
  }
}

# --- Auto Scaling Group pour les agents K3s ---
resource "aws_autoscaling_group" "k3s_agents" {
  name_prefix = "${var.project_name}-k3s-agents-"

  min_size         = var.k3s_agent_min_size
  max_size         = var.k3s_agent_max_size
  desired_capacity = var.k3s_agent_desired_size

  launch_template {
    id      = aws_launch_template.k3s_agent.id
    version = "$Latest"
  }

  vpc_zone_identifier = aws_subnet.private[*].id

  health_check_type         = "EC2"
  health_check_grace_period = 300

  force_delete = true

  tag {
    key                 = "Name"
    value               = "${var.project_name}-k3s-agent-${var.environment}"
    propagate_at_launch = true
  }

  tag {
    key                 = "Project"
    value               = var.project_name
    propagate_at_launch = true
  }

  tag {
    key                 = "Role"
    value               = "k3s-agent"
    propagate_at_launch = true
  }

  lifecycle {
    create_before_destroy = true
  }
}

# --- Token K3s ---
resource "random_string" "k3s_token" {
  length  = 48
  special = false
  upper   = false
}

# --- Token Kubeconfig ---
resource "random_string" "kubeconfig_token" {
  length  = 32
  special = false
  upper   = false
}