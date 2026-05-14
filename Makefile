# ============================================================
# Makefile — Commandes utilitaires pour le projet DevOps
# ============================================================
# Usage : make <commande>
# Exemple : make terraform-init
# ============================================================

.PHONY: help \
	terraform-init terraform-plan terraform-apply terraform-destroy \
	ansible-provision ansible-deploy ansible-monitoring ansible-site \
	docker-build docker-push k8s-apply k8s-status \
	lint test clean

# Variables
TF_DIR = terraform
ANSIBLE_DIR = ansible
INVENTORY = $(ANSIBLE_DIR)/inventory/hosts
ANSIBLE_OPTS = -i $(INVENTORY) --vault-password-file $(ANSIBLE_DIR)/.vault_pass

help: ## Afficher l'aide
	@echo "============================================================"
	@echo "  Commandes disponibles :"
	@echo "============================================================"
	@grep -E '^[a-zA-Z_-]+:.*##' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*##"}; {printf "  \033[36m%-25s\033[0m %s\n", $$1, $$2}'
	@echo ""

# --- Terraform ---
terraform-init: ## Initialiser Terraform
	cd $(TF_DIR) && terraform init -input=false

terraform-plan: ## Aperçu des changements Terraform
	cd $(TF_DIR) && terraform plan -var-file="terraform.tfvars" -no-color

terraform-apply: ## Appliquer l'infrastructure Terraform
	cd $(TF_DIR) && terraform apply -var-file="terraform.tfvars" -auto-approve

terraform-destroy: ## Détruire toute l'infrastructure Terraform ⚠️
	cd $(TF_DIR) && terraform destroy -var-file="terraform.tfvars"

# --- Ansible ---
ansible-site: ## Exécuter le playbook principal (complet)
	cd $(ANSIBLE_DIR) && ansible-playbook $(ANSIBLE_OPTS) site.yml

ansible-provision: ## Provisionner les serveurs (sans déployer l'app)
	cd $(ANSIBLE_DIR) && ansible-playbook $(ANSIBLE_OPTS) provision.yml

ansible-deploy: ## Déployer l'application uniquement
	cd $(ANSIBLE_DIR) && ansible-playbook $(ANSIBLE_OPTS) deploy.yml

ansible-monitoring: ## Installer le monitoring seulement
	cd $(ANSIBLE_DIR) && ansible-playbook $(ANSIBLE_OPTS) monitoring.yml

ansible-check: ## Vérification syntaxique des playbooks
	cd $(ANSIBLE_DIR) && ansible-playbook $(ANSIBLE_OPTS) --syntax-check site.yml

ansible-inventory: ## Lister les hôtes disponibles
	cd $(ANSIBLE_DIR) && ansible-inventory $(ANSIBLE_OPTS) --graph

# --- Docker / Kubernetes ---
docker-build: ## Construire l'image Docker
	docker build -t devops-project-web:latest .

docker-push: ## Pousser l'image vers ECR (modifier le tag d'abord)
	@echo "⚠️ Configurez ECR_REPO et IMAGE_TAG avant de push"
	@echo "Exemple : docker tag devops-project-web:latest <ECR_REPO>:<TAG>"
	@echo "Puis : docker push <ECR_REPO>:<TAG>"

k8s-apply: ## Appliquer les manifests Kubernetes
	kubectl apply -k $(ANSIBLE_DIR)/../kubernetes/

k8s-status: ## Vérifier le statut des pods
	kubectl -n devops-project get pods

k8s-logs: ## Voir les logs des pods
	kubectl -n devops-project logs -l app=devops-project --tail=100 -f

# --- Utilitaires ---
test: ## Exécuter les tests
	./vendor/bin/phpunit --testdox

lint: ## Vérification syntaxique Terraform
	cd $(TF_DIR) && terraform fmt -check -recursive
	cd $(TF_DIR) && terraform validate

clean: ## Nettoyer les artefacts locaux
	rm -rf .terraform *.tfstate *.tfstate.backup
	rm -rf __pycache__ .pytest_cache

init: terraform-init ansible-check ## Initialiser tout le projet