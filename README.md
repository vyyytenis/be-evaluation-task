# Symfony Docker
## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` (the logs will be displayed in the current shell)
4. Open `docker compose exec php sh`
5. Run migrations (in step 4 window)
`php bin/console doctrine:database:create
 php bin/console make:migration
 php bin/console doctrine:migrations:migrate `
6. Start worker (in step 4 window) `php bin/console messenger:consume async -vv`
7. Open `https://localhost`

# IMPORTANT Twilio!
* To send sms other than to +37060790942, I need to **verify new number in Twilio account**.
* If you send to other than +37060790942 **it will fail and dummy sms provider will be used** as failover. Simulates a random failure

# IMPORTANT AWS SES!
* You send email to vyyytenis@gmail.com or kareiva.vytenis@gmail.com, counter in AWS goes up.
* To send to other, emails have to be verified, otherwise it will fail and go to Dummy sender. Simulates a random failure

vendor/bin/phpunit tests/Functional/SendNotificationHanlderTest.php 

bin/console make:test
