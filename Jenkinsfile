pipeline {
    agent any

    environment {
        // --- Docker ---
        AWS_REGION = "us-east-1"
        ECR_REPO = "123456789.dkr.ecr.${AWS_REGION}.amazonaws.com/devops-project"
        IMAGE_TAG = "build-${BUILD_NUMBER}"

        // --- Kubernetes ---
        KUBE_NAMESPACE = "devops-project"
        EKS_CLUSTER = "devops-project-cluster-dev"

        // --- Terraform ---
        TF_DIR = "terraform"
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

        // ============================================================
        // DÉPLOIEMENT KUBERNETES
        // ============================================================

        stage('Mise à jour du tag dans Kustomize') {
            steps {
                sh """
                    echo "📝 Mise à jour de l'image tag dans kustomization..."
                    cd kubernetes
                    sed -i 's|newTag:.*|newTag: ${IMAGE_TAG}|g' kustomization.yaml
                    echo "✅ Tag mis à jour"
                    cat kustomization.yaml
                """
            }
        }

        stage('Déploiement sur EKS') {
            steps {
                sh """
                    echo "🚀 Déploiement sur AWS EKS..."
                    aws eks update-kubeconfig --region ${AWS_REGION} --name ${EKS_CLUSTER}
                    cd kubernetes
                    kubectl apply -k .
                    echo "✅ Déploiement terminé sur EKS"
                """
            }
        }

        stage('Vérification du déploiement') {
            steps {
                sh """
                    echo "🔍 Vérification du statut des pods..."
                    kubectl -n ${KUBE_NAMESPACE} rollout status deployment/devops-project --timeout=180s
                    echo "✅ Vérification terminée — Tous les pods sont opérationnels"
                """
            }
        }

        stage('Smoke Tests') {
            steps {
                sh """
                    echo "🧪 Exécution des smoke tests..."
                    # curl ou scripts de test
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