* `composer install`
* configure `.env` with your parameters (mysql & amqp string )
* `php bin/console doctrine:database:create && php bin/console doctrine:migrations:migrate`
* `php bin/console rabbitmq:consumer event`