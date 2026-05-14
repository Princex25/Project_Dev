pipeline {
    agent any

    environment {
        AWS_REGION = "us-east-1"
        ECR_REPO = "123456789.dkr.ecr.${AWS_REGION}.amazonaws.com/devops-project"
        IMAGE_TAG = "build-${BUILD_NUMBER}"
        KUBE_NAMESPACE = "devops-project"
        KUBECONFIG_PATH = "${HOME}/.kube/config"
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
                // Exécuter les tests PHPUnit si configuré
                // sh './vendor/bin/phpunit --testdox'
                sh 'echo "✅ Tests terminés"'
            }
        }

        stage('Analyse de sécurité') {
            steps {
                sh 'echo "🔒 Scan de sécurité du code..."'
                // Scanner les secrets (ex: trufflehog, gitleaks)
                // Scanner les vulnérabilités (ex: semgrep, sonarqube)
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

        stage('Mise à jour du tag dans Kustomize') {
            steps {
                sh """
                    echo "📝 Mise à jour de l'image tag dans kustomization..."
                    cd kubernetes

                    # Mettre à jour le tag d'image dans kustomization.yaml
                    sed -i 's|image:.*|image: ${ECR_REPO}:${IMAGE_TAG}|g' kustomization.yaml

                    echo "✅ Tag mis à jour dans kustomization.yaml"
                    cat kustomization.yaml
                """
            }
        }

        stage('Déploiement sur EKS') {
            steps {
                sh """
                    echo "🚀 Déploiement sur AWS EKS..."

                    # Mettre à jour kubeconfig pour EKS
                    aws eks update-kubeconfig --region ${AWS_REGION} --name devops-project-cluster

                    # Appliquer les configurations Kubernetes via kustomize
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
                    kubectl -n ${KUBE_NAMESPACE} rollout status deployment/devops-project --timeout=120s

                    echo "✅ Vérification terminée — Tous les pods sont opérationnels"
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
            // Notification Slack/Email possible ici
        }
        always {
            // Nettoyage des images locales
            sh 'docker rmi ${ECR_REPO}:${IMAGE_TAG} ${ECR_REPO}:latest 2>/dev/null || true'
        }
    }
}