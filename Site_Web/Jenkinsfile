pipeline {
    agent any

    options {
        timestamps()
    }

    environment {
        IMAGE_NAME = "admin2-web:${BUILD_NUMBER}"
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker image') {
            steps {
                sh 'docker build -t $IMAGE_NAME .'
            }
        }

        stage('Deploy to Render') {
            when {
                expression {
                    return env.RENDER_DEPLOY_HOOK_URL && env.RENDER_DEPLOY_HOOK_URL.trim()
                }
            }
            steps {
                sh 'curl -fsS -X POST "$RENDER_DEPLOY_HOOK_URL"'
            }
        }
    }

    post {
        failure {
            echo 'Pipeline failed.'
        }
    }
}
