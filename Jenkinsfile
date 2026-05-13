pipeline {
    agent any

    options {
        timestamps()
    }

    environment {
        IMAGE_NAME = "devops-project-web:${BUILD_NUMBER}"
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
    }

    post {
        failure {
            echo 'Pipeline failed.'
        }
    }
}
