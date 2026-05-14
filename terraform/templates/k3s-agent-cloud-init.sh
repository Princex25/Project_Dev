#!/bin/bash
# ============================================================
# Cloud-Init — K3s Agent (Worker)
# ============================================================

set -e

# Variables
K3S_VERSION="${k3s_version}"
K3S_TOKEN="${k3s_token}"
K3S_SERVER_URL="${k3s_server_url}"

# Mise à jour système
yum update -y

# Installation de K3s Agent
curl -sfL https://get.k3s.io | INSTALL_K3S_VERSION="${K3S_VERSION}" K3S_URL="${K3S_SERVER_URL}" K3S_TOKEN="${K3S_TOKEN}" sh -s - agent \
  --node-ip "$(hostname -I | awk '{print $1}')" \
  --docker

# Label pour le agent node
NODE_NAME=$(hostname)
k3s kubectl label node "${NODE_NAME}" node-role.kubernetes.io/worker="" --overwrite

echo "K3s Agent installé avec succès !"