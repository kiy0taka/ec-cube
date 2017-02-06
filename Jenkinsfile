node {
    try {
        stage 'Start containers'
        postgres = docker.image("--name ${JOB_NAME}-${BUILD_NUMBER}-db -e TZ=Asia/Tokyo postgres:latest").run()
        mailcacher = docker.image('--name ${JOB_NAME}-${BUILD_NUMBER}-mail schickling/mailcatcher').run()
        sleep 3

        docker.image('eccube/php7-ext-apache').inside("""
                -e TZ=Asia/Tokyo -e DBSERVER=db -e DBUSER=postgres -e MAIL_HOST=mail
                --link ${JOB_NAME}-${BUILD_NUMBER}-db:db --link ${JOB_NAME}-${BUILD_NUMBER}-mail:mail""") {

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
