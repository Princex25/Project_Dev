# ============================================================
# EKS — Cluster Kubernetes managé
# ============================================================

# --- Cluster EKS ---
module "eks" {
  source  = "terraform-aws-modules/eks/aws"
  version = "~> 20.8"

  cluster_name    = "${var.project_name}-cluster-${var.environment}"
  cluster_version = var.eks_cluster_version

  cluster_endpoint_public_access  = true
  cluster_endpoint_private_access = true

  cluster_addons = {
    coredns = {
      most_recent = true
    }
    kube-proxy = {
      most_recent = true
    }
    vpc-cni = {
      most_recent              = true
      service_account_role_arn = module.vpc_cni_irsa.iam_role_arn
    }
    aws-ebs-csi-driver = {
      most_recent              = true
      service_account_role_arn = module.ebs_csi_irsa.iam_role_arn
    }
  }

  # VPC configuration
  vpc_id     = aws_vpc.main.id
  subnet_ids = aws_subnet.private[*].id

  # Node Group principal
  eks_managed_node_groups = {
    main = {
      name = "${var.project_name}-nodes-${var.environment}"

      instance_types = [var.eks_instance_type]
      capacity_type  = "ON_DEMAND"

      min_size     = var.eks_node_min_size
      max_size     = var.eks_node_max_size
      desired_size = var.eks_node_desired_size

      disk_size = 50

      labels = {
        role        = "worker"
        environment = var.environment
      }

      tags = merge(var.common_tags, {
        Name = "${var.project_name}-node-${var.environment}"
      })
    }
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-eks-${var.environment}"
  })
}

# --- IAM pour VPC CNI plugin ---
module "vpc_cni_irsa" {
  source  = "terraform-aws-modules/iam/aws//modules/iam-role-for-service-accounts-eks"
  version = "~> 5.37"

  role_name             = "${var.project_name}-vpc-cni-${var.environment}"
  attach_vpc_cni_policy = true
  vpc_cni_enable_ipv4   = true

  oidc_providers = {
    main = {
      provider_arn               = module.eks.oidc_provider_arn
      namespace_service_accounts = ["kube-system:aws-node"]
    }
  }

  tags = var.common_tags
}

# --- IAM pour EBS CSI Driver ---
module "ebs_csi_irsa" {
  source  = "terraform-aws-modules/iam/aws//modules/iam-role-for-service-accounts-eks"
  version = "~> 5.37"

  role_name             = "${var.project_name}-ebs-csi-${var.environment}"
  attach_ebs_csi_policy = true

  oidc_providers = {
    main = {
      provider_arn               = module.eks.oidc_provider_arn
      namespace_service_accounts = ["kube-system:ebs-csi-controller-sa"]
    }
  }

  tags = var.common_tags
}