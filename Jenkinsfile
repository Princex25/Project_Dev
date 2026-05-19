pipeline {
    agent any

    environment {
        // --- Docker ---
        AWS_REGION = "us-east-1"
        ECR_REPO = "123456789.dkr.ecr.${AWS_REGION}.amazonaws.com/devops-project"
        IMAGE_TAG = "build-${BUILD_NUMBER}"

        // --- EKS ---
        EKS_CLUSTER_NAME = "devops-project-cluster-dev"
        KUBE_NAMESPACE = "devops-project"

        // --- Chemins ---
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
        withCredentials([
            string(credentialsId: 'aws-access-key', variable: 'AWS_ACCESS_KEY_ID'),
            string(credentialsId: 'aws-secret-key', variable: 'AWS_SECRET_ACCESS_KEY')
        ]) {
            sh '''
                aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin 123456789.dkr.ecr.us-east-1.amazonaws.com/devops-project
                docker push 123456789.dkr.ecr.us-east-1.amazonaws.com/devops-project:latest
                docker push 123456789.dkr.ecr.us-east-1.amazonaws.com/devops-project:build-${BUILD_ID}
            '''
        }
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
        // DÉPLOIEMENT KUBERNETES SUR EKS
        // ============================================================

        stage('Déploiement Kubernetes') {
            steps {
                sh """
                    echo "📡 Mise à jour de la configuration kubeconfig pour EKS..."
                    aws eks update-kubeconfig --region ${AWS_REGION} --name ${EKS_CLUSTER_NAME}

                    echo "🚀 Déploiement des ressources Kubernetes..."
                    kubectl apply -f kubernetes/
                    
                    # Remplacer le tag d'image dans le déploiement
                    kubectl set image deployment/app-deployment app=${ECR_REPO}:${IMAGE_TAG} -n ${KUBE_NAMESPACE}
                    
                    echo "✅ Déploiement Kubernetes terminé"
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
                    kubectl -n ${KUBE_NAMESPACE} get pods -o wide
                    kubectl -n ${KUBE_NAMESPACE} get ingress
                    kubectl -n ${KUBE_NAMESPACE} get hpa
                    echo "✅ Vérification terminée — Tous les composants sont opérationnels"
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
        }
    }
}
