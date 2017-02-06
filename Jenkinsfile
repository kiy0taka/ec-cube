node {
    try {

        stage 'Git checkout'
        checkout scm

        stage 'Start containers'
        containerPrefix = "${JOB_BASE_NAME}-${BUILD_NUMBER}"
        postgres = docker.image("--name ${containerPrefix}-db -e TZ=Asia/Tokyo postgres:latest").run()
        mailcacher = docker.image("--name ${containerPrefix}-mail schickling/mailcatcher").run()
        sleep 3

        docker.image('eccube/php7-ext-apache').inside("""
                -e TZ=Asia/Tokyo -e DBSERVER=db -e DBUSER=postgres -e MAIL_HOST=mail
                --link ${containerPrefix}-db:db --link ${containerPrefix}-mail:mail""") {

            stage 'Composer install'
            sh 'composer install --dev --no-interaction -o'
            stage 'EC-CUBE install'
            sh 'php ./eccube_install.php pgsql none'
            stage 'Test'
            sh 'vendor/bin/phpunit --log-junit junit.xml tests/Eccube/Tests/Repository/'

        }
    } catch (e) {
        currentBuild.result = 'FAILURE'
    } finally {
        stage 'Stop containers'
        postgres.stop()
        mailcacher.stop()

        stage 'Report'
        junit 'junit.xml'
        slackSend "${JOB_NAME} ${BUILD_NUMBER} ${currentBuild.result} (${BUILD_URL})"
    }

}
