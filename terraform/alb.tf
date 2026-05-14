# ============================================================
# ALB — Application Load Balancer
# Note : L'AWS Load Balancer Controller dans EKS gère les
#        target groups et listeners via les objets Ingress K8s.
#        Ce fichier crée uniquement l'ALB de base.
# ============================================================

resource "aws_lb" "main" {
  name               = "${var.project_name}-alb-${var.environment}"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [aws_security_group.alb.id]
  subnets            = aws_subnet.public[*].id

  enable_deletion_protection = false
  enable_http2               = true
  idle_timeout                = 60

  drop_invalid_header_fields = true

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-alb-${var.environment}"
  })
}

# --- Listener HTTP (redirection vers HTTPS) ---
resource "aws_lb_listener" "http" {
  load_balancer_arn = aws_lb.main.arn
  port              = 80
  protocol          = "HTTP"

  default_action {
    type = "redirect"
    redirect {
      port        = "443"
      protocol    = "HTTPS"
      status_code = "HTTP_301"
    }
  }
}

# --- Listener HTTPS (placeholder — certificat géré par ACM) ---
# Le certificat ACM doit être créé dans la console AWS ou via Terraform
# L'AWS Load Balancer Controller dans EKS complétera automatiquement
# la configuration des listeners et target groups via les objets Ingress

resource "aws_lb_listener" "https" {
  load_balancer_arn = aws_lb.main.arn
  port              = 443
  protocol          = "HTTPS"

  ssl_policy = "ELBSecurityPolicy-TLS13-1-2-2021-06"
  certificate_arn = var.acm_certificate_arn != "" ? var.acm_certificate_arn : "arn:aws:acm:${var.aws_region}:${data.aws_caller_identity.current.account_id}:certificate/PENDING"

  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.app.arn
  }
}

# --- Target Group (vide — remplie par l'Ingress Controller K8s) ---
resource "aws_lb_target_group" "app" {
  name_prefix = "${var.project_name}-"
  port        = 80
  protocol    = "HTTP"
  vpc_id      = aws_vpc.main.id

  health_check {
    enabled             = true
    healthy_threshold   = 3
    unhealthy_threshold = 3
    timeout             = 5
    interval            = 30
    path                = "/devops-project/php-login/index.php"
    matcher             = "200"
    protocol            = "HTTP"
  }

  stickiness {
    type            = "lb_cookie"
    cookie_duration = 86400
    enabled         = true
  }

  deregistration_delay = 30

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-tg-${var.environment}"
  })

  lifecycle {
    create_before_destroy = true
  }
}

# --- Données AWS ---
data "aws_caller_identity" "current" {}