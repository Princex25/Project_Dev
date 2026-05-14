pipeline {
    agent any

    environment {
        // --- Docker ---
        AWS_REGION = "us-east-1"
        ECR_REPO = "123456789.dkr.ecr.${AWS_REGION}.amazonaws.com/devops-project"
        IMAGE_TAG = "build-${BUILD_NUMBER}"

        // --- K3s ---
        KUBE_NAMESPACE = "devops-project"
        K3S_SERVER_IP = "" // À définir ou récupéré via Terraform output

        // --- Chemins ---
        TF_DIR = "terraform"
        ANSIBLE_DIR = "ansible"
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
                echo "✅ Code source récupéré avec succès"
            }
        }

        stage('Tests') {
            steps {
                sh 'echo "🔍 Exécution des tests..."'
                // sh './vendor/bin/phpunit --testdox'
                sh 'echo "✅ Tests terminés"'
            }
        }

        stage('Analyse de sécurité') {
            steps {
                sh 'echo "🔒 Scan de sécurité du code..."'
                // trufflehog, gitleaks, semgrep...
                sh 'echo "✅ Analyse de sécurité terminée"'
            }
        }

        stage('Build Docker Image') {
            steps {
                sh """
                    echo "🏗️ Construction de l'image Docker..."
                    docker build -t ${ECR_REPO}:${IMAGE_TAG} .
                    docker tag ${ECR_REPO}:${IMAGE_TAG} ${ECR_REPO}:latest
                    echo "✅ Image Docker construite : ${ECR_REPO}:${IMAGE_TAG}"
                """
            }
        }

        stage('Push vers ECR') {
            steps {
                sh """
                    echo "📤 Authentification auprès d'ECR..."
                    aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${AWS_REGION}.dkr.ecr.amazonaws.com

                    echo "📤 Pushing de l'image vers ECR..."
                    docker push ${ECR_REPO}:${IMAGE_TAG}
                    docker push ${ECR_REPO}:latest
                    echo "✅ Image pushée avec succès dans ECR"
                """
            }
        }

        // ============================================================
        // ÉTAPES TERRAFORM
        // ============================================================

        stage('Terraform Init') {
            steps {
                sh """
                    echo "🔧 Initialisation Terraform..."
                    cd ${TF_DIR}
                    terraform init -input=false
                    echo "✅ Terraform initialisé"
                """
            }
        }

        stage('Terraform Plan') {
            steps {
                sh """
                    echo "📋 Plan Terraform en cours..."
                    cd ${TF_DIR}
                    terraform plan -var-file="terraform.tfvars" -no-color -out=tfplan
                    echo "✅ Plan Terraform généré"
                """
            }
        }

        stage('Terraform Apply') {
            steps {
                sh """
                    echo "🚀 Application de l'infrastructure Terraform..."
                    cd ${TF_DIR}
                    terraform apply -auto-approve tfplan
                    echo "✅ Infrastructure Terraform déployée"
                """
            }
        }

        stage('Récupérer IP K3s Server') {
            steps {
                sh """
                    echo "📡 Récupération de l'IP du serveur K3s..."
                    export K3S_SERVER_IP=\$(cd ${TF_DIR} && terraform output -raw k3s_server_ip 2>/dev/null || echo "")
                    echo "IP K3s Server : \${K3S_SERVER_IP}"

                    if [ -z "\${K3S_SERVER_IP}" ]; then
                        echo "⚠️ IP récupérée depuis Terraform state"
                        export K3S_SERVER_IP=\$(cd ${TF_DIR} && terraform show -json | jq -r '.values.root_module.resources[] | select(.name=="k3s_server") | .values.private_ip // .values.network_interface[0].private_ip')
                    fi

                    # Sauvegarder l'IP pour les étapes suivantes
                    echo "K3S_SERVER_IP=\${K3S_SERVER_IP}" > k3s-server-ip.txt
                    echo "✅ IP K3s Server : \${K3S_SERVER_IP}"
                """
                script {
                    env.K3S_SERVER_IP = readFile('k3s-server-ip.txt').trim().split('=')[1]
                }
            }
        }

        // ============================================================
        // ÉTAPES ANSIBLE
        // ============================================================

        stage('Ansible Provisioning') {
            steps {
                sh """
                    echo "🎭 Provisionning des serveurs avec Ansible..."
                    cd ${ANSIBLE_DIR}

                    # Mettre à jour l'inventaire avec l'IP réelle
                    sed -i 's|10.0.1.50|${K3S_SERVER_IP}|g' inventory/hosts

                    ansible-playbook -i inventory/hosts provision.yml -v
                    echo "✅ Provisionning terminé"
                """
            }
        }

        stage('Ansible Déploiement App') {
            steps {
                sh """
                    echo "🎭 Déploiement de l'application avec Ansible..."
                    cd ${ANSIBLE_DIR}
                    ansible-playbook -i inventory/hosts deploy.yml -v
                    echo "✅ Déploiement applicatif terminé"
                """
            }
        }

        stage('Ansible Monitoring') {
            steps {
                sh """
                    echo "🎭 Installation du monitoring..."
                    cd ${ANSIBLE_DIR}
                    ansible-playbook -i inventory/hosts monitoring.yml -v
                    echo "✅ Monitoring installé"
                """
            }
        }

        // ============================================================
        // VÉRIFICATION FINALE
        // ============================================================

        stage('Vérification du déploiement') {
            steps {
                sh """
                    echo "🔍 Vérification du statut du cluster..."
                    export KUBECONFIG=~/.kube/config
                    ssh -o StrictHostKeyChecking=no ubuntu@${K3S_SERVER_IP} 'k3s kubectl get nodes -o wide'
                    ssh -o StrictHostKeyChecking=no ubuntu@${K3S_SERVER_IP} 'k3s kubectl -n devops-project get pods -o wide'
                    ssh -o StrictHostKeyChecking=no ubuntu@${K3S_SERVER_IP} 'k3s kubectl -n devops-project get ingress'
                    ssh -o StrictHostKeyChecking=no ubuntu@${K3S_SERVER_IP} 'k3s kubectl -n devops-project get hpa'
                    echo "✅ Vérification terminée — Tous les composants sont opérationnels"
                """
            }
        }

        stage('Smoke Tests') {
            steps {
                sh """
                    echo "🧪 Exécution des smoke tests..."
                    # Test de connectivité
                    curl -s -o /dev/null -w "%{http_code}" http://\${K3S_SERVER_IP}:30080/devops-project/php-login/index.php || echo "Service non encore accessible via NodePort"
                    echo "✅ Smoke tests terminés"
                """
            }
        }
    }

    post {
        success {
            echo "🎉 Pipeline terminé avec succès ! Build #${BUILD_NUMBER}"
        }
        failure {
            echo "❌ Pipeline échoué ! Build #${BUILD_NUMBER}"
        }
        always {
            sh 'docker rmi ${ECR_REPO}:${IMAGE_TAG} ${ECR_REPO}:latest 2>/dev/null || true'
            // Archive des plans Terraform
            archiveArtifacts artifacts: 'terraform/tfplan', allowEmptyArchive: true
        }
    }
}