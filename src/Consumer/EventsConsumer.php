<?php


namespace App\Consumer;

use App\Entity\Event;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class EventsConsumer implements ConsumerInterface
{

    private $manager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(AMQPMessage $msg)
    {
        $response = json_decode($msg->body, true);
        $this->processMessageEvent($response);
    }

    private function processMessageEvent(array $data)
    {
        // check if we have record in the db
        $event = $this->manager->getRepository(Event::class)
            ->findOneBy(['datum' => new DateTime($data['datum']),
                         'countryCode' => $data['countryCode'],
                         'eventType' => $data['eventType']]);

        if ($event == null) {
            // create new record
            $event = new Event();
            $event->setCountryCode($data['countryCode']);
            $event->setEventType($data['eventType']);
            $event->setDatum();
            $this->manager->persist($event);
            $this->manager->flush();

        } else {
            // update existing record
            $query = $this->manager->createQuery(
                'UPDATE App\Entity\Event as e 
                    SET e.ammount = e.ammount + 1
                    WHERE e.id = :id')
                ->setParameter('id', $event->getId())
                ->execute();
        }
    }
}