### Starting up
* `composer install`
* configure `.env` with your parameters (mysql, amqp, redis url string )
* `php bin/console doctrine:database:create && php bin/console doctrine:migrations:migrate`
* `php bin/console rabbitmq:consumer event`


`POST /events` request expects JSON format
```$xslt
{
	"countryCode": "RS",
	"eventType": "click"
}
```
`GET /events` prima opcioni query parametar `type=json|csv`. Fallback response je `json` type
### Project notes/improvements
* By default redis cache is 60 sec for the api data
* Validation could/should be used when doing POST
* Coummand could/should be built that can be called by cron job to trigger re-cache of the response
