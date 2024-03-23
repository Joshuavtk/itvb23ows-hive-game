pipeline {
    agent { docker { image 'php:8.3.4-alpine3.19' }, label '!windows' }
    environment {
        SONARQUBE_PROJECT_KEY = 'OWS-Hive-game'
    }
    stages {
        stage('build') {
            steps {
                sh 'php --version'
            }
        }
        stage('SonarQube') {
            steps {
                script { scannerHome = tool 'SonarQube Scanner' }
                withSonarQubeEnv('SonarQube') {
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=${SONARQUBE_PROJECT_KEY}"
                }
            }
        }
    }
}
