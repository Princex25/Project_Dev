#!/bin/bash
# ============================================================
# Cloud-Init — K3s Server (Master)
# ============================================================

set -e

# Variables
K3S_VERSION="${k3s_version}"
K3S_TOKEN="${k3s_token}"
K3S_CLUSTER_NAME="${k3s_cluster_name}"
DB_ENDPOINT="${db_endpoint}"
DB_NAME="${db_name}"

# Mise à jour système
yum update -y

# Installation de K3s
curl -sfL https://get.k3s.io | INSTALL_K3S_VERSION="${K3S_VERSION}" sh -s - server \
  --cluster-init \
  --token "${K3S_TOKEN}" \
  --cluster-cidr "10.42.0.0/16" \
  --service-cidr "10.43.0.0/16" \
  --disable traefik \
  --node-ip "$(hostname -I | awk '{print $1}')" \
  --tls-san "$(hostname -I | awk '{print $1}')" \
  --write-kubeconfig-mode 644

# Attendre que K3s soit prêt
sleep 30

# Exporter le kubeconfig pour Ansible
mkdir -p /home/ubuntu/.kube
cp /etc/rancher/k3s/k3s.yaml /home/ubuntu/.kube/config
chown -R ubuntu:ubuntu /home/ubuntu/.kube
chmod 600 /home/ubuntu/.kube/config

# Configurer le contexte
export KUBECONFIG=/etc/rancher/k3s/k3s.yaml
k3s kubectl config set-context default --cluster=default --user=default

# Label pour le server node
NODE_NAME=$(hostname)
k3s kubectl label node "${NODE_NAME}" node-role.kubernetes.io/control-plane="" node-role.kubernetes.io/server="" --overwrite

# Installation de l'AWS Load Balancer Controller via Helm
helm repo add eks https://aws.github.io/eks-charts
helm repo update

helm install aws-load-balancer-controller eks/aws-load-balancer-controller \
  --namespace kube-system \
  --set clusterName="${K3S_CLUSTER_NAME}" \
  --set serviceAccount.create=true \
  --set serviceAccount.name=aws-load-balancer-controller \
  --wait

echo "K3s Server installé avec succès !"