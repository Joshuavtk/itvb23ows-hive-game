pipeline {
    agent {
        label '!windows'
    }
    environment {
        SONARQUBE_PROJECT_KEY = 'OWS-Hive-game'
    }
    stages {
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
