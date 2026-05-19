pipeline {
    agent any

    triggers {
        githubPush()
    }

    environment {
        IMAGE_NAME = "devops-project-web"
        KUBE_NAMESPACE = "devops-project"
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                echo "✅ Code récupéré depuis GitHub"
            }
        }

        stage('Build Docker Image') {
            steps {
                sh """
                    echo "🏗️ Build image..."
                    docker build -t ${IMAGE_NAME}:latest .
                    echo "✅ Image buildée localement"
                """
            }
        }

        stage('Deploy Kubernetes') {
            steps {
                sh """
                    echo "☸️ Redémarrage des pods..."
                    kubectl rollout restart deployment/devops-project -n ${KUBE_NAMESPACE}
                    kubectl rollout status deployment/devops-project -n ${KUBE_NAMESPACE} --timeout=120s
                    echo "✅ Déployé avec succès !"
                """
            }
        }
    }

    post {
        success {
            echo "🎉 Application déployée : http://54.175.200.207/devops-project"
        }
        failure {
            echo "❌ Échec du déploiement"
        }
    }
}
