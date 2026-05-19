pipeline {
    agent any
    environment {
        IMAGE_NAME = "devops-project-web"
        KUBE_NAMESPACE = "devops-project"
        KUBECONFIG = "/var/jenkins_home/.kube/config"
    }
    stages {
        stage('Checkout') {
            steps {
                git url: 'https://github.com/Princex25/Project_Dev.git', branch: 'main'
                echo "✅ Code récupéré depuis GitHub"
            }
        }
        stage('Build Docker Image') {
            steps {
                sh 'docker build -t devops-project-web:latest .'
                echo "✅ Image buildée localement"
            }
        }
        stage('Deploy Kubernetes') {
            steps {
                echo "☸️ Redémarrage des pods..."
                sh 'kubectl rollout restart deployment/devops-project -n devops-project'
                sh 'kubectl rollout status deployment/devops-project -n devops-project --timeout=120s'
                echo "✅ Déployé avec succès"
            }
        }
    }
    post {
        success {
            echo "🎉 Application déployée avec succès"
        }
        failure {
            echo "❌ Échec du déploiement"
        }
    }
}
