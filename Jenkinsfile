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

        stage('Install Dependencies') {
            steps {
                sh "composer install"
            }
        }

        stage('Unit Tests') {
            steps {
                sh 'vendor/bin/phpunit'
                xunit([
                    thresholds: [
                        failed ( failureThreshold: "0" ),
                        skipped ( unstableThreshold: "0" )
                    ],
                    tools: [
                        PHPUnit(pattern: 'build/logs/junit.xml', stopProcessingIfError: false, failIfNotNew: true)
                    ]
                ])
            }
        }
    }
}
