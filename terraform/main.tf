# ============================================================
# Fournisseurs principaux
# ============================================================

provider "aws" {
  region = var.aws_region

  default_tags {
    tags = {
      Project     = ""
      Environment = var.environment
      ManagedBy   = "terraform"
      Team        = "devops"
    }
  }
}

# ============================================================
# Données externes
# ============================================================

data "aws_availability_zones" "available" {
  state = "available"
}