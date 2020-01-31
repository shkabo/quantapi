<?php

namespace App\Controller;

use Nebkam\SymfonyTraits\FormTrait;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
