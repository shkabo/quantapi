<?php

namespace App\Controller;

use App\Entity\Event;
use Nebkam\SymfonyTraits\FormTrait;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

class EventsController extends AbstractController
{
    use FormTrait;

    /**
     * @Route("/events", name="events", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Producer $eventProducer
     * @return JsonResponse
     */
    public function update(Request $request, EntityManagerInterface $em, $eventProducer)
    {
        $msg = $this->getJsonContent($request);
        $msg['datum'] = date('Y-m-d');

        $eventProducer->setContentType('application/json')
            ->publish(json_encode($msg));
        return $this->json('OK', Response::HTTP_OK);
    }

    /**
     * @Route("/events", name="get_events_summary", methods={"GET"})
     * @param Request $request
     * @param AdapterInterface $cache
     * @return Response
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     */
    public function getSummary(Request $request, AdapterInterface $cache)
    {
        $type = $request->get('type') === 'csv' ? 'csv' : 'json';
        $cacheKey = 'summary_'.$type;
        $item = $cache->getItem($cacheKey);

        // we don't have cache ?
        if (!$item->isHit()) {
            $data = $this->getDoctrine()->getRepository(Event::class)->topFiveLastSevenDays();
            $data = $this->serializeData($data, $type);
            $item->set($data);
            // cache 60 sec
            $item->expiresAfter(new \DateInterval('PT60S'));
            $cache->save($item);
        }

        switch($type) {
            case 'csv':
                return CsvResponse::generateCsv(['Datum', 'CountryCode', 'Event', 'Amount'], $item->get());
            default:
                return $this->json($item->get());
        }

    }

    /**
     * @param $data
     * @param string $type
     * @return array|\ArrayObject|bool|float|int|string|null
     * @throws ExceptionInterface
     */
    private function serializeData($data, string $type)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');
        return $serializer->normalize($data, $type, [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d']);
    }
}
